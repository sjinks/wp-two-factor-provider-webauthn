<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Credential\UserHandle,
	MadWizard\WebAuthn\Crypto\CoseAlgorithm,
	MadWizard\WebAuthn\Crypto\CoseKeyInterface,
	MadWizard\WebAuthn\Crypto\Ec2Key,
	MadWizard\WebAuthn\Exception\ParseException,
	MadWizard\WebAuthn\Format\ByteBuffer,
};
use WP_User;
use wpdb;

/**
 * @psalm-type CredentialLegacyMetaValue = array{keyHandle: string, publicKey: string, certificate: string, counter: int, name: string, added: int, last_used: int}
 * @psalm-import-type CredentialRowArray from WebAuthn_Credential_Store
 */
abstract class Credential_Migrator {
	/**
	 * @global wpdb $wpdb
	 * @throws ParseException
	 */
	public static function migrate( WP_User $user, UserHandle $handle ): void {
		/** @var wpdb $wpdb */
		global $wpdb;

		/** @var mixed[] */
		$legacy = get_user_meta( $user->ID, WebAuthn_Credential_Store::REGISTERED_KEY_LEGACY_META );

		/** @var mixed $data */
		foreach ( $legacy as $data ) {
			if ( self::is_valid_legacy_credential( $data ) ) {
				/** @psalm-var CredentialRowArray */
				$credential = [
					'user_handle'   => $handle->toString(),
					'credential_id' => $data['keyHandle'],
					'public_key'    => self::coseize_u2f_pubkey( $data['publicKey'] )->toString(),
					'counter'       => $data['counter'],
					'name'          => $data['name'],
					'added'         => $data['added'],
					'last_used'     => $data['last_used'],
					'u2f'           => 1,
				];

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->insert( $wpdb->webauthn_credentials, $credential, [ '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%d' ] );
				if ( defined( 'WEBAUTHN_DELETE_U2F_KEYS_ON_MIGRATION' ) && constant( 'WEBAUTHN_DELETE_U2F_KEYS_ON_MIGRATION' ) ) {
					delete_user_meta( $user->ID, WebAuthn_Credential_Store::REGISTERED_KEY_LEGACY_META, $data );
				}
			}
		}
	}

	/**
	 * @param mixed $item
	 * @psalm-assert-if-true CredentialLegacyMetaValue $item
	 */
	private static function is_valid_legacy_credential( $item ): bool {
		return is_array( $item )
			&& ! empty( $item['keyHandle'] ) && is_string( $item['keyHandle'] )
			&& ! empty( $item['publicKey'] ) && is_string( $item['publicKey'] )
			&& ! empty( $item['certificate'] ) && is_string( $item['certificate'] )
			&& ! empty( $item['counter'] ) && $item['counter'] >= -1
			&& ! empty( $item['name'] ) && is_string( $item['name'] )
			&& ! empty( $item['added'] ) && is_int( $item['added'] )
			&& ! empty( $item['last_used'] ) && is_int( $item['last_used'] );
	}

	public static function coseize_u2f_pubkey( string $key ): CoseKeyInterface {
		$binary = ByteBuffer::fromBase64Url( $key )->getBinaryString();
		$x      = substr( $binary, 1, 32 );
		$y      = substr( $binary, 33, 32 );

		return new Ec2Key( new ByteBuffer( $x ), new ByteBuffer( $y ), Ec2Key::CURVE_P256, CoseAlgorithm::ES256 );
	}
}
