<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use MadWizard\WebAuthn\Dom\AuthenticatorAttachment;
use MadWizard\WebAuthn\Dom\UserVerificationRequirement;
use WildWolf\Utils\Singleton;

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
			__( 'Authenticator Attachment Modality', '2fa-wa' ),
			[ $this->input_factory, 'select' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'authenticator_attachment',
				'options'   => [
					''                                => _x( 'None', 'Authenticator attachment modality', '2fa-wa' ),
					AuthenticatorAttachment::CROSS_PLATFORM => _x( 'Cross-platform', 'Authenticator attachment modality', '2fa-wa' ),
					AuthenticatorAttachment::PLATFORM => _x( 'Platform', 'Authenticator attachment modality', '2fa-wa' ),
				],
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'<em>Platform attachment</em> is for authenticators physically bound to a client device (like a fingerprint scanner on a smartphone).<br/>'
					. '<em>Cross-platform attachment</em> is for removable authenticators which can "roam" between client devices (like a security key).<br/>'
					. 'Consider using <em>None</em> if you do not need to restrict your users to the specific class of authenticators.<br/>'
					. '<a href="https://www.w3.org/TR/webauthn-2/#authenticator-attachment-modality">Details</a>',
					'2fa-wa'
				),
			]
		);

		add_settings_field(
			'user_verification_requirement',
			__( 'User Verification Requirement', '2fa-wa' ),
			[ $this->input_factory, 'select' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'user_verification_requirement',
				'options'   => [
					UserVerificationRequirement::DISCOURAGED => _x( 'Discouraged', 'User Verification Requirement', '2fa-wa' ),
					UserVerificationRequirement::PREFERRED => _x( 'Preferred', 'User Verification Requirement', '2fa-wa' ),
					UserVerificationRequirement::REQUIRED  => _x( 'Required', 'User Verification Requirement', '2fa-wa' ),
				],
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'<em>Discouraged</em>: user verificatoin is not required (e.g., in the interest of minimizing disruption to the user interaction flow).<br/>'
					. '<em>Preferred</em>: user verification (like entering a PIN code) is preferred but not required for successful authentication.<br/>'
					. '<em>Required</em>: user verification is required for successful authentication. Please note that not all browsers support this setting.<br/>',
					'2fa-wa'
				),
			]
		);

		add_settings_field(
			'timeout',
			__( 'Timeout', '2fa-wa' ),
			[ $this->input_factory, 'input' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'timeout',
				'type'      => 'number',
				'help'      => __(
					'The default timeout for security key operations, in seconds. Set to 0 to use the browser default value.',
					'2fa-wa'
				),
			]
		);

		add_settings_field(
			'u2f_hack',
			__( 'U2F compatibility hack', '2fa-wa' ),
			[ $this->input_factory, 'checkbox' ],
			Admin::OPTIONS_MENU_SLUG,
			$settings_section,
			[
				'label_for' => 'u2f_hack',
				'help'      => __(
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'Chrome for Android sometimes ignores the AppID extension required for interoperability between the old U2F and the modern WebAuthn protocol.<br/>'
					. 'When enabled, this compatibility enables the check whther the security key used was registered with U2F and if so, forces the use of the AppID extension.',
					'2fa-wa'
				),
			]
		);
	}
}
