<?php

function wpa_campaigns( $current, $campaign_stats ) {
?>

	 <div class="data_boxes">
		<div class="data_boxes_title"><?php esc_html_e( 'Campaigns Statistics', 'wp-analytify' ); ?><div class="arrow_btn"></div></div>
			<div class="data_container">

				<?php
					/*
				echo '<pre>';
                        print_r($campaign_stats);
					echo '</pre>';*/

					$i = 0;
				if ( ! empty( $campaign_stats['rows'] ) ) { ?>

		                <?php foreach ( $campaign_stats['rows'] as $c_stats ) {
		                		$i++;
		                	?>
						<div class="pa_campaigns_statistics">
							<div class="pa-tdo-left">
								<div class="pa_data_text_wraper">
		             				<span class="large-count"><?php echo $c_stats[0]; ?></span>
								</div>
							</div>

							<div class="pa-tdo-right" id="pa-tdo-right">
								<div class="pa-bigtext"><span class="count-visits"><?php echo number_format( $c_stats[1] ); ?></span><span class="source"><a href="#nogo"><?php esc_html_e( 'Sessions', 'wp-analytify' ); ?></a></span></div>
								<div class="pa-bigtext"><span class="count-visits">
										<?php
										if ( $c_stats[1] > 0 ) {
											echo number_format( round( ($c_stats[2] / $c_stats[1]) * 100, 2 ), 2 );
										} else {
											echo '0';
										}
										?>
									</span><span class="source"><a href="#nogo"><?php esc_html_e( '% New Sessions', 'wp-analytify' ); ?></a></span></div>
								<div class="pa-bigtext"><span class="count-visits"><?php echo $c_stats[2]; ?></span><span class="source"><a href="#nogo"><?php esc_html_e( 'New Users', 'wp-analytify' ); ?></a></span></div>
							</div>
							<div class="pa-tdo-rights" id="pa-tdo-rights">
								<div class="pa-bigtext"><span class="count-visits">
										<?php if ( $c_stats[4] <= 0 ) { ?>
													0.00%
							                    <?php
} else {
	echo number_format( round( ($c_stats[3] / $c_stats[4]) * 100, 2 ), 2 );
?>%
<?php } ?>
									</span><span class="source"><a href="#nogo"><?php esc_html_e( 'Bounce Rate', 'wp-analytify' ); ?></a></span>
								</div>
								<div class="pa-bigtext">
									<span class=" count-visits">
										<?php
										if ( $c_stats[1] <= 0 ) {
					                        ?>
											0.00
					                        <?php
										} //$stats->totalsForAllResults['ga:sessions'] <= 0
										else {
					                        ?>
					                        <?php
											echo number_format( round( $c_stats[6] / $c_stats[1], 2 ), 2 );
					                        ?>
					                        <?php } ?>
									</span><span class="source"><a href="#nogo"><?php esc_html_e( 'Pages / Session', 'wp-analytify' ); ?></a></span>
								</div>
								<div class="pa-bigtext"><span class="count-visits"><?php echo $current->pa_pretty_time( $c_stats[5] ); ?></span><span class="source"><a href="#nogo"><?php esc_html_e( 'Avg. Session duration', 'wp-analytify' ); ?></a></span></div>
							</div>
						</div>
				  		<?php } ?>


				<?php } else {

					esc_html_e( 'No Campaigns.', 'wp-analytify' );

} ?>

			</div>
		<div class="data_boxes_footer">
			<span class="blk">
				<span class="dot"></span>
				<span class="line"></span>
			</span>
			<span class="information-txt"><?php esc_html_e( 'You are running '. $i . ' Campaigns in total.', 'wp-analytify' ); ?></span>
		</div>
</div>
<?php } ?>
