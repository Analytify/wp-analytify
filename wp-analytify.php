<?php
/**
 * Plugin Name: Analytify - Google Analytics Dashboard
 * Plugin URI: https://analytify.io/details
 * Description: Analytify brings a brand new and modern feeling of Google Analytics superbly integrated within the WordPress.
 * Version: 4.1.1
 * Author: Analytify
 * Author URI: https://analytify.io/
 * License: GPLv3
 * Text Domain: wp-analytify
 * Tested up to: 5.8
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

		// protected 	$transient_timeout;
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
			$upload_dir = wp_upload_dir( null, false );
			$this->define( 'ANALYTIFY_LOG_DIR', $upload_dir['basedir'] . '/analytify-logs/' );
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

			require_once ANALYTIFY_LIB_PATH . 'logs/class-analytify-log-handler-interface.php';
			require_once ANALYTIFY_LIB_PATH . 'logs/class-analytify-logger-interface.php';
			require_once ANALYTIFY_LIB_PATH . 'logs/class-analytify-log-levels.php';
			require_once ANALYTIFY_LIB_PATH . 'logs/class-analytify-logger.php';
			require_once ANALYTIFY_LIB_PATH . 'logs/abstract-analytify-log-handler.php';
			require_once ANALYTIFY_LIB_PATH . 'logs/class-analytify-log-handler-file.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-logs.php';

			include_once ANALYTIFY_PLUGIN_DIR . '/inc/wpa-core-functions.php';

			include_once ANALYTIFY_PLUGIN_DIR . '/inc/class-wpa-adminbar.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-rest-api.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/inc/class-wpa-ajax.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/classes/class.upgrade.php';
			include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-dashboard-widget.php';

			include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-gdpr-compliance.php';

			include_once ANALYTIFY_PLUGIN_DIR . '/classes/user_optout.php';

			include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-email.php';

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
			// add_action( 'admin_menu', array( $this, 'modules_fallback_page' ) );

			add_action( 'plugin_action_links', 	array( $this, 'plugin_action_links' ), 10, 2 );
			add_action( 'plugin_row_meta', 			array( $this, 'plugin_row_meta' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );

			add_action( 'admin_notices', array( $this, 'analytify_admin_notice' ) );

			add_action( 'wp_head', array( $this, 'analytify_add_analytics_code' ) );
			add_action( 'wp_head', array( $this, 'analytify_add_manual_analytics_code' ) );

			add_action( 'wp_ajax_get_ajax_single_admin_analytics', array( $this, 'get_ajax_single_admin_analytics' ) );

			add_action( 'wp_ajax_set_module_state', array( $this, 'set_module_state' ) );

			// Show analytics sections under the posts/pages in the metabox.
			add_action( 'add_meta_boxes', array( $this, 'show_admin_single_analytics_add_metabox' ) );


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
			
			// Remove submenu pages.
			add_filter( 'submenu_file', array( $this, 'remove_submenu_pages' ) );

			// Show links at post rows.
			add_filter( 'post_row_actions', array( $this, 'post_rows_stats' ), 10, 2 );
			add_filter( 'page_row_actions', array( $this, 'post_rows_stats' ), 10, 2 );
			add_action( 'post_submitbox_minor_actions', array( $this, 'post_submitbox_stats_action' ), 10, 1 );

			// Add after_plugin_row... action for pro plugin
			// add_action( 'after_plugin_row_wp-analytify/wp-analytify.php', array( $this, 'wpa_plugin_row'), 11, 2 );

			add_action( 'wp_footer' , array( $this, 'track_miscellaneous_errors' ) );
			add_action( 'admin_footer',	array( $this, 'add_deactive_modal' ) );
			add_action( 'admin_init', array( $this, 'redirect_optin' ) );

			add_action( 'admin_init', array( $this, 'dismiss_notices' ) );

			// add_action( 'admin_init', array( $this, 'analytify_buy_pro_notice' ) );
			// add_action( 'admin_notices', array( $this, 'bf_admin_notice' ) );
			// add_action( 'admin_init', array( $this, 'bf_nag_ignore' ) );
			// add_action( 'admin_notices', array( $this, 'winter_sale_promo' ) );
			// add_action( 'admin_init', array( $this, 'winter_sale_dismiss_notice' ) );
			add_action( 'analytify_cleanup_logs', array( $this, 'analytify_cleanup_logs' ) );

			// Optimzation,
			// Update profile summary option for Newly installed users.
			add_action( 'update_option_wp-analytify-profile', array( $this, 'update_profiles_list_summary' ), 10, 2 );

			// Optimzation,
			// Update profile summary option for already installed version.
			add_action( 'admin_init', array( $this, 'update_profile_list_summary_on_update' ), 1 );
			add_filter( 'plugin_row_meta' , array( $this, 'add_rating_icon' ), 50, 2 );

			add_action( 'init', array( $this, 'init_gdpr_compliance' ), 1 );
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
						
						<?php WPANALYTIFY_Utils::date_form( $start_date, $end_date, array( 'input_submit_id' => 'view_analytics' ) ); ?>

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

			// Check for GDPR compliance.
			if ( Analytify_GDPR_Compliance::is_gdpr_compliance_blocking() ) {
				return;
			}

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

					// Check the tracking method.
					if ( 'gtag' === ANALYTIFY_TRACKING_MODE ) {
					$ga_code = $this->output_gtag_code( $UA_CODE ); 
					} else {
						$ga_code = $this->output_ga_code( $UA_CODE );
					}

					echo apply_filters( 'analytify_ga_script', $ga_code );
				}
			}
		}


		/**
		 * Add Google Manual Analytics JS code
		 */
		 public function analytify_add_manual_analytics_code() {

			// Return if already authenticated. 
			// Should use tracking code from profiles option instead.
			if ( get_option( 'pa_google_token' ) ) {
				return;
			}

			$manual_ua_code = $this->settings->get_option( 'manual_ua_code', 'wp-analytify-authentication', false );

			if ( ! $manual_ua_code ) {
				return;
			}

			global $current_user;
       		$roles = $current_user->roles;

			if ( in_array( 'administrator', $roles ) ) {
				echo '<!-- This user is disabled from tracking by Analytify !-->';
			} else {
				// Always use gtag mode for manual code unless filterd explicitly.
				if ( apply_filters( 'analytify_manaul_ga_script', false ) ) {
					echo apply_filters( 'analytify_ga_script', $this->output_ga_code( $manual_ua_code )  );
				} else {
					echo apply_filters( 'analytify_gtag_script', $this->output_gtag_code( $manual_ua_code ) );
				}
			}
    	}

		/**
		 * Generate gtag code.
		 *
		 * @param  [string] $UA_CODE Google Analytics UA code.
		 * @since 3.0
		 * @return $gtag_code
		*/
    	private function output_gtag_code( $UA_CODE ) {
			
			ob_start();

      		echo sprintf( esc_html__( '%2$s This code is added by WP Analytify (%1$s) %4$s %3$s', 'wp-analytify' ), ANALYTIFY_VERSION, '<!--', '!-->', 'https://analytify.io/downloads/analytify-wordpress-plugin/' );

			$anonymize_ip = ( 'on' === $this->settings->get_option( 'anonymize_ip', 'wp-analytify-advanced' ) ) ? 'true' : 'false';
			$force_ssl = ( 'on' === $this->settings->get_option( 'force_ssl', 'wp-analytify-advanced' ) ) ? 'true' : 'false';
			$allow_display_features = ( 'on' === $this->settings->get_option( 'demographic_interest_tracking', 'wp-analytify-advanced' ) ) ? 'true' : 'false';
			
			// check if 'linker_cross_domain_tracking' is enabled
			$linker_cross_domain_tracking = ( 'on' === $this->settings->get_option( 'linker_cross_domain_tracking', 'wp-analytify-advanced' ) ) ? true : false;

			if ( $linker_cross_domain_tracking ){
				// get 'linked_domain' field
				$all_linked_domains = $this->settings->get_option( 'linked_domain', 'wp-analytify-advanced' );
				$all_linked_domains = trim( $all_linked_domains );

				if( !empty($all_linked_domains) ){
					// if the field is not empty

					// removing single and double quotes
					$all_linked_domains = str_replace("'", "", $all_linked_domains);
					$all_linked_domains = str_replace('"', '', $all_linked_domains);

					$list_linked_domains = explode(',', $all_linked_domains );
					$number_of_linked_domains = count($list_linked_domains);
					
					if( $number_of_linked_domains > 0 ){
						// there are multiple domains

						// remove all the spaces
						$linked_domains = preg_replace('/\s+/', '', $all_linked_domains);

						// add single quotes arround domains
						$linked_domains = "'".str_replace(",", "', '", $linked_domains)."'";

						// remove any empty quotes
						$linked_domains = str_replace(", ''", "", $linked_domains);

					}else{
						// if there is only only domain
						$linked_domains = "'".$all_linked_domains."'";
					}

				}else{
					// if the field is empty
					$linker_cross_domain_tracking = false;
				}			
				
			} ?>

			<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_html( $UA_CODE ); ?>"></script>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			const configuration = {
				'anonymize_ip': <?php echo $anonymize_ip; ?>,
				'forceSSL': <?php echo $force_ssl; ?>,
				'allow_display_features': <?php echo $allow_display_features; ?>,
				<?php if( $linker_cross_domain_tracking ){ ?>
					'linker': {
						'domains': [<?php echo $linked_domains; ?>]
					}
				<?php } ?>				
			};
			const UACode = '<?php echo esc_html( $UA_CODE ); ?>';

			gtag('config', UACode, configuration);

			<?php
			if ( 'on' === $this->settings->get_option( 'track_user_id', 'wp-analytify-advanced' ) && is_user_logged_in() ) {
				echo "gtag('set', {'userId': " . esc_html( get_current_user_id() ) . ' });';
			}
			if ( $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' ) ) {
				echo $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' );
			}
			
			do_action( 'ga_ecommerce_js' );
			do_action( 'analytify_tracking_code_before_pageview' ); ?>

			</script>

			<?php
			echo sprintf( esc_html__( '%2$s This code is added by WP Analytify (%1$s) %3$s', 'wp-analytify' ), ANALYTIFY_VERSION, '<!--', '!-->' );
			$gtag_code = ob_get_contents();
			ob_end_clean();
			return $gtag_code;
		}

		/**
		 * Generate gtag code.
		 *
		 * @param  [string] $UA_CODE Google Analytics UA code.
		 * @since 3.0
		 * @return $ga_code
		*/
		public function output_ga_code( $UA_CODE ) {
			ob_start();
			$src = apply_filters( 'analytify_output_ga_js_src', '//www.google-analytics.com/analytics.js' );
			echo sprintf( esc_html__( '%2$s This code is added by WP Analytify (%1$s) %4$s %3$s', 'wp-analytify' ), ANALYTIFY_VERSION, '<!--', '!-->', 'https://analytify.io/downloads/analytify-wordpress-plugin/' );
			?>
			<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})

			(window,document,'script','<?php echo $src ?>','ga');
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
			do_action( 'analytify_tracking_code_before_pageview' );
			echo "ga('send', 'pageview');";

			?>
			</script>

			<?php
			echo sprintf( esc_html__( '%2$s This code is added by WP Analytify (%1$s) %3$s', 'wp-analytify' ), ANALYTIFY_VERSION, '<!--', '!-->' );
			$ga_code = ob_get_contents();
			ob_end_clean();
			return $ga_code;
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

				$settings_link .= sprintf( esc_html__( '%1$s Support %2$s | ', 'wp-analytify'), '<a target="blank" href="https://wordpress.org/support/plugin/wp-analytify">', '</a>' );

				if ( ! class_exists( 'WP_Analytify_Pro' ) ) {
					$settings_link .= sprintf( esc_html__( '%1$s Get Analytify Pro %2$s |', 'wp-analytify' ),  '<a  href="https://analytify.io/pricing/?utm_source=analytify-lite&utm_medium=plugin-action-link&utm_campaign=pro-upgrade" target="_blank" style="color:#3db634;">', '</a>' );
				}

				if( 'yes' == get_option( '_analytify_optin' ) ){
					$settings_link .= sprintf( esc_html__( '%1$s Opt Out %2$s | ', 'wp-analytify'), '<a class="opt-out" href="' . admin_url( 'admin.php?page=analytify-settings' ) . '">', '</a>' );
				} else {
					$settings_link .= sprintf( esc_html__( '%1$s Opt In %2$s | ', 'wp-analytify'), '<a href="' . admin_url( 'admin.php?page=analytify-optin' ) . '">', '</a>' );
				}


				$settings_link .= sprintf( esc_html__( '%1$s Dashboard %2$s ', 'wp-analytify'), '<a href="' . admin_url( 'admin.php?page=analytify-dashboard' ) . '">', '</a>' );

				// $settings_link .= sprintf( esc_html__( '%1$s Help %2$s  ', 'wp-analytify'), '<a href="' . admin_url( 'index.php?page=wp-analytify-getting-started' ) . '">', '</a>'  );
				array_unshift( $links, $settings_link );


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

				sprintf( esc_html__( '%1$s Get FREE Help %2$s', 'wp-analytify' ), '<a target="blank" href="https://wordpress.org/support/plugin/wp-analytify">', '</a>' ),
				sprintf( esc_html__( '%1$s Explore Add Ons %2$s', 'wp-analytify' ), '<a href="https://analytify.io/add-ons/?ref=27">', '</a>' ),
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

				$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION;

				echo '<div class="wrap"><h2 style="display: none;"></h2></div>

				<div class="wpanalytify"><div class="wpb_plugin_wraper">

				<div class="wpb_plugin_header_wraper">
				<div class="graph"></div>

				<div class="wpb_plugin_header">

				<div class="wpb_plugin_header_title"></div>

				<div class="wpb_plugin_header_info">
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">Changelog - v'. $version .'</a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="'. plugins_url( 'assets/images/logo.svg', __FILE__ ) .'" alt="Analytify">
				</div>
				</div></div><div class="analytify-settings-body-container"><div class="wpb_plugin_body_wraper"><div class="wpb_plugin_body">';
					$this->settings->rendered_settings();
					$this->settings->show_tabs();
					echo '<div class="wpb_plugin_tabs_content">';
					$this->settings->show_forms();
					echo '</div>';

				echo '</div></div></div></div>';

				// include_once( ANALYTIFY_ROOT_PATH . '/inc/options-settings.php' );

			}else if ( strpos( $screen->base, 'analytify-logs' ) !== false ) {
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
			if ( $page == 'analytify_page_analytify-settings' || $page == 'analytify_page_analytify-campaigns' ) {
				wp_enqueue_style( 'jquery_tooltip', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', false, ANALYTIFY_VERSION);
			}

			// for Single Page/Post Stats.
			if ( $page == 'analytify_page_analytify-settings' || $page == 'post.php' || $page == 'post-new.php' ) {
				wp_enqueue_style( 'chosen', plugins_url( 'assets/old/css/chosen.min.css', __FILE__ ) );
			}


			if ( strpos( $page, 'analytify' ) !== false || $page == 'post.php' || $page == 'post-new.php' ||  $page == 'index.php' ) {
				wp_enqueue_style( 'wp-analytify-style', plugins_url( 'assets/old/css/wp-analytify-style.css', __FILE__ ), false, ANALYTIFY_VERSION );
				wp_enqueue_style( 'wp-analytify-default-style', plugins_url( 'assets/default/css/styles.css', __FILE__ ), false, ANALYTIFY_VERSION );

				$conditional_style = '';

				// Filter dashboard header animation.
				if ( apply_filters( 'analytify_dashboard_head_animate', true ) ) {
					$conditional_style .= '
					.wpanalytify .graph {
						-webkit-animation: graph_animation 130s linear infinite;
						-moz-animation: graph_animation 130s linear infinite;
						-o-animation: graph_animation 130s linear infinite;
						animation: graph_animation 130s linear infinite;
					}
					.wpanalytify .graph:after {
						-webkit-animation: graph_animation 250s linear infinite;
						-moz-animation: graph_animation 250s linear infinite;
						-o-animation: graph_animation 250s linear infinite;
						animation: graph_animation 250s linear infinite;
					}';
				}
                
				// Add conditional style.
      			wp_add_inline_style( 'wp-analytify-default-style', $conditional_style );
			}

			wp_enqueue_style( 'wp-analytify-utils-style', plugins_url( 'assets/default/css/utils.css', __FILE__ ), false, ANALYTIFY_VERSION );
			// For WP Pointer
			if ( get_option( 'show_tracking_pointer_1' ) != 1 ) { wp_enqueue_style( 'wp-pointer' ); }

		}


		/**
		 * Loading admin scripts JS for the plugin.
		 */
		public function admin_scripts( $page ) {

			wp_enqueue_script( 'wp-analytify-script-js', plugins_url( 'assets/old/js/wp-analytify.js', __FILE__ ), array( 'jquery' ), ANALYTIFY_VERSION );

			global $post_type;

			// for main page
			if ( $page == 'index.php' || $page == 'toplevel_page_analytify-dashboard' || $page == 'analytify_page_analytify-woocommerce' || $page == 'analytify_page_edd-dashboard' || $page == 'analytify_page_analytify-campaigns' || $page == 'analytify_page_analytify-goals' || $page == 'analytify_page_analytify-forms' || $page == 'analytify_page_analytify-dimensions'  || $page == 'analytify_page_analytify-authors' || $page == 'analytify_page_analytify-events' || $page == 'analytify_page_analytify-forms' || $page == 'analytify_page_analytify-promo' || in_array( $post_type, $this->settings->get_option( 'show_analytics_post_types_back_end','wp-analytify-admin', array() ) ) ) {

				wp_enqueue_script( 'moment', plugins_url( 'assets/default/js/moment.min.js', __FILE__ ), array( 'jquery' ), '2.18.1' );
       			wp_enqueue_script( 'pikaday-js', plugins_url( 'assets/default/js/pikaday.js', __FILE__ ), array( 'moment' ), '1.8.2' );

				wp_enqueue_script( 'analytify-dashboard-js', plugins_url( 'assets/default/js/wp-analytify-dashboard.js', __FILE__ ), array( 'pikaday-js' ), ANALYTIFY_VERSION );
				wp_localize_script( 'analytify-dashboard-js', 'analytify_dashboard', array(
						'i18n' => array(
							'previousMonth' => __( 'Previous Month', 'wp-analytify' ),
							'nextMonth'     => __( 'Next Month', 'wp-analytify' ),
							'months'        => array(
								__( 'January', 'wp-analytify' ),
								__( 'February', 'wp-analytify' ),
								__( 'March', 'wp-analytify' ),
								__( 'April', 'wp-analytify' ),
								__( 'May', 'wp-analytify' ),
								__( 'June', 'wp-analytify' ),
								__( 'July', 'wp-analytify' ),
								__( 'August', 'wp-analytify' ),
								__( 'September', 'wp-analytify' ),
								__( 'October', 'wp-analytify' ),
								__( 'November', 'wp-analytify' ),
								__( 'December1', 'wp-analytify' ),
							),
							'weekdays'      => array(
								__( 'Sunday', 'wp-analytify' ),
								__( 'Monday', 'wp-analytify' ),
								__( 'Tuesday', 'wp-analytify' ),
								__( 'Wednesday', 'wp-analytify' ),
								__( 'Thursday', 'wp-analytify' ),
								__( 'Friday', 'wp-analytify' ),
								__( 'Saturday', 'wp-analytify' ),
							),
							'weekdaysShort' => array(
								__( 'Sun', 'wp-analytify' ),
								__( 'Mon', 'wp-analytify' ),
								__( 'Tue', 'wp-analytify' ),
								__( 'Wed', 'wp-analytify' ),
								__( 'Thu', 'wp-analytify' ),
								__( 'Fri', 'wp-analytify' ),
								__( 'Sat', 'wp-analytify' ),
							),
						),
					)
				);
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
				wp_localize_script( 'analytify-settings-js', 'analytify_settings', array(
					'is_hide_profile' =>  $this->settings->get_option( 'hide_profiles_list', 'wp-analytify-profile', 'off' ),
					'is_authenticate' => (bool)get_option( 'pa_google_token' ) ,
				) );
			}

			// Addons page script.
			if ( 'analytify_page_analytify-addons' === $page ) {
				wp_enqueue_script( 'analytify-addons-js', plugins_url( 'assets/default/js/wp-analytify-addons.js', __FILE__ ), array( 'jquery'), ANALYTIFY_VERSION );
				wp_localize_script( 'analytify-addons-js', 'analytify_addons', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce('addons')
				) );
			}

			// for Single Page/Post Stats.
			if ( $page == 'analytify_page_analytify-settings' || $page == 'post.php' || $page == 'post-new.php' ) {
				wp_enqueue_script( 'chosen-js', plugins_url( 'assets/old/js/chosen.jquery.min.js', __FILE__ ), false, '1.8.7' );
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
				'single_post_stats'								 => wp_create_nonce( 'analytify-get-single-stats' )
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
		 * Remove submenu pages.
		 * 
		 */
		function remove_submenu_pages( $submenu ) {

			// Remove promo submenu page
			remove_submenu_page( 'analytify-dashboard', 'analytify-promo' );
		
			return $submenu;
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
		 * Add scripts for front end.
		 *
		 */
		function front_scripts() {

			global $post;

			$enabled_post_types = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_post_types_front_end','wp-analytify-front', array() );

			// Check tracking allowed for current post type.
			if ( ! empty( $enabled_post_types ) && ! in_array( $post->post_type, $enabled_post_types ) ) {
				return;
			}

			// Check of all requirements are filled for tracking.
			$is_tracking_available = WPANALYTIFY_Utils::is_tracking_available();

			// Scroll depth tracking script.
			if ( $is_tracking_available && 'on' == $this->settings->get_option( 'depth_percentage', 'wp-analytify-advanced' ) ) {
				// Remove protocol form permalink.
				$permalink = get_the_permalink( $post->ID );
				$permalink = str_replace( array( 'http://', 'https://' ), '', $permalink );
				$localize_data = array(
					'permalink'		=> $permalink,
					'tracking_mode'	=> ANALYTIFY_TRACKING_MODE
				);
				
				wp_enqueue_script( 'scrolldepth-js', 	plugins_url( 'assets/default/js/scrolldepth.js', __FILE__ ), array('jquery'), ANALYTIFY_VERSION, true );
				wp_localize_script( 'scrolldepth-js', 'analytifyScroll', $localize_data );
			}
		}

		/**
		 * Create Analytics menu at the left side of dashboard
		 */
		public function add_admin_menu() {

			$allowed_roles = $this->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard',  array() );
			$allowed_roles[] = 'administrator';

 			// if dont have Analytify Dashboard access, Return.
			if ( ! $this->pa_check_roles( $allowed_roles ) ) {
				return;
			}

			add_submenu_page( null, __( 'Activate', 'wp-analytify' ), __( 'Activate', 'wp-analytify' ), 'manage_options', 'analytify-optin', array( $this, 'render_optin' )  );

			add_menu_page( ANALYTIFY_NICK, 'Analytify', 'read', 'analytify-dashboard', array(
				$this,
				'pa_page_file_path',
			), plugins_url( 'assets/images/wp-analytics-logo.png', __FILE__ ),'2.1.9');

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'Dashboards', 'wp-analytify' ), esc_html__( 'Dashboards', 'wp-analytify' ), 'read', 'analytify-dashboard', array(
				$this,
				'pa_page_file_path',
			), 10 );

			// // Fallback menus for addons.
			// $wp_analytify_modules = get_option( 'wp_analytify_modules' );
			
			// if ( $wp_analytify_modules ) {
			// 	foreach ( $wp_analytify_modules as $module ) {
			// 		if ( 'active' !== $module['status'] ) {
			// 			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'Settings', 'wp-analytify' ), esc_html__( 'Settings', 'wp-analytify' ), 'manage_options', 'analytify-settings', array(
			// 				$this,
			// 				'pa_page_file_path',
			// 			));
			// 		}
			// 	}
			// }
			
			do_action( 'analytify_add_submenu' );
			//do_action( 'analyitfy_email_setting_submenu' );

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'Settings', 'wp-analytify' ), esc_html__( 'Settings', 'wp-analytify' ), 'manage_options', 'analytify-settings', array(
				$this,
				'pa_page_file_path',
			), 50 );

			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'Help', 'wp-analytify' ), esc_html__( 'Help', 'wp-analytify' ), 'read', 'analytify-settings#wp-analytify-help', array(
				$this,
				'pa_page_file_path',
			), 55 );

			// Add license submenu
			if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
				add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'License', 'wp-analytify' ), esc_html__( 'License', 'wp-analytify' ), 'read', 'analytify-settings#wp-analytify-license', array(
					$this,
					'pa_page_file_path',
				), 60 );
			}
			
			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( ' list of all Add-ons', 'wp-analytify' ), esc_html__( 'Add-ons', 'wp-analytify' ), 'manage_options', 'analytify-addons', array(
				$this,
				'pa_page_file_path',
			), 65 );
			
			add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . esc_html__( 'PRO vs FREE', 'wp-analytify' ), esc_html__( 'PRO vs FREE', 'wp-analytify' ), 'manage_options', 'analytify-go-pro',  array(
				$this,
				'pa_page_file_path',
			), 70 );

			// Promo page (will not appear in side menu).
			add_submenu_page( 'analytify-dashboard', esc_html__( 'Analytify Promo', 'wp-analytify' ), esc_html__( 'Analytify Promo', 'wp-analytify' ), 'manage_options', 'analytify-promo', array( $this, 'addons_promo_screen' ) );
		}

		/**
		 * Fallback addons page if plugin is deactive.
		 */
		function modules_fallback_page() {
	
			$wp_analytify_modules = get_option( 'wp_analytify_modules' );
		
			if ( $wp_analytify_modules && $_SERVER ) {
				foreach ( $wp_analytify_modules as $module ) {
					if ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] === 'page='.$module['page_slug'] && 'active' !== $module['status'] ) {
						wp_redirect( admin_url( '/admin.php?page=analytify-promo&addon='. $module['slug'] ) );
						exit;
					}
				}
			}
		}

		/**
		 * Show promo screen for addons.
		 * 
		 */
		function addons_promo_screen(  ) {
			include ANALYTIFY_ROOT_PATH . '/views/default/admin/addons-promo.php';
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

				if ( $GLOBALS['WP_ANALYTIFY']->get_exception() ) {
					WPANALYTIFY_Utils::handle_exceptions( $GLOBALS['WP_ANALYTIFY']->get_exception() );
				} else if ( get_option( 'pa_google_token' ) != '' ) {
					$profiles = $this->service->management_accountSummaries->listManagementAccountSummaries();
					return $profiles;
				} else {
					echo '<br /><div class="notice notice-warning"><p>' . esc_html__( 'Notice: You must authenticate to access your web profiles.', 'wp-analytify' ) . '</p></div>';
				}


			} catch (Exception $e) {

				$logger = analytify_get_logger();
        		$logger->warning( $e->getMessage(), array( 'source' => 'analytify_profile_summary' ) );

				if ( is_callable( array( $e, 'getErrors' ) ) ) {
					$error = $e->getErrors();
				} else {
					$error = array( [
						'reason' => 'unexpected_profile_error'
					] );
				}

				WPANALYTIFY_Utils::handle_exceptions( $error );
				$GLOBALS['WP_ANALYTIFY']->set_exception( $error );
				update_option( 'analytify_profile_exception', $error );
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

			check_ajax_referer( 'analytify-get-single-stats', 'nonce' );

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
		 * Set modules state.
		 * 
		 */
		function set_module_state() {
			$analytify_modules = get_option( 'wp_analytify_modules' );
			$module_slug = sanitize_text_field( $_POST['module_slug'] );
			$set_state = sanitize_text_field( $_POST['set_state'] );
			$internal_module = sanitize_text_field( $_POST['internal_module'] );
			$plugins_dir = ABSPATH . 'wp-content/plugins/';
			$return = 'success';

			if( !wp_verify_nonce($_POST['nonce'], 'addons') ){ echo 'failed'; wp_die(); }
	
			if ( 'true' === $internal_module ) {
	
				if ( 'active' === $set_state ) {
					$analytify_modules[$module_slug]['status'] = 'active';
				} else {
					$analytify_modules[$module_slug]['status'] = false;
				}
	
				update_option( 'wp_analytify_modules', $analytify_modules );
	
			} else {
				
				if ( 'active' === $set_state ) {
					$plugin_change_state = activate_plugin( $plugins_dir . $module_slug );
				} else {
					$plugin_change_state = deactivate_plugins( $plugins_dir . $module_slug );
				}
	
				// Error in response.
				if ( ! empty( $plugin_change_state ) ) {
					$return = 'failed';
				}
			}
	
			echo $return;
			wp_die();
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
				// decode the url for the filtration.
				$link = apply_filters( 'analytify_sinlge_stats_permalink', get_permalink( $post_id ), $post_id );
				$u_post = parse_url( urldecode( $link ) );
			}

			if ( 'localhost' == $u_post['host'] ) {
				$filter = 'ga:pagePath==/'; // .$u_post['path'];
			} else {
				$filter = 'ga:pagePath==' . $u_post['path'] . '';
				// change the page poth filter for site that use domain mapping.
				$filter = apply_filters( 'analytify_page_path_filter', $filter, $u_post );

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
			$show_settings = $this->settings->get_option( 'show_panels_back_end','wp-analytify-admin', array( 'show-overall-dashboard' ) );

			// Stop here, if user has disable backend analytics i.e OFF.
			if ( 'on' === $this->settings->get_option( 'disable_back_end','wp-analytify-admin' ) and 0 === $ajax ) {
				return;
			}

			echo sprintf( esc_html__( '%1$s Displaying Analytics of this page from %2$s to %3$s %4$s', 'wp-analytify' ), '<p>', date( 'jS F, Y', strtotime( $s_date ) ), date( 'jS F, Y', strtotime( $e_date ) ), '</p>') ;
			echo '<div class="analytify_wraper analytify_single_post_page">';

			if ( is_array( $show_settings ) ) {

				if ( in_array( 'show-overall-dashboard', $show_settings ) ) {

					// set demension if amp addon is installed.
					$_general_stats_filter = defined( 'ANALYTIFY_AMP_VERSION' ) ? 'ga:pagePath' : false;
					$stats = $this->pa_get_analytics( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:percentNewSessions,ga:newUsers,ga:avgTimeOnPage',$s_date, $e_date, $_general_stats_filter, false, $filter, false, 'analytify-single-general-stats' );

					if ( isset( $stats->totalsForAllResults ) ) {

						include_once ANALYTIFY_ROOT_PATH . '/views/default/admin/single-general-stats.php';
						wpa_include_single_general( $this, $stats );
					}
        		}

				// Display page depth stats.
				if ( in_array( 'show-scroll-depth-stats', $show_settings ) && 'on' === $this->settings->get_option( 'depth_percentage', 'wp-analytify-advanced' ) ) {

					// Remove protocol form permalink.
					$permalink = get_the_permalink( $post_id );
					$permalink = str_replace( array( 'http://', 'https://' ), '', $permalink );

					$title_filter = 'ga:eventLabel=='.$permalink;
					$depth_stats = $this->pa_get_analytics( 'ga:totalEvents,ga:eventValue',$s_date, $e_date, 'ga:eventCategory,ga:eventAction,ga:eventLabel', false, $title_filter, false, 'analytify-single-general-scrolldepth' );

					if ( isset( $depth_stats->totalsForAllResults ) ) {
						include_once ANALYTIFY_ROOT_PATH . '/views/default/admin/single-depth-stats.php';
						wpa_include_single_depth( $this, $depth_stats );
					}
				}
			}

			do_action( 'wp_analytify_stats_under_post' , $show_settings ,$s_date, $e_date , $filter );

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

				$attach_hours = '';
				$attach_min = '';
				$attach_sec = '';
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
						return $value['hours'] . $attach_hours . $value['minutes'] . $attach_min . $value['seconds'] . $attach_sec;

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

			if ( get_option( 'analytify_profile_exception' ) ) {
				return;
			}

			$profile_id     = get_option( 'pt_webprofile' );
			$acces_token    = get_option( 'post_analytics_token' );
			$manual_ua_code = $this->settings->get_option( 'manual_ua_code', 'wp-analytify-authentication', false );

			if ( current_user_can( 'install_plugins' ) ) {

				//global $current_user ;
				//$user_id = $current_user->ID;
				/* Check that the user hasn't already clicked to ignore the message */
				// if ( ! get_user_meta($user_id, 'analytify_2_1_22_ignore') ) {
				// echo '<div class="updated"><p>';
				// printf(__('Thanks for updating <strong>Analytify</strong>! <a href="https://analytify.io/go/analytify-review" target="_blank" rel="noopener">Read</a> how thousands of user loving Analytify and sharing their story! <a href="https://analytify.io/go/analytify-review" target="_blank" rel="noopener"><strong>Click here</strong></a>.
				// 	<a href="%1$s">[Hide Notice]</a>'), add_query_arg( array( 'analytify_2_1_22_ignore' => '0' ) )  );
    			//	echo "</p></div>";
				//  }
			}

			/* Show notices */
			if ( empty( $manual_ua_code ) && ( ! isset( $acces_token ) || empty( $acces_token ) || ! get_option( 'pa_google_token' ) ) ) {
				$dashboard_pages = [
					'toplevel_page_analytify-dashboard',
					'analytify_page_analytify-goals',
					'analytify_page_analytify-woocommerce',
					'analytify_page_analytify-authors',
					'edd-dashboard',
					'analytify_page_analytify-dimensions',
					'analytify_page_analytify-campaigns',
					'analytify_page_analytify-addons',
				];
				$current_screen = get_current_screen()->base;
				
				// Prevent doubling of notices on analytify dashboard pages.
				if ( ! in_array( $current_screen, $dashboard_pages ) ) {
					$class   = 'wp-analytify-danger';
					$link    = esc_url( menu_page_url( 'analytify-settings', false ) );
					$message = sprintf( esc_html__( '%1$sNotice:%2$s %3$sConnect%4$s %2$s Analytify with your Google account.', 'wp-analytify' ), '<b>', '</b>', '<b><a style="text-decoration:none" href=' . $link . '>', '</a>');
					analytify_notice( $message, $class );
				}
		
				// echo sprintf( esc_html__( '%1$s %2$s %3$sNotice:%4$s %5$sConnect%6$s %4$s Analytify with your Google account. %7$s %8$s', 'wp-analytify' ), '<div class="error notice is-dismissible">', '<p>', '<b>', '</b>', '<b><a style="text-decoration:none" href=' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '>', '</a>','</p>', '</div>' );

			} else {

				if ( empty( $manual_ua_code ) && ! WP_ANALYTIFY_FUNCTIONS::is_profile_selected() ) {

					// echo '<div class="error notice is-dismissible"><p>' . sprintf( esc_html__( 'Congratulations! Analytify is now authenticated. Please select your website profile %1$s here %2$s to get started.', 'wp-analytify' ), '<a style="text-decoration:none" href="' . esc_url( menu_page_url( 'analytify-settings', false ) ) . '#wp-analytify-profile">','</a>' ) . '</p></div>';

					$class   = 'wp-analytify-success';
					$link    = esc_url( menu_page_url( 'analytify-settings', false ) );
					$message = sprintf( esc_html__( 'Congratulations! Analytify is now authenticated. Please select your website profile %1$s here %2$s to get started.', 'wp-analytify' ), '<a style="text-decoration:none" href="' . $link . '#wp-analytify-profile">','</a>' );
					analytify_notice( $message, $class );
				}
			}

			if ( defined( 'ANALYTIFY_TRACKING_MODE' ) && 'gtag' === ANALYTIFY_TRACKING_MODE ) {
				$update_gtag_plugins = array();

				if ( class_exists( 'WP_Analytify_Woocommerce' ) && 1 === version_compare( '4.1.0', ANALTYIFY_WOOCOMMERCE_VERSION ) ) {
					array_push( $update_gtag_plugins, 'Analytify - WooCommerce Tracking' );
				}
				if ( class_exists( 'Analytify_Forms' ) && 1 === version_compare( '2.0.0', ANALYTIFY_FORMS_VERSION ) ) {
					array_push( $update_gtag_plugins, 'Analytify - Forms Tracking' );
				}
				if ( class_exists( 'WP_Analytify_Pro_Base' ) && 1 === version_compare( '4.1.0', ANALYTIFY_PRO_VERSION ) ) {
					array_push( $update_gtag_plugins, 'Analytify Pro' );
				}
	
				if ( ! empty( $update_gtag_plugins ) ) {
					$class   = 'wp-analytify-danger';
					$message = sprintf( esc_html__( '%1$sNotice:%2$s Please update the following plugins to make them work with the Analytify gtag.js tracking mode. %3$s', 'wp-analytify' ), '<b>', '</b>', '<br>' . implode( ', ', $update_gtag_plugins ) );
					analytify_notice( $message, $class );
				}
			}
			
			// #TODO Add promo UI
			// if ( class_exists( 'WooCommerce' ) && ! class_exists( 'WP_Analytify_Pro_Base' ) ) {
			// 	$class   = 'wp-analytify-success';
			// 	$link    = esc_url('https://analytify.io/add-ons/woocommerce/' );
			// 	$message = sprintf( esc_html__( '%1$s Important Notice %2$s &mdash; Analytify %3$sEnhanced E-Commerce Tracking for WooCommerce%4$s can help you track your ecommerce stats.', 'wp-analytify' ), '<b>', '</b>', '<a style="text-decoration:none" href="' . $link . '" target="_blank">','</a>' );
			// 	analytify_notice( $message, $class );
			// }
			
			if ( 'visible' === get_option( 'analytify_gtag_move_to_notice' ) && 'gtag' !== ANALYTIFY_TRACKING_MODE ) {
				$this->gtag_move_to_notice();
			}
		}

		/**
		 * Notice to switch gtag.js tracking mode.
		 *
		 * @return void
		 */
		function gtag_move_to_notice() {

			$scheme      = (parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY )) ? '&' : '?';
			$url         = $_SERVER['REQUEST_URI'] . $scheme . 'wp_analytify_gtag_dismiss=yes';
			$dismiss_url = wp_nonce_url( $url, 'analytify-gtag-nonce' ); ?>

			<div class="wp-analytify-notification wp-analytify-danger">
				<a class="" href="#" aria-label="Dismiss the welcome panel"></a>
				<div class="wp-analytify-notice-logo">
					<img src="<?php echo plugins_url( 'assets/images/notice-logo.svg', __FILE__ ) ?>" alt="">
				</div>
				<div class="wp-analytify-notice-discription">
					<p><?php _e( 'Analytify introduced the new gtag.js tracking mode. Switch it now from plugin Advanced settings to use recommended Google Analytics gtag.js tracking method. <br />', 'wp-analytify' ); ?><br /></p>
					<ul class="analytify-review-ul" style="padding-top:10px">
						
						<li><a href="<?php echo $dismiss_url; ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'I have moved to gtag.js', 'wp-analytify' ) ?></a></li>
					</ul>
				</div>
			</div>

			<?php
		}

		function dismiss_notices() {

			if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! isset( $_GET['_wpnonce'] ) ) {
				return;
			}

			// Gtag dismiss notice.
			if ( wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'analytify-gtag-nonce' ) && isset( $_GET['wp_analytify_gtag_dismiss'] ) && 'yes' === $_GET['wp_analytify_gtag_dismiss'] ) {
				delete_option( 'analytify_gtag_move_to_notice' );
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
				add_user_meta( $user_id, 'analytify_2_1_22_ignore', 'true', true );
			}

			/* If user clicks to ignore the 2.1.5 notice, add that to their user meta */
			if ( isset( $_GET['analytify_2_1_22_ignore'] ) && '0' === $_GET['analytify_2_1_22_ignore'] ) { // Input var okay.
				add_user_meta( $user_id, 'analytify_2_1_22_ignore', 'true', true );
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

			add_site_option( 'wp_analytify_review_dismiss_4_1_0', 'yes' );
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
			$review_dismissal	= get_site_option( 'wp_analytify_review_dismiss_4_1_0' );

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
					<h3><?php _e( 'How\'s Analytify going, impressed?', 'wp-analytify' ) ?></h3>
					<p><?php _e( 'We hope you\'ve enjoyed using Analytify! Would you consider leaving us a 5-star review on WordPress.org?', 'wp-analytify' ) ?></p>
					<ul class="analytify-review-ul"><li><a href="https://wordpress.org/support/view/plugin-reviews/wp-analytify?rate=5#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'wp-analytify' ) ?></a></li>
             <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a 5-star review', 'wp-analytify' ) ?></a></li>
             <li><a href="<?php echo $later_url ?>"><span class="dashicons dashicons-calendar-alt"></span><?php _e( 'Maybe Later', 'wp-analytify' ) ?></a></li>
             <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'wp-analytify' ) ?></a></li></ul>
				</div>
			</div>
			<?php
		}


		/**
		 * Show Buy Pro Notice after 7 days of activation.
		 *
		 * @since 2.1.23
		 */
		function analytify_buy_pro_notice() {

			$this->buy_pro_notice_dismissal();

			$activation_time 	= get_site_option( 'wp_analytify_buy_pro_active_time' );
			$review_dismissal	= get_site_option( 'wp_analytify_buy_pro_notice' );

			if ( 'yes' == $review_dismissal ) {
				return;
			}


			if ( ! $activation_time ) {

				$activation_time = time();
				add_site_option( 'wp_analytify_buy_pro_active_time', $activation_time );
			}

			// 604800 = 7 Days in seconds.
			if ( time() - $activation_time > 604800 ) {
				add_action( 'admin_notices' , array( $this, 'analytify_buy_pro_message' ) );
			}

		}

		/**
		 * Dismiss Buy Pro Notice.
		 *
		 * @since 2.1.23
		 */
		function buy_pro_notice_dismissal() {

			if ( ! is_admin() ||
			! current_user_can( 'manage_options' ) ||
			! isset( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'wp_analytify_buy_pro_notice' ) ||
			! isset( $_GET['wp_analytify_buy_pro_notice_dismiss'] ) ) {

				return;
			}

			add_site_option( 'wp_analytify_buy_pro_notice', 'yes' );

		}

		/**
		 * Show Buy Pro Notice.
		 *
		 * @since 2.1.23
		 */
		function analytify_buy_pro_message() {

			$scheme      = (parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY )) ? '&' : '?';
			$url         = $_SERVER['REQUEST_URI'] . $scheme . 'wp_analytify_buy_pro_notice_dismiss=yes';
			$dismiss_url = wp_nonce_url( $url, 'wp_analytify_buy_pro_notice' );

			$class   = 'wp-analytify-success';

			$message =	sprintf( 'Analytify now powering %1$s30,000+%2$s websites. Use the coupon code %1$sGOPRO10%2$s to redeem a %1$s10%% %2$s discount on Pro. %3$sApply Coupon%4$s %5$s I\'m good with free.%6$s', '<strong>', '</strong>', '<a href="https://analytify.io/pricing/?discount=gopro10" target="_blank" class="wp-analytify-notice-link"><span class="dashicons dashicons-smiley"></span> ', '</a>', '<a href="'. $dismiss_url .'" class="wp-analytify-notice-link"><span class="dashicons dashicons-dismiss"></span>', '</a>' );
			analytify_notice( $message, $class );

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

			$is_tracking_available = WPANALYTIFY_Utils::is_tracking_available();
			
			if ( ! $is_tracking_available ) {
				return;
			}

			// 404 tracking hits in Google analytics
			if ( 'on' == $this->settings->get_option( '404_page_track', 'wp-analytify-advanced' ) ) {
				if ( is_404() ) {
					$current_url = home_url( add_query_arg( null, null ) );
					echo '<script>
								if (typeof gtag !== "undefined") {
									gtag("event", "Page Not Found", {
										"event_category" : "404 Error",
										"event_label" : "' . $current_url . '"
									});
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
						 gtag('event', 'errMsg', {
							'event_category' : 'JavaScript Error',
							'event_label' : 'errSrc',
							'non_interaction': true
						});
                     }
					if (typeof gtag !== 'undefined') {
					   window.addEventListener('error', trackJavaScriptError, false);
					}
				</script>";
			}

			//AJAX tracking hits in Google analytics
			if ( 'on' == $this->settings->get_option( 'ajax_error_track', 'wp-analytify-advanced' )  ) {
				echo "<script>
						if (typeof gtag !== 'undefined') {
							jQuery(document).ajaxError(function (e, request, settings) {
								gtag('event', 'request.statusText', {
									'event_category' : 'Ajax Error',
									'event_label' : 'settings.url',
									'non_interaction': true
								});
							});
						}
					</script>" ;
			}

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

		/**
		 * Trigger logging cleanup using the logging class.
		 *
		 * @since 2.1.23
		 */

		function analytify_cleanup_logs() {
			$logger = analytify_get_logger();

			if ( is_callable( array( $logger, 'clear_expired_logs' ) ) ) {
				$logger->clear_expired_logs();
			}
		}

		/**
		 * Update profiles_list_summary option when hide profile is set.
		 * @param  array $old_value
		 * @param  array $new_value
		 *
		 * @since 2.1.4
		 */
		function update_profiles_list_summary( $old_value, $new_value ) {

			if ( isset( $new_value['hide_profiles_list'] ) && $new_value['hide_profiles_list'] == 'on' && ( $new_value['hide_profiles_list'] != $old_value['hide_profiles_list']  ) && isset( $new_value['profile_for_dashboard'] ) ) {

				$accounts = get_option( 'profiles_list_summary' );
				update_option( 'profiles_list_summary_backup', $accounts, 'no' );

				$new_properties = array();
				foreach ( $accounts->getItems() as $account ) {
					foreach ( $account->getWebProperties() as  $property ) {
						foreach ( $property->getProfiles() as $profile ) {
							// Get Property ID i.e UA Code
							if ( $profile->getId() === $new_value['profile_for_dashboard'] ) {
								$new_properties[$account->getId()] = $property;
							}
							if ( $profile->getId() === $new_value['profile_for_posts'] ) {
								$new_properties[$account->getId()] = $property;
							}
						}
					}

				}
				update_option( 'profiles_list_summary', $new_properties );
			}
		}


		/**
		 * Remove the unnecessary data from profile summary list.
		 *
		 * @since 2.2.5
		 */
		function update_profile_list_summary_on_update() {
			if ( version_compare( ANALYTIFY_VERSION ,  get_option( 'WP_ANALYTIFY_PLUGIN_VERSION' ) , '>' ) ) {
				$option = get_option( 'wp-analytify-profile' );
				if ( isset( $option['hide_profiles_list'] ) && $option['hide_profiles_list'] == 'on' ) {
					$accounts = get_option( 'profiles_list_summary' );

					if ( ! $accounts ) {
						return;
					}
					// Means that its run already.
					if ( is_array( $accounts ) ) {
						return;
					}
			 		update_option( 'profiles_list_summary_backup', $accounts, 'no' );

					$new_value['profile_for_dashboard'] = $option['profile_for_dashboard'];
					$new_value['profile_for_posts'] = $option['profile_for_posts'];

					$new_properties = array();
					foreach ( $accounts->getItems() as $account ) {
						foreach ( $account->getWebProperties() as  $property ) {
							foreach ( $property->getProfiles() as $profile ) {
								// Get Property ID i.e UA Code
								if ( $profile->getId() === $new_value['profile_for_dashboard'] ) {
									$new_properties[$account->getId()] = $property;
								}
								if ( $profile->getId() === $new_value['profile_for_posts'] ) {
									$new_properties[$account->getId()] = $property;
								}
							}
						}
					}
					update_option( 'profiles_list_summary', $new_properties );
				}
			}
		}


		/**
		 * Show Black Friday Deal Notice.
		 *
		 */
		function bf_admin_notice() {
			if ( current_user_can( 'install_plugins' ) && ! class_exists( 'WP_Analytify_Pro' ) ) {
				global $current_user ;
				$user_id = $current_user->ID;
				/* Check that the user hasn't already clicked to ignore the message */
				if ( ! get_user_meta( $user_id, 'analytify_ignore_bf_deal_1' ) ) {
					$message = '<p> ';
					$message .= sprintf (__( '<strong>Biggest Black Friday Deal</strong> in the WordPress Analytics Universe! Everything is <strong>50%% OFF</strong> for <strong>Analytify</strong> [Limited Availability].<a href="https://analytify.io/in/thanks2018" target="_blank" style="text-decoration: none;"><span class="dashicons dashicons-smiley" style="margin-left: 10px;"></span> Grab The Deal</a>
						<a href="%1$s" style="text-decoration: none; margin-left: 10px;"><span class="dashicons dashicons-dismiss"></span> I\'m good with free version</a>' ),  admin_url( 'admin.php?page=analytify-dashboard&analytify_bf_nag_ignore_1=0' ) );
					$message .=  "</p>";
					$class = 'wp-analytify-success';
					analytify_notice( $message, $class );
				}
			}
		}

		/**
		 * Remove Black Friday Deal Notice.
		 *
		 */
		function bf_nag_ignore() {
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset( $_GET['analytify_bf_nag_ignore_1'] ) && '0' == $_GET['analytify_bf_nag_ignore_1'] ) {
				add_user_meta( $user_id, 'analytify_ignore_bf_deal_1', 'true', true );
			}
		}

		/**
		 * Show Winter Sale promo notice.
		 *
		 */
		function winter_sale_promo() {
			if ( current_user_can( 'install_plugins' ) && ! class_exists( 'WP_Analytify_Pro' ) ) {
				global $current_user ;
				$user_id = $current_user->ID;
				/* Check that the user hasn't already clicked to ignore the message */
				if ( ! get_user_meta( $user_id, 'analytify_ignore_winter_deal' ) ) {
					$message = '<p> ';
					$message .= sprintf (__( '<strong>The Biggest New Year Deal</strong> in the WordPress Universe! Everything is <strong>50%% OFF</strong> for <strong>Analytify</strong> [Limited Availability].<a href="https://analytify.io/in/winter2019" target="_blank" style="text-decoration: none;"><span class="dashicons dashicons-smiley" style="margin-left: 10px;"></span> Grab The Deal</a>
						<a href="%1$s" style="text-decoration: none; margin-left: 10px;"><span class="dashicons dashicons-dismiss"></span> I\'m good with free version</a>' ),  admin_url( 'admin.php?page=analytify-dashboard&analytify_winter_nag_ignore=0' ) );
					$message .=  "</p>";
					$class = 'wp-analytify-success';
					analytify_notice( $message, $class );
				}
			}
		}

		/**
		 * Dismiss Winter Sale promo notice.
		 *
		 */
		function winter_sale_dismiss_notice() {
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset( $_GET['analytify_winter_nag_ignore'] ) && '0' == $_GET['analytify_winter_nag_ignore'] ) {
				add_user_meta( $user_id, 'analytify_ignore_winter_deal', 'true', true );
			}
		}

		/**
		 * Add rating icon on plugins page.
		 *
		 * @since 2.2.11
		 */
		function add_rating_icon( $meta_fields, $file ) {

			if ( $file != 'wp-analytify/wp-analytify.php' ) { return $meta_fields; }

			echo "<style>.analytify-rate-stars { display: inline-block; color: #ffb900; position: relative; top: 3px; }.analytify-rate-stars svg{ fill:#ffb900; } .analytify-rate-stars svg:hover{ fill:#ffb900 } .analytify-rate-stars svg:hover ~ svg{ fill:none; } </style>";
			$plugin_url = "https://wordpress.org/support/plugin/wp-analytify/reviews/?rate=5#new-post";
			$meta_fields[] = "<a href='" . esc_url( $plugin_url ) ."' target='_blank' title='" . esc_html__('Rate', 'wp-analytify') . "'>
			<i class='analytify-rate-stars'>"
			. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
			. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
			. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
			. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
			. "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
			. "</i></a>";

			return $meta_fields;

		}

		/**
		 * Init the compliance class.
		 *
		 * @return void
		 */
		function init_gdpr_compliance() {
			new Analytify_GDPR_Compliance();
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

	// update version.
	if ( ! get_option( 'pa_google_token' ) ) {
		update_option( 'wpa_current_version', '2.1.2' );
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

		update_option( 'analytify_active_date', date( 'l jS F Y h:i:s A' ) . date_default_timezone_get() );
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
	delete_option( 'wp-analytify-authentication' );
	delete_option( 'wp-analytify-advanced' );
	delete_option( 'wp-analytify-help' );
	delete_option( 'analytify_ua_code' );
	delete_option( 'analytify_date_differ' );
	delete_option( 'profiles_list_summary' );
	delete_option( 'pa_google_token' );
	delete_option( 'post_analytics_token' );
	delete_option( 'WP_ANALYTIFY_NEW_LOGIN' );
	delete_option( '_analytify_optin' );
	delete_option( 'wp_analytify_active_time' );
	delete_option( 'wp_analytify_buy_pro_active_time' );
	delete_option( 'WP_ANALYTIFY_PLUGIN_VERSION' );
	delete_option( 'analytify_free_upgrade_routine' );
	delete_option( 'wp-analytify-dashboard' );
	delete_option( 'wp-analytify-profile' );

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