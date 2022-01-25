=== Plugin Name ===
Contributors: volodymyrkolesnykov
Donate link: https://example.com/
Tags: 2fa, webauthn, two factor, login, security, authentication
Requires at least: 5.5
Tested up to: 5.9
Stable tag: trunk
Requires PHP: 7.4
License: MIT
License URI: https://opensource.org/licenses/MIT

WebAuthn authentication provider for Two Factor plugin.

== Description ==

This plugin adds support for WebAuthn into the [Two Factor](https://wordpress.org/plugins/two-factor/) plugin.

Because the U2F API [is deprecated and will be removed in February 2022](https://groups.google.com/a/chromium.org/g/blink-dev/c/xHC3AtU_65A/m/yg20tsVFBAAJ),
the plugin enables [seamless support](https://user-images.githubusercontent.com/7810770/150708968-3c331612-54ad-4373-9d36-6ec064301755.mp4)
for the previously registered U2F security keys so that the users don't have to re-register their keys and still be able to log in.

Notes:
* please use [GitHub issues](https://github.com/sjinks/wp-two-factor-provider-webauthn/issues) to report bugs;
* the full source code with all development files is available on [GitHub](https://github.com/sjinks/wp-two-factor-provider-webauthn).

== Frequently Asked Questions ==

Be the first to ask.

== Screenshots ==

1. User profile settings showing the registered security keys.
2. Plugin settings page.

== Changelog ==

= 1.0 =
* First public release.
