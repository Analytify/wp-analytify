<?php
/**
 * Analytify Dashboard file.
 *
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

$wp_analytify   = new WP_Analytify();

$start_date_val = strtotime( '- 7 days' );
$end_date_val   = strtotime( 'now' );
$start_date     = date( 'Y-m-d', $start_date_val );
$end_date       = date( 'Y-m-d', $end_date_val );

if ( isset( $_POST['view_data'] ) ) {

	$s_date   = $_POST['st_date'];
	$ed_date  = $_POST['ed_date'];

}

if ( isset( $s_date ) ) {
	$start_date = $s_date ;
}

if ( isset( $ed_date ) ) {
 $end_date = $ed_date;
}


$date1 = date_create( $start_date );
$date2 = date_create( $end_date );
$diff  = date_diff($date2 ,$date1);

$date = strtotime($start_date. $diff->format("%R%a days") );
$date =  date( 'Y-m-d', $date );

$compare_start_date =  $date;
$compare_end_date  =   $start_date  ;


// Fetch Dashboard Profile ID.
$dashboard_profile_ID = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );

// Delete the cache.
if ( 'on' === $wp_analytify->settings->get_option( 'delete_dashboard_cache','wp-analytify-dashboard' ) ) {
	delete_dashboard_transients( $dashboard_profile_ID, $start_date, $end_date ); }

?>
<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url( '../assets/old/images/wp-analytics-logo.png', __FILE__ );?>" alt=""></span>
		<?php printf( esc_html__( '%1$s Dashboard', 'wp-analytify' ), 'WP Analytify Plugin' ); ?>
	</h2>

		<?php

		if ( WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection( 'Analytify' ) ) { return; }

		/*
		* Check with roles assigned at dashboard settings.
		*/
		$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );

		if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

			$acces_token  = get_option( 'post_analytics_token' );
			if ( $acces_token ) {

			?>
				<div class="dashboard-option">
					<form  action="<?php echo admin_url( 'admin.php?page=analytify-dashboard') ?>" method="post" id="dashboard-options-form">
						<select  name="show"  id="dashboard-options" >
							<option value="">Dashboard</option>
							<option value="sessions">SESSIONS</option>
							<option value="users">Users</option>
							<option value="bounce-rate">BOUNCE RATE</option>
						</select>
					</form>
				</div>

			<div id="col-container">
				<div class="metabox-holder">
			  <div class="postbox" style="width:100%;">
				  <div id="main-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox ">
					  <div class="handlediv" title="Click to toggle"><br />
					  </div>
					  <h3 class="hndle">
						<span>
						<?php

						echo sprintf( esc_html__( 'Complete Statistics of the Site (%1$s) and profile view (%4$s) Starting From %2$s to %3$s', 'wp-analytify' ), WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ), date( 'jS F, Y', strtotime( $start_date ) ), date( 'jS F, Y', strtotime( $end_date ) ), WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'name' ) );

						?>
						</span>
					  </h3>
					  <div class="inside">
						<div class="pa-filter">
						  <form action="" method="post">
							<input type="text" id="st_date" name="st_date" value="<?php echo $start_date; ?>"> to
							<input type="text" id="ed_date" name="ed_date" value="<?php echo $end_date; ?>">
							<input type="submit" id="view_data" name="view_data" value="View Stats" class="button-primary btn-green">
						  </form>
						</div>

						<?php

						// $show_settings = array();
						// $show_settings = get_option('dashboard_panels');
						// Real time stats //
						if ( has_action( 'wp_analytify_view_real_time_stats' ) ) {

							do_action( 'wp_analytify_view_real_time_stats' );

						}

						// End Real time stats //
						?>
						<div>
							<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.bundle.min.js"></script>

							<canvas id="chart" height="400" width="650"></canvas>

							<?php

							$start_date_previous_year = '2015-01-01';
							$end_date_previous_year = '2015-12-31';


								$last_year = $wp_analytify->pa_get_analytics_dashboard( 'ga:users', $start_date_previous_year, $end_date_previous_year, 'ga:month,ga:nthMonth', false , false, 12 );
								$_last_year  = '';
								foreach ($last_year['rows'] as $k) {
									// echo $k[2]."<br>";
									$_last_year[] = $k['2'];
								}


								$start_date_this_year = '2016-01-01';
								$end_date_this_year = '2016-07-16';


								$this_year = $wp_analytify->pa_get_analytics_dashboard( 'ga:users', $start_date_this_year, $end_date_this_year, 'ga:month,ga:nthMonth', false , false, 12 );
								$_this_year  = '';

								foreach ($this_year['rows'] as $k) {
									$_this_year[] = $k['2'];
								}

							 ?>

							<script>

							var lineChartData = {
								labels: ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
								datasets: [
									{
										label: '2010 customers #',
										fillColor: '#382765',
										data: [2500, 1902, 1041, 610, 1245, 952]
									},
									{
										label: '2014 customers #',
										fillColor: '#7BC225',
										data: [3104, 1689, 1318, 589, 1199, 1436]
									}
								]
							};

							var ctx = document.getElementById("chart").getContext("2d");
							// var myChart = new Chart(ctx, {
							// 	type: "bar",
							// 	data: lineChartData,
							//
							// });
							var randomScalingFactor = function() {
								return (Math.random() > 0.5 ? 1.0 : -1.0) * Math.round(Math.random() * 100);
							};
							var randomColorFactor = function() {
								return Math.round(Math.random() * 255);
							};
							var randomColor = function() {
								return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
							};

							var _last_year = <?php echo json_encode($_last_year ); ?>;
							var _this_year = <?php echo json_encode($_this_year ); ?>;

							var barChartData = {
								labels: ["January", "February", "March", "April", "May", "June", "July" , "Augest" , "September" , "October" , "November" , "December"],
								datasets: [{
									label: 'Last Year',
									backgroundColor: [randomColor(), randomColor(), randomColor(), randomColor(), randomColor(), randomColor(), randomColor()],
									data: _last_year
								}, {
									label: 'This Year',
									backgroundColor: "rgba(151,187,205,0.5)",
									data: _this_year
								}]

							};
							window.myBar = new Chart(ctx, {
								 type: 'bar',
								data: barChartData,
								options: {
									responsive: true,
									hoverMode: 'label',
									hoverAnimationDuration: 400,
									stacked: false,
									title:{
										display:true,
										text:"Chart.js Bar Chart - Multi Axis"
									},

								}
							});
							</script>
						</div>

						 <?php // General stats // ?>

							<div id="wp-analytify-general-stats-box"><img class="dashboard-loader"  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_general_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" , compare_start_date : "<?php echo $compare_start_date ?>" , compare_end_date : "<?php echo $compare_end_date ?>" , date_different: "<?php echo $diff->format("%a days") ?>"  },function(data){

										$('#wp-analytify-general-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End General stats //
							// Top Pages stats //
							?>
							<div id="wp-analytify-top-pages-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_top_pages', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-top-pages-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End Top Pages stats //
							// Country stats //
							?>
							<div id="wp-analytify-country-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_country_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-country-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End Country stats //
							// City stats //
							?>
							<div id="wp-analytify-city-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_city_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-city-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End City stats //
							// Keywords stats //
							?>
							<div id="wp-analytify-keyword-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_keyword_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-keyword-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End Keywords stats //
							// Social stats //
							?>
							<div id="wp-analytify-social-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_social_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-social-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End Social stats //
							// Browser stats //
							?>
							<div id="wp-analytify-browser-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_browser_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-browser-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End Browser stats //
							// End OS stats //
							?>
							<div id="wp-analytify-os-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_os_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-os-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End OS stats //
							// Start Mobile stats //
							if ( has_action( 'wp_analytify_load_mobile_stats' ) ) {

								do_action( 'wp_analytify_load_mobile_stats', $start_date, $end_date, $dashboard_profile_ID );

							}

							// End Mobile stats //
							// Referral stats //
							?>
							<div id="wp-analytify-referrer-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_referrer_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-referrer-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							// End Referral stats //
							// Exit stats //
							?>
							<div id="wp-analytify-page-exit-stats-box"><img  class="dashboard-loader" src="<?php echo plugins_url( 'assets/old/images/loading.gif',  dirname( __FILE__ ) );?>"></div>
							<script>
							//<![CDATA[

								jQuery( function($) {
									$.get(ajaxurl, { action:'analytify_load_page_exit_stats', dashboard_profile_ID:"<?php echo $dashboard_profile_ID ;?>", start_date:"<?php echo $start_date ;?>", end_date: "<?php echo $end_date ;?>" },function(data){

										$('#wp-analytify-page-exit-stats-box').html(data);
									});
								});
							//]]>
							</script>

							<?php

							if ( has_action( 'wp_analytify_view_miscellaneous_error' ) ) {

								do_action( 'wp_analytify_view_miscellaneous_error' ,  $start_date, $end_date, $dashboard_profile_ID );
							}

							// End Exit stats //
							?>
					  	</div>
				  </div>
				</div>
			  </div>
				</div>
			</div>

			<?php
			} else {
				esc_html_e( 'You must be authenticate to see the Analytics Dashboard.', 'wp-analytify' );
			}
		} else {

			esc_html_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify' );
		}

?>
</div>
<script type="text/javascript">

jQuery(document).ready(function ($) {

	$("#st_date").datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth : true,
			changeYear : true,
						beforeShow: function() {
							 $('#ui-datepicker-div').addClass('mycalander');
					 },
			yearRange: '-9y:c+nn',
			defaultDate: "<?php echo $start_date;?>"
		});

	$("#ed_date").datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth : true,
			changeYear : true,
						beforeShow: function() {
							 $('#ui-datepicker-div').addClass('mycalander');
					 },
			yearRange: '-9y:c+nn',
			defaultDate: "<?php echo $end_date; ?>"
		});
});

jQuery(window).resize(function(){
	drawChart();
	drawRegionsMap();
});
</script>
