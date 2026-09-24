<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Email Scheduler Trait
 *
 * This trait contains all the scheduling and cron functionality for the Analytify Email system.
 * It was created to separate scheduling logic from other email operations, keeping the code
 * organized and maintainable.
 *
 * PURPOSE:
 * - Manages email cron scheduling and timing (WP daily cron; weekly/monthly are day-matched)
 * - Weekly: last 7 days ending now; disabled when weekday is "Select Day" (false)
 * - Monthly last_day: send on last calendar day (site TZ); report = 1st through that day
 * - Monthly numeric day: send that day (or last day if day does not exist, e.g. 31 in Feb); report = 30 days inclusive ending that day
 * - Scheduling uses the site timezone via wp_timezone(); email range labels use wp_date()
 *
 * @package WP_Analytify
 * @subpackage Email
 * @since 8.0.0
 * @version 9.0.0
 */

trait Analytify_Email_Scheduler {

	/**
	 * Callback function for cron job - sends scheduled email reports.
	 *
	 * @return void
	 * @since 1.0.0
	 * @version 9.1.0
	 */
	public function callback_on_cron_time() {
		// Return if no profile selected.
		$profile = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Variable name is acceptable for this context
		if ( empty( $profile ) ) {
			return;
		}

		// Return if reports are off.
		$disable_emails = $this->WP_ANALYTIFY->settings->get_option( 'disable_email_reports', 'wp-analytify-email' ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Variable name is acceptable for this context
		if ( 'on' === $disable_emails ) {
			return;
		}

		// stop TranslatePress to translate the emails.
		add_filter( 'trp_stop_translating_page', '__return_true' );

		$wp_analytify        = $GLOBALS['WP_ANALYTIFY'];
		$site_url            = site_url();
		$when_to_send_report = $this->when_to_send_report();

		foreach ( $when_to_send_report as $when ) {
			$is_custom_range_report = false;

			if ( 'week' === $when ) {
				$report_of      = 'Weekly';
				$start_date_val = strtotime( '-1 week' );
				$end_date_val   = strtotime( 'now' );
			} elseif ( 'yesterday' === $when ) {
				$report_of      = 'Yesterday';
				$start_date_val = strtotime( 'yesterday' );
				$end_date_val   = strtotime( 'yesterday' );
			} elseif ( 'custom_range' === $when ) {
				if ( ! function_exists( 'analytify_email_get_validated_custom_range_bounds' ) ) {
					continue;
				}
				$cr_bounds = analytify_email_get_validated_custom_range_bounds( $wp_analytify->settings, true );
				if ( null === $cr_bounds ) {
					continue;
				}
				$start_date_val = strtotime( $cr_bounds['start'] . ' 00:00:00 UTC' );
				$end_date_val   = strtotime( $cr_bounds['end'] . ' 00:00:00 UTC' );
				if ( ! $start_date_val || ! $end_date_val ) {
					continue;
				}
				$report_of = __( 'Custom Range', 'wp-analytify' );
			} elseif ( 'month' === $when ) {
				$report_of      = 'Monthly';
				$start_date_val = strtotime( '-1 month' );
				$end_date_val   = strtotime( 'now' );
			} else {
				// Unknown schedule token; do not fall back to a rolling month window.
				continue;
			}

			$start_date = gmdate( 'Y-m-d', $start_date_val );
			$end_date   = gmdate( 'Y-m-d', $end_date_val );

			// Monthly sends only: Pro replaces the rolling month window (helpers loaded in analytify-email.php).
			// Do not apply to yesterday or explicit custom_range — those dates would be overwritten.
			if ( 'month' === $when && function_exists( 'analytify_email_scheduled_report_period' ) ) {
				$custom_period = analytify_email_scheduled_report_period( $wp_analytify->settings );
				if ( is_array( $custom_period ) && isset( $custom_period['start'], $custom_period['end'] ) ) {
					$start_date             = $custom_period['start'];
					$end_date               = $custom_period['end'];
					$start_date_val         = strtotime( $start_date . ' 00:00:00 UTC' );
					$end_date_val           = strtotime( $end_date . ' 00:00:00 UTC' );
					$report_of              = __( 'Custom Range', 'wp-analytify' );
					$is_custom_range_report = true;
				}
			}

			if ( function_exists( 'wp_date' ) ) {
				$report_range_display = wp_date( 'M j, Y', $start_date_val ) . ' - ' . wp_date( 'M j, Y', $end_date_val );
			} else {
				$report_range_display = gmdate( 'M j, Y', $start_date_val ) . ' - ' . gmdate( 'M j, Y', $end_date_val );
			}

			$date1 = date_create( $start_date );
			$date2 = date_create( $end_date );
			if ( 'yesterday' === $when ) {
				$different          = '1 ' . analytify__( 'days', 'wp-analytify' );
				$compare_end_date   = gmdate( 'Y-m-d', strtotime( '-2 days' ) );
				$compare_start_date = $compare_end_date;
			} elseif ( $date1 && $date2 ) {
				$diff      = date_diff( $date2, $date1 );
				$different = $diff->format( '%a' ) . ' ' . analytify__( 'days', 'wp-analytify' );

				$compare_start_date = strtotime( $start_date . ' ' . $diff->format( '%R%a' ) . ' days' );
				$compare_start_date = $compare_start_date ? gmdate( 'Y-m-d', $compare_start_date ) : $start_date;
			} else {
				$different          = '0 ' . analytify__( 'days', 'wp-analytify' );
				$compare_start_date = $start_date;
			}
			if ( 'yesterday' !== $when ) {
				$compare_end_date = $start_date;
			}

			$_logo_id = $wp_analytify->settings->get_option( 'analytify_email_logo', 'wp-analytify-email' );

			if ( $_logo_id ) {
				$_logo_link_array = wp_get_attachment_image_src( $_logo_id, array( 150, 150 ) );
				$logo_link        = $_logo_link_array ? $_logo_link_array[0] : '';
			} else {
				$logo_link = ( defined( 'ANALYTIFY_IMAGES_PATH' ) ? ANALYTIFY_IMAGES_PATH : 'https://analytify.io/assets/email/' ) . 'logo.png';
			}

			$emails       = $wp_analytify->settings->get_option( 'analytify_email_user_email', 'wp-analytify-email' );
			$emails_array = array();

			if ( ! empty( $emails ) ) {
				if ( ! is_array( $emails ) ) {
					$emails_array = explode( ',', $emails );
				} else {
					$emails_array = $emails;
				}
			}

			$subject = $wp_analytify->settings->get_option( 'analytify_email_subject', 'wp-analytify-email' );

			if ( ! $subject ) {
				$protocols = array( 'https://', 'https://www', 'http://', 'http://www.', 'www.' );
				$site_url  = str_replace( $protocols, '', get_home_url() );

				$site_url = sanitize_text_field( $site_url );

				if ( 'week' === $when ) {
					// translators: Weekly engagement.
					$subject = sprintf( __( 'Weekly Engagement Summary of %s', 'wp-analytify' ), $site_url );
				} elseif ( $is_custom_range_report ) {
					// translators: Default email subject when the report uses a saved custom date range. %s: site hostname.
					$subject = sprintf( __( 'Custom Analytics Report for %s', 'wp-analytify' ), $site_url );
				} elseif ( 'month' === $when ) {
					// translators: Monthly engagement.
					$subject = sprintf( __( 'Monthly Engagement Summary of %s', 'wp-analytify' ), $site_url );
				} elseif ( 'yesterday' === $when ) {
					// translators: Previous day engagement.
					$subject = sprintf( __( 'Yesterday\'s Engagement Summary of %s', 'wp-analytify' ), $site_url );
				} elseif ( 'custom_range' === $when ) {
					// translators: Default subject for scheduled email when schedule type is Custom Range. %s: site hostname.
					$subject = sprintf( __( 'Analytics Report for %s', 'wp-analytify' ), $site_url );
				}
			}

			$_from_name  = $wp_analytify->settings->get_option( 'analytiy_from_name', 'wp-analytify-email' );
			$_from_name  = ! empty( $_from_name ) ? $_from_name : 'Analytify Notifications';
			$_from_email = $wp_analytify->settings->get_option( 'analytiy_from_email', 'wp-analytify-email' );
			$_from_email = ! empty( $_from_email ) ? $_from_email : 'no-reply@analytify.io';

			foreach ( $emails_array as $email_group ) {
				if ( is_array( $email_group ) ) {
					$email_group_name = trim( $email_group['name'] );
					$name             = ! empty( $email_group_name ) ? ' ' . esc_html( $email_group_name ) : '';
					$email_address    = sanitize_email( $email_group['email'] );
				} else {
					$name          = '';
					$email_address = sanitize_email( $email_group );
				}

				$headers = array(
					'From: ' . $_from_name . ' <' . $_from_email . '>',
					// 'To: ' . $email_address, // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This is intentionally commented out
					'Content-Type: text/html; charset=UTF-8',
				);

				$custom_message = $wp_analytify->settings->get_option( 'analytify_note_text', 'wp-analytify-email' );

				if ( empty( $custom_message ) ) {
					$custom_message = __( 'Please find below your Google Analytics report for the noted period.', 'wp-analytify' );
				}
				/**
				 * Filter to modify the custom email message in Analytify.
				 *
				 * This filter allows developers to customize the email message content
				 * before it is processed or sent.
				 *
				 * @since 7.0.0
				 *
				 * @param string $custom_message The default custom email message.
				 * @return string The modified custom email message.
				 */

				$custom_message = apply_filters( 'analytify_custom_email_message', $custom_message );

			// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- This is an email template with inline styles
				$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<meta name="x-apple-disable-message-reformatting" />
					<title>Analytify</title>
				<meta name="color-scheme" content="light dark">
				<meta name="supported-color-schemes" content="light dark">
					<style type="text/css">
						@media screen and (max-width: 620px) {
							.main-table {
								width: 100% !important;
								padding-left: 20px !important;
								padding-right: 20px !important;
							}
						}

						@media screen and (max-width: 560px) {
							.box-table>tbody>tr>td {
								width: 100% !important;
								display: block !important;
								margin-bottom: 10px !important;
							}

							.session-table>table {
								display: block !important;
								width: 100% !important;
							}

							.session-table>table>tbody {
								display: block !important;
								width: 100% !important;
							}

							.session-table>table>tbody>tr {
								display: block !important;
								width: 96% !important;
								margin: 10px 2% 10px !important;
							}

							.os-table>td,
							.keywords-table>td {
								display: block;
								width: 100% !important;
							}

							.geographic-table>tbody>tr>td {
								display: block !important;
								width: 100% !important;
							}

							.user-data>table>tbody>tr>td {
								padding: 10px !important;
							}

							.mobile-hide {
								display: none !important;
							}

							.main-table>tbody>tr>td {
								padding: 10px !important;
							}

							.user-data>table>tbody>tr>td img {
								margin-left: 0 !important;
							}
						}
						@media (prefers-color-scheme: dark ) {
							body, [bgcolor="#ffffff"],[bgcolor="#f5f9ff"],[bgcolor="#f9fafa"], [bgcolor="#f3f7fa"], .session-table, .session-table tr td{
								background-color: #000 !important;
							}
							table[bgcolor="#f9fafa"]>tbody>tr>td, .os-table td,.geographic-table{
								background-color: #000000 !important;
							}
							.session-table tr td{
								border-color: #fff !important; 
								color: #fff !important;
							}
							
							table tbody td [color="#313133"],
							table tbody td [color="#383b3d"],
							table tbody td [color="#444"],
							table tbody td [color="#444444"],
							table tbody td [color="#848484"],
							table tbody td [color="#909090"]{
								color: #fff !important;
							}
							table tbody td hr{
								border-top:1px solid #fff !important;
							}
						} 
					</style>
				</head>

				<body style="margin: 0;padding: 0; background: #f3f7fa; " bgcolor="#f3f7fa">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" bgcolor="#f3f7fa">
					
					<tr>
						<td valign="top" style="padding-bottom:95px">
							<table cellpadding="0" cellspacing="0" border="0" width="600" align="center" class="main-table">
							
								<tr>
									<td style="padding: 22px 35px;">
										<table width="100%" cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td align="left"><a href="' . $site_url . '"><img src="' . $logo_link . '" alt="analytify"/></a></td>
												<td align="right" style="font: normal 15px \'Roboto\', Arial, Helvetica, sans-serif; line-height: 1.5;">
												<font color="#444444">' . esc_html(
													sprintf(
														/* translators: %s: period label, e.g. Weekly, Monthly, Yesterday, Custom Range. */
														__( '%s Report', 'wp-analytify' ),
														$report_of
													)
												) . '</font><br>
												<font color="#848484">' . esc_html( $report_range_display ) . '</font><br />
												<font color="#848484"><a href="' . get_home_url() . '">' . get_home_url() . '</a></font>
												</td>
											</tr>
										</table>
									</td>
								</tr>	

								<tr>
						<td style="padding: 0 15px;">
										<table width="100%" cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td valign="top">
													<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff">
														<tr>
															<td	style="font: 400 18px \'Roboto slab\', Arial, Helvetica, sans-serif; padding: 25px 20px 11px 20px;">
																<font color="#444444">' . analytify__( 'Hi' ) . $name . ',</font>
															</td>
														</tr>
														<tr>
															<td style="font: normal 14px \'Roboto\', Arial, Helvetica, sans-serif; padding: 0px 20px 0px 20px;">
																<font color="#848484">
																	' . wp_kses_post( $custom_message ) . '
																</font>
															</td>
														</tr>
													</table>
												</td>
											</tr>';

				$selected_stats = ! empty( $wp_analytify->settings->get_option( 'analytify_email_stats', 'wp-analytify-email' ) ) ? $wp_analytify->settings->get_option( 'analytify_email_stats', 'wp-analytify-email' ) : array( 'show-overall-general' );

				// General Stats.
				if ( is_array( $selected_stats ) && in_array( 'show-overall-general', $selected_stats, true ) ) {

					if ( class_exists( 'WPANALYTIFY_Utils' ) ) {
						$ga_mode = WPANALYTIFY_Utils::get_ga_mode();
						if ( 'ga4' === $ga_mode ) {

							$stats = $wp_analytify->get_reports(
								'analytify-email-general-stats',
								array(
									'sessions',
									'totalUsers',
									'bounceRate',
									'screenPageViewsPerSession',
									'screenPageViews',
									'engagedSessions',
									'newUsers',
									'averageSessionDuration',
									'userEngagementDuration',
								),
								array(
									'start' => $start_date,
									'end'   => $end_date,
								),
								array(
									'date',
								),
								array(
									'type'  => 'dimension',
									'order' => 'desc',
									'name'  => 'date',
								),
								array()
							);

							$old_stats = $wp_analytify->get_reports(
								'analytify-email-general-compare-stats',
								array(
									'sessions',
									'totalUsers',
									'bounceRate',
									'screenPageViewsPerSession',
									'screenPageViews',
									'engagedSessions',
									'newUsers',
									'averageSessionDuration',
									'userEngagementDuration',
								),
								array(
									'start' => $compare_start_date,
									'end'   => $compare_end_date,
								),
								array(
									'date',
								),
								array(
									'type'  => 'dimension',
									'order' => 'desc',
									'name'  => 'date',
								),
								array()
							);

							if ( ! function_exists( 'pa_email_include_general' ) ) {
								$plugin_dir = defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR : dirname( dirname( __DIR__ ) );
								include $plugin_dir . '/views/email/general-stats.php';
							}

							$message .= pa_email_include_general( $wp_analytify, $stats, $old_stats, $different );
						}
					}
				}

				$dates = array(
					'start_date' => $start_date,
					'end_date'   => $end_date,
				);

				// Get pro settings options.
				$message = apply_filters( 'wp_analytify_email_on_cron_time', $message, $selected_stats, $dates );

				$message .= '
																</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</body>
							</html>';

				wp_mail( $email_address, $subject, $message, $headers );
			}
		}
	}

	/**
	 * Determine when to send email reports based on current time and settings.
	 *
	 * @return array<string, mixed> Array of report types to send.
	 * @since 1.0.0
	 */
	public function when_to_send_report() {
		$when_to_send_email = array();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled in the callback function
		$is_test_email = isset( $_POST['test_email'] );

		if ( class_exists( 'WP_Analytify_Email' ) || class_exists( 'WP_Analytify_Addon_Email' ) ) {
			$time_settings = function_exists( 'analytify_email_get_schedule_cron_time_array' )
				? analytify_email_get_schedule_cron_time_array( $GLOBALS['WP_ANALYTIFY']->settings )
				: $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytif_email_cron_time', 'wp-analytify-email' );
			if ( ! is_array( $time_settings ) ) {
				$time_settings = array();
			}
			$week_raw   = isset( $time_settings['week'] ) ? $time_settings['week'] : '';
			$month_raw  = isset( $time_settings['month'] ) ? $time_settings['month'] : '';
			$week_date  = ( $week_raw && 'false' !== $week_raw ) ? $week_raw : '';
			$month_date = ( $month_raw && 'false' !== $month_raw ) ? trim( (string) $month_raw ) : '';

			if ( isset( $time_settings['yesterday'] ) && 'enabled' === $time_settings['yesterday'] ) {
				$when_to_send_email[] = 'yesterday';
			}

			$custom_on = isset( $time_settings['custom_range'] ) && 'enabled' === $time_settings['custom_range'];
			if ( $custom_on ) {
				$cr_s = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytify_email_custom_range_start', 'wp-analytify-email' );
				$cr_e = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytify_email_custom_range_end', 'wp-analytify-email' );
				if ( ! empty( $cr_s ) && ! empty( $cr_e ) ) {
					$when_to_send_email[] = 'custom_range';
				}
			}
		} else {
			$week_date  = 'Monday';
			$month_date = false;
		}

		/**
		 * PHPUnit may set `$GLOBALS['analytify_tests_email_schedule_clock']` to an array with optional
		 * keys `l` (weekday), `j` (day of month), `t` (days in month) before calling schedule logic.
		 *
		 * Filters the calendar values used for weekly / monthly schedule matching in email reports.
		 *
		 * Return an array with optional keys: `l` weekday name (e.g. Wednesday), `j` day of month (1-31),
		 * `t` number of days in the month. Omit a key to keep the live `gmdate()` value for that part.
		 *
		 * @since 9.0.1
		 *
		 * @param array<string, string|int>|null $clock Optional clock override.
		 */
		$clock = null;
		if ( isset( $GLOBALS['analytify_tests_email_schedule_clock'] ) && is_array( $GLOBALS['analytify_tests_email_schedule_clock'] ) ) {
			$clock = $GLOBALS['analytify_tests_email_schedule_clock'];
		}
		if ( null === $clock ) {
			$clock = apply_filters( 'analytify_email_schedule_clock', null );
		}
		if ( is_array( $clock ) ) {
			$current_day       = isset( $clock['l'] ) ? (string) $clock['l'] : gmdate( 'l' );
			$current_date      = isset( $clock['j'] ) ? (string) (int) $clock['j'] : gmdate( 'j' );
			$last_day_of_month = isset( $clock['t'] ) ? (string) (int) $clock['t'] : gmdate( 't' );
		} else {
			$current_day       = gmdate( 'l' ); // Sunday through Saturday.
			$current_date      = gmdate( 'j' ); // Day of the month without leading zeros.
			$last_day_of_month = gmdate( 't' ); // Number of days in the given month.
		}

		if ( '' !== $week_date && $current_day === $week_date ) {
			$when_to_send_email[] = 'week';
		}

		// Monthly: specific calendar day, numeric last-day match, or Pro "Last Day" option.
		if ( $month_date ) {
			$is_last_day_token = (bool) preg_match( '/^last[_\s-]?day$/i', (string) $month_date );
			if ( $is_last_day_token ) {
				if ( (string) $current_date === (string) $last_day_of_month ) {
					$when_to_send_email[] = 'month';
				}
			} elseif ( (string) $last_day_of_month === (string) $month_date ) {
				// Saved day equals month length: send on any day (legacy monthly "last day of month" numeric form).
				$when_to_send_email[] = 'month';
			} elseif ( (string) $current_date === (string) $month_date ) {
				$when_to_send_email[] = 'month';
			}
		}

		// Test Email: use the same rules as cron. If nothing would run today (e.g. weekly not due),
		// fall back to a preview so the button still sends something.
		if ( $is_test_email && empty( $when_to_send_email ) ) {
			if ( class_exists( 'WP_Analytify_Email' ) || class_exists( 'WP_Analytify_Addon_Email' ) ) {
				$when_to_send_email[] = 'month';
			} else {
				$when_to_send_email[] = 'week';
			}
		}

		// Convert to associative array with meaningful keys.
		$result = array();
		foreach ( $when_to_send_email as $index => $value ) {
			$result[ 'schedule_' . $index ] = $value;
		}
		return $result;
	}
}
