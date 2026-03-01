<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WildWolf\Utils\Singleton;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Dom\AuthenticatorAttachment,
	MadWizard\WebAuthn\Dom\UserVerificationRequirement,
};

final class AdminSettings {
	use Singleton;

	const OPTION_GROUP = '2fa_webauthn_settings';

	/** @var InputFactory */
	private $input_factory;

	/**
	 * Constructed during `admin_init`
	 *
	 * @codeCoverageIgnore
	 */
	private function __construct() {
		$this->register_settings();
	}

	public function register_settings(): void {
		$this->input_factory = new InputFactory( Settings::OPTIONS_KEY, Settings::instance() );
		register_setting(
			self::OPTION_GROUP,
			Settings::OPTIONS_KEY,
			[
				'default'           => [],
				'sanitize_callback' => [ SettingsValidator::class, 'sanitize' ],
			]
		);

		$settings_section = 'general-settings';
		add_settings_section(
			$settings_section,
			'',
			'__return_empty_string',
			Admin::OPTIONS_MENU_SLUG
		);

		add_settings_field(
			'authenticator_attachment',
			__( 'Authenticator Attachment Modality', 'two-factor-provider-webauthn' ),
			[ $this->input_factory, 'select' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'authenticator_attachment',
				'options'   => [
					''                                => _x( 'No preference', 'Authenticator attachment modality', 'two-factor-provider-webauthn' ),
					AuthenticatorAttachment::CROSS_PLATFORM => _x( 'Cross-platform', 'Authenticator attachment modality', 'two-factor-provider-webauthn' ),
					AuthenticatorAttachment::PLATFORM => _x( 'Platform', 'Authenticator attachment modality', 'two-factor-provider-webauthn' ),
				],
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					"<em>Platform (built-in authenticator)</em>: Use the device's built-in authenticators, such as Touch ID, Face ID, Windows Hello, or the phone’s fingerprint sensor. These are usually the easiest and most convenient option.<br/>"
					. '<em>Cross-platform (security keys)</em>: Use removable security keys, such as YubiKey or similar USB/NFC keys. These can be used on multiple devices.<br/>'
					. '<em>No preference (recommended)</em>: Allow both built-in authenticators and security keys. This gives users the most flexibility and is recommended in most cases.<br/>'
					. '<a href="https://www.w3.org/TR/webauthn-2/#authenticator-attachment-modality">Details</a>',
					'two-factor-provider-webauthn'
				),
			]
		);

		add_settings_field(
			'user_verification_requirement',
			__( 'User Verification Requirement', 'two-factor-provider-webauthn' ),
			[ $this->input_factory, 'select' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'user_verification_requirement',
				'options'   => [
					UserVerificationRequirement::DISCOURAGED => _x( 'Discouraged', 'User Verification Requirement', 'two-factor-provider-webauthn' ),
					UserVerificationRequirement::PREFERRED => _x( 'Preferred', 'User Verification Requirement', 'two-factor-provider-webauthn' ),
					UserVerificationRequirement::REQUIRED  => _x( 'Required', 'User Verification Requirement', 'two-factor-provider-webauthn' ),
				],
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'<em>Discouraged</em>: User verification is not required. Users may be able to authenticate without biometrics or a PIN. This is the least strict option.<br/>'
					. '<em>Preferred</em>: User verification, such as biometrics (fingerprint, Face ID) or a device PIN, is preferred when available, but not strictly required. This provides good security while maintaining compatibility.<br/>'
					. '<em>Required</em>: User verification is always required. Users must confirm their identity using biometrics or a PIN. Some older devices or security keys may not support this.<br/>',
					'two-factor-provider-webauthn'
				),
			]
		);

		add_settings_field(
			'resident_key_requirement',
			__( 'Passkeys / Resident Key Requirement', 'two-factor-provider-webauthn' ),
			[ $this->input_factory, 'select' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'resident_key_requirement',
				'options'   => [
					'discouraged' => _x( 'Discouraged', 'Resident Key Requirement', 'two-factor-provider-webauthn' ),
					'preferred'   => _x( 'Preferred', 'Resident Key Requirement', 'two-factor-provider-webauthn' ),
					'required'    => _x( 'Required', 'Resident Key Requirement', 'two-factor-provider-webauthn' ),
				],
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'<em>Discouraged</em>: Passkeys are not specifically requested. Authentication will still work normally, but passkey features like automatic sign-in may be less likely to be available.<br/>'
					. '<em>Preferred</em>: Passkeys are used when supported by the device. This allows users to sign in more easily and enables modern passkey features.<br/>'
					. '<em>Required</em>: Only passkeys are allowed. Devices or security keys that do not support passkeys will not work.<br/>',
					'two-factor-provider-webauthn'
				),
			],
		);

		add_settings_field(
			'timeout',
			__( 'Timeout', 'two-factor-provider-webauthn' ),
			[ $this->input_factory, 'input' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'timeout',
				'type'      => 'number',
				'help'      => __(
					'The default timeout for security key operations, in seconds. Set to 0 to use the browser default value.',
					'two-factor-provider-webauthn'
				),
			]
		);

		add_settings_field(
			'u2f_hack',
			__( 'U2F compatibility hack', 'two-factor-provider-webauthn' ),
			[ $this->input_factory, 'checkbox' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'u2f_hack',
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'Chrome for Android sometimes ignores the AppID extension required for interoperability between the old U2F and the modern WebAuthn protocol.<br/>'
					. 'When enabled, this hack checks whether the security key used was registered with U2F and, if so, forces the use of the AppID extension.',
					'two-factor-provider-webauthn'
				),
			]
		);

		add_settings_field(
			'disable_u2f',
			__( 'Disable old U2F provider', 'two-factor-provider-webauthn' ),
			[ $this->input_factory, 'checkbox' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'disable_u2f',
				'help'      => __( 'This option allows you to turn off the old U2F provider in the Two Factor plugin.', 'two-factor-provider-webauthn' ),
			]
		);
	}
}
