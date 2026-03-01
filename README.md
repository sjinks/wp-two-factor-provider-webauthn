# WebAuthn Provider for Two Factor Plugin

[![Build and Test](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/ci.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/ci.yml)
[![Code Standards Compliance Checks](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/lint.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/lint.yml)
[![Static Code Analysis](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/static-code-analysis.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/static-code-analysis.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sjinks_wp-two-factor-provider-webauthn&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=sjinks_wp-two-factor-provider-webauthn)
[![CodeQL Analysis](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/codeql-analysis.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/codeql-analysis.yml)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/donate/?hosted_button_id=SAG6877JDJ3KU)

This plugin adds WebAuthn and passkey support to the [Two Factor](https://wordpress.org/plugins/two-factor/) plugin, providing a modern, secure authentication method.

**Features:**

* Support for WebAuthn and passkeys (Windows Hello, Touch ID, YubiKeys, etc.)
* Backward compatibility with previously registered U2F security keys
* User-friendly settings and seamless authentication experience
* Customizable error logging and behavior via action hooks
* Works with the Two Factor plugin for flexible 2FA authentication

The plugin enables users to register and use hardware security keys and platform authenticators for stronger protection against password-based attacks and phishing.

Google Chrome [deprecated the U2F API](https://groups.google.com/a/chromium.org/g/blink-dev/c/xHC3AtU_65A/m/yg20tsVFBAAJ). As a consequence, the U2F provider of the Two Factor plugin no longer works. This plugin uses the WebAuthn Authentication API compatible with U2F and provides a replacement for the U2F provider.

The integration is seamless: if the user has U2F credentials registered, the plugin will import them. If the user has the U2F provider enabled, the plugin will automatically enable the WebAuthn provider as well. If the U2F provider is set as the primary authentication method, it will be replaced with WebAuthn, keeping U2F as a backup method.

https://user-images.githubusercontent.com/7810770/150708968-3c331612-54ad-4373-9d36-6ec064301755.mp4

See [readme.txt](readme.txt) for the changelog.
