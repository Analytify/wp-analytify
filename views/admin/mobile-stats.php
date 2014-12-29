<?php 
// 
function pa_include_mobile( $current, $mobile_stats ) {

?>
<div class="data_boxes">
    <div class="data_boxes_title "><?php echo _e( 'Mobile Devices Statistics', 'wp-analytify'); ?> <div class="arrow_btn"></div></div>
        <div class="data_container">
            <?php
            if (! empty( $mobile_stats["rows"] ) ) { ?>
            <div class="names_grids">
                <?php foreach ($mobile_stats["rows"] as $m_stats){ ?>
                        <div class="stats">
                            <div class="row-visits">
                                <span class="large-count"><?php echo $current->wpa_number_format( $m_stats[1] ); ?></span>
                            Visits
                            </div>
                            <div class="visits-count">
                                <i><?php  echo $m_stats[0];?> </i>
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
        <span class="information-txt">Listing statistics of Mobile usage.</span>
    </div>
</div> 
<?php } ?>