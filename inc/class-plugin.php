<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use Two_Factor_FIDO_U2F;
use TwoFactor_Provider_WebAuthn;
use WildWolf\Utils\Singleton;

final class Plugin {
	use Singleton;

	private function __construct() {
		$this->set_up_hooks();
	}

	public function set_up_hooks(): void {
		$basename = plugin_basename( dirname( __DIR__ ) . '/plugin.php' );
		add_action( 'activate_' . $basename, [ $this, 'maybe_update_schema' ] );
		add_action( 'plugins_loaded', [ $this, 'maybe_update_schema' ] );
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(): void {
		load_plugin_textdomain( 'two-factor-provider-webauthn', false, plugin_basename( dirname( __DIR__ ) ) . '/lang/' );
		add_filter( 'two_factor_providers', [ $this, 'two_factor_providers' ] );

		if ( is_admin() ) {
			Admin::instance();
		}
	}

	/**
	 * @psalm-param array<class-string,string> $providers
	 * @psalm-return array<class-string,string>
	 * @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement
	 */
	public function two_factor_providers( array $providers ): array {
		$providers[ TwoFactor_Provider_WebAuthn::class ] = __DIR__ . '/class-twofactor-provider-webauthn.php';

		$disable_u2f = Settings::instance()->get_disable_u2f();
		if ( $disable_u2f ) {
			unset( $providers[ Two_Factor_FIDO_U2F::class ] );
		}

		return $providers;
	}

	public function maybe_update_schema(): void {
		$schema = Schema::instance();
		if ( $schema->is_update_needed() ) {
			$schema->update();
		}
	}
}
