<?php
// View of Browser Statistics
function pa_include_browser( $current, $browser_stats ) {
?>
<div class="data_boxes">
	<div class="data_boxes_title"><?php esc_html_e( 'Browsers Statistics', 'wp-analytify' ); ?><div class="arrow_btn"></div></div>
	<div class="data_container">
	<?php
	if ( ! empty( $browser_stats['rows'] ) ) { ?>
	  <script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"],'callback': drawChart});
					function drawChart() {

					  var data = google.visualization.arrayToDataTable([
								['Broswer (OS)', 'Sessions'],
									<?php
									foreach ( $browser_stats['rows'] as $b_stats ) { ?>
												  ['<?php echo $b_stats[0];?> (<?php echo $b_stats[1];?>)',  <?php echo $b_stats[2];?>],
													<?php } ?>
								]);

					  var options = {
									  hAxis: {titleTextStyle: {color: 'red'}},
									  legend: 'none',
									};
					  var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
						formatter.format(data, 1);

					  var chart = new google.visualization.ColumnChart(document.getElementById('broswer_chart'));
					  chart.draw(data, options);

					}
	  </script>
	  <div id="broswer_chart" style="width: 600px; height: 400px; margin:0 auto;"></div>
	<?php } ?>
	</div>
	<div class="data_boxes_footer">
	  <span class="blk">
		<span class="dot"></span>
		<span class="line"></span>
	  </span>
	  <span class="information-txt"><?php esc_html_e( 'listing statistics of top five browsers', 'wp-analytify' );?></span>
	</div>
</div>
<?php }