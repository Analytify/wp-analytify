<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// View of Country wise Statistics
function pa_include_country( $current, $country_stats ) {
?>
<div class="data_boxes">
    <div class="data_boxes_title"><?php echo _e( 'Top Countries', 'wp-analytify'); ?> <div class="arrow_btn"></div></div>
        <div class="data_container">
            <?php
            if (! empty( $country_stats["rows"] ) ) { ?>
                <script type='text/javascript'>
                    google.load('visualization', '1', {'packages': ['geochart'],'callback': drawRegionsMap});
                    google.setOnLoadCallback(drawRegionsMap);
                    function drawRegionsMap() {
                            var data = google.visualization.arrayToDataTable([
                              ['Country', 'Visitors'],
                              <?php
                            foreach ($country_stats["rows"] as $c_stats): ?>
                              ['<?php echo $c_stats[0];?>', <?php echo $c_stats[1]; ?>],
                              <?php endforeach; ?>
                            ]);
                            var options = {};
                            var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
                        formatter.format(data, 1);
                        
                            var chart = new google.visualization.GeoChart(document.getElementById('rchart_div'));
                            chart.draw(data, options);
                    };
                </script>
            <div id="rchart_div" style="width: 600px; height: 400px; margin:0 auto;" ></div>
            
            <div class="names_grids">
                <?php foreach ( $country_stats["rows"] as $c_stats ){ ?>
                        <div class="stats">
                            <div class="row-visits">
                                <span class="large-count"><?php echo $current->wpa_number_format( $c_stats[1] ) ; ?></span>
                            Visits
                            </div>
                            <div class="visits-count">
                                <i><?php echo $c_stats[0];?></i>
                            </div>
                        </div>
                <?php } ?>
                            
            </div>
            <?php } ?>
        </div>
    <div class="data_boxes_footer">
        <span class="blk"> 
            <span class="dot"></span> 
            <span class="line"></span> 
        </span> 
        <span class="information-txt">Listing statistics of top five countries.</span>
    </div>
</div> 
<?php } ?>