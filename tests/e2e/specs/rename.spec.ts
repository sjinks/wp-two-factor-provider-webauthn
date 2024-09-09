import { test, expect, type CDPSession } from '@playwright/test';
import settings from '../e2e-settings';
import { addVirtualAuthenticator, clearCredentials, removeVirtualAuthenticator } from '../lib/webauthn-helpers';
import { login, registerKey } from '../lib/test-helpers';
import { ProfilePage } from '../lib/profilepage';

let client: CDPSession;
let authenticatorId: string;
let credential1Id: string;
let credential2Id: string;
const key1Name = 'Test Key 1';
const key2Name = 'Test Key 2';

test.beforeEach(async ({ context, page }) => {
	client = await context.newCDPSession(page);
	authenticatorId = await addVirtualAuthenticator(client, 'ctap2', 'usb');
});

test.afterEach(() => removeVirtualAuthenticator(client, authenticatorId));

test('Rename Key Workflow', async ({ page }) => {
	await test.step('Log in', async () => {
		await login(page, settings.user3Username, settings.user3Password);
		return expect(page.url()).toContain('/wp-admin/');
	});

	await test.step('Register key', async () => {
		let credential = await registerKey(page, client, authenticatorId, key1Name);
		credential1Id = credential.credentialId;

		// We won't be trying to log in, that's why it is safe to kill the registered credential.
		// Without this step, we won't be able to register another key.
		await clearCredentials(client, authenticatorId);

		credential = await registerKey(page, client, authenticatorId, key2Name);
		credential2Id = credential.credentialId;
	});

	await test.step('Rename key', async () => {
		const newKey1Name = `${key1Name}!`;
		const newKey2Name = `${key2Name}!`;

		expect(page.url()).toContain('/wp-admin/profile.php');
		const profilePage = new ProfilePage(page);
		const recodedCID1 = credential1Id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
		const recodedCID2 = credential2Id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
		await profilePage.renameKey(recodedCID1, newKey1Name, false);
		const locator = await profilePage.renameKey(recodedCID2, newKey2Name, true);
		await expect(locator).toContainText('The key has been renamed');

		const [actualKeyName1, actualKeyName2] = await Promise.all([
			profilePage.getKeyNameByCID(recodedCID1),
			profilePage.getKeyNameByCID(recodedCID2),
		]);

		const expectedKey1Name = key1Name;
		const expectedKey2Name = newKey2Name;

		expect(actualKeyName1).toBe(expectedKey1Name);
		expect(actualKeyName2).toBe(expectedKey2Name);
	});

	await test.step('Dismiss the other rename form', () => {
		const profilePage = new ProfilePage(page);
		const recodedCID1 = credential1Id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
		return profilePage.dismissRenameConfirmation(recodedCID1);
	});

	await test.step('Rename should not accept an empty name', async () => {
		expect(page.url()).toContain('/wp-admin/profile.php');
		const profilePage = new ProfilePage(page);
		const recodedCID1 = credential1Id.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
		const locator = await profilePage.renameKey(recodedCID1, '', true);
		await expect(locator).toContainText('Key name cannot be empty');

		const actualKeyName1 = await profilePage.getKeyNameByCID(recodedCID1);
		const expectedKey1Name = key1Name;

		expect(actualKeyName1).toBe(expectedKey1Name);
	});
});
