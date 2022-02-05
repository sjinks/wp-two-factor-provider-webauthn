import { Locator, Page } from '@playwright/test';

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

	public async login(username: string, password: string): Promise<unknown> {
		await this.userFieldLocator.fill(username);
		await this.passwordFieldLocator.fill(password);
		return Promise.all([
			this.page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
			this.submitButtonLocator.click(),
		]);
	}

	public getSecondFactorProvider(): Promise<string> {
		return this.providerInputLocator.inputValue();
	}

	public loginWithKey(): Promise<unknown> {
		return Promise.all([
			this.page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
			this.webAuttnRetryButtonLocator.click(),
		]);
	}
}
