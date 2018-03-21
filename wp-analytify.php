<?php
/**
 * Plugin Name: Analytify - Google Analytics Dashboard
 * Plugin URI: https://analytify.io/details
 * Description: Analytify brings a brand new and modern feeling Google Analytics superbly integrated with WordPress Dashboard. It presents the statistics in a beautiful way under the WordPress Posts/Pages at front end, backend and in its own Dashboard. This provides Stats from Country, Referrers, Social media, General stats, New visitors, Returning visitors, Exit pages, Browser wise and Top keywords. This plugin provides the RealTime statistics in a new UI which is easy to understand & looks good.
 * Version: 2.1.13
 * Author: Analytify
 * Author URI: https://analytify.io
 * License: GPLv3
 * Text Domain: wp-analytify
 * Tested up to: 4.9
 * Domain Path: /languages
 *
 * @package WP_ANALYTIFY
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Analytify_General' ) ) {

	require_once 'analytify-general.php';
}

if ( ! class_exists( 'WP_Analytify' ) ) {
	/**
	 * Main WP_Analytify class
	 *
	 * @since       1.0.0
	 */
	class WP_Analytify extends Analytify_General {

		/**
		 * @var         WP_Analytify $instance The one true WP_Analytify
		 * @since       1.2.2
		 */
		private static $instance = null;

		protected 	$transient_timeout;
		public 		$token  = false;
		public 		$client = null;


		/**
		 * [__construct description]
		 */
		function __construct() {

			parent::__construct();
			$this->setup_constants();
			$this->includes();
			$this->load_textdomain();
			$this->hooks();
		}


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.2.2
		 * @return      object self::$instance The one true WP_Analytify
		 */
		public static function get_instance() {
			if ( ! self::$instance ) {

				self::$instance = new WP_Analytify();
				// self::$instance->setup_constants();
				// self::$instance->includes();
				// self::$instance->load_textdomain();
				// self::$instance->hooks();
			}
			return self::$instance;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.2.2
		 * @return      void
		 */
		private function setup_constants() {

			// Setting Global Values.
		}


		/**
		 * Define constant if not already set
		 *
		 * @since 1.2.4
		 * @param  string      $name  contanst name.
		 * @param  string|bool $value constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @since 1.2.4
		 * @param string $type ajax, frontend or admin.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
				return is_admin();
				case 'ajax' :
				return defined( 'DOING_AJAX' );
				case 'cron' :
				return defined( 'DOING_CRON' );
				case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.2.2
		 * @return      void
		 */
		private function includes() {

			//include_once ANALYTIFY_PLUGIN_DIR . '/welcome.php';
			// require_once ANALYTIFY_PLUGIN_DIR . '/wp-analytify/inc/class-analytify-logging.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/inc/wpa-core-functions.php';

			include_once ANALYTIFY_PLUGIN_DIR . '/inc/class-wpa-adminbar.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/inc/class-wpa-ajax.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/classes/class.upgrade.php';

			if ( $this->is_request( 'ajax' ) ) {
				 $this->ajax_includes();
			}

		}

		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.2.2
		 * @return      void
		 */
		private function hooks() {

			add_action( 'admin_init', array( $this, '_save_core_version' ) );
			add_action( 'admin_init', array( $this, 'wpa_check_authentication' ) );
			add_action( 'admin_init', array( $this, 'analytify_review_notice' ) );
			add_action( 'admin_init', array( $this, 'analytify_nag_ignore' ) );
			add_action( 'admin_init', array( $this, 'logout' ), 1 );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

			add_action( 'plugin_action_links', 	array( $this, 'plugin_action_links' ), 10, 2 );
			add_action( 'plugin_row_meta', 			array( $this, 'plugin_row_meta' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_styles' ) );

			add_action( 'admin_notices', array( $this, 'analytify_admin_notice' ) );

			add_action( 'wp_head', array( $this, 'analytify_add_analytics_code' ) );

			add_action( 'wp_ajax_nopriv_get_ajax_single_admin_analytics', array( $this, 'get_ajax_single_admin_analytics' ) );
			add_action( 'wp_ajax_get_ajax_single_admin_analytics', array( $this, 'get_ajax_single_admin_analytics' ) );

			add_action( 'load-analytify_page_analytify-settings', array( $this, 'load_settings_assets' ) );

			/**
			 * load Analytics under the EDIT POST Screen
       		 * add action runs only for admin section and load metabox.
			 */

			if ( is_admin() ) {
				add_action( 'load-post.php', array( $this, 'load_metaboxes' ) );
			}

			// // Show welcome message when user activate plugin.
			// if ( 1 != get_option( 'show_tracking_pointer_1' ) ) {
			//
			// 	add_action( 'admin_print_footer_scripts', array(
			// 		$this,
			// 		'pa_welcome_message',
			// 	) );
			// }

			add_filter( 'admin_footer_text', 'wpa_admin_rate_footer_text', 1 );
			add_action( 'admin_footer', 'wpa_print_js', 25 );

			// Show links at post rows.
			add_filter( 'post_row_actions', array( $this, 'post_rows_stats' ), 10, 2 );
			add_filter( 'page_row_actions', array( $this, 'post_rows_stats' ), 10, 2 );
			add_action( 'post_submitbox_minor_actions', array( $this, 'post_submitbox_stats_action' ), 10, 1 );

			// Add after_plugin_row... action for pro plugin
			// add_action( 'after_plugin_row_wp-analytify/wp-analytify.php', array( $this, 'wpa_plugin_row'), 11, 2 );

			add_action( 'wp_footer' , array( $this, 'track_miscellaneous_errors' ) );
      		add_action( 'admin_footer',	array( $this, 'add_deactive_modal' ) );
			add_action( 'admin_init', array( $this, 'redirect_optin' ) );
		}

		/**
		 * Redirect to Welcome page.
		 *
		 * @since 2.0.14
		 */
		function redirect_optin() {

			if ( ! get_site_option( '_analytify_optin' ) && isset( $_GET['page'] ) && ( $_GET['page'] === 'analytify-settings' || $_GET['page'] === 'analytify-dashboard' || $_GET['page'] === 'analytify-woocommerce' || $_GET['page'] === 'analytify-addons' ) ) {
				wp_redirect( admin_url('admin.php?page=analytify-optin') );
				exit;
			}
		}

		function add_deactive_modal() {
			global $pagenow;

			if ( 'plugins.php' !== $pagenow ) {
				return;
			}
			include ANALYTIFY_PLUGIN_DIR . 'inc/analytify-optout-form.php';
			include ANALYTIFY_PLUGIN_DIR . 'inc/analytify-deactivate-form.php';

		}


		/**
		 * Internationalization.
		 *
		 * @access      public
		 * @since       1.2.2
		 * @return      void
		 */
		public function load_textdomain() {

			$plugin_dir = basename( dirname( __FILE__ ) );
			load_plugin_textdomain( 'wp-analytify', false , $plugin_dir . '/languages/' );
		}


		 /** Show analytics sections under the posts/pages in the metabox. */
		function load_metaboxes() {
				add_action( 'add_meta_boxes', array( $this, 'show_admin_single_analytics_add_metabox' ) );
		}

		/**
		 * Show metabox under each Post type to display Analytics of single post/page in wp-admin.
		 */
		public function show_admin_single_analytics_add_metabox() {

			global $post;

			if ( ! isset( $post ) ) {
				return false; }

			// Don't show statistics on posts which are not published.
			if ( 'publish' !== $post->post_status ) { return false; }

			$post_types = $this->settings->get_option( 'show_analytics_post_types_back_end','wp-analytify-admin' );

			// Don't load boxes/sections if no any post type is selected.
			if ( ! empty( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					// var_dump($post_type); //wp_die();
					add_meta_box('pa-single-admin-analytics', // $id
						__( 'Analytify - Stats of this Post/Page', 'wp-analytify' ), // $title.
						array(
						$this,
						'show_admin_single_analytics',
						), // $callback
						$post_type, // $posts
						'normal',   // $context
						'high'      // $priority
					);
				} //$post_types as $post_type
			}
		}

		/**
		 * Save Authentication code on return
		 *
		 * @since 1.3
		 */
		public function wpa_check_authentication() {

			if ( isset( $_GET['code'] ) && 'analytify-settings' === $_GET['page'] ) {

				$key_google_token = sanitize_text_field( wp_unslash( $_GET['code'] ) );
				update_option( 'WP_ANALYTIFY_NEW_LOGIN', 'yes' );
				WP_Analytify::pt_save_data( $key_google_token );
				wp_redirect(  admin_url( 'admin.php?page=analytify-settings' ) . '#wp-analytify-profile' );
				exit;
			}

		}


		/**
		 * Save version number of the plugin and show a custom message for users
		 *
		 * @since 1.3
		 */

		public function _save_core_version() {

			if ( ANALYTIFY_VERSION != get_option( 'WP_ANALYTIFY_PLUGIN_VERSION' ) ) {

				update_option( 'WP_ANALYTIFY_PLUGIN_VERSION_OLD', get_option( 'WP_ANALYTIFY_PLUGIN_VERSION' ), '2.0.7' );  // saving old plugin version

				update_option( 'WP_ANALYTIFY_PLUGIN_VERSION', ANALYTIFY_VERSION );
			}
		}


		/**
		 * Show Analytics of single post/page in wp-admin under EDIT screen.
		 */
		public function show_admin_single_analytics() {

			global $post;

			$back_exclude_posts = false;
			// // Don't show statistics on posts which are not published.
			// if ( get_post_status ( $post->ID ) != 'publish' ) {
			// esc_html_e( 'Statistics will be loaded after you publish this content.', 'wp-analytify' );
			// return false;
			// }
			$_exclude_profile = get_option( 'wp-analytify-admin' );
			if ( isset( $_exclude_profile['exclude_pages_back_end'] ) ) {
				$back_exclude_posts = explode( ',', $_exclude_profile['exclude_pages_back_end'] ); }

			if ( is_array( $back_exclude_posts ) ) {

				if ( in_array( $post->ID, $back_exclude_posts ) ) {

					esc_html_e( 'This post is excluded and will NOT show Analytics.', 'wp-analytify' );

					return;
				}
			}

			// $wp_analytify  = new WP_Analytify();
			$url_post     = '';
			$url_post     = parse_url( get_permalink( $post->ID ) );

			if ( get_the_time( 'Y', $post->ID ) < 2005 ) {

				$start_date = '2005-01-01';
			} else {

				$start_date = get_the_time( 'Y-m-d', $post->ID );
			}

			$end_date = date( 'Y-m-d' );

			$is_access_level = $this->settings->get_option( 'show_analytics_roles_back_end','wp-analytify-admin' );

			if ( $this->pa_check_roles( $is_access_level ) ) {  ?>


				<div class="analytify_setting">
					<div class="analytify_select_date analytify_select_date_single_page">
						<form class="analytify_form_date" action="" method="post">
							<div class="analytify_select_date_fields">
								<input type="hidden" name="start_date" id="analytify_start_val">
								<input type="hidden" name="end_date" id="analytify_end_val">

								<label for="analytify_start"><?php _e( 'From:', 'wp-analytify' )?></label>
								<input type="text" id="analytify_start" value="<?php echo isset(  $start_date ) ?  $start_date :
																		'' ?>">
								<label for="analytify_end"><?php _e( 'To:', 'wp-analytify' )?></label>
								<input type="text" id="analytify_end" value="<?php echo isset( $end_date ) ? $end_date :
																		'' ?>">
								<input type="hidden" name="urlpost" id="urlpost" value="<?php echo  esc_url( $url_post['path'] ); ?>">

								<div class="analytify_arrow_date_picker"></div>
							</div>
							<input type="submit" value="<?php _e( 'View Stats', 'wp-analytify' )?>" name="view_data" class="analytify_submit_date_btn"  id="view_analytics">

							<?php do_action( 'after_single_view_stats_buttons' ) ?>
							<ul class="analytify_select_date_list">
								<li><?php _e( 'Last 30 days', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_30_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

								<li><?php _e( 'This month', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_this_month_start_date"></span> – <span class="analytify_end_date_data analytify_today_date"></span></li>

								<li><?php _e( 'Last month', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_month_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


								<li><?php _e( 'Last 3 months', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_3_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

								<li><?php _e( 'Last 6 months', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_6_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


								<li><?php _e( 'Last year', 'wp-analytify' )?> <span data-start="" data-end=""><span class="analytify_start_date_data analytify_last_year_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>


								<li><?php _e( 'Custom Range', 'wp-analytify' )?> <span class="custom_range"><?php _e( 'Select a custom date', 'wp-analytify' )?></span></li>
							</ul>
						</form>
					</div>
				</div>

			<div class="show-hide">
				<?php $this->get_single_admin_analytics( $start_date, $end_date, $post->ID, 0 ); ?>
			</div>
			<?php
			} else {
				esc_html_e( 'You are not allowed to see stats', 'wp-analytify' );
			}
		}


		/**
		 * Add Google Analytics JS code
		 */
		public function analytify_add_analytics_code() {

			if ( 'on' === $this->settings->get_option( 'install_ga_code', 'wp-analytify-profile', 'off' ) ) {

				global $current_user;

				$roles = $current_user->roles;

				if ( isset( $roles[0] ) and in_array( $roles[0], $this->settings->get_option( 'exclude_users_tracking', 'wp-analytify-profile', array() ) ) ) {

					echo '<!-- This user is disabled from tracking by Analytify !-->';
				} else {

					if ( ! $this->settings->get_option( 'profile_for_posts', 'wp-analytify-profile' ) ) {
						return;
					}

					// Fetch Universel Analytics UA code for selected website.
					$UA_CODE = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
					echo sprintf( esc_html__( '%2$s This code is added by WP Analytify (%1$s) %4$s %3$s', 'wp-analytify' ), ANALYTIFY_VERSION, '<!--', '!-->', 'https://analytify.io/downloads/analytify-wordpress-plugin/' );

					?>

					<script>
						(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
							(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
							m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})


						(window,document,'script','//www.google-analytics.com/analytics.js','ga');
						<?php
						if ( 'on' === $this->settings->get_option( 'linker_cross_domain_tracking', 'wp-analytify-advanced' ) ) {
							echo "	ga('create', '{$UA_CODE}', 'auto', {'allowLinker': true});";
							echo "ga('require', 'linker');";
						} else {
							echo "	ga('create', '{$UA_CODE}', 'auto');";
						}

						if ( 'on' === $this->settings->get_option( 'anonymize_ip', 'wp-analytify-advanced' ) ) {
							echo "ga('set', 'anonymizeIp', true);";
						}

						if ( 'on' === $this->settings->get_option( 'force_ssl', 'wp-analytify-advanced' ) ) {
							echo "ga('set', 'forceSSL', true);";
						}

						if ( 'on' === $this->settings->get_option( 'track_user_id', 'wp-analytify-advanced' ) && is_user_logged_in() ) {
							echo "ga('set', 'userId', " . esc_html( get_current_user_id() ) . ');';
						}

						if ( 'on' === $this->settings->get_option( 'demographic_interest_tracking', 'wp-analytify-advanced' ) ) {
							echo "ga('require', 'displayfeatures');";
						}

						if ( $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' ) ) {
							echo $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' );
						}

						// Add enhanced eccomerce extension
						do_action( 'ga_ecommerce_js' );

						echo "ga('send', 'pageview');";

						?>
					</script>

					<?php

					echo sprintf( esc_html__( '%2$s This code is added by WP Analytify (%1$s) %3$s', 'wp-analytify' ), ANALYTIFY_VERSION, '<!--', '!-->' );
				}
			}
		}


		/**
		 * Add a link to the settings page to the plugins list
		 */
		public function plugin_action_links( $links, $file ) {

			static $this_plugin;

			if ( empty( $this_plugin ) ) {

				$this_plugin = 'wp-analytify/wp-analytify.php';
			}

			if ( $file == $this_plugin ) {

				// $settings_link = sprintf( esc_html__( '%1$s Settings %2$s | %3$s Dashboard %4$s | %5$s Help %6$s', 'wp-analytify' ), '<a href="' . admin_url( 'admin.php?page=analytify-settings' ) . '">', '</a>', '<a href="' . admin_url( 'admin.php?page=analytify-dashboard' ) . '">', '</a>', '<a href="' . admin_url( 'index.php?page=wp-analytify-getting-started' ) . '">', '</a>' );

				$settings_link = sprintf( esc_html__( '%1$s Settings %2$s | ', 'wp-analytify'), '<a href="' . admin_url( 'admin.php?page=analytify-settings' ) . '">', '</a>' );

				if( 'yes' == get_option( '_analytify_optin' ) ){
					$settings_link .= sprintf( esc_html__( '%1$s Opt Out %2$s | ', 'wp-analytify'), '<a class="opt-out" href="' . admin_url( 'admin.php?page=analytify-settings' ) . '">', '</a>' );
				} else {
					$settings_link .= sprintf( esc_html__( '%1$s Opt In %2$s | ', 'wp-analytify'), '<a href="' . admin_url( 'admin.php?page=analytify-optin' ) . '">', '</a>' );
				}


				$settings_link .= sprintf( esc_html__( '%1$s Dashboard %2$s | ', 'wp-analytify'), '<a href="' . admin_url( 'admin.php?page=analytify-dashboard' ) . '">', '</a>' );

				$settings_link .= sprintf( esc_html__( '%1$s Help %2$s  ', 'wp-analytify'), '<a href="' . admin_url( 'index.php?page=wp-analytify-getting-started' ) . '">', '</a>'  );
				array_unshift( $links, $settings_link );

				if ( ! class_exists( 'WP_Analytify_Pro' ) ) {
					$pro_link = sprintf( esc_html__( '%1$s Upgrade To Pro %2$s', 'wp-analytify' ),  '<a  href="https://analytify.io/pricing/?utm_source=analytify-lite&utm_medium=plugin-action-link&utm_campaign=pro-upgrade" target="_blank" style="color:#3db634;">', '</a>' );
					array_push( $links, $pro_link );
				}

			}

			return $links;
		}

		/**
		 * Plugin row meta links
		 *
		 * @since 1.1
		 * @param array  $input already defined meta links.
		 * @param string $file plugin file path and name being processed.
		 * @return array $input
		 */
		function plugin_row_meta( $input, $file ) {
			if ( $file != 'wp-analytify/wp-analytify.php' ) {
				return $input; }

			$links = array(

				sprintf( esc_html__( '%1$s Getting Started %2$s', 'wp-analytify' ), '<a href="' . admin_url( 'index.php?page=wp-analytify-getting-started' ) . '">', '</a>' ),
				sprintf( esc_html__( '%1$s Add Ons %2$s', 'wp-analytify' ), '<a href="http://analytify.io/add-ons/">', '</a>' ),
				'<a href="https://wordpress.org/support/view/plugin-reviews/wp-analytify/" target="_blank"><span class="dashicons dashicons-thumbs-up"></span> ' . __( 'Vote!', 'wp-analytify' ) . '</a>'
				);

			$input = array_merge( $input, $links );

			return $input;
		}


		/**
		 * Display warning if profiles are not selected.
		 */
		public function pa_check_warnings() {

			add_action( 'admin_footer', array( &$this, 'profile_warning' ) );
		}

		/**
		 * Get current screen details
		 */
		public function pa_page_file_path() {

			$screen = get_current_screen();

			if ( strpos( $screen->base, 'analytify-settings' ) !== false ) {

				echo '<div class="wrap wpanalytify">';
					$this->settings->rendered_settings();
					$this->settings->show_tabs();
					$this->settings->show_forms();
				echo '</div>';
				?>

				<?php
				// include_once( ANALYTIFY_ROOT_PATH . '/inc/options-settings.php' );
			}

			else if ( strpos( $screen->base, 'analytify-logs' ) !== false ) {
					include_once( ANALYTIFY_ROOT_PATH . '/inc/page-logs.php' );
				} else if ( strpos( $screen->base, 'analytify-addons' ) !== false ) {
					include_once( ANALYTIFY_ROOT_PATH . '/inc/page-addons.php' );
				}  else if ( strpos( $screen->base, 'analytify-go-pro' ) !== false ) {
					include_once( ANALYTIFY_ROOT_PATH . '/inc/analytify-go-pro.php' );
				} else {
					if ( isset( $_GET['show'] ) ) {
						do_action( 'show_detail_dashboard_content' );
					} else {
							include_once( ANALYTIFY_ROOT_PATH . '/inc/analytics-dashboard.php' );
					}
				}
		}

		/**
		 * Styling: loading admin stylesheets for the plugin.
		 *
		 * @param  $page loaded page name.
		 */
		public function admin_styles( $page ) {

			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'admin-bar-style', plugins_url( 'assets/old/css/admin_bar_styles.css', __FILE__ ), false, ANALYTIFY_VERSION );

			// for Settings only
			if ( $page == 'analytify_page_analytify-settings' ) {
				wp_enqueue_style( 'jquery_tooltip', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', false, ANALYTIFY_VERSION);
			}

			// for Single Page/Post Stats.
			if ( $page == 'analytify_page_analytify-settings' || $page == 'post.php' || $page == 'post-new.php' ) {
				wp_enqueue_style( 'chosen', plugins_url( 'assets/old/css/chosen.min.css', __FILE__ ) );
			}

			wp_enqueue_style( 'wp-analytify-style', plugins_url( 'assets/old/css/wp-analytify-style.css', __FILE__ ), false, ANALYTIFY_VERSION );
			wp_enqueue_style( 'wp-analytify-default-style', plugins_url( 'assets/default/css/styles.css', __FILE__ ), false, ANALYTIFY_VERSION);

			// For WP Pointer
			if ( get_option( 'show_tracking_pointer_1' ) != 1 ) { wp_enqueue_style( 'wp-pointer' ); }

		}

		/**
		 * Loading admin scripts JS for the plugin.
		 */
		public function admin_scripts( $page ) {

			wp_enqueue_script( 'wp-analytify-script-js', plugins_url( 'assets/old/js/wp-analytify.js', __FILE__ ), array( 'jquery-ui-datepicker', 'jquery' ), ANALYTIFY_VERSION );

			global $post_type;

			// for main page
			if ( $page == 'index.php' || $page == 'toplevel_page_analytify-dashboard' || $page == 'analytify_page_analytify-woocommerce' || $page == 'analytify_page_edd-dashboard' || $page == 'analytify_page_analytify-campaigns' || $page == 'analytify_page_analytify-goals' || $page == 'analytify_page_analytify-forms-dashboard' || in_array( $post_type, $this->settings->get_option( 'show_analytics_post_types_back_end','wp-analytify-admin', array() ) ) ) {

				wp_enqueue_script( 'pikaday-js', 	plugins_url( 'assets/default/js/pikaday.js', __FILE__ ), array( 'moment-js' ) , ANALYTIFY_VERSION );
				wp_enqueue_script( 'moment-js', 	plugins_url( 'assets/default/js/moment.min.js', __FILE__ ), false, ANALYTIFY_VERSION );

				wp_enqueue_script( 'analytify-dashboard-js', plugins_url( 'assets/default/js/wp-analytify-dashboard.js', __FILE__ ), false, ANALYTIFY_VERSION );

			}

			// for dashboard only
			if ( $page == 'toplevel_page_analytify-dashboard' || $page == 'analytify_page_analytify-woocommerce' || $page == 'analytify_page_edd-dashboard' || $page == 'analytify_page_analytify-campaigns'  ) {

				wp_enqueue_script( 'echarts-js', 			plugins_url( 'assets/default/js/dist/echarts.js', __FILE__ ), false, ANALYTIFY_VERSION, true );
				wp_enqueue_script( 'echarts-pie-js', 	plugins_url( 'assets/default/js/dist/chart/pie.js', __FILE__ ), false, ANALYTIFY_VERSION, true );
				wp_enqueue_script( 'echarts-map-js', 	plugins_url( 'assets/default/js/dist/chart/map.js', __FILE__ ), false, ANALYTIFY_VERSION, true );
				wp_enqueue_script( 'echarts-line-js', plugins_url( 'assets/default/js/dist/chart/line.js', __FILE__ ), false, ANALYTIFY_VERSION, true );
				wp_enqueue_script( 'echarts-bar-js', 	plugins_url( 'assets/default/js/dist/chart/bar.js', __FILE__ ), false, ANALYTIFY_VERSION, true );
			}


			// for Settings only
			if ( $page == 'analytify_page_analytify-settings') {

				wp_enqueue_script( 'analytify-settings-js', plugins_url( 'assets/default/js/wp-analytify-settings.js', __FILE__ ), array('jquery-ui-tooltip', 'jquery'), ANALYTIFY_VERSION );
			}

			// for Single Page/Post Stats.
			if ( $page == 'analytify_page_analytify-settings' || $page == 'post.php' || $page == 'post-new.php' ) {
				wp_enqueue_script( 'chosen-js', plugins_url( 'assets/old/js/chosen.jquery.min.js', __FILE__ ), false, ANALYTIFY_VERSION );

			}

			if ( get_option( 'show_tracking_pointer_1' ) != 1 ) { wp_enqueue_script( 'wp-pointer' ); }

			wp_localize_script( 'wp-analytify-script-js',
				'wpanalytify_strings',
				array(
					'enter_license_key'                     => __( 'Please enter your license key.', 'wp-analytify' ),
					'register_license_problem'              => __( 'A problem occurred when trying to register the license, please try again.', 'wp-analytify' ),
					'license_check_problem'                 => __( 'A problem occurred when trying to check the license, please try again.', 'wp-analytify' ),
					'license_registered'                    => __( 'Your license has been activated. You will now receive automatic updates and access to email support.', 'wp-analytify' ),
				)
			);

			$nonces = apply_filters( 'wpanalytify_nonces', array(
				'check_license'                    => wp_create_nonce( 'check-license' ),
				'activate_license'                 => wp_create_nonce( 'activate-license' ),
				'clear_log'                        => wp_create_nonce( 'clear-log' ),
				'fetch_log'                        => wp_create_nonce( 'fetch-log' ),
				'reactivate_license'               => wp_create_nonce( 'reactivate-license' ),
			) );

			$data = apply_filters( 'wpanalytify_data', array(

				'this_url'               => esc_html( addslashes( home_url() ) ),
				'is_multisite'           => esc_html( is_multisite() ? 'true' : 'false' ),
				'nonces'                 => $nonces,

			) );

			wp_localize_script( 'wp-analytify-script-js', 'wpanalytify_data', $data );
			// print JS at footer
			 //wpa_print_js();
		}

		/**
		 * Add style for front admin bar
		 *
		 * @since 2.0.4.
		 */
		function front_styles() {

 			if ( is_admin_bar_showing() ) {
				wp_enqueue_style( 'admin-bar-style', plugins_url( 'assets/old/css/admin_bar_styles.css', __FILE__ ), false, ANALYTIFY_VERSION );
 			}

		}

		/**
		 * Create Analytics menu at the left side of dashboard
		 */
		public function add_admin_menu() {

			/*
            $wp_analytify  = new WP_Analytify();
			if( $wp_analytify->pa_check_roles( get_option( 'post_analytics_access_back' ) ) ) {*/


			add_submenu_page( null, __( 'Activate', 'wp-analytify' ), __( 'Activate', 'wp-analytify' ), 'manage_options', 'analytify-optin', array( $this, 'render_optin' )  );

			add_menu_page( ANALYTIFY_NICK, 'Analytify', 'read', 'analytify-dashboard', array(
				$this,
				'pa_page_file_path',
			), plugins_url( 'assets/images/wp-analytics-logo.png', __FILE__ ),'2.1.9');

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( ' Dashboard', 'wp-analytify' ), esc_html__( ' Dashboard', 'wp-analytify' ), 'read', 'analytify-dashboard', array(
				$this,
				'pa_page_file_path',
			));

			/* }*/

			do_action( 'analytify_add_submenu' );

			// add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( ' Campaigns', 'wp-analytify' ), esc_html__( ' Campaigns', 'wp-analytify' ), 'manage_options', 'analytify-campaigns', array(
			// 	$this,
			// 	'pa_page_file_path',
			// ));

			// add_submenu_page( null, ANALYTIFY_NICK . esc_html__( ' Logs', 'wp-analytify' ), esc_html__( ' Logs', 'wp-analytify' ), 'manage_options', 'analytify-logs', array(
			// $this,
			// 'pa_page_file_path',
			// ));

			do_action( 'analyitfy_email_setting_submenu' );

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'Settings', 'wp-analytify' ), esc_html__( 'Settings', 'wp-analytify' ), 'manage_options', 'analytify-settings', array(
				$this,
				'pa_page_file_path',
			));

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( ' list of all Add-ons', 'wp-analytify' ), esc_html__( 'Add-ons', 'wp-analytify' ), 'manage_options', 'analytify-addons', array(
				$this,
				'pa_page_file_path',
			));

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'PRO vs FREE', 'wp-analytify' ), '<b style="color:#f9845b">' . esc_html__( 'PRO vs FREE', 'wp-analytify' ) . '</b>', 'manage_options', 'analytify-go-pro',  array(
				$this,
				'pa_page_file_path',
			) );
		}



		function render_optin() {
			include ANALYTIFY_PLUGIN_DIR . 'inc/analytify-optin-form.php';
		}

		/**
		 * Creating tabs for settings
		 *
		 * @since 1.0
		 * @param string $current current tab in the settings page.
		 */

		public function pa_settings_tabs( $current = 'authentication' ) {

			$tabs = array(
				'authentication' => 'Authentication',
				'profile'        => 'Profile',
				'admin'          => 'Admin',
				'dashboard'		   => 'Dashboard',
				'advanced'		   => 'Advanced',
			);

			if ( has_filter( 'wp_analytify_tabs' ) ) { $tabs = apply_filters( 'wp_analytify_tabs', $tabs ); }

			echo '<div class="left-area">';
			echo '<div id="icon-themes" class="icon32"><br></div>';
			echo '<h2 class="nav-tab-wrapper">';

			foreach ( $tabs as $tab => $name ) {

				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=analytify-settings&tab=$tab'>$name</a>";
			}

			echo '</h2>';
		}

		/**
		 * Get profiles from user Google Analytics account profiles.
		 */

		public function pt_get_analytics_accounts() {

			try {

				if ( get_option( 'pa_google_token' ) != '' ) {
					$profiles = $this->service->management_profiles->listManagementProfiles( '~all', '~all' );

					return $profiles;
				} else {
					echo '<br /><p class="description">' . esc_html__( 'You must authenticate to access your web profiles.', 'wp-analytify' ) . '</p>';
				}
			} catch (Exception $e) {
				echo sprintf( esc_html__( '%1$s %2$s Oops, Something went wrong!%3$s %4$s Try to %5$s Reset %6$s Authentication.', 'wp-analytify' ), '<br />', '<strong>', '</strong>', '<br /><br />', '<a href=\'?page=analytify-settings&tab=authentication\' title="Reset">', '</a>' );
			}

		}

		/**
		 * Get the profiles details summary.
		 *
		 * @since 2.0.3
		 */
		public function pt_get_analytics_accounts_summary() {

			try {

				if ( get_option( 'analytify_profile_exception' ) ) {

					WPANALYTIFY_Utils::handle_exceptions( get_option( 'analytify_profile_exception' ) );
				} else if ( get_option( 'pa_google_token' ) != '' ) {
					$profiles = $this->service->management_accountSummaries->listManagementAccountSummaries();
					return $profiles;
				} else {
					echo '<br /><div class="notice notice-warning"><p>' . esc_html__( 'Notice: You must authenticate to access your web profiles.', 'wp-analytify' ) . '</p></div>';
				}
			} catch (Exception $e) {
				// Show admin notice if some exception occurs.
				WPANALYTIFY_Utils::handle_exceptions( $e->getErrors() );
				update_option( 'analytify_profile_exception', $e->getErrors() );
			}

		}


		public function pa_setting_url() {

			return admin_url( 'admin.php?page=analytify-settings' );

		}


		public function pt_save_data( $key_google_token ) {

			try {

				update_option( 'post_analytics_token', $key_google_token );
				if ( $this->pa_connect() ) { return true; }
			} catch (Exception $e) {

				echo $e->getMessage();
			}

		}

		/**
		 * Warning messages.
		 */
		public function profile_warning() {

			$profile_id     = get_option( 'pt_webprofile' );
			$acces_token    = get_option( 'post_analytics_token' );

			if ( ! isset( $acces_token ) || empty( $acces_token ) ) {

				echo "<div id='message' class='error'><p><strong>" . sprintf( esc_html__( 'Analytify is not active. Please %1$sAuthenticate%2$s in order to get started using this plugin.', 'wp-analytify' ) , '<a href="' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '">', '</a>' ) . '</p></div>';
			} else {

				if ( ! isset( $profile_id ) || empty( $profile_id ) ) {
					echo sprintf( esc_html__( '%1$s Google Analytics Profile is not set. Set the %2$s Profile %3$s' , 'wp-analytify' ), '<div class="error"><p><strong>', '<a href="' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '&tab=profile">', '</a></p></div>' );
				}
			}
		}

		/**
		 * get the Analytics data from ajax() call
		 */
		public function get_ajax_single_admin_analytics() {

			$start_date = '';
			$end_date   = '';
			$post_id    = 0 ;
			$start_date = sanitize_text_field( wp_unslash( $_POST['start_date'] ) );
			$end_date   = sanitize_text_field( wp_unslash( $_POST['end_date'] ) );
			$post_id    = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );

			$this->get_single_admin_analytics( $start_date, $end_date, $post_id, 1 );

			die();
		}

		/**
		* get the Analytics data for wp-admin posts/pages.
		*
		*/
		public function get_single_admin_analytics( $start_date = '', $end_date = '', $post_id = 0, $ajax = 0 ) {

			global $post;

			// Check Profile selection.
			if ( WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection( 'Analytify', '<br /><b class="wpa_notice_error">'. __('Select your website profile at Analytify->settings->profile tab to load stats.', 'wp-analytify' ) .'</b>' ) ) { return; }

			if ( 0 === $post_id ) {
				$u_post = '/'; // $url_post['path'];
			} else {
				$u_post = parse_url( get_permalink( $post_id ) );
			}

			if ( 'localhost' == $u_post['host'] ) {
				$filter = 'ga:pagePath==/'; // .$u_post['path'];
			} else {
				$filter = 'ga:pagePath==' . $u_post['path'] . '';

				// Url have query string incase of WPML.
				if ( isset( $u_post['query'] )  ) {
					$filter .= '?' . $u_post['query'];
				}

			}

			if ( '' == $start_date ) {

				$s_date = get_the_time( 'Y-m-d', $post->ID );
				if ( get_the_time( 'Y', $post->ID ) < 2005 ) {
					$s_date = '2005-01-01';
				}
			} else {
				$s_date = $start_date;
			}

			if ( '' == $end_date ) {
				$e_date = date( 'Y-m-d' );
			} else {
				$e_date = $end_date;
			}

			$show_settings = array();
			$show_settings = $this->settings->get_option( 'show_panels_back_end','wp-analytify-admin' );

			// Stop here, if user has disable backend analytics i.e OFF.
			if ( 'on' === $this->settings->get_option( 'disable_back_end','wp-analytify-admin' ) and 0 === $ajax ) {
				return;
			}


			 echo sprintf( esc_html__( '%1$s Displaying Analytics of this page from.%2$s to %3$s %4$s', 'wp-analytify' ), '<p>', date( 'jS F, Y', strtotime( $s_date ) ), date( 'jS F, Y', strtotime( $e_date ) ), '</p>') ;
			echo '<div class="analytify_wraper analytify_single_post_page">';
			if ( ! empty( $show_settings ) ) {

				if ( is_array( $show_settings ) ) {



					if ( in_array( 'show-overall-dashboard', $show_settings ) ) {

						$stats = $this->pa_get_analytics( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:percentNewSessions,ga:newUsers,ga:avgTimeOnPage',$s_date, $e_date, false, false, $filter );

						if ( isset( $stats->totalsForAllResults ) ) {

							include_once ANALYTIFY_ROOT_PATH . '/views/default/admin/single-general-stats.php';
							wpa_include_single_general( $this, $stats );
						}
					}
				}

				if ( has_action( 'wp_analytify_stats_under_post' ) ) {
					do_action( 'wp_analytify_stats_under_post' , $show_settings ,$s_date, $e_date , $filter );
				}



			} else {

				$stats = $this->pa_get_analytics( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:pageviewsPerSession,ga:percentNewSessions,ga:newUsers',$s_date, $e_date, false, false, $filter );

				if ( isset( $stats->totalsForAllResults ) ) {
					include_once ANALYTIFY_ROOT_PATH . '/views/default/admin/single-general-stats.php';
					wpa_include_single_general( $this, $stats );
				}
			}
			echo '</div>';
		}


		/**
		 * Pretty numbers
		 *
		 * @param int $num number.
		 */
		function wpa_pretty_numbers( $num ) {

			return round( ($num / 1000),2 ) . 'k';
		}

		/**
		 * Format numbers.
		 *
		 * @param int $num number.
		 */
		function wpa_number_format( $num ) {

			return number_format( $num );
		}

		/**
		 * Pretty time to display.
		 *
		 * @param int $time time.
		 */
		function pa_pretty_time( $time ) {

				// Check if numeric.
			if ( is_numeric( $time ) ) {

				$value = array(
					'years'   => '00',
					'days'    => '00',
					'hours'   => '',
					'minutes' => '',
					'seconds' => '',
					);

				if ( $time >= 31556926 ) {
					$value['years'] = floor( $time / 31556926 );
					$time           = ($time % 31556926);
				} //$time >= 31556926

				if ( $time >= 86400 ) {
					$value['days'] = floor( $time / 86400 );
					$time          = ($time % 86400);
				} //$time >= 86400
				if ( $time >= 3600 ) {
					$value['hours'] = str_pad( floor( $time / 3600 ), 1, 0, STR_PAD_LEFT );
					$time           = ($time % 3600);
				} //$time >= 3600
				if ( $time >= 60 ) {
					$value['minutes'] = str_pad( floor( $time / 60 ), 1, 0, STR_PAD_LEFT );
					$time             = ($time % 60);
				} //$time >= 60
						$value['seconds'] = str_pad( floor( $time ), 1, 0, STR_PAD_LEFT );
					// Get the hour:minute:second version.
				if ( '' != $value['hours'] ) {
					$attach_hours = '<sub>' . _x( 'h', 'Hour Time', 'wp-analytify' ) . '</sub> ';
				}
				if ( '' != $value['minutes'] ) {
					$attach_min = '<sub>' . _x( 'm', 'Minute Time', 'wp-analytify' ) . '</sub> ';
				}
				if ( '' != $value['seconds'] ) {
					$attach_sec = '<sub>' . _x( 's', 'Second Time', 'wp-analytify' ) . '</sub>';
				}
						return $value['hours'] . @$attach_hours . $value['minutes'] . @$attach_min . $value['seconds'] . $attach_sec;
					// return $value['hours'] . ':' . $value['minutes'] . ':' . $value['seconds'];
			} //is_numeric($time)
			else {
				return false;
			}
		}

		/**
		 * Check current user role.
		 *
		 * @since 1.2.6
		 * @param array $access_level selected access level.
		 * @return boolean
		 */
		public function pa_check_roles( $access_level ) {

			if ( is_user_logged_in() && isset( $access_level ) ) {

				global $current_user;
				$roles = $current_user->roles;
				//var_dump($roles);
				if ( array_intersect( $roles, $access_level ) ) {

					return true;
				} elseif ( is_super_admin( $current_user->ID ) && is_multisite() ) {

					return true;
				} else {

					return false;
				}
			}
		}

		/**
		 * Display a notice that can be dismissed.
		 *
		 * @since 1.3
		 */
		function analytify_admin_notice() {

			$profile_id     = get_option( 'pt_webprofile' );
			$acces_token    = get_option( 'post_analytics_token' );

			if ( current_user_can( 'install_plugins' ) ) {

				global $current_user ;
				$user_id = $current_user->ID;
				/* Check that the user hasn't already clicked to ignore the message */
				if ( ! get_user_meta($user_id, 'analytify_2_1_6_ignore') ) {
				echo '<div class="updated"><p>';
				printf(__('Thanks for updating <strong>Analytify</strong>! <a href="https://analytify.io/go/analytify-review" target="_blank" rel="noopener">Read</a> how thousands of user loving Analytify and sharing their story! <a href="https://analytify.io/go/analytify-review" target="_blank" rel="noopener"><strong>Click here</strong></a>.
					<a href="%1$s">[Hide Notice]</a>'),  admin_url( 'admin.php?page=analytify-dashboard&analytify_2_1_6_ignore=0' ));
       			 echo "</p></div>";
				 }
			}

			/* Show notices */
	        if ( ! isset( $acces_token ) || empty( $acces_token ) || ! get_option( 'pa_google_token' ) ) {

				echo sprintf( esc_html__( '%1$s %2$s %3$sNotice:%4$s %5$sConnect%6$s %4$s Analytify with your Google account. %7$s %8$s', 'wp-analytify' ), '<div class="error notice is-dismissible">', '<p>', '<b>', '</b>', '<b><a style="text-decoration:none" href=' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '>', '</a>','</p>', '</div>' );
			} else {

				if ( ! WP_ANALYTIFY_FUNCTIONS::is_profile_selected() ) {

					echo '<div class="error notice is-dismissible"><p>' . sprintf( esc_html__( 'Congratulations! Analytify is now authenticated. Please select your website profile %1$s here %2$s to get started.', 'wp-analytify' ), '<a style="text-decoration:none" href="' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '#wp-analytify-profile">','</a>' ) . '</p></div>';
				}
			}

		}


		/**
		 * [analytify_nag_ignore Ignore notice]
		 * @return void
		 */
		function analytify_nag_ignore() {

			global $current_user;

			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset( $_GET['analytify_nag_ignore'] ) && '0' === $_GET['analytify_nag_ignore'] ) { // Input var okay.
				add_user_meta( $user_id, 'analytify_2_1_6_ignore', 'true', true );
			}

			/* If user clicks to ignore the 2.1.5 notice, add that to their user meta */
			if ( isset( $_GET['analytify_2_1_6_ignore'] ) && '0' === $_GET['analytify_2_1_6_ignore'] ) { // Input var okay.
				add_user_meta( $user_id, 'analytify_2_1_6_ignore', 'true', true );
			}


		}

		/**
		 * Show pointers for announcements
		 *
		 * @return void
		 */
		public function pa_welcome_message() {

			$pointer_content  = '<h3>Announcement:</h3>';
			$pointer_content .= '<p><input type="checkbox" name="wpa_allow_tracking" value="1" id="wpa_allow_tracking"> ';
			$pointer_content .= 'Help us making Analytify even better by sharing very basic plugin usage data.';

			if ( ! WPANALYTIFY_Utils::is_active_pro() ) {
				$pointer_content .= ' Opt-in and receive a $10 Off coupon for <a href="https://analytify.io/upgrade-from-free">Analytify PRO</a>.</p>';
			}

			?>

			<script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {

                if(typeof(jQuery().pointer) != 'undefined') {

                    $('#toplevel_page_analytify-dashboard').pointer({

						content: '<?php echo $pointer_content; ?>',
                        position: {
                            edge: 'left',
                            align: 'center'
                        },
                        close: function() {
                            $.post( ajaxurl, {
                                pointer: 'tracking',
                                wpa_allow:  $('#wpa_allow_tracking:checked').val(),
                                action: 'analytify_dismiss_pointer'
                            });

                           <?php if ( ! WPANALYTIFY_Utils::is_active_pro() ) { ?>
                            	if($('#wpa_allow_tracking:checked').val() == 1) alert( '<?php _e( 'Thankyou!\nYour Coupon code is Analytify2016', 'wp-analytify' ) ?>' );
                           <?php  } ?>
                        }
                    }).pointer('open');
                };
            });
            //]]>
        </script>

		<?php
		}


		/**
		 *	Check and Dismiss review message.
		 *
		 *	@since 1.3
		 */
		private function review_dismissal() {

			//delete_site_option( 'wp_analytify_review_dismiss' );
			if ( ! is_admin() ||
				! current_user_can( 'manage_options' ) ||
				! isset( $_GET['_wpnonce'] ) ||
				! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'analytify-review-nonce' ) ||
				! isset( $_GET['wp_analytify_review_dismiss'] ) ) {

				return;
			}

			add_site_option( 'wp_analytify_review_dismiss', 'yes' );
		}

		/**
		 * Ask users to review our plugin on .org
		 *
		 * @since 1.3
		 * @return boolean false
		 */
		public function analytify_review_notice() {

			$this->review_dismissal();
			$this->review_prending();

			$activation_time 	= get_site_option( 'wp_analytify_active_time' );
			$review_dismissal	= get_site_option( 'wp_analytify_review_dismiss' );

			if ( 'yes' == $review_dismissal ) {
				return;
			}

			if ( ! $activation_time ) {

				$activation_time = time();
				add_site_option( 'wp_analytify_active_time', $activation_time );
			}

			// 1296000 = 15 Days in seconds.
			if ( time() - $activation_time > 1296000 ) {
				add_action( 'admin_notices' , array( $this, 'analytify_review_notice_message' ) );
			}

		}

		/**
		 * Set time to current so review notice will popup after 14 days
		 *
		 * @since 2.0.9
		 */
		function review_prending() {

			// delete_site_option( 'wp_analytify_review_dismiss' );
			if ( ! is_admin() ||
				! current_user_can( 'manage_options' ) ||
				! isset( $_GET['_wpnonce'] ) ||
				! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'analytify-review-nonce' ) ||
				! isset( $_GET['wp_analytify_review_later'] ) ) {

				return;
			}

			// Reset Time to current time.
			update_site_option( 'wp_analytify_active_time', time() );

		}

		/**
		 * Review notice message
		 *
		 * @since  1.3
		 */
		public function analytify_review_notice_message() {

			$scheme      = (parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY )) ? '&' : '?';
			$url         = $_SERVER['REQUEST_URI'] . $scheme . 'wp_analytify_review_dismiss=yes';
			$dismiss_url = wp_nonce_url( $url, 'analytify-review-nonce' );

			$_later_link = $_SERVER['REQUEST_URI'] . $scheme . 'wp_analytify_review_later=yes';
			$later_url   = wp_nonce_url( $_later_link, 'analytify-review-nonce' );

			// echo sprintf( esc_html__( '%1$s %2$s You have been using the %3$s for some time now, do you like it? If so, please consider leaving us a review on WordPress.org! It would help us out a lot and we would really appreciate it. %4$s %5$s Leave a Review %6$s %7$s No thanks %8$s%9$s%10$s', 'wp-analytify' ), '<div class="updated">', '<p>', '<a href="' . esc_url( admin_url( 'admin.php?page=analytify-dashboard' ) ) . '">WP Analytify</a>', '<br><br>', '<a onclick="location.href=\'' . esc_url( $dismiss_url ) . '\';" class="button button-primary" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/wp-analytify?rate=5#postform' ) . '" target="_blank">', '</a>', '<a href="' . esc_url( $dismiss_url ) . '">', '</a>', '</p>', '</div>' );
		?>
			<div class="analytify-review-notice">
				<div class="analytify-review-thumbnail">
					<img src="<?php echo plugins_url( 'assets/images/notice-logo.svg', __FILE__ ) ?>" alt="">
				</div>
				<div class="analytify-review-text">
					<h3><?php _e( 'Leave A Review?', 'wp-analytify' ) ?></h3>
					<p><?php _e( 'We hope you\'ve enjoyed using Analytify! Would you consider leaving us a review on WordPress.org?', 'wp-analytify' ) ?></p>
					<ul class="analytify-review-ul"><li><a href="https://wordpress.org/support/view/plugin-reviews/wp-analytify?rate=5#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'wp-analytify' ) ?></a></li>
             <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', 'wp-analytify' ) ?></a></li>
             <li><a href="<?php echo $later_url ?>"><span class="dashicons dashicons-calendar-alt"></span><?php _e( 'Maybe Later', 'wp-analytify' ) ?></a></li>
             <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'wp-analytify' ) ?></a></li></ul>
				</div>
			</div>
		<?php
		}


		/**
		 * Include required ajax files.
		 * Ajax functions for admin and the front-end
		 */
		public function ajax_includes() {
			include_once( 'inc/class-wpa-ajax.php' );
		}

		/**
		 * Display stats link under each post row
		 *
		 * @param  Array  $actions [description].
		 * @param  Object $post    Current post data.
		 * @return Array
		 *
		 * @since 1.3.5
		 */
		function post_rows_stats( $actions, $post ) {

			if ( 'publish' === $post->post_status ) {
				$actions['post_row_stats'] = '<a href="' . admin_url( 'post.php?post=' . $post->ID . '&action=edit#pa-single-admin-analytics' ) . '" title="View Stats of “' . get_the_title( $post ) . '”">Stats</a>'; }

			return $actions;
		}

		/**
		 * Display stats button in the publish box
		 *
		 * @param  Object $post WP_POST Object.
		 * @return void
		 *
		 * @since 1.3.5
		 */
		function post_submitbox_stats_action( $post ) {

			if ( 'publish' === $post->post_status &&  in_array( $post->post_type, $this->settings->get_option( 'show_analytics_post_types_back_end','wp-analytify-admin', array() ) ) ) {
				echo '<a id="view_stats_analytify" href="' . esc_url( admin_url( 'post.php?post=' . esc_html( $post->ID ) . '&action=edit#pa-single-admin-analytics' ) ) . '" title="View Stats of “' . get_the_title( $post ) . '”" class="button button-primary button-large" style="float:left">View Stats</a>'; }
		}


		/**
		 * Track 404, JS and Ajax Errors in Google Analytics.
		 *
		 * @since 2.0.0
		 */
		function track_miscellaneous_errors() {

			// 404 tracking hits in Google analytics
			if ( 'on' == $this->settings->get_option( '404_page_track', 'wp-analytify-advanced' ) ) {
				if ( is_404() ) {
					$current_url = home_url( add_query_arg( null, null ) );
					echo '<script>
								if (typeof ga !== "undefined") {
									ga("send", "event", "404 Error", "Page Not Found" , "' . $current_url . '" );
								}
							 </script>';
				}
			}

			// JS tracking hits in Google analytics
			if ( 'on' == $this->settings->get_option( 'javascript_error_track', 'wp-analytify-advanced' )  ) {
				echo "<script>
                     function trackJavaScriptError(e) {
                         var errMsg = e.message;
                         var errSrc = e.filename + ': ' + e.lineno;
                         ga('send', 'event', 'JavaScript Error', errMsg, errSrc, { 'nonInteraction': 1 });
                     }
					if (typeof ga !== 'undefined') {
					   window.addEventListener('error', trackJavaScriptError, false);
					}
				</script>";
			}

			//AJAX tracking hits in Google analytics
			if ( 'on' == $this->settings->get_option( 'ajax_error_track', 'wp-analytify-advanced' )  ) {
				echo "<script>
						if (typeof ga !== 'undefined') {

                             jQuery(document).ajaxError(function (e, request, settings) {
                                 ga ('send' , 'event' , 'Ajax Error' ,   request.statusText  ,settings.url  , { 'nonInteraction': 1 });
                            });
						}
					</script>" ;
			}

		}

		function load_settings_assets() {

		}

		/**
		 * process logout and clear stored options.
		 */
		public function logout() {

			//var_dump(get_transient( 'profiles_list_summary' ));

			if ( isset( $_POST['wp_analytify_log_out'] ) ) {

				delete_option( 'pt_webprofile' );
				delete_option( 'pt_webprofile_dashboard' );
				delete_option( 'pt_webprofile_url' );
				delete_option( 'pa_google_token' );
				//delete_option( 'show_tracking_pointer_1' );
				delete_option( 'post_analytics_token' );
				delete_option( 'hide_profiles' );
				delete_option( 'profiles_list_summary' ); //profiles_list_summary

				$update_message = sprintf( esc_html__( '%1$s %2$s %3$s Authentication Cleared login again. %4$s %5$s %6$s', 'wp-analytify' ), '<div id="setting-error-settings_updated" class="updated notice is-dismissible settings-error below-h2">', '<p>', '<strong>', '</strong>', '</p>', '</div>' );
			}

		}

	}

} // End if class_exists check


// ===================== active - inactive - delete hooks ==========================

register_activation_hook( __FILE__, 'wp_analytify_activate' ); //active
register_deactivation_hook( __FILE__, 'wp_analytify_de_activate' ); //in-active
register_uninstall_hook( __FILE__, 'wp_analytify_uninstall' ); // delete


/**
 * Run on plugin activation.
 *
 * @since       1.2.2
 * @return      void
 */

function wp_analytify_activate() {

	// If user has opt-in send activate notification.
	if ( get_site_option( '_analytify_optin' ) == 'yes' ) {
		analytify_send_data( array( 'action' => 'Activate' ) );
	}

	// Return if settings already added in DB.
	$_admin_settings = get_option( 'wp-analytify-admin' );
	if ( 'on' ===  $_admin_settings['disable_back_end']  && ! empty( $_admin_settings['show_analytics_roles_back_end'] ) ) {
		return;
	}

	// Load default settings on new install.
	if ( ! get_option( 'analytify_default_settings' ) ) {

		$profile_tab_settings = array (
			 'exclude_users_tracking'  => array( 'administrator' ),
		);
		update_option( 'wp-analytify-profile', $profile_tab_settings );

		$admin_tab_settings = array(
			 'disable_back_end'                   => 'on',
			 'show_analytics_roles_back_end'      => array( 'administrator', 'editor' ),
			 'show_analytics_post_types_back_end' => array( 'post', 'page' ),
			 'show_panels_back_end'               => array( 'show-overall-dashboard', 'show-social-dashboard', 'show-geographic-dashboard', 'show-system-stats', 'show-keywords-dashboard', 'show-referrer-dashboard' )
		);
		update_option( 'wp-analytify-admin', $admin_tab_settings );

		$dashboard_tab_settings['show_analytics_panels_dashboard'] = array(
			 'show-real-time',
			 'show-compare-stats',
			 'show-overall-dashboard',
			 'show-top-pages-dashboard',
			 'show-geographic-dashboard',
			 'show-system-stats',
			 'show-keywords-dashboard',
			 'show-social-dashboard',
			 'show-referrer-dashboard',
			 'show-page-stats-dashboard',
		);
		$dashboard_tab_settings['show_analytics_roles_dashboard'] = array(
				'administrator'
		);
		update_option( 'wp-analytify-dashboard' , $dashboard_tab_settings );

		// Update meta so default settings load only one time.
		update_option( 'analytify_default_settings', 'done' );
	}
}

/**
 * Delete option values on plugin deactivation.
 *
 * @since       1.2.2
 * @return      void
 */
function wp_analytify_de_activate() {

	// if ( 1 == get_option( 'wpa_allow_tracking' ) || 'enabled' == get_option( 'analytify_opt' ) ) {
	// 	send_status_analytify( get_option( 'admin_email' ), 'in-active' );
	// }

	// delete welcome page check on de-activate.
	delete_option( 'show_welcome_page' );
}

/**
* Delete plugin settings meta on deleting the plugin
*
* @return void
*/
function wp_analytify_uninstall() {

	if ( 'enabled' == get_option( 'analytify_opt' ) ) {
		analytify_send_data( array( 'action' => 'Uninstall' ) );
	}

	// if ( 1 == get_option( 'wpa_allow_tracking' )  || 'enabled' == get_option( 'analytify_opt' ) ) {
	// 	send_status_analytify( get_option( 'admin_email' ), 'delete' );
	// }

	delete_option( 'analytify_default_settings' );
	delete_option( 'wp-analytify-admin' );

}

/**
 * Send status of subscriber who opt-in for improving the product.
 *
 * @param string $email  users email.
 * @param string $status plugin status.
 */
function send_status_analytify( $email, $status ) {

	$url = 'https://analytify.io/plugin-manager/';
	if ( '' === $email ) {
		$email = 'track@analytify.io';
	}
	$fields = array(
		'email' 		=> $email,
		'site' 			=> get_site_url(),
		'status' 		=> $status,
		'type'			=> 'FREE',
		);
	wp_remote_post( $url, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
		)
	);
}


/**
 * Wrapper function to send data.
 * @param  [arrays]  $args.
 *
 * @since 2.0.14
 *
 */
function analytify_send_data( $args ) {

	$cuurent_user = wp_get_current_user();
	$fields = array(
		'email' 		        => get_option( 'admin_email' ),
		'website' 			    => get_site_url(),
		'action'            => '',
		'reason'            => '',
		'reason_detail'     => '',
		'display_name'			=> $cuurent_user->display_name,
		'blog_language'     => get_bloginfo( 'language' ),
		'wordpress_version' => get_bloginfo( 'version' ),
		'plugin_version'    => ANALYTIFY_VERSION,
		'php_version'				=> PHP_VERSION,
		'plugin_name' 			=> 'Analytify',
	);

	$args = array_merge( $fields, $args );
	$response = wp_remote_post( 'https://analytify.io/', array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(),
		'body'        => $args,
	) );


	// if ( 200 == wp_remote_retrieve_response_code( $response ) ){
	// 	update_option( '_analytify_optin', 'yes' );
	// }

}

// ====================== active - inactive - delete hooks =========================


/**
 * Create instance of wp-analytify class.
 */
function load_wp_analytify_free() {

	return WP_Analytify::get_instance();
}


add_action( 'plugins_loaded', 'analytify_free_instance', 10 );

function analytify_free_instance() {
	$GLOBALS['WP_ANALYTIFY'] = load_wp_analytify_free();
}

?>
