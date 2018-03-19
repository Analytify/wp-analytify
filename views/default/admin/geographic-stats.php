<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
* View of Geographic Statistics
*/
function fetch_geographic_stats ( $current, $countries_stats, $cities_stats, $show_map = true ) {

  if ( isset( $countries_stats['rows'] ) && $countries_stats['rows'] > 0  ):

    $code = '';

    if ( true === $show_map) {

      $higest_number = $countries_stats['rows'][0][1];
      $_lowest_number = WPANALYTIFY_Utils::end( $countries_stats['rows'] );
      $lowest_number = $_lowest_number[1]

      ?>
      <script>
      jQuery(document).ready(function ($) {

        // configure for module loader
        require.config({
          paths: {
            echarts: 'js/dist/'
          }
        });

        // use
        require(
          [
            'echarts',
            'echarts/chart/map', // require the specific chart type
          ],
          function (ec) {
            // Initialize after dom ready
            var geographic_stats_graph = ec.init( document.getElementById('analytify_geographic_stats_graph' ) );

            var geographic_stats_graph_option = {
              tooltip : {
                trigger: 'item',
                formatter : function( params ) {
                  var value = (params.value + '').split('.');
                  // value = value[0].replace(/(\d{1,3})(?=(?:\d{3})+(?!\d))/g, '$1,')
                  //         + '.' + value[1];
                  if ( value[0]  != '-' ) {
                    value = value[0];
                  } else {
                    value = 0;
                  }
                  return '<?php _e( "Geographic Stats", "wp-analytify" ) ?>' + '<br/>' + params.name + ' : ' + value;
                }
              },
              toolbox: {
                show : false,
                orient : 'horizontal',
                x: 'right',
                y: '10',
                feature : {
                  restore : { show: true },
                  saveAsImage : { show: true }
                }
              },
              roamController: {
                show: true,
                mapTypeControl: {
                  'world': true
                },
                x: 'right',
                y: 'bottom'
              },
              dataRange: {
                min: <?php echo $lowest_number ?>,
                max: <?php echo $higest_number ?>,
                text:['<?php _e( "High", "wp-analytify" ) ?>','<?php _e( "Low", "wp-analytify" ) ?>'],
                realtime: true,
                calculable : true,
                color: ['#ff5252','#ffbc00','#448aff']
              },
              series : [
                {
                  name: 'Geographic Stats',
                  type: 'map',
                  mapType: 'world',
                  roam: false,
                  scaleLimit :{
                    min: 1,
                    max: 10
                  },
                  mapLocation: {
                    y : 60
                  },
                  itemStyle:{
                    emphasis:{label:{show:true}}
                  },
                  data:[
                    <?php foreach ($countries_stats['rows'] as $country): ?>
                    {name : '<?php echo $country[0] == 'United States' ? 'United States of America' : $country[0] ?>', value : <?php echo $country[1] ?>},
                    <?php endforeach; ?>
                  ]
                }
              ]
            };

            // Load data into the ECharts instance
            geographic_stats_graph.setOption(geographic_stats_graph_option);


            window.onresize = function () {
              geographic_stats_graph.resize();
            }

          }
        );
      });

      </script>

      <div class="analytify_txt_center analytify_graph_wraper">
        <div id="analytify_geographic_stats_graph" style="height:600px"></div>
      </div>
      <?php } ?>
    <div class="analytify_clearfix">
      <table class="analytify_data_tables analytify_border_th_tp analytify_half analytify_pull_left">
        <thead>
          <tr>
            <th class="analytify_txt_left analytify_top_geographic_detials_wraper">
              <?php esc_html_e( 'Top countries', 'wp-analytify' ); ?>
              <?php do_action( 'analytify_after_top_country_text' ) ?>
            </th>
            <th class="analytify_value_row"><?php esc_html_e( 'Visitors', 'wp-analytify' ); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php $counter = 0 ?>
          <?php foreach ( $countries_stats['rows'] as $key => $country ): ?>
            <?php $counter++ ?>
            <tr>
              <td><span class="<?php echo pretty_class( $country[0] )   ?> analytify_flages"></span> <?php echo $country[0] ?></td>
              <td class="analytify_txt_center"> <?php echo $country[1] ?></td>
            </tr>
            <?php  if( $counter > 4 ) break ?>
          <?php endforeach; ?>

        </tbody>
      </table>
      <table class="analytify_data_tables analytify_border_th_tp p analytify_half analytify_pull_left">
        <thead>
          <tr>
            <th class="analytify_txt_left analytify_top_geographic_detials_wraper analytify_brd_lft">
              <?php esc_html_e( 'Top cities', 'wp-analytify' ); ?>
              <?php do_action( 'analytify_after_top_city_text' ) ?>
            </th>
            <th class="analytify_value_row"><?php esc_html_e( 'Visitors', 'wp-analytify' ); ?></th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ( $cities_stats['rows'] as $city ): ?>
            <tr>
              <td class="analytify_boder_left"><span class="analytify_<?php echo str_replace( ' ', '_', strtolower( $city[1] ) )  ?> analytify_flages"></span> <?php echo $city[0] ?></td>
              <td class="analytify_txt_center"><?php echo $city[2] ?></td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>
    </div>
  <?php else:
    echo  $current->no_records();
  endif;
} ?>
