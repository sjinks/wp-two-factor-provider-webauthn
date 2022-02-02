import { Locator, Page, Response } from '@playwright/test';

const selectors = {
	twoFactorOptions: '#two-factor-options',
	webAuthnSection: '#webauthn-security-keys-section',
	updateProfileButton: 'p.submit > input#submit',
};

const tfoSelectors = {
	webAuthnEnabledCheckbox: 'input[name="_two_factor_enabled_providers[]"][value="TwoFactor_Provider_WebAuthn"]',
	webAuthnPrimaryProvider: 'input[name="_two_factor_provider"][value="TwoFactor_Provider_WebAuthn"]',
};

const waSelectors = {
	keyNameInput: '#webauthn-key-name',
	registerNewKeyButton: 'div.add-webauthn-key > p > button',
	keyActions: (credentialId: string) => `table.webauthn-keys > tbody td.name:has(a[data-handle="${credentialId}"])`,
	noItemsRow: 'table.webauthn-keys > tbody > tr.no-items',
};

const waKeyActionsSelectors = {
	revokeKey: (credentialId: string) => `span.delete > a[data-handle="${credentialId}"]`,
	revokeKeyConfirmationButton: (credentialId: string) => `div.confirm-revoke button.button-link-delete`,
};

const ajaxRequestChecker =
	(action: string): ((response: Response) => boolean) =>
	(response: Response): boolean =>
		response.url().endsWith('/wp-admin/admin-ajax.php') &&
		response.request().method() === 'POST' &&
		response
			.request()
			.postData()
			.includes(`action=${encodeURIComponent(action)}`);

export class ProfilePage {
	private readonly page: Page;

	private readonly twoFactorOptionsLocator: Locator;
	private readonly webAuthnSectionLocator: Locator;
	private readonly updateProfileButtonLocator: Locator;

	public constructor(page: Page) {
		this.page = page;

		this.twoFactorOptionsLocator = page.locator(selectors.twoFactorOptions);
		this.webAuthnSectionLocator = page.locator(selectors.webAuthnSection);
		this.updateProfileButtonLocator = page.locator(selectors.updateProfileButton);
	}

	public visit(): Promise<unknown> {
		return this.page.goto('/wp-admin/profile.php', { waitUntil: 'domcontentloaded' });
	}

	public async registerKey(keyName: string): Promise<unknown> {
		await this.webAuthnSectionLocator.scrollIntoViewIfNeeded();

		await this.webAuthnSectionLocator.locator(waSelectors.keyNameInput).fill(keyName);
		await Promise.all([
			this.page.waitForResponse(ajaxRequestChecker('webauthn_preregister')),
			this.page.waitForResponse(ajaxRequestChecker('webauthn_register')),
			this.webAuthnSectionLocator.locator(waSelectors.registerNewKeyButton).click(),
		]);

		return true;
	}

	public async enableWebAuthnProvider(): Promise<unknown> {
		await this.twoFactorOptionsLocator.scrollIntoViewIfNeeded();
		return this.twoFactorOptionsLocator.locator(tfoSelectors.webAuthnEnabledCheckbox).check();
	}

	public async makeWebAuthnProviderPrimary(): Promise<unknown> {
		await this.twoFactorOptionsLocator.scrollIntoViewIfNeeded();
		return this.twoFactorOptionsLocator.locator(tfoSelectors.webAuthnPrimaryProvider).check();
	}

	public saveProfile(): Promise<unknown> {
		return Promise.all([
			this.page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
			this.updateProfileButtonLocator.click(),
		]);
	}

	public async revokeKey(credentialId: string): Promise<unknown> {
		const keyActionsLocator = this.webAuthnSectionLocator.locator(waSelectors.keyActions(credentialId));
		const noItemsRowLocator = this.webAuthnSectionLocator.locator(waSelectors.noItemsRow);

		const revokeLinkLocator = keyActionsLocator.locator(waKeyActionsSelectors.revokeKey(credentialId));
		const revokeConfirmLocator = keyActionsLocator.locator(
			waKeyActionsSelectors.revokeKeyConfirmationButton(credentialId),
		);

		await keyActionsLocator.scrollIntoViewIfNeeded();
		await keyActionsLocator.hover();
		await revokeLinkLocator.waitFor({ state: 'visible' });
		await revokeLinkLocator.click();
		await revokeConfirmLocator.waitFor({ state: 'visible' });
		await Promise.all([
			this.page.waitForResponse(ajaxRequestChecker('webauthn_delete_key')),
			revokeConfirmLocator.click(),
		]);

		return noItemsRowLocator.waitFor({ state: 'visible' });
	}
}
