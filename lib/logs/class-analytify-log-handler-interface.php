<?php
/**
 * Log Handler Interface
 *
 * @version 1.0.0
 * @package Analytify/Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Analytify Log Handler Interface
 *
 * Functions that must be defined to correctly fulfill log handler API.
 *
 * @version 1.0.0
 */
interface ANALYTIFY_Log_Handler_Interface {

	/**
	 * Handle a log entry.
	 *
	 * @param int    $timestamp Log timestamp.
	 * @param string $level emergency|alert|critical|error|warning|notice|info|debug.
	 * @param string $message Log message.
	 * @param array  $context Additional information for log handlers.
	 *
	 * @return bool False if value was not handled and true if value was handled.
	 */
	public function handle( $timestamp, $level, $message, $context );
}
