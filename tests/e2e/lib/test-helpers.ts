import { type Page, type CDPSession, expect } from '@playwright/test';
import { LoginPage } from './loginpage';
import type { Protocol } from 'playwright-core/types/protocol';
import { ProfilePage } from './profilepage';
import { getCredentials } from './webauthn-helpers';

export async function login(page: Page, username: string, password: string): Promise<void> {
	const loginPage = new LoginPage(page);
	await loginPage.visit();
	await loginPage.login(username, password);
}

export async function registerKey(
	page: Page,
	client: CDPSession,
	authenticatorId: string,
	keyName: string,
): Promise<Protocol.WebAuthn.Credential> {
	const profilePage = new ProfilePage(page);
	await profilePage.visit();
	const locator = await profilePage.registerKey(keyName);
	await expect(locator).toContainText('The key has been registered');

	const credentials = await getCredentials(client, authenticatorId);
	return credentials[0];
}
