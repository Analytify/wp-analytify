<?php 
// View of City wise Statistics
function pa_include_city( $current, $city_stats ) {
?>
<div class="data_boxes">
    <div class="data_boxes_title"><?php echo _e( 'Top Cities', 'wp-analytify'); ?> <div class="arrow_btn"></div></div>
        <div class="data_container">
            <?php
            if (! empty( $city_stats["rows"] ) ) { ?>
            <div class="names_grids">
                <?php foreach ($city_stats["rows"] as $c_stats){ ?>
                        <div class="stats">
                            <div class="row-visits">
                                <span class="large-count"><?php echo $current->wpa_number_format( $c_stats[1] ); ?></span>
                            Visits
                            </div>
                            <div class="visits-count">
                                <i><?php  echo $c_stats[0];?> </i>
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
        <span class="information-txt">Listing statistics of top five cities.</span>
    </div>
</div> 
<?php } ?>