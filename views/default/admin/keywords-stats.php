<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
* View of Keyword Statistics
*/
function fetch_keywords_stats( $current, $stats , $return = false ) {

	$total_visits =   $stats->totalsForAllResults['ga:sessions'] ;

	ob_start();
	?>
	<table class="analytify_bar_tables">
		<tbody>

			<?php 	if (  isset( $stats['rows'] ) && $stats['rows'] > 0 ) : ?>
				<?php foreach ( $stats['rows'] as $row ): ?>
					<tr>
						<td>
							<?php
							if ( '(not provided)' == $row[0] ) {
								_e( '(not provided)', 'wp-analytify' );
							} elseif ( '(not set)' == $row[0] ) {
								_e( '(not set)', 'wp-analytify' );
							} else{
								echo $row[0];
							}
							 ?>
							<span class="analytify_bar_graph">
								<span style="width: <?php echo  ( $row[1] / $total_visits ) * 100 ?>%"></span>
							</span>
						</td>
						<td class="analytify_txt_center analytify_value_row"><?php echo WPANALYTIFY_Utils::pretty_numbers( $row[1] ); ?></td>
					</tr>
				<?php endforeach; ?>

				<?php
			else: ?>
						<tr>
							<td class='analytify_td_error_msg' >
								<?php echo $current->no_records(); ?>
							</td>
						</tr>
			<?php endif;
			?>
		</tbody>
	</table>
	<?php

	$body = ob_get_clean();


	if ( $return ) {
		return json_encode( array(
			"total_stats" => WPANALYTIFY_Utils::pretty_numbers( $total_visits ),
			"body"        => $body
			) ) ;
		} else {
			echo json_encode( array(
				"total_stats" => WPANALYTIFY_Utils::pretty_numbers( $total_visits ),
				"body"        => $body
				) ) ;

			}

	}
	?>
