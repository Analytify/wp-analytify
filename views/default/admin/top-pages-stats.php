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
				<th class="analytify_value_row"><?php esc_html_e( 'Avg. Time', 'wp-analytify' ); ?></th>
				<th class="analytify_value_row"><?php esc_html_e( 'Bounce Rate', 'wp-analytify' ); ?></th>
			</tr>
		</thead>
		<tbody>

			<?php
			if ( isset( $stats['rows'] ) && $stats['rows'] > 0 ) :

				$i = 1;
				$dashboard_profile_ID = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
				$site_url = WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' );

				foreach ( $stats['rows'] as $top_page ) {
					?>
					<tr>
						<td class="analytify_txt_center"><?php echo $i; ?></td>
						<td><a target='_blank' href="<?php echo $site_url . $top_page[1] ?>"><?php echo $top_page[0]; ?></a></td>
						<td class="analytify_txt_center analytify_value_row"><?php echo WPANALYTIFY_Utils::pretty_numbers( $top_page[2] ); ?></td>
						<td class="analytify_txt_center analytify_value_row"><?php echo isset( $top_page[3] ) ? WPANALYTIFY_Utils::pretty_time( $top_page[3] ) : ''; ?></td>
						<td class="analytify_txt_center analytify_value_row"><?php echo isset( $top_page[4] ) ? WPANALYTIFY_Utils::pretty_numbers( $top_page[4] ) . '%' : ''; ?></td>
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
