<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

// View of Referrers Statistics
function fetch_referrers_stats( $current, $stats, $return = false ) {

	$total_visits =  $stats->totalsForAllResults['ga:sessions'] ;

	ob_start();
	?>
	<table class="analytify_bar_tables">
		<tbody>

			<?php if (  isset( $stats['rows'] ) && $stats['rows'] > 0 ) : ?>
				<?php foreach ( $stats['rows'] as $row ): ?>
					<tr>
						<td><?php echo $row[0] . '/' . $row[1] ; ?><span class="analytify_bar_graph"><span style="width: <?php echo ( $row[2] / $total_visits ) * 100 ?>%"></span></span></td>
						<td class="analytify_txt_center analytify_value_row"><?php echo WPANALYTIFY_Utils::pretty_numbers( $row[2] ); ?></td>
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
	<?php

	$body = ob_get_clean();

	if ( $return ) {
		return json_encode( array (
		"total_stats" => WPANALYTIFY_Utils::pretty_numbers( $total_visits ),
		"body"        => $body
		) ) ;
	} else {
		echo json_encode( array (
		"total_stats" => WPANALYTIFY_Utils::pretty_numbers( $total_visits ),
		"body"        => $body
		) ) ;
	}



	}
	?>
