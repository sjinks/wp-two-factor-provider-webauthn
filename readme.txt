=== WebAuthn Provider for Two Factor ===
Contributors: volodymyrkolesnykov
Donate link: https://www.paypal.com/donate/?hosted_button_id=SAG6877JDJ3KU
Tags: 2fa, webauthn, two factor, login, security, authentication
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 2.5.3
Requires PHP: 8.1
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

= 2.5.4 =
* Platform requirements updated to PHP 8.1 and WordPress 6.0 (although the plugin still should work with older versions of PHP and WordPress)
* GH-1008: better integration with Two Factor 0.13.0

= 2.5.3 =
* Restore `WebAuthn_Provider::get_instance()` because WPVIP has an ancient version of Two Factor

= 2.5.2 =
* Fix the conflict when another package loads a library that has `autoload.files` key (see https://github.com/sjinks/wp-two-factor-provider-webauthn/pull/980)

= 2.5.1 =
* GH-898: do not show the UI if the plugin has failed to install its tables
* GH-972: do not show the profile UI if the provider is disabled
* drop official PHP 7.4 support

= 2.5.0 =
* iCloud support for Firefox (props dd32)

= 2.4.1 =
* GH-541: fix issues with YubiKeys (backported a patch by Markus Bauer from https://github.com/madwizard-org/webauthn-server/pull/23)

= 2.4.0 =
* GH-830: introduce `webauthn_register_key_use_nicename` filter (props kat3samsin)

= 2.3.0 =
* GH-827: Add `webauthn_register_key_suppress_output` filter
* GH-826: Add `webauthn_app_id` filter to customize U2F AppID
* GH-824: Initialize `wpdb` properties as early as possible
* Update `madwizard/webauthn` to 0.10.0

= 2.2.0 =
* Do not create user handles if they are not needed
* Add a hook to customize WebAuthN server
* Update dependencies
* Refactor tests

= 2.1.0 =
* GH-462: Use correct user ID when editing a user
* GH-456: Set relying party ID to COOKIE_DOMAIN if it is available (props dd32)
* Allow only for network-wide plugin activation (to match Two Factor)

= 2.0.3 =
* Update translations (thank you, Copilot)
* Add Ukrainian translation (thank you, Copilot)

= 2.0.2 =
* Update madwizard/webauthn to 0.9.0
* Update development dependencies
* Update E2E tests

= 2.0.1 =
* GH-295: fix client extensions validation
* Update development dependencies

= 2.0.0 =
* Put external dependencies into a unique namespace (GH-36, GH-53, GH-236)
* Update madwizard/webauthn to 0.8.0
* Update development dependencies

= 1.0.10 =
* Add zh-tw translations (props [Chun-Chih Cheng](https://profiles.wordpress.org/alex1114/), [Alex Lion](https://profiles.wordpress.org/alexclassroom/))
* GH-215, GH-33: Fix "Unable to save the key to the database" error for long public keys
* Update development dependencies

= 1.0.9 =
* Update madwizard/webauthn to 0.8.0
* Update development dependencies
* Add debug mode (activated with `define( 'DEBUG_TFPWA', true );`)

= 1.0.8 =
* Security: Update guzzlehttp/guzzle to 7.4.5 (fix [CVE-2022-31090](https://github.com/advisories/GHSA-25mq-v84q-4j7r) and [CVE-2022-31091](https://github.com/advisories/GHSA-q559-8m2m-g699))
* Do not load the plugin while WordPress is being installed

= 1.0.7.1 =
* Fix deployment issue. It's time to automate the process

= 1.0.7 =
* GH-130: fix Network Installation issue
* Update development dependencies
* Add security-related workflows to CI
* Improve tests

= 1.0.6.1 =
* Fix deployment issue

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
