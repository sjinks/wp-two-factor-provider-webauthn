<?php

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\PHP;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log

$base_dir = __DIR__ . '/../plugins/two-factor-provider-webauthn';
if ( getenv( 'COLLECT_COVERAGE' ) === '1' && PHP_SAPI !== 'cli' && is_dir( $base_dir ) && is_dir( $base_dir . '/coverage-report' ) ) {
	/** @psalm-suppress UnresolvableInclude */
	require_once $base_dir . '/vendor/autoload.php';

	$filter = new Filter();
	$filter->includeDirectory( $base_dir );
	$filter->excludeDirectory( $base_dir . '/node_modules' );
	$filter->excludeDirectory( $base_dir . '/stubs' );
	$filter->excludeDirectory( $base_dir . '/vendor' );

	$selector = new Selector();
	$dt       = new DateTime();
	try {
		$coverage = new CodeCoverage( $selector->forLineCoverage( $filter ), $filter );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$coverage->start( sprintf( '%s %s - %s', ( $_SERVER['REQUEST_METHOD'] ?? '-' ), ( $_SERVER['REQUEST_URI'] ?? '-' ), $dt->format( 'YmdHisu' ) ) );

		register_shutdown_function( function () use ( $coverage, $base_dir ) {
			$coverage->stop();
			try {
				$php = new PHP();
				$dt  = new DateTime();
				$php->process( $coverage, sprintf( '%s/coverage-report/%s.cov', $base_dir, $dt->format( 'YmdHisu' ) ) );
			} catch ( Throwable $e ) {
				error_log( (string) $e );
			}
		} );
	} catch ( Throwable $e ) {
		error_log( (string) $e );
	}
}
