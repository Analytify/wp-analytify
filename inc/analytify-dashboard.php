<?php
$wp_analytify = new WP_Analytify();

$start_date_val =   strtotime("- 30 days"); 
$end_date_val   =   strtotime("now");
$start_date     =   date( "Y-m-d", $start_date_val);
$end_date       =   date( "Y-m-d", $end_date_val);

if( isset( $_POST["view_data"] ) ) {

	$s_date   = $_POST["st_date"];
	$ed_date  = $_POST["ed_date"];

}

if( isset( $s_date ) ) {
	$start_date = $s_date;
}

if( isset( $ed_date ) ) {
	$end_date = $ed_date;
}

?>
<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url('wp-analytify/images/wp-analytics-logo.png');?>" alt=""></span>
		<?php echo __( 'Analytify Dashboard', 'wp-analytify' ); ?>
	</h2>
	<?php

	$acces_token  = get_option( "post_analytics_token" );
	if( $acces_token ) {
	
	?>
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
										echo _e('Complete Statistics of the Site ', 'wp-analytify'); 
										echo _e(get_option("pt_webprofile_url"));
										echo _e(' Starting From ', 'wp-analytify'); 
										echo _e(date("jS F, Y", strtotime($start_date))); 
										echo _e(' to ', 'wp-analytify'); 
										echo _e(date("jS F, Y", strtotime($end_date))); 
										?>
								</span>
							</h3>
							<div class="inside">
								<div class="pa-filter">
									<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
										<input type="text" id="st_date" name="st_date" value="<?php echo $start_date; ?>">
										<input type="text" id="ed_date" name="ed_date" value="<?php echo $end_date; ?>">
										<input type="submit" id="view_data" name="view_data" value="View Data" class="button-primary btn-green">
									</form>
								</div>

								<a target="_blank" href="http://wp-analytify.com/upgrade-from-free" title="Upgrade to PRO to enjoy full features of Analytify.">
									<img class="gray-areas" src="<?php echo plugins_url('images/live-stats-preview.png', dirname(__FILE__) );?>" width="100%" height="auto" alt="Upgrade to PRO to enjoy full features of Analytify." />
								</a>

								<?php

								// General stats //

								$stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users', $start_date, $end_date);
								if ( isset( $stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/general-stats.php'; 
									pa_include_general($wp_analytify,$stats);
								}
								
								// End General stats //
								
								// Top Pages stats //
								$top_page_stats = $wp_analytify->pa_get_analytics_dashboard('ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', false, 5);
								if ( isset( $top_page_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/top-pages-stats.php'; 
									pa_include_top_pages_stats( $wp_analytify, $top_page_stats );
								}
								// End Top Pages stats //

								// Country stats //
								
								$country_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', false, 5);
								if ( isset( $country_stats->totalsForAllResults )) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/country-stats.php'; 
									pa_include_country($wp_analytify,$country_stats);
								}
							
								// End Country stats //

								$city_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:city', '-ga:sessions', false, 5);
								if ( isset( $city_stats->totalsForAllResults )) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/city-stats.php'; 
									pa_include_city($wp_analytify,$city_stats);
								}

								// Keywords stats //

								$keyword_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 10);
								if ( isset( $keyword_stats->totalsForAllResults )){
									include ANALYTIFY_ROOT_PATH . '/views/admin/keywords-stats.php'; 
									pa_include_keywords($wp_analytify,$keyword_stats);
								}

								// End Keywords stats //

								// Browser stats //

								$browser_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:browser,ga:operatingSystem', '-ga:sessions',false,5);
								if ( isset( $browser_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/browser-stats.php'; 
									pa_include_browser( $wp_analytify,$browser_stats );
								}
								
								// End Browser stats //
								
								$operating_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:operatingSystem,ga:operatingSystemVersion', '-ga:sessions', false, 5);
								if ( isset( $city_stats->totalsForAllResults )) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/os-stats.php'; 
									pa_include_operating($wp_analytify,$operating_stats);
								}

								$mobile_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:mobileDeviceInfo', '-ga:sessions', false, 5);
								if ( isset( $city_stats->totalsForAllResults )) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/mobile-stats.php'; 
									pa_include_mobile($wp_analytify,$mobile_stats);
								}

								// Referral stats //
								$referr_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 10);
								if ( isset( $referr_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH.'/views/admin/referrers-stats.php'; 
									pa_include_referrers($wp_analytify,$referr_stats);
								}

								// End Referral stats //


								// Exit stats //
								$page_stats = $wp_analytify->pa_get_analytics_dashboard('ga:entrances,ga:pageviews,ga:exits', $start_date, $end_date, 'ga:PagePath', '-ga:exits', false, 5);
								$top_page_stats = $wp_analytify->pa_get_analytics_dashboard('ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', false, 5);
								if ( isset( $page_stats->totalsForAllResults ) ) {
									include ANALYTIFY_ROOT_PATH . '/views/admin/pages-stats.php'; 
									pa_include_pages_stats( $wp_analytify, $page_stats );
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
	}
	else{
		print(_e( 'You must be authenticate to see the Analytics Dashboard.', 'wp-analytify' ));
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