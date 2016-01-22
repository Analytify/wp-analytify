<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pa_include_pages_stats( $current, $page_stats) {
?>

  <div class="data_boxes">
  	<div class="data_boxes_title"><?php echo _e( 'What\'s happening when users come to your site.', 'wp-analytify'); ?> <div class="arrow_btn"></div></div>
    <div class="data_container">
    	<?php
      $exit_num = 0;
      $exit_path = '';
      if (! empty( $page_stats["rows"]) ) { //print_r($page_stats["rows"]);?>

        <script type="text/javascript">
          google.load("visualization", "1", { packages:["corechart"],'callback': drawChart} );
          
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Links', 'Page Views', 'Page exits'],
                <?php
                foreach ($page_stats["rows"] as $p_stats) { ?>
                            ['<?php echo $p_stats[0]; ?>',  
                            <?php echo $p_stats[2]; ?>,
                            <?php echo $p_stats[3];?>
                            ],
                            <?php } ?>
                          ]);
                          var options = {
                            hAxis: {titleTextStyle: {color: 'red'}}
                          };
                          var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
                        formatter.format( data, 1);
                        formatter.format( data, 2);

                          var chart = new google.visualization.ColumnChart(document.getElementById('pages_chart'));
                          chart.draw(data, options);
          }
        </script>
    <div id="pages_chart" style="width: 960px; height: 400px; margin:0 auto;"></div>
    <div style="text-align:center; padding: 40px 0px;"><span class="large-count"><?php echo $current->wpa_number_format( $page_stats->totalsForAllResults['ga:exits'] ); ?></span> Total Exits</div>
      <?php

      foreach ($page_stats["rows"] as $p_stats) {
        $exit_num  = $p_stats[1];
        //$exit_path_title = $p_stats[0];
        $exit_path = $p_stats[0];
        break;
      }
      if( $exit_path == "/" ) {
                            //$exit_path .= " -> Home";
      }
      }
      ?>
  </div>
  <div class="data_boxes_footer">
        <span class="blk"> 
          <span class="dot"></span> 
          <span class="line"></span> 
        </span> 
        <span class="information-txt"><?php _e('Did you know that', 'wp-analytify')?> <b><?php echo $current->wpa_number_format( $exit_num ); ?></b> <?php _e(' people landed directly to your site at ', 'wp-analytify')?> <a href="<?php echo get_option('pt_webprofile_url') . $exit_path;?>" target="_blank"><?php echo $exit_path;?></a></span>
  </div>
  </div>
<?php
} 
?>