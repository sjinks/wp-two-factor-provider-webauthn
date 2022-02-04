import { test, expect, CDPSession } from '@playwright/test';
import settings from '../e2e-settings';
import { addVirtualAuthenticator, removeVirtualAuthenticator } from '../lib/webauthn-helpers';
import { login, registerKey } from '../lib/test-helpers';
import { ProfilePage } from '../lib/profilepage';

let client: CDPSession;
let authenticatorId: string;
let credentialId: string;

test.beforeEach(async ({ context, page }) => {
	client = await context.newCDPSession(page);
	authenticatorId = await addVirtualAuthenticator(client, 'ctap2', 'usb');
});

test.afterEach(() => removeVirtualAuthenticator(client, authenticatorId));

test('Revoke Key Workflow', async ({ page }) => {
	await test.step('Log in', async () => {
		await login(page, settings.user2Username, settings.user2Password);
		return expect(page.url()).toMatch('/wp-admin/');
	});

	await test.step('Register key', async () => {
		const credential = await registerKey(page, client, authenticatorId, 'Test Key');
		credentialId = credential.credentialId;
	});

	await test.step('Revoke key', () => {
		expect(page.url()).toMatch('/wp-admin/profile.php');
		const profilePage = new ProfilePage(page);
		const recodedCID = credentialId.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
		return profilePage.revokeKey(recodedCID);
	});
});
