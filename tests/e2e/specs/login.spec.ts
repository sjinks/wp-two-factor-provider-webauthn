import { test, expect, type CDPSession } from '@playwright/test';

import settings from '../e2e-settings';
import { GenericAdminPage } from '../lib/genericadminpage';
import { LoginPage } from '../lib/loginpage';
import { ProfilePage } from '../lib/profilepage';
import { login, registerKey } from '../lib/test-helpers';
import { addVirtualAuthenticator, getCredential, removeVirtualAuthenticator } from '../lib/webauthn-helpers';

let client: CDPSession;
let authenticatorId: string;
let credentialId: string;
let signCount: number;

test.beforeEach( async ( { context, page } ) => {
	client = await context.newCDPSession( page );
	authenticatorId = await addVirtualAuthenticator( client, 'ctap2', 'usb' );
} );

test.afterEach( () => removeVirtualAuthenticator( client, authenticatorId ) );

test( 'Login Workflow', async ( { page } ) => {
	await test.step( 'Log in', async () => {
		await login( page, settings.user1Username, settings.user1Password );
		return expect( page.url() ).toContain( '/wp-admin/' );
	} );

	await test.step( 'Register key', async () => {
		const credential = await registerKey( page, client, authenticatorId, 'Test Key' );
		credentialId = credential.credentialId;
		signCount = credential.signCount;
	} );

	await test.step( 'Configure WebAuthn provider', async () => {
		expect( page.url() ).toContain( '/wp-admin/profile.php' );
		const profilePage = new ProfilePage( page );
		await profilePage.enableWebAuthnProvider();
		await profilePage.saveProfile();
		await profilePage.makeWebAuthnProviderPrimary();
		return profilePage.saveProfile();
	} );

	await test.step( 'Log out', async () => {
		const adminPage = new GenericAdminPage( page );
		await adminPage.logOut();
		return expect( page.url() ).toContain( '/wp-login.php' );
	} );

	await test.step( 'Log in with key', async () => {
		expect( page.url() ).toContain( '/wp-login.php' );
		const loginPage = new LoginPage( page );
		await loginPage.login( settings.user1Username, settings.user1Password );

		await expect( loginPage.getSecondFactorProvider() ).resolves.toBe( 'TwoFactor_Provider_WebAuthn' );
		await loginPage.loginWithKey();

		const credential = await getCredential( client, authenticatorId, credentialId );
		return expect( credential.signCount ).toBeGreaterThan( signCount );
	} );

	await test.step( 'Authentication bypass is mitigated', async () => {
		const adminPage = new GenericAdminPage( page );
		await adminPage.logOut();

		const loginPage = new LoginPage( page );
		await loginPage.login( settings.user1Username, settings.user1Password );

		await expect( loginPage.getSecondFactorProvider() ).resolves.toBe( 'TwoFactor_Provider_WebAuthn' );

		await page.locator( 'input[name="webauthn_response"]' ).evaluate( ( el: HTMLInputElement ) => {
			el.value = 'null';
		} );

		const [ resp ] = await Promise.all( [
			page.waitForResponse( ( response ) => response.status() === 200 && response.request().isNavigationRequest() ),
			page.locator( '#loginform' ).evaluate( ( form: HTMLFormElement ) => form.submit() ),
		] );

		await resp.finished();

		expect( page.url() ).toContain( '/wp-login.php' );
		return expect( page.locator( '#login_error' ) ).toHaveText( /Invalid verification code/u );
	} );
} );
