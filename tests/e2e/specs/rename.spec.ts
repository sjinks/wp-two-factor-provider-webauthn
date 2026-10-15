import settings from '../e2e-settings';
import { ProfilePage } from '../lib/profilepage';
import { login, registerKey } from '../lib/test-helpers';
import { test, expect } from '../lib/webauthn-helpers';

const key1Name = 'Test Key 1';
const key2Name = 'Test Key 2';

test( 'Rename Key Workflow', async ( { page, credentials } ) => {
	let credential1Id: string;
	let credential2Id: string;

	await test.step( 'Log in', async () => {
		await login( page, settings.user3Username, settings.user3Password );
		return expect( page.url() ).toContain( '/wp-admin/' );
	} );

	await test.step( 'Register key', async () => {
		let credential = await registerKey( page, credentials, key1Name );
		credential1Id = credential.id;

		// We won't be trying to log in, that's why it is safe to kill the registered credential.
		// Without this step, we won't be able to register another key.
		await credentials.delete( credential1Id );

		credential = await registerKey( page, credentials, key2Name );
		credential2Id = credential.id;
	} );

	await test.step( 'Rename key', async () => {
		const newKey1Name = `${ key1Name }!`;
		const newKey2Name = `${ key2Name }!`;

		expect( page.url() ).toContain( '/wp-admin/profile.php' );
		const profilePage = new ProfilePage( page );
		await profilePage.renameKey( credential1Id, newKey1Name, false );
		const locator = await profilePage.renameKey( credential2Id, newKey2Name, true );
		await expect( locator ).toContainText( 'The key has been renamed' );

		const [ actualKeyName1, actualKeyName2 ] = await Promise.all( [
			profilePage.getKeyNameByCID( credential1Id ),
			profilePage.getKeyNameByCID( credential2Id ),
		] );

		const expectedKey1Name = key1Name;
		const expectedKey2Name = newKey2Name;

		expect( actualKeyName1 ).toBe( expectedKey1Name );
		expect( actualKeyName2 ).toBe( expectedKey2Name );
	} );

	await test.step( 'Dismiss the other rename form', () => {
		const profilePage = new ProfilePage( page );
		return profilePage.dismissRenameConfirmation( credential1Id );
	} );

	await test.step( 'Rename should not accept an empty name', async () => {
		expect( page.url() ).toContain( '/wp-admin/profile.php' );
		const profilePage = new ProfilePage( page );
		const locator = await profilePage.renameKey( credential1Id, '', true );
		await expect( locator ).toContainText( 'Key name cannot be empty' );

		const actualKeyName1 = await profilePage.getKeyNameByCID( credential1Id );
		const expectedKey1Name = key1Name;

		expect( actualKeyName1 ).toBe( expectedKey1Name );
	} );
} );
