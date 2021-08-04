<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * View of General Statistics for Single Page
 */

function wpa_include_single_general( $current, $stats ) {

	$results = $stats->totalsForAllResults;
	$is_amp_installed = defined( 'ANALYTIFY_AMP_VERSION' );
	?>
	<div class="analytify_general_status analytify_status_box_wraper">
			<div class="analytify_status_header">
					<h3><?php esc_html_e( 'General Statistics', 'wp-analytify' ); ?></h3>
			</div>
			<div class="analytify_status_body">
				<div class="analytify_general_status_boxes_wraper">
					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Sessions', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:sessions'] ); ?> <?php echo $is_amp_installed ? '(' . $stats->rows['1']['1'] . ')'  : '' ?> </div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'A session is a time period in which a user is actively engaged with your Website.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Visitors', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:users'] ); ?> <?php echo $is_amp_installed ? '(' . $stats->rows['1']['2'] . ')'  : '' ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Users, who complete a minimum one session on your website or content.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Page views', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:pageviews'] ); ?> <?php echo $is_amp_installed ? '(' . $stats->rows['1']['3'] . ')'  : '' ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Page Views are the total number of Pageviews, Viewed by visitors including repeated views.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Avg. time on Page', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_time( $results['ga:avgTimeOnPage'] ); ?> <?php echo $is_amp_installed ? '(' . WPANALYTIFY_Utils::pretty_time( $stats->rows['1']['8'] ) . ')' : '' ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'Total time that a single user spends on your website.', 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( 'Bounce Rate', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:bounceRate'] ); ?> <span class="analytify_xl_f">%</span> <?php echo $is_amp_installed ?  '(' . WPANALYTIFY_Utils::pretty_numbers( $stats->rows['1']['6'] ) . ' <span class="analytify_xl_f">%</span>)'  : '' ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( "Percentage of Single page visits (i.e Number of visits in which a visitor leaves your website from the landing page without browsing your website).", 'wp-analytify' ); ?></p>
							</div>
					</div>

					<div class="analytify_general_status_boxes">
							<h4><?php esc_html_e( '% New sessions', 'wp-analytify' ); ?></h4>
							<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:percentNewSessions'] ); ?>% <?php echo $is_amp_installed ?  '(' . $stats->rows['1']['7'] . '%)' : '' ?></div>
							<div class="analytify_info_tooltip">
								<p><?php esc_html_e( 'A new session is a time period when a new user comes to your website and is actively engaged with your website.', 'wp-analytify' ); ?></p>
							</div>
					</div>
				</div>
			</div>
			<div class="analytify_status_footer">
				<span class="analytify_info_stats"><?php _e( 'Did you know that Average Session Duration of this page is', 'wp-analytify' )?> <?php echo WPANALYTIFY_Utils::pretty_time( $results['ga:avgSessionDuration'] ); ?><span class="analytify_red  general_stats_message"></span>.</span>
			</div>
		</div>
	<?php
}
