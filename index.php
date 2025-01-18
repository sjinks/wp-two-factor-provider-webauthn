<?php
/*
 * Plugin Name: WebAuthn Provider for Two Factor
 * Description: WebAuthn Provider for Two Factor plugin.
 * Version: 2.5.3
 * Author: Volodymyr Kolesnykov
 * License: MIT
 * Text Domain: two-factor-provider-webauthn
 * Domain Path: /lang
 * Network: true
 */

use Composer\Autoload\ClassLoader;
use WildWolf\WordPress\TwoFactorWebAuthn\Plugin;

if ( defined( 'ABSPATH' ) ) {
	/** @var mixed */
	$save = $GLOBALS['__composer_autoload_files'] ?? null;

	/** @var ClassLoader */
	$loader = require __DIR__ . '/vendor/autoload.php'; // NOSONAR
	$loader->addClassMap( [
		WP_List_Table::class => ABSPATH . 'wp-admin/includes/class-wp-list-table.php',
	] );

	/** @psalm-suppress MixedAssignment */
	$GLOBALS['__composer_autoload_files'] = $save;

	if ( ! defined( 'WP_INSTALLING' ) || ! WP_INSTALLING ) {
		Plugin::instance();
	}
}
