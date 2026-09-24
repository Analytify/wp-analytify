<?php
/**
 * Analytify REST Endpoints General Trait
 *
 * This trait provides general analytics endpoints for the Analytify REST API.
 * It was created to separate general analytics functionality from the main REST class,
 * offering endpoints for common analytics reports like general stats, top pages,
 * geographic data, and system information.
 *
 * PURPOSE:
 * - Provides general analytics endpoints
 * - Handles common analytics data requests
 * - Manages general statistics processing
 * - Offers geographic and system analytics
 *
 * @package WP_Analytify
 * @subpackage REST_API
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

trait Analytify_Rest_Endpoints_General {

	/**
	 * Get general analytics statistics
	 *
	 * Retrieves and formats general analytics data including pageviews,
	 * sessions, users, and other key metrics for the specified date range.
	 * Sets up comparison dates and defines box descriptions for the dashboard.
	 *
	 * @return array<string, mixed> General analytics statistics with box and chart data
	 */
	private function general_stats() {
		$this->set_compare_dates();
		$boxes_description = array(
			'sessions'         => array(
				'title'       => esc_html__( 'Sessions', 'wp-analytify' ),
				'description' => esc_html__( 'A session is a time period in which a user is actively engaged with your website.', 'wp-analytify' ),
				'bottom'      => false,
				'number'      => 0,
			),
			'visitors'         => array(
				'title'       => esc_html__( 'Visitors', 'wp-analytify' ),
				'description' => esc_html__( 'Users who complete a minimum of one session on your website.', 'wp-analytify' ),
				'bottom'      => false,
				'number'      => 0,
			),
			'pageviews'        => array(
				'title'       => esc_html__( 'Page Views', 'wp-analytify' ),
				'description' => esc_html__( 'Page Views are the total number of Pageviews, these include repeated views.', 'wp-analytify' ),
				'bottom'      => false,
				'number'      => 0,
			),
			'avg_time_on_site' => array(
				'title'       => esc_html__( 'Avg. Time on Site', 'wp-analytify' ),
				'description' => esc_html__( 'Total time that a single user spends on your website.', 'wp-analytify' ),
				'bottom'      => false,
				'number'      => 0,
			),
			'bounce_rate'      => array(
				'title'       => esc_html__( 'Bounce Rate', 'wp-analytify' ),
				'description' => esc_html__( 'Percentage of single page visits (i.e number of visits in which a visitor leaves your website from the landing page without browsing your website).', 'wp-analytify' ),
				'append'      => '<span class="analytify_xl_f">%</span>',
				'bottom'      => false,
				'number'      => 0,
			),
			'pages_session'    => array(
				'title'       => esc_html__( 'Pages per Session', 'wp-analytify' ),
				'description' => esc_html__( 'Pages per Session is the number of pages viewed by a user during a single session. Repeated views are counted.', 'wp-analytify' ),
				'bottom'      => false,
				'number'      => 0,
			),
			'new_sessions'     => array(
				'title'       => esc_html__( '% New Sessions', 'wp-analytify' ),
				'description' => esc_html__( 'A new session is when a new user comes to your website.', 'wp-analytify' ),
				'append'      => '<span class="analytify_xl_f">%</span>',
				'bottom'      => false,
				'number'      => 0,
			),
			'engaged_sessions' => array(
				'title'       => esc_html__( 'Engaged Sessions', 'wp-analytify' ),
				'description' => esc_html__( 'The number of sessions that lasted longer than 10 seconds, or had a conversion event, or had 2 or more page views.', 'wp-analytify' ),
				'bottom'      => false,
				'number'      => 0,
			),
		);

		unset( $boxes_description['new_sessions'] );

		// Fetch main GA4 general stats data with newVsReturning dimension.
		$general_stats_raw = $this->wp_analytify->get_reports(
			'show-default-overall-dashboard',
			array(
				'sessions',
				'totalUsers',
				'screenPageViews',
				'averageSessionDuration',
				'bounceRate',
				'screenPageViewsPerSession',
				'engagedSessions',
				'userEngagementDuration',
				'activeUsers',
			),
			$this->get_dates(),
			array(
				'newVsReturning',
			)
		);

		// Device category stats.
		$device_category_stats = $this->wp_analytify->get_reports(
			'show-default-overall-device-dashboard',
			array( 'sessions' ),
			$this->get_dates(),
			array( 'deviceCategory' ),
			array(
				'type' => 'dimension',
				'name' => 'deviceCategory',
			)
		);

		// Browser breakdown stats.
		$browser_stats_raw = $this->wp_analytify->get_reports(
			'show-default-browser-breakdown-dashboard',
			array( 'sessions' ),
			$this->get_dates(),
			array( 'browser' ),
			array(
				'type'  => 'metric',
				'name'  => 'sessions',
				'order' => 'desc',
			),
			array(
				'logic'   => 'AND',
				'filters' => array(
					array(
						'type'           => 'dimension',
						'name'           => 'browser',
						'match_type'     => 4,
						'value'          => '(not set)',
						'not_expression' => true,
					),
				),
			),
			3
		);

		$general_stats = isset( $general_stats_raw['aggregations'] ) ? $general_stats_raw['aggregations'] : array();

		// Build boxes (main stats).
		$boxes_stats = array(
			'sessions'         => array(
				'raw'    => $general_stats['sessions'] ?? 0,
				'number' => WPANALYTIFY_Utils::pretty_numbers( $general_stats['sessions'] ?? 0 ),
			),
			'visitors'         => array(
				'raw'    => $general_stats['totalUsers'] ?? 0,
				'number' => WPANALYTIFY_Utils::pretty_numbers( $general_stats['totalUsers'] ?? 0 ),
			),
			'pageviews'        => array(
				'raw'    => $general_stats['screenPageViews'] ?? 0,
				'number' => WPANALYTIFY_Utils::pretty_numbers( $general_stats['screenPageViews'] ?? 0 ),
			),
			'avg_time_on_site' => array(
				'raw'    => $general_stats['averageSessionDuration'] ?? 0,
				'number' => WPANALYTIFY_Utils::pretty_time( $general_stats['averageSessionDuration'] ?? 0 ),
			),
			'bounce_rate'      => array(
				'raw'    => $general_stats['bounceRate'] ?? 0,
				'number' => WPANALYTIFY_Utils::fraction_to_percentage( $general_stats['bounceRate'] ?? 0 ),
			),
			'pages_session'    => array(
				'raw'    => $general_stats['screenPageViewsPerSession'] ?? 0,
				'number' => round( $general_stats['screenPageViewsPerSession'] ?? 0, 2 ),
			),
			'engaged_sessions' => array(
				'raw'    => $general_stats['engagedSessions'] ?? 0,
				'number' => WPANALYTIFY_Utils::pretty_numbers( $general_stats['engagedSessions'] ?? 0 ),
			),
		);

		// NEW vs RETURNING processing.
		$new_vs_returning_data = array(
			'new'       => array(
				'sessions' => 0,
				'users'    => 0,
			),
			'returning' => array(
				'sessions' => 0,
				'users'    => 0,
			),
			'unknown'   => array(
				'sessions' => 0,
				'users'    => 0,
			),
		);

		if ( isset( $general_stats_raw['rows'] ) && is_array( $general_stats_raw['rows'] ) ) {
			foreach ( $general_stats_raw['rows'] as $row ) {
				$type = strtolower( trim( $row['newVsReturning'] ?? '' ) );
				if ( 'new' === $type ) {
					$new_vs_returning_data['new']['sessions'] += (int) $row['sessions'];
					$new_vs_returning_data['new']['users']    += (int) $row['totalUsers'];
				} elseif ( 'returning' === $type ) {
					$new_vs_returning_data['returning']['sessions'] += (int) $row['sessions'];
					$new_vs_returning_data['returning']['users']    += (int) $row['totalUsers'];
				} else {
					$new_vs_returning_data['unknown']['sessions'] += (int) $row['sessions'];
					$new_vs_returning_data['unknown']['users']    += (int) $row['totalUsers'];
				}
			}
		}

		$chart_description['new_vs_returning_visitors'] = array(
			'title'  => __( 'New vs Returning Visitors', 'wp-analytify' ),
			'type'   => 'PIE',
			'stats'  => array(
				'new'       => array(
					'label'    => __( 'New', 'wp-analytify' ),
					'number'   => $new_vs_returning_data['new']['users'],
					'sessions' => WPANALYTIFY_Utils::pretty_numbers( $new_vs_returning_data['new']['sessions'] ),
				),
				'returning' => array(
					'label'    => __( 'Returning', 'wp-analytify' ),
					'number'   => $new_vs_returning_data['returning']['users'],
					'sessions' => WPANALYTIFY_Utils::pretty_numbers( $new_vs_returning_data['returning']['sessions'] ),
				),
			),
			'colors' => ( function () {
				$default_colors = array( '#03a1f8', '#00c853' );
				$filtered_colors = apply_filters( 'analytify_new_vs_returning_visitors_chart_colors', array() );
				return array_replace( $default_colors, $filtered_colors );
			} )(),
		);

		// Device category stats (mobile/tablet/desktop).
		$chart_description['visitor_devices'] = array(
			'title'  => esc_html__( 'Devices of Visitors', 'wp-analytify' ),
			'type'   => 'PIE',
			'stats'  => array(
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
			'colors' => ( function () {
				$default_colors = array( '#444444', '#ffbc00', '#ff5252' );
				$filtered_colors = apply_filters( 'analytify_visitor_devices_chart_colors', array() );
				return array_replace( $default_colors, $filtered_colors );
			} )(),
		);

		if ( isset( $device_category_stats['rows'] ) && is_array( $device_category_stats['rows'] ) ) {
			foreach ( $device_category_stats['rows'] as $device ) {
				if ( isset( $chart_description['visitor_devices']['stats'][ $device['deviceCategory'] ] ) ) {
					$chart_description['visitor_devices']['stats'][ $device['deviceCategory'] ]['number'] = $device['sessions'];
				}
			}
		}

		// Browser breakdown stats processing.
		$browser_breakdown_stats = array();
		$browser_colors          = array( '#4285F4', '#EA4335', '#FBBC04', '#34A853', '#FF6D00', '#46BDC6', '#7B1FA2', '#E91E63', '#00897B', '#5E35B1' );

		if ( isset( $browser_stats_raw['rows'] ) && is_array( $browser_stats_raw['rows'] ) ) {
			$color_index = 0;
			foreach ( $browser_stats_raw['rows'] as $browser ) {
				if ( isset( $browser['browser'] ) && isset( $browser['sessions'] ) ) {
					$browser_key                             = strtolower( str_replace( ' ', '_', $browser['browser'] ) );
					$browser_breakdown_stats[ $browser_key ] = array(
						'label'  => $browser['browser'],
						'number' => $browser['sessions'],
					);
					++$color_index;
				}
			}
		}

		$chart_description['browser_breakdown'] = array(
			'title'       => esc_html__( 'Browser Breakdown', 'wp-analytify' ),
			'description' => esc_html__( 'Top 3 Browsers by Sessions.', 'wp-analytify' ),
			'type'        => 'PIE',
			'stats'       => $browser_breakdown_stats,
			'colors'      => ( function () use ( $browser_colors ) {
				$filtered_colors = apply_filters( 'analytify_browser_breakdown_chart_colors', array() );
				return ! empty( $filtered_colors ) ? $filtered_colors : $browser_colors;
			} )(),
		);

		// Compare stats if comparison date is set.
		if ( $this->compare_start_date && $this->compare_end_date ) {
			$compare_stats_raw = $this->wp_analytify->get_reports(
				'show-default-overall-dashboard-compare',
				array(
					'sessions',
					'totalUsers',
					'screenPageViews',
					'averageSessionDuration',
					'bounceRate',
					'screenPageViewsPerSession',
					'engagedSessions',
				),
				array(
					'start' => $this->compare_start_date,
					'end'   => $this->compare_end_date,
				)
			);
		}

		if ( isset( $compare_stats_raw['aggregations'] ) ) {
			$compare_stats = array(
				'sessions'         => $compare_stats_raw['aggregations']['sessions'] ?? 0,
				'visitors'         => $compare_stats_raw['aggregations']['totalUsers'] ?? 0,
				'pageviews'        => $compare_stats_raw['aggregations']['screenPageViews'] ?? 0,
				'avg_time_on_site' => $compare_stats_raw['aggregations']['averageSessionDuration'] ?? 0,
				'bounce_rate'      => $compare_stats_raw['aggregations']['bounceRate'] ?? 0,
				'pages_session'    => $compare_stats_raw['aggregations']['screenPageViewsPerSession'] ?? 0,
				'engaged_sessions' => $compare_stats_raw['aggregations']['engagedSessions'] ?? 0,
			);
		}

		// Footer (optional).
		$footer_description = false;
		if ( isset( $general_stats['userEngagementDuration'] ) ) {
			$footer_description = apply_filters( 'analytify_general_stats_footer', $general_stats['userEngagementDuration'], array( $this->start_date, $this->end_date ) );
		}

		// Fill final box values and compare deltas.
		foreach ( $boxes_description as $key => $box ) {
			if ( isset( $boxes_stats[ $key ] ) ) {
				$boxes_description[ $key ]['number'] = (string) $boxes_stats[ $key ]['number'];
				if ( isset( $compare_stats[ $key ] ) ) {
					$boxes_description[ $key ]['bottom'] = $this->compare_stat( $boxes_stats[ $key ]['raw'], $compare_stats[ $key ], $key );
				}
			}
		}

		return array(
			'success' => true,
			'boxes'   => apply_filters( 'analytify_general_stats_boxes', $boxes_description, array( $this->start_date, $this->end_date ) ),
			'charts'  => apply_filters( 'analytify_general_stats_charts', $chart_description, array( $this->start_date, $this->end_date ) ),
			'footer'  => $footer_description,
		);
	}

	/**
	 * Get general stats footer.
	 *
	 * @param mixed $number The number value.
	 * @param mixed $data   The data parameter (unused).
	 * @return string
	 */
	public function general_stats_footer( $number, $data ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Parameter required by filter
		// translators: %s is the formatted time duration.
		return sprintf( __( 'Total time visitors spent on your site: %s.', 'wp-analytify' ), '<span class="analytify_red general_stats_message">' . WPANALYTIFY_Utils::pretty_time( $number ) . '</span>' );
	}

	/**
	 * Get profile info.
	 *
	 * @param string $key The key to get.
	 * @return mixed
	 */
	private function get_profile_info( $key ) {
		$dashboard_profile_id = $this->wp_analytify->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
		switch ( $key ) {
			case 'profile_id':
				return $dashboard_profile_id;
			case 'website_url':
				return WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_id, 'websiteUrl' );
			default:
				return null;
		}
	}

	/**
	 * Set compare dates.
	 *
	 * @return void
	 */
	private function set_compare_dates() {
		$date_diff = WPANALYTIFY_Utils::calculate_date_diff( $this->start_date, $this->end_date );
		if ( ! $date_diff ) {
			return; }
		$this->compare_start_date = $date_diff['start_date'];
		$this->compare_end_date   = $date_diff['end_date'];
		$this->compare_days       = $date_diff['diff_days'];
	}

	/**
	 * Compare stat.
	 *
	 * @param mixed  $current_stat The current stat.
	 * @param mixed  $old_stat     The old stat.
	 * @param string $type         The type.
	 * @return array|false
	 */
	private function compare_stat( $current_stat, $old_stat, $type ) {
		if ( is_null( $this->compare_start_date ) || is_null( $this->compare_end_date ) || is_null( $this->compare_days ) ) {
			return false; }
		if ( ! $old_stat || 0 === $old_stat ) {
			return false; }
		$number     = number_format( ( ( $current_stat - $old_stat ) / $old_stat ) * 100, 2 );
		$arrow_type = ( 'bounce_rate' === $type ) ? ( $number < 0 ? 'analytify_green_inverted' : 'analytify_red_inverted' ) : ( $number > 0 ? 'analytify_green' : 'analytify_red' );
		return array(
			'arrow_type' => $arrow_type,
			'main_text'  => $number . esc_html__( '%', 'wp-analytify' ),
			// translators: %s is the number of days.
			'sub_text'   => sprintf( esc_html__( '%s days ago', 'wp-analytify' ), $this->compare_days ),
		);
	}

	/**
	 * Get dates.
	 *
	 * @return array
	 */
	private function get_dates() {
		return array(
			'start' => $this->start_date,
			'end'   => $this->end_date,
		);
	}
}
