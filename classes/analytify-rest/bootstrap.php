<?php
/**
 * Analytify REST Bootstrap Trait
 *
 * This trait contains the REST API bootstrap functionality for the Analytify plugin.
 * It handles REST API initialization, request handling, and permission checking.
 *
 * @package WP_Analytify
 * @subpackage REST
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

trait Analytify_Rest_Bootstrap {
	/**
	 * Instance of the class.
	 *
	 * @var self|null
	 */
	private static $instance;

	/**
	 * WP Analytify instance.
	 *
	 * @var mixed
	 */
	private $wp_analytify;

	/**
	 * Google Analytics mode.
	 *
	 * @var string
	 */
	private $ga_mode;

	/**
	 * Start date for analytics.
	 *
	 * @var string
	 */
	private $start_date;

	/**
	 * End date for analytics.
	 *
	 * @var string
	 */
	private $end_date;

	/**
	 * Date difference in days.
	 *
	 * @var int
	 */
	private $date_differ;

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	private $post_id;

	/**
	 * Compare start date.
	 *
	 * @var string|null
	 */
	private $compare_start_date = null;

	/**
	 * Compare end date.
	 *
	 * @var string|null
	 */
	private $compare_end_date = null;

	/**
	 * Compare days.
	 *
	 * @var int|null
	 */
	private $compare_days = null;

	/**
	 * Get instance of the class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'analytify_rest_api_init' ) );
		add_filter( 'analytify_general_stats_footer', array( $this, 'general_stats_footer' ), 10, 2 );
		add_filter( 'analytify_single_post_sections', array( $this, 'single_post_sections' ), 10, 3 );
	}

	/**
	 * Initialize REST API.
	 *
	 * @return void
	 */
	public function analytify_rest_api_init() {
		$this->wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$this->ga_mode      = WPANALYTIFY_Utils::get_ga_mode();
		register_rest_route(
			'wp-analytify/v1',
			'/get_report/(?P<request_type>[a-zA-Z0-9-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_request' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
			)
		);
	}

	/**
	 * Check permissions for REST API requests.
	 *
	 * @param WP_REST_Request $data The REST request object.
	 * @return bool
	 */
	public function permission_check( $data ) {
		$route = $data->get_route();
		if ( strpos( $route, 'single-post-stats' ) !== false ) {
			$is_access_level = $this->wp_analytify->settings->get_option( 'show_analytics_roles_back_end', 'wp-analytify-admin', array( 'administrator' ) );
			return (bool) $this->wp_analytify->pa_check_roles( $is_access_level );
		}
		$is_access_level = $this->wp_analytify->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard', array( 'administrator' ) );
		return (bool) $this->wp_analytify->pa_check_roles( $is_access_level );
	}

	/**
	 * Handle REST API requests.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_request( WP_REST_Request $request ) {
		$request_type      = $request->get_param( 'request_type' );
		$this->start_date  = $request->get_param( 'sd' ) ? $request->get_param( 'sd' ) : wp_date( 'Y-m-d', strtotime( '-30 days', current_time( 'timestamp' ) ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- current_time is acceptable for date calculations
		$this->end_date    = $request->get_param( 'ed' ) ? $request->get_param( 'ed' ) : wp_date( 'Y-m-d', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- current_time is acceptable for date calculations
		$this->date_differ = $request->get_param( 'd_diff' );
		$this->post_id     = $request->get_param( 'post_id' );
		switch ( $request_type ) {
			case 'general-stats':
				return new WP_REST_Response( $this->general_stats() );
			case 'top-pages-stats':
				return new WP_REST_Response( $this->top_pages_stats() );
			case 'geographic-stats':
				return new WP_REST_Response( $this->geographic_stats() );
			case 'system-stats':
				return new WP_REST_Response( $this->system_stats() );
			case 'keyword-stats':
				return new WP_REST_Response( $this->keyword_stats() );
			case 'social-stats':
				return new WP_REST_Response( $this->social_stats() );
			case 'referer-stats':
				return new WP_REST_Response( $this->get_referer_stats() );
			case 'what-is-happening-stats':
				return new WP_REST_Response( $this->get_what_is_happening_stats() );
			case 'single-post-stats':
				return new WP_REST_Response( $this->get_single_post_stats() );
		}
		return new WP_Error( 'analytify_invalid_endpoint', esc_html__( 'Invalid endpoint.', 'wp-analytify' ), array( 'status' => 404 ) );
	}
}
