<?php
/**
 * Analytify Loader
 *
 * Loads all modular components and initializes them
 *
 * @package WP_ANALYTIFY
 * @since 8.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Analytify Loader Class
 *
 * Responsible for loading and initializing all modular components
 */
class Analytify_Loader {

	/**
	 * Main plugin instance
	 *
	 * @var WP_Analytify
	 */
	private $analytify;

	/**
	 * Component instances
	 *
	 * @var array<string, mixed>
	 */
	private $components = array();

	/**
	 * Constructor
	 *
	 * @version 7.0.5
	 * @param WP_Analytify $analytify Main plugin instance.
	 */
	public function __construct( $analytify ) {
		$this->analytify = $analytify;
		$this->load_components();
		$this->init_components();
	}

	/**
	 * Load all component files
	 *
	 * @version 7.0.5
	 * @return void
	 */
	private function load_components() {
		$component_files = array(
			'admin-notices',
			'analytics-accounts',
			'analytics-reports',
			'profile-management',
			'utils',
			'gdpr-compliance',
			'module-manager',
			'scripts-styles',
			'promotions',
			'analytify-admin-ui',
			'ga4-property-management',
			'plugin-lifecycle',
			'class-analytify-license-sticky-notice',
		);

		foreach ( $component_files as $component ) {
			$file_path = ( defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR : dirname( __DIR__ ) ) . '/inc/' . $component . '.php';
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
			}
		}
	}

	/**
	 * Initialize all components
	 *
	 * @version 7.0.5
	 * @return void
	 */
	private function init_components() {
		// Initialize components that need the main plugin instance.
		$this->components['admin_notices']           = new Analytify_Admin_Notices( $this->analytify );
		$this->components['analytics_accounts']      = new Analytify_Analytics_Accounts( $this->analytify );
		$this->components['analytics_reports']       = new Analytify_Analytics_Reports( $this->analytify );
		$this->components['profile_management']      = new Analytify_Profile_Management( $this->analytify );
		$this->components['gdpr_compliance']         = new Analytify_GDPR_Compliance( $this->analytify );
		$this->components['module_manager']          = new Analytify_Module_Manager( $this->analytify );
		$this->components['scripts_styles']          = new Analytify_Scripts_Styles( $this->analytify );
		$this->components['promotions']              = new Analytify_Promotions( $this->analytify );
		$this->components['ga4_property_management'] = new Analytify_GA4_Property_Management( $this->analytify );
		$this->components['plugin_lifecycle']        = new Analytify_Plugin_Lifecycle( $this->analytify );

		if ( class_exists( 'Analytify_License_Sticky_Notice', false ) ) {
			$this->components['license_sticky_notice'] = new Analytify_License_Sticky_Notice();
			$this->components['license_sticky_notice']->init();
		}

		// Set up component hooks - register admin notices and scripts immediately.
		$this->setup_admin_notice_hooks();
		$this->setup_scripts_hooks();
		add_action( 'init', array( $this, 'setup_other_component_hooks' ), 20 );

		do_action( 'analytify_components_loaded', $this->components );
	}

	/**
	 * Set up admin notice hooks immediately
	 *
	 * @version 8.1.1
	 * @return void
	 */
	private function setup_admin_notice_hooks() {
		// Admin notices hooks - register immediately.
		if ( isset( $this->components['admin_notices'] ) ) {
			$admin_notices = $this->components['admin_notices'];

			// Add admin notice hooks with proper priority.
			add_action( 'admin_notices', array( $admin_notices, 'pro_update_notice' ), 10 );
			add_action( 'admin_notices', array( $admin_notices, 'analytify_admin_notice' ), 10 );
			add_action( 'admin_notices', array( $admin_notices, 'addons_ga4_update_notice' ), 10 );
			add_action( 'admin_notices', array( $admin_notices, 'analytify_measurement_protocol_secret_notice' ), 10 );
			add_action( 'admin_notices', array( $admin_notices, 'analytify_cache_clear_notice' ), 10 );

			// AJAX handlers.
			add_action( 'wp_ajax_analytify_dismiss_rank_math_notice', array( $admin_notices, 'analytify_dismiss_rank_math_notice' ) );
		}
	}

	/**
	 * Set up scripts and styles hooks immediately (CRITICAL - must not be delayed)
	 *
	 * @version 7.0.5
	 * @return void
	 */
	private function setup_scripts_hooks() {
		// Scripts & Styles hooks - MUST be registered immediately to prevent layout destruction.
		if ( isset( $this->components['scripts_styles'] ) ) {
			$scripts_styles = $this->components['scripts_styles'];

			// Admin scripts and styles - CRITICAL for plugin layout.
			add_action( 'admin_enqueue_scripts', array( $scripts_styles, 'admin_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $scripts_styles, 'admin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $scripts_styles, 'front_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $scripts_styles, 'front_scripts' ) );
			add_action( 'admin_head', array( $scripts_styles, 'add_dashboard_inline_styles' ) );
			add_action( 'admin_head', array( $scripts_styles, 'add_dashboard_inline_scripts' ) );
		}
	}

	/**
	 * Set up other component hooks after WordPress is loaded
	 *
	 * @version 7.0.5
	 * @return void
	 */
	public function setup_other_component_hooks() {
		// Promotions hooks.
		if ( isset( $this->components['promotions'] ) ) {
			$promotions = $this->components['promotions'];

			// Promotional notices and messages.
			add_action( 'admin_init', array( $promotions, 'analytify_review_notice' ) );
			add_action( 'admin_init', array( $promotions, 'analytify_buy_pro_notice' ) );
			add_action( 'admin_init', array( $promotions, 'dismiss_notices' ) );
			add_action( 'admin_notices', array( $promotions, 'gtag_move_to_notice' ) );
			add_action( 'admin_notices', array( $promotions, 'profile_warning' ) );
			add_filter( 'plugin_row_meta', array( $promotions, 'add_rating_icon' ), 10, 2 );
		}

		// Analytics reports hooks.
		if ( isset( $this->components['analytics_reports'] ) ) {
			$analytics_reports = $this->components['analytics_reports'];

			// Add meta boxes for single post analytics.
			add_action( 'add_meta_boxes', array( $analytics_reports, 'show_admin_single_analytics_add_metabox' ) );

			// AJAX handlers.
			add_action( 'wp_ajax_get_ajax_single_admin_analytics', array( $analytics_reports, 'get_ajax_single_admin_analytics' ) );
			add_action( 'wp_ajax_set_module_state', array( $analytics_reports, 'set_module_state' ) );

			// Post row stats.
			add_filter( 'post_row_actions', array( $analytics_reports, 'post_rows_stats' ), 10, 2 );
			add_filter( 'page_row_actions', array( $analytics_reports, 'post_rows_stats' ), 10, 2 );
			add_action( 'post_submitbox_minor_actions', array( $analytics_reports, 'post_submitbox_stats_action' ), 10, 1 );
		}

		// Profile management hooks.
		if ( isset( $this->components['profile_management'] ) ) {
			$profile_management = $this->components['profile_management'];

			// Profile management hooks.
			add_action( 'update_option_wp-analytify-profile', array( $profile_management, 'update_profiles_list_summary' ), 10, 2 );
			add_action( 'update_option_wp-analytify-advanced', array( $profile_management, 'update_selected_profiles' ), 10, 2 );
			add_action( 'admin_init', array( $profile_management, 'update_profile_list_summary_on_update' ), 1 );
		}

		// GDPR: {@see Analytify_GDPR_Compliance} registers init + add_meta_boxes in its constructor.

		// Module manager hooks.
		if ( isset( $this->components['module_manager'] ) ) {
			$module_manager = $this->components['module_manager'];

			// Module management hooks.
			add_action( 'admin_init', array( $module_manager, 'modules_fallback_page' ) );
		}
	}

	/**
	 * Get a specific component instance
	 *
	 * @version 7.0.5
	 * @param string $component_name Component name.
	 * @return object|null Component instance or null if not found
	 */
	public function get_component( $component_name ) {
		return isset( $this->components[ $component_name ] ) ? $this->components[ $component_name ] : null;
	}

	/**
	 * Get all component instances
	 *
	 * @version 7.0.5
	 * @return array<string, mixed> All component instances.
	 */
	public function get_all_components() {
		return $this->components;
	}

	/**
	 * Check if a component is loaded
	 *
	 * @version 7.0.5
	 * @param string $component_name Component name.
	 * @return bool Whether component is loaded.
	 */
	public function is_component_loaded( $component_name ) {
		return isset( $this->components[ $component_name ] );
	}

	/**
	 * Check if a component is loaded (alias for is_component_loaded)
	 *
	 * @version 7.0.5
	 * @param string $component_name Component name.
	 * @return bool Whether component is loaded.
	 */
	public function has_component( $component_name ) {
		return $this->is_component_loaded( $component_name );
	}
}
