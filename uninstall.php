<?php

// phpcs:disable WordPress.DB.DirectDatabaseQuery

use WildWolf\WordPress\TwoFactorWebAuthn\AJAX;
use WildWolf\WordPress\TwoFactorWebAuthn\Schema;
use WildWolf\WordPress\TwoFactorWebAuthn\Settings;
use WildWolf\WordPress\TwoFactorWebAuthn\WebAuthn_Provider;

/**
 * @global wpdb $wpdb
 * @var wpdb $wpdb
 * @psalm-suppress InvalidGlobal
 */
global $wpdb;

if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	require __DIR__ . '/vendor/autoload.php';
	Schema::instance();
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->webauthn_credentials}" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->webauthn_users}" );
	delete_option( Schema::VERSION_KEY );
	delete_option( Settings::OPTIONS_KEY );
	delete_metadata( 'user', 0, AJAX::REGISTRATION_CONTEXT_USER_META, null, true );
	delete_metadata( 'user', 0, WebAuthn_Provider::AUTHENTICATION_CONTEXT_USER_META, null, true );
}
