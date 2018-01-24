<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

// View of Page Entrance/Exit Statistics
function fetch_pages_stats( $current, $stats ) {

	ob_start();
	?>

	<table class="analytify_data_tables analytify_page_stats_table">
		<thead>
			<tr>
				<th class="analytify_txt_left analytify_link_title"><?php esc_html_e( 'Url link', 'wp-analytify' ); ?></th>
				<th class="analytify_compair_value_row"><?php esc_html_e( 'Entrance', 'wp-analytify' ); ?></th>
				<th class="analytify_compair_value_row"><?php esc_html_e( 'Exits', 'wp-analytify' ); ?></th>
				<th class="analytify_compair_row"><?php esc_html_e( 'Entrance% Exits%', 'wp-analytify' ); ?></th>
			</tr>
		</thead>
		<tbody>

			<?php

			if ( isset( $stats['rows'] ) && $stats['rows'] > 0 ) :
				$i            = 0;
				$url          = $stats['rows'][0][1];
				$top_entrance = $stats['rows'][0][2];

				$dashboard_profile_ID = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
				$site_url             = WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' );

				foreach ( $stats['rows'] as $row ) :
					$i++;
			?>
					<tr>
						<td class="analytify_page_url_detials"><span class="analytify_page_name analytify_bullet_<?php echo $i; ?>"><?php echo $row[0]; ?></span><a target="_blank" href="<?php echo $site_url . $row[1]; ?>"><?php echo $row[1]; ?></a> </td>
						<td class="analytify_txt_center analytify_w_300 analytify_l_f"><?php echo WPANALYTIFY_Utils::pretty_numbers( $row[2] ); ?></td>
						<td class="analytify_txt_center analytify_w_300 analytify_l_f"><?php echo WPANALYTIFY_Utils::pretty_numbers( $row[3] ); ?></td>
						<td class="analytify_txt_center analytify_w_300 analytify_l_f">

							<div class="analytify_enter_exit_bars analytify_enter">
								<?php echo round( $row[4], 2 ) . '<span class="analytify_persantage_sign">%</span>'; ?>
								<span class="analytify_bar_graph"><span style="width: <?php echo round( $row[4], 2 ); ?>%"></span></span>
							</div>
							<div class="analytify_enter_exit_bars">
								<?php echo round( $row[5], 2 ) . '<span class="analytify_persantage_sign">%</span>'; ?>
								<span class="analytify_bar_graph"><span style="width: <?php echo round( $row[5], 2 ); ?>%"></span></span>
							</div>


						</td>
					</tr>
					<?php endforeach; ?>

				<?php else : ?>
					<tr>
						<td class='analytify_td_error_msg' colspan="4" >
							<?php echo $current->no_records(); ?>
						</td>
					</tr>
				<?php	endif; ?>

			</tbody>
		</table>

<?php

	$body = ob_get_clean();

	echo json_encode(
		array(
			'message' => sprintf( esc_html__( 'Did you know that %1$s people landed directly to your site at %2$s', 'wp-analytify' ), WPANALYTIFY_Utils::pretty_numbers( $top_entrance ), $url ),
			'body'    => $body,
		)
	);

}
?>
