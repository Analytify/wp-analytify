<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

// view of top pages
function fetch_top_pages_stats( $current, $stats ) { ?>

	<table class="analytify_data_tables wp_analytify_paginated">
		<thead>
			<tr>
				<th class="analytify_num_row">#</th>
				<th class="analytify_txt_left"><?php esc_html_e( 'Title', 'wp-analytify' ); ?></th>
				<th class="analytify_value_row"><?php esc_html_e( 'Views', 'wp-analytify' ); ?></th>
			</tr>
		</thead>
		<tbody>

			<?php
			if ( isset( $stats['rows'] ) && $stats['rows'] > 0 ) :

				$i = 1;

				foreach ( $stats['rows'] as $top_page ) {
					?>
					<tr>
						<td class="analytify_txt_center"><?php echo $i; ?></td>
						<td><?php echo $top_page[0]; ?></td>
						<td class="analytify_txt_center analytify_value_row"><?php echo WPANALYTIFY_Utils::pretty_numbers( $top_page[1] ); ?></td>
					</tr>
					<?php

					$i++;
				}

			else: ?>
			<tr>
				<td class="analytify_td_error_msg" colspan="3">
					<?php $current->no_records(); ?>
				</td>
			</tr>
			<?php	endif; ?>
		</tbody>
	</table>
<?php
}
?>
