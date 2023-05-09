<?php
/*
 * Plugin Name: WebAuthn Provider for Two Factor
 * Description: WebAuthn Provider for Two Factor plugin.
 * Version: 2.1.0
 * Author: Volodymyr Kolesnykov
 * License: MIT
 * Text Domain: two-factor-provider-webauthn
 * Domain Path: /lang
 * Network: true
 */

use Composer\Autoload\ClassLoader;
use WildWolf\WordPress\TwoFactorWebAuthn\Plugin;

if ( defined( 'ABSPATH' ) ) {
	/** @var ClassLoader */
	$loader = require_once __DIR__ . '/vendor/autoload.php';
	$loader->addClassMap( [
		WP_List_Table::class => ABSPATH . 'wp-admin/includes/class-wp-list-table.php',
	] );

	if ( ! defined( 'WP_INSTALLING' ) || ! WP_INSTALLING ) {
		Plugin::instance();
	}
}
