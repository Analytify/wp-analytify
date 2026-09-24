<?php
/**
 * WPBrigade SDK path and API constants.
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'WPBRIGADE_SDK_VERSION' ) ) {
	define( 'WPBRIGADE_SDK_VERSION', '3.3.1' );
}

if ( ! defined( 'WPBRIGADE_SDK_DIR' ) ) {
	define( 'WPBRIGADE_SDK_DIR', __DIR__ );
}

if ( ! defined( 'WPBRIGADE_SDK_DIR_PATH' ) ) {
	define( 'WPBRIGADE_SDK_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPBRIGADE_SDK_DIR_INCLUDES' ) ) {
	define( 'WPBRIGADE_SDK_DIR_INCLUDES', WPBRIGADE_SDK_DIR . '/includes' );
}
if ( ! defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
	define( 'WPBRIGADE_SDK_API_ENDPOINT', 'https://app.telemetry.wpbrigade.com/api/v2' );
}

if ( ! defined( 'WPBRIGADE_SDK_DIR_SDK' ) ) {
	define( 'WPBRIGADE_SDK_DIR_SDK', WPBRIGADE_SDK_DIR_INCLUDES );
}
