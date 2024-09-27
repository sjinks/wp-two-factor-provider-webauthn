import type { Locator, Page } from '@playwright/test';

const selectors = {
	userField: '#user_login',
	passwordField: '#user_pass',
	submitButton: '#wp-submit',
	tfaForm: 'form[name="validate_2fa_form"]',
	providerInput: 'input#provider',
	webAuthnRetryButton: '#webauthn-retry > button',
};

export class LoginPage {
	private readonly page: Page;

	private readonly userFieldLocator: Locator;
	private readonly passwordFieldLocator: Locator;
	private readonly submitButtonLocator: Locator;

	private readonly tfaFormLocator: Locator;
	private readonly providerInputLocator: Locator;
	private readonly webAuttnRetryButtonLocator: Locator;

	public constructor(page: Page) {
		this.page = page;

		this.userFieldLocator = page.locator(selectors.userField);
		this.passwordFieldLocator = page.locator(selectors.passwordField);
		this.submitButtonLocator = page.locator(selectors.submitButton);

		this.tfaFormLocator = page.locator(selectors.tfaForm);
		this.providerInputLocator = this.tfaFormLocator.locator(selectors.providerInput);
		this.webAuttnRetryButtonLocator = this.tfaFormLocator.locator(selectors.webAuthnRetryButton);
	}

	public visit(): Promise<unknown> {
		return this.page.goto('/wp-login.php');
	}

	public async login(username: string, password: string): Promise<void> {
		await this.userFieldLocator.click();
		// eslint-disable-next-line playwright/no-wait-for-timeout
		await this.page.waitForTimeout(60);
		await this.userFieldLocator.fill(username);

		await this.passwordFieldLocator.click();
		// eslint-disable-next-line playwright/no-wait-for-timeout
		await this.page.waitForTimeout(60);
		await this.passwordFieldLocator.fill(password);

		// eslint-disable-next-line playwright/no-wait-for-timeout
		await this.page.waitForTimeout(60);
		await this.submitButtonLocator.click();
		await this.page.waitForLoadState('domcontentloaded');
	}

	public getSecondFactorProvider(): Promise<string> {
		return this.providerInputLocator.inputValue();
	}

	public async loginWithKey(): Promise<void> {
		await this.webAuttnRetryButtonLocator.click();
		return this.page.waitForLoadState('domcontentloaded');
	}
}
