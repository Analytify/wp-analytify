<?php
/**
 * Analytify Dashboard file.
 *
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

/**
 * Show Old dashboard to users.
 * We are keeping the old dashboard and will use it later.
 */
if ( isset( $_COOKIE['wp_analytify_current_dashboard'] ) and $_COOKIE['wp_analytify_current_dashboard'] === 'old' ) {
	include( ANALYTIFY_ROOT_PATH . '/inc/analytics-dashboard-old.php' );
	return;
}

$wp_analytify   = $GLOBALS['WP_ANALYTIFY'];

$start_date_val = strtotime( '-1 month' );
$end_date_val   = strtotime( 'now' );
$start_date     = date( 'Y-m-d', $start_date_val );
$end_date       = date( 'Y-m-d', $end_date_val );

$selected_stats = $wp_analytify->settings->get_option( 'show_analytics_panels_dashboard','wp-analytify-dashboard', array() );

$classes = '';
foreach ( $selected_stats as $value ) {
	$classes .= $value . ' ';
}

if ( isset( $_POST['analytify_date_diff'] ) && ! empty( $_POST['analytify_date_diff'] ) ) {
	update_option( 'analytify_date_differ', $_POST['analytify_date_diff'] );
}

$_differ = get_option( 'analytify_date_differ' );

if ( $_differ ) {
	if ( $_differ == 'current_day' ) {
		$start_date = date( 'Y-m-d' );
	} elseif ( $_differ == 'last_7_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
	} elseif ( $_differ == 'last_14_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-14 days' ) );
	} elseif ( $_differ == 'last_30_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-1 month' ) );
	} elseif (  $_differ == 'this_month' ) {
		$start_date =  date('Y-m-01') ;
	} elseif ( $_differ == 'last_month' ) {
		$start_date =  date('Y-m-01', strtotime('-1 month') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	} elseif ( $_differ == 'last_3_months' ) {
		$start_date =  date('Y-m-01', strtotime('-3 month') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	} elseif ( $_differ == 'last_6_months' ) {
		$start_date =  date('Y-m-01', strtotime('-6 month') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	} elseif ( $_differ == 'last_year' ) {
		$start_date =  date('Y-m-01', strtotime('-1 year') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	}

}

if ( isset( $_POST['analytify_date_start'] ) && ! empty( $_POST['analytify_date_start'] ) && isset( $_POST['analytify_date_end'] ) && ! empty( $_POST['analytify_date_end'] ) ) {
	$start_date	= sanitize_text_field( wp_unslash( $_POST['analytify_date_start'] ) );
	$end_date	= sanitize_text_field( wp_unslash( $_POST['analytify_date_end'] ) );
}

$date1 = date_create( $start_date );
$date2 = date_create( $end_date );
$diff  = date_diff( $date2, $date1 );

$compare_start_date = strtotime( $start_date . $diff->format( '%R%a days' ) );
$compare_start_date = date( 'Y-m-d', $compare_start_date );
$compare_end_date  	= $start_date;

// var_dump( $start_date );
// var_dump( $end_date );
// var_dump( $compare_start_date );
// var_dump( $compare_end_date );
// Fetch Dashboard Profile ID.
$dashboard_profile_ID = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
$nonce = wp_create_nonce( 'analytify-get-dashboard-stats' );
$acces_token  = get_option( 'post_analytics_token' );

$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION;

// if ( ! $acces_token ) {
// 	return ;
// } else {
// 	if (  WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection( 'Analytify' )  ) { return; }
// }

/*
* Check with roles assigned at dashboard settings.
*/
// $is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );
// //var_dump($is_access_level);
// // Show dashboard to admin incase of empty access roles.
// if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }
// //var_dump($is_access_level);
// //var_dump($wp_analytify->pa_check_roles( $is_access_level ));
// if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

// 	if ( $acces_token ) {

// dequeue event calendar js
wp_dequeue_script( 'tribe-common' );
wp_dequeue_script( 'mcw-crypto-common' ); ?>

<div class="wpanalytify analytify-dashboard-nav">
	<div class="wpb_plugin_wraper">
		<div class="wpb_plugin_header_wraper">
			<div class="graph"></div>
			<div class="wpb_plugin_header">
				<div class="wpb_plugin_header_title"></div>
				<div class="wpb_plugin_header_info">
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">Changelog - v<?php echo $version; ?></a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="<?php echo ANALYTIFY_PLUGIN_URL . '/assets/images/logo.svg'?>" alt="Analytify">
				</div>
			</div>
		</div>
				
		<div class="analytify-dashboard-body-container">
			<div class="wpb_plugin_body_wraper">
				<div class="wpb_plugin_body">
					<div class="wpa-tab-wrapper"><?php echo $wp_analytify->dashboard_navigation(); ?></div>
					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
						<div class="analytify_wraper <?php echo $classes ?>">
							<div class="analytify_main_title_section">
								<div class="analytify_dashboard_title">
									<h1 class="analytify_pull_left analytify_main_title"><?php esc_html_e( 'Dashboard', 'wp-analytify' ); ?></h1>
									<?php
									$_analytify_profile = get_option( 'wp-analytify-profile' );
									
									if ( $acces_token && isset( $_analytify_profile['profile_for_dashboard'] ) && ! empty( $_analytify_profile['profile_for_dashboard'] ) ) : ?>
										<span class="analytify_stats_of"><a href="<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?>" target="_blank"><?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?></a> (<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'name' ) ?>)</span>
									<?php endif; ?>

								</div>

								<div class="analytify_main_setting_bar">
									<div class="analytify_pull_right analytify_setting">
										<div class="analytify_select_date">

											<?php 
											if ( method_exists( 'WPANALYTIFY_Utils', 'date_form' )  ) {
												WPANALYTIFY_Utils::date_form( $start_date, $end_date );
											} ?>

										</div>
									</div>
								</div>
								<!-- <div class="analytify_select_dashboard analytify_pull_right"><?php // do_action( 'analytify_dashboad_dropdown' ); ?></div> -->
							</div>

							<?php 
							if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {

							/*
							* Check with roles assigned at dashboard settings.
							*/
							$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );
							
							// Show dashboard to admin incase of empty access roles.
							if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }

							$report_url        = WP_ANALYTIFY_FUNCTIONS::get_ga_report_url( $dashboard_profile_ID ) ;
							$report_date_range = WP_ANALYTIFY_FUNCTIONS::get_ga_report_range( $start_date, $end_date, $compare_start_date, $compare_end_date ); 
							//var_dump($is_access_level);
							//var_dump($wp_analytify->pa_check_roles( $is_access_level ));
							if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

								if ( $acces_token ) {
								// if ( in_array( 'show-real-time', $selected_stats ) ) {
								// 	do_action( 'wp_analytify_view_real_time_stats' );
								// }

								if ( in_array( 'show-compare-stats', $selected_stats ) ) {
									do_action( 'wp_analytify_view_compare_stats', $start_date, $end_date, $compare_start_date, $compare_end_date );
								}
								?>

								<!-- General Stats -->
								<?php if (  in_array( 'show-overall-dashboard', $selected_stats ) ) :  ?>
									<div class="analytify_general_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php esc_html_e( 'General Statistics', 'wp-analytify' ); ?></h3>
										</div>
										<div class="analytify_status_body stats_loading">

											<script>
											//<![CDATA[

											jQuery( function($) {
												setTimeout(function(){
													$.get(ajaxurl, { action:'analytify_load_default_general_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" , compare_start_date : "<?php echo $compare_start_date ?>" , compare_end_date : "<?php echo $compare_end_date ?>" , date_different: "<?php echo $diff->format( '%a' ) . ' ' . __( 'days', 'wp-analytify' )  ?>", nonce : '<?php echo $nonce ?>'  },function(data){

														try {
															var data_array = $.parseJSON(data);
															$('.analytify_general_status_boxes_wraper').html(data_array.body).parent().removeClass("stats_loading");
															equalheight('.analytify_general_status_boxes');
															$('.general_stats_message').html(data_array.message).children().removeClass('analytify_xl_f');
														} catch (e) {
															$('.analytify_general_status_boxes_wraper').html(data).parent().removeClass("stats_loading");
														}

													});
												},1500);

											});
											//]]>
											</script>
											<div class="analytify_general_status_boxes_wraper">
											</div>
										</div>
										<div class="analytify_status_footer">
											<span class="analytify_info_stats"><?php _e( 'Did you know that total time on your site is', 'wp-analytify' )?>  <span class="analytify_red  general_stats_message"></span>?</span>
										</div>
									</div>
								<?php endif ?>
								<!-- End of General Stats -->

								<!-- Top Pages Statistics -->
								<?php if (  in_array( 'show-top-pages-dashboard', $selected_stats ) ) :  ?>
									<div class="analytify_general_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php esc_html_e( 'Top pages by views', 'wp-analytify' ); ?>
												<?php $referral_url = 'https://analytics.google.com/analytics/web/#report/content-pages/' ; ?>
												<a href="<?php echo $referral_url . $report_url . $report_date_range ?>" target="_blank" class="analytify_tooltip"><span class="analytify_tooltiptext"><?php _e( 'View All Top Pages', 'wp-analytify' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
												<?php do_action( 'analytify_after_top_page_text' ) ?>
											</h3>
											<div class="analytify_top_page_detials analytify_tp_btn"></div>
										</div>
										<div class="analytify_status_body stats_loading">
											<script>
											//<![CDATA[

											jQuery( function($) {
												setTimeout(function(){
													$.get(ajaxurl, { action:'analytify_load_default_top_pages', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" , compare_start_date : "<?php echo $compare_start_date ?>" , compare_end_date : "<?php echo $compare_end_date ?>" , date_different: "<?php echo $diff->format( '%a days' ) ?>", nonce : '<?php echo $nonce ?>'  },function(data){

														$('.analytify_top_pages_boxes_wraper').html(data).parent().removeClass("stats_loading");
														wp_analytify_paginated();
													});
												},2000);
											});
											//]]>
											</script>
											<div class="analytify_top_pages_boxes_wraper">
											</div>
										</div>
										<div class="analytify_status_footer">
											<span class="analytify_info_stats"><?php esc_html_e( 'Top pages and posts', 'wp-analytify' ); ?></span>
											<div class="wp_analytify_pagination"></div>

										</div>
									</div>
								<?php endif ?>
								<!-- End Top Pages Statistics -->

								<!-- Geographic Statistics -->
								<?php if ( in_array( 'show-geographic-dashboard', $selected_stats ) ) :  ?>
									<div class="analytify_general_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php esc_html_e( 'Geographic', 'wp-analytify' ); ?></h3>
										</div>
										<div class="analytify_status_body stats_loading">
											<script>
											//<![CDATA[

											jQuery( function($) {
												$.get(ajaxurl, { action:'analytify_load_default_geographic', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>", report_url: "<?php echo $report_url ?>", report_date_range: "<?php echo $report_date_range ?>", nonce : '<?php echo $nonce ?>' },function(data){

													$('.analytify_geographic_stats_boxes_wraper').html(data).parent().removeClass("stats_loading");

												});
											});
											//]]>
											</script>
											<div class="analytify_geographic_stats_boxes_wraper">
											</div>
										</div>

										<div class="analytify_status_footer">
											<span class="analytify_info_stats"><?php esc_html_e( 'Top countries and cities', 'wp-analytify' ); ?></span>
										</div>
									</div>
								<?php endif ?>
								<!-- End Geographic Statistics -->


								<!-- System Statistics -->
								<?php if ( in_array( 'show-system-stats', $selected_stats ) ) :  ?>
									<div class="analytify_general_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php esc_html_e( 'Tech Stats', 'wp-analytify' ); ?></h3>
										</div>
										<div class="stats_loading">
											<script>
											//<![CDATA[

											jQuery( function($) {
												setTimeout(function(){
													$.get(ajaxurl, { action:'analytify_load_default_system', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>", nonce : '<?php echo $nonce ?>'  },function(data){

														$('.analytify_system_stats_boxes_wraper').html(data).parent().removeClass("stats_loading");

													});
												},2500);
											});
											//]]>
											</script>
											<div class="analytify_system_stats_boxes_wraper">
											</div>
										</div>
									</div>
									<!-- End System Statistics -->
								<?php endif ?>

								<!-- Gif Add Start -->
								<?php if ( ! class_exists( 'WP_Analytify_Pro' ) && get_option( 'analytify_remove_comparison_gif' ) != 'yes' ) : ?>
								<div class="analytify_general_status analytify_general_status-gif">
									<span class="dashicons dashicons-no-alt analytify_general_status-icon">Dismiss</span>
									<a href="https://analytify.io/upgrade-from-free" class="analytify_block" target="_blank">
										<img src="<?php echo plugins_url( '../assets/images/analytify_compare.gif', __FILE__ )  ?>" alt="Upgrade to Pro" style="width:100%">
										<a href="https://analytify.io/upgrade-from-free" class="analytify_go_pro_overlay" target="_blank">

											<span class="analytify_go_pro_overlay_inner">
												<span class="analytify_h2">Premium feature</span>
												<span class="analytify_btn" target="_blank">Upgrade Now</span>
											</span>
									</a>
								</div>
								<?php endif ?>
								<!-- Gif Add End -->

								<!-- Keyword Statistics -->
								<?php if ( in_array( 'show-keywords-dashboard', $selected_stats ) ) :  ?>
									<div class="analytify_general_status analytify_status_box_wraper">
										<div class="analytify_status_header analytify_header_adj">
											<h3>
												<?php esc_html_e( 'How people are finding you (keywords)', 'wp-analytify' ); ?>
												<?php do_action( 'analytify_after_top_keyword_text' ) ?>
											</h3>
											<div class="analytify_status_header_value keywords_total">
												<span class="analytify_medium_f"><?php esc_html_e( 'Total Visits', 'wp-analytify' ); ?></span>
											</div>
											<div class="analytify_top_keywords_detials analytify_tp_btn">

											</div>
										</div>
										<div class="analytify_status_body stats_loading">
											<script>
											//<![CDATA[

											jQuery( function($) {
												$.get(ajaxurl, { action:'analytify_load_default_keyword', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>", nonce : '<?php echo $nonce ?>'  },function(data){

													try {
														var data_array = $.parseJSON(data);
														$(".keywords_total").append( data_array.total_stats );
														$('.analytify_keyword_stats_boxes_wraper').html( data_array.body ).parent().removeClass("stats_loading");
													} catch (e) {
														$('.analytify_keyword_stats_boxes_wraper').html(data).parent().removeClass("stats_loading");
													}

												});
											});
											//]]>
											</script>
											<div class="analytify_keyword_stats_boxes_wraper"></div>
										</div>
										<div class="analytify_status_footer">
											<span class="analytify_info_stats"><?php esc_html_e( 'Ranked keywords', 'wp-analytify' ); ?></span>
										</div>
									</div>
								<?php endif ?>
								<!-- Enf of Keywords Stats -->

								<div class="analytify_column">
										<div class="analytify_half analytify_left_flow">

											<!-- Social Network Statistics -->
											<?php if ( in_array( 'show-social-dashboard', $selected_stats ) ) :  ?>
												<div class="analytify_general_status analytify_status_box_wraper">
													<div class="analytify_status_header analytify_header_adj">
														<h3>
															<?php esc_html_e( 'Social Network', 'wp-analytify' ); ?>
															<?php $referral_url = 'https://analytics.google.com/analytics/web/#report/social-overview/' ; ?>
															<a href="<?php echo $referral_url . $report_url . $report_date_range ?>" target="_blank" class="analytify_tooltip"><span class="analytify_tooltiptext"><?php _e( 'View All Social Traffic', 'wp-analytify' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
															<?php do_action( 'analytify_after_top_social_media_text' ) ?>
														</h3>
														<div class="analytify_top_keywords_detials analytify_tp_btn">

														</div>
														<div class="analytify_status_header_value social_total">
															<span class="analytify_medium_f"><?php esc_html_e( 'Total Visits', 'wp-analytify' ); ?></span>
														</div>
													</div>
													<div class="analytify_status_body stats_loading">

														<script>
														//<![CDATA[

														jQuery( function($) {
															$.get(ajaxurl, { action:'analytify_load_default_social_media', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>", nonce : '<?php echo $nonce ?>'   },function(data){

																try {
																	var data_array = $.parseJSON(data);
																	$(".social_total").append( data_array.total_stats );
																	$('.analytify_social_media_stats_boxes_wraper').html( data_array.body ).parent().removeClass("stats_loading");
																} catch (e) {

																	$('.analytify_social_media_stats_boxes_wraper').html( data ).parent().removeClass("stats_loading");
																}
															});
														});
														//]]>
														</script>
														<div class="analytify_social_media_stats_boxes_wraper"></div>

													</div>
													<div class="analytify_status_footer">
														<span class="analytify_info_stats"><?php esc_html_e( 'Number of Visitors Coming from Social Channels', 'wp-analytify' ); ?></span>
													</div>
												</div>
												<!-- End Social Stats -->
											<?php endif ?>
										</div>

										<div class="analytify_half analytify_right_flow">
											<!-- Top Reffers -->
											<?php if ( in_array( 'show-referrer-dashboard', $selected_stats ) ) :  ?>
												<div class="analytify_general_status analytify_status_box_wraper">
													<div class="analytify_status_header analytify_header_adj">
														<h3>
															<?php esc_html_e( 'Top Referrers', 'wp-analytify' ); ?>
															<?php $referral_url = 'https://analytics.google.com/analytics/web/#/report/trafficsources-all-traffic/' ; ?>
															<a href="<?php echo $referral_url . $report_url . $report_date_range . '&explorer-table-dataTable.sortColumnName=analytics.visits&explorer-table-dataTable.sortDescending=true&explorer-table.plotKeys=%5B%5D&explorer-table.secSegmentId=analytics.sourceMedium' ?>" target="_blank" class="analytify_tooltip"><span class="analytify_tooltiptext"><?php _e( 'View All Top Referrers', 'wp-analytify' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
															<?php do_action( 'analytify_after_top_reffers_text' ) ?>
														</h3>
														<div class="analytify_top_keywords_detials analytify_tp_btn">

														</div>
														<div class="analytify_status_header_value  reffers_total">
															<span class="analytify_medium_f"><?php esc_html_e( 'Total Visits', 'wp-analytify' ); ?></span>
														</div>
													</div>
													<div class="analytify_status_body stats_loading">

														<script>
														//<![CDATA[

														jQuery( function($) {

															$.ajax({
																url:  <?php echo wp_json_encode( esc_url_raw( rest_url( "wp-analytify/v1/get_report/$dashboard_profile_ID/refferer" ) ) ); ?>,
																data: {
																	sd : '<?php echo $start_date ;?>',
																	ed : '<?php echo $end_date ?>'
																},
																beforeSend: function ( xhr ) {
																	xhr.setRequestHeader( 'X-WP-Nonce', '<?php echo wp_create_nonce( 'wp_rest' ) ?>' );
																},
															})
															.fail(function() {
																var _html = '<table class="analytify_data_tables analytify_no_header_table"><tbody><tr><td class="analytify_td_error_msg"><div class="analytify-stats-error-msg"><div class="wpb-error-box"><span class="blk"><span class="line"></span><span class="dot"></span></span><span class="information-txt">REST API endpoint is disabled.</span></div></div></td></tr></tbody></table>'
																$('.analytify_reffers_stats_boxes_wraper').html(_html).parent().removeClass("stats_loading");
															})
															.done(function(data) {
																var data_array = $.parseJSON(data);
																$(".reffers_total").append( data_array.total_stats );
																$('.analytify_reffers_stats_boxes_wraper').html( data_array.body ).parent().removeClass("stats_loading");
															});
														});


														//]]>
														</script>
														<div class="analytify_reffers_stats_boxes_wraper"></div>

													</div>
													<div class="analytify_status_footer">
														<span class="analytify_info_stats"><?php esc_html_e( 'Top referrers to your website', 'wp-analytify' ); ?></span>
													</div>
												</div>
											<?php endif; ?>
										</div>
								</div>

								<!-- Page Statistics -->
								<?php if ( in_array( 'show-page-stats-dashboard', $selected_stats ) ) :  ?>
									<div class="analytify_general_status analytify_status_box_wraper">
										<div class="analytify_status_header">
											<h3><?php esc_html_e( 'What\'s happening when users come to your site.', 'wp-analytify' ); ?> <?php do_action( 'analytify_after_top_page_stats_text' ) ?></h3>
											<div class="analytify_top_page_detials analytify_tp_btn">

											</div>
										</div>
										<div class="analytify_status_body stats_loading">
											<script>
											//<![CDATA[

											jQuery( function($) {
												$.ajax({
													url:  <?php echo wp_json_encode( esc_url_raw( rest_url( "wp-analytify/v1/get_report/$dashboard_profile_ID/what-happen" ) ) ); ?>,
													data: {
														sd : '<?php echo $start_date ?>',
														ed : '<?php echo $end_date ?>'
													},
													beforeSend: function ( xhr ) {
														xhr.setRequestHeader( 'X-WP-Nonce', '<?php echo wp_create_nonce( 'wp_rest' ) ?>' );
													},
												})
												.fail(function() {
													var _html = '<table class="analytify_data_tables analytify_no_header_table"><tbody><tr><td class="analytify_td_error_msg"><div class="analytify-stats-error-msg"><div class="wpb-error-box"><span class="blk"><span class="line"></span><span class="dot"></span></span><span class="information-txt">REST API endpoint is disabled.</span></div></div></td></tr></tbody></table>'
													$('.analytify_page_stats_boxes_wraper').html(_html).parent().removeClass("stats_loading");
												})
												.done(function(data) {
													var data_array = $.parseJSON(data);
													$('.analytify_page_stats_boxes_wraper').html(data_array.body).parent().removeClass("stats_loading");
													$('.top_pages_message').html(data_array.message);
												});
											});
											//]]>
											</script>
											<div class="analytify_page_stats_boxes_wraper"></div>
										</div>
										<div class="analytify_status_footer">
											<span class="analytify_info_stats top_pages_message"></span>
										</div>
									</div>
								<?php endif ?>
								<!-- End Page Statistics -->

								<?php do_action( 'wp_analytify_view_ajax_error', $start_date, $end_date, $dashboard_profile_ID, $report_url, $report_date_range ) ?>
								<?php do_action( 'wp_analytify_view_404_error', $start_date, $end_date, $dashboard_profile_ID, $report_url, $report_date_range ) ?>
								<?php do_action( 'wp_analytify_view_javascript_error', $start_date, $end_date, $dashboard_profile_ID, $report_url, $report_date_range ) ?>

								<?php
								} else {
									esc_html_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-analytify' );
								}
							} else {
								esc_html_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify' );
							}
						} ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>