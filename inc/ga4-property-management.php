<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * GA4 Property Management Component for WP Analytify
 *
 * This file contains all GA4 property setup and management functions
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GA4 Property Management Component Class
 */
class Analytify_GA4_Property_Management {

	/**
	 * Main plugin instance
	 *
	 * @var WP_Analytify
	 */
	private $analytify;

	/**
	 * Constructor
	 *
	 * @version 7.0.5
	 * @param WP_Analytify $analytify Main plugin instance.
	 */
	public function __construct( $analytify ) {
		$this->analytify = $analytify;
	}

	/**
	 * Setup property for tracking and reporting.
	 *
	 * @version 9.1.0
	 * @param integer $property_id Property ID.
	 * @param string  $mode Mode.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function setup_property( $property_id, $mode ) {

		$ga4_streams      = $this->analytify_get_ga_streams( $property_id ) ?? false;
		$measurement_data = array();

		if ( ! empty( $ga4_streams ) ) {
			$stream_name    = 'Analytify - ' . get_site_url();
			$default_stream = null;
			// if our created stream exist select that one. Otherwise select the first stream in array.
			foreach ( $ga4_streams as $stream ) {
				if ( isset( $stream['stream_name'] ) && $stream['stream_name'] === $stream_name ) {
					$default_stream = $stream;
					break;
				} elseif ( ! $default_stream && isset( $stream['stream_name'] ) ) {
					$default_stream = $stream;
				}
			}
			// if found the stream select that one otherwise checks added for old streams structure we were using if found the stream in that structure select it otherwise return null.
			$measurement_data = ! empty( $default_stream ) ? $default_stream : ( isset( $ga4_streams['measurement_id'] ) && isset( $ga4_streams['full_name'] ) ? $ga4_streams : null );
		} else {
			$measurement_data = $this->analytify_create_ga_stream( $property_id );
		}

		if ( ! empty( $measurement_data['measurement_id'] ) ) {

			// Get and Update the secret value in settings.
			$get_secret_data = $this->analytify_get_mp_secret( $measurement_data['full_name'] );
			if ( ! empty( $get_secret_data ) && isset( $get_secret_data['secretValue'] ) ) {
				WPANALYTIFY_Utils::update_option( 'measurement_protocol_secret', 'wp-analytify-advanced', $get_secret_data['secretValue'] );
			} else {
				$mp_secret_data = $this->analytify_create_mp_secret( $property_id, $measurement_data['full_name'], $measurement_data['measurement_id'] );
				if ( ! empty( $mp_secret_data ) && isset( $mp_secret_data['secretValue'] ) ) {
					WPANALYTIFY_Utils::update_option( 'measurement_protocol_secret', 'wp-analytify-advanced', $mp_secret_data['secretValue'] );
				}
			}

			$dimensions = $this->analytify_list_dimensions_needs_creation();

			// Store property with stream data for future use.
			update_option( 'analytify_' . $mode . '_property_info', $measurement_data );

			if ( 'tracking' === $mode ) {
				update_option( 'analytify_ua_code', $measurement_data['measurement_id'] );
				new Analytify_Host_Analytics( 'gtag', false, true ); // update the locally host analytics file.
				// update the advanced section ga4 stream option value.
				WPANALYTIFY_Utils::update_option( 'ga4_web_data_stream', 'wp-analytify-advanced', $measurement_data['measurement_id'] );
			}

			// Create dimensions for Analytify tracking.
			foreach ( $dimensions as $dimension_info ) {
				$create_dimesion = $this->analytify_create_dimension( $dimension_info['parameter_name'], $dimension_info['display_name'], $dimension_info['scope'] );
			}
		}
	}

	/**
	 * Get GA4 streams for a property
	 *
	 * @version 7.0.5
	 * @param integer $property_id Property ID.
	 * @return array<string, mixed>|false
	 */
	private function analytify_get_ga_streams( $property_id ) {
		if ( method_exists( $this->analytify, 'analytify_get_ga_streams' ) ) {
			return $this->analytify->analytify_get_ga_streams( $property_id );
		}
		return false;
	}

	/**
	 * Create GA4 stream
	 *
	 * @version 7.0.5
	 * @param integer $property_id Property ID.
	 * @return array<string, mixed>|false
	 */
	private function analytify_create_ga_stream( $property_id ) {
		if ( method_exists( $this->analytify, 'analytify_create_ga_stream' ) ) {
			return $this->analytify->analytify_create_ga_stream( $property_id );
		}
		return false;
	}

	/**
	 * Get measurement protocol secret
	 *
	 * @version 7.0.5
	 * @param string $full_name Full name.
	 * @return string|false
	 */
	private function analytify_get_mp_secret( $full_name ) {
		if ( method_exists( $this->analytify, 'analytify_get_mp_secret' ) ) {
			return $this->analytify->analytify_get_mp_secret( $full_name );
		}
		return false;
	}

	/**
	 * Create measurement protocol secret
	 *
	 * @version 7.0.5
	 * @param integer $property_id Property ID.
	 * @param string  $full_name Full name.
	 * @param string  $measurement_id Measurement ID.
	 * @return string|false
	 */
	private function analytify_create_mp_secret( $property_id, $full_name, $measurement_id ) {
		if ( method_exists( $this->analytify, 'analytify_create_mp_secret' ) ) {
			return $this->analytify->analytify_create_mp_secret( $property_id, $full_name, $measurement_id );
		}
		return false;
	}

	/**
	 * List dimensions that need creation
	 *
	 * @since 7.0.5
	 * @version 9.1.0
	 * @return array
	 */
	private function analytify_list_dimensions_needs_creation() {
		if ( method_exists( $this->analytify, 'analytify_list_dimensions_needs_creation' ) ) {
			return $this->analytify->analytify_list_dimensions_needs_creation();
		}
		return array();
	}

	/**
	 * Create dimension
	 *
	 * @since 7.0.5
	 * @version 9.1.0
	 * @param string $parameter_name Parameter name.
	 * @param string $display_name Display name.
	 * @param string $scope Scope.
	 * @return bool
	 */
	private function analytify_create_dimension( $parameter_name, $display_name, $scope ) {
		if ( method_exists( $this->analytify, 'analytify_create_dimension' ) ) {
			return $this->analytify->analytify_create_dimension( $parameter_name, $display_name, $scope );
		}
		return false;
	}
}
