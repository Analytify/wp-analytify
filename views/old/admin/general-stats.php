<?php

/*
 * View of General Statistics
 */

function pa_include_general( $current, $stats , $compare_stats , $date_different ) {

?>
<div class="data_boxes">
	<div class="data_boxes_title"><?php esc_html_e( 'General Statistics', 'wp-analytify' ); ?> <div class="arrow_btn"></div></div>
	<div class="data_container">
	<div class="grids_auto_size">
				<div class="grid_title">
					<?php esc_html_e( 'SESSIONS', 'wp-analytify' ); ?>
				</div>
				<?php echo  number_format( ( $stats->totalsForAllResults['ga:sessions'] - $compare_stats->totalsForAllResults['ga:sessions'] ) / ( $stats->totalsForAllResults['ga:sessions'] + $compare_stats->totalsForAllResults['ga:sessions'] ) * 100 )."%" ?>

			<div class="grid_data cen">
			  <div class="data_value"><?php echo number_format( $stats->totalsForAllResults['ga:sessions'] ); ?></div>
			</div>
			 <a href="<?php echo admin_url( 'admin.php?page=analytify-dashboard&show=sessions' )  ?>">More Details</a>
			<div class="grid_footer cen">
				<?php esc_html_e( 'Total number of Sessions within the date range. A session is the period time a user is actively engaged with your website, app, etc.', 'wp-analytify' ); ?>
			</div>
		</div>
	<div class="grids_auto_size">
			<div class="grid_title">
				<?php esc_html_e( 'USERS', 'wp-analytify' ); ?>
			</div>
			<?php echo number_format( ( $stats->totalsForAllResults['ga:users'] - $compare_stats->totalsForAllResults['ga:users'] ) / ( $stats->totalsForAllResults['ga:users'] + $compare_stats->totalsForAllResults['ga:users'] ) * 100 )."%"?>
			<div class="grid_data cen">
			  <div class="data_value"><?php echo number_format( $stats->totalsForAllResults['ga:users'] ); ?></div>
			</div>
			 <a href="<?php echo admin_url( 'admin.php?page=analytify-dashboard&show=users' )  ?>">More Details</a>
			<div class="grid_footer cen">
				<?php esc_html_e( 'Users that have had at least one session within the selected date range. Includes both new and returning users.', 'wp-analytify' ); ?>
			</div>
	</div>
	<div class="grids_auto_size">
			<div class="grid_title"> <?php esc_html_e( 'BOUNCE RATE', 'wp-analytify' ); ?> </div>
			<div class="grid_data cen">
				<div class="data_value">
					<?php if ( $stats->totalsForAllResults['ga:entrances'] <= 0 ) { ?>
						0.00%
					<?php
} //$stats->totalsForAllResults['ga:entrances'] <= 0
else {
	echo number_format( $stats->totalsForAllResults['ga:bounceRate'], 2 );
?>%
<?php } ?>
				</div>
			</div>
			 <a href="<?php echo admin_url( 'admin.php?page=analytify-dashboard&show=bounce_rate' )  ?>">More Details</a>
				<?php echo  number_format( ($stats->totalsForAllResults['ga:bounceRate'] - $compare_stats->totalsForAllResults['ga:bounceRate']) / ($stats->totalsForAllResults['ga:bounceRate'] + $compare_stats->totalsForAllResults['ga:bounceRate']) * 100  ) ."%" ?>
				<div class="grid_footer cen">
					<?php esc_html_e( 'Bounce Rate is the percentage of single-page visits (i.e. visits in which the person left your site from the entrance page without interacting with the page).', 'wp-analytify' ); ?>
				</div>
	</div>
	  <div class="grids_auto_size">
			<div class="grid_title"> <?php esc_html_e( 'AVG TIME ON SITE' , 'wp-analytify' );?> </div>
		<div class="grid_data cen">
			<div class="data_value">
				<?php
				if ( $stats->totalsForAllResults['ga:sessions'] <= 0 ) {
				?>
				  00:00:00
				<?php
				} //$stats->totalsForAllResults['ga:sessions'] <= 0
				else {
				?>
				<?php
				echo $current->pa_pretty_time( $stats->totalsForAllResults['ga:avgSessionDuration'] );
				?>
				<?php } ?>
			</div>
		</div>
				<div class="grid_footer cen"> <?php esc_html_e( 'The amount of time someone spends on your site.' , 'wp-analytify' ); ?> </div>
	</div>
		<div class="grids_auto_size">
			<div class="grid_title"> <?php esc_html_e( 'AVERAGE PAGES' , 'wp-analytify' ); ?> </div>
				<div class="grid_data cen">
					<div class="data_value">
						<?php
						if ( $stats->totalsForAllResults['ga:sessions'] <= 0 ) {
						?>
						0.00
						<?php
						} //$stats->totalsForAllResults['ga:sessions'] <= 0
						else {
						?>
						<?php
						echo number_format( round( $stats->totalsForAllResults['ga:pageviews'] / $stats->totalsForAllResults['ga:sessions'], 2 ), 2 );
						?>
						<?php } ?>
					</div>
				</div>
					  <div class="grid_footer cen">
							<?php esc_html_e( 'Pages/Session is the average number of pages viewed during a session. Repeated views of a single page are counted.', 'wp-analytify' ); ?>
					  </div>
		</div>
	 <div class="grids_auto_size">
		<div class="grid_title"> <?php esc_html_e( 'PAGE VIEWS', 'wp-analytify' ); ?> </div>
			<div class="grid_data cen">
				<div class="data_value">

					<?php
					if ( $stats->totalsForAllResults['ga:pageviews'] <= 0 ) {
						echo '0';
					} else {
						echo $current->wpa_number_format( $stats->totalsForAllResults['ga:pageviews'] );
					}
					?>
				</div>
			</div>
			  <div class="grid_footer cen">
				<?php esc_html_e( 'Pageviews is the total number of pages viewed. Repeated views of a single page are counted.', 'wp-analytify' );?>
			  </div>
	</div>
	<div class="grids_auto_size">
		<div class="grid_title">
			<?php esc_html_e( ' % NEW SESSIONS', 'wp-analytify' ); ?>
		</div>
		<div class="grid_data cen">
			<div class="data_value">
				<?php

					$total_sessions   = $stats->totalsForAllResults['ga:sessions'];
					$newusers         = $stats->totalsForAllResults['ga:newUsers'];

				if ( $total_sessions > 0 ) {
					echo number_format( round( ($newusers / $total_sessions) * 100, 2 ), 2 );
				} else {
					echo '0';
				}
				?>
				%
			  </div>
		</div>
		  <div class="grid_footer cen">
			<?php esc_html_e( 'Pages/Session is the average number of pages viewed during a session. Repeated views of a single page are counted.', 'wp-analytify' );?>
		  </div>
	</div>
	<div class="grids_auto_size">
		<div class="grid_title">  <?php esc_html_e( 'NEW/RETURNING VISITORS' , 'wp-analytify' ); ?> </div>
		<div class="grid_data cen">
			<div class="data_value">
				<?php
				if ( isset( $stats->totalsForAllResults ) ) {
					if ( ! empty( $stats['rows'] ) ) :
						$returning = $stats->totalsForAllResults['ga:sessions'] - $stats->totalsForAllResults['ga:newUsers'];
				?>
				<script type="text/javascript">

				  google.load("visualization", "1", {packages:["corechart"],'callback': drawChart});

				  function drawChart() {
					var data =  google.visualization.arrayToDataTable([
					  ['Visitors', 'Visitors Records'],
					  ['New Visitors',<?php echo $stats->totalsForAllResults['ga:newUsers']; ?>],
					  ['Return Visitors',<?php echo $returning; ?>],
					]);

					var options = {
					  is3D: true,
					  legend:'none',
					  colors: ['#4fca74', '#0E9236']
					};

					var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
					formatter.format(data, 1);

					var chart = new google.visualization.PieChart(document.getElementById('visitorschart'));
					chart.draw(data, options);
				  }

				</script>
				<div id="visitorschart" style="width: 99%; height:150px;"></div>
				<span class="visitor-stats-new"><span class="font-sizing"><?php esc_html_e( 'New ', 'wp-analytify' );?>(<?php echo $current->wpa_number_format( $stats->totalsForAllResults['ga:newUsers'] ); ?>)</span> </span>
				<span class="visitor-stats-ret"><span class="font-sizing"><?php esc_html_e( 'Returning', 'wp-analytify' );?> (<?php echo $current->wpa_number_format( $returning ); ?>)</span> </span>
				<?php
				endif;
				}
					?>
			</div>
		</div>
	</div>
	</div>

	<div class="data_boxes_footer">
				<span class="blk">
					<span class="dot"></span>
					<span class="line"></span>
				</span>
				<span class="information-txt">
					<?php esc_html_e( 'Did you know that total time on your site is' , 'wp-analytify' ); ?>
					<?php
					  echo $current->pa_pretty_time( $stats->totalsForAllResults['ga:sessionDuration'] );
					?>
				</span>
	  </div>
	</div>

<?php } ?>
