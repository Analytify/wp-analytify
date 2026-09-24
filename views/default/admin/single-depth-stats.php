<?php
/**
 * View of scroll depth statistics for single page.
 *
 * @package WP_Analytify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * View of scroll depth statistics for single page.
 *
 * @param WP_Analytify         $wp_analytify Main plugin instance.
 * @param array<string, mixed> $stats Statistics data.
 *
 * @return void
 */
function wpa_include_ga_single_depth( $wp_analytify, $stats ) {

	$rows         = $stats['rows'];
	$total_visits = $stats['aggregations']['eventCount'];
	?>

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
				if ( ! empty( $rows ) ) {
					$total_visits = $stats['aggregations']['eventCount'];

					foreach ( $rows as $row ) {
						?>
						<tr>
							<td>
								<?php	echo esc_html( $row['customEvent:link_label'] ) . '%'; ?>
								<span class="analytify_bar_graph">
									<span style="width: <?php echo esc_attr( ( $row['eventCount'] / $total_visits ) * 100 ); ?>%"></span>
								</span>
							</td>
							<td class="analytify_txt_center analytify_value_row"><?php echo esc_html( WPANALYTIFY_Utils::pretty_numbers( $row['eventCount'] ) ); ?></td>
						</tr>
						<?php
					}
				} else {
					echo wp_kses_post( method_exists( $wp_analytify, 'no_records' ) ? $wp_analytify->no_records() : '<p>' . esc_html__( 'No records found', 'wp-analytify' ) . '</p>' );
				}
				?>
			</tbody>
		</table>
	</div>

	<?php
}
