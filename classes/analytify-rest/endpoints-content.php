<?php
/**
 * Analytify REST Endpoints Content Trait
 *
 * This trait provides content-specific analytics endpoints for the Analytify REST API.
 * It was created to separate content analytics functionality from the main REST class,
 * offering endpoints for single post statistics and content performance analysis.
 *
 * PURPOSE:
 * - Provides content analytics endpoints
 * - Handles single post statistics
 * - Manages content performance metrics
 * - Offers content engagement analysis
 *
 * @package WP_Analytify
 * @subpackage REST_API
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

trait Analytify_Rest_Endpoints_Content {

	/**
	 * Get top pages statistics
	 *
	 * Retrieves analytics data for the most visited pages on the website,
	 * including pageviews, unique pageviews, and bounce rates.
	 *
	 * @version 9.1.1
	 * @return array<string, mixed> Top pages statistics with performance metrics
	 */
	private function top_pages_stats() {
		$api_limit = apply_filters( 'analytify_api_limit_top_pages_stats', 100, 'dashboard' );
		$site_url  = $this->get_profile_info( 'website_url' );
		if ( empty( $site_url ) ) {
			$site_url = home_url();
		}
		$site_url  = untrailingslashit( (string) $site_url );
		$stats     = array();
		$stats_raw = $this->wp_analytify->get_reports(
			'show-default-top-pages-dashboard',
			array( 'screenPageViews', 'averageSessionDuration', 'bounceRate' ),
			$this->get_dates(),
			array( 'pageTitle', 'pagePath' ),
			array(
				'type'  => 'metric',
				'name'  => 'screenPageViews',
				'order' => 'desc',
			),
			array(
				'logic'   => 'AND',
				'filters' => array(
					array(
						'type'           => 'dimension',
						'name'           => 'pageTitle',
						'match_type'     => 4,
						'value'          => '(not set)',
						'not_expression' => true,
					),
					array(
						'type'           => 'dimension',
						'name'           => 'pagePath',
						'match_type'     => 4,
						'value'          => '(not set)',
						'not_expression' => true,
					),
				),
			),
			$api_limit
		);
		if ( $stats_raw['rows'] ) {
			foreach ( $stats_raw['rows'] as $row ) {
				$views = $row['screenPageViews'] ? WPANALYTIFY_Utils::pretty_numbers( $row['screenPageViews'] ) : 0;
				if ( $views < 1 ) {
					continue; }
				$page_path = isset( $row['pagePath'] ) ? (string) $row['pagePath'] : '';
				if ( '' !== $page_path && '/' !== $page_path[0] ) {
					$page_path = '/' . $page_path;
				}
				$page_url = esc_url( $site_url . $page_path );
				$stats[]  = array(
					'no'                     => null,
					'pageTitle'              => '<a href="' . $page_url . '" target="_blank" rel="noopener noreferrer">' . esc_html( $row['pageTitle'] ) . '</a>',
					'screenPageViews'        => $views,
					'userEngagementDuration' => $row['averageSessionDuration'] ? WPANALYTIFY_Utils::pretty_time( $row['averageSessionDuration'] ) : 0,
					'bounceRate'             => $row['bounceRate'] ? WPANALYTIFY_Utils::fraction_to_percentage( $row['bounceRate'] ) . '%' : 0,
				);
			}
		}
		return array(
			'success'    => true,
			'headers'    => array(
				'no'                     => array(
					'label'    => esc_html__( '#', 'wp-analytify' ),
					'type'     => 'counter',
					'th_class' => 'analytify_num_row',
					'td_class' => 'analytify_txt_center',
				),
				'pageTitle'              => array(
					'label'    => esc_html__( 'Title', 'wp-analytify' ),
					'th_class' => 'analytify_txt_left',
					'td_class' => '',
				),
				'screenPageViews'        => array(
					'label'    => esc_html__( 'Views', 'wp-analytify' ),
					'th_class' => 'analytify_value_row',
					'td_class' => 'analytify_txt_center analytify_value_row',
				),
				'userEngagementDuration' => array(
					'label'    => esc_html__( 'Avg. Time', 'wp-analytify' ),
					'th_class' => 'analytify_value_row',
					'td_class' => 'analytify_txt_center analytify_value_row',
				),
				'bounceRate'             => array(
					'label'    => esc_html__( 'Bounce Rate', 'wp-analytify' ),
					'th_class' => 'analytify_value_row',
					'td_class' => 'analytify_txt_center analytify_value_row',
				),
			),
			'stats'      => $stats,
			'pagination' => true,
			'footer'     => apply_filters( 'analytify_top_pages_footer', __( 'Top pages and posts.', 'wp-analytify' ), array( $this->start_date, $this->end_date ) ),
		);
	}

	/**
	 * Get single post analytics statistics
	 *
	 * Retrieves detailed analytics data for a specific post or page,
	 * including pageviews, time on page, and user engagement metrics.
	 *
	 * @return array<string, mixed> Single post statistics with section data
	 */
	private function get_single_post_stats() {
		$sections = array();

		/**
		 * Sections added by the Core:
		 * 'General Statistics', 'Scroll Depth Reach'.
		 *
		 * Section added by Pro:
		 * 'Geographic', 'System Stats', 'How people are finding you (keywords)',
		 * 'Social Media', 'Top Referrers', 'What's happening when users come to your page'.
		 *
		 * More sections, can be added via the filter.
		 */
		$sections = apply_filters( 'analytify_single_post_sections', $sections, $this->post_id, array( $this->start_date, $this->end_date ) );

		if ( ! $sections || ! is_array( $sections ) ) {
			$sections['success'] = false;
			$sections['message'] = esc_html__( 'No sections found.', 'wp-analytify' );
		} else {
			$sections['success'] = true;
			$start_timestamp     = strtotime( $this->start_date );
			$end_timestamp       = strtotime( $this->end_date );
			// translators: Analytics display - %1$s is start date, %2$s is end date.
			$sections['heading'] = sprintf( esc_html__( 'Displaying Analytics of this page from %1$s to %2$s.', 'wp-analytify' ), wp_date( 'jS F, Y', $start_timestamp ? $start_timestamp : time() ), wp_date( 'jS F, Y', $end_timestamp ? $end_timestamp : time() ) );
		}

		return $sections;
	}

	/**
	 * Get single post sections for analytics display
	 *
	 * Processes and formats analytics sections for single post display,
	 * including general statistics and scroll depth metrics.
	 *
	 * @param array $sections Array of sections to process.
	 * @param int   $post_id  Post ID for analytics.
	 * @param array $date     Start and end dates.
	 * @return array Processed sections with analytics data
	 */
	public function single_post_sections( $sections, $post_id, $date ) {
		$show_settings = $this->wp_analytify->settings->get_option( 'show_panels_back_end', 'wp-analytify-admin', array( 'show-overall-dashboard' ) );
		if ( empty( $show_settings ) || ( ! in_array( 'show-overall-dashboard', $show_settings, true ) && ! in_array( 'show-scroll-depth-stats', $show_settings, true ) ) ) {
			return $sections;
		}
		$report = new Analytify_Report(
			array(
				'dashboard_type' => 'single_post',
				'start_date'     => $date[0],
				'end_date'       => $date[1],
				'post_id'        => $post_id,
			)
		);
		if ( in_array( 'show-overall-dashboard', $show_settings, true ) ) {
			$general_stats             = $report->get_general_stats();
			$sections['general_stats'] = array(
				'title'            => esc_html__( 'General Statistics', 'wp-analytify' ),
				'type'             => 'boxes',
				'stats'            => $general_stats['boxes'],
				'new_vs_returning' => $general_stats['new_vs_returning_boxes'],
				'device_visitors'  => $general_stats['device_visitors_boxes'],
			);
		}
		if ( in_array( 'show-scroll-depth-stats', $show_settings, true ) && 'on' === $this->wp_analytify->settings->get_option( 'depth_percentage', 'wp-analytify-advanced' ) ) {
			$scroll_depth_stats       = $report->get_scroll_depth_stats();
			$sections['scroll_depth'] = array(
				'title'       => esc_html__( 'Scroll Depth Reach', 'wp-analytify' ),
				'type'        => 'table',
				'table_class' => 'analytify_bar_tables',
				'headers'     => array(
					'percentage' => array(
						'label'    => esc_html__( 'Scroll Percentage', 'wp-analytify' ),
						'th_class' => 'analytify_txt_left',
						'td_class' => '',
					),
					'events'     => array(
						'label'    => esc_html__( 'Total Reached', 'wp-analytify' ),
						'th_class' => '',
						'td_class' => 'analytify_txt_center analytify_value_row',
					),
				),
				'stats'       => $scroll_depth_stats['stats'],
			);
		}
		return $sections;
	}
}
