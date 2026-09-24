<?php
/**
 * Geographic AJAX Trait for WP Analytify
 *
 * @package WP_Analytify
 */

/**
 * Geographic AJAX Trait.
 *
 * This trait contains geographic-related AJAX functionality that was previously
 * in the WPANALYTIFY_AJAX class. It handles country, city, and geographic
 * analytics data loading.
 *
 * @since 8.0.0
 */
trait Analytify_AJAX_Geographic {

	/**
	 * Load country stats for the dashboard
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function load_country_stats() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-country-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-country-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-country-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/country-stats.php';
				if ( function_exists( 'pa_include_country' ) ) {
					pa_include_country( $wp_analytify, $stats );
				}
			}
		}
		die();
	}

	/**
	 * Load city stats for the dashboard
	 *
	 * @return void
	 */
	public static function load_city_stats() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-city-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-city-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-city-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/city-stats.php';
				if ( function_exists( 'pa_include_city' ) ) {
					pa_include_city( $wp_analytify, $stats );
				}
			}
		}
		die();
	}

	/**
	 * Load default geographic stats for the dashboard
	 *
	 * @return void
	 */
	public static function load_default_geographic() {
		check_ajax_referer( 'analytify-get-dashboard-stats', 'nonce' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? $_GET['dashboard_profile_ID'] : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date             = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		// phpcs:enable

		// Countries.
		$countries_stats = $wp_analytify->get_reports(
			'show-geographic-countries-dashboard',
			array(
				'sessions',
				'country',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:country',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		// Cities.
		$cities_stats = $wp_analytify->get_reports(
			'show-geographic-cities-dashboard',
			array(
				'sessions',
				'city',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:city',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		$geographic_stats = array(
			'countries' => $countries_stats,
			'cities'    => $cities_stats,
		);

		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/geographic-stats.php';
		if ( function_exists( 'pa_include_geographic' ) ) {
			pa_include_geographic( $wp_analytify, $geographic_stats );
		}
		die();
	}
}
