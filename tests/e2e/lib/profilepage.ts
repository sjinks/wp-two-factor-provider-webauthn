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
	operationStatus: (text: string) => `div[role="alert"] > p:has-text("${text}")`,
};

const waKeyActionsSelectors = {
	keyName: 'span.key-name',
	revokeKey: 'span.delete > a[data-handle]',
	revokeKeyConfirmButton: 'div.confirm-revoke button.button-link-delete',
	revokeKeyDismissButton: 'div.confirm-revoke button.button-secondary',
	renameKey: 'span.rename > a[data-handle]',
	renameKeyInput: '.rename-key label > input[type="text"]',
	renameKeyConfirmButton: '.rename-key button.button-primary',
	renameKeyDismissButton: '.rename-key button.button-secondary',
};

const ajaxRequestChecker =
	(action: string): ((response: Response) => boolean) =>
	(response: Response): boolean =>
		response.url().endsWith('/wp-admin/admin-ajax.php') &&
		response.request().method() === 'POST' &&
		(response.request().postData() ?? '').includes(`action=${encodeURIComponent(action)}`);

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

		return this.page.waitForSelector(waSelectors.operationStatus('The key has been registered'));
	}

	public async enableWebAuthnProvider(): Promise<unknown> {
		await this.twoFactorOptionsLocator.scrollIntoViewIfNeeded();
		return this.twoFactorOptionsLocator.locator(tfoSelectors.webAuthnEnabledCheckbox).check();
	}

	public async makeWebAuthnProviderPrimary(): Promise<unknown> {
		await this.twoFactorOptionsLocator.scrollIntoViewIfNeeded();
		return this.twoFactorOptionsLocator.locator(tfoSelectors.webAuthnPrimaryProvider).check();
	}

	public async saveProfile(): Promise<void> {
		await this.updateProfileButtonLocator.click();
		await this.page.waitForLoadState('domcontentloaded');
	}

	public async revokeKey(credentialId: string): Promise<unknown> {
		const keyActionsLocator = this.webAuthnSectionLocator.locator(waSelectors.keyActions(credentialId));
		const noItemsRowLocator = this.webAuthnSectionLocator.locator(waSelectors.noItemsRow);

		const revokeLinkLocator = keyActionsLocator.locator(waKeyActionsSelectors.revokeKey);
		const revokeConfirmLocator = keyActionsLocator.locator(waKeyActionsSelectors.revokeKeyConfirmButton);

		await keyActionsLocator.scrollIntoViewIfNeeded();
		await keyActionsLocator.hover();
		await revokeLinkLocator.click();
		await Promise.all([
			this.page.waitForResponse(ajaxRequestChecker('webauthn_delete_key')),
			revokeConfirmLocator.click(),
		]);

		return noItemsRowLocator.waitFor({ state: 'visible' });
	}

	public async renameKey(credentialId: string, newName: string, doRename: boolean): Promise<void> {
		const keyActionsLocator = this.webAuthnSectionLocator.locator(waSelectors.keyActions(credentialId));
		const renameLinkLocator = keyActionsLocator.locator(waKeyActionsSelectors.renameKey);
		const newNameInputLocator = keyActionsLocator.locator(waKeyActionsSelectors.renameKeyInput);
		const confirmRenameLocator = keyActionsLocator.locator(waKeyActionsSelectors.renameKeyConfirmButton);

		await keyActionsLocator.scrollIntoViewIfNeeded();
		await keyActionsLocator.hover();
		await renameLinkLocator.click();

		if (doRename) {
			await newNameInputLocator.fill(newName);
			await Promise.all([
				this.page.waitForResponse(ajaxRequestChecker('webauthn_rename_key')),
				confirmRenameLocator.click(),
			]);

			await this.page.waitForSelector(
				waSelectors.operationStatus(newName ? 'The key has been renamed' : 'Key name cannot be empty'),
			);
		}
	}

	public getKeyNameByCID(credentialId: string): Promise<string> {
		const keyActionsLocator = this.webAuthnSectionLocator.locator(waSelectors.keyActions(credentialId));
		const keyNameLocator = keyActionsLocator.locator(waKeyActionsSelectors.keyName);
		return keyNameLocator.innerText();
	}

	public dismissRevokeConfirmation(credentialId: string): Promise<unknown> {
		const keyActionsLocator = this.webAuthnSectionLocator.locator(waSelectors.keyActions(credentialId));
		const dismissButtonLocator = keyActionsLocator.locator(waKeyActionsSelectors.revokeKeyDismissButton);
		return Promise.all([dismissButtonLocator.waitFor({ state: 'detached' }), dismissButtonLocator.click()]);
	}

	public dismissRenameConfirmation(credentialId: string): Promise<unknown> {
		const keyActionsLocator = this.webAuthnSectionLocator.locator(waSelectors.keyActions(credentialId));
		const dismissButtonLocator = keyActionsLocator.locator(waKeyActionsSelectors.renameKeyDismissButton);
		return Promise.all([dismissButtonLocator.waitFor({ state: 'detached' }), dismissButtonLocator.click()]);
	}
}
