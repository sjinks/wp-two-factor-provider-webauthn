import { test, expect } from '@playwright/test';
import settings from '../e2e-settings';
import { login } from '../lib/test-helpers';
import { Settings, WebAuthnSettingsPage } from '../lib/webauthsettingspage';

test('Modify Settings Workflow', async ({ page }) => {
	await test.step('Log in', async () => {
		await login(page, settings.adminUsername, settings.adminPassword);
		return expect(page.url()).toMatch('/wp-admin/');
	});

	const settingsPage = new WebAuthnSettingsPage(page);
	const defaultSettings: Settings = {
		authenticatorAttachment: '',
		uvRequirement: 'preferred',
		timeout: 0,
		u2fHack: true,
	};

	const newSettings: Partial<Settings> = {
		uvRequirement: 'discouraged',
		timeout: 120000,
		u2fHack: false,
	};

	await test.step('Go to Settings page', () => settingsPage.visit());

	await test.step('Check default settings', () =>
		expect(settingsPage.getSettings()).resolves.toEqual(defaultSettings),
	);

	await test.step('Apply new settings', async () => {
		await settingsPage.setSettings(newSettings);
		await settingsPage.saveSettings();
	});

	await test.step('Check new settings', () =>
		expect(settingsPage.getSettings()).resolves.toEqual({ ...defaultSettings, ...newSettings }),
	);
});
