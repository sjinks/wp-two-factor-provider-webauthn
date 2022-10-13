# wp-two-factor-provider-webauthn

[![Build and Test](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/ci.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/ci.yml)
[![Code Standards Compliance Checks](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/lint.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/lint.yml)
[![Static Code Analysis](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/static-code-analysis.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/static-code-analysis.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sjinks_wp-two-factor-provider-webauthn&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=sjinks_wp-two-factor-provider-webauthn)
[![CodeQL Analysis](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/codeql-analysis.yml/badge.svg)](https://github.com/sjinks/wp-two-factor-provider-webauthn/actions/workflows/codeql-analysis.yml)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/donate/?hosted_button_id=SAG6877JDJ3KU)

WebAuthn Provider for [Two Factor](https://github.com/WordPress/two-factor) plugin

Google Chrome is going to [deprecate the U2F API](https://groups.google.com/a/chromium.org/g/blink-dev/c/xHC3AtU_65A/m/yg20tsVFBAAJ). As a consequence, the U2F provider of the Two Factor plugin will no longer work. This plugin uses the WebAuthn Authentication API compatible with U2F and provides a replacement for the U2F provider.

The integration is seamless: if the user has U2F credentials registered, the plugin will import them. If the user has the U2F provider enabled, the plugin will automatically enable the WebAuthn provider as well. If the U2F provider is set as the primary authentication method, it will be replaced with WebAuthn, keeping U2F as a backup method.

https://user-images.githubusercontent.com/7810770/150708968-3c331612-54ad-4373-9d36-6ec064301755.mp4
