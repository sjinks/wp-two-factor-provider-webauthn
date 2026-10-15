import { test as base, type Credentials } from '@playwright/test';

export { expect } from '@playwright/test';

export const test = base.extend<{ credentials: Credentials }>( {
	credentials: async ( { context }, use ) => {
		await context.credentials.install();
		await use( context.credentials );
	},
} );
