import { type Page, type Credentials, expect } from '@playwright/test';

import { LoginPage } from './loginpage';
import { ProfilePage } from './profilepage';

export async function login( page: Page, username: string, password: string ): Promise<void> {
	const loginPage = new LoginPage( page );
	await loginPage.visit();
	await loginPage.login( username, password );
}

export async function registerKey(
	page: Page,
	credentials: Credentials,
	keyName: string,
): Promise<Awaited<ReturnType<Credentials[ 'get' ]>>[ number ]> {
	const profilePage = new ProfilePage( page );
	await profilePage.visit();
	const locator = await profilePage.registerKey( keyName );
	await expect( locator ).toContainText( 'The key has been registered' );

	const registeredCredentials = await credentials.get();
	expect( registeredCredentials ).toHaveLength( 1 );
	return registeredCredentials[ 0 ];
}
