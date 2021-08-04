<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * View of Scroll Depth Statistics for Single Page
 */

function wpa_include_single_depth( $current, $stats ) {	
	$total_visits = WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:totalEvents'] );	?>

	<div class="analytify_general_status analytify_status_box_wraper">
		<div class="analytify_status_header">
			<h3><?php esc_html_e( 'Scroll Depth Reach', 'wp-analytify' ); ?></h3>
			<div class="analytify_status_header_value keywords_total">
				<span class="analytify_medium_f"><?php esc_html_e( 'Total Reach', 'wp-analytify' ); ?></span>
			</div>
		</div>

		<table class="analytify_bar_tables">
			<tbody>
      <?php      
      if ( isset( $stats['rows'] ) && $stats['rows'] > 0 ) : 
        $scroll_stats = $stats['rows'];

        // Sort array in ascending order of depth threshold
        usort( $scroll_stats, function( $a, $b ) {
          return $a[1] - $b[1];
        });
        
        foreach ( $scroll_stats as $row ): ?>
					<tr>
						<td>
							<?php	echo $row[1] . '%'; ?>
							<span class="analytify_bar_graph">
								<span style="width: <?php echo  ( $row[3] / $total_visits ) * 100 ?>%"></span>
							</span>
						</td>
						<td class="analytify_txt_center analytify_value_row"><?php echo WPANALYTIFY_Utils::pretty_numbers( $row[3] ); ?></td>
					</tr>
				<?php endforeach; ?>

				<?php else: ?>
          <tr>
            <td class='analytify_td_error_msg' >
              <?php echo $current->no_records(); ?>
            </td>
          </tr>
				<?php endif; ?>

			</tbody>
		</table>

	</div>
	<?php
}
