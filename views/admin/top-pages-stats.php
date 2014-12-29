<?php 


function pa_include_top_pages_stats( $current, $top_page_stats) {
?>
  <div class="data_boxes">
  	<div class="data_boxes_title"><?php echo _e( 'Top pages by views.', 'wp-analytify'); ?> <div class="arrow_btn"></div></div>
    <div class="data_container">
      <table class="pa-pg Real_Time_Statistics_table">
        <tr>
            <th class="wd_1">#</th>
            <th>Title</th>
            <th class="wd_2">Views</th>
        </tr>
        <?php 
          $i=1;
          
          foreach ($top_page_stats['rows'] as $top_page) { 
        ?>  
        <tr>
            <td><?php echo $i; ?></td>
            <td class="pa-pleft"><?php echo $top_page[0]; ?></td>
            <td class="pa-pright"><?php echo $current->wpa_number_format( $top_page[1] ); ?></td>
        </tr>
      <?php 
          $i++;
        } 
      ?>
      </table>
  </div>
  </div>
<?php
} 
?>