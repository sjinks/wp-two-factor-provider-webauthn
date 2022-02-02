import { PlaywrightTestConfig, devices } from '@playwright/test';

/**
 * See https://playwright.dev/docs/test-configuration.
 */
const config: PlaywrightTestConfig = {
	testDir: './tests/e2e/specs',
	timeout: 30 * 1000,
	expect: {
		timeout: 5000,
	},

	forbidOnly: !!process.env.CI,
	retries: 0,
	workers: process.env.CI ? 2 : undefined,
	reporter: process.env.CI ? 'github' : 'line',

	globalSetup: require.resolve('./tests/e2e/global-setup'),

	use: {
		actionTimeout: 0,
		baseURL: process.env.PLAYWRIGHT_BASE_URL || 'https://localhost:8443',
		ignoreHTTPSErrors: true,
		headless: process.env.CI ? true : undefined,
		video: 'retain-on-failure',
		trace: 'retain-on-failure',
	},

	projects: [
		{
			name: 'chromium',
			use: {
				...devices['Desktop Chrome'],
			},
		},
	],

	outputDir: 'test-results/',
};

export default config;
