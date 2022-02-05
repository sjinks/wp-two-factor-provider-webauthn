import { test, expect, CDPSession } from '@playwright/test';
import { LoginPage } from '../lib/loginpage';
import { ProfilePage } from '../lib/profilepage';
import { GenericAdminPage } from '../lib/genericadminpage';
import settings from '../e2e-settings';
import { addVirtualAuthenticator, getCredential, removeVirtualAuthenticator } from '../lib/webauthn-helpers';
import { login, registerKey } from '../lib/test-helpers';

let client: CDPSession;
let authenticatorId: string;
let credentialId: string;
let signCount: number;

test.beforeEach(async ({ context, page }) => {
	client = await context.newCDPSession(page);
	authenticatorId = await addVirtualAuthenticator(client, 'ctap2', 'usb');
});

test.afterEach(() => removeVirtualAuthenticator(client, authenticatorId));

test('Login Workflow', async ({ page }) => {
	await test.step('Log in', async () => {
		await login(page, settings.user1Username, settings.user1Password);
		return expect(page.url()).toMatch('/wp-admin/');
	});

	await test.step('Register key', async () => {
		const credential = await registerKey(page, client, authenticatorId, 'Test Key');
		credentialId = credential.credentialId;
		signCount = credential.signCount;
	});

	await test.step('Configure WebAuthn provider', async () => {
		expect(page.url()).toMatch('/wp-admin/profile.php');
		const profilePage = new ProfilePage(page);
		await profilePage.enableWebAuthnProvider();
		await profilePage.makeWebAuthnProviderPrimary();
		return profilePage.saveProfile();
	});

	await test.step('Log out', async () => {
		const adminPage = new GenericAdminPage(page);
		await adminPage.logOut();
		return expect(page.url()).toMatch('/wp-login.php');
	});

	await test.step('Log in with key', async () => {
		expect(page.url()).toMatch('/wp-login.php');
		const loginPage = new LoginPage(page);
		await loginPage.login(settings.user1Username, settings.user1Password);

		expect(loginPage.getSecondFactorProvider()).resolves.toBe('TwoFactor_Provider_WebAuthn');
		await loginPage.loginWithKey();

		const credential = await getCredential(client, authenticatorId, credentialId);
		return expect(credential.signCount).toBeGreaterThan(signCount);
	});
});
