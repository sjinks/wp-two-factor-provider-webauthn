<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

use WildWolf\Utils\Singleton;
use wpdb;

final class Schema {
	use Singleton;

	public const VERSION_KEY    = Constants::SCHEMA_VERSION_KEY;
	public const LATEST_VERSION = 3;

	/**
	 * @global wpdb $wpdb
	 */
	private function __construct() {
		/** @var wpdb $wpdb */
		global $wpdb;

		$wpdb->webauthn_credentials = $wpdb->base_prefix . Constants::WA_CREDENTIALS_TABLE_NAME;
		$wpdb->webauthn_users       = $wpdb->base_prefix . Constants::WA_USERS_TABLE_NAME;
	}

	public function is_installed(): bool {
		$current_version = (int) get_site_option( self::VERSION_KEY, 0 );
		return $current_version > 0;
	}

	public function is_update_needed(): bool {
		$current_version = (int) get_site_option( self::VERSION_KEY, 0 );
		return $current_version < self::LATEST_VERSION;
	}

	public function update(): void {
		set_time_limit( -1 );
		ignore_user_abort( true );

		$current_version = (int) get_site_option( self::VERSION_KEY, 0 );
		$success         = true;

		if ( self::LATEST_VERSION !== $current_version ) {
			$success = $this->update_schema();
		}

		if ( $success ) {
			update_site_option( self::VERSION_KEY, self::LATEST_VERSION );
		}
	}

	/**
	 * @global wpdb $wpdb
	 */
	private function update_schema(): bool {
		/** @var wpdb $wpdb */
		global $wpdb;

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';
		}

		$charset_collate = $wpdb->get_charset_collate();

		$sql = [
			"CREATE TABLE {$wpdb->webauthn_users} (
				user_id bigint(20) unsigned NOT NULL,
				user_handle varchar(128) NOT NULL,
				PRIMARY KEY  (user_id),
				UNIQUE KEY user_handle (user_handle)
			) {$charset_collate};",
			"CREATE TABLE {$wpdb->webauthn_credentials} (
				id bigint(20) unsigned NOT NULL auto_increment,
				user_handle varchar(128) NOT NULL,
				credential_id varchar(767) CHARSET ascii COLLATE ascii_bin NOT NULL,
				public_key varchar(1024) NOT NULL,
				counter int(11) NOT NULL,
				name varchar(255) NOT NULL,
				added int(11) NOT NULL,
				last_used int(11) NOT NULL,
				u2f tinyint(2) NOT NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY credential_id (credential_id),
				KEY user_handle (user_handle)
			) {$charset_collate};",
		];

		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.dbDelta_dbdelta
		dbDelta( $sql );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$table_count = count( $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->webauthn_credentials ) ) ) ) + count( $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->webauthn_users ) ) ) );
		return 2 === $table_count;
	}
}
