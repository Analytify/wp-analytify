<?php
/**
 * Loads WPBrigade SDK core classes (single runtime).
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/config.php';

$incoming_sdk_version = defined( 'WPBRIGADE_SDK_VERSION' ) ? WPBRIGADE_SDK_VERSION : '3.2.0';

if ( ! defined( 'WP_WPBRIGADE_SDK_VERSION' ) ) {
	define( 'WP_WPBRIGADE_SDK_VERSION', $incoming_sdk_version );
}

$wpb_sdk_runtime_includes = __DIR__ . '/includes';

// Always merge helpers from this bundle (function_exists guards).
require_once $wpb_sdk_runtime_includes . '/wpb-sdk-essential-functions.php';

if ( ! class_exists( 'WPBRIGADE_Optin_Verification', false ) ) {
	require_once $wpb_sdk_runtime_includes . '/class-wpb-sdk-optin-verification.php';
}

if ( ! class_exists( 'WPBRIGADE_Opt_Manager', false ) ) {
	require_once $wpb_sdk_runtime_includes . '/class-wpb-opt-manager.php';
}

if ( class_exists( 'WPBRIGADE_Logger', false ) ) {
	$loaded_sdk_version = defined( 'WP_WPBRIGADE_SDK_VERSION' ) ? WP_WPBRIGADE_SDK_VERSION : '0';
	if (
		version_compare( $loaded_sdk_version, $incoming_sdk_version, '<' )
		|| version_compare( $loaded_sdk_version, '3.2.0', '<' )
	) {
		if ( ! isset( $GLOBALS['wpb_sdk_registry'] ) || ! is_array( $GLOBALS['wpb_sdk_registry'] ) ) {
			$GLOBALS['wpb_sdk_registry'] = array();
		}
		$GLOBALS['wpb_sdk_registry']['legacy_logger_conflict'] = true;
	}
	return;
}

require_once $wpb_sdk_runtime_includes . '/class-wpb-sdk-logger.php';
