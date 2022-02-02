import { Page } from '@playwright/test';

const selectors = {
	activate: (name: string) => `td.plugin-title > strong:text("${name}") + div.row-actions > span.activate > a`,
	deactivate: (name: string) => `td.plugin-title > strong:text("${name}") + div.row-actions > span.deactivate > a`,
	message: '#message',
};

export class PluginsPage {
	private readonly page: Page;

	public constructor(page: Page) {
		this.page = page;
	}

	public visit(): Promise<unknown> {
		return this.page.goto('/wp-admin/plugins.php', { waitUntil: 'domcontentloaded' });
	}

	public async activatePlugin(name: string): Promise<boolean> {
		const activateLinkLocator = this.page.locator(selectors.activate(name));
		const deactivateLinkLocator = this.page.locator(selectors.deactivate(name));

		const [cntActivate] = await Promise.all([activateLinkLocator.count(), deactivateLinkLocator.count()]);

		if (cntActivate === 1) {
			await Promise.all([
				this.page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
				activateLinkLocator.click(),
			]);
			return true;
		}

		return false;
	}

	public async getMessage(): Promise<string> {
		const messageSelector = this.page.locator(selectors.message);
		return messageSelector.locator('p').first().innerText();
	}
}
