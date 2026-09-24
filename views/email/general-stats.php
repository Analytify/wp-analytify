<?php
/**
 * General Stats Email View.
 *
 * Renders general analytics stats (sessions, visitors, bounce rate, etc.) for email reports.
 *
 * @package WP_Analytify
 * @version   9.0.0
 */

/**
 * Get compared colors for stats (text and background) based on current vs previous period.
 *
 * @param mixed  $results         Current period value.
 * @param mixed  $compare_results Previous period value.
 * @param mixed  $date_different   Date difference (unused; kept for signature compatibility).
 * @param string $stats_for       Optional. Metric key; 'bounce_rate' inverts green/red meaning.
 * @return array<string, string> Keys 'color' and 'bg_color' (hex).
 * @version 9.0.0
 */
function get_compared_colors( $results, $compare_results, $date_different, $stats_for = '' ) {

	// empty() treats 0, 0.0, '0', false, null, '' as empty — avoids strict === 0 missing string '0'.
	if ( empty( $compare_results ) || ! is_numeric( $compare_results ) ) {
		return array(
			'color'    => '#000000',
			'bg_color' => '#ffffff',
		);
	}

	$compare_base = (float) $compare_results;
	if ( 0.0 === $compare_base ) {
		return array(
			'color'    => '#000000',
			'bg_color' => '#ffffff',
		);
	}

	$current = is_numeric( $results ) ? (float) $results : 0.0;
	$compare = ( ( $current - $compare_base ) / $compare_base ) * 100;

	// Invert results for bounce rate.
	if ( ! empty( $stats_for ) && 'bounce_rate' === $stats_for ) {
		return array(
			'color'    => $compare < 0 ? '#00c853' : '#fa5825',
			'bg_color' => $compare < 0 ? '#4ed98817' : '#ffffff',
		);
	}

	return array(
		'color'    => $compare > 0 ? '#00c853' : '#fa5825',
		'bg_color' => $compare > 0 ? '#4ed98817' : '#ffffff',
	);
}

/**
 * Build HTML row for comparison stats (percentage change + "X ago" label) in email.
 *
 * @param mixed  $results         Current period value.
 * @param mixed  $compare_results Previous period value.
 * @param mixed  $date_different  Label for the comparison period (e.g. "7 days"); output is escaped.
 * @param string $stats_for       Optional. Metric key; 'bounce_rate' inverts arrow/color logic.
 * @return string HTML fragment (table row) or empty string when compare_results is 0.
 * @version 9.0.0
 */
function get_compare_email_stats( $results, $compare_results, $date_different, $stats_for = '' ) {

	if ( empty( $compare_results ) || ! is_numeric( $compare_results ) ) {
		return '';
	}

	$compare_base = (float) $compare_results;
	if ( 0.0 === $compare_base ) {
		return '';
	}

	$current       = is_numeric( $results ) ? (float) $results : 0.0;
	$compare_pct   = ( ( $current - $compare_base ) / $compare_base ) * 100;
	$compare_label = number_format( $compare_pct, 2 ) . '%';
	$image_name    = $compare_pct > 0 ? 'analytify_green_arrow.png' : 'analytify_red_arrow.png';
	$color         = $compare_pct > 0 ? '#00c853' : '#fa5825';

	// Invert results for bounce rate.
	if ( ! empty( $stats_for ) && 'bounce_rate' === $stats_for ) {
		$image_name = $compare_pct < 0 ? 'analytify_green_arrow_down.png' : 'analytify_red_arrow_up.png';
		$color      = $compare_pct < 0 ? '#00c853' : '#fa5825';
	}

	return '<tr>
		<td colspan="3">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tbody>
			<tr>
				<td valign="bottom" style="padding: 10px 10px 3px; font: 700 16px "Roboto", Arial, Helvetica, sans-serif;" align="center"><font color=" ' . esc_attr( $color ) . ' "><img src="' . esc_url( ANALYTIFY_IMAGES_PATH . $image_name ) . '" alt="' . esc_attr( $image_name ) . '" style="padding-right:10px; width:10px">' . esc_html( $compare_label ) . '</font></td>
			</tr>
				<tr>
					<td style="padding: 3px 10px 10px; font: 700 10px Roboto, Arial, Helvetica, sans-serif;text-transform:uppercase;" align="center"><font color="#909090">' . esc_html( $date_different ) . ' ago</font></td>
				</tr>
			</tbody>
		</table>
		</td>
	</tr>';
}

/**
 * Generates the main stats table rows for the general-stats email (sessions, visitors, bounce rate, etc.).
 *
 * @param array<string, mixed> $current        Analytify's main object (reserved for future use).
 * @param array<string, mixed> $stats          GA stats for the current period (aggregations).
 * @param array<string, mixed> $old_stats      GA stats from the previous period (for comparison).
 * @param string               $date_different Human-readable comparison period label (e.g. "7 days").
 * @return string HTML fragment for the email body, or empty string on failure.
 * @version 9.0.0
 */
function pa_email_include_general( $current, $stats, $old_stats, $date_different ) {

	// Sessions.
	$__sessions_current = $stats['aggregations']['sessions'] ?? 0;
	$__sessions_old     = $old_stats['aggregations']['sessions'] ?? 0;

	// Visitors.
	$__total_users_current = $stats['aggregations']['totalUsers'] ?? 0;
	$__total_users_old     = $old_stats['aggregations']['totalUsers'] ?? 0;

	// Bounce Rate.
	$__bounce_rate_current = $stats['aggregations']['bounceRate'] ?? 0;
	$__bounce_rate_old     = $old_stats['aggregations']['bounceRate'] ?? 0;

	// Average time on site.
	$__avg_time_on_site_current = $stats['aggregations']['averageSessionDuration'] ?? 0;
	$__avg_time_on_site_old     = $old_stats['aggregations']['averageSessionDuration'] ?? 0;

	// Pages/Session.
	$__pages_per_session_current = $stats['aggregations']['screenPageViewsPerSession'] ?? 0;
	$__pages_per_session_old     = $old_stats['aggregations']['screenPageViewsPerSession'] ?? 0;

	// Page Views.
	$__page_views_current = $stats['aggregations']['screenPageViews'] ?? 0;
	$__page_views_old     = $old_stats['aggregations']['screenPageViews'] ?? 0;

	// Engaged Sessions.
	$__engaged_sessions_current = $stats['aggregations']['engagedSessions'] ?? 0;
	$__engaged_sessions_old     = $old_stats['aggregations']['engagedSessions'] ?? 0;

	// New visitors.
	$__new_visitors_current = $stats['aggregations']['newUsers'] ?? 0;
	$__new_visitors_old     = $old_stats['aggregations']['newUsers'] ?? 0;

	// Total time on site.
	$__total_time_on_site_current = $stats['aggregations']['userEngagementDuration'] ?? 0;

	// Used to generate the main table.
	$formatted_td_stats = array(

		// Sessions.
		'sessions'           => array(
			'label'  => analytify__( 'Sessions', 'wp-analytify' ),
			'colors' => get_compared_colors( $__sessions_current, $__sessions_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_numbers( $__sessions_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__sessions_current, $__sessions_old, $date_different ) : '',
		),

		// Visitors.
		'visitors'           => array(
			'label'  => analytify__( 'Visitors', 'wp-analytify' ),
			'colors' => get_compared_colors( $__total_users_current, $__total_users_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_numbers( $__total_users_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__total_users_current, $__total_users_old, $date_different ) : '',
		),

		// Bounce Rate.
		'bounce_rate'        => array(
			'label'  => analytify__( 'Bounce Rate', 'wp-analytify' ),
			'colors' => get_compared_colors( $__bounce_rate_current, $__bounce_rate_old, $date_different, 'bounce_rate' ),
			'value'  => WPANALYTIFY_Utils::fraction_to_percentage( $__bounce_rate_current ) . '%',
			'old'    => $old_stats ? get_compare_email_stats( $__bounce_rate_current, $__bounce_rate_old, $date_different, 'bounce_rate' ) : '',
		),

		// Average time on site.
		'avg_time_on_site'   => array(
			'label'  => analytify__( 'Avg time on site', 'wp-analytify' ),
			'colors' => get_compared_colors( $__avg_time_on_site_current, $__avg_time_on_site_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_time( $__avg_time_on_site_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__avg_time_on_site_current, $__avg_time_on_site_old, $date_different ) : '',
		),

		// Pages/Session.
		'pages_per_session'  => array(
			'label'  => analytify__( 'Pages/Session', 'wp-analytify' ),
			'colors' => get_compared_colors( $__pages_per_session_current, $__pages_per_session_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_numbers( $__pages_per_session_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__pages_per_session_current, $__pages_per_session_old, $date_different ) : '',
		),

		// Page Views.
		'page_views'         => array(
			'label'  => analytify__( 'Page Views', 'wp-analytify' ),
			'colors' => get_compared_colors( $__page_views_current, $__page_views_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_numbers( $__page_views_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__page_views_current, $__page_views_old, $date_different ) : '',
		),

		// Engaged Sessions.
		'engaged_sessions'   => array(
			'label'  => analytify__( 'Engaged Sessions', 'wp-analytify' ),
			'colors' => get_compared_colors( $__engaged_sessions_current, $__engaged_sessions_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_numbers( $__engaged_sessions_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__engaged_sessions_current, $__engaged_sessions_old, $date_different ) : '',
		),

		// New visitors.
		'new_visitors'       => array(
			'label'  => analytify__( 'New Visitors', 'wp-analytify' ),
			'colors' => get_compared_colors( $__new_visitors_current, $__new_visitors_old, $date_different ),
			'value'  => WPANALYTIFY_Utils::pretty_numbers( $__new_visitors_current ),
			'old'    => $old_stats ? get_compare_email_stats( $__new_visitors_current, $__new_visitors_old, $date_different ) : '',
		),

		// Returning Visitors.
		'returning_visitors' => array(
			'label'  => analytify__( 'Returning Visitors', 'wp-analytify' ),
			'colors' => get_compared_colors( $__sessions_current - $__new_visitors_current, $__sessions_old - $__new_visitors_old, $date_different ),
			'value'  => absint( WPANALYTIFY_Utils::pretty_numbers( $__sessions_current - $__new_visitors_current ) ),
			'old'    => $old_stats ? get_compare_email_stats( $__sessions_current - $__new_visitors_current, $__sessions_old - $__new_visitors_old, $date_different ) : '',
		),

	);

	ob_start();
	?>
	<tr>
		<td bgcolor="#ffffff" class="session-table">
			<table cellspacing="20" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="100%" class="box-table">
				<tr>
					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['sessions']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['sessions']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Sessions', 'wp-analytify' ); ?></font>                       
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['sessions']['value'] ); ?></font>
								</td>
							</tr>
							<?php if ( $formatted_td_stats['sessions']['old'] ) : ?>
								<?php echo wp_kses_post( $formatted_td_stats['sessions']['old'] ); ?>
								<?php endif; ?>
						</table>
					</td>

					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['visitors']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['visitors']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Visitors', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>

							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['visitors']['value'] ); ?></font>
								</td>
							</tr>

						<?php if ( $formatted_td_stats['visitors']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['visitors']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>

					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['bounce_rate']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['bounce_rate']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Bounce rate', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['bounce_rate']['value'] ); ?></font></td>
							</tr>

						<?php if ( $formatted_td_stats['bounce_rate']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['bounce_rate']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>
				</tr>

				<tr>
					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['avg_time_on_site']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['avg_time_on_site']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php esc_html_e( 'Avg time on site', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
							<td width="45"></td>
							<td align="center">
								<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
							</td>
							<td width="45"></td>
						</tr>
					<tr>
						<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
							<font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['avg_time_on_site']['value'] ); ?></font>
						</td>
					</tr>

						<?php if ( $formatted_td_stats['avg_time_on_site']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['avg_time_on_site']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>
					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['pages_per_session']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['pages_per_session']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php esc_html_e( 'Pages/Session', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['pages_per_session']['value'] ); ?></font></td>
							</tr>
						<?php if ( $formatted_td_stats['pages_per_session']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['pages_per_session']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>

					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['page_views']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['page_views']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Page Views', 'wp-analytify' ); ?></font></td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['page_views']['value'] ); ?></font></td>
							</tr>

						<?php if ( $formatted_td_stats['page_views']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['page_views']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['engaged_sessions']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['engaged_sessions']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Engaged Sessions', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color=""><?php echo wp_kses_post( $formatted_td_stats['engaged_sessions']['value'] ); ?></font></td>
							</tr>

						<?php if ( $formatted_td_stats['engaged_sessions']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['engaged_sessions']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>
					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['new_visitors']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['new_visitors']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'New Visitors', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color=""><?php echo wp_kses_post( $formatted_td_stats['new_visitors']['value'] ); ?></font></td>
							</tr>

						<?php if ( $formatted_td_stats['new_visitors']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['new_visitors']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>

					<td style="border: 1px solid <?php echo esc_attr( $formatted_td_stats['returning_visitors']['colors']['color'] ); ?>; background-color: <?php echo esc_attr( $formatted_td_stats['returning_visitors']['colors']['bg_color'] ); ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php esc_html_e( 'Returning visitors', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo wp_kses_post( $formatted_td_stats['returning_visitors']['value'] ); ?></font>
								</td>
							</tr>

						<?php if ( $formatted_td_stats['returning_visitors']['old'] ) : ?>
							<?php echo wp_kses_post( $formatted_td_stats['returning_visitors']['old'] ); ?>
							<?php endif; ?>

						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="16" border="0" width="100%" bgcolor="#f9fafa">
			<tr>
				<td width="32" style="text-align: right;"><img src="<?php echo esc_url( ANALYTIFY_IMAGES_PATH . 'anlytify_about_icon.png' ); ?>" alt="About icon"></td>
				<td style="font: normal 13px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444"><?php analytify_e( 'Total time visitors spent on your site: ', 'wp-analytify' ); ?> <?php echo wp_kses_post( WPANALYTIFY_Utils::pretty_time( $__total_time_on_site_current ) ); ?></font></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding:15px;"></td>
	</tr>
	<?php if ( ! class_exists( 'WP_Analytify_Email' ) && ! class_exists( 'WP_Analytify_Addon_Email' ) ) { ?>
	
	<tr>
		<td valign="top" class="analytify-promo-inner-table" style="padding: 30px 45px;" bgcolor="#ffffff">
			<table style="margin: 0 auto;" cellspacing="0" cellpadding="0" width="100%" align="center">
				<tbody>
					<tr>
						<td valign="top" colspan="2" style="font-size: 26px; font-family: 'Roboto'; font-weight: bold; line-height: 26px; padding-bottom: 24px;" align="center" class="analytify-promo-heading"><font color="#313133"><?php analytify_e( 'Customize weekly and monthly reports' ); ?></font> </td>
					</tr>
					<tr>
						<td valign="top" colspan="2" style="font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px; padding-bottom: 15px;"><font color="#383b3d"><?php analytify_e( 'Email notifications add-on extends the Analytify Pro, and enables more control on customizing Analytics Email reports for your websites, delivers Analytics summaries straight in your inbox weekly and monthly.' ); ?></font></td>
					</tr>
					<tr>
						<td valign="top" class="analytify-promo-lists" width="40%">
							<table cellspacing="0" cellpadding="0" width="100%" align="center">
								<tbody>
									<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Add your logo' ); ?></font></td>
									</tr>
									<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Edit Email Subject' ); ?></font></td>
									</tr>
									<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Choose your own metrics to display in reports' ); ?></font></td>
									</tr>
								</tbody>
							</table>
						</td>
						<td valign="top" class="analytify-promo-lists" width="52%" style="padding-left: 8%;">
							<table cellspacing="0" cellpadding="0" width="100%" align="center">
								<tbody>
									<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Add personal note' ); ?></font></td>
									</tr>
									<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Schedule weekly reports' ); ?></font></td>
									</tr>
									<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Schedule monthly reports' ); ?></font></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr> 
					<?php if ( class_exists( 'WP_Analytify_Pro_Base' ) ) { ?>
					<tr>
						<td valign="top" colspan="2" align="center" style="padding-top: 24px;"><a href="<?php echo esc_url( 'https://analytify.io/add-ons/email-notifications?utm_source=analytify-pro&utm_medium=email-reports&utm_content=cta&utm_campaign=addons-upgrade' ); ?>"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/c29b00f7-b5fa-4e04-9a28-e9d77c69ba15.png" alt="<?php esc_attr_e( 'Buy Email Notifications add-on', 'wp-analytify' ); ?>"></a></td>
					</tr>
					<?php } else { ?>
					<tr>
						<td valign="top" colspan="2" align="center" style="padding-top: 24px;"><a href="<?php echo esc_url( 'https://analytify.io/add-ons/email-notifications?utm_source=analytify-lite&utm_medium=email-reports&utm_content=cta&utm_campaign=bundle-upgrade' ); ?>"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/3c067584-abb3-4c6b-8c28-4cc265e67bfa.png" alt="<?php esc_attr_e( 'Upgrade to Analytify Pro + Email Notifications add-on bundle', 'wp-analytify' ); ?>" class="analytify-update-pro"></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</td>
	</tr>
		<?php
	}
	$message = ob_get_clean();
	return $message ? $message : '';
}
