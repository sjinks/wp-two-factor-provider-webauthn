import { Locator, Page } from '@playwright/test';

const selectors = {
	menuBarMyAccount: '#wp-admin-bar-my-account a[aria-haspopup="true"]',
	logoutLink: '#wp-admin-bar-logout > a',
};

export class GenericAdminPage {
	private readonly page: Page;

	private readonly menuBarMyAccountLocator: Locator;
	private readonly logoutLinkLocator: Locator;

	public constructor(page: Page) {
		this.page = page;

		this.menuBarMyAccountLocator = page.locator(selectors.menuBarMyAccount);
		this.logoutLinkLocator = page.locator(selectors.logoutLink);
	}

	public async logOut(): Promise<unknown> {
		await this.menuBarMyAccountLocator.hover();
		await this.logoutLinkLocator.waitFor({ state: 'visible' });
		return Promise.all([this.page.waitForNavigation(), this.logoutLinkLocator.click()]);
	}
}
