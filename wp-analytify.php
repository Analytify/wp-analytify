<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: Analytify Dashboard
 * Plugin URI: https://analytify.io/?ref=27&utm_source=wp-org&utm_medium=plugin-header&utm_campaign=pro-upgrade&utm_content=plugin-uri
 * Description: Analytify brings a brand new and modern feeling of Google Analytics superbly integrated within the WordPress.
 * Version: 9.1.2
 * Author: Analytify
 * Author URI: https://analytify.io/?ref=27&utm_source=wp-org&utm_medium=plugin-header&utm_campaign=pro-upgrade&utm_content=author-uri
 * License: GPLv3
 * Text Domain: wp-analytify
 * Requires at least: 4.0
 * Tested up to: 7.0
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WPBrigade/wp-analytify
 *
 * @package WP_ANALYTIFY
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wa_wpb78834179' ) ) {
	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return mixed
	 */
	function wa_wpb78834179() {
		global $wa_wpb78834179;

		if ( ! isset( $wa_wpb78834179 ) || ! is_array( $wa_wpb78834179 ) ) {
			require_once __DIR__ . '/lib/wpb-sdk/start.php';

			/**
			 * Initialize WPB SDK.
			 *
			 * @phpstan-ignore-next-line
			 */
			$wa_wpb78834179 = wpb_sdk_dynamic_init(
				array(
					'id'              => '7',
					'slug'            => 'wp-analytify',
					'type'            => 'plugin',
					'plugin_file'     => __FILE__,
					'sdk_views_dir'   => __DIR__ . '/lib/wpb-sdk/views',
					'public_key'      => '1|4aOA8EuyIN4pi2miMvC23LLpnHbBZFNki9R9pVmwd673d3c8',
					'secret_key'      => 'sk_b36c525848fee035',
					'is_premium'      => false,
					'has_addons'      => false,
					'has_paid_plans'  => false,
					'optin_user_meta' => array(
						'token'          => '_analytify_verification_token',
						'email_verified' => '_analytify_email_verified',
					),
					'lifecycle'       => array(
						'class'              => 'Analytify_Plugin_Lifecycle',
						'bootstrap_files'    => array(
							'inc/analytify-constants.php',
							'inc/plugin-lifecycle.php',
						),
						'uninstall_requires' => 'classes/analytify-utils.php',
						'constructor_args'   => array( null ),
					),
					'telemetry'       => array(
						'user_info_format'  => 'laravel',
						'optout_submit_key' => 'analytify-submit-optout',
					),
					'optin'           => array(
						'option_name'      => '_analytify_optin',
						'verify_query_arg' => 'wp-analytify_optin_verify',
						'settings_page'    => 'analytify-settings',
						'optin_page'       => 'analytify-optin',
						'use_site_option'  => true,
						'guard_pages'      => array(
							'analytify-settings',
							'analytify-dashboard',
							'analytify-pmpro',
							'analytify-learndash',
							'analytify-lifterlms',
							'analytify-woocommerce',
							'analytify-addons',
						),
					),
					'menu'            => array(
						'slug'    => 'wp-analytify',
						'account' => false,
						'support' => false,
					),
					'settings'        => array(
						'wp_analytify_modules'             => '',
						'wp-analytify-tracking'            => '',
						'wp-analytify-email'               => '',
						'wp-analytify-events-tracking'     => '',
						'wp-analytify-front'               => '',
						'wp-analytify-custom-dimensions'   => '',
						'wp-analytify-forms'               => '',
						'analytify_widget_date_differ'     => '',
						'wp-analytify-profile'             => '',
						'wp-analytify-admin'               => '',
						'wp-analytify-dashboard'           => '',
						'wp-analytify-advanced'            => '',
						'analytify_ua_code'                => '',
						'analytify_date_differ'            => '',
						'wp_analytify_review_dismiss'      => '',
						'analytify_date_start'             => '',
						'analytify_date_end'               => '',
						'wp_analytify_review_dismiss_4_1_8' => '',
						'wpanalytify_settings'             => '',
						'analytify_license_key'            => '',
						'analytify_license_status'         => '',
						'analytify_campaigns_license_status' => '',
						'analytify_campaigns_license_key'  => '',
						'analytify_goals_license_status'   => '',
						'analytify_goals_license_key'      => '',
						'Analytify_Addon_Forms_license_status' => '',
						'analytify_forms_license_key'      => '',
						'analytify_authors_license_status' => '',
						'analytify_authors_license_key'    => '',
						'analytify_woo_license_status'     => '',
						'analytify_woo_license_key'        => '',
						'analytify_email_license_status'   => '',
						'analytify_email_license_key'      => '',
						'analytify-google-ads-tracking'    => '',
						'_analytify_optin'                 => '',
						'analytify_cache_timeout'          => '',
						'analytify_csv_data'               => '',
						'analytify_active_date'            => '',
						'analytify_edd_license_status'     => '',
						'analytify_edd_license_key'        => '',
						'_transient_timeout_analytify_api_addons' => '',
						'_transient_analytify_api_addons'  => '',
						'analytify_ga4_exceptions'         => '',
						'analytify-ga-properties-summery'  => '',
						'analytify_ga4-streams'            => '',
						'analytify_tracking_property_info' => '',
						'analytify_reporting_property_info' => '',
						'analytify_gtag_move_to_notice'    => '',
						'analytify_current_version'        => '',
						'analytify_logs_setup'             => '',
						'analytify_pro_default_settings'   => '',
						'analytify_pro_active_date'        => '',
						'analytify_pro_upgrade_routine'    => '',
						'analytify_pro_current_version'    => '',
						'WP_ANALYTIFY_PRO_PLUGIN_VERSION'  => '',
						'wp-analytify-license'             => '',
						'analytify_authentication_date'    => '',
						'WP_ANALYTIFY_PLUGIN_VERSION_OLD'  => '',
						'WP_ANALYTIFY_PRO_PLUGIN_VERSION_OLD' => '',
						'analytify_default_settings'       => '',
						'analytify_free_upgrade_routine'   => '',
						'WP_ANALYTIFY_PLUGIN_VERSION'      => '',
						'wp_analytify_active_time'         => '',
						'wp-analytify-authentication'      => '',
						'wp-analytify-help'                => '',
						'WP_ANALYTIFY_NEW_LOGIN'           => '',
						'profiles_list_summary'            => '',
						'pa_google_token'                  => '',
						'post_analytics_token'             => '',
					),
				)
			);
		}

		return $wa_wpb78834179;
	}

	wa_wpb78834179();
	do_action( 'wa_wpb78834179_loaded' );
}

require_once __DIR__ . '/inc/analytify-constants.php';

if ( ! class_exists( 'Analytify_General' ) ) {
	require_once 'analytify-general.php';
}

if ( ! class_exists( 'WP_Analytify' ) ) {
	/**
	 * Main WP_Analytify class.
	 *
	 * @since       1.0.0
	 */
	// phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed,Squiz.Commenting.ClassComment.Missing
	class WP_Analytify extends Analytify_General {

		/**
		 * Plugin instance.
		 *
		 * @var         WP_Analytify $instance The one true WP_Analytify
		 * @since       1.2.2
		 */
		private static $instance = null;

		/**
		 * Token data.
		 *
		 * @var mixed
		 */
		public $token = false;
		/**
		 * Client instance.
		 *
		 * @var mixed
		 */
		public $client = null;

		/**
		 * Post stats disable flag.
		 *
		 * @var mixed
		 */
		protected $disable_post_stats;

		/**
		 * Settings data.
		 *
		 * @var mixed
		 */
		public $settings;

		/**
		 * Component loader instance
		 *
		 * @var Analytify_Loader
		 */
		private $loader;

		/**
		 * Measurement ID for gtag when consent-deferred (any CMP that opts in via filters).
		 *
		 * @var string|null
		 */
		private $consent_deferred_gtag_ua_code = null;

		/**
		 * Whether the wp_footer handler for consent-deferred gtag was registered.
		 *
		 * @var bool
		 */
		private $consent_deferred_gtag_hooked = false;

		/**
		 * Filter applied to deferred gtag markup ({@see 'analytify_ga_script'} or {@see 'analytify_gtag_script'}).
		 *
		 * @var string
		 */
		private $consent_deferred_gtag_filter = 'analytify_ga_script';

		/**
		 * When true, {@see self::output_gtag_code()} emits immediate async gtag only (for footer base64 payload).
		 *
		 * @var bool
		 */
		private $output_gtag_for_footer_payload = false;

		// ========================================.
		// CONSTRUCTOR & CORE METHODS START.
		// ========================================.

		/**
		 * Constructor.
		 *
		 * @since 1.2.2
		 */
		public function __construct() {
			parent::__construct();
			$this->setup_constants();
			$this->includes();
			$this->disable_post_stats = $this->settings->get_option( 'enable_back_end', 'wp-analytify-admin' );
			$this->hooks();
		}

		/**
		 * Get active instance.
		 *
		 * @access      public
		 * @since       1.2.2
		 * @return      object self::$instance The one true WP_Analytify
		 */
		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Analytify();
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

			$upload_dir = wp_upload_dir( null, false );
		}

		/**
		 * Define constant if not already set
		 *
		 * @since 1.2.4
		 * @param  string      $name  contanst name.
		 * @param  string|bool $value constant value.
		 * @return void
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
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
				default:
					return false;
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
			$files = array(
				WP_ANALYTIFY_LIB_PATH . 'logs/class-analytify-log-handler-interface.php',
				WP_ANALYTIFY_LIB_PATH . 'logs/class-analytify-logger-interface.php',
				WP_ANALYTIFY_LIB_PATH . 'logs/class-analytify-log-levels.php',
				WP_ANALYTIFY_LIB_PATH . 'logs/class-analytify-logger.php',
				WP_ANALYTIFY_LIB_PATH . 'logs/abstract-analytify-log-handler.php',
				WP_ANALYTIFY_LIB_PATH . 'logs/class-analytify-log-handler-file.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-logs.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-sanitize.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytify-core-functions.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/inc/class-analytify-adminbar.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/abstracts/analytify-report-abstract.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/abstracts/analytify-host-analytics-abstract.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-host-analytics.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-report-core.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-rest-api.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/inc/class-analytify-ajax.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/inc/class-analytify-post-columns.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/class-wp-analytify-compatibility-upgrade.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-dashboard-widget.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/class-analytify-user-optout.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/class-analytify-gdpr-compliance.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-email.php',
				WP_ANALYTIFY_PLUGIN_DIR . '/classes/analytify-settings.php',
			);

			foreach ( $files as $file ) {
				if ( file_exists( $file ) ) {
					include_once $file;
				} else {

					echo '<div class="notice notice-error"><p>' . esc_html__( 'A critical file is missing:', 'wp-analytify' ) . ' ' . esc_html( $file ) . '. ' . esc_html__( 'The Analytify plugin needs to be deactivated && re-installed.', 'wp-analytify' ) . '</p></div>';
					return;
				}
			}

			if ( file_exists( WP_ANALYTIFY_PLUGIN_DIR . '/inc/class-analytify-loader.php' ) ) {
				require_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/class-analytify-loader.php';
				if ( class_exists( 'Analytify_Loader' ) ) {
					$this->loader = new Analytify_Loader( $this );
				}
			}
		}

		// ========================================.
		// CONSTRUCTOR & CORE METHODS END.
		// ========================================.

		// ========================================.
		// WordPress HOOKS & ACTIONS START.
		// ========================================.

		/**
		 * Run action && filter hooks
		 *
		 * @access      private
		 * @since       1.2.2
		 * @return      void
		 */
		private function hooks() {

			add_action( 'init', array( $this, 'load_textdomain' ) ); // Hook load_textdomain.
			add_action( 'admin_init', array( $this, '_save_core_version' ) );
			add_action( 'admin_init', array( $this, 'wpa_check_authentication' ) );
			add_action( 'admin_init', array( $this, 'logout' ), 1 );
			add_filter( 'removable_query_args', array( $this, 'Analytify_remove_query' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

			add_action( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
			add_filter( 'wpb_sdk_plugin_action_links', array( $this, 'wp_analytify_plugin_action_links' ), 10, 3 );

			add_action( 'wp_head', array( $this, 'analytify_add_analytics_code' ) );
			add_action( 'wp_head', array( $this, 'analytify_add_manual_analytics_code' ) );

			add_filter( 'admin_footer_text', 'wpa_admin_rate_footer_text', 1 );
			add_action( 'admin_footer', 'wpa_print_js', 25 );

			add_action( 'admin_init', array( $this, 'redirect_optin' ) );
			add_action( 'admin_init', array( $this, 'block_subscriber_analytify_access' ) );

			add_action( 'analytify_cleanup_logs', array( $this, 'analytify_cleanup_logs' ) );
			add_action(
				'init',
				function () {
					if ( WPANALYTIFY_Utils::get_option( 'locally_host_analytics', 'wp-analytify-advanced', false ) && ! wp_next_scheduled( 'analytify_analytics_lib_cron' ) ) {
						wp_schedule_event( time(), 'daily', 'analytify_analytics_lib_cron' );
					}
				}
			);

			add_action(
				'analytify_analytics_lib_cron',
				function () {
					if ( class_exists( 'Analytify_Host_Analytics' ) ) {
						new Analytify_Host_Analytics( 'gtag', true );
					}
				}
			);
		}

		/**
		 * Redirect to Welcome page.
		 *
		 * @since 2.0.14
		 * @return void
		 */
		public function redirect_optin() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

			if ( $page && ( 'analytify-settings' === $page || 'analytify-dashboard' === $page || 'analytify-pmpro' === $page || 'analytify-learndash' === $page || 'analytify-lifterlms' === $page || 'analytify-woocommerce' === $page || 'analytify-addons' === $page ) ) {
				if ( ! get_site_option( '_analytify_optin' ) ) {
					wp_safe_redirect( admin_url( 'admin.php?page=analytify-optin' ) );
					exit;
				}
			}
		}


		/**
		 * Internationalization.
		 *
		 * @access      public
		 * @since       1.2.2
		 * @return      void
		 */
		public function load_textdomain() {
			$plugin_dir = basename( __DIR__ );
			load_plugin_textdomain( 'wp-analytify', false, $plugin_dir . '/languages/' );
		}

		/**
		 *
		 * Save Authentication code on return
		 * && nonce verification
		 *
		 * @return void
		 */
		public function wpa_check_authentication() {

			$state = isset( $_GET['state'] ) ? json_decode( urldecode( sanitize_text_field( wp_unslash( $_GET['state'] ) ) ), true ) : null;

			if ( isset( $_GET['code'] ) && isset( $_GET['page'] ) && 'analytify-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
				$get_nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : null;
				$nonce     = isset( $state['nonce'] ) ? $state['nonce'] : $get_nonce;

				if ( wp_verify_nonce( $nonce, 'analytify_analytics_login' ) ) {

					$key_google_token = sanitize_text_field( wp_unslash( $_GET['code'] ) );
					update_option( 'WP_ANALYTIFY_NEW_LOGIN', 'yes' );
					delete_transient( 'analytify_google_userinfo_probe_skip' );
					self::pt_save_data( $key_google_token );
					wp_safe_redirect( admin_url( 'admin.php?page=analytify-settings' ) . '#wp-analytify-profile' );
					exit;

				} else {
					$plugin_page_url = admin_url( 'plugins.php' );
					wp_die(
						sprintf(    // translators: Nonce verification failed.
							esc_html__( 'Sorry, you are not allowed as nonce verification failed. %1$sClick here to return to the Dashboard%2$s.', 'wp-analytify' ),
							'<a href="' . esc_url( $plugin_page_url ) . '">',
							'</a>'
						)
					);
				}
			}
		}

		/**
		 * Save version number of the plugin && show a custom message for users
		 *
		 * @since 1.3
		 * @return void
		 */
		public function _save_core_version() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Underscore prefix is intentional for internal method
			if ( ANALYTIFY_VERSION !== get_option( 'WP_ANALYTIFY_PLUGIN_VERSION' ) ) {
				update_option( 'WP_ANALYTIFY_PLUGIN_VERSION_OLD', get_option( 'WP_ANALYTIFY_PLUGIN_VERSION' ), true );  // saving old plugin version.
				update_option( 'WP_ANALYTIFY_PLUGIN_VERSION', ANALYTIFY_VERSION );
			}
		}





		// ========================================.
		// GOOGLE ANALYTICS TRACKING CODE START.
		// ========================================.

		/**
		 * Add Google Analytics JS code
		 *
		 * @since 1.0.0
		 * @version 7.0.4
		 * @return bool|void Returns false if tracking is blocked/skipped, true if code is output, void otherwise.
		 */
		public function analytify_add_analytics_code() {
			if ( WPANALYTIFY_Utils::skip_page_tracking() ) {
				return false;
			}

			// Check for GDPR compliance.
			if ( Analytify_GDPR_Compliance::is_gdpr_compliance_blocking() ) {
				return false;
			}

			if ( 'on' === $this->settings->get_option( 'install_ga_code', 'wp-analytify-profile', 'off' ) ) {
				global $current_user;

				$roles = $current_user->roles;

				if ( isset( $roles[0] ) && in_array( $roles[0], $this->settings->get_option( 'exclude_users_tracking', 'wp-analytify-profile', array() ), true ) ) {
					echo '<!-- This user is disabled from tracking by Analytify !-->';
					return false;
				} else {
					if ( ! $this->settings->get_option( 'profile_for_posts', 'wp-analytify-profile' ) ) {
						return false;
					}

					$ua_code = WP_ANALYTIFY_FUNCTIONS::get_UA_code();

					// Validate UA code before outputting tracking code.
					if ( empty( $ua_code ) ) {
						// Add a comment in HTML for debugging.
						echo '<!-- Analytify: No tracking code - check profile selection or OAuth connection -->';
						return false;
					}

					if ( 'gtag' === WP_ANALYTIFY_TRACKING_MODE && $this->maybe_schedule_consent_deferred_gtag( $ua_code, 'analytify_ga_script' ) ) {
						return false;
					}

					if ( 'gtag' === WP_ANALYTIFY_TRACKING_MODE ) {
						$ga_code = $this->output_gtag_code( $ua_code );
					} else {
						$ga_code = $this->output_ga_code( $ua_code );
					}

					echo apply_filters( 'analytify_ga_script', $ga_code ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JavaScript code needs to be output directly
					return true;
				}
			}
		}

		/**
		 * Add Google Manual Analytics JS code
		 *
		 * @return void
		 */
		public function analytify_add_manual_analytics_code() {
			if ( get_option( 'pa_google_token' ) ) {
				return;
			}

			$manual_ua_code = $this->settings->get_option( 'manual_ua_code', 'wp-analytify-authentication', false );

			if ( ! $manual_ua_code ) {
				return;
			}

			if ( class_exists( 'WPANALYTIFY_Utils' ) && WPANALYTIFY_Utils::skip_page_tracking() ) {
				return;
			}

			if ( Analytify_GDPR_Compliance::is_gdpr_compliance_blocking() ) {
				return;
			}

			global $current_user;
			$roles = $current_user->roles;

			if ( in_array( 'administrator', $roles, true ) ) {
				echo '<!-- This user is disabled from tracking by Analytify !-->';
			} elseif ( apply_filters( 'analytify_manaul_ga_script', false ) ) {
					echo apply_filters( 'analytify_ga_script', $this->output_ga_code( $manual_ua_code ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JavaScript code needs to be output directly
			} elseif ( $this->maybe_schedule_consent_deferred_gtag( $manual_ua_code, 'analytify_gtag_script' ) ) {
				return;
			} else {
				echo apply_filters( 'analytify_gtag_script', $this->output_gtag_code( $manual_ua_code ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JavaScript code needs to be output directly
			}
		}

		/**
		 * Schedule gtag for wp_footer when consent-deferred mode is on (any compatible cookie plugin).
		 *
		 * @since 9.0.0
		 * @param string $ua_code       Measurement ID (or legacy property id).
		 * @param string $result_filter Filter name for the deferred markup.
		 * @return bool True when deferred (no head output).
		 */
		private function maybe_schedule_consent_deferred_gtag( $ua_code, $result_filter = 'analytify_ga_script' ) {

			if ( ! class_exists( 'Class_Analytify_GDPR_Compliance' ) ) {
				return false;
			}

			if ( ! Class_Analytify_GDPR_Compliance::is_consent_deferred_gtag_enabled() ) {
				return false;
			}

			if ( ! defined( 'WP_ANALYTIFY_TRACKING_MODE' ) || 'gtag' !== WP_ANALYTIFY_TRACKING_MODE ) {
				return false;
			}

			$ua_code = is_string( $ua_code ) ? trim( $ua_code ) : '';
			if ( '' === $ua_code ) {
				return false;
			}

			$allowed_filters = array( 'analytify_ga_script', 'analytify_gtag_script' );
			if ( ! in_array( $result_filter, $allowed_filters, true ) ) {
				$result_filter = 'analytify_ga_script';
			}

			if ( $this->consent_deferred_gtag_hooked ) {
				return true;
			}

			$this->consent_deferred_gtag_filter  = $result_filter;
			$this->consent_deferred_gtag_ua_code = $ua_code;
			$this->consent_deferred_gtag_hooked  = true;
			add_action( 'wp_footer', array( $this, 'analytify_print_consent_deferred_gtag' ), 1 );

			return true;
		}

		/**
		 * Print base64 gtag payload and loader for consent-deferred integrations.
		 *
		 * Listens to DOM events from {@see Class_Analytify_GDPR_Compliance::get_consent_ready_dom_events()}
		 * and exposes {@see Class_Analytify_GDPR_Compliance::get_consent_deferred_js_loader_name()} on window.
		 *
		 * @since 9.0.0
		 * @return void
		 */
		public function analytify_print_consent_deferred_gtag() {

			if ( null === $this->consent_deferred_gtag_ua_code ) {
				return;
			}

			if ( class_exists( 'WPANALYTIFY_Utils' ) && WPANALYTIFY_Utils::skip_page_tracking() ) {
				return;
			}

			if ( Analytify_GDPR_Compliance::is_gdpr_compliance_blocking() ) {
				return;
			}

			global $current_user;
			$roles = $current_user->roles;

			if ( isset( $roles[0] ) && in_array( $roles[0], $this->settings->get_option( 'exclude_users_tracking', 'wp-analytify-profile', array() ), true ) ) {
				return;
			}

			$ua = $this->consent_deferred_gtag_ua_code;

			$this->output_gtag_for_footer_payload = true;
			$markup                               = $this->output_gtag_code( $ua );
			$this->output_gtag_for_footer_payload = false;
			if ( '' === $markup ) {
				return;
			}

			$markup = apply_filters( $this->consent_deferred_gtag_filter, $markup ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core plugin filter.

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Deferred gtag payload for atob(); not obfuscation.
			$b64 = base64_encode( $markup );

			$dom_events = class_exists( 'Class_Analytify_GDPR_Compliance' )
				? Class_Analytify_GDPR_Compliance::get_consent_ready_dom_events()
				: array( 'analytifyConsentGranted' );

			$js_global = class_exists( 'Class_Analytify_GDPR_Compliance' )
				? Class_Analytify_GDPR_Compliance::get_consent_deferred_js_loader_name()
				: 'analytifyLoadGtagAfterConsent';

			?>
			<script type="text/plain" id="analytify-consent-deferred-gtag-b64" style="display:none;"><?php echo esc_html( $b64 ); ?></script>
			<script>
			(function () {
				var events = <?php echo wp_json_encode( $dom_events ); ?>;
				var globalName = <?php echo wp_json_encode( $js_global ); ?>;
				function analytifyConsentDeferredInjectGtag() {
					if (window.__analytifyConsentDeferredGtagDone) {
						return;
					}
					var el = document.getElementById('analytify-consent-deferred-gtag-b64');
					if (!el || !el.textContent) {
						return;
					}
					var html;
					try {
						html = atob(el.textContent.replace(/\s/g, ''));
					} catch (e) {
						return;
					}
					window.__analytifyConsentDeferredGtagDone = true;
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var nodes = doc.querySelectorAll('script');
					for (var i = 0; i < nodes.length; i++) {
						var oldScript = nodes[i];
						var s = document.createElement('script');
						if (oldScript.src) {
							s.src = oldScript.src;
							s.async = true;
						} else {
							s.textContent = oldScript.textContent;
						}
						document.head.appendChild(s);
					}
				}
				if (events && events.length) {
					for (var j = 0; j < events.length; j++) {
						document.addEventListener(events[j], analytifyConsentDeferredInjectGtag, false);
					}
				}
				if (globalName && typeof globalName === 'string' && /^[a-zA-Z_$][a-zA-Z0-9_$]*$/.test(globalName)) {
					window[globalName] = analytifyConsentDeferredInjectGtag;
				}
			})();
			</script>
			<?php
		}

		/**
		 * Back-compat alias for {@see self::analytify_print_consent_deferred_gtag()}.
		 *
		 * @since 9.0.0
		 * @return void
		 */
		public function analytify_print_ccv_deferred_gtag() {

			$this->analytify_print_consent_deferred_gtag();
		}

		/**
		 * Generate gtag code.
		 *
		 * @param  string $ua_code Google Analytics UA code.
		 * @since 3.0
		 * @version 9.1.0
		 * @return $gtag_code
		 */
		private function output_gtag_code( $ua_code ) {
			ob_start();

			$local_analytics_file = class_exists( 'Analytify_Host_Analytics' ) ? ( new Analytify_Host_Analytics( 'gtag', false ) )->local_analytics_file_url() : false;

			if ( false !== apply_filters( 'analytify_tracking_code_comments', true ) ) {
				printf( // translators: Tracking code comments.
					esc_html__( '%2$s This code is added by Analytify (%1$s) %4$s %3$s', 'wp-analytify' ),
					esc_html( ANALYTIFY_VERSION ),
					'<!--',
					'!-->',
					'https://analytify.io/'
				);
			}

			$allow_display_features       = ( 'on' === $this->settings->get_option( 'demographic_interest_tracking', 'wp-analytify-advanced' ) ) ? 'true' : 'false';
			$linker_cross_domain_tracking = ( 'on' === $this->settings->get_option( 'linker_cross_domain_tracking', 'wp-analytify-advanced' ) ) ? true : false;
			$linked_domains               = array();

			if ( $linker_cross_domain_tracking ) {
				$all_linked_domains = $this->settings->get_option( 'linked_domain', 'wp-analytify-advanced' );
				$all_linked_domains = trim( $all_linked_domains ? $all_linked_domains : '' );

				if ( ! empty( $all_linked_domains ) ) {
					$all_linked_domains = str_replace( "'", '', $all_linked_domains );
					$all_linked_domains = str_replace( '"', '', $all_linked_domains );

					$all_linked_domains = preg_replace( '/\s+/', '', $all_linked_domains );

					$list_linked_domains      = explode( ',', $all_linked_domains );
					$number_of_linked_domains = count( $list_linked_domains );

					if ( $number_of_linked_domains > 0 ) {
						$linked_domains = array_filter(
							(array) $list_linked_domains,
							function ( $value ) {
								return strlen( $value ) > 0;
							}
						);
					} else {
						$linked_domains = (array) $all_linked_domains;
					}
				} else {
					$linker_cross_domain_tracking = false;
				}
			}

			$configuration = array(

				'allow_display_features' => $allow_display_features,
			);

			if ( $linker_cross_domain_tracking ) {
				$configuration['linker'] = array(
					'domains' => $linked_domains,
				);
			}

			$debug_mode = apply_filters( 'analytify_debug_mode', false );

			if ( $debug_mode ) {
				$configuration['debug_mode'] = true;
			}

			if ( 'on' === $this->settings->get_option( 'track_user_id', 'wp-analytify-advanced' ) && is_user_logged_in() ) {
				$configuration['user_id'] = esc_html( (string) get_current_user_id() );
			}

			$configuration      = apply_filters( 'analytify_gtag_configuration', $configuration );
			$configuration_json = wp_json_encode( $configuration, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );

			// Build the script src URL correctly.
			$script_src  = $local_analytics_file ?? 'https://www.googletagmanager.com/gtag/js';
			$script_src .= ( strpos( $script_src, '?' ) !== false ? '&' : '?' ) . 'id=' . esc_attr( $ua_code );

			// Excluded query parameters: strip only these query keys from page_location (path and hash unchanged).
			$exclude_params = 'on' === $this->settings->get_option( 'exclude_query_params', 'wp-analytify-advanced' );
			$params_list    = $this->settings->get_option( 'query_params_to_exclude', 'wp-analytify-advanced' );
			$params_list    = is_string( $params_list ) ? $params_list : '';

			$js_location_logic = '';
			if ( $exclude_params && '' !== $params_list ) {
				$params_array = analytify_query_params_to_exclude_keys_array( $params_list );
				if ( ! empty( $params_array ) ) {
					$params_json = wp_json_encode( $params_array );

					// Strip only query keys (case-insensitive); path and hash are not altered; no query string / repeated params are safe.
					$js_location_logic = "
			try {
				var wa_loc = window.location.href;
				var wa_url = new URL(wa_loc);
				var wa_params = $params_json;
				var keysToRemove = [];
				wa_url.searchParams.forEach(function(val, key) {
					if (wa_params.indexOf(key.toLowerCase()) !== -1) { keysToRemove.push(key); }
				});
				keysToRemove.forEach(function(k) { wa_url.searchParams.delete(k); });
				configuration.page_location = wa_url.toString();
			} catch (e) {}
				";
				}
			}

			$gdpr_class       = 'Class_Analytify_GDPR_Compliance';
			$has_gdpr_class   = class_exists( $gdpr_class );
			$consent_params   = $has_gdpr_class ? Class_Analytify_GDPR_Compliance::get_gtag_consent_default_params() : array();
			$has_consent_line = $has_gdpr_class
				&& Class_Analytify_GDPR_Compliance::should_output_gtag_consent_defaults()
				&& ! empty( $consent_params );
			$use_defer_gtag   = $has_gdpr_class
				&& Class_Analytify_GDPR_Compliance::is_consent_deferred_gtag_enabled()
				&& ! $this->output_gtag_for_footer_payload;
			$defer_events     = $has_gdpr_class ? Class_Analytify_GDPR_Compliance::get_consent_ready_dom_events() : array();
			if ( $use_defer_gtag && empty( $defer_events ) ) {
				$use_defer_gtag = false;
			}
			$split_gtag_markup  = $has_consent_line || $use_defer_gtag;
			$defer_pageview_cmp = $has_gdpr_class
				&& ! $use_defer_gtag
				&& Class_Analytify_GDPR_Compliance::should_defer_gtag_pageview_until_cmp_consent();
			$consent_json       = wp_json_encode(
				$consent_params,
				JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
			);
			$defer_events_json  = wp_json_encode( array_values( $defer_events ) );
			$script_src_json    = wp_json_encode( $script_src );
			$loader_name        = $use_defer_gtag ? Class_Analytify_GDPR_Compliance::get_consent_deferred_js_loader_name() : '';
			?>

			<?php if ( $split_gtag_markup ) : ?>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
				<?php if ( $has_consent_line ) : ?>
			gtag('consent', 'default', <?php echo $consent_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON from sanitized consent params. ?>);
			<?php endif; ?>
			</script>
			<?php endif; ?>

			<?php if ( ! $use_defer_gtag ) : ?>
			<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- Inline gtag script must be output directly in the page head. ?>
			<script async src="<?php echo esc_url( $script_src ); ?>"></script>
			<?php endif; ?>

			<script>
			<?php if ( ! $split_gtag_markup ) : ?>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			<?php endif; ?>
			<?php if ( ! $use_defer_gtag ) : ?>
			gtag('js', new Date());
			<?php endif; ?>

			const configuration = <?php echo $configuration_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() with JSON_HEX flags already escapes JSON safely for JavaScript context. ?>;
			const gaID = '<?php echo esc_js( $ua_code ); ?>';

			<?php if ( $use_defer_gtag ) : ?>
			(function() {
				var gtagSrc = <?php echo $script_src_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() on URL. ?>;
				var consentEvents = <?php echo $defer_events_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() on validated event names. ?>;
				var injectFired = false;
				function analytifyInjectGtagAfterConsent() {
					if (injectFired) {
						return;
					}
					injectFired = true;
					var s = document.createElement('script');
					s.async = true;
					s.src = gtagSrc;
					s.onload = function() {
						gtag('js', new Date());
						<?php do_action( 'analytify_tracking_code_before_pageview' ); ?>
						<?php
						if ( ! empty( $js_location_logic ) ) {
							echo $js_location_logic; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JS logic constructed internally with safe JSON.
						}
						?>
						gtag('config', gaID, configuration);
						<?php
						if ( $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' ) ) {
							$custom_js = $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' );
							if ( ! empty( $custom_js ) ) {
								echo wp_kses_post( $custom_js );
							}
						}
						do_action( 'ga_ecommerce_js' );
						do_action( 'analytify_tracking_code_after_pageview' );
						?>
					};
					document.head.appendChild(s);
				}
				if (consentEvents.length) {
					consentEvents.forEach(function(evName) {
						document.addEventListener(evName, analytifyInjectGtagAfterConsent);
					});
				}
				<?php if ( '' !== $loader_name ) : ?>
				window['<?php echo esc_js( $loader_name ); ?>'] = analytifyInjectGtagAfterConsent;
				<?php endif; ?>
			})();
			<?php else : ?>
				<?php if ( $defer_pageview_cmp ) : ?>
			(function() {
				var fired = false;
				function firePageview() {
					if (fired) { return; }
					fired = true;
					<?php do_action( 'analytify_tracking_code_before_pageview' ); ?>
					<?php
					if ( ! empty( $js_location_logic ) ) {
						echo $js_location_logic; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JS logic constructed internally with safe JSON.
					}
					?>
					gtag('config', gaID, configuration);
					<?php
					if ( $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' ) ) {
						$custom_js = $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' );
						if ( ! empty( $custom_js ) ) {
							echo wp_kses_post( $custom_js );
						}
					}
					do_action( 'ga_ecommerce_js' );
					do_action( 'analytify_tracking_code_after_pageview' );
					?>
				}
				function onAnalyticsGranted() {
					gtag('consent', 'update', {
						analytics_storage: 'granted',
						personalization_storage: 'granted'
					});
					firePageview();
				}
				document.addEventListener('cookieyes_consent_update', function(evt) {
					var accepted = evt.detail && evt.detail.accepted;
					if (accepted && -1 !== accepted.indexOf('analytics')) {
						onAnalyticsGranted();
					}
				});
				document.addEventListener('cookieyes_banner_load', function() {
					if ('function' === typeof window.getCkyConsent && getCkyConsent().categories.analytics) {
						onAnalyticsGranted();
					}
				});
				document.addEventListener('cookielawinfo-consent', onAnalyticsGranted);
			})();
				<?php else : ?>
					<?php do_action( 'analytify_tracking_code_before_pageview' ); ?>
					<?php
					if ( ! empty( $js_location_logic ) ) {
						echo $js_location_logic; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JS logic constructed internally with safe JSON.
					}
					?>

			gtag('config', gaID, configuration);

					<?php
					if ( $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' ) ) {
						$custom_js = $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' );

						if ( ! empty( $custom_js ) ) {
							echo wp_kses_post( $custom_js );
						}
					}

					do_action( 'ga_ecommerce_js' );
					do_action( 'analytify_tracking_code_after_pageview' );
					?>
				<?php endif; ?>
			<?php endif; ?>

			</script>

			<?php
			if ( false !== apply_filters( 'analytify_tracking_code_comments', true ) ) {
				printf( // translators: Tracking code comments.
					esc_html__( '%2$s This code is added by Analytify (%1$s) %3$s', 'wp-analytify' ),
					esc_html( ANALYTIFY_VERSION ),
					'<!--',
					'!-->'
				);
			}

			$gtag_code = ob_get_contents();
			ob_end_clean();
			return $gtag_code ? $gtag_code : '';
		}

		/**
		 * Generate gtag code.
		 *
		 * @param  string $ua_code Google Analytics UA code.
		 * @since 3.0
		 * @return string
		 */
		public function output_ga_code( $ua_code ) {
			ob_start();

			$src = apply_filters( 'analytify_output_ga_js_src', '//www.google-analytics.com/analytics.js' );

			if ( false !== apply_filters( 'analytify_tracking_code_comments', true ) ) {
				printf( // translators: Tracking code comments.
					esc_html__( '%2$s This code is added by Analytify (%1$s) %4$s %3$s', 'wp-analytify' ),
					esc_html( ANALYTIFY_VERSION ),
					'<!--',
					'!-->',
					'https://analytify.io/'
				);
			}
			?>

			<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})

				(window,document,'script','<?php echo esc_url( $src ); ?>','ga');
				
				<?php
				if ( 'on' === $this->settings->get_option( 'linker_cross_domain_tracking', 'wp-analytify-advanced' ) ) {
					echo "	ga('create', '" . esc_js( $ua_code ) . "', 'auto', {'allowLinker': true});";
					echo "ga('require', 'linker');";
				} else {
					echo "	ga('create', '" . esc_js( $ua_code ) . "', 'auto');";
				}

				if ( 'on' === $this->settings->get_option( 'track_user_id', 'wp-analytify-advanced' ) && is_user_logged_in() ) {
					echo "ga('set', 'userId', " . esc_html( (string) get_current_user_id() ) . ');';
				}

				if ( 'on' === $this->settings->get_option( 'demographic_interest_tracking', 'wp-analytify-advanced' ) ) {
					echo "ga('require', 'displayfeatures');";
				}

				if ( $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' ) ) {

					$custom_js = $this->settings->get_option( 'custom_js_code', 'wp-analytify-advanced' );

					if ( $custom_js && false !== $custom_js ) {
						$custom_js = wp_kses(
							$custom_js,
							array(
								'script'   => array(),
								'noscript' => array(),
								'div'      => array(
									'id'    => array(),
									'class' => array(),
								),
								'span'     => array(
									'id'    => array(),
									'class' => array(),
								),
							)
						);
						echo '<script type="text/javascript">' . esc_js( wp_strip_all_tags( $custom_js ) ) . '</script>';
					}
				}

							do_action( 'ga_ecommerce_js' );
				do_action( 'analytify_tracking_code_before_pageview' );
				echo "ga('send', 'pageview');";
				?>

			</script>

			<?php
			if ( false !== apply_filters( 'analytify_tracking_code_comments', true ) ) {
				printf( // translators: Tracking code comments.
					esc_html__( '%2$s This code is added by Analytify (%1$s) %3$s', 'wp-analytify' ),
					esc_html( ANALYTIFY_VERSION ),
					'<!--',
					'!-->'
				);
			}

			$ga_code = ob_get_contents();
			ob_end_clean();
			return $ga_code ? $ga_code : '';
		}

		// ========================================.
		// GOOGLE ANALYTICS TRACKING CODE END.
		// ========================================.


		/**
		 * Adds Analytify-specific action links on the Plugins screen (Settings, Pro notices).
		 *
		 * Opt In / Opt Out are registered by the bundled WPB SDK only.
		 *
		 * @param array<int|string, string> $links  Plugin row action links.
		 * @param string                    $slug   Product slug from the SDK.
		 * @param array<string, mixed>      $module Module definition from the SDK.
		 * @return array<int|string, string>
		 */
		public function wp_analytify_plugin_action_links( $links, $slug = '', $module = array() ) {
			if ( '' !== $slug && 'wp-analytify' !== $slug ) {
				return $links;
			}
			$settings_link = '';
			if ( class_exists( 'WP_Analytify_Pro' ) ) {
				// Remove deactivate link if Pro is active, and add a message that it's required by Pro.
				$settings_link .= sprintf(
					/* translators: %1$s: opening span tag, %2$s: closing span tag. */
					esc_html__( '%1$s Required by Analytify Pro %2$s | ', 'wp-analytify' ),
					'<span style="color:#32373c">',
					'</span>'
				);
				unset( $links['deactivate'] );
			} elseif ( class_exists( 'Analytify_Dashboard_Addon' ) ) {
				// Remove deactivate link if Widget is active, and add a message that it's required by Widget.
				$settings_link .= sprintf(
					/* translators: %1$s: opening span tag, %2$s: closing span tag. */
					esc_html__( '%1$s Required by Analytify Dashboard Widget %2$s | ', 'wp-analytify' ),
					'<span style="color:#32373c">',
					'</span>'
				);
				unset( $links['deactivate'] );
			}

			if ( ! class_exists( 'WP_Analytify_Pro' ) ) {
				$settings_link .= sprintf(
					/* translators: %1$s opening link tag, %2$s closing link tag */
					esc_html__( '%1$s Get Analytify Pro %2$s | ', 'wp-analytify' ),
					'<a href="' . esc_url( 'https://analytify.io/pricing/?utm_source=analytify-lite&utm_medium=plugin-action-link&utm_campaign=pro-upgrade&utm_content=Get+Analytify+Pro' ) . '" target="_blank" rel="noopener noreferrer" style="color:#3db634;">',
					'</a>'
				);
			}

			$settings_link .= sprintf(
				/* translators: %1$s opening link tag, %2$s closing link tag */
				esc_html__( '%1$s Settings %2$s', 'wp-analytify' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=analytify-settings' ) ) . '">',
				'</a>'
			);

			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 * Plugin row meta links
		 *
		 * @since 1.1
		 * @version 5.0.5
		 * @param array  $input already defined meta links.
		 * @param string $file plugin file path && name being processed.
		 * @param array  $plugin_data relevent data about the currect plugin.
		 * @param string $status weather plugin is active or disabled.
		 * @return array $input
		 */
		public function plugin_row_meta( $input, $file, $plugin_data, $status ) {

			$analytify_plugins = array(
				'wp-analytify/wp-analytify.php',
				'analytify-analytics-dashboard-widget/wp-analytify-dashboard.php',
				'wp-analytify-pro/wp-analytify-pro.php',
				'wp-analytify-woocommerce/wp-analytify-woocommerce.php',
				'wp-analytify-goals/wp-analytify-goals.php',
				'wp-analytify-forms/wp-analytify-forms.php',
				'wp-analytify-email/wp-analytify-email.php',
				'wp-analytify-edd/wp-analytify-edd.php',
				'wp-analytify-authors/wp-analytify-authors.php',
			);

			if ( ! in_array( $file, $analytify_plugins, true ) ) {
				return $input;
			}

			if ( isset( $plugin_data['Author'] ) ) {

				$input[1] = sprintf(
					'By <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
					esc_url( $plugin_data['AuthorURI'] ),
					esc_html( $plugin_data['Author'] )
				);

			}

			if ( 'wp-analytify/wp-analytify.php' !== $file && 'analytify-analytics-dashboard-widget/wp-analytify-dashboard.php' !== $file && in_array( $file, $analytify_plugins, true ) ) {

				$input[2] = sprintf(
					'<a href="%s" target="_blank" rel="noopener noreferrer">Visit Plugin Site</a>',
					esc_url( $plugin_data['PluginURI'] )
				);
				return $input;

			}

			$links = array(
				sprintf( // translators: Premium features.
					esc_html__( '%1$s Explore Premium Features %2$s', 'wp-analytify' ),
					'<a target="_blank" href="https://analytify.io/add-ons/?ref=27&utm_source=analytify-pro&utm_medium=plugin-action-link&utm_campaign=pro-upgrade&utm_content=Explore+Premium+Features">',
					'</a>'
				),
			);

			$input = array_merge( $input, $links );

			return $input;
		}

		/**
		 * Display warning if profiles are not selected.
		 *
		 * @return void
		 */
		public function pa_check_warnings() {
			add_action( 'admin_footer', array( &$this, 'profile_warning' ) );
		}


		// Page routing function moved to Page_Management component.




		// ========================================.
		// ADMIN ASSETS & STYLING START.
		// ========================================.

		// Admin styles function moved to Scripts_Styles component.

		// Functions moved to Scripts_Styles, Module_Manager && Promotions components.

		// ========================================.
		// ANALYTICS DATA RETRIEVAL START.
		// ========================================.

		// Analytics accounts function moved to Analytics_Accounts component.

		// Analytics accounts summary function moved to Analytics_Accounts component.

		/**
		 * Get settings URL.
		 *
		 * @return string
		 */
		public function pa_setting_url() {
			return admin_url( 'admin.php?page=analytify-settings' );
		}

		/**
		 * Save Google token data.
		 *
		 * @param mixed $key_google_token The Google token data.
		 * @return bool|void
		 */
		public function pt_save_data( $key_google_token ) {
			try {
				update_option( 'post_analytics_token', $key_google_token );

				if ( $this->analytify_pa_connect_v2() ) {
					return true;
				}
			} catch ( Exception $e ) {
				echo esc_html( $e->getMessage() );
			}
		}

		// Profile warning function moved to Promotions component.

		// AJAX handler moved to Analytics_Reports component.

		// Module state function moved to Module_Manager component.




		// ========================================.
		// ANALYTICS DATA RETRIEVAL END.
		// ========================================.

		// Deprecated analytics function moved to Analytics_Reports component.




		// ========================================.
		// UTILITY & HELPER FUNCTIONS START.
		// ========================================.

		// Utility functions moved to Utils component.
		// Backward compatibility wrappers.
		/**
		 * Format numbers for display.
		 *
		 * @param mixed $num The number to format.
		 * @return string
		 */
		public function wpa_pretty_numbers( $num ) {
			return Analytify_Utils::wpa_pretty_numbers( $num );
		}

		/**
		 * Format number with proper formatting.
		 *
		 * @param mixed $num The number to format.
		 * @return string
		 */
		public function wpa_number_format( $num ) {
			return Analytify_Utils::wpa_number_format( $num );
		}

		/**
		 * Format time for display.
		 *
		 * @param mixed $time The time to format.
		 * @return string
		 */
		public function pa_pretty_time( $time ) {
			return Analytify_Utils::pa_pretty_time( $time );
		}

		/**
		 * Check user access level.
		 *
		 * @param mixed $access_level The required access level.
		 * @return bool
		 */
		public function pa_check_roles( $access_level ) {
			return Analytify_Utils::pa_check_roles( $access_level );
		}


		/**
		 * Notice to switch gtag.js tracking mode.
		 *
		 * @return void
		 */
		// Welcome message function moved to Promotions component.

		// ========================================.
		// ADMIN MENU & PAGE ROUTING START.
		// ========================================.

		/**
		 * Create Analytics menu at the left side of dashboard
		 *
		 * @version 9.1.0
		 * @return void
		 */
		public function add_admin_menu() {
			$current_user = wp_get_current_user();

			if ( Analytify_Utils::is_subscriber_blocked_from_analytify() ) {
				return;
			}

			$allowed_roles   = $this->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard', array() );
			$allowed_roles[] = 'administrator';

			$is_author_only = $current_user && in_array( 'author', $current_user->roles, true ) && ! in_array( 'administrator', $current_user->roles, true );

			// Determine the capability to use for the dashboard.
			// If the user has an allowed role, we use 'read' to ensure they can see the menu,
			// since add_admin_menu already restricted registration to allowed roles.
			$dashboard_cap = 'manage_options';
			if ( array_intersect( $current_user->roles, $allowed_roles ) ) {
				$dashboard_cap = 'read';
			}

			// Allow authors to see the menu (so they can access Authors dashboard submenu).
			// But restrict them from accessing main dashboard content.
			if ( $is_author_only ) {
				// Authors can see menu but will be restricted from main dashboard page.
				// They need edit_posts capability to see Authors dashboard.
				if ( ! current_user_can( 'edit_posts' ) ) {
					return;
				}
			} elseif ( ! $this->pa_check_roles( $allowed_roles ) ) {
				// For non-authors, check normal access.
				return;
			}

			add_submenu_page( 'admin.php', __( 'Activate', 'wp-analytify' ), __( 'Activate', 'wp-analytify' ), 'manage_options', 'analytify-optin', array( $this, 'render_optin' ) );

			add_menu_page(
				ANALYTIFY_NICK,
				'Analytify',
				$dashboard_cap,
				'analytify-dashboard',
				array(
					$this,
					'pa_page_file_path',
				),
				'data:image/svg+xml;base64,' . base64_encode( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Used for SVG data encoding
					'<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			width="18px" height="18px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
			<style type="text/css">
				.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#23282D;}
				.st1{fill-rule:evenodd;clip-rule:evenodd;fill:#9EA3A8;}
			</style>
			<g>
				<path class="st0" d="M17.2,16c-0.4,0-0.8-0.3-0.8-0.8v-0.8V14c-1.6,2.4-4.3,4-7.5,4c-5,0-9-4-9-9s4-9,9-9c3.1,0,5.8,1.6,7.5,4V3.8
					v0l0,0c0-0.4,0.4-0.7,0.8-0.7S18,3.4,18,3.8l0,0v0V5v9.5v0.8C18,15.6,17.7,16,17.2,16z M9,1.5C4.9,1.5,1.5,4.9,1.5,9
					s3.4,7.5,7.5,7.5s7.5-3.4,7.5-7.5S13.1,1.5,9,1.5z"/>
				<g>
					<g>
						<path class="st1" d="M5.9,8.4c-0.5,0-0.9,0.4-0.9,0.9v2.9c0,0.5,0.4,0.9,0.9,0.9s0.9-0.4,0.9-0.9V9.3C6.7,8.8,6.3,8.4,5.9,8.4z
								M9,7C8.5,7,8.1,7.4,8.1,7.9v4.3C8.1,12.7,8.5,13,9,13s0.9-0.4,0.9-0.9V7.9C9.9,7.4,9.5,7,9,7z M12.1,4.9c-0.5,0-0.9,0.4-0.9,0.9
							v6.4c0,0.5,0.4,0.9,0.9,0.9s0.9-0.4,0.9-0.9V5.8C12.9,5.3,12.6,4.9,12.1,4.9z"/>
					</g>
				</g>
			</g>
			</svg>'
				),
				2
			);

			add_submenu_page(
				'analytify-dashboard',
				ANALYTIFY_NICK . esc_html__( ' Dashboards', 'wp-analytify' ),
				esc_html__( 'Dashboards', 'wp-analytify' ),
				$dashboard_cap,
				'analytify-dashboard',
				array(
					$this,
					'pa_page_file_path',
				),
				10
			);

			do_action( 'analytify_add_submenu' );

			add_submenu_page(
				'analytify-dashboard',
				ANALYTIFY_NICK . esc_html__( ' Settings', 'wp-analytify' ),
				esc_html__( 'Settings', 'wp-analytify' ),
				'manage_options',
				'analytify-settings',
				array(
					$this,
					'pa_page_file_path',
				),
				50
			);

			add_submenu_page(
				'analytify-dashboard',
				ANALYTIFY_NICK . esc_html__( ' Help', 'wp-analytify' ),
				esc_html__( 'Help', 'wp-analytify' ),
				'manage_options',
				'analytify-settings#wp-analytify-help',
				array(
					$this,
					'pa_page_file_path',
				),
				55
			);

			// Add license submenu.
			if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
				add_submenu_page(
					'analytify-dashboard',
					ANALYTIFY_NICK . esc_html__( ' License', 'wp-analytify' ),
					esc_html__( 'License', 'wp-analytify' ),
					'manage_options',
					'analytify-settings#wp-analytify-license',
					array(
						$this,
						'pa_page_file_path',
					),
					60
				);
			}

			add_submenu_page(
				'analytify-dashboard',
				ANALYTIFY_NICK . esc_html__( ' list of all Add-ons', 'wp-analytify' ),
				esc_html__( 'Add-ons', 'wp-analytify' ),
				'manage_options',
				'analytify-addons',
				array(
					$this,
					'pa_page_file_path',
				),
				65
			);

			add_submenu_page(
				'analytify-dashboard',
				ANALYTIFY_NICK . esc_html__( ' FREE vs PRO', 'wp-analytify' ),
				esc_html__( 'FREE vs PRO', 'wp-analytify' ),
				'manage_options',
				'analytify-go-pro',
				array(
					$this,
					'pa_page_file_path',
				),
				70
			);

			// Promo page (will not appear in side menu).
			add_submenu_page( 'analytify-dashboard', esc_html__( 'Analytify Promo', 'wp-analytify' ), null, 'manage_options', 'analytify-promo', array( $this, 'addons_promo_screen' ) );
		}

		/**
		 * Redirect subscribers away from Analytify admin screens.
		 *
		 * @since 9.1.0
		 * @version 9.1.0
		 * @return void
		 */
		public function block_subscriber_analytify_access() {
			if ( ! Analytify_Utils::is_subscriber_blocked_from_analytify() ) {
				return;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen routing only.
			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

			if ( $page && 0 === strpos( $page, 'analytify' ) ) {
				wp_safe_redirect( admin_url() );
				exit;
			}
		}

		/**
		 * Get current screen details
		 *
		 * @version 9.1.0
		 * @return void
		 */
		public function pa_page_file_path() {
			$allowed_roles   = $this->settings->get_option( 'show_analytics_roles_dashboard', 'wp-analytify-dashboard', array() );
			$allowed_roles[] = 'administrator';

			if ( ! $this->pa_check_roles( $allowed_roles ) ) {
				wp_safe_redirect( admin_url() );
				exit;
			}

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
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">View Changelog</a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="' . esc_url( plugins_url( 'assets/img/logo.svg', __FILE__ ) ) . '" alt="Analytify">
				</div>
				</div></div><div class="analytify-settings-body-container"><div class="wpb_plugin_body_wraper"><div class="wpb_plugin_body">';
				// Initialize settings before rendering.
				$this->settings->set_sections( $this->settings->get_settings_sections() );
				$this->settings->set_fields( $this->settings->get_settings_fields() );
				$this->settings->rendered_settings();
				$this->settings->show_tabs();
				echo '<div class="wpb_plugin_tabs_content">';
				$this->settings->show_forms();
				echo '</div>';

				echo '</div></div></div></div>';

			} elseif ( strpos( $screen->base, 'analytify-dashboard' ) !== false ) {
				// Restrict authors from accessing main dashboard - redirect to Authors dashboard.
				$current_user   = wp_get_current_user();
				$is_author_only = $current_user && in_array( 'author', (array) $current_user->roles, true ) && ! in_array( 'administrator', (array) $current_user->roles, true );

				if ( $is_author_only ) {
					// Redirect authors to their Authors dashboard.
					wp_safe_redirect( admin_url( 'admin.php?page=analytify-authors' ) );
					exit;
				}

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
				if ( isset( $_GET['show'] ) ) {
					do_action( 'show_detail_dashboard_content' );
				} else {
					require_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytics-dashboard.php';
				}
			} elseif ( strpos( $screen->base, 'analytify-optin' ) !== false ) {
				require_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytify-optin-form.php';
			} elseif ( strpos( $screen->base, 'analytify-addons' ) !== false ) {
				require_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/page-addons.php';
			} elseif ( strpos( $screen->base, 'analytify-woocommerce' ) !== false ) {
				// WooCommerce page - this might not exist, let's check if file exists first.
				$woo_file = WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytify-woocommerce.php';
				if ( file_exists( $woo_file ) ) {
					require_once $woo_file;
				}
			} elseif ( strpos( $screen->base, 'analytify-logs' ) !== false ) {
				include_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/page-logs.php';
			} elseif ( strpos( $screen->base, 'analytify-go-pro' ) !== false ) {
				include_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytify-go-pro.php';
			} elseif ( isset( $_GET['show'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
				do_action( 'show_detail_dashboard_content' );
			} else {
				// Dequeue event calendar js.
				wp_dequeue_script( 'tribe-common' );
				wp_dequeue_script( 'mcw-crypto-common' );
				include_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytics-dashboard.php';
			}
		}

		// ========================================.
		// ADMIN MENU & PAGE ROUTING END.
		// ========================================.

		/**
		 * Render optin page
		 *
		 * @return void
		 */
		public function render_optin() {
			require_once WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytify-optin-form.php';
		}

		// Functions moved to Promotions && Analytics_Reports components.

		/**
		 * Process logout && clear stored options.
		 *
		 * @return void
		 */
		public function logout() {
			if ( isset( $_POST['wp_analytify_log_out'] ) && isset( $_POST['analytify_analytics_logout_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['analytify_analytics_logout_nonce'] ) ), 'analytify_analytics_logout' ) ) {
				delete_option( 'pt_webprofile' );
				delete_option( 'pt_webprofile_dashboard' );
				delete_option( 'pt_webprofile_url' );
				delete_option( 'pa_google_token' );
				$this->analytify_auth_settings_clear_google_oauth_email();
				delete_option( 'post_analytics_token' );
				delete_option( 'hide_profiles' );
				delete_option( 'analytify-ga4-streams' );
				delete_option( 'analytify_tracking_property_info' );
				delete_option( 'analytify_reporting_property_info' );
				delete_option( 'analytify_ga4_exception' );
				delete_option( 'analytify_ga4_exceptions' );
				delete_option( 'profiles_list_summary' );
				delete_option( 'analytify_ga_properties_list' );
				delete_option( 'wp-analytify-mode' );
				delete_option( 'analytify-deprecated-auth' );
				delete_option( 'analytify-ga-properties-summery' );
				delete_option( 'analytify_profile_exception' );
				delete_option( 'profiles_list_summary_backup' );
				delete_transient( 'analytify_quota_exception' );
				delete_transient( 'analytify_google_userinfo_probe_skip' );

				$_analytify_profile = get_option( 'wp-analytify-profile' );
				if ( $_analytify_profile ) {
					unset( $_analytify_profile['hide_profiles_list'], $_analytify_profile['profile_for_posts'], $_analytify_profile['profile_for_dashboard'] );
					update_option( 'wp-analytify-profile', $_analytify_profile );
				}

				$update_message = sprintf(  // translators: Login again notice.
					esc_html__( '%1$s %2$s %3$s Authentication Cleared login again. %4$s %5$s %6$s', 'wp-analytify' ),
					'<div id="setting-error-settings_updated" class="updated notice is-dismissible settings-error below-h2">',
					'<p>',
					'<strong>',
					'</strong>',
					'</p>',
					'</div>'
				);
			}
		}

		/**
		 * Used to add query args that need to be removed from url
		 *
		 * @since 5.0.6
		 * @param mixed $args_array The arguments array to process.
		 * @return array
		 */
		public function Analytify_remove_query( $args_array ) {
			$analytify_args_to_remove = array( 'analytify-cache' );
			$args_array               = array_merge( $args_array, $analytify_args_to_remove );
			return $args_array;
		}

		/**
		 * Trigger logging cleanup using the logging class.
		 *
		 * Uses guarded logger (null-safe) to avoid fatal if logger unavailable.
		 *
		 * @since 2.1.23
		 * @version 1
		 * @return void
		 */
		public static function analytify_cleanup_logs() {
			$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;
			if ( class_exists( 'QM' ) ) {
				QM::info( 'Analytify: Starting cleanup of expired logs.' );
			}

			if ( $logger && is_callable( array( $logger, 'clear_expired_logs' ) ) ) {
				$logger->clear_expired_logs();
				if ( class_exists( 'QM' ) ) {
					QM::info( 'Analytify: Expired logs cleared successfully.' );
				}
			}
		}




		// ========================================.
		// PROFILE & SETTINGS MANAGEMENT START.
		// ========================================.

		// Functions moved to Profile_Management && GA4_Property_Management components.

		// ========================================.
		// PROFILE & SETTINGS MANAGEMENT END.
		// ========================================.

		// Black Friday Deal Notice function moved to Promotions component.

		// Function moved to Promotions component.

		// Winter Sale promo function moved to Promotions component.

		// Winter Sale dismiss notice function moved to Promotions component.

		// Rating icon function moved to Promotions component.

		// ========================================.
		// GDPR COMPLIANCE & META BOXES START.
		// ========================================.

		// Functions add_exclusion_meta_box && print_exclusion_meta_box moved to GDPR_Compliance component.

		// ========================================.
		// GDPR COMPLIANCE & META BOXES END.
		// ========================================.

		/**
		 * Init the compliance class.
		 *
		 * @return void
		 */
		public function init_gdpr_compliance() {
			if ( class_exists( 'Class_Analytify_GDPR_Compliance' ) ) {
				new Class_Analytify_GDPR_Compliance();
			}
		}

		/**
		 * Get a component from the loader
		 *
		 * @param string $component_name The name of the component to get.
		 * @return mixed|null The component instance or null if not found
		 */
		public function get_component( $component_name ) {
			if ( $this->loader && method_exists( $this->loader, 'get_component' ) ) {
				return $this->loader->get_component( $component_name );
			}
			return null;
		}

		/**
		 * Check if a component is loaded
		 *
		 * @param string $component_name The name of the component to check.
		 * @return bool True if component is loaded, false otherwise
		 */
		public function has_component( $component_name ) {
			if ( $this->loader && method_exists( $this->loader, 'has_component' ) ) {
				return $this->loader->has_component( $component_name );
			}
			return false;
		}

		// Welcome message function moved to Promotions component.

		/**
		 * Show promo screen for addons
		 *
		 * @return void
		 */
		public function addons_promo_screen() {
			include WP_ANALYTIFY_PLUGIN_DIR . '/views/default/admin/addons-promo.php';
		}
	}
} // End if class_exists check.

// ========================================.
// MAIN PLUGIN CLASS DEFINITION END.
// ========================================.

/**
 * Create instance of wp-analytify class.
 *
 * @return void
 */
function analytify_free_instance() {
	$GLOBALS['WP_ANALYTIFY'] = WP_Analytify::get_instance();
}
add_action( 'plugins_loaded', 'analytify_free_instance', 10 );

// ========================================.
// PLUGIN INSTANCE INITIALIZATION END.
// ========================================.

/**
 * AJAX callback function for performing a factory reset of Analytify plugin settings
 *
 * This function handles the secure AJAX request to reset all Analytify settings
 * to their default values. It includes security checks, capability verification,
 * and proper error handling.
 *
 * @hook wp_ajax_analytify_factory_reset
 * @since 8.0.0
 * @return void Sends JSON response indicating success or failure
 */
function analytify_factory_reset_callback() {
	// Verify the nonce for security - prevents CSRF attacks.
	check_ajax_referer( 'analytify_factory_reset_nonce', 'nonce' );

	// Check if current user has administrative capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized' );
	}

	// Load the factory reset class if not already available.
	if ( ! class_exists( 'Analytify_Factory_Reset' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'classes/analytify-factory-reset.php';
	}

	// Initialize the reset class and execute the reset process.
	$reset = new Analytify_Factory_Reset();
	$reset->remove_settings();

	// Send success response to the AJAX request.
	wp_send_json_success();
}

/**
 * Hook the factory reset function to WordPress AJAX handlers
 *
 * This registers the function for both privileged (admin) and non-privileged users,
 * but the capability check inside the function will restrict access to admins only.
 */
add_action( 'wp_ajax_analytify_factory_reset', 'analytify_factory_reset_callback' );
