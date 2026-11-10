import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

import settings from '../e2e-settings';
import { GenericAdminPage } from '../lib/genericadminpage';
import { LoginPage } from '../lib/loginpage';
import { ProfilePage } from '../lib/profilepage';
import { login, registerKey } from '../lib/test-helpers';
import { test, expect } from '../lib/webauthn-helpers';

/* eslint-disable no-await-in-loop -- Requests share a session and pending ceremony, so exercise them sequentially. */

const execute = promisify( execFile );

async function wordpress( ...args: string[] ): Promise<string> {
	const [ command, prefix ] = process.env.E2E_WP_CLI === 'vip'
		? [ 'vip', [ 'dev-env', 'exec', '--quiet', '--', 'wp' ] ]
		: [ 'docker', [ 'compose', 'run', '--rm', '-T', 'cli', 'wp' ] ];
	const { stdout } = await execute( command, [ ...prefix, ...args ] );
	return stdout.trim();
}

async function setSessionProof( userId: string, timestamp: number ): Promise<void> {
	await wordpress( 'eval', `
		$sessions = get_user_meta( ${ Number( userId ) }, 'session_tokens', true );
		foreach ( $sessions as &$session ) {
			$session['two-factor-login'] = ${ timestamp };
		}
		update_user_meta( ${ Number( userId ) }, 'session_tokens', $sessions );
	` );
}

async function sessionProof( userId: string ): Promise<number[]> {
	return JSON.parse( await wordpress( 'eval', `
		$sessions = get_user_meta( ${ Number( userId ) }, 'session_tokens', true );
		echo wp_json_encode( array_values( array_column( $sessions, 'two-factor-login' ) ) );
	` ) ) as number[];
}

test( 'Key management requires recent second-factor proof', async ( { page, credentials, browserName } ) => {
	test.setTimeout( 120_000 );
	const username = `reauth-${ browserName }-${ Date.now() }`;
	const userId = await wordpress( 'user', 'create', username, `${ username }@example.com`,
		`--user_pass=${ settings.user1Password }`, '--porcelain' );
	const profile = new ProfilePage( page );
	let credentialId = '';
	let pendingRegistration: Record<string, string> = {};
	let renameNonce = '';
	let deleteNonce = '';
	let registrationNonce = '';
	const freshProof = Math.floor( Date.now() / 1000 ) - 60;

	try {
		await test.step( 'Initial enrollment remains available without an enabled factor', async () => {
			await login( page, username, settings.user1Password );
			const credential = await registerKey( page, credentials, 'Original key' );
			credentialId = credential.id;
			await expect( page.locator( '.key-name' ) ).toHaveText( 'Original key' );
			await profile.enableWebAuthnProvider();
			await profile.saveProfile();
			await setSessionProof( userId, freshProof );
		} );

		await test.step( 'Prepare a valid registration while the session is fresh', async () => {
			await credentials.delete( credentialId );
			await page.route( '**/wp-admin/admin-ajax.php', async ( route ) => {
				const form = new URLSearchParams( route.request().postData() ?? '' );
				if ( form.get( 'action' ) === 'webauthn_register' ) {
					pendingRegistration = Object.fromEntries( form );
					await route.fulfill( { status: 400, json: { success: false, data: 'Registration paused' } } );
				} else {
					await route.continue();
				}
			} );
			await profile.registerKey( 'Pending key' );
			await page.unroute( '**/wp-admin/admin-ajax.php' );
			renameNonce = await page.locator( '.rename a[data-handle]' ).getAttribute( 'data-nonce' ) ?? '';
			deleteNonce = await page.locator( '.delete a[data-handle]' ).getAttribute( 'data-nonce' ) ?? '';
			registrationNonce = pendingRegistration._ajax_nonce;
			expect( pendingRegistration.credential ).toBeTruthy();
			expect( await sessionProof( userId ) ).toEqual( [ freshProof ] );
		} );

		for ( const timestamp of [ freshProof - 3600, 0 ] ) {
			await test.step( `Reject direct AJAX with session proof ${ timestamp }`, async () => {
				await setSessionProof( userId, timestamp );
				for ( const form of [
					{ action: 'webauthn_preregister', user_id: userId, _ajax_nonce: registrationNonce },
					pendingRegistration,
					{ action: 'webauthn_rename_key', user_id: userId, handle: credentialId, _ajax_nonce: renameNonce, name: 'Attacker key' },
					{ action: 'webauthn_delete_key', user_id: userId, handle: credentialId, _ajax_nonce: deleteNonce },
				] ) {
					const response = await page.request.post( '/wp-admin/admin-ajax.php', { form } );
					expect( response.status() ).toBe( 400 );
					expect( await response.json() ).toEqual( { success: false, data: 'Two Factor Revalidation required.' } );
				}
				expect( await sessionProof( userId ) ).toEqual( [ timestamp ] );
				await profile.visit();
				await expect( page.locator( '.key-name' ) ).toHaveText( 'Original key' );
				await expect( page.locator( '#two-factor-options' ) ).toHaveAttribute( 'disabled' );
			} );
		}

		await test.step( 'Fresh sessions can finish registration, rename and revoke without refreshing proof', async () => {
			await setSessionProof( userId, freshProof );
			const response = await page.request.post( '/wp-admin/admin-ajax.php', { form: pendingRegistration } );
			expect( response.status() ).toBe( 200 );
			expect( ( await response.json() as { success: boolean } ).success ).toBe( true );
			await profile.visit();
			await profile.renameKey( credentialId, 'Renamed key', true );
			await expect( page.locator( '.key-name' ).first() ).toHaveText( 'Renamed key' );
			const revoke = await page.request.post( '/wp-admin/admin-ajax.php', { form: {
				action: 'webauthn_delete_key', user_id: userId, handle: credentialId, _ajax_nonce: deleteNonce,
			} } );
			expect( revoke.status() ).toBe( 200 );
			expect( await sessionProof( userId ) ).toEqual( [ freshProof ] );
			await profile.visit();
			await expect( page.locator( '.key-name' ) ).toHaveText( 'Pending key' );
		} );

		await test.step( 'Genuine second-factor authentication permits key management', async () => {
			await new GenericAdminPage( page ).logOut();
			await login( page, username, settings.user1Password );
			await new LoginPage( page ).loginWithKey();
			await profile.visit();
			const [ credential ] = await credentials.get();
			await profile.renameKey( credential.id, 'Authenticated key', true );
			await expect( page.locator( '.key-name' ) ).toHaveText( 'Authenticated key' );
		} );
	} finally {
		await wordpress( 'user', 'delete', userId, '--yes' );
	}
} );
