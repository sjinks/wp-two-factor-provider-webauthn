<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn;

abstract class DateTimeUtils {
	public static function format_date_time( int $dt ) : string {
		$date_format = (string) get_option( 'date_format', 'Y-m-d' );
		$time_format = (string) get_option( 'time_format', 'H:i:s' );
		return date_i18n( $date_format . ' ' . $time_format, $dt, true );
	}

	public static function format_date_time_full( int $dt ) : string {
		return gmdate( 'Y-m-d H:i:s', $dt );
	}
}
