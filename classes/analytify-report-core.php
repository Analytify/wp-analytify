<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Generates and returns reports.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File name follows project convention
/**
 * Report class.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class Analytify_Report extends Analytify_Report_Abstract {

	/**
	 * Hold numbers of general stats only.
	 * Can be used for generating footers.
	 *
	 * @var mixed
	 */
	private $general_stats_num = null;

	/**
	 * Undocumented function
	 *
	 * @return array<string, mixed>
	 */
	public function get_general_stats(): array {

		$cache_key        = $this->cache_key( 'general-stats' );
		$device_cache_key = $this->cache_key( 'device-stats' );

		return $this->general_stats_ga4( $cache_key, $device_cache_key );
	}

	/**
	 * Generates browser stats - GA4.
	 *
	 * @version 8.2.0
	 * @param string $cache_key Cache key.
	 * @param string $device_cache_key Device cache key.
	 * @return array<string, mixed>
	 */
	protected function general_stats_ga4( $cache_key, $device_cache_key ): array {

		$boxes                 = $this->general_stats_boxes();
		$send_email_total_time = $this->total_time_for_send_email();
		unset( $boxes['new_sessions'] );
		$new_vs_returning_boxes = $this->new_vs_returning();
		$device_visitors_boxes  = $this->visitor_devices();
		$dimensions             = array();
		$device_dimensions      = array(
			'deviceCategory',
		);
		$filters                = array();
		$raw                    = $this->wp_analytify && method_exists( $this->wp_analytify, 'get_reports' ) ? $this->wp_analytify->get_reports(
			$cache_key,
			array(
				'sessions',
				'totalUsers',
				'screenPageViews',
				'bounceRate',
				'screenPageViewsPerSession',
				'engagedSessions',
				'userEngagementDuration',
				'newUsers',
				'activeUsers',
				'averageSessionDuration',
			),
			$this->get_dates(),
			$this->attach_post_url_dimension( $dimensions ),
			array(),
			array(
				'logic'   => 'AND',
				'filters' => $this->attach_post_url_filter( $filters ),
			)
		) : array();
		if ( isset( $raw['aggregations']['sessions'] ) ) {
			$boxes['sessions']['value']    = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['sessions'] );
			$general_stats_num['sessions'] = $raw['aggregations']['sessions'];
		}
		if ( isset( $raw['aggregations']['totalUsers'] ) ) {
			$boxes['visitors']['value']    = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['totalUsers'] );
			$general_stats_num['visitors'] = $raw['aggregations']['totalUsers'];
		}
		if ( isset( $raw['aggregations']['screenPageViews'] ) ) {
			$boxes['page_views']['value']    = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['screenPageViews'] );
			$general_stats_num['page_views'] = $raw['aggregations']['screenPageViews'];
		}
		if ( isset( $raw['aggregations']['averageSessionDuration'] ) ) {
			$boxes['avg_time_on_page']['value']    = WPANALYTIFY_Utils::pretty_time( $raw['aggregations']['averageSessionDuration'] );
			$general_stats_num['avg_time_on_page'] = $raw['aggregations']['averageSessionDuration'];
		}
		if ( isset( $raw['aggregations']['bounceRate'] ) ) {
			$boxes['bounce_rate']['value']    = WPANALYTIFY_Utils::fraction_to_percentage( $raw['aggregations']['bounceRate'] );
			$general_stats_num['bounce_rate'] = $raw['aggregations']['bounceRate'];
		}
		if ( isset( $raw['aggregations']['screenPageViewsPerSession'] ) ) {
			$boxes['view_per_session']['value']    = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['screenPageViewsPerSession'] );
			$general_stats_num['view_per_session'] = $raw['aggregations']['screenPageViewsPerSession'];
		}

		if ( isset( $raw['aggregations']['engagedSessions'] ) ) {
			$boxes['engaged_sessions']['value']    = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['engagedSessions'] );
			$general_stats_num['engaged_sessions'] = $raw['aggregations']['engagedSessions'];
		}

		if ( isset( $raw['aggregations']['newUsers'] ) ) {
			$new_vs_returning_boxes['new_vs_returning_visitors']['stats']['new']['number'] = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['newUsers'] );
		}
		if ( isset( $raw['aggregations']['activeUsers'] ) ) {
			$new_vs_returning_boxes['new_vs_returning_visitors']['stats']['returning']['number'] = WPANALYTIFY_Utils::pretty_numbers( $raw['aggregations']['activeUsers'] );
		}

		$device_stats = $this->wp_analytify && method_exists( $this->wp_analytify, 'get_reports' ) ? $this->wp_analytify->get_reports(
			$device_cache_key,
			array( 'sessions' ),
			$this->get_dates(),
			$this->attach_post_url_dimension( $device_dimensions ),
			array(),
			array(
				'logic'   => 'AND',
				'filters' => $this->attach_post_url_filter( $filters ),
			)
		) : array();
		if ( isset( $device_stats['rows'] ) && $device_stats['rows'] ) {
			foreach ( $device_stats['rows'] as $device ) {
				$device_visitors_boxes['visitor_devices']['stats'][ $device['deviceCategory'] ]['number'] = $device['sessions'];
			}
		}

		if ( isset( $raw['aggregations']['userEngagementDuration'] ) ) {
			$send_email_total_time['total_time']['value'] = WPANALYTIFY_Utils::pretty_time( $raw['aggregations']['userEngagementDuration'] );
		}

		return array(
			'boxes'                  => $boxes,
			'new_vs_returning_boxes' => $new_vs_returning_boxes,
			'device_visitors_boxes'  => $device_visitors_boxes,
			'total_time_spent'       => $send_email_total_time,
		);
	}

	/**
	 * Returns the simple stats for general stats.
	 * This is intended to be used for the footer or in some calculation.
	 *
	 * @return array<string, mixed>|null
	 */
	public function get_general_stats_num() {
		return $this->general_stats_num;
	}

	/**
	 * Returns scroll depth stats.
	 *
	 * @return array<string, mixed>
	 */
	public function get_scroll_depth_stats(): array {

		$cache_key = $this->cache_key( 'scroll-depth' );

		if ( $this->is_ga4 ) {
			return $this->scroll_depth_ga4( $cache_key );
		}

		return array();
	}

	/**
	 * Generates scroll depth stats - GA4.
	 *
	 * @param string $cache_key Cache key.
	 * @return array<string, mixed>
	 */
	protected function scroll_depth_ga4( $cache_key ): array {

		$stats = array();

		$dimensions = array(
			'customEvent:wpa_category',
			'customEvent:wpa_percentage',
		);
		$filters    = array(
			array(
				'type'       => 'dimension',
				'name'       => 'customEvent:wpa_category',
				'match_type' => 1,
				'value'      => 'Analytify Scroll Depth',
			),
			array(
				'type'           => 'dimension',
				'name'           => 'customEvent:wpa_percentage',
				'match_type'     => 4,
				'value'          => '(not set)',
				'not_expression' => true,
			),
		);

		$raw = $this->wp_analytify && method_exists( $this->wp_analytify, 'get_reports' ) ? $this->wp_analytify->get_reports(
			$cache_key,
			array(
				'eventCount',
			),
			$this->get_dates(),
			$this->attach_post_url_dimension( $dimensions ),
			array(),
			array(
				'logic'   => 'AND',
				'filters' => $this->attach_post_url_filter( $filters ),
			)
		) : array();

		$total = 1;
		if ( isset( $raw['aggregations']['eventCount'] ) && $raw['aggregations']['eventCount'] > 0 ) {
			$total = $raw['aggregations']['eventCount'];
		}

		if ( isset( $raw['rows'] ) && $raw['rows'] ) {
			foreach ( $raw['rows'] as $row ) {
				if ( 'csv' === $this->dashboard_type ) {
					$single_stat['percentage'] = $row['customEvent:wpa_percentage'] . esc_html__( '%', 'wp-analytify' );
				} else {
					$bar = is_numeric( $row['eventCount'] ) ? round( ( $row['eventCount'] / $total ) * 100 ) : 0;

					$single_stat['percentage']  = esc_html( $row['customEvent:wpa_percentage'] ) . esc_html__( '%', 'wp-analytify' );
					$single_stat['percentage'] .= '<span class="analytify_bar_graph"><span style="width:' . $bar . '%"></span></span>';
				}
				$single_stat['events'] = esc_html( $row['eventCount'] );

				$stats[] = $single_stat;
			}
		}

		return array(
			'stats' => $stats,
		);
	}
}
