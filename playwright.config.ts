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

	forbidOnly: Boolean( process.env.CI ),
	retries: 0,
	// Browser projects share WordPress users and global plugin settings.
	workers: 1,
	reporter: process.env.CI ? 'github' : 'line',

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
				...devices[ 'Desktop Chrome' ],
			},
		},
		{
			name: 'firefox',
			use: {
				...devices[ 'Desktop Firefox' ],
			},
		},
		{
			name: 'webkit',
			use: {
				...devices[ 'Desktop Safari' ],
			},
		},
	],

	outputDir: 'test-results/',
};

export default config;
