<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Email Single Send Trait
 *
 * This trait contains all the single post email sending functionality for the Analytify Email system.
 * It was created to separate single email logic from other email operations, keeping the code
 * organized and maintainable.
 *
 * PURPOSE:
 * - Handles AJAX requests for single post emails
 * - Manages single post email sending logic
 * - Processes email data for individual posts
 * - Handles email validation and processing
 *
 * @package WP_Analytify
 * @subpackage Email
 * @since 8.0.0
 */

trait Analytify_Email_Single_Send {

	/**
	 * Send Email Stats for Single Page/Post (returns immediately with success; sends email after response flush).
	 *
	 * @since 1.2.0
	 * @since 9.0.0 Sends JSON success response, flushes to client, then runs email in same request so mail is sent reliably (e.g. on localhost where background self-request often fails).
	 * @return void
	 */
	public function send_analytics_email() {

		$nonce           = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$recipient_email = isset( $_POST['recipient_email'] ) ? sanitize_email( wp_unslash( $_POST['recipient_email'] ) ) : '';
		$is_access_level = $this->WP_ANALYTIFY->settings->get_option( 'show_analytics_roles_back_end', 'wp-analytify-admin', array( 'administrator' ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Variable name is acceptable for this context

		$is_access_level = (bool) $this->WP_ANALYTIFY->pa_check_roles( $is_access_level ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Variable name is acceptable for this context

		if ( ! wp_verify_nonce( $nonce, 'analytify-single-post-email' ) || ! $is_access_level ) {
			wp_die( 'Sorry, you are not allowed to do that.', 403 );
		}

		$start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['start_date'] ) ) : '';
		$end_date   = isset( $_POST['end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['end_date'] ) ) : '';
		$post_id    = isset( $_POST['post_id'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : 0;

		// Send success response so Preview/Response are not empty and UI shows "Email Report Sent!".
		$body = wp_json_encode( array( 'success' => true ) );
		if ( ! headers_sent() ) {
			status_header( 200 );
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
			header( 'Content-Length: ' . (string) strlen( $body ) );
		}
		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded response.
		// Flush so the client receives the response immediately; then send email in same request.
		while ( ob_get_level() ) {
			ob_end_flush();
		}
		flush();
		if ( function_exists( 'fastcgi_finish_request' ) ) {
			fastcgi_finish_request();
		}

		$this->do_send_single_analytics_email( $post_id, $start_date, $end_date, $recipient_email );
		exit;
	}

	/**
	 * Background handler: run single-post email job (called via non-blocking request, no user session).
	 *
	 * @since 9.0.0
	 * @return void
	 */
	public function send_analytics_email_background() {
		$job_key = isset( $_POST['job_key'] ) ? sanitize_text_field( wp_unslash( $_POST['job_key'] ) ) : '';
		$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( '' === $job_key || ! wp_verify_nonce( $nonce, 'analytify_email_bg_' . $job_key ) ) {
			wp_die( '', 403 );
		}
		$job = get_transient( 'analytify_email_job_' . $job_key );
		delete_transient( 'analytify_email_job_' . $job_key );
		if ( ! is_array( $job ) || empty( $job['post_id'] ) ) {
			wp_die( '', 400 );
		}
		$this->do_send_single_analytics_email(
			(int) $job['post_id'],
			isset( $job['start_date'] ) ? $job['start_date'] : '',
			isset( $job['end_date'] ) ? $job['end_date'] : '',
			isset( $job['recipient_email'] ) ? $job['recipient_email'] : ''
		);
		wp_die();
	}

	/**
	 * Build and send single-post analytics email (Search Console + GA + wp_mail). Used by background job.
	 *
	 * @since 9.0.0
	 * @param int    $post_id          Post ID.
	 * @param string $start_date       Start date (YYYY-MM-DD).
	 * @param string $end_date         End date (YYYY-MM-DD).
	 * @param string $recipient_email  Recipient email or empty to use saved list.
	 * @return void
	 */
	protected function do_send_single_analytics_email( $post_id, $start_date, $end_date, $recipient_email ) {
		$site_url = site_url();

		if ( 0 === $post_id ) {
			$u_post = '/'; // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This is intentionally commented out
		} else {
			$permalink = get_permalink( (int) $post_id );
			$u_post    = $permalink ? wp_parse_url( $permalink ) : array();
		}

		if ( is_array( $u_post ) && isset( $u_post['host'] ) && 'localhost' === $u_post['host'] ) {
			$filter = 'ga:pagePath==/'; // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This is intentionally commented out
		} else {
			$filter = 'ga:pagePath==' . ( is_array( $u_post ) && isset( $u_post['path'] ) ? $u_post['path'] : '' ) . '';
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This is intentionally commented out
			// $filter = 'ga:pagePath==' . $u_post['host'] . '/';
			$filter = apply_filters( 'analytify_page_path_filter', $filter, $u_post );
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This is intentionally commented out
			// Url have query string incase of WPML.
			if ( is_array( $u_post ) && isset( $u_post['query'] ) ) {
				$filter .= '?' . $u_post['query'];
			}
		}

		if ( '' === $start_date ) {
			$post_obj = get_post( (int) $post_id );
			$s_date   = $post_obj ? get_the_time( 'Y-m-d', $post_obj ) : gmdate( 'Y-m-d' );
			if ( $post_obj && get_the_time( 'Y', $post_obj ) < 2005 ) {
				$s_date = '2005-01-01';
			}
		} else {
			$s_date = $start_date;
		}

		if ( '' === $end_date ) {
			$e_date = gmdate( 'Y-m-d' );
		} else {
			$e_date = $end_date;
		}
		$search_console_stats = $this->WP_ANALYTIFY->get_search_console_stats( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Variable name is acceptable for this context
			'post_' . $post_id,
			array(
				'start' => $s_date,
				'end'   => $e_date,
			)
		);
		$total_clicks         = 0;
		$total_impressions    = 0;
		$total_ctrs           = 0;
		if ( isset( $search_console_stats['response']['rows'] ) && count( $search_console_stats['response']['rows'] ) > 0 ) {
			foreach ( $search_console_stats['response']['rows'] as $row ) {
				$total_clicks      += $row['clicks'];
				$total_impressions += $row['impressions'];
			}
		}

		if ( $total_impressions > 0 ) {
			$total_ctrs = round( ( $total_clicks / $total_impressions ) * 100 );
		} else {
			$total_ctrs = 0;
		}

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];

		$_logo_id = $wp_analytify->settings->get_option( 'analytify_email_logo', 'wp-analytify-email' );
		if ( $_logo_id ) {
			$_logo_link_array = wp_get_attachment_image_src( $_logo_id, array( 150, 150 ) );
			$logo_link        = is_array( $_logo_link_array ) && isset( $_logo_link_array[0] ) ? $_logo_link_array[0] : '';
		} else {
			$logo_link = ( defined( 'ANALYTIFY_IMAGES_PATH' ) ? ANALYTIFY_IMAGES_PATH : 'https://analytify.io/assets/email/' ) . 'logo.png';
		}

		if ( $recipient_email ) {
			$emails_array = array( $recipient_email );
		} else {
			$emails       = $wp_analytify->settings->get_option( 'analytify_email_user_email', 'wp-analytify-email' );
			$emails_array = array();
			if ( ! empty( $emails ) ) {
				if ( ! is_array( $emails ) ) {
					$emails_array = explode( ',', $emails );
				} else {
					$emails_array = $emails;
				}
			}
		}

		$subject = 'Analytics for ' . get_the_title( (int) $post_id );

		$_from_name = $wp_analytify->settings->get_option( 'analytiy_from_name', 'wp-analytify-email' );
		$_from_name = ! empty( $_from_name ) ? $_from_name : 'Analytify Notifications';

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
				$name          = ucwords( strstr( $email_address, '@', true ) ? strstr( $email_address, '@', true ) : '' );
			}

			$headers = array(
				'From: ' . $_from_name . ' <' . $_from_email . '>',
				// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This is intentionally commented out
				// 'To: '. $email_address,
				'Content-Type: text/html; charset=UTF-8',
			);
			// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- This is inline CSS for email templates
			$message        = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Analytify</title>
				<style type="text/css">
					@media screen and (max-width: 620px) {
						.main-table {
							width: 100% !important;
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
											<font color="#444444">' . __( 'Analytics Report', 'wp-analytify' ) . '</font><br>
											<font color="#848484">' . $s_date . ' - ' . $e_date . '</font><br />
											<font color="#848484"><a href="' . esc_url( get_permalink( (int) $post_id ) ? get_permalink( (int) $post_id ) : '' ) . '">' . esc_html( get_the_title( (int) $post_id ) ? get_the_title( (int) $post_id ) : '' ) . '</a></font>
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
															<font color="#444444">Hi ' . $name . ',</font>
														</td>
													</tr>
													<tr>
													<td style="font: normal 14px \'Roboto\', Arial, Helvetica, sans-serif; padding: 0px 20px 20px 20px;">
														<font color="#848484">Analytify helped you find out your ' . ( $total_impressions ? $total_impressions : '0' ) . ' site visits, and ' . ( $total_clicks ? $total_clicks : '0' ) . ' clicks with an average CTR of ' . ( $total_ctrs ? $total_ctrs : '0' ) . '% from ' . wp_date( 'jS F Y', strtotime( $start_date ) ? strtotime( $start_date ) : time() ) . ' to ' . wp_date( 'jS F Y', strtotime( $end_date ) ? strtotime( $end_date ) : time() ) . '.</font>
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

						$report_obj = new Analytify_Report(
							array(
								'dashboard_type' => 'single_post',
								'start_date'     => $s_date,
								'end_date'       => $e_date,
								'post_id'        => $post_id,
							)
						);

						$stats = $report_obj->get_general_stats();

						if ( ! function_exists( 'pa_email_include_general' ) ) {
							$plugin_dir = defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR : dirname( dirname( __DIR__ ) );
							include $plugin_dir . '/views/email/general-stats-single.php';
						}

						if ( function_exists( 'pa_email_include_single_general' ) ) {
							pa_email_include_single_general( $wp_analytify, $stats['boxes'], false, false, $stats['total_time_spent'] );
						}
					} else {
						$report_obj = new Analytify_Report(
							array(
								'dashboard_type' => 'single_post',
								'start_date'     => $s_date,
								'end_date'       => $e_date,
								'post_id'        => $post_id,
							)
						);

						$stats = $report_obj->get_general_stats();

						if ( ! function_exists( 'pa_email_include_general' ) ) {
							$plugin_dir = defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR : dirname( dirname( __DIR__ ) );
							include $plugin_dir . '/views/email/general-stats-single.php';
						}

						if ( function_exists( 'pa_email_include_single_general' ) ) {
							pa_email_include_single_general( $wp_analytify, $stats['boxes'], false, false, false );
						}
					}
				}
			}

			$dates = array(
				'start_date' => $start_date,
				'end_date'   => $end_date,
			);

			// Get pro settings options.
			$message = apply_filters( 'wp_analytify_single_email', $message, $selected_stats, $dates );

			$message .= '			</table>
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
