<?php
/**
 * View of General Statistics for Single Page.
 *
 * @package WP_Analytify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Display general statistics for a single page.
 *
 * @param mixed $stats Statistics data.
 * @return void
 */
function ga_single_general( $stats ) {

	$rows = $stats['rows'][0];
	?>

	<div class="analytify_general_status analytify_status_box_wraper">
			<div class="analytify_status_header">
					<h3><?php esc_html_e( 'General Statistics', 'wp-analytify' ); ?></h3>
			</div>
			<div class="analytify_status_body">
				<div class="analytify_general_status_boxes_wraper">
					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Sessions', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo esc_html( WPANALYTIFY_Utils::pretty_numbers( $rows['sessions'] ) ); ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'A session is a time period in which a user is actively engaged with your Website.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Visitors', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo esc_html( WPANALYTIFY_Utils::pretty_numbers( $rows['totalUsers'] ) ); ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Users, who complete a minimum one session on your website or content.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Page views', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo esc_html( WPANALYTIFY_Utils::pretty_numbers( $rows['screenPageViews'] ) ); ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Page Views are the total number of Page views, Viewed by visitors including repeated views.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Avg. time on Page', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo esc_html( WPANALYTIFY_Utils::pretty_time( $rows['averageSessionDuration'] ) ); ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Total time that a single user spends on your website.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Bounce Rate', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo esc_html( WPANALYTIFY_Utils::pretty_numbers( $rows['bounceRate'] ) ); ?> <span class="analytify_xl_f">%</span></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Percentage of Single page visits (i.e Number of visits in which a visitor leaves your website from the landing page without browsing your website).', 'wp-analytify' ); ?></p>
							</div>
					</div>
				</div>
			</div>
			<div class="analytify_status_footer">
				<span class="analytify_info_stats"><?php esc_html_e( 'Did you know that Average Session Duration of this page is', 'wp-analytify' ); ?> <?php echo esc_html( WPANALYTIFY_Utils::pretty_time( $rows['averageSessionDuration'] ) ? WPANALYTIFY_Utils::pretty_time( $rows['averageSessionDuration'] ) : '' ); ?><span class="analytify_red  general_stats_message"></span>.</span>
			</div>
		</div>
	<?php
}
