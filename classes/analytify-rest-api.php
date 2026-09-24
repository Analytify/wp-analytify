<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Analytify REST API class.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once __DIR__ . '/analytify-rest/bootstrap.php';
require_once __DIR__ . '/analytify-rest/endpoints-general.php';
require_once __DIR__ . '/analytify-rest/endpoints-content.php';
require_once __DIR__ . '/analytify-rest/endpoints-dimensions.php';

	/**
	 * Handle Analytify REST API endpoints.
	 *
	 * @package WP_Analytify
	 * @since 1.0.0
	 */
class Analytify_Rest_API {
	use Analytify_Rest_Bootstrap;
	use Analytify_Rest_Endpoints_General;
	use Analytify_Rest_Endpoints_Content;
	use Analytify_Rest_Endpoints_Dimensions;


	/**
	 * Adds 'General Statistics', 'Scroll Depth Reach' sections for single post stats.
	 *
	 * @param array<string, mixed> $sections Sections.
	 * @param int                  $post_id  Post id.
	 * @param array<int, string>   $date     Start and End date.
	 * @return array<string, mixed>
	 */
	public function single_post_sections( $sections, $post_id, $date ): array {

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
				// TODO: add footer.
			);
		}

		if ( in_array( 'show-scroll-depth-stats', $show_settings, true ) && 'on' === $this->wp_analytify->settings->get_option( 'depth_percentage', 'wp-analytify-advanced' ) ) {

			$scroll_depth_stats = $report->get_scroll_depth_stats();

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

	/**
	 * Formate 'general_statistics' footer, add labels and description.
	 *
	 * @param string               $number Number to format.
	 * @param array<string, mixed> $data   Start and End date (unused).
	 *
	 * @return string
	 */
	public function general_stats_footer( $number, $data ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Parameter required by filter
		$time_value = is_numeric( $number ) ? WPANALYTIFY_Utils::pretty_time( (float) $number ) : '0';
		// translators: %s is the formatted time duration.
		return sprintf( __( 'Total time visitors spent on your site: %s.', 'wp-analytify' ), '<span class="analytify_red general_stats_message">' . $time_value . '</span>' );
	}

	/**
	 * Get profile related data based on the key (option) provided.
	 *
	 * @param string $key Option name.
	 * @return string|null
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
	 * Sets compare dates based on the start an end dates.
	 *
	 * @return void
	 */
	private function set_compare_dates() {
		$date_diff = WPANALYTIFY_Utils::calculate_date_diff( $this->start_date, $this->end_date );
		if ( ! $date_diff ) {
			return;
		}

		$this->compare_start_date = $date_diff['start_date'];
		$this->compare_end_date   = $date_diff['end_date'];
		$this->compare_days       = $date_diff['diff_days'];
	}

	/**
	 * Compares current stat with the previous one and returns the formatted difference.
	 *
	 * @param int    $current_stat Current stat.
	 * @param int    $old_stat     Old stat to compare with.
	 * @param string $type         Type of stat (key).
	 *
	 * @return array<string, mixed>|false
	 */
	private function compare_stat( $current_stat, $old_stat, $type ) {

		// Check for compare dates.
		if ( is_null( $this->compare_start_date ) || is_null( $this->compare_end_date ) || is_null( $this->compare_days ) ) {
			return false;
		}

		// So we don't divide by zero.
		if ( ! $old_stat || 0 === $old_stat ) {
			return false;
		}
		$number = number_format( ( ( $current_stat - $old_stat ) / $old_stat ) * 100, 2 );

		if ( 'bounce_rate' === $type ) {
			$arrow_type = ( $number < 0 ) ? 'analytify_green_inverted' : 'analytify_red_inverted';
		} else {
			$arrow_type = ( $number > 0 ) ? 'analytify_green' : 'analytify_red';
		}

		return array(
			'arrow_type' => $arrow_type,
			'main_text'  => $number . esc_html__( '%', 'wp-analytify' ),
			// translators: Days.
			'sub_text'   => sprintf( esc_html__( '%s days ago', 'wp-analytify' ), $this->compare_days ),
		);
	}

	/**
	 * Returns start and end date as an array to be used for GA4's get_reports()
	 *
	 * @return array<string, mixed>
	 */
	private function get_dates(): array {
		return array(
			'start' => $this->start_date,
			'end'   => $this->end_date,
		);
	}
}

/**
 * Init the instance.
 */
Analytify_Rest_API::get_instance();
