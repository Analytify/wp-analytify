 <?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pa_include_referrers($current,$referr_stats)
        {
?> 
    <div class="data_boxes">
        <div class="data_boxes_title"><?php echo _e( 'Top Referrers', 'wp-analytify'); ?><div class="arrow_btn"></div></div>
              <div class="data_container">
                <script type="text/javascript">
                  google.load("visualization", "1", {packages:["corechart"],'callback': drawChart});
                  //google.setOnLoadCallback(drawChart);
                  function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                      ['Task', 'Referr stats'],
                      <?php foreach ($referr_stats["rows"] as $r_stats):?>
                      ['<?php echo $r_stats[0]; ?>/<?php echo $r_stats[1]; ?>',
                      <?php echo $r_stats[2]; ?>
                      ],
                    <?php endforeach; ?>
                    ]);
                    var options = {
                      legend: 'none',
                      pieHole: 0.4,
                    };
                    var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
                        formatter.format(data, 1);
                        
                    var chart = new google.visualization.PieChart(document.getElementById('referrchart'));
                    chart.draw(data, options);
                  }
                </script>
            <div id="referrchart" style="width: 300px; height: 300px; margin:0 auto;"></div>
                <?php 
                  if (!empty($referr_stats["rows"])):
              ?>
                <div class="names_grids">
                    <?php foreach ($referr_stats["rows"] as $r_stats): ?>
                            <div class="stats">
                                <div class="row-visits">
                                    <span class="large-count"><?php echo $current->wpa_number_format( $r_stats[2] ); ?> </span>
                                    <?php echo _e( 'Visits' , 'wp-analytify'); ?>
                                </div>
                                <div class="visits-count">
                                    <i><?php echo $r_stats[0];?>/
                                    <?php echo $r_stats[1];?></i>
                                </div>
                            </div>
            <?php endforeach; ?>
        </div>
            <?php endif; ?>
    </div>
  <div class="data_boxes_footer">
        <span class="blk"> 
            <span class="dot"></span> 
            <span class="line"></span> 
        </span> 
        <span class="information-txt"><?php echo _e('who are the strong Referrers to your site ?? See above.', 'wp-analytify'); ?></span>
  </div>
</div>
<?php } ?>