import { test, expect } from '@playwright/test';

import settings from '../e2e-settings';
import { ProfilePage } from '../lib/profilepage';
import { login } from '../lib/test-helpers';

for ( const [ name, message ] of [
	[ 'image', '<img src=x onerror="document.body.dataset.webauthnXss=1">Registration failed' ],
	[ 'svg', '<svg onload="document.body.dataset.webauthnXss=1"></svg>Registration failed' ],
] ) {
	test( `Render ${ name } markup in AJAX errors as text`, async ( { page } ) => {
		await login( page, settings.user2Username, settings.user2Password );
		const profilePage = new ProfilePage( page );
		await profilePage.visit();
		await page.route( '**/wp-admin/admin-ajax.php', async ( route ) => {
			await route.fulfill( { status: 400, json: { success: false, data: message } } );
		} );

		await page.getByRole( 'button', { name: 'Register New Key' } ).click();

		const notice = page.locator( '.tfa-webauthn-alert' );
		await expect( notice ).toHaveText( message );
		await expect( notice.locator( 'img, svg' ) ).toHaveCount( 0 );
		await expect( page.locator( 'body' ) ).not.toHaveAttribute( 'data-webauthn-xss' );
	} );
}

test( 'Render browser exception messages as text', async ( { page } ) => {
	await page.addInitScript( () => {
		navigator.credentials.create = () => Promise.reject(
			new DOMException( '<img src=x onerror="document.body.dataset.webauthnXss=1">Browser error', 'UnknownError' ),
		);
	} );

	await login( page, settings.adminUsername, settings.adminPassword );
	const profilePage = new ProfilePage( page );
	await profilePage.visit();

	await page.getByRole( 'button', { name: 'Register New Key' } ).click();

	const notice = page.locator( '.tfa-webauthn-alert' );
	await expect( notice ).toHaveText( '<img src=x onerror="document.body.dataset.webauthnXss=1">Browser error' );
	await expect( notice.locator( 'img' ) ).toHaveCount( 0 );
	await expect( page.locator( 'body' ) ).not.toHaveAttribute( 'data-webauthn-xss' );
} );

test( 'Server returns plain-text registration errors', async ( { page, context } ) => {
	await context.credentials.install();
	await login( page, settings.adminUsername, settings.adminPassword );
	const profilePage = new ProfilePage( page );
	await profilePage.visit();
	await page.route( '**/wp-admin/admin-ajax.php', async ( route ) => {
		const form = new URLSearchParams( route.request().postData() ?? '' );
		if ( form.get( 'action' ) === 'webauthn_register' ) {
			const credential = JSON.parse( form.get( 'credential' ) ?? '{}' ) as { response: { attestationObject: string } };
			const attestation = Buffer.from( credential.response.attestationObject, 'base64' );
			// Add an unexpected field to the authenticator's three-entry CBOR map.
			// eslint-disable-next-line playwright/no-conditional-expect
			expect( attestation[ 0 ] ).toBe( 0xa3 );
			const field = Buffer.from( '<img src=x onerror="document.body.dataset.webauthnXss=1">' );
			credential.response.attestationObject = Buffer.concat( [
				Buffer.from( [ 0xa4 ] ),
				attestation.subarray( 1 ),
				Buffer.from( [ 0x78, field.length ] ),
				field,
				Buffer.from( [ 0xf5 ] ),
			] ).toString( 'base64' );

			form.set( 'credential', JSON.stringify( credential ) );
			await route.continue( { postData: form.toString() } );
		} else {
			await route.continue();
		}
	} );

	const [ response, notice ] = await Promise.all( [
		page.waitForResponse( ( resp ) =>
			new URLSearchParams( resp.request().postData() ?? '' ).get( 'action' ) === 'webauthn_register' ),
		profilePage.registerKey( 'Unexpected field' ),
	] );

	const result = await response.json() as { success: boolean; data: string };
	expect( response.status() ).toBe( 400 );
	expect( result.success ).toBe( false );
	expect( result.data ).toBe( 'Unexpected fields in data ().' );
	await expect( notice ).toHaveText( result.data );
	await expect( notice.locator( 'img' ) ).toHaveCount( 0 );
	await expect( page.locator( 'body' ) ).not.toHaveAttribute( 'data-webauthn-xss' );
} );
