<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WildWolf\Utils\Singleton;
use WP_User;

final class Admin {
	use Singleton;

	public const OPTIONS_MENU_SLUG = '2fa-webauthn';

	private function __construct() {
		$this->init();
	}

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ AdminSettings::class, 'instance' ] );
		add_action( 'show_user_security_settings', [ $this, 'show_user_security_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		if ( defined( 'DOING_AJAX' ) && constant( 'DOING_AJAX' ) ) {
			add_action( 'admin_init', [ AJAX::class, 'instance' ] );
		}
	}

	public function admin_menu(): void {
		add_options_page( __( 'TwoFactor WebAuthn Settings', 'two-factor-provider-webauthn' ), __( 'TwoFactor WebAuthn', 'two-factor-provider-webauthn' ), 'manage_options', self::OPTIONS_MENU_SLUG, [ __CLASS__, 'options_page' ] );
	}

	public static function options_page(): void {
		Utils::render( 'settings' );
	}

	/**
	 * @param string $hook
	 * @return void
	 * @global int $user_id
	 */
	public function admin_enqueue_scripts( $hook ): void {
		/** @var int $user_id */
		global $user_id;
		if ( in_array( $hook, array( 'user-edit.php', 'profile.php' ), true ) ) {
			wp_enqueue_script(
				'webauthn-register-key',
				plugins_url( 'assets/profile.min.js', __DIR__ ),
				[ 'wp-i18n' ],
				(string) filemtime( __DIR__ . '/../assets/profile.min.js' ),
				true
			);

			wp_localize_script( 'webauthn-register-key', 'tfa_webauthn', [
				'nonce' => wp_create_nonce( "webauthn-register_key_{$user_id}" ),
			] );

			wp_set_script_translations( 'webauthn-register-key', 'two-factor-provider-webauthn', plugin_dir_path( dirname( __DIR__ ) . '/index.php' ) . 'lang' );
		}
	}

	public function show_user_security_settings( WP_User $user ): void {
		Utils::render( 'user-profile', [
			'user' => $user,
		] );
	}
}
