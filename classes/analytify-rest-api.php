<?php


/**
 * Rest API EndPoints
 */
class Analytify_Rest_API {


	function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Register End Point.
	 *
	 */
	function rest_api_init() {
		$namespace = 'wp-analytify/v1';

		register_rest_route(
			$namespace,
			'/get_report/(?P<profile_id>\d+)/(?P<request_type>[a-zA-Z0-9-]+)',
			array(
				array(
					'methods'  => WP_REST_Server::READABLE, // Get Request
					'callback' => array( $this, 'handle_request' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * Handle the Request.
	 *
	 * @since 2.1.23
	 */
	function handle_request( WP_REST_Request $request ) {
		$wp_analytify    = $GLOBALS['WP_ANALYTIFY'];
		$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard', array( 'Administrator' ) );


		// Generate error if unauthorized user send the request.
		if ( ! $wp_analytify->pa_check_roles( $is_access_level ) ) {
			return new WP_Error( 'analytify_forbidden', __( 'You are not allowed to access Analytify Dashboard.', 'wp-analytify' ), array( 'status' => 403 ) );
		}


		$dashboard_profile_ID = $request->get_param( 'profile_id' );
		$start_date           = $request->get_param( 'sd' );
		$end_date             = $request->get_param( 'ed' );
		$request_type         = $request->get_param( 'request_type' );


		if ( $request_type == 'what-happen' ) {
			return $this->get_what_happen_stats( $wp_analytify, $start_date, $end_date );
		} elseif ( $request_type == 'refferer' ) {
			return $this->get_refferer_stats( $wp_analytify, $start_date, $end_date );
		}

		// if no request type match. Return Error
		return new WP_Error( 'analytify_invalid_endpoint', __( 'Invalid endpoint.', 'wp-analytify' ), array( 'status' => 404 ) );

	}


  /**
   * Load Default Page Stats.
   *
   * @since 2.1.23
   */
	function get_what_happen_stats( $wp_analytify, $start_date, $end_date ) {

		$page_stats = $wp_analytify->pa_get_analytics_dashboard_via_rest( 'ga:entrances,ga:exits,ga:entranceRate,ga:exitRate', $start_date, $end_date, 'ga:pageTitle,ga:pagePath', '-ga:entrances', false, 5, 'show-default-what-happen' );

		if ( isset( $page_stats['api_error'] ) ) {
			return json_encode( array( 'body' => $page_stats['api_error'] ) );
		}

		if ( $page_stats ) {
			include ANALYTIFY_ROOT_PATH . '/views/default/admin/pages-stats.php';
			return fetch_pages_stats( $wp_analytify, $page_stats );
		}

	}


  /**
   * Load Refferer Stats.
   *
   * @since 2.1.23
   *
   */
	function get_refferer_stats( $wp_analytify, $start_date, $end_date ) {

		$referr_stats = $wp_analytify->pa_get_analytics_dashboard_via_rest( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 7, 'show-default-refferer' );

		if ( isset( $referr_stats['api_error'] ) ) {
			return json_encode( array( 'body' => $referr_stats['api_error'] ) );
		}

		if ( $referr_stats ) {
			include ANALYTIFY_ROOT_PATH . '/views/default/admin/referrers-stats.php';
			return fetch_referrers_stats( $wp_analytify, $referr_stats, true );
		}
	}

}

new Analytify_Rest_API();
