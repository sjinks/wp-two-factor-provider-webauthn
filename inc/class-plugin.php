<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

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
		load_plugin_textdomain( '2fa-wa', false, plugin_basename( dirname( __DIR__ ) ) . '/lang/' );
		add_filter( 'two_factor_providers', [ $this, 'two_factor_providers' ] );

		if ( is_admin() ) {
			Admin::instance();
		}
	}

	/**
	 * @psalm-param array<class-string,string> $providers
	 * @psalm-return array<class-string,string>
	 */
	public function two_factor_providers( array $providers ): array {
		$providers[ TwoFactor_Provider_WebAuthn::class ] = __DIR__ . '/class-twofactor-provider-webauthn.php';
		return $providers;
	}

	public function maybe_update_schema(): void {
		$schema = Schema::instance();
		if ( $schema->is_update_needed() ) {
			$schema->update();
		}
	}
}
