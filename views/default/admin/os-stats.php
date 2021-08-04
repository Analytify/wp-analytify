<?php
// View of Operating System wise Statistics
function pa_include_operating( $current, $operating_stats ) {
?>
<div class="data_boxes">
	<div class="data_boxes_title "><?php esc_html_e( 'Operating System Statistics', 'wp-analytify' ); ?> <div class="arrow_btn"></div></div>
		<div class="data_container">
			<?php
			if ( ! empty( $operating_stats['rows'] ) ) { ?>
			<div class="names_grids">
				<?php foreach ( $operating_stats['rows'] as $op_stats ) { ?>
						<div class="stats">
							<div class="row-visits">
								<span class="large-count"><?php echo number_format( $op_stats[2] ); ?></span>
							Visits
							</div>
							<div class="visits-count">
								<i><?php  echo $op_stats[0];?> (<?php  echo $op_stats[1];?>) </i>
							</div>
						</div>
				<?php } ?>

			</div>
			<?php } ?>
		</div>
	<div class="data_boxes_footer">
		<span class="blk">
			<span class="dot"></span>
			<span class="line"></span>
		</span>
		<span class="information-txt"><?php esc_html_e( 'Listing statistics of top five operating systems.', 'wp-analytify' ); ?></span>
	</div>
</div>
<?php }
