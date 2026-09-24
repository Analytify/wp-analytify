<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists

/**
 * Analytify Utils Core Trait
 *
 * This trait contains core utility functions for the Analytify plugin.
 * It was created to separate core utility logic from the main utils class,
 * providing essential helper functions for data processing, formatting,
 * and common operations used throughout the plugin.
 *
 * PURPOSE:
 * - Provides core utility functions
 * - Handles data formatting and processing
 * - Manages common operations and checks
 * - Offers essential helper methods
 *
 * @package WP_Analytify
 * @subpackage Utils
 * @since 8.0.0
 */

trait Analytify_Utils_Core {

	/**
	 * Safely unslash data with fallback
	 *
	 * Removes slashes from data using WordPress wp_unslash function
	 * with a fallback to stripslashes_deep for compatibility.
	 *
	 * @param mixed $arg Data to unslash.
	 * @return mixed Unslashed data
	 */
	public static function safe_wp_unslash( $arg ) {
		return function_exists( 'wp_unslash' ) ? wp_unslash( $arg ) : stripslashes_deep( $arg );
	}

	/**
	 * Format time duration in human-readable format
	 *
	 * Converts numeric time values (in seconds) to a human-readable
	 * format with years, days, hours, minutes, and seconds.
	 *
	 * @param int|float $time Time value in seconds.
	 * @return string|false Formatted time string or false if invalid input
	 */
	public static function pretty_time( $time ) {
		if ( is_numeric( $time ) ) {
			$value        = array(
				'years'   => '00',
				'days'    => '00',
				'hours'   => '',
				'minutes' => '',
				'seconds' => '',
			);
			$attach_hours = '';
			$attach_min   = '';
			$attach_sec   = '';
			$time         = floor( $time );

			if ( $time >= 31556926 ) {
				$value['years'] = floor( $time / 31556926 );
				$time           = ( $time % 31556926 );
			}
			if ( $time >= 86400 ) {
				$value['days'] = floor( $time / 86400 );
				$time          = ( $time % 86400 );
			}
			if ( $time >= 3600 ) {
				$value['hours'] = str_pad( (string) floor( $time / 3600 ), 1, '0', STR_PAD_LEFT );
				$time           = ( $time % 3600 );
			}
			if ( $time >= 60 ) {
				$value['minutes'] = str_pad( (string) floor( $time / 60 ), 1, '0', STR_PAD_LEFT );
				$time             = ( $time % 60 );
			}
			$value['seconds'] = str_pad( (string) floor( $time ), 1, '0', STR_PAD_LEFT );

			if ( '' !== $value['hours'] ) {
				$attach_hours = '<span class="analytify_xl_f">' . _x( 'h', 'Hour Time', 'wp-analytify' ) . ' </span> ';
			}
			if ( '' !== $value['minutes'] ) {
				$attach_min = '<span class="analytify_xl_f">' . _x( 'm', 'Minute Time', 'wp-analytify' ) . ' </span>';
			}
			if ( '' !== $value['seconds'] ) {
				$attach_sec = '<span class="analytify_xl_f">' . _x( 's', 'Second Time', 'wp-analytify' ) . '</span>';
			}

			return $value['hours'] . $attach_hours . $value['minutes'] . $attach_min . $value['seconds'] . $attach_sec;
		}
		return false;
	}

	/**
	 * Format numbers with K suffix for large values
	 *
	 * Converts large numbers to a more readable format by adding
	 * 'k' suffix for values over 10,000 (e.g., 15,000 becomes 15k).
	 *
	 * @param int|float $num Number to format.
	 * @return string Formatted number
	 */
	public static function pretty_numbers( $num ) {
		if ( ! is_numeric( $num ) ) {
			return $num;
		}
		return ( $num > 10000 ) ? round( ( $num / 1000 ), 2 ) . 'k' : number_format( $num );
	}

	/**
	 * Convert fraction to percentage
	 *
	 * Converts a decimal fraction to a percentage and formats it
	 * using the pretty_numbers method for consistency.
	 *
	 * @param float $number Fraction to convert (0.0 to 1.0).
	 * @return string Formatted percentage
	 */
	public static function fraction_to_percentage( $number ) {
		return self::pretty_numbers( $number * 100 );
	}

	/**
	 * Get appropriate delimiter for REST API URLs
	 *
	 * Determines whether to use '?' or '&' as a delimiter based on
	 * whether the REST API base URL already contains query parameters.
	 *
	 * @return string Appropriate delimiter character
	 */
	public static function get_delimiter() {
		$rest_url = esc_url_raw( get_rest_url() );
		return strpos( $rest_url, '/wp-json/' ) !== false ? '?' : '&';
	}

	/**
	 * Check if analytics tracking is available
	 *
	 * Determines whether analytics tracking should be enabled based on
	 * user roles, GDPR compliance, authentication status, and settings.
	 *
	 * @param bool $only_auth Whether to only check authentication.
	 * @return bool True if tracking is available, false otherwise
	 */
	public static function is_tracking_available( $only_auth = false ) {
		global $current_user;
		$roles = $current_user->roles;

		// Check if user role is excluded from tracking.
		if ( isset( $roles[0] ) && in_array( $roles[0], $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'exclude_users_tracking', 'wp-analytify-profile', array() ), true ) ) {
			return false;
		}

		// Check GDPR compliance blocking.
		if ( Class_Analytify_GDPR_Compliance::is_gdpr_compliance_blocking() ) {
			return false;
		}

		// Check authentication and settings.
		if ( get_option( 'pa_google_token' ) ) {
			if ( 'on' === $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'install_ga_code', 'wp-analytify-profile', 'off' ) && WP_ANALYTIFY_FUNCTIONS::get_UA_code() ) {
				return true;
			}
		} elseif ( ! $only_auth && $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'manual_ua_code', 'wp-analytify-authentication', false ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if current page uses Gutenberg editor
	 *
	 * Determines whether the current page is using the Gutenberg block
	 * editor by checking multiple methods for compatibility.
	 *
	 * @return bool True if using Gutenberg, false otherwise
	 */
	public static function is_gutenberg_editor() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}
		$current_screen = get_current_screen();
		if ( $current_screen && method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}
		return false;
	}

	/**
	 * Get current admin post type
	 *
	 * Retrieves the post type of the current admin page by checking
	 * multiple sources in order of preference.
	 *
	 * @return string|null Post type or null if not found
	 */
	public static function get_current_admin_post_type() {
		global $post, $typenow, $current_screen;

		if ( $post && $post->post_type ) {
			return $post->post_type;
		} elseif ( $typenow ) {
			return $typenow;
		} elseif ( $current_screen && is_object( $current_screen ) && isset( $current_screen->post_type ) ) {
			return $current_screen->post_type;
		} elseif ( isset( $_REQUEST['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
			return sanitize_key( $_REQUEST['post_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
		}
		return null;
	}

	/**
	 * Get option value with fallback
	 *
	 * Retrieves a specific option value from a section with a default
	 * fallback value if the option is not set.
	 *
	 * @param string $option  Option name to retrieve.
	 * @param string $section Section name containing the option.
	 * @param mixed  $default Default value if option not found.
	 * @return mixed Option value or default
	 */
	public static function get_option( $option, $section, $default = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound -- Default parameter name is acceptable
		$options = get_option( $section );
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}
		return $default;
	}

	/**
	 * Update option value.
	 *
	 * Updates a specific option value in a section.
	 *
	 * @param mixed $option  Option name to update.
	 * @param mixed $section Section name containing the option.
	 * @param mixed $value   New value for the option.
	 * @return bool True if option was updated successfully.
	 */
	public static function update_option( $option, $section, $value ) {
		$options            = (array) get_option( $section );
		$options[ $option ] = $value;
		return update_option( $section, $options );
	}

	/**
	 * Add GA4 exception
	 *
	 * Stores an exception for a specific GA4 type, including reason and message.
	 *
	 * @param mixed $type    Exception type (e.g., 'mp_secret_exception', 'create_stream_exception').
	 * @param mixed $reason  Reason for the exception.
	 * @param mixed $message Detailed message for the exception.
	 * @return void
	 */
	public static function add_ga4_exception( $type, $reason, $message ) {
		$analytify_ga4_exceptions                     = (array) get_option( 'analytify_ga4_exceptions' );
		$analytify_ga4_exceptions[ $type ]['reason']  = $reason;
		$analytify_ga4_exceptions[ $type ]['message'] = $message;
		update_option( 'analytify_ga4_exceptions', $analytify_ga4_exceptions );
	}

	/**
	 * Remove GA4 exception
	 *
	 * Removes an exception for a specific GA4 type.
	 *
	 * @param mixed $type Exception type to remove.
	 * @return void
	 */
	public static function remove_ga4_exception( $type ) {
		$analytify_ga4_exceptions = (array) get_option( 'analytify_ga4_exceptions' );
		unset( $analytify_ga4_exceptions[ $type ] );
		update_option( 'analytify_ga4_exceptions', $analytify_ga4_exceptions );
	}

	// Additional core helpers moved from WPANALYTIFY_Utils.
	/**
	 * Remove WordPress plugin directory path
	 *
	 * Removes the WordPress plugin directory path from a plugin file path
	 * to get a relative path for easier handling.
	 *
	 * @param string $name Full path to the plugin file.
	 * @return string Relative path
	 */
	public static function remove_wp_plugin_dir( $name ) {
		$plugin = str_replace( WP_PLUGIN_DIR, '', $name );
		return substr( $plugin, 1 );
	}

	/**
	 * Safely get and sanitize GET parameter
	 *
	 * Eliminates DRY violations for the common pattern:
	 * sanitize_text_field( wp_unslash( $_GET['param'] ) )
	 *
	 * @param string $param Parameter name.
	 * @param string $default Default value if parameter not set.
	 * @return string Sanitized parameter value or default
	 */
	public static function safe_get_param( $param, $default = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound -- Default parameter name is acceptable
		if ( ! isset( $_GET[ $param ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled by caller
			return $default;
		}
		return sanitize_text_field( wp_unslash( $_GET[ $param ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled by caller
	}

	/**
	 * Safely get and sanitize POST parameter
	 *
	 * Eliminates DRY violations for the common pattern:
	 * sanitize_text_field( wp_unslash( $_POST['param'] ) )
	 *
	 * @param string $param Parameter name.
	 * @param string $default Default value if parameter not set.
	 * @return string Sanitized parameter value or default
	 */
	public static function safe_post_param( $param, $default = '' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.defaultFound -- Default parameter name is acceptable
		if ( ! isset( $_POST[ $param ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by caller
			return $default;
		}
		return sanitize_text_field( wp_unslash( $_POST[ $param ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by caller
	}

	/**
	 * Check if current page matches a specific pattern
	 *
	 * Eliminates DRY violations for the common pattern:
	 * isset( $_GET['page'] ) && strpos( $_GET['page'], 'analytify-*' ) === 0
	 *
	 * @param string $pattern Page pattern to check (e.g., 'analytify-settings', 'analytify-dashboard').
	 * @return bool True if current page matches pattern
	 */
	public static function is_current_page( $pattern ) {
		$current_page = self::safe_get_param( 'page' );
		return $current_page && strpos( $current_page, $pattern ) === 0;
	}

	/**
	 * Safely verify nonce with proper sanitization
	 *
	 * Eliminates DRY violations for the common pattern:
	 * wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'nonce-name' )
	 *
	 * @param string $nonce_key Nonce key to verify.
	 * @param string $action Action name for nonce verification.
	 * @param string $method Request method ('GET' or 'POST').
	 * @return bool True if nonce is valid
	 */
	public static function safe_verify_nonce( $nonce_key, $action, $method = 'GET' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by this method
		$super_global = ( 'POST' === $method ) ? $_POST : $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended -- Nonce verification is handled by this method

		if ( ! isset( $super_global[ $nonce_key ] ) ) {
			return false;
		}

		$nonce_value = sanitize_key( wp_unslash( $super_global[ $nonce_key ] ) );
		return (bool) wp_verify_nonce( $nonce_value, $action );
	}

	/**
	 * Calculate date difference
	 *
	 * Calculates the difference between two dates and returns an array
	 * containing start date, end date, and the number of days difference.
	 *
	 * @param string $start_date Start date.
	 * @param string $end_date   End date.
	 * @return array<string, mixed> Array of dates and difference
	 */
	public static function calculate_date_diff( $start_date, $end_date ) {
		$start_date_obj = date_create( $start_date );
		$end_date_obj   = date_create( $end_date );

		if ( ! $start_date_obj || ! $end_date_obj ) {
			return array(
				'start_date' => $start_date,
				'end_date'   => $end_date,
				'diff_days'  => 0,
			);
		}

		$diff               = date_diff( $end_date_obj, $start_date_obj );
		$compare_start_date = gmdate( 'Y-m-d', strtotime( $start_date . $diff->format( ' %R%a days' ) ) ? strtotime( $start_date . $diff->format( ' %R%a days' ) ) : time() );
		$compare_end_date   = $start_date;
		$diff_days          = $diff->format( '%a' );
		return array(
			'start_date' => $compare_start_date,
			'end_date'   => $compare_end_date,
			'diff_days'  => (string) $diff_days,
		);
	}

	/**
	 * Print settings array.
	 *
	 * Prints an array of settings in a formatted JSON structure.
	 *
	 * @param array<string, mixed> $settings_array Array of settings to print.
	 * @return void
	 */
	public static function print_settings_array( $settings_array ) {
		if ( is_array( $settings_array ) ) {
			foreach ( $settings_array as $key => $value ) {
				if ( is_array( $value ) ) {
					printf( "\n-- %s --\n", esc_html( $key ) );
					// wp_json_encode outputs safe JSON text - don't escape it for textarea display.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON output is safe
					echo wp_json_encode( $value, JSON_PRETTY_PRINT ) . "\n";
				} else {
					printf( "%s: %s\n", esc_html( $key ), esc_html( $value ) );
				}
			}
		}
	}

	/**
	 * Get addons to update.
	 *
	 * Determines which addons need to be updated based on their versions.
	 *
	 * @return array<string, mixed> Array of addon names that need updating.
	 */
	public static function get_addons_to_upgmdate() {
		$addons_to_update = array();
		if ( defined( 'ANALYTIFY_PRO_VERSION' ) && -1 === version_compare( ANALYTIFY_PRO_VERSION, '5.0.0' ) ) {
			$addons_to_update[] = 'Analytify Pro';
		}
		if ( defined( 'ANALTYIFY_WOOCOMMERCE_VERSION' ) && -1 === version_compare( ANALTYIFY_WOOCOMMERCE_VERSION, '5.0.0' ) ) {
			$addons_to_update[] = 'Analytify - WooCommerce Tracking';
		}
		if ( defined( 'ANALTYIFY_AUTHORS_DASHBORD_VERSION' ) && -1 === version_compare( ANALTYIFY_AUTHORS_DASHBORD_VERSION, '3.0.0' ) ) {
			$addons_to_update[] = 'Analytify - Authors Tracking';
		}
		if ( defined( 'ANALYTIFY_FORMS_VERSION' ) && -1 === version_compare( ANALYTIFY_FORMS_VERSION, '3.0.0' ) ) {
			$addons_to_update[] = 'Analytify - Forms Tracking';
		}
		if ( defined( 'ANALTYIFY_CAMPAIGNS_VERSION' ) && -1 === version_compare( ANALTYIFY_CAMPAIGNS_VERSION, '3.0.0' ) ) {
			$addons_to_update[] = 'Analytify - UTM Campaigns Tracking';
		}
		if ( defined( 'ANALTYIFY_EMAIL_VERSION' ) && -1 === version_compare( ANALTYIFY_EMAIL_VERSION, '3.0.0' ) ) {
			$addons_to_update[] = 'Analytify - Email Notifications';
		}
		if ( defined( 'ANALYTIFY_DASHBOARD_VERSION' ) && -1 === version_compare( ANALYTIFY_DASHBOARD_VERSION, '3.0.0' ) ) {
			$addons_to_update[] = 'Analytify - Google Analytics Dashboard Widget';
		}
		if ( class_exists( 'WP_Analytify_Edd' ) ) {
			$all_plugins = get_plugins();
			if ( isset( $all_plugins['wp-analytify-edd/wp-analytify-edd.php']['Version'] ) && -1 === version_compare( $all_plugins['wp-analytify-edd/wp-analytify-edd.php']['Version'], '3.0.0' ) ) {
				$addons_to_update[] = 'Analytify - Easy Digital Downloads Tracking';
			}
		}
		// Convert to associative array with addon names as keys.
		$result = array();
		foreach ( $addons_to_update as $addon ) {
			$result[ $addon ] = $addon;
		}
		return $result;
	}

	/**
	 * Returns row limit shared by dashboard tables and CSV exports.
	 *
	 * Uses the same filter hook and context as the frontend so custom
	 * limit filters apply consistently to both views.
	 *
	 * @since 9.1.1
	 *
	 * @param string $filter_name   Filter hook name.
	 * @param int    $default_limit Default row limit.
	 * @param mixed  ...$args       Additional filter arguments after context.
	 * @return int
	 */
	public static function wp_analytify_get_api_limit( $filter_name, $default_limit, ...$args ) {
		$context = 'dashboard';

		// Optional explicit context: 'dashboard' | 'csv_export'. Other first extras
		// (e.g. 'WC', post ID) stay as additional filter arguments.
		if ( isset( $args[0] ) && is_string( $args[0] )
			&& in_array( $args[0], array( 'dashboard', 'csv_export' ), true ) ) {
			$context = array_shift( $args );
		}

		$limit = apply_filters( $filter_name, $default_limit, $context, ...$args );

		return max( 0, (int) $limit );
	}
}
