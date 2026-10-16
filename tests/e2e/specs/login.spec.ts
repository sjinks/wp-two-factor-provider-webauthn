import settings from '../e2e-settings';
import { GenericAdminPage } from '../lib/genericadminpage';
import { LoginPage } from '../lib/loginpage';
import { ProfilePage } from '../lib/profilepage';
import { login, registerKey } from '../lib/test-helpers';
import { test, expect } from '../lib/webauthn-helpers';

test( 'Login Workflow', async ( { page, credentials, browserName } ) => {
	const username = `${ settings.user1Username }-${ browserName }`;
	let credentialId: string;
	let signCount: number;

	await test.step( 'Log in', async () => {
		await login( page, username, settings.user1Password );
		return expect( page.url() ).toContain( '/wp-admin/' );
	} );

	await test.step( 'Register key', async () => {
		const credential = await registerKey( page, credentials, 'Test Key' );
		credentialId = credential.id;
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
		await loginPage.login( username, settings.user1Password );

		await expect( loginPage.getSecondFactorProvider() ).resolves.toBe( 'TwoFactor_Provider_WebAuthn' );
		await loginPage.loginWithKey();

		const [ credential ] = await credentials.get( { id: credentialId } );
		return expect( credential.signCount ).toBeGreaterThan( signCount );
	} );

	await test.step( 'Authentication bypass is mitigated', async () => {
		const adminPage = new GenericAdminPage( page );
		await adminPage.logOut();

		const loginPage = new LoginPage( page );
		await loginPage.login( username, settings.user1Password );

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
