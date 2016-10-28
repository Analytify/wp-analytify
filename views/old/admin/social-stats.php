<?php

/*
 * View of Social Statistics
 */

function pa_include_social( $current, $social_stats ) {
	?>
	<div class="data_boxes">
		<div class="data_boxes_title">
			<?php esc_html_e( 'Social Media Statistics', 'wp-analytify' ); ?>
			<div class="arrow_btn"></div>
		</div>
		<div class="data_container">

		<?php if ( ! empty( $social_stats['rows'] ) ) :  ?>

			<script type="text/javascript">
				google.load("visualization", "1", {packages:["corechart"],'callback': drawChart});
			  //google.setOnLoadCallback(drawChart);
			  function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Task', 'Referr stats'],
			  		<?php foreach ( $social_stats['rows'] as $s_stats ) :  ?>
			  		['<?php echo $s_stats[0]; ?>',
			  		<?php echo $s_stats[1]; ?>
					],
			  	<?php endforeach; ?>
				]);
				var options = {
					hAxis: {titleTextStyle: {color: 'red'}},
					legend: 'none',
				};
				var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
				formatter.format(data, 1);
				var chart = new google.visualization.ColumnChart(document.getElementById('social_chart'));
				chart.draw(data, options);
			  }
			</script>
			<div id="social_chart" style="width: 600px; height: 300px; margin:0 auto;"></div>

			<div class="names_grids">
				<?php foreach ( $social_stats['rows'] as $s_stats ) :  ?>
					<div class="stats">
						<div class="row-visits">
							<span class="large-count"><?php echo $current->wpa_number_format( $s_stats[1] ); ?></span> <?php esc_html_e( 'Visits', 'wp-analytify' ); ?>
						</div>
						<div class="visits-count">
							<i><?php echo $s_stats[0]; ?></i>
						</div>
					</div><?php endforeach; ?>
				</div>
			</div>

		<?php endif;  ?>

			<div class="data_boxes_footer">
				<span class="blk">
					<span class="dot"></span>
					<span class="line"></span>
				</span>
				<span class="information-txt"><?php esc_html_e( 'See how many users are coming to your site from Social media.', 'wp-analytify' );?></span>
			</div>
		</div><?php } ?>
