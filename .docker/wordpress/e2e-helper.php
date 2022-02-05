<?php

namespace WildWolf\WordPress\E2EHelper;

use WP_Error;

/**
 * @param mixed $preempt
 * @param array $_args
 * @param string $url
 * @return mixed
 */
function pre_http_request( $preempt, array $_args, string $url ) /* NOSONAR */ {
	if ( false !== strpos( $url, '://api.wordpress.org/' ) ) {
		$preempt = new WP_Error( 'Forbidden' );
	} else {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $url );
	}

	return $preempt;
}

if ( defined( '\\ABSPATH' ) ) {
	add_filter( 'pre_http_request', __NAMESPACE__ . '\\pre_http_request', 1, 3 );
}
