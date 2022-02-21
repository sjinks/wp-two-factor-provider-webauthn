<?php

// phpcs:disable WordPress.DB.DirectDatabaseQuery

use WildWolf\WordPress\TwoFactorWebAuthn\Constants;

/**
 * @global wpdb $wpdb
 * @var wpdb $wpdb
 * @psalm-suppress InvalidGlobal
 */
global $wpdb;

if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	require_once __DIR__ . '/inc/class-constants.php';

	$wpdb->webauthn_credentials = $wpdb->prefix . Constants::WA_CREDENTIALS_TABLE_NAME;
	$wpdb->webauthn_users       = $wpdb->prefix . Constants::WA_USERS_TABLE_NAME;

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->webauthn_credentials}" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->webauthn_users}" );
	delete_option( Constants::SCHEMA_VERSION_KEY );
	delete_option( Constants::OPTIONS_KEY );
	delete_metadata( 'user', 0, Constants::REGISTRATION_CONTEXT_USER_META_KEY, null, true );
	delete_metadata( 'user', 0, Constants::AUTHENTICATION_CONTEXT_USER_META_KEY, null, true );
}
