<?php

/**
 * Class to Show Analytify Dashboard Addon Notice
 *
 * @since 2.1.23
 */
class Analytify_Dashboard_Addon_Install {

  private $is_already_installed = '';
  function __construct() {

    add_action( 'wp_dashboard_setup', array( $this, 'add_analytify_widget' ) );
    add_action( 'wp_ajax_activate-analytify-dashboard-free', array( $this, 'activate_free' ) );
  }

  /**
   * Register Widget.
   *
   * @since 2.1.23
   */
  public function add_analytify_widget() {

    $allowed_roles = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard',  array( 'administrator' ) );
    // if dont have Analytify Dashboard access, Return.
    if ( ! $GLOBALS['WP_ANALYTIFY']->pa_check_roles( $allowed_roles ) ) {
      return;
    }

    $this->is_already_installed = file_exists( WP_PLUGIN_DIR . '/analytify-analytics-dashboard-widget/wp-analytify-dashboard.php' );

    if ( $this->is_already_installed == true ) {
      return;
    }
    wp_add_dashboard_widget( 'analytify-dashboard-addon', __( 'Google Analytics Dashboard By Analytify', 'analytify-analytics-dashboard-widget' ), array( $this, 'wpa_general_dashboard_area' ), null , null );

  }

  /**
  * Create Widget Container.
  *
  * @since 2.1.23
  */
  public function wpa_general_dashboard_area( $var, $dashboard_id ) {


    $activate_url = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => 'analytify-analytics-dashboard-widget/wp-analytify-dashboard.php' ), admin_url( 'plugins.php' ) ), 'activate' . '-plugin_' . 'analytify-analytics-dashboard-widget/wp-analytify-dashboard.php' );
    ?>
    <div class="inside">

      <div class="install-analytify-dashboard-widget">
        <div class="install-analytify-dashboard-widget-content">
          <h2><?php esc_html_e( 'View All Analytics on the WordPress Dashboard', 'wp-analytify' ) ?></h2>
          <p><?php esc_html_e( 'Once you install Analytify Dashboard Widget Addon, this dashboard widget will be filled with Analytics.', 'wp-analytify' ) ?></p>

          <?php  if ( $this->is_already_installed ): ?>
            <a href="<?php echo $activate_url ?>" class="button button-primary button-hero activate-analytify-dashboard-free"><?php esc_html_e( 'Activate Dashboard Add-on', 'wp-analytify' ) ?></a>
            <img src="<?php echo admin_url( 'images/spinner.gif' ); ?> " style=" display: none; margin: 0 auto; padding-top: 20px;" class='install-analytify-dashboard-widget-loader'>
          <?php else: ?>
            <a href="" target="_blank" class="button button-primary button-hero install-analytify-dashboard-free" data-nonce="<?php echo wp_create_nonce( 'updates' ); ?>"><?php esc_html_e( 'Install Dashboard Add-on Free', 'wp-analytify' ) ?></a>
            <a href="<?php echo $activate_url ?>" class="button button-primary button-hero activate-analytify-dashboard-free" style="display: none"><?php esc_html_e( 'Activate Dashboard Add-on', 'wp-analytify' ) ?></a>
            <img src="<?php echo admin_url( 'images/spinner.gif' ); ?> " style=" display: none; margin: 0 auto; padding-top: 20px;" class='install-analytify-dashboard-widget-loader'>
          <?php  endif; ?>

        </div>
      </div>
    </div>

    <script type="text/javascript">

      (function($, window, document) {
        $('.install-analytify-dashboard-free').on('click', function(event) {
          event.preventDefault();
          var nonce = $(this).data('nonce');
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              slug: 'analytify-analytics-dashboard-widget',
              action: 'install-plugin',
              _ajax_nonce: nonce
            },
            beforeSend: function(){
              $('.install-analytify-dashboard-free').attr('disabled', 'disabled');
              $('.install-analytify-dashboard-widget-loader').css('display', 'block');
            }
          })
          .done(function() {
            $('.install-analytify-dashboard-widget-loader').css('display', 'none');
            $('.install-analytify-dashboard-free').css('display', 'none');
            $('.activate-analytify-dashboard-free').show()
          });
        });

        // Activate plugin
        $(document).on('click', '.activate-analytify-dashboard-free', function(event) {
          event.preventDefault();
          var button = $(this);
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'activate-analytify-dashboard-free'
            },
            beforeSend: function() {
              button.attr('disabled', 'disabled');
              button.siblings('.install-analytify-dashboard-widget-loader').css('display', 'block');
            }
          }).always(function() {
            location.reload();
          });

        });
      }(window.jQuery, window, document));
    </script>
    <?php
  }

  /**
   * Activate Dashboard Widget.
   *
   */
  function activate_free() {
    $plugin = 'analytify-analytics-dashboard-widget/wp-analytify-dashboard.php';
    if( ! is_plugin_active( $plugin ) ) {
      activate_plugin( $plugin );
    }
    wp_die();
  }

}

new Analytify_Dashboard_Addon_Install();
