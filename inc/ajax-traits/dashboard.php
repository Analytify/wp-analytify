<?php
/**
 * Dashboard AJAX Trait for WP Analytify
 *
 * @package WP_Analytify
 */

/**
 * Dashboard AJAX Trait.
 *
 * This trait contains dashboard-related AJAX functionality that was previously
 * in the WPANALYTIFY_AJAX class. It handles dashboard stats, top pages, and
 * general analytics data loading.
 *
 * @since 8.0.0
 */
trait Analytify_AJAX_Dashboard {

	/**
	 * Load general stats for the dashboard
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function load_general_stats() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-overall-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-overall-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/general-stats.php';
				if ( function_exists( 'pa_include_general' ) ) {
					pa_include_general( $wp_analytify, $stats );
				}
			}
		}

		die();
	}

	/**
	 * Fetch general stats for the dashboard.
	 *
	 * @return void
	 */
	public static function load_default_general_stats() {

		check_ajax_referer( 'analytify-get-dashboard-stats', 'nonce' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Nonce verified above.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		// Main general stats.
		$stats = $wp_analytify->get_reports(
			'show-default-overall-dashboard',
			array(
				'sessions',
				'totalUsers',
				'screenPageViews',
				'averageSessionDuration',
				'bounceRate',
				'screenPageViewsPerSession',
				'newUsers',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			)
		);

		// New users.
		$new_users_stats = $wp_analytify->get_reports(
			'show-default-new-returning-dashboard',
			array(
				'newUsers',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			)
		);

		// Returning users.
		$returning_users_stats = $wp_analytify->get_reports(
			'show-default-new-returning-dashboard',
			array(
				'activeUsers',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			)
		);

		$new_returning_stats = array(
			'new_users'       => $new_users_stats,
			'returning_users' => $returning_users_stats,
		);

		// Device category.
		$device_category_stats = $wp_analytify->get_reports(
			'show-default-overall-device-dashboard',
			array(
				'sessions',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			)
		);

		// Include the view.
		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/general-stats.php';
		if ( function_exists( 'pa_include_general' ) ) {
			pa_include_general( $wp_analytify, $stats );
		}
		die();
	}

	/**
	 * Load top pages for the dashboard
	 *
	 * @return void
	 */
	public static function load_top_pages() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-top-pages-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-top-pages-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/top-pages.php';
				if ( function_exists( 'pa_include_top_pages' ) ) {
					pa_include_top_pages( $wp_analytify, $stats );
				}
			}
		}
		die();
	}

	/**
	 * Load default top pages for the dashboard
	 *
	 * @return void
	 */
	public static function load_default_top_pages() {
		check_ajax_referer( 'analytify-get-dashboard-stats', 'nonce' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? $_GET['dashboard_profile_ID'] : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date             = isset( $_GET['end_date'] ) ? $_GET['end_date'] : '';
		// phpcs:enable

		$stats = $wp_analytify->get_reports(
			'show-default-top-pages-dashboard',
			array(
				'screenPageViews',
				'pagePath',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:pagePath',
				'sort'        => '-ga:screenPageViews',
				'max-results' => 10,
			)
		);

		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/top-pages.php';
		if ( function_exists( 'pa_include_top_pages' ) ) {
			pa_include_top_pages( $wp_analytify, $stats );
		}
		die();
	}
}
