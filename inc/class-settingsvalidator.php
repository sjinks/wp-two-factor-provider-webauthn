<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Dom\AuthenticatorAttachment,
	MadWizard\WebAuthn\Dom\UserVerificationRequirement,
};

/**
 * @psalm-import-type SettingsArray from Settings
 */
abstract class SettingsValidator {
	/**
	 * @psalm-param mixed[] $settings
	 * @psalm-return SettingsArray
	 */
	public static function ensure_data_shape( array $settings ): array {
		$defaults = Settings::defaults();
		$result   = $settings + $defaults;
		foreach ( $result as $key => $_value ) {
			if ( ! isset( $defaults[ $key ] ) ) {
				unset( $result[ $key ] );
			}
		}

		/** @var mixed $value */
		foreach ( $result as $key => $value ) {
			$my_type    = gettype( $value );
			$their_type = gettype( $defaults[ $key ] );
			if ( $my_type !== $their_type ) {
				settype( $result[ $key ], $their_type );
			}
		}

		/** @psalm-var SettingsArray */
		return $result;
	}

	/**
	 * @param mixed $settings
	 * @psalm-return SettingsArray $settings
	 */
	public static function sanitize( $settings ): array {
		if ( is_array( $settings ) ) {
			$settings = self::ensure_data_shape( $settings );

			$settings['timeout'] = filter_var( $settings['timeout'], FILTER_VALIDATE_INT, [
				'options' => [
					'default'   => 0,
					'min_range' => 0,
					'max_range' => PHP_INT_MAX / 1000,
				],
			] );

			if ( ! in_array( $settings['authenticator_attachment'], [ '', AuthenticatorAttachment::PLATFORM, AuthenticatorAttachment::CROSS_PLATFORM ], true ) ) {
				$settings['authenticator_attachment'] = '';
			}

			if ( ! in_array( $settings['user_verification_requirement'], [ UserVerificationRequirement::PREFERRED, UserVerificationRequirement::DISCOURAGED, UserVerificationRequirement::REQUIRED ], true ) ) {
				$settings['user_verification_requirement'] = UserVerificationRequirement::DEFAULT;
			}

			return $settings;
		}

		return Settings::defaults();
	}
}
