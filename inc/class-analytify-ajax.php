<?php
/**
 * Analytify AJAX Handler
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Handling all the AJAX calls in WP Analytify
 *
 * @since 1.2.4
 * @class WPANALYTIFY_AJAX
 */

// Include all AJAX trait files.
require_once __DIR__ . '/ajax-traits/dashboard.php';
require_once __DIR__ . '/ajax-traits/geographic.php';
require_once __DIR__ . '/ajax-traits/content.php';
require_once __DIR__ . '/ajax-traits/system.php';
require_once __DIR__ . '/ajax-traits/utility.php';
require_once __DIR__ . '/ajax-traits/main-class.php';

add_action( 'wp_ajax_analytify_opt_out_option', 'analytify_opt_out_option' );
add_action( 'wp_ajax_analytify_refresh_ga4_streams', 'analytify_refresh_ga4_streams' );

/**
 * Update partial opt-out options
 *
 * This Method is used as ajax call action to update partial opt-out options.
 *
 * @return void
 */
function analytify_opt_out_option() {
	if ( ! current_user_can( 'manage_options' ) || ! check_ajax_referer( 'analytify_optout_page_nonce', 'optout_nonce' ) ) {
		wp_die( '<p>' . esc_html__( 'Sorry, you are not allowed to edit this item.', 'wp-analytify' ) . '</p>', 403 );
	}
	// Get the current option and decode it as an associative array.
	$sdk_data = json_decode( get_option( 'wpb_sdk_wp-analytify' ), true );
	// If there is no current option, initialize an empty array.
	if ( ! $sdk_data ) {
		$sdk_data = array();
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Handled by nonce verification
	$setting_name = isset( $_POST['setting_name'] ) ? $_POST['setting_name'] : '';  // e.g., communication, diagnostic_info, extensions.
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Handled by nonce verification
	$setting_value = isset( $_POST['setting_value'] ) ? $_POST['setting_value'] : '';  // The new value to be updated.
	// Update the specific setting in the array.
	$sdk_data[ $setting_name ] = $setting_value;
	// Encode the array back into a JSON string and update the option.
	update_option( 'wpb_sdk_wp-analytify', wp_json_encode( $sdk_data ) );
	die( 'analytify_opt_out_option' );
}

/**
 * AJAX handler to refresh GA4 streams for a property
 *
 * @return void
 */
function analytify_refresh_ga4_streams() {
	if ( ! current_user_can( 'manage_options' ) || ! check_ajax_referer( 'analytify_settings_nonce', 'nonce' ) ) {
		wp_die( '<p>' . esc_html__( 'Sorry, you are not allowed to edit this item.', 'wp-analytify' ) . '</p>', 403 );
	}

	$property_id = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';

	if ( empty( $property_id ) ) {
		wp_send_json_error( 'Property ID is required' );
	}

	// Initialize GA4 core to fetch streams.
	if ( isset( $GLOBALS['WP_ANALYTIFY'] ) && method_exists( $GLOBALS['WP_ANALYTIFY'], 'analytify_get_ga_streams' ) ) {
		$streams = $GLOBALS['WP_ANALYTIFY']->analytify_get_ga_streams( $property_id );

		if ( ! empty( $streams ) ) {
			// Store streams data.
			$existing_streams                 = get_option( 'analytify-ga4-streams', array() );
			$existing_streams[ $property_id ] = $streams;
			update_option( 'analytify-ga4-streams', $existing_streams );

			wp_send_json_success( array( 'streams' => $streams ) );
		} else {
			wp_send_json_error( 'No streams found for this property' );
		}
	} else {
		wp_send_json_error( 'GA4 Core method not available' );
	}
}

// The main AJAX class is now defined in the trait files.
// All functionality is preserved and organized into logical groups.

/**
 * Load AJAX handler
 *
 * @return mixed
 */
function wp_analytify_ajax_load() {
	return WPANALYTIFY_AJAX::init();
}

$GLOBALS['WPANALYTIFY_AJAX'] = wp_analytify_ajax_load();
