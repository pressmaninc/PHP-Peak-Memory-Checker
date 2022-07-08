<?php
/*
Plugin Name: PHP Peak Memory Checker
Plugin URI:
Description: This plugin checks PHP memory usage and sends an email to the administrator if the maximum memory usage exceeds the threshold.
Version: 1.1.0
Author: PRESSMAN
Author URI: https://www.pressman.ne.jp/
Text Domain: php-peak-memory-checker
License: GNU GPL v2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * PHP_Peak_Memory_Checker
 */
class PHP_Peak_Memory_Checker {

	private static $instance;

	private const DEFAULT_MEMORY_THRESHOLD = 32;

	private $keys_to_ignore = array( 'pass1', 'pass2', 'pwd', 'password' );

	/**
	 * Make it singleton.
	 *
	 * @return obj
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		register_shutdown_function( array( $this, 'wp10_php_memory_log' ) );
	}

	/**
	 * Main function.
	 *
	 * @return void
	 */
	public function wp10_php_memory_log() {
		$threshold = self::DEFAULT_MEMORY_THRESHOLD;
		if ( defined( 'WP10_MEMORY_ALERT_THRESHOLD' ) ) {
			$threshold = WP10_MEMORY_ALERT_THRESHOLD;
		}

		$current = memory_get_usage() / 1024 / 1024;
		$peak    = memory_get_peak_usage() / 1024 / 1024;

		// Do what you want.
		do_action( 'cpmp_peak_data', $peak );

		if ( $peak <= $threshold ) {
			return;
		}

		// Array Keys to ignore.
		$keys_to_ignore = apply_filters( 'cpmp_keys_to_ignore_list', $this->keys_to_ignore );

		// Sanitize.
		$server_sanitized  = $this->sanitize( $_SERVER, $keys_to_ignore );
		$request_sanitized = $this->sanitize( $_REQUEST, $keys_to_ignore );

		// Stringify output.
		$request_export = var_export(
			array(
				'SERVER'  => $server_sanitized,
				'REQUEST' => $request_sanitized,
			),
			true
		);
		$message = sprintf( 'Peak memory usage has exceeded ' . $threshold . "MB.\ncurrent: %6.1f MB peak: %6.1f MB \n\n server/request: %s", $current, $peak, $request_export );

		// If plugin "External_Notification2slack" is active, send a notification to slack. Otherwise, email it.
		if ( ! has_action( 'external_notification2slack' ) ) {
			$to      = get_option( 'admin_email' );
			$subject = '[PHP Peak Memory Checker] PHP memory has reached a max threshold.';
			wp_mail( $to, $subject, $message );
		} else {
			do_action( 'external_notification2slack', $message );
		}
	}

	/**
	 * Sanitize.
	 *
	 * @param  array  $array
	 * @param  array  $keys_to_ignore
	 * @return array  $sanitized
	 */
	public function sanitize( $array, $keys_to_ignore = array() ) {
		$sanitized    = array();

		foreach ( $array as $key => $value ) {
			if ( in_array( $key, $keys_to_ignore, true ) ) {
				unset( $array[ $key ] );
				continue;
			}

			if ( is_array( $value ) ) {
				$sanitized_value = $this->sanitize( $value );
			} else {
				$sanitized_value = wp_kses( $value, array() );
			}
			$sanitized_key = wp_kses( $key, array() );
			$sanitized[ $sanitized_key ] = $sanitized_value;
		}
		return $sanitized;
	}

}

PHP_Peak_Memory_Checker::get_instance();
