<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use RuntimeException;
use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Server\Authentication\AuthenticationContext,
	MadWizard\WebAuthn\Server\Registration\RegistrationContext,
};

final class Context_Store {
	public const AUTHENTICATION = 'authentication';
	public const REGISTRATION   = 'registration';
	private const LIFETIME      = 600;
	private const VERSION       = 1;
	private const MAC_LENGTH    = 64;

	public static function save( int $user_id, AuthenticationContext|RegistrationContext $context ): void {
		$ceremony = $context instanceof AuthenticationContext ? self::AUTHENTICATION : self::REGISTRATION;
		$now      = time();

		// Preserve the library's object graph, including extension inputs, without reconstructing its internals.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- authenticated before deserialization in take().
		$serialized = serialize( $context );

		$payload = self::VERSION . ':' . ( $now + self::LIFETIME ) . ':' . base64_encode( $serialized );

		// A fixed-length hex MAC prefixes version:expiry:base64, keeping storage ASCII-safe.
		$envelope = self::sign( $payload, $user_id, $ceremony ) . $payload;

		if ( ! set_transient( self::key( $user_id, $ceremony ), $envelope, self::LIFETIME ) ) {
			throw new RuntimeException( 'Unable to save the WebAuthn context.' );
		}
	}

	/**
	 * Consume before validation, including failed attempts. Transient get/delete is not atomic.
	 *
	 * @psalm-param self::AUTHENTICATION|self::REGISTRATION $ceremony
	 */
	public static function take( int $user_id, string $ceremony ): AuthenticationContext|RegistrationContext|null {
		$key     = self::key( $user_id, $ceremony );
		$value   = get_transient( $key );
		$deleted = delete_transient( $key );
		if ( ! is_string( $value ) || ! $deleted || strlen( $value ) <= self::MAC_LENGTH ) {
			return null;
		}

		$mac     = substr( $value, 0, self::MAC_LENGTH );
		$payload = substr( $value, self::MAC_LENGTH );
		if ( ! hash_equals( self::sign( $payload, $user_id, $ceremony ), $mac ) ) {
			return null;
		}

		$fields = explode( ':', $payload, 3 );
		if ( 3 !== count( $fields ) || (string) self::VERSION !== $fields[0] ) {
			return null;
		}

		$expires_at = (int) $fields[1];
		$now        = time();
		// Require a canonical decimal integer, rejecting malformed values and integer overflow.
		if ( (string) $expires_at !== $fields[1] || $expires_at > $now + self::LIFETIME || $expires_at <= $now ) {
			return null;
		}

		$serialized = base64_decode( $fields[2], true );
		if ( false === $serialized || '' === $serialized ) {
			return null;
		}

		// Only authenticated, unexpired, site/user/ceremony-bound bytes may reach the library's deserializer.
		/** @var mixed $context */
		$context = unserialize( $serialized ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- verified HMAC above.
		if ( self::AUTHENTICATION === $ceremony ) {
			return $context instanceof AuthenticationContext ? $context : null;
		}

		return $context instanceof RegistrationContext ? $context : null;
	}

	private static function key( int $user_id, string $ceremony ): string {
		return 'webauthn_context_' . $ceremony . '_' . $user_id;
	}

	private static function sign( string $payload, int $user_id, string $ceremony ): string {
		// Bind the expected site, user, and ceremony through key derivation instead of storing them.
		$scope = 'two-factor-provider-webauthn/context/v1:' . get_current_blog_id() . ':' . $user_id . ':' . $ceremony;
		$key   = hash_hmac( 'sha256', $scope, wp_salt( 'auth' ), true );
		return hash_hmac( 'sha256', $payload, $key );
	}
}
