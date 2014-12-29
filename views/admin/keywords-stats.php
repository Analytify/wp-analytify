<?php 

/*
 * View of Keyword Statistics
 */

function pa_include_keywords( $current, $keyword_stats) {
?>

<div class="data_boxes">
    	<div class="data_boxes_title"><?php echo _e( 'How people are finding you (keywords)', 'wp-analytify'); ?><div class="arrow_btn"></div></div>
      <div class="data_container">
                <?php
                    if (!empty($keyword_stats["rows"])):
                ?>
            <div class="names_grids">
              <div id="tagcloud">
              <?php foreach ($keyword_stats["rows"] as $k_stats):
              $keywords=$k_stats[1];
              if ($keywords < 20): 
                 $class = 'smallest'; 
               elseif ($keywords >= 20 && $keywords < 40):
                 $class = 'small'; 
               elseif ($keywords >= 40 && $keywords < 60):
                 $class = 'medium';
               elseif ($keywords >= 60 && $keywords < 80):
                 $class = 'large';
               else:
               $class = 'largest';
               endif;
              ?>
            <div class="<?php echo $class; ?> keywordscont">
              <span class="visits_by_keyword"><?php echo $current->wpa_number_format( $k_stats[1] ); ?></span> Visits <br />
                <i><?php echo $k_stats[0]; ?></i>
            </div>
            <?php endforeach; ?>
        </div>
     		
  </div>
  <?php endif; ?>
    </div>
    <div class="data_boxes_footer">
        <span class="blk"> 
          <span class="dot"></span> 
          <span class="line"></span> 
        </span> 
          <span class="information-txt"><?php echo _e('See which keywords are matching what users search.', 'wp-analytify')?></span></div>
    </div>
    <?php } ?>