<?php

namespace {
	define( 'ABSPATH', 'vendor/johnpbloch/wordpress-core' );

	/**
	 * Adds slashes to a string or recursively adds slashes to strings within an array.
	 *
	 * This should be used when preparing data for core API that expects slashed data.
	 * This should not be used to escape data going directly into an SQL query.
	 *
	 * @since 3.6.0
	 * @since 5.5.0 Non-string values are left untouched.
	 *
	 * @psalm-template T of string|array
	 * @psalm-param T $value String or array of data to slash.
	 * @psalm-return T Slashed $value.
	 */
	function wp_slash( $value ) {}

	/**
	 * Removes slashes from a string or recursively removes slashes from strings within an array.
	 *
	 * This should be used to remove slashes from data passed to core API that
	 * expects data to be unslashed.
	 *
	 * @since 3.6.0
	 *
	 * @psalm-template T of string|array
	 * @param T $value String or array of data to unslash.
	 * @return T Unslashed $value.
	 */
	function wp_unslash( $value ) {}

	/**
	 * @param string $hook_name The name of the filter hook.
	 * @param mixed  $value     The value to filter.
	 * @param mixed  ...$args   Additional parameters to pass to the callback functions.
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	function apply_filters( $hook_name, $value, ...$args ) {}

	define( 'OBJECT', 'OBJECT' );
	define( 'object', 'OBJECT' );
	define( 'OBJECT_K', 'OBJECT_K' );
	define( 'ARRAY_A', 'ARRAY_A' );
	define( 'ARRAY_N', 'ARRAY_N' );

	class wpdb {

		/** @var string */
		public $webauthn_credentials = '';
		/** @var string */
		public $webauthn_users = '';

		/**
		 * @param string $query
		 * @param mixed $args
		 * @return string|null
		 */
		public function prepare( $query, ...$args ) {}
	}

	/*
	 * @param mixed $data        Optional. Data to encode as JSON, then print and die. Default null.
	 * @param int   $status_code Optional. The HTTP status code to output. Default null.
	 * @param int   $options     Optional. Options to be passed to json_encode(). Default 0.
	 * @psalm-return never-return
	 */
	function wp_send_json_error( $data = null, $status_code = null, $options = 0 ) {}

	/**
	 * @param mixed $response    Variable (usually an array or object) to encode as JSON,
	 *                           then print and die.
	 * @param int   $status_code Optional. The HTTP status code to output. Default null.
	 * @param int   $options     Optional. Options to be passed to json_encode(). Default 0.
	 * @psalm-return never-return
	 */
	function wp_send_json( $response, $status_code = null, $options = 0 ) {}
}
