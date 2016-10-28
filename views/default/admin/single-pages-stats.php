<?php


function pa_single_pages_stats( $current, $page_stats ) {
	?>

	<div class="data_boxes">
		<div class="data_boxes_title"><?php esc_html_e( 'What\'s happening when users come to this page.', 'wp-analytify' ); ?> <div class="arrow_btn"></div></div>
		<div class="data_container">
			<?php
			$entrance_num = 0;

			if ( ! empty( $page_stats['rows'] ) ) { // print_r($page_stats["rows"]);?>

			<script type="text/javascript">
			google.load("visualization", "1", { packages:["corechart"],'callback': drawChart} );

			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Links', 'Page Views', 'Page exits'],
					<?php
					foreach ( $page_stats['rows'] as $p_stats ) { ?>
						['<?php echo $p_stats[0]; ?>',
						<?php echo $p_stats[2]; ?>,
						<?php echo $p_stats[3];?>
					],
					<?php } ?>
				]);
				var options = {
					hAxis: {titleTextStyle: {color: 'red'}}
				};

				var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
				formatter.format( data, 1);
				formatter.format( data, 2);

				var chart = new google.visualization.ColumnChart(document.getElementById('pages_chart'));
				chart.draw(data, options);
			}
			</script>
			<div id="pages_chart" style="width: 900px; height: 400px; margin:0 auto;"></div>
			<div style="text-align:center; padding: 40px 0px;"><span class="large-count"><?php echo $current->wpa_number_format( $page_stats->totalsForAllResults['ga:exits'] );?></span> <?php esc_html_e( 'Total Exits', 'wp-analytify' ); ?></div>
			<?php

			foreach ( $page_stats['rows'] as $p_stats ) {
				$entrance_num  = $p_stats[1];
				// $exit_path  = $p_stats[0];
			}
			}
		?>
		</div>
			<div class="data_boxes_footer">
				<span class="blk">
					<span class="dot"></span>
					<span class="line"></span>
				</span>
				<span class="information-txt"><?php esc_html_e( 'Did you know that', 'wp-analytify' )?> <b><?php echo $current->wpa_number_format( $entrance_num ); ?></b> <?php esc_html_e( 'people landed at this page directly.', 'wp-analytify' )?></span>
			</div>
		</div>
<?php
}
?>
