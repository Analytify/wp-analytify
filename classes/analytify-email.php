<?php

class Analytify_Email_Core {

	private $WP_ANALYTIFY = '';

	function __construct() {
		if ( ! $this->verify_update() ) 
			return;

		$this->WP_ANALYTIFY = $GLOBALS['WP_ANALYTIFY'];
		
		$this->setup_constants();
		$this->anaytify_email_check_time();
		$this->hooks();

		if ( isset( $_POST['test_email'] ) ) {
			$this->callback_on_cron_time();
			add_action( 'admin_notices', array( $this , 'analytify_email_notics' ) );
		}
	}

	function hooks() {
		add_action( 'admin_enqueue_scripts' , array( $this, 'analytify_email_scripts' ) );
		// add_action( 'analyitfy_email_setting_submenu', array( $this, 'email_submenu' ), 25 );
		add_action( 'analytify_email_cron_function', array( $this, 'callback_on_cron_time' ) );
		add_action( 'wp_analytify_pro_setting_tabs' , array( $this, 'analytify_email_setting_tabs' ) , 20 , 1 );
		add_filter( 'wp_analytify_pro_setting_fields', array( $this, 'analytifye_email_setting_fields' ) , 20, 1 );
		add_action( 'after_single_view_stats_buttons', array( $this, 'single_send_email' ) );
		add_action( 'wp_ajax_send_analytics_email', array( $this, 'send_analytics_email' ) );
		add_action( 'analytify_settings_logs', array( $this, 'analytify_settings_logs' ) );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0
	 */
	function analytify_email_scripts() {
		wp_enqueue_script( 'analytify_email_script', ANALYTIFY_PLUGIN_URL . 'assets/default/js/wp-analytify-email.js', array(), ANALYTIFY_VERSION, 'true' );
	}

	// /**
	//  * Add email reporting submenu.
	//  *
	//  * @since 1.0
	//  */
	// function email_submenu() {
	// 	add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'Email Notifications', 'wp-analytify' ), esc_html__( 'Email Notifications', 'wp-analytify' ), 'manage_options', 'analytify-settings#wp-analytify-email', array( $this, 'analytify_email_setting' ) );
	// }

	function analytify_email_setting(){
	}

	/**
	 * Setup plugin constants
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function setup_constants() {
		// Setting Global Values
		$this->define( 'ANALYTIFY_IMAGES_PATH', "https://analytify.io/assets/email/" );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	function anaytify_email_check_time() {
		// Check if event is scheduled before.
		if ( ! wp_next_scheduled( 'analytify_email_cron_function' ) ) {
			wp_schedule_event( time() , 'daily', 'analytify_email_cron_function' );
		}
	}

	function analytify_email_setting_tabs( $old_tabs ) {
		$pro_tabs = array(
			array(
				'id'       => 'wp-analytify-email',
				'title'    => __( 'Email', 'wp-analytify' ),
				'priority' => '32',
			),
		);

		return array_merge( $old_tabs,$pro_tabs );
	}

	function  analytify_email_notics() { 
		$class   = 'wp-analytify-success';
		$message = esc_html( 'Analytify detailed report sent!', 'wp-analytify-email' );
		
		analytify_notice( $message, $class );
	}

	function custom_phpmailer_init( $PHPMailer ) {
		$PHPMailer->IsSMTP();
		$PHPMailer->SMTPAuth = true;
		$PHPMailer->SMTPSecure = 'ssl';
		$PHPMailer->Host = 'smtp.gmail.com';
		$PHPMailer->Port = 465;
		$PHPMailer->Username = 'test@gmail.com';
		$PHPMailer->Password = '';
	}

	function analytifye_email_setting_fields( $old_fields ) {
		$email_fields = array(
			'wp-analytify-email' => array(
				array(
					'name'  => 'disable_email_reports',
					'label' => __( 'Disable Email Reporting', 'wp-analytify' ),
					'desc'  => __( 'This will stop sending your website stats email reports.', 'wp-analytify' ),
					'type'  => 'checkbox',
				),
				array(
					'name'              => 'analytiy_from_email',
					'label'             => __( 'Enter Sender Email Address', 'wp-analytify' ),
					'desc'              => __( 'Sender Email Address.', 'wp-analytify' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_email',
				),
				array(
					'name'              => 'analytify_email_user_email',
					'label'             => __( 'Enter Receiver Email Address', 'wp-analytify' ),
					'desc'              => __( 'Use commas to add more than one mail address.', 'wp-analytify' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		);

		if ( ! class_exists( 'WP_Analytify_Email' ) ) {
			array_push( $email_fields['wp-analytify-email'], array(
				'name'              => 'analytify_email_promo',
				'type'              => 'email_promo',
				'label'             => '',
				'desc'              => '',
				) );
		}

		return array_merge( $old_fields, $email_fields );
	}

	function callback_on_cron_time() {
		// Retrun if no profile selected.
		$profile = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
		if ( empty( $profile ) ) {
			return;
		}

		// Retrun if reports are off.
		$disable_emails = $this->WP_ANALYTIFY->settings->get_option( 'disable_email_reports', 'wp-analytify-email' );
		if ( 'on' == $disable_emails ) {
			return;
		}

		// stop TranslatePress to translate the emails.
		add_filter( 'trp_stop_translating_page', '__return_true' );

		$Analytify_Email = $GLOBALS['WP_ANALYTIFY'];
		$site_url = site_url();
		$when_to_send_report = $this->when_to_send_report();
		
		foreach ( $when_to_send_report as $when ) {
			if ( $when == 'week' ) {
				$start_date_val = strtotime( '-1 week' );
				$report_of = 'Weekly';
			} else {
				$start_date_val = strtotime( '-1 month' );
				$report_of = 'Monthly';
			}

			$end_date_val        = strtotime( 'now' );
			$start_date          = date( 'Y-m-d', $start_date_val );
			$end_date            = date( 'Y-m-d', $end_date_val );

			$date1               = date_create( $start_date );
			$date2               = date_create( $end_date );
			$diff                = date_diff( $date2, $date1 );
			$different           = $diff->format("%a") . ' ' . analytify__( 'days' , 'wp-analytify' ) ;

			$compare_start_date  = strtotime( $start_date. $diff->format("%R%a days") );
			$compare_start_date  = date( 'Y-m-d', $compare_start_date );
			$compare_end_date 	 = $start_date;

			$_logo_id  = $Analytify_Email->settings->get_option( 'analytify_email_logo','wp-analytify-email' );

			if ( $_logo_id ) {
				$_logo_link_array =  wp_get_attachment_image_src( $_logo_id, array( 150, 150 ) );
				$logo_link = $_logo_link_array[0];
			} else {
				$logo_link = ANALYTIFY_IMAGES_PATH . "logo.png";
			}

			$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<title>Analytify</title>
				<link href="http://fonts.googleapis.com/css?family=Roboto%7cRoboto+Slab:400,500" rel="stylesheet" />
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
											<td align="left"><a href="'. $site_url .'"><img src="'. $logo_link .'" alt=""/></a></td>
											<td align="right" style="font: normal 15px \'Roboto\', Arial, Helvetica, sans-serif; line-height: 1.5;">
											<font color="#444444">' . $report_of . __( ' Report', 'wp-analytify' ) . '</font><br>
											<font color="#848484">' . date( 'M d Y', $start_date_val ) . ' - ' . date( 'M d Y', $end_date_val ) . '</font><br />
											<font color="#848484"><a href="'. get_home_url() .'">'. get_home_url() .'</a></font>
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
															<font color="#444444">Hi, '.wp_get_current_user()->data->display_name.'!</font>
														</td>
													</tr>
													<tr>
														<td	style="font: normal 14px \'Roboto\', Arial, Helvetica, sans-serif; padding: 0px 20px 0px 20px;">
															<font color="#848484">Check out the following metrics and examine the growth of your online venture.</font>
														</td>
													</tr>
												</table>
											</td>
										</tr>';

								$selectd_stats = ! empty(	$Analytify_Email->settings->get_option( 'analytify_email_stats', 'wp-analytify-email' ) ) ? $Analytify_Email->settings->get_option( 'analytify_email_stats', 'wp-analytify-email' ) : array( 'show-overall-general' );
		
								// General Stats.
								if ( is_array( $selectd_stats ) &&  in_array( 'show-overall-general', $selectd_stats )  ) {
									$stats = $Analytify_Email->pa_get_analytics_dashboard( 'ga:sessions,ga:users,ga:bounceRate,ga:avgTimeOnPage,ga:pageviewsPerSession,ga:pageviews,ga:percentNewSessions,ga:newUsers,ga:avgSessionDuration,ga:sessionDuration', $start_date, $end_date, false, false, false, false, 'analytify-email-general-stats' );
									$old_stats = $Analytify_Email->pa_get_analytics_dashboard( 'ga:sessions,ga:users,ga:bounceRate,ga:avgTimeOnPage,ga:pageviewsPerSession,ga:pageviews,ga:percentNewSessions,ga:newUsers,ga:avgSessionDuration', $compare_start_date, $compare_end_date, false, false, false, false, 'analytify-email-general-compare-stats' );

									if ( ! function_exists( 'pa_email_include_general' ) ) {
										include ANALYTIFY_ROOT_PATH . '/views/email/general-stats.php';
									}

									$message .= pa_email_include_general( $Analytify_Email, $stats, $old_stats, $different );
								}

								$dates = array( 'start_date' => $start_date, 'end_date' => $end_date ) ;

								// Get pro settings options.
								$message = apply_filters( 'wp_analytify_email_on_cron_time', $message, $selectd_stats, $dates );

								// should the email add a note in the bottom
								$show_email_note	= apply_filters( 'wp_analytify_show_email_note', true );
								$emil_note_text		= $Analytify_Email->settings->get_option( 'analytiy_mail_text_additional', 'wp-analytify-email' );

								if ( '' !== $emil_note_text && $show_email_note ) {
									
									// Filter the email additional note text.
									$text_email_note = apply_filters( 'wp_analytify_email_note_text', $emil_note_text );

									if( $show_email_note ) {
										$message .= '
										<tr>
											<td style="padding: 20px 20px;">
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
													<td valign="top" style="width:100px; font: bold 14px \'Roboto\', Arial, Helvetica, sans-serif; padding-right:4px;"><font  color="#444444">Note: </font></td>
														<td style="font: normal 14px \'Roboto\', Arial, Helvetica, sans-serif;"><font color="#848484">'.$text_email_note.'</font><br><br></td>
													</tr>
												</table>
											</td>
										</tr>';
									}
								}

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

			$emails = $Analytify_Email->settings->get_option( 'analytify_email_user_email','wp-analytify-email' );
			$emails_array = array( get_option( 'admin_email' ) );
			
			if ( ! empty( $emails ) ) {
				$emails_array = explode( ',' , $emails );
			} 

			$subject = $Analytify_Email->settings->get_option( 'analytify_email_subject','wp-analytify-email' );
			
			if ( ! $subject ) {

				$protocols = array( 'https://', 'https://www', 'http://', 'http://www.', 'www.' );
 				$site_url = str_replace( $protocols, '', get_home_url() );

				if ( $when == 'week' ) {
					$subject = __( 'Weekly Engagement Summary of ' . $site_url, 'wp-analytify' );
				} elseif ( $when == 'month' ) {
					$subject = __( 'Monthly Engagement Summary of ' . $site_url, 'wp-analytify' );
				}

			}

			$_from_name  = $Analytify_Email->settings->get_option( 'analytiy_from_name', 'wp-analytify-email' );
			$_from_name  = ! empty( $_from_name ) ? $_from_name : 'Analytify Notifications';
			$_from_email = $Analytify_Email->settings->get_option( 'analytiy_from_email', 'wp-analytify-email' );
			$_from_email = ! empty( $_from_email ) ? $_from_email : 'no-reply@analytify.io';
			// $_from_email = ! empty( $_from_email ) ? $_from_email : get_option( 'admin_email' );

			$headers = array(
				'From: '. $_from_name . ' <' . $_from_email . '>',
				'Content-Type: text/html; charset=UTF-8'
			);

			wp_mail( $emails_array , $subject, $message, $headers );
		}
	}

	function when_to_send_report() {
		$when_to_send_email = array();
		
		// Return true, if test button trigger.
		if ( isset( $_POST['test_email'] ) ) {
			if ( class_exists( 'WP_Analytify_Email' ) ) {
				$when_to_send_email[] = 'month';
			} else {
				$when_to_send_email[] = 'week';
			}

			return $when_to_send_email;
		}

		if ( class_exists( 'WP_Analytify_Email' ) ) {
			$time_settings = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'analytif_email_cron_time','wp-analytify-email' );
			$week_date  = $time_settings['week'];
			$month_date = $time_settings['month'];
		} else {
			$week_date  = 'Monday';
			$month_date = false;
		}

		$current_day       = date( 'l' ); // Sunday through Saturday.
		$current_date      = date( 'j' ); // Day of the month without leading zeros.
		$last_day_of_month = date( 't' ); // Number of days in the given month.

		if ( $week_date == $current_day ) {
			$when_to_send_email[] = 'week';
		}

		// if last date of month
		if ( $month_date == $last_day_of_month ) {
			$when_to_send_email[] = 'month';
		} elseif ( $month_date == $current_date ) {
			$when_to_send_email[] = 'month';
		}

		return $when_to_send_email;
	}

	/**
	 * Show Send Email button on Single Page/Post.
	 *
	 * @since 1.2.0
	 */
	function single_send_email() {

		echo '<input type="submit" value=" '. __( 'Send Email', 'wp-analytify' ) .'" name="send_email" class="analytify_submit_date_btn"  id="send_single_analytics">';
		echo "<span  style='min-height:30px;min-width:150px;display:none' class='send_email stats_loading'></span>";
	}

	/**
	 * Send Email Stats for Single Page/Post
	 *
	 * @since 1.2.0
	 */
	function send_analytics_email() {

		$start_date = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
		$end_date   = sanitize_text_field( wp_unslash( $_POST['end_date'] ) );
		$post_id    = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		$site_url   = site_url();

		if ( 0 === $post_id ) {
			$u_post = '/'; // $url_post['path'];
		} else {
			$u_post = parse_url( get_permalink( $post_id ) );
		}

		if ( 'localhost' == $u_post['host'] ) {
			$filter = 'ga:pagePath==/'; // .$u_post['path'];
		} else {
			$filter = 'ga:pagePath==' . $u_post['path'] . '';
			// $filter = 'ga:pagePath==' . $u_post['host'] . '/';
			$filter = apply_filters( 'analytify_page_path_filter', $filter, $u_post );
			// Url have query string incase of WPML.
			if ( isset( $u_post['query'] )  ) {
				$filter .= '?' . $u_post['query'];
			}
		}


		if ( '' == $start_date ) {

			$s_date = get_the_time( 'Y-m-d', $post->ID );
			if ( get_the_time( 'Y', $post->ID ) < 2005 ) {
				$s_date = '2005-01-01';
			}
		} else {
			$s_date = $start_date;
		}

		if ( '' == $end_date ) {
			$e_date = date( 'Y-m-d' );
		} else {
			$e_date = $end_date;
		}

		$Analytify_Email = $GLOBALS['WP_ANALYTIFY'];

		$_logo_id  = $Analytify_Email->settings->get_option( 'analytify_email_logo','wp-analytify-email' );
		if ( $_logo_id ) {
			$_logo_link_array =  wp_get_attachment_image_src( $_logo_id, array( 150, 150 ) );
			$logo_link = $_logo_link_array[0];
		} else {
			$logo_link = ANALYTIFY_IMAGES_PATH . "logo.png";
		}

		$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<title>Analytify</title>
				<link href="http://fonts.googleapis.com/css?family=Roboto%7cRoboto+Slab:400,500" rel="stylesheet" />
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
											<td align="left"><a href="'. $site_url .'"><img src="'. $logo_link .'" alt=""/></a></td>
											<td align="right" style="font: normal 15px \'Roboto\', Arial, Helvetica, sans-serif; line-height: 1.5;">
											<font color="#444444">' . __( 'Analytics Report', 'wp-analytify' ) . '</font><br>
											<font color="#848484">' . $s_date . ' - ' . $e_date . '</font><br />
											<font color="#848484"><a href="' .get_permalink( $post_id ). '">'. get_the_title( $post_id ) .'</a></font>
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
															<font color="#444444">Hi there,</font>
														</td>
													</tr>
													<tr>
														<td	style="font: normal 14px \'Roboto\', Arial, Helvetica, sans-serif; padding: 0px 20px 0px 20px;">
															<font color="#848484">In the last 7 Days Analytify helped you have site visits of 2,601, total Click of 55, and total CTR of 2</font>
														</td>
													</tr>
												</table>
											</td>
										</tr>';

						$selectd_stats = 	$Analytify_Email->settings->get_option( 'show_panels_back_end', 'wp-analytify-admin' );

						// General Stats.
						if ( is_array( $selectd_stats ) &&  in_array( 'show-overall-dashboard', $selectd_stats ) ) {

							$stats = $Analytify_Email->pa_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:percentNewSessions,ga:newUsers,ga:avgTimeOnPage', $s_date, $e_date, false, false, $filter );

							include ANALYTIFY_ROOT_PATH . '/views/email/general-stats-single.php';
							$message .= pa_email_include_single_general( $Analytify_Email, $stats, false, false );

						}
						
						$dates = array( 'start_date' => $start_date, 'end_date' => $end_date ) ;

						// Get pro settings options.
						$message = apply_filters( 'wp_analytify_single_email', $message, $selectd_stats, $dates );
						
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

		$emails = $Analytify_Email->settings->get_option( 'analytify_email_user_email','wp-analytify-email' );
		$emails_array = array( get_option( 'admin_email' ) );
			
		if ( ! empty( $emails ) ) {
			$emails_array = explode( ',' , $emails );
		} 

		$subject = 'Analytics for ' . get_the_title( $post_id ) ;

		$_from_name  = $Analytify_Email->settings->get_option( 'analytiy_from_name', 'wp-analytify-email' );
		$_from_name  = ! empty( $_from_name ) ? $_from_name : 'Analytify Notifications';
		$_from_email = $Analytify_Email->settings->get_option( 'analytiy_from_email', 'wp-analytify-email' );
		$_from_email = ! empty( $_from_email ) ? $_from_email : 'no-reply@analytify.io';
		// $_from_email = ! empty( $_from_email ) ? $_from_email : get_option( 'admin_email' );

		$headers = array(
			'From: '. $_from_name . ' <' . $_from_email . '>',
			'Content-Type: text/html; charset=UTF-8'
		);

		wp_mail( $emails_array , $subject, $message, $headers );
		wp_die();
	}

	/**
	 * Add email settings in diagnostic information.
	 *
	 */
	function analytify_settings_logs() {

		echo "\r\n";

		echo "-- Analytify Email Setting --\r\n \r\n";

		$analytify_email = get_option( 'wp-analytify-email' );

		WPANALYTIFY_Utils::print_settings_array( $analytify_email );
	}

	/**
	 * Verify email addon.
	 * Check if eamil addon is already present and is perior to latest split functionality version.
	 * 
	 * @return bool
	 */
	function verify_update() {
		if ( defined( 'ANALTYIFY_EMAIL_VERSION' ) && '1.2.8' >= ANALTYIFY_EMAIL_VERSION ) {
			return false;
		}

		return true;
	}

}

/**
 * Init email reports.
 * 
 * @since 3.1.0
 * @return null
 */
function init_analytify_email() {
	new Analytify_Email_Core();
}

add_action( 'init', 'init_analytify_email' );
