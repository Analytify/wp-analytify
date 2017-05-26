<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * View of General Statistics
 */

 // add_filter( 'wpanalytify_data',  function ($data){
 // 	$data1 = array(
 // 		'New' => 123,
 // 		'Returning' => 321,
 // 	);
 // 	return array_merge($data, $data1);
 // });

function fetch_general_stats( $current, $current_stats, $device_category_stats, $compare_stats , $date_different, $new_returning_stats ) {


	$results = $current_stats->totalsForAllResults;

	$new_users 				= $new_returning_stats->rows[0][1];
	$returning_users 	= $new_returning_stats->rows[1][1];

	$compare_results = $compare_stats->totalsForAllResults;

	$device_data = '';

	foreach( $device_category_stats->rows as $row ){
		if (!empty($device_data)) {
		$device_data .= ",";
		}

		if ( 'mobile' ==  $row[0] ) {
			$device_data .= json_encode(array("name" =>  __( 'Mobile', 'wp-analytify' ) , "value" => $row[1])) ;
		} elseif ( 'tablet' == $row[0] ) {
			$device_data .= json_encode(array("name" =>  __( 'Tablet', 'wp-analytify' ) , "value" => $row[1])) ;
		} elseif ( 'desktop' == $row[0] ) {
			$device_data .= json_encode(array("name" =>  __( 'Desktop', 'wp-analytify' ) , "value" => $row[1])) ;
		}

	}

		if ( $device_data == "" ) {
			$device_data = 0 ;
		}

	//var_dump($device_category_stats->rows);

	 ob_start();
	?>

	<script>
	jQuery(document).ready(function ($) {

			// configure for module loader
			require.config({
					paths: {
							echarts: 'js/dist/'
					}
			});

			require(
					[
							'echarts',
							'echarts/chart/pie', // require the specific chart type
					],
					function (ec) {
							// Initialize after dom ready
							//



							var new_returing_graph_option = {
		              tooltip : {
		                  trigger: 'item',
		                  formatter: "{b} {a} : {c} ({d}%)"
		              },
		              color: [
		                  '#03a1f8', '#00c853'
		              ],
		              legend: {
		                  orient: 'horizontal',
		                  y: 'bottom',
		                  data: ['<?php _e( 'New', 'wp-analytify') ?>','<?php _e( 'Returning', 'wp-analytify') ?>']
		              },
		              series : [
		                  {
		                      name: 'VISITORS',
		                      type: 'pie',
													smooth: true,
		                      roseType : 'radius',
		                      radius : [20, 60],
		                      center: ['50%', '42%'],
		                      data:[
		                          {name:'<?php _e( 'New', 'wp-analytify') ?>', value:'<?php echo $new_users; ?>' },
		                          {name:'<?php _e( 'Returning', 'wp-analytify') ?>', value:'<?php echo $returning_users; ?>'}
		                      ]
		                  }
		              ]
		          };
							var user_device_graph_option= {
									tooltip : {
											trigger: 'item',
											formatter: "{a} <br/>{b} : {c} ({d}%)"
									},
									color: [
											'#444444', '#ffbc00', '#ff5252'
									],
									legend: {
											x : 'center',
											y : 'bottom',
											data:['<?php _e( 'Mobile', 'wp-analytify' ) ?>','<?php _e( 'Tablet', 'wp-analytify' ) ?>','<?php _e( 'Desktop', 'wp-analytify' ) ?>']
									},

									series : [
											{
													name:'<?php _e( "User Devices", "wp-analytify" ) ?>',
													type:'pie',
													smooth: true,
													radius : [20, 60],
													center : ['55%', '42%'],
													roseType : 'radius',
													label: {
															normal: {
																	show: false
															},
															emphasis: {
																	show: false
															}
													},
													lableLine: {
															normal: {
																	show: false
															},
															emphasis: {
																	show: false
															}
													},
													data:[
															<?php echo $device_data; ?>
													]
											}
									]
							};


						if ( <?php echo $new_users; ?> == 0 ) {

							$("#analytify_new_returing_graph").html('<div class="analytify_general_stats_value">0</div><p>No Activity Found</p>');

						} else {

							var new_returing_graph = ec.init(document.getElementById('analytify_new_returing_graph'));
							new_returing_graph.setOption(new_returing_graph_option);

							window.onresize = function () {
									new_returing_graph.resize();
							}

						}

						function notJson(str) {
							try {
								JSON.parse(str);
							} catch (e) {
								return false;
							}
							return true;
						}

						if ( notJson (<?php echo $device_data ?>) ) {

							$("#analytify_user_device_graph").html('<div class="analytify_general_stats_value">0</div><p>No Activity Found</p>');

						} else {

							var user_device_graph = ec.init(document.getElementById('analytify_user_device_graph'));
							user_device_graph.setOption(user_device_graph_option);

						}






					}
			);

	});
	</script>

	<div class="analytify_general_status_boxes">
			<h4><?php esc_html_e( 'Sessions', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:sessions'] ); ?></div>
			<p><?php esc_html_e( 'Total number of Sessions within the date range. A session is the period time a user is actively engaged with your website. app. etc.', 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:sessions'],  $compare_results['ga:sessions'], $date_different ) ?>
	</div>

	<div class="analytify_general_status_boxes">
			<h4><?php esc_html_e( 'Visitors', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:users'] ); ?></div>
			<p><?php esc_html_e( 'Users that have had at least one session within the selected date range. Includes both new and returning users.', 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:users'], $compare_results['ga:users'], $date_different );?>
	</div>

	<div class="analytify_general_status_boxes">
			<h4><?php esc_html_e( 'Page views', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:pageviews'] ); ?></div>
			<p><?php esc_html_e( 'Pageviews is the total number of pages viewed. Repeated views of a single page are counted.', 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:pageviews'], $compare_results['ga:pageviews'], $date_different );?>
	</div>

	<div class="analytify_general_status_boxes">
			<h4><?php esc_html_e( 'Avg. time on site', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_time( $results['ga:avgSessionDuration'] ); ?></div>
			<p><?php esc_html_e( 'The amount of time a user spends on your site.', 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:avgSessionDuration'], $compare_results['ga:avgSessionDuration'], $date_different );?>
	</div>

	<div class="analytify_general_status_boxes">
			<h4><?php esc_html_e( 'Bounce Rate', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:bounceRate'] ); ?><span class="analytify_xl_f">%</span></div>
			<p><?php esc_html_e( "Bounce Rate is the percentage of single-page visits (i.e. visits in which the person left your site from the entrance page without interacting with the page ).", 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:bounceRate'], $compare_results['ga:bounceRate'], $date_different, 'bounce_rate' );?>
	</div>

	<div class="analytify_general_status_boxes">
			<h4><?php esc_html_e( 'pages/session', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:pageviewsPerSession'], 2 ); ?></div>
			<p><?php esc_html_e( 'Pages/Session is the average number of pages viewed during a session. Repeated views of a single page are counted.', 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:pageviewsPerSession'], $compare_results['ga:pageviewsPerSession'], $date_different );?>
	</div>

	<div class="analytify_general_status_boxes pad_b_0">
			<h4><?php esc_html_e( '% New sessions', 'wp-analytify' ); ?></h4>
			<div class="analytify_general_stats_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $results['ga:percentNewSessions'] ); ?>%</div>
			<p><?php esc_html_e( 'Pages/Session is the average number of pages viewed during a session. Repeated views of a single page are counted.', 'wp-analytify' ); ?></p>
			<?php get_compare_stats( $results['ga:percentNewSessions'], $compare_results['ga:percentNewSessions'], $date_different );?>
	</div>

	<div class="analytify_general_status_boxes pad_b_0">
			<h4><?php esc_html_e( 'New vs Returning visitors', 'wp-analytify' ); ?></h4>
			<div id="analytify_new_returing_graph" style="height:240px"></div>
			<!-- <div class="analytify_general_status_footer_info">
					<span class="analytify_green analytify_info_value">20.7%</span> 31 days ago
			</div> -->
	</div>
	<div class="analytify_general_status_boxes pad_b_0">
			<h4><?php esc_html_e( 'Devices of visitors', 'wp-analytify' ); ?></h4>
			<div id="analytify_user_device_graph" style="height:240px"></div>
			<!-- <div class="analytify_general_status_footer_info">
					<span class="analytify_green analytify_info_value">20.7%</span> 31 days ago
			</div> -->
	</div>

	<?php
	$body = ob_get_clean();

	echo json_encode( array(
		"message" => WPANALYTIFY_Utils::pretty_time( $results['ga:sessionDuration'] ) ,
		"body"    => $body
	) ) ;
}

function get_compare_stats( $results, $compare_results, $date_different, $name='' ) {

	if ( $date_different == 0 ) { return; }

	$compare = number_format( ( ( $results - $compare_results ) / $compare_results ) * 100, 2 );

	if ( 'bounce_rate' === $name ) {
		$class   = $compare < 0 ? 'analytify_green' : 'analytify_red';
	} else {
		$class   = $compare > 0 ? 'analytify_green' : 'analytify_red';
	}

	echo '<div class="analytify_general_status_footer_info">
			<span class="' . $class . '  analytify_info_value"> ' . $compare . ' %</span> ' . $date_different . __( ' ago', 'wp-analytify' ) . '
	</div>';
}
?>
