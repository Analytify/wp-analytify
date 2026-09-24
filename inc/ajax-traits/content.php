<?php
/**
 * Content AJAX Trait for WP Analytify
 *
 * @package WP_Analytify
 */

/**
 * Content AJAX Trait.
 *
 * This trait contains content-related AJAX functionality that was previously
 * in the WPANALYTIFY_AJAX class. It handles keywords, social media, page exit,
 * and other content analytics data loading.
 *
 * @since 8.0.0
 */
trait Analytify_AJAX_Content {

	/**
	 * Load keyword stats for the dashboard
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function load_keyword_stats() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-keywords-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-keywords-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-keywords-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/keywords-stats.php';
				if ( function_exists( 'pa_include_keywords' ) ) {
					pa_include_keywords( $wp_analytify, $stats );
				}
			}
		}
		die();
	}

	/**
	 * Load social stats for the dashboard
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function load_social_stats() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_start_date'] ) ) : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['compare_end_date'] ) ) : '';
		$date_different     = isset( $_GET['date_different'] ) ? sanitize_text_field( wp_unslash( $_GET['date_different'] ) ) : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-social-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-social-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/social-stats.php';
				if ( function_exists( 'pa_include_social' ) ) {
					pa_include_social( $wp_analytify, $stats );
				}
			}
		}
		die();
	}

	/**
	 * Load page exit stats for the dashboard
	 *
	 * @return void
	 */
	public static function load_page_exit_stats() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- AJAX handler with nonce checked upstream.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? $_GET['dashboard_profile_ID'] : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date             = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';

		$compare_start_date = isset( $_GET['compare_start_date'] ) ? $_GET['compare_start_date'] : '';
		$compare_end_date   = isset( $_GET['compare_end_date'] ) ? $_GET['compare_end_date'] : '';
		$date_different     = isset( $_GET['date_different'] ) ? $_GET['date_different'] : '';
		// phpcs:enable

		if ( is_array( self::$show_settings ) && in_array( 'show-page-stats-dashboard', self::$show_settings, true ) ) {

			$stats = get_transient( md5( 'show-page-stats-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Get prev stats.
			$compare_stats = get_transient( md5( 'show-page-stats-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			if ( isset( $stats->totalsForAllResults ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/page-stats.php';
				if ( function_exists( 'pa_include_page_stats' ) ) {
					pa_include_page_stats( $wp_analytify, $stats, $compare_stats, $date_different );
				}
			}
		}
		die();
	}

	/**
	 * Load default keyword stats for the dashboard
	 *
	 * @return void
	 */
	public static function load_default_keyword() {
		check_ajax_referer( 'analytify-get-dashboard-stats', 'nonce' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? $_GET['dashboard_profile_ID'] : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date             = isset( $_GET['end_date'] ) ? $_GET['end_date'] : '';
		// phpcs:enable

		$stats = $wp_analytify->get_reports(
			'show-default-keyword-dashboard',
			array(
				'sessions',
				'keyword',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:keyword',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/keywords-stats.php';
		if ( function_exists( 'pa_include_keywords' ) ) {
			pa_include_keywords( $wp_analytify, $stats );
		}
		die();
	}

	/**
	 * Load default social media stats for the dashboard
	 *
	 * @return void
	 */
	public static function load_default_social_media() {
		check_ajax_referer( 'analytify-get-dashboard-stats', 'nonce' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified above.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? $_GET['dashboard_profile_ID'] : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date             = isset( $_GET['end_date'] ) ? $_GET['end_date'] : '';
		// phpcs:enable

		$stats = $wp_analytify->get_reports(
			'show-default-social-dashboard',
			array(
				'sessions',
				'socialNetwork',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:socialNetwork',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/social-stats.php';
		if ( function_exists( 'pa_include_social' ) ) {
			pa_include_social( $wp_analytify, $stats );
		}
		die();
	}
}
