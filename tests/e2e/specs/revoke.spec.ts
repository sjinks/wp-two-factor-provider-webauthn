import settings from '../e2e-settings';
import { ProfilePage } from '../lib/profilepage';
import { login, registerKey } from '../lib/test-helpers';
import { test, expect } from '../lib/webauthn-helpers';

test( 'Revoke Key Workflow', async ( { page, credentials } ) => {
	let credentialId: string;

	await test.step( 'Log in', async () => {
		await login( page, settings.user2Username, settings.user2Password );
		return expect( page.url() ).toContain( '/wp-admin/' );
	} );

	await test.step( 'Register key', async () => {
		const credential = await registerKey( page, credentials, 'Test Key' );
		credentialId = credential.id;
	} );

	await test.step( 'Revoke key', () => {
		expect( page.url() ).toContain( '/wp-admin/profile.php' );
		const profilePage = new ProfilePage( page );
		return profilePage.revokeKey( credentialId );
	} );
} );
