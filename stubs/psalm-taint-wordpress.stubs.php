<?php

namespace {
	/**
	 * @param mixed $text
	 * @return string
	 * @psalm-taint-escape html
	 */
	function sanitize_text_field( $text ) {}

	/**
	 * @param string $text
	 * @return string
	 * @psalm-taint-escape html
	 */
	function esc_html( $text ) {}

	/**
	 * @param string $text
	 * @return string
	 * @psalm-taint-escape html
	 */
	function esc_attr( $text ) {}

	/**
	 * @param string $data
	 * @return string
	 * @psalm-taint-escape html
	 */
	function wp_kses_post( $data ) {}

	class wpdb {
		/**
		 * @param string $query
		 * @param mixed ...$args
		 * @return string|null
		 * @psalm-taint-escape sql
		 */
		public function prepare( $query, ...$args ) {}

		/**
		 * @param string $query
		 * @return int|false
		 * @psalm-taint-sink sql $query
		 */
		public function query( $query ) {}
	}
}
