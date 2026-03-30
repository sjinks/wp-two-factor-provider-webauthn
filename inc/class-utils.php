<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\{
	MadWizard\WebAuthn\Builder\ServerBuilder,
	MadWizard\WebAuthn\Config\RelyingParty,
	MadWizard\WebAuthn\Server\ServerInterface,
};

abstract class Utils {
	public static function get_u2f_app_id(): string {
		/** @psalm-var array{host: string, port?: positive-int} */
		$url_parts = wp_parse_url( home_url() );

		if ( ! empty( $url_parts['port'] ) ) {
			$url = sprintf( 'https://%s:%d', $url_parts['host'], $url_parts['port'] );
		} else {
			$url = sprintf( 'https://%s', $url_parts['host'] );
		}

		return (string) apply_filters( 'webauthn_app_id', $url );
	}

	/**
	 * @psalm-param literal-string $view
	 * @psalm-param array<string,mixed> $params
	 * @psalm-suppress PossiblyUnusedParam
	 * @psalm-taint-sink include $view
	 */
	public static function render( string $view, array $params = [] ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		/** @psalm-suppress UnresolvableInclude */
		require __DIR__ . '/../views/' . $view . '.php'; // NOSONAR
	}

	public static function create_webauthn_server(): ServerInterface {
		$builder = new ServerBuilder();
		$party   = new RelyingParty( get_bloginfo( 'name' ), self::get_u2f_app_id() );

		/** @var mixed */
		$cookie_domain = defined( 'COOKIE_DOMAIN' ) ? constant( 'COOKIE_DOMAIN' ) : null;
		if ( is_string( $cookie_domain ) && '' !== $cookie_domain ) {
			$id = ltrim( $cookie_domain, '.' );
			$party->setId( $id );
		}

		$builder->setRelyingParty( $party );
		$builder->setCredentialStore( new WebAuthn_Credential_Store() );
		$builder->enableExtensions( 'appid' );

		do_action( 'tfa_webauthn_init_server', $builder );

		return $builder->build();
	}

	/**
	 * @psalm-taint-escape html
	 */
	public static function get_post_field_as_string( string $field ): string {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ $field ] ) && is_scalar( $_POST[ $field ] ) ) {
			return sanitize_text_field( wp_unslash( (string) $_POST[ $field ] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return '';
	}
}
