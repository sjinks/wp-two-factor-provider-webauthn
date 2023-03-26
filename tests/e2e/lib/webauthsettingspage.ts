import { Locator, Page } from '@playwright/test';

const selectors = {
	authenticatorAttachment: '#authenticator_attachment',
	uvRequirement: '#user_verification_requirement',
	timeout: '#timeout',
	u2fHack: '#u2f_hack',
	submitButton: '#submit',
	notice: 'div.notice.settings-error > p',
};

export interface Settings {
	authenticatorAttachment: string;
	uvRequirement: string;
	timeout: number;
	u2fHack: boolean;
}

export class WebAuthnSettingsPage {
	private readonly page: Page;

	private authenticatorAttachmentLocator: Locator;
	private uvRequirementLocator: Locator;
	private timeoutLocator: Locator;
	private u2fHackLocator: Locator;
	private submitButtonLocator: Locator;
	private noticeLocator: Locator;

	public constructor(page: Page) {
		this.page = page;

		this.authenticatorAttachmentLocator = this.page.locator(selectors.authenticatorAttachment);
		this.uvRequirementLocator = this.page.locator(selectors.uvRequirement);
		this.timeoutLocator = this.page.locator(selectors.timeout);
		this.u2fHackLocator = this.page.locator(selectors.u2fHack);
		this.submitButtonLocator = this.page.locator(selectors.submitButton);
		this.noticeLocator = this.page.locator(selectors.notice);
	}

	public visit(): Promise<unknown> {
		return Promise.all([
			this.page.waitForURL((url) => url.pathname === '/wp-admin/options-general.php', {
				waitUntil: 'domcontentloaded',
			}),
			this.page.goto('/wp-admin/options-general.php?page=2fa-webauthn'),
		]);
	}

	public async getSettings(): Promise<Settings> {
		const waitTimeout = 1000;
		const [authenticatorAttachment, uvRequirement, timeout, u2fHack] = await Promise.all([
			this.authenticatorAttachmentLocator.inputValue({ timeout: waitTimeout }),
			this.uvRequirementLocator.inputValue({ timeout: waitTimeout }),
			this.timeoutLocator.inputValue({ timeout: waitTimeout }),
			this.u2fHackLocator.isChecked({ timeout: waitTimeout }),
		]);

		return { authenticatorAttachment, uvRequirement, timeout: +timeout, u2fHack };
	}

	public setSettings(settings: Partial<Settings>): Promise<unknown> {
		const promises: Promise<unknown>[] = [];
		if (settings.authenticatorAttachment !== undefined) {
			promises.push(this.authenticatorAttachmentLocator.selectOption(settings.authenticatorAttachment));
		}

		if (settings.uvRequirement !== undefined) {
			promises.push(this.uvRequirementLocator.selectOption(settings.uvRequirement));
		}

		if (settings.timeout !== undefined) {
			promises.push(this.timeoutLocator.fill(settings.timeout.toString()));
		}

		if (settings.u2fHack !== undefined) {
			promises.push(this.u2fHackLocator.setChecked(settings.u2fHack));
		}

		return Promise.all(promises);
	}

	public async saveSettings(): Promise<string> {
		await this.submitButtonLocator.click();
		await this.page.waitForLoadState('domcontentloaded');
		return `${this.noticeLocator.textContent()}`;
	}
}
