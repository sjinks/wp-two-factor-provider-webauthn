import { chromium, expect, FullConfig } from '@playwright/test';
import { LoginPage } from './lib/loginpage';
import { PluginsPage } from './lib/pluginspage';
import settings from './e2e-settings';

export default async function golbalSetup(config: FullConfig): Promise<void> {
	const { baseURL, headless } = config.projects[0].use;

	const browser = await chromium.launch({ headless });
	const context = await browser.newContext({
		baseURL,
		ignoreHTTPSErrors: true,
	});

	const page = await context.newPage();

	const loginPage = new LoginPage(page);
	await loginPage.visit();
	await loginPage.login(settings.adminUsername, settings.adminPassword);

	const nonce: string = await page.evaluate('wpApiSettings.nonce');

	const pluginsPage = new PluginsPage(page);
	await pluginsPage.visit();
	if (await pluginsPage.activatePlugin('WebAuthn Provider for Two Factor')) {
		expect(pluginsPage.getMessage()).resolves.toMatch('Plugin activated');
	}

	const [response1, response2, response3] = await Promise.all([
		context.request.post('/wp-json/wp/v2/users', {
			data: {
				username: settings.user1Username,
				password: settings.user1Password,
				email: settings.user1Email,
			},
			headers: {
				'X-WP-Nonce': nonce,
			},
		}),
		context.request.post('/wp-json/wp/v2/users', {
			data: {
				username: settings.user2Username,
				password: settings.user2Password,
				email: settings.user2Email,
			},
			headers: {
				'X-WP-Nonce': nonce,
			},
		}),
		context.request.post('/wp-json/wp/v2/users', {
			data: {
				username: settings.user3Username,
				password: settings.user3Password,
				email: settings.user3Email,
			},
			headers: {
				'X-WP-Nonce': nonce,
			},
		}),
	]);

	expect(response1.ok()).toBeTruthy();
	expect(response2.ok()).toBeTruthy();
	expect(response3.ok()).toBeTruthy();

	await context.request.dispose();
	await browser.close();
}
