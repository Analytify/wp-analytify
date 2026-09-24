<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure
/**
 * Analytify Report Abstract Class
 *
 * This abstract class provides the base functionality for generating and returning
 * analytics reports in the Analytify plugin.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Analytify_Report_Abstract' ) ) {
	/**
	 * Generates and returns reports
	 */
	abstract class Analytify_Report_Abstract {

		/**
		 * The main Analytify object.
		 *
		 * @var object
		 */
		protected $wp_analytify;

		/**
		 * Is reporting GA4 or not?
		 *
		 * @var boolean
		 */
		protected $is_ga4;

		/**
		 * Selected 'start state'.
		 *
		 * @var string
		 */
		protected $start_date;

		/**
		 * Selected 'End state'.
		 *
		 * @var string
		 */
		protected $end_date;

		/**
		 * Post ID.
		 *
		 * @var int
		 */
		protected $post_id;

		/**
		 * Post URL without the domain, with query string.
		 *
		 * @var string
		 */
		protected $post_url;

		/**
		 * Type of report.
		 * Can be 'dashboard', 'csv', 'single_post', 'email'.
		 *
		 * @var string
		 */
		protected $dashboard_type;

		/**
		 * Class constructor.
		 *
		 * @version 7.0.5
		 * @param array<string, mixed> $args Arguments.
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			$this->is_ga4       = class_exists( 'WPANALYTIFY_Utils' ) ? 'ga4' === WPANALYTIFY_Utils::get_ga_mode() : false;

			/**
			 * Setting default args.
			 */
			// Dashboard type - Can be 'dashboard', 'csv', 'single_post', 'email'.
			$this->dashboard_type = isset( $args['dashboard_type'] ) && in_array( $args['dashboard_type'], array( 'dashboard', 'csv', 'single_post', 'email' ), true ) ? $args['dashboard_type'] : 'dashboard';

			// Dates.
			$this->start_date = isset( $args['start_date'] ) ? $args['start_date'] : wp_date( 'Y-m-d', strtotime( '-30 days', current_time( 'timestamp' ) ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- current_time is acceptable for date calculations
			$this->end_date   = isset( $args['end_date'] ) ? $args['end_date'] : wp_date( 'Y-m-d', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- current_time is acceptable for date calculations

			// Post ID and URL.
			if ( 'single_post' === $this->dashboard_type ) {
				$this->post_id = isset( $args['post_id'] ) ? $args['post_id'] : null;

				// URL.
				$permalink = get_permalink( $this->post_id );

				$this->post_url = apply_filters( 'analytify_single_post_stats_url', $permalink, $this->post_id );
			}
		}

		/**
		 * Text holder for general stat boxes.
		 *
		 * @version 8.2.0
		 * @return array<string, mixed>
		 */
		protected function general_stats_boxes() {
			return array(
				'sessions'         => array(
					'title'       => esc_html__( 'Sessions', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'A session is a time period in which a user is actively engaged with your website.', 'wp-analytify' ),
					'append'      => false,
				),
				'visitors'         => array(
					'title'       => esc_html__( 'Visitors', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'Users who complete a minimum of one session on your website.', 'wp-analytify' ),
					'append'      => false,
				),
				'page_views'       => array(
					'title'       => esc_html__( 'Page Views', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'Total number of Page Views, these include repeated views.', 'wp-analytify' ),
					'append'      => false,
				),
				'avg_time_on_page' => array(
					'title'       => esc_html__( 'Avg. Time on Page', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'Total time that a single user spends on your website.', 'wp-analytify' ),
					'append'      => false,
				),
				'bounce_rate'      => array(
					'title'       => esc_html__( 'Bounce Rate', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'Percentage of single page visits (i.e number of visits in which a visitor leaves your website from the landing page without browsing your website).', 'wp-analytify' ),
					'append'      => '<span class="analytify_xl_f">%</span>',
				),
				'new_sessions'     => array(
					'title'       => esc_html__( '% New sessions', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'A new session is a time period when a new user comes to your website and is actively engaged with your website.', 'wp-analytify' ),
					'append'      => '<span class="analytify_xl_f">%</span>',
				),
				'view_per_session' => array(
					'title'       => esc_html__( 'Pages / Session', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'Number of page views by a user during a single session. Repeated views are counted.', 'wp-analytify' ),
					'append'      => false,
				),
				'engaged_sessions' => array(
					'title'       => esc_html__( 'Engaged Sessions', 'wp-analytify' ),
					'value'       => '0',
					'description' => esc_html__( 'The number of sessions that lasted longer than 10 seconds, or had a conversion event, or had 2 or more page views.', 'wp-analytify' ),
					'append'      => false,
				),
			);
		}

		/**
		 * Get new vs returning visitors data
		 *
		 * @version 7.0.5
		 * @return array<string, mixed>
		 */
		protected function new_vs_returning() {
			return array(
				'new_vs_returning_visitors' => array(
					'title' => esc_html__( 'New vs Returning Visitors', 'wp-analytify' ),
					'stats' => array(
						'new'       => array(
							'label'  => esc_html__( 'New', 'wp-analytify' ),
							'number' => 0,
						),
						'returning' => array(
							'label'  => esc_html__( 'Returning', 'wp-analytify' ),
							'number' => 0,
						),
					),
				),
			);
		}
		/**
		 * Get visitor devices data
		 *
		 * @version 7.0.5
		 * @return array<string, mixed>
		 */
		protected function visitor_devices() {
			return array(
				'visitor_devices' => array(
					'title' => esc_html__( 'Devices of Visitors', 'wp-analytify' ),
					'stats' => array(
						'mobile'  => array(
							'label'  => esc_html__( 'Mobile', 'wp-analytify' ),
							'number' => 0,
						),
						'tablet'  => array(
							'label'  => esc_html__( 'Tablet', 'wp-analytify' ),
							'number' => 0,
						),
						'desktop' => array(
							'label'  => esc_html__( 'Desktop', 'wp-analytify' ),
							'number' => 0,
						),
					),
				),
			);
		}

		/**
		 * Get total time for email sending
		 *
		 * @version 7.0.5
		 * @return array<string, mixed>
		 */
		protected function total_time_for_send_email() {
			return array(
				'total_time' => array(
					'title'       => esc_html__( 'Total Time Spent', 'wp-analytify' ),
					'value'       => '0',
					'description' => '',
					'append'      => false,
				),
			);
		}

		/**
		 * Attaches post URL dimension.
		 * Only if the dashboard type is 'single_post'.
		 *
		 * @version 7.0.5
		 * @param array<string, mixed> $dimensions Dimensions.
		 * @return array<string, mixed>
		 */
		protected function attach_post_url_dimension( $dimensions = array() ) {
			if ( 'single_post' === $this->dashboard_type ) {
				$dimensions[] = 'pagePath';
			}
			// Convert to associative array with string keys.
			$result = array();
			foreach ( $dimensions as $key => $value ) {
				$result[ is_string( $key ) ? $key : (string) $key ] = $value;
			}
			return $result;
		}

		/**
		 * Attaches post URL filter to filters array.
		 * Only if the dashboard type is 'single_post'.
		 *
		 * @version 7.0.5
		 * @param array<string, mixed> $filters Filters.
		 * @return array<string, mixed>
		 */
		protected function attach_post_url_filter( $filters = array() ) {
			$link   = apply_filters( 'analytify_sinlge_stats_permalink', $this->post_url );
			$link   = is_string( $link ) ? $link : '';
			$u_post = wp_parse_url( urldecode( $link ) );
			$filter = is_array( $u_post ) && isset( $u_post['path'] ) ? $u_post['path'] : '';
			// change the page poth filter for site that use domain mapping.
			$filter = apply_filters( 'analytify_page_path_filter', $filter, $u_post );

			// Url have query string incase of WPML.
			if ( isset( $u_post['query'] ) ) {
				$filter .= '?' . $u_post['query'];
			}

			if ( 'single_post' === $this->dashboard_type ) {
				$filters[] = array(
					'type'       => 'dimension',
					'name'       => 'pagePath',
					'match_type' => 1,
					'value'      => $filter,
				);
				$filters[] = array(
					'type'           => 'dimension',
					'name'           => 'pagePath',
					'match_type'     => 4,
					'value'          => '(not set)',
					'not_expression' => true,
				);
			}
			return $filters;
		}

		/**
		 * Generates the cache key based on what type of dashboard is being displayed.
		 *
		 * @version 7.0.5
		 * @param string $key Cache Key.
		 * @return string
		 */
		protected function cache_key( $key ) {

			switch ( $this->dashboard_type ) {
				case 'single_post':
					$key = $key . '-' . $this->post_id;
					break;
				case 'csv':
					$key = $key . '-csv';
					break;
				default:
					break;
			}

			return $key;
		}

		/**
		 * Removes keys from sub-arrays.
		 *
		 * @version 7.0.5
		 * @param array<string, mixed> $stats Stats.
		 * @return array<string, mixed>
		 */
		protected function strip_child_keys( $stats ) {
			$result = array();
			foreach ( $stats as $key => $item ) {
				if ( is_array( $item ) ) {
					$result[ $key ] = array_values( $item );
				} else {
					$result[ $key ] = $item;
				}
			}
			return $result;
		}

		/**
		 * Returns start and end date as an array to be used for GA4's get_reports()
		 *
		 * @version 7.0.5
		 * @return array<string, mixed>
		 */
		protected function get_dates() {
			return array(
				'start' => $this->start_date,
				'end'   => $this->end_date,
			);
		}

		/**
		 * Get profile related data based on the key (option) provided.
		 *
		 * @version 7.0.5
		 * @param string $key Option name.
		 * @return string|null
		 */
		protected function get_profile_info( $key ) {
			$dashboard_profile_id = $this->wp_analytify && isset( $this->wp_analytify->settings ) ? $this->wp_analytify->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' ) : '';
			switch ( $key ) {
				case 'profile_id':
					return $dashboard_profile_id;
				case 'website_url':
					return WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_id, 'websiteUrl' );
				default:
					return null;
			}
		}
	}
}
