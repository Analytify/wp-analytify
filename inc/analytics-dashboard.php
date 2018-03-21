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
	if ( $_differ == 'last_7_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
	}elseif ( $_differ == 'last_14_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-14 days' ) );
	}elseif ( $_differ == 'last_30_days' ) {
		$start_date = date( 'Y-m-d', strtotime( '-1 month' ) );
	}elseif (  $_differ == 'this_month' ) {
		$start_date =  date('Y-m-01') ;
	}elseif ( $_differ == 'last_month' ) {
		$start_date =  date('Y-m-01', strtotime('-1 month') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	}elseif ( $_differ == 'last_3_months' ) {
		$start_date =  date('Y-m-01', strtotime('-3 month') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	}elseif ( $_differ == 'last_6_months' ) {
		$start_date =  date('Y-m-01', strtotime('-6 month') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	}elseif ( $_differ == 'last_year' ) {
		$start_date =  date('Y-m-01', strtotime('-1 year') );
		$end_date =  date('Y-m-t', strtotime('-1 month') );
	}

}

if ( isset( $_POST['view_data'] ) ) {

	$s_date   = sanitize_text_field( wp_unslash( $_POST['st_date'] ) );
	$ed_date  = sanitize_text_field( wp_unslash( $_POST['ed_date'] ) );
}

if ( isset( $s_date ) ) {
	$start_date = $s_date ;
}

if ( isset( $ed_date ) ) {
	$end_date = $ed_date;
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

// Delete the cache.
if ( 'on' === $wp_analytify->settings->get_option( 'delete_dashboard_cache','wp-analytify-dashboard' ) || WPANALYTIFY_Utils::force_clear_cache() ) {
	delete_dashboard_transients( $dashboard_profile_ID, $start_date, $end_date );
}

	?>

	<?php

	$acces_token  = get_option( 'post_analytics_token' );

	if ( ! $acces_token ) {
		return ;
	} else {
		if (  WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection( 'Analytify' )  ) { return; }
	}


	/*
    * Check with roles assigned at dashboard settings.
	*/
	$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );
	//var_dump($is_access_level);
	// Show dashboard to admin incase of empty access roles.
	if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }
	//var_dump($is_access_level);
	//var_dump($wp_analytify->pa_check_roles( $is_access_level ));
	if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

		if ( $acces_token ) {
			// dequeue event calendar js
			wp_dequeue_script( 'tribe-common' );
		?>

		<div class="analytify_wraper <?php echo $classes ?>">
				<div class="analytify_main_title_section">
					<h1 class="analytify_pull_left analytify_main_title"><?php esc_html_e( 'Dashboard', 'wp-analytify' ); ?>
					<span class="analytify_stats_of"><?php esc_html_e( 'Complete Statistics of the Site', 'wp-analytify' ); ?> <a href="<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?>" target="_blank"><?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?></a> (<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'name' ) ?>)</span></h1>
					<div class="analytify_select_dashboard analytify_pull_right">

						<?php do_action( 'analytify_dashboad_dropdown' ); ?>

					</div>
				</div>

				<div class="analytify_main_setting_bar">
					<!-- <h2 class="analytify_pull_left analytify_t_pad"><?php //esc_html_e( 'REALTIME STATS', 'wp-analytify' ); ?></h2> -->
					<div class="analytify_pull_right analytify_setting">
						<div class="analytify_select_date">
							<form class="analytify_form_date" action="" method="post">
								<div class="analytify_select_date_fields">
									<input type="hidden" name="st_date" id="analytify_start_val">
									<input type="hidden" name="ed_date" id="analytify_end_val">
									<input type="hidden" name="analytify_date_diff" id="analytify_date_diff">

									<label for="analytify_start"><?php _e( 'From:', 'wp-analytify' )?></label>
									<input type="text" required id="analytify_start" value="<?php echo isset( $start_date ) ? $start_date :
																			'' ?>">
									<label for="analytify_end"><?php _e( 'To:', 'wp-analytify' )?></label>
									<input type="text" required id="analytify_end" value="<?php echo isset( $end_date ) ? $end_date :
																			'' ?>">
									<div class="analytify_arrow_date_picker"></div>
								</div>
								<input type="submit" value="<?php _e( 'View Stats', 'wp-analytify' ) ?>" name="view_data" class="analytify_submit_date_btn">
								<ul class="analytify_select_date_list">

									<li><?php _e( 'Last 7 days', 'wp-analytify' )?> <span data-date-diff="last_7_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_7_days"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

									<li><?php _e( 'Last 14 days', 'wp-analytify' )?> <span data-date-diff="last_14_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_14_days"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

									<li><?php _e( 'Last 30 days', 'wp-analytify' )?> <span data-date-diff="last_30_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_30_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

									<li><?php _e( 'This month', 'wp-analytify' )?> <span data-date-diff="this_month" data-start="" data-end=""><span class="analytify_start_date_data analytify_this_month_start_date"></span> – <span class="analytify_end_date_data analytify_today_date"></span></li>

									<li><?php _e( 'Last month', 'wp-analytify' )?> <span data-date-diff="last_month" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_month_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


									<li><?php _e( 'Last 3 months', 'wp-analytify' )?> <span data-date-diff="last_3_months" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_3_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

									<li><?php _e( 'Last 6 months', 'wp-analytify' )?> <span data-date-diff="last_6_months" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_6_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


									<li><?php _e( 'Last year', 'wp-analytify' )?> <span data-date-diff="last_year" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_year_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


									<li><?php _e( 'Custom Range', 'wp-analytify' )?> <span class="custom_range"><?php _e( 'Select a custom date', 'wp-analytify' )?></span></li>
								</ul>
							</form>
						</div>
					</div>
				</div>

				<?php

				if ( in_array( 'show-real-time', $selected_stats ) ) {
					do_action( 'wp_analytify_view_real_time_stats' );
				}

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
								$.get(ajaxurl, { action:'analytify_load_default_general_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" , compare_start_date : "<?php echo $compare_start_date ?>" , compare_end_date : "<?php echo $compare_end_date ?>" , date_different: "<?php echo $diff->format( '%a' ) . ' ' . __( 'days', 'wp-analytify' )  ?>"  },function(data){

									var data_array = $.parseJSON(data);
									$('.analytify_general_status_boxes_wraper').html(data_array.body).parent().removeClass("stats_loading");
									equalheight('.analytify_general_status_boxes');
									$('.general_stats_message').html(data_array.message).children().removeClass('analytify_xl_f');
								});
							});
							//]]>
							</script>
							<div class="analytify_general_status_boxes_wraper">
							</div>
						</div>
						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php _e( 'Did you know that total time on your site is', 'wp-analytify' )?>  <span class="analytify_red  general_stats_message"></span>.</span>
						</div>
					</div>
				<?php endif ?>
				<!-- End of General Stats -->

				<!-- Top Pages Statistics -->
				<?php if (  in_array( 'show-top-pages-dashboard', $selected_stats ) ) :  ?>
					<div class="analytify_general_status analytify_status_box_wraper">
						<div class="analytify_status_header">
							<h3><?php esc_html_e( 'Top pages by views', 'wp-analytify' ); ?></h3>
							<div class="analytify_top_page_detials analytify_tp_btn"><?php do_action( 'analytify_after_top_page_text' ) ?></div>
						</div>
						<div class="analytify_status_body stats_loading">
							<script>
							//<![CDATA[

							jQuery( function($) {
								$.get(ajaxurl, { action:'analytify_load_default_top_pages', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" , compare_start_date : "<?php echo $compare_start_date ?>" , compare_end_date : "<?php echo $compare_end_date ?>" , date_different: "<?php echo $diff->format( '%a days' ) ?>"  },function(data){

									$('.analytify_top_pages_boxes_wraper').html(data).parent().removeClass("stats_loading");
									wp_analytify_paginated();
								});
							});
							//]]>
							</script>
							<div class="analytify_top_pages_boxes_wraper">
							</div>
						</div>
						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php esc_html_e( 'List of the top pages and posts.', 'wp-analytify' ); ?></span>
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
								$.get(ajaxurl, { action:'analytify_load_default_geographic', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

									$('.analytify_geographic_stats_boxes_wraper').html(data).parent().removeClass("stats_loading");

								});
							});
							//]]>
							</script>
							<div class="analytify_geographic_stats_boxes_wraper">
							</div>
						</div>

						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php esc_html_e( 'Listing statistics of top countries and cities.', 'wp-analytify' ); ?></span>
						</div>
					</div>
				<?php endif ?>
				<!-- End Geographic Statistics -->


				<!-- System Statistics -->
				<?php if ( in_array( 'show-system-stats', $selected_stats ) ) :  ?>
					<div class="analytify_general_status analytify_status_box_wraper">
						<div class="analytify_status_header">
							<h3><?php esc_html_e( 'System Stats', 'wp-analytify' ); ?></h3>
						</div>
						<div class="stats_loading">
							<script>
							//<![CDATA[

							jQuery( function($) {
								$.get(ajaxurl, { action:'analytify_load_default_system', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>"  },function(data){

									$('.analytify_system_stats_boxes_wraper').html(data).parent().removeClass("stats_loading");

								});
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
							<h3><?php esc_html_e( 'How people are finding you (keywords)', 'wp-analytify' ); ?></h3>
							<div class="analytify_status_header_value keywords_total">
								<span class="analytify_medium_f"><?php esc_html_e( 'Total Visits', 'wp-analytify' ); ?></span>
							</div>
							<div class="analytify_top_keywords_detials analytify_tp_btn">
								<?php do_action( 'analytify_after_top_keyword_text' ) ?>
							</div>
						</div>
						<div class="analytify_status_body stats_loading">
							<script>
							//<![CDATA[

							jQuery( function($) {
								$.get(ajaxurl, { action:'analytify_load_default_keyword', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>"  },function(data){

									var data_array = $.parseJSON(data);
									$(".keywords_total").append( data_array.total_stats );
									$('.analytify_keyword_stats_boxes_wraper').html( data_array.body ).parent().removeClass("stats_loading");

								});
							});
							//]]>
							</script>
							<div class="analytify_keyword_stats_boxes_wraper"></div>
						</div>
						<div class="analytify_status_footer">
							<span class="analytify_info_stats"><?php esc_html_e( 'Listing your ranked keywords', 'wp-analytify' ); ?></span>

						</div>
					</div>
				<?php endif ?>
				<!-- Enf of Keywords Stats -->

				<div class="analytify_column">
						<div class="analytify_half analytify_left_flow">

							<!-- SocialMedia Statistics -->
							<?php if ( in_array( 'show-social-dashboard', $selected_stats ) ) :  ?>
								<div class="analytify_general_status analytify_status_box_wraper">
									<div class="analytify_status_header analytify_header_adj">
										<h3><?php esc_html_e( 'Social Media', 'wp-analytify' ); ?></h3>
										<div class="analytify_top_keywords_detials analytify_tp_btn">
											<?php do_action( 'analytify_after_top_social_media_text' ) ?>
										</div>
										<div class="analytify_status_header_value social_total">
											<span class="analytify_medium_f"><?php esc_html_e( 'Total Visits', 'wp-analytify' ); ?></span>
										</div>
									</div>
									<div class="analytify_status_body stats_loading">

										<script>
										//<![CDATA[

										jQuery( function($) {
											$.get(ajaxurl, { action:'analytify_load_default_social_media', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>"   },function(data){

												var data_array = $.parseJSON(data);
												$(".social_total").append( data_array.total_stats );
												$('.analytify_social_media_stats_boxes_wraper').html( data_array.body ).parent().removeClass("stats_loading");


											});
										});
										//]]>
										</script>
										<div class="analytify_social_media_stats_boxes_wraper"></div>

									</div>
									<div class="analytify_status_footer">
										<span class="analytify_info_stats"><?php esc_html_e( 'See how many users are coming to your site from Social media', 'wp-analytify' ); ?></span>

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
										<h3><?php esc_html_e( 'Top Referrers', 'wp-analytify' ); ?></h3>
										<div class="analytify_top_keywords_detials analytify_tp_btn">
											<?php do_action( 'analytify_after_top_reffers_text' ) ?>
										</div>
										<div class="analytify_status_header_value  reffers_total">
											<span class="analytify_medium_f"><?php esc_html_e( 'Total Visits', 'wp-analytify' ); ?></span>
										</div>
									</div>
									<div class="analytify_status_body stats_loading">

										<script>
										//<![CDATA[

										jQuery( function($) {
											$.get(ajaxurl, { action:'analytify_load_default_reffers', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>"  },function(data){

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
										<span class="analytify_info_stats"><?php esc_html_e( 'who are the strong Referrers to your site ?? See above', 'wp-analytify' ); ?></span>

									</div>
								</div>
							<?php endif; ?>
						</div>
				</div>


				<!-- Page Statistics -->
				<?php if ( in_array( 'show-page-stats-dashboard', $selected_stats ) ) :  ?>
					<div class="analytify_general_status analytify_status_box_wraper">
						<div class="analytify_status_header">
							<h3><?php esc_html_e( 'What\'s happening when users come to your site.', 'wp-analytify' ); ?></h3>
							<div class="analytify_top_page_detials analytify_tp_btn">
								<?php do_action( 'analytify_after_top_page_stats_text' ) ?>
							</div>
						</div>
						<div class="analytify_status_body stats_loading">
							<script>
							//<![CDATA[

							jQuery( function($) {
								$.get(ajaxurl, { action:'analytify_load_default_page', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>"  },function(data){

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

				<?php do_action( 'wp_analytify_view_ajax_error', $start_date, $end_date, $dashboard_profile_ID ) ?>
				<?php do_action( 'wp_analytify_view_404_error', $start_date, $end_date, $dashboard_profile_ID ) ?>
				<?php do_action( 'wp_analytify_view_javascript_error', $start_date, $end_date, $dashboard_profile_ID ) ?>

		</div>
		<?php
		} else {
			esc_html_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-analytify' );
		}
	} else {

		esc_html_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify' );
	}
?>
