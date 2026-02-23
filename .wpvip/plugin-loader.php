<?php

defined( 'ABSPATH' ) || die();

if ( ! defined( 'WP_TESTS_DOMAIN' ) && function_exists( 'wpcom_vip_load_plugin' ) ) {
	wpcom_vip_load_plugin( 'two-factor-provider-webauthn/index.php' );
}
