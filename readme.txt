=== WebAuthn Provider for Two Factor ===
Contributors: volodymyrkolesnykov
Donate link: https://www.paypal.com/donate/?hosted_button_id=SAG6877JDJ3KU
Tags: 2fa, webauthn, two factor, login, security, authentication
Requires at least: 5.5
Tested up to: 6.0
Stable tag: 1.0.6
Requires PHP: 7.4
License: MIT
License URI: https://opensource.org/licenses/MIT

WebAuthn authentication provider for Two Factor plugin.

== Description ==

This plugin adds support for WebAuthn into the [Two Factor](https://wordpress.org/plugins/two-factor/) plugin.

Because the U2F API [is deprecated and will be removed in February 2022](https://groups.google.com/a/chromium.org/g/blink-dev/c/xHC3AtU_65A/m/yg20tsVFBAAJ), the plugin enables [seamless support](https://user-images.githubusercontent.com/7810770/150708968-3c331612-54ad-4373-9d36-6ec064301755.mp4) for the previously registered U2F security keys so that the users don't have to re-register their keys and still be able to log in.

Notes:

* please use [GitHub issues](https://github.com/sjinks/wp-two-factor-provider-webauthn/issues) to report bugs;
* the full source code with all development files is available on [GitHub](https://github.com/sjinks/wp-two-factor-provider-webauthn).

== Frequently Asked Questions ==

Be the first to ask.

== Screenshots ==

1. User profile settings showing the registered security keys.
2. Plugin settings page.

== Changelog ==

= 1.0.6 =
* GH-93: remove unnecessary `required` attribute from `webauthn_key_name`
* Security: Update guzzlehttp/guzzle to 7.4.4 (fix CVE-2022-31042 and CVE-2022-31043)
* Update development dependencies

= 1.0.5 =
* Synchronize plugin version across all files

= 1.0.4 =
* Update translations
* GH-93: add an option to turn off the old U2F provider
* Update dependencies
* Add more E2E tests

= 1.0.3 =
* GH-33: increase length of credential_id column to solve issues with Chrome on Mac
* GH-38: fix bugs preventing plugin uninstallation
* Make Settings::offsetGet() compatible with PHP 8.1

= 1.0.2 =
* Added E2E tests
* UI fixes

= 1.0.1 =
* First public release.

== Upgrade Notice ==

None yet.
