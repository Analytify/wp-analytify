<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
* View of Social Media Statistics
*/
function fetch_socialmedia_stats( $current, $stats, $return = false ) {

	$total_visits = WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:sessions'] );
	ob_start();
	# code...
	?>
	<table class="analytify_data_tables analytify_no_header_table">
		<tbody>

			<?php 	if (  isset( $stats['rows'] ) && $stats['rows'] > 0 ) :  ?>
				<?php foreach ( $stats['rows'] as $row ): ?>
					<tr>
						<td><span class="<?php echo pretty_class( $row[0] ) ?> analytify_social_icons"></span> <?php echo $row[0]; ?></td>
						<td class="analytify_txt_center analytify_value_row"><?php echo WPANALYTIFY_Utils::pretty_numbers( $row[1] ); ?></td>
					</tr>
				<?php endforeach; ?>

			<?php else :?>
				<tr>
					<td class='analytify_td_error_msg' >
						<?php echo $current->no_records(); ?>
					</td>
				</tr>
			<?php	endif; ?>

		</tbody>
	</table>
	<?php


	$body = ob_get_clean();

	if ( $return ) {
		return json_encode( array(
			"total_stats" => $total_visits ,
			"body"        => $body
		) ) ;
	} else {
		echo json_encode( array(
			"total_stats" => $total_visits ,
			"body"        => $body
		) ) ;
	}



}
?>
