<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
* View of System Statistics
*/
function fetch_system_stats ( $current, $browser_stats, $os_stats, $mobile_stats ) {

  $code = '';


  ?>

  <div class="analytify_status_body analytify_clearfix">
    <div class="analytify_one_tree_table">
      <table class="analytify_data_tables">
        <thead>
          <tr>
            <th class="analytify_txt_left analytify_top_geographic_detials_wraper">
              <?php esc_html_e( 'Browsers statistics', 'wp-analytify' ); ?>
              <?php do_action( 'analytify_after_top_browser_text' ) ?>
            </th>
            <th class="analytify_value_row"><?php esc_html_e( 'Visits', 'wp-analytify' ); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php   if ( isset( $browser_stats['rows'] ) && $browser_stats['rows'] > 0 ) : ?>
            <?php foreach ( $browser_stats['rows'] as $browser ) : ?>
              <tr>
                <td nowrap>
                  <span class="<?php echo pretty_class( $browser[0] ) ?> analytify_social_icons"></span>
                  <span class="<?php echo pretty_class( $browser[1] ) ?> analytify_social_icons"></span>
                  <?php echo "{$browser[0]} {$browser[1]}" ?>
                </td>
                <td class="analytify_txt_center analytify_value_row"><?php echo $browser[2] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td class="analytify_td_error_msg" colspan="2">
                <?php $current->no_records(); ?>
              </td>
            </tr>
          <?php endif ?>

        </tbody>
      </table>
    </div>
    <div class="analytify_one_tree_table">
      <table class="analytify_data_tables">
        <thead>
          <tr>
            <th class="analytify_txt_left analytify_top_geographic_detials_wraper analytify_brd_lft">
              <?php esc_html_e( 'Operating system statistics', 'wp-analytify' ); ?>
              <?php do_action( 'analytify_after_top_operating_system_text' ) ?>
              </th>
            <th class="analytify_value_row"><?php esc_html_e( 'Visits', 'wp-analytify' ); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php   if ( isset( $os_stats['rows'] ) && $os_stats['rows'] > 0 ) : ?>
            <?php foreach ( $os_stats['rows'] as $os_stat ): ?>
              <tr>
                <td class="analytify_boder_left" nowrap><span class="<?php echo pretty_class( $os_stat[0] )  ?> analytify_social_icons"></span> <?php echo "{$os_stat[0]} {$os_stat[1]}" ?> </td>
                <td class="analytify_txt_center analytify_value_row"><?php echo $os_stat[2] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td class="analytify_td_error_msg" colspan="2">
                <?php $current->no_records(); ?>
              </td>
            </tr>
          <?php endif ?>

        </tbody>
      </table>
    </div>
    <div class="analytify_one_tree_table">
      <table class="analytify_data_tables ">
        <thead>
          <tr>
            <th class="analytify_txt_left analytify_top_geographic_detials_wraper analytify_brd_lft">
              <?php esc_html_e( 'Mobile device statistics', 'wp-analytify' ); ?>
              <?php do_action( 'analytify_after_top_mobile_device_text' ) ?>
              </th>
            <th class="analytify_value_row"><?php esc_html_e( 'Visits', 'wp-analytify' ); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php   if ( isset( $mobile_stats['rows'] ) && $mobile_stats['rows'] > 0 ) : ?>
            <?php foreach ( $mobile_stats['rows'] as $mobile ): ?>
              <tr>
                <td class="analytify_boder_left" nowrap><span class="<?php echo pretty_class( $mobile[0] )  ?> analytify_social_icons"></span> <?php echo "{$mobile[0]} {$mobile[1]}"; ?></td>
                <td class="analytify_txt_center analytify_value_row"><?php echo $mobile[2]; ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td class="analytify_td_error_msg" colspan="2">
                <?php $current->no_records(); ?>
              </td>
            </tr>
          <?php endif ?>

        </tbody>
      </table>
    </div>
  </div>
  <div class="analytify_status_footer">
    <span class="analytify_info_stats"><?php esc_html_e( 'Listing statistics of top Mobile, Browsers and Operating System Stats.', 'wp-analytify' ); ?></span>
  </div>
  <?php

}
?>
