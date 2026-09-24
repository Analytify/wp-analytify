<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure.
/**
 * Core Functions Class for WP Analytify
 *
 * This file contains the WP_ANALYTIFY_FUNCTIONS class that was previously
 * in wpa-core-functions.php. The class is kept as is for backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

/**
 * Core functions class
 */
class WP_ANALYTIFY_FUNCTIONS {

	/**
	 * Check GA version and show notice if UA is being used.
	 *
	 * @return bool
	 */
	public static function wpa_check_ga_version() {

		if ( 'ga4' === WPANALYTIFY_Utils::get_ga_mode() ) {
			return true;
		}

		$class   = 'wp-analytify-danger';
		$message = sprintf(     // translators: Check GA version.
			esc_html__( '%1$sAttention:%2$s Switch to GA4 (Google Analytics 4), Your current version of Google Analytics (UA) is outdated and no longer tracks data. %3$sFollow the guide%4$s.', 'wp-analytify' ),
			'<b>',
			'</b>',
			'<a href="https://analytify.io/doc/switch-to-ga4/?utm_source=plugin-notices" target="_blank">',
			'</a>'
		);
		analytify_notice( $message, $class );

		return false;
	}

	/**
	 * Check if profile is selected for dashboard.
	 *
	 * @param string $type Page name.
	 * @param string $message Custom message.
	 * @return bool true or false
	 *
	 * @since  [1.3]
	 */
	public static function wpa_check_profile_selection( $type, $message = '' ) {

		$_analytify_profile = get_option( 'wp-analytify-profile' );
		$dashboard_profile  = isset( $_analytify_profile['profile_for_dashboard'] ) ? $_analytify_profile['profile_for_dashboard'] : '';

		if ( empty( $dashboard_profile ) ) {

			if ( '' === $message ) {

				$class          = 'wp-analytify-danger';
				$link           = menu_page_url( 'analytify-settings', false ) . '#wp-analytify-profile';
				$notice_message = sprintf( // translators: No profile selected notice.
					esc_html__( '%1$s Dashboard can\'t be loaded until you select your website profile %2$s here%3$s.', 'wp-analytify' ),
					$type,
					'<a style="text-decoration:none" href="' . $link . '">',
					'</a>'
				);
				analytify_notice( $notice_message, $class );
			} else {
				echo wp_kses_post( $message );
			}
			return true;

		} else {
			return false;
		}
	}

	/**
	 * Creates the Google OAuth authentication URL.
	 *
	 * @since 2.0.0
	 * @version 7.0.0
	 * @return string The complete authentication URL with query parameters.
	 */
	public static function analytify_create_auth_url() {
		// @formatter:off
		$auth_url  = 'https://accounts.google.com/o/oauth2/v2/auth?';
		$query_arr = array(
			'client_id'     => WP_ANALYTIFY_CLIENTID,
			'redirect_uri'  => WP_ANALYTIFY_REDIRECT,
			'response_type' => 'code',
			'scope'         => WP_ANALYTIFY_SCOPE_FULL,
			'state'         => add_query_arg( array( 'nonce' => wp_create_nonce( 'analytify_analytics_login' ) ), get_admin_url() . 'admin.php?page=analytify-settings' ),
			'access_type'   => 'offline',
			'prompt'        => 'consent',
		);
		// @formatter:on
		$auth_url = $auth_url . http_build_query( $query_arr );
		return $auth_url;
	}

	/**
	 * Fetch list of all profiles.
	 *
	 * @return array<string, mixed>
	 */
	public static function fetch_profiles_list() {

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$profiles     = get_transient( 'profiles_list' );

		if ( ! $profiles && get_option( 'pa_google_token' ) ) {

			$profiles = $wp_analytify->pt_get_analytics_accounts();
			set_transient( 'profiles_list', $profiles, 0 );
		}

		return $profiles;
	}

	/**
	 * Fetch list of all profiles in dropdown
	 *
	 * @since  2.0.0
	 * @return object accounts list
	 */
	public static function fetch_profiles_list_summary() {

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$profiles     = get_option( 'profiles_list_summary' );

		if ( ! $profiles && get_option( 'pa_google_token' ) ) {

			$profiles = $wp_analytify->pt_get_analytics_accounts_summary();
			update_option( 'profiles_list_summary', $profiles );
		}

		return $profiles;
	}

	/**
	 * Returns the property list that was saved after fetching from Google.
	 * If DB does not contains the list, get using Google's method for UA and GA4.
	 *
	 * @param string $mode Mode (UA or GA4).
	 * @return array<string, mixed>
	 */
	public static function fetch_ga_properties( $mode = 'both' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Parameter kept for backward compatibility.
		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		if ( $wp_analytify->get_ga4_exception() ) {
			WPANALYTIFY_Utils::handle_exceptions( $wp_analytify->get_ga4_exception() );
		}

		$properties = get_option( 'analytify-ga-properties-summery' );

		// If option is not set yet, get and generate property list for both UA and GA4.
		if ( empty( $properties['GA4'] ) && empty( $properties['UA'] ) && get_option( 'pa_google_token' ) ) {

			$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			$properties   = array();
			// Store all UA properties.
			$properties['UA'] = array();
			// Store all GA4 properties.
			$properties['GA4'] = array();

			// Fetch ga4 or UA properties based on the google analytics version.
			if ( 'ga4' === WPANALYTIFY_Utils::get_ga_mode() ) {
				$ga4_profiles_raw = $wp_analytify->analytify_get_ga_properties();
				if ( ! empty( $ga4_profiles_raw ) ) {
					foreach ( $ga4_profiles_raw as $parent_account_name => $account_properties ) {
						foreach ( $account_properties as $property_item ) {
							// Push into an array with the property name as key and profile ID as child key.
							$properties['GA4'][ $parent_account_name ][ 'ga4:' . $property_item['id'] ] = array(
								'name'            => $property_item['display_name'],
								'code'            => $property_item['id'],
								'property_id'     => '',
								'website_url'     => '',
								'web_property_id' => '',
								'view_id'         => '',
							);
						}
					}
				}
			}

			update_option( 'analytify-ga-properties-summery', $properties );
		}

		return $properties;
	}

	/**
	 * This function is used to fetch the profile name, UA Code from selected account/property.
	 *
	 * @param string|int $id    - Profile ID.
	 * @param string     $index - Type of info wanted.
	 * @return string
	 */
	public static function search_profile_info( $id, $index ) {

		if ( ! get_option( 'pa_google_token' ) ) {
			return '';
		}

		$ga_properties = self::fetch_ga_properties();
		$ga_properties = 'ga4' === WPANALYTIFY_Utils::get_ga_mode() ? $ga_properties['GA4'] : $ga_properties['UA'];

		if ( empty( $ga_properties ) ) {
			return '';
		}

		foreach ( $ga_properties as $account => $properties ) {
			foreach ( $properties as $property_id => $property ) {
				// Handle GA4 prefix - check both with and without prefix.
				if ( ! empty( $property_id ) && ! empty( $id ) && ( $id === $property_id || ( strpos( $id, 'ga4:' ) === 0 && substr( $id, 4 ) === $property_id ) ) ) {
					switch ( $index ) {
						case 'webPropertyId':
							return $property['code'];
						case 'websiteUrl':
							return $property['website_url'];
						case 'name':
							return $property['name'];
						case 'accountId':
							return $property['property_id'];
						case 'internalWebPropertyId':
							return $property['web_property_id'];
						case 'viewId':
							return $property['view_id'];
						default:
							return '';
					}
				}
			}
		}

		return '';
	}

	/**
	 * This function is used to fetch the property information
	 *
	 * @param string $name Name of the value required. Accepts:property_id, stream_name, measurement_id, url.
	 *
	 * @return string
	 */
	public static function ga_reporting_property_info( $name ) {

		if ( ! get_option( 'pa_google_token' ) ) {
			return '';
		}

		$property_data = get_option( 'analytify_reporting_property_info', false );

		if ( ! $property_data ) {
			return '';
		}

		switch ( $name ) {
			case ( 'property_id' === $name || 'webPropertyId' === $name ):
				$value = $property_data['property_id'];
				break;
			case ( 'stream_name' === $name || 'name' === $name ):
				$value = str_replace( ' - ' . $property_data['url'], '', $property_data['stream_name'] );
				break;
			case 'measurement_id':
				$value = $property_data['measurement_id'];
				break;
			case ( 'url' === $name || 'websiteUrl' === $name ):
				$value = $property_data['url'];
				break;
			default:
				$value = '';
				break;
		}

		return $value;
	}

	/**
	 * Return the UA Code for selected profile.
	 * For GA4, returns Measurement ID (G-XXXXXXXXXX).
	 *
	 * @since 2.0.4
	 * @version 8.0.0
	 * @return string
	 */
	public static function get_UA_code() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- Method name kept for backward compatibility.

		// Get selected GA4 property.
		$profile_id = $GLOBALS['WP_ANALYTIFY']->settings->get_option(
			'profile_for_posts',
			'wp-analytify-profile'
		);

		if ( empty( $profile_id ) ) {
			delete_option( 'analytify_ua_code' );
			return '';
		}

		// Check cached measurement ID.
		$cached  = get_option( 'analytify_ua_code' );
		$current = self::get_ga4_measurement_id( $profile_id );

		// Cache is valid.
		if ( $cached && $cached === $current ) {
			return $cached;
		}

		// Update cache with fresh code.
		if ( ! empty( $current ) ) {
			update_option( 'analytify_ua_code', $current );
		} else {
			delete_option( 'analytify_ua_code' );
		}

		return $current;
	}


	/**
	 * Resolve GA4 Measurement ID from all available sources.
	 *
	 * This function attempts to retrieve the GA4 Measurement ID in the following order:
	 * 1. From the reporting property info.
	 * 2. From advanced settings (user-selected stream).
	 * 3. From stored stream data.
	 *
	 * @param string $profile_id The GA profile ID or GA4 property identifier.
	 * @return string The GA4 Measurement ID if found, empty string otherwise.
	 * @since 8.0.0
	 */
	private static function get_ga4_measurement_id( $profile_id ) {
		// 1. Reporting property info
		$measurement_id = self::ga_reporting_property_info( 'measurement_id' );
		if ( ! empty( $measurement_id ) ) {
			return $measurement_id;
		}

		// 2. Advanced settings (user-selected stream)
		$advanced = get_option( 'wp-analytify-advanced', array() );
		if ( ! empty( $advanced['ga4_web_data_stream'] ) ) {
			return $advanced['ga4_web_data_stream'];
		}

		// 3. Stored stream data
		$property_id = ( strpos( $profile_id, 'ga4:' ) !== false )
			? explode( ':', $profile_id )[1]
			: $profile_id;

		$streams = get_option( 'analytify-ga4-streams', array() );

		if ( isset( $streams[ $property_id ] ) && is_array( $streams[ $property_id ] ) ) {
			$first_stream = reset( $streams[ $property_id ] );
			if ( ! empty( $first_stream['measurement_id'] ) ) {
				return $first_stream['measurement_id'];
			}
		}

		return '';
	}



	/**
	 * Check if user is connected to Google Analytics.
	 *
	 * @return bool
	 */
	public static function is_connected() {
		return ! empty( get_option( 'pa_google_token' ) );
	}

	/**
	 * Check if profile is selected for posts and dashboard.
	 *
	 * @return bool
	 */
	public static function is_profile_selected() {
		$load_profile_settings = get_option( 'wp-analytify-profile' );
		if ( ! empty( $load_profile_settings['profile_for_posts'] ) && ! empty( $load_profile_settings['profile_for_dashboard'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get Google Analytics report URL.
	 *
	 * @param string $dashboard_profile_ID Dashboard profile ID.
	 * @return string
	 */
	public static function get_ga_report_url( $dashboard_profile_ID ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Parameter kept for backward compatibility.
		if ( 'ga4' === WPANALYTIFY_Utils::get_ga_mode() ) {
			return 'p' . WPANALYTIFY_Utils::get_reporting_property();
		}
		return '';
	}

	/**
	 * Get Google Analytics report range URL parameters.
	 *
	 * @param string $start_date Start date.
	 * @param string $end_date End date.
	 * @param string $compare_start_date Compare start date.
	 * @param string $compare_end_date Compare end date.
	 * @return string
	 */
	public static function get_ga_report_range( $start_date, $end_date, $compare_start_date, $compare_end_date ) {
		return '%3F_u.date00%3D' . str_replace( '-', '', $start_date ) . '%26_u.date01%3D' . str_replace( '-', '', $end_date ) . '%26_u.date10%3D' . str_replace( '-', '', $compare_start_date ) . '%26_u.date11%3D' . str_replace( '-', '', $compare_end_date );
	}

	/**
	 * Message when the analytics session token is missing (e.g. post_analytics_token).
	 * Used by Pro add-on dashboards such as Paid Memberships Pro.
	 *
	 * @since 9.0.0
	 * @return void
	 */
	public static function wpa_authenticate_message() {
		esc_html_e( 'You must be logged in to see the Analytics Dashboard.', 'wp-analytify' );
	}

	/**
	 * Message when the current user lacks the role/capability to view the dashboard.
	 *
	 * @since 9.0.0
	 * @return void
	 */
	public static function wpa_authorize_message() {
		esc_html_e( 'You do not have permission to view this dashboard.', 'wp-analytify' );
	}

	/**
	 * HTML wrapper for inline dashboard errors (matches analytify-stats-error-msg pattern).
	 *
	 * @param string $message_html Already-escaped message text.
	 * @return string
	 */
	private static function wpa_dashboard_error_markup( $message_html ) {
		return '<div class="analytify-stats-error-msg"><div class="wpb-error-box"><span class="blk"><span class="line">'
			. '</span><span class="dot"></span></span><span class="information-txt">' . $message_html . '</span></div></div>';
	}

	/**
	 * Markup when Google Analytics is not connected or the session token is missing.
	 * LearnDash and other Pro dashboards echo the return value.
	 *
	 * @since 9.0.0
	 * @return string
	 */
	public static function wpa_not_connected() {
		return self::wpa_dashboard_error_markup(
			esc_html__( 'You must be logged in to see the Analytics Dashboard.', 'wp-analytify' )
		);
	}

	/**
	 * Markup when the current user may not view the dashboard (role/capability).
	 *
	 * @since 9.0.0
	 * @return string
	 */
	public static function wpa_not_allowed() {
		return self::wpa_dashboard_error_markup(
			esc_html__( 'You do not have permission to view this dashboard.', 'wp-analytify' )
		);
	}

	/**
	 * Placeholder for Pro add-on dashboards that used to call a second profile notice.
	 * The notice is already output when the caller evaluates {@see wpa_check_profile_selection()}
	 * in an `if` condition (side effect), so this must not call it again.
	 *
	 * @since 9.0.0
	 * @return void
	 */
	public static function wpa_select_profile_message() {
	}
}
