<?php
/**
 * System AJAX Trait for WP Analytify
 *
 * @package WP_Analytify
 */

/**
 * System AJAX Trait.
 *
 * This trait contains system-related AJAX functionality that was previously
 * in the WPANALYTIFY_AJAX class. It handles browser, OS, mobile, device,
 * and other system analytics data loading.
 *
 * @since 8.0.0
 */
trait Analytify_AJAX_System {

	/**
	 * Load default system stats for the dashboard
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function load_default_system() {
		check_ajax_referer( 'analytify-get-dashboard-stats', 'nonce' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Nonce verified above.
		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = isset( $_GET['dashboard_profile_ID'] ) ? sanitize_text_field( wp_unslash( $_GET['dashboard_profile_ID'] ) ) : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$start_date           = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date             = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';
		// phpcs:enable

		// Browser stats.
		$browser_stats = $wp_analytify->get_reports(
			'show-default-browser-dashboard',
			array(
				'sessions',
				'browser',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:browser',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		// OS stats.
		$os_stats = $wp_analytify->get_reports(
			'show-default-os-dashboard',
			array(
				'sessions',
				'operatingSystem',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:operatingSystem',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		// Mobile stats.
		$mobile_stats = $wp_analytify->get_reports(
			'show-default-mobile-dashboard',
			array(
				'sessions',
				'deviceCategory',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:deviceCategory',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		// Referrer stats.
		$referrer_stats = $wp_analytify->get_reports(
			'show-default-reffers-dashboard',
			array(
				'sessions',
				'source',
			),
			array(
				'start' => $start_date,
				'end'   => $end_date,
			),
			array(
				'dimensions'  => 'ga:source',
				'sort'        => '-ga:sessions',
				'max-results' => 10,
			)
		);

		$system_stats = array(
			'browser'  => $browser_stats,
			'os'       => $os_stats,
			'mobile'   => $mobile_stats,
			'referrer' => $referrer_stats,
		);

		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/old/admin/system-stats.php';
		if ( function_exists( 'pa_include_system' ) ) {
			pa_include_system( $wp_analytify, $system_stats );
		}
		die();
	}
}
