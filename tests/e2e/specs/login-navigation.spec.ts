import { test, expect } from '@playwright/test';

import settings from '../e2e-settings';
import { ProfilePage } from '../lib/profilepage';
import { login } from '../lib/test-helpers';

test( 'Wait for deferred login before opening the profile', async ( { page } ) => {
	await page.addInitScript( () => {
		document.addEventListener( 'submit', ( event ) => {
			const form = event.target;
			if ( form instanceof HTMLFormElement && form.id === 'loginform' ) {
				event.preventDefault();
				// Model a submission scheduled after the click handler returns.
				setTimeout( () => form.submit(), 250 );
			}
		} );
	} );

	await test.step( 'Log in and immediately open the profile', async () => {
		await login( page, settings.adminUsername, settings.adminPassword );
		await new ProfilePage( page ).visit();
	} );

	await expect( page.getByRole( 'button', { name: 'Register New Key' } ) ).toBeVisible();
} );
