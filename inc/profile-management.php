<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Profile Management Component for WP Analytify
 *
 * This file contains all profile settings and GA4 setup functions
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include GA4 Property Management class for direct instantiation.
require_once __DIR__ . '/ga4-property-management.php';

/**
 * Profile Management Component Class
 */
class Analytify_Profile_Management {

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
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 *
	 * @since 7.0.5
	 * @version 7.0.5
	 * @return void
	 */
	private function init_hooks() {
		// Profile management hooks.
		add_action( 'update_option_wp-analytify-profile', array( $this, 'update_profiles_list_summary' ), 10, 2 );
		add_action( 'update_option_wp-analytify-advanced', array( $this, 'update_selected_profiles' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'update_profile_list_summary_on_update' ), 1 );
	}

	/**
	 * Update profiles list summary when profile option is updated
	 *
	 * @since 7.0.5
	 * @version 7.0.5
	 * @param mixed $old_value Old profile value.
	 * @param mixed $new_value New profile value.
	 * @return void
	 */
	public function update_profiles_list_summary( $old_value, $new_value ) {
		if ( isset( $new_value['hide_profiles_list'] ) && 'on' === $new_value['hide_profiles_list'] && ( $new_value['hide_profiles_list'] !== $old_value['hide_profiles_list'] ) && isset( $new_value['profile_for_dashboard'] ) ) {
			$accounts = get_option( 'profiles_list_summary' );

			update_option( 'profiles_list_summary_backup', $accounts, 'no' );

			$new_properties = array();
			if ( ! empty( $accounts ) ) {
				foreach ( $accounts->getItems() as $account ) {
					foreach ( $account->getWebProperties() as  $property ) {
						foreach ( $property->getProfiles() as $profile ) {
							// Get Property ID i.e UA Code.
							if ( $profile->getId() === $new_value['profile_for_dashboard'] ) {
								$new_properties[ $account->getId() ] = $property;
							}
							if ( $profile->getId() === $new_value['profile_for_posts'] ) {
								$new_properties[ $account->getId() ] = $property;
							}
						}
					}
				}
			}

			update_option( 'profiles_list_summary', $new_properties );
		}

		// Update stream and save measurement id when user selects the GA4 property for tracking. (Profile for posts (Backend/Front-end)).
		if ( isset( $new_value['profile_for_posts'] ) && $new_value['profile_for_posts'] && substr( $new_value['profile_for_posts'], 0, 3 ) === 'ga4' ) {
			$property_id             = explode( ':', $new_value['profile_for_posts'] )[1];
			$ga4_property_management = new Analytify_GA4_Property_Management( $this->analytify );
			$ga4_property_management->setup_property( $property_id, 'tracking' );
		}

		// Update stream and save measurement id when user selects the GA4 property for reporting. (Profile for dashboard).
		if ( isset( $new_value['profile_for_dashboard'] ) && $new_value['profile_for_dashboard'] && substr( $new_value['profile_for_dashboard'], 0, 3 ) === 'ga4' ) {
			$ga4_update_number = wp_rand( 10, 100 );
			update_option( 'ga4_update_number', 'updated_' . $ga4_update_number );
			$property_id             = explode( ':', $new_value['profile_for_dashboard'] )[1];
			$ga4_property_management = new Analytify_GA4_Property_Management( $this->analytify );
			$ga4_property_management->setup_property( $property_id, 'reporting' );
		} else {
			$ua_update_number = wp_rand( 10, 100 );
			update_option( 'ua_update_number', 'updated_' . $ua_update_number );
		}
	}


	/**
	 * Update selected profiles when advanced option is updated.
	 *
	 * @since 7.0.5
	 * @version 8.1.2
	 * @param mixed $old_value Old advanced value.
	 * @param mixed $new_value New advanced value.
	 * @return void
	 */
	public function update_selected_profiles( $old_value, $new_value ) {

		if ( isset( $new_value['google_analytics_version'] ) && ( isset( $old_value['google_analytics_version'] ) && $new_value['google_analytics_version'] !== $old_value['google_analytics_version'] ) ) {
			$analytify_profile_section = get_option( 'wp-analytify-profile' );
			if ( isset( $analytify_profile_section['profile_for_dashboard'] ) && $analytify_profile_section['profile_for_dashboard'] ) {
				$analytify_profile_section['profile_for_dashboard'] = '';
			}
			if ( isset( $analytify_profile_section['profile_for_posts'] ) && $analytify_profile_section['profile_for_posts'] ) {
				$analytify_profile_section['profile_for_posts'] = '';
			}
			update_option( 'wp-analytify-profile', $analytify_profile_section );
			delete_option( 'analytify-ga-properties-summery' );
			delete_option( 'analytify_ga4_exceptions' );
		}

		// if user change the stream update the ua code and mp secret.
		if ( isset( $new_value['ga4_web_data_stream'] ) && isset( $old_value['ga4_web_data_stream'] ) && $new_value['ga4_web_data_stream'] !== $old_value['ga4_web_data_stream'] ) {
			// TODO: legacy code with wrong naming.
			$ua_code = get_option( 'analytify_ua_code' );
			// check if the ua code is same if it's then return.
			if ( $ua_code === $new_value['ga4_web_data_stream'] ) {
				return;
			}
			// Update the tracking code.
			update_option( 'analytify_ua_code', $new_value['ga4_web_data_stream'] );

			new Analytify_Host_Analytics( 'gtag', false, true ); // update the locally host analytics file.

			// Get the stored data for currect property and stream.
			$property_info = get_option( 'analytify_tracking_property_info' );
			$all_streams   = get_option( 'analytify-ga4-streams' );

			if ( ! empty( $property_info ) ) {
				// Extract the current property id.
				$property_id = $property_info['property_id'];

				// get all the data for currently selected stream from the all streams array.
				$stream_data = $all_streams[ $property_id ][ $new_value['ga4_web_data_stream'] ] ?? false;

				// Set mp secret value initially to null (must be string for GA4 Measurement Protocol).
				$new_secret_value = null;

				if ( isset( $stream_data['full_name'] ) ) {

					$get_secret = $this->analytify->analytify_get_mp_secret( $stream_data['full_name'] );
					if ( ! empty( $get_secret ) && isset( $get_secret['secretValue'] ) && is_string( $get_secret['secretValue'] ) ) {
						$new_secret_value = $get_secret['secretValue'];
					}
					if ( null === $new_secret_value ) {
						$create_secret = $this->analytify->analytify_create_mp_secret( $property_id, $stream_data['full_name'], $stream_data['measurement_id'] );
						if ( ! empty( $create_secret ) && isset( $create_secret['secretValue'] ) && is_string( $create_secret['secretValue'] ) ) {
							$new_secret_value = $create_secret['secretValue'];
						}
					}
					if ( null !== $new_secret_value ) {
						WPANALYTIFY_Utils::update_option( 'measurement_protocol_secret', 'wp-analytify-advanced', $new_secret_value );
					}
					update_option( 'analytify_tracking_property_info', $stream_data );
					update_option( 'analytify_reporting_property_info', $stream_data );
				}
			}
		}
	}

	/**
	 * Update profile list summary on plugin update
	 *
	 * @since 7.1.1
	 * @version 8.1.0
	 * @return void
	 */
	public function update_profile_list_summary_on_update() {
		if ( version_compare( ANALYTIFY_VERSION, get_option( 'WP_ANALYTIFY_PLUGIN_VERSION' ), '>' ) ) {
			$option = get_option( 'wp-analytify-profile' );

			if ( isset( $option['hide_profiles_list'] ) && 'on' === $option['hide_profiles_list'] ) {
				$accounts = get_option( 'profiles_list_summary' );

				if ( ! $accounts ) {
					return;
				}

				// Means that its run already.
				if ( is_array( $accounts ) ) {
					return;
				}

				update_option( 'profiles_list_summary_backup', $accounts, 'no' );

				$new_value['profile_for_dashboard'] = $option['profile_for_dashboard'];
				$new_value['profile_for_posts']     = $option['profile_for_posts'];

				$new_properties = array();

				foreach ( $accounts->getItems() as $account ) {
					foreach ( $account->getWebProperties() as  $property ) {
						foreach ( $property->getProfiles() as $profile ) {
							// Get Property ID i.e UA Code.
							if ( $profile->getId() === $new_value['profile_for_dashboard'] ) {
								$new_properties[ $account->getId() ] = $property;
							}
							if ( $profile->getId() === $new_value['profile_for_posts'] ) {
								$new_properties[ $account->getId() ] = $property;
							}
						}
					}
				}

				update_option( 'profiles_list_summary', $new_properties );
			}
		}
	}
}
