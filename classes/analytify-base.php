<?php

/**
 *
 */
class Analytify_Base{

	protected $settings;
	protected $plugin_file_path;
	protected $plugin_dir_path;
	protected $plugin_folder_name;
	protected $plugin_basename;
	protected $plugin_title;
	protected $plugin_slug;
	protected $core_slug;
	protected $template_dir;
	protected $is_addon = false;
	protected $is_pro = false;


	function __construct( $plugin_file_path ) {

		$this->load_settings();

		$this->plugin_file_path   = $plugin_file_path;
		$this->plugin_dir_path    = plugin_dir_path( $plugin_file_path );
		$this->plugin_folder_name = basename( $this->plugin_dir_path );
		$this->plugin_basename    = plugin_basename( $plugin_file_path );
		$this->template_dir       = $this->plugin_dir_path . 'template' . DIRECTORY_SEPARATOR;
		$this->plugin_title       = ucwords( str_ireplace( '-', ' ', basename( $plugin_file_path ) ) );
		$this->plugin_title       = str_ireplace( array( 'wp', 'analytify', 'pro', '.php' ), array( 'WP', 'ANALYTIFY', 'PRO', '' ), $this->plugin_title );

		$this->plugin_slug = basename( $plugin_file_path, '.php' );

		// used to add admin menus and to identify the core version
		$this->core_slug = ( $this->is_pro || $this->is_addon ) ? 'wp-analytify-pro' : 'wp-analytify';

		if ( is_multisite() ) {
			$this->plugin_base = 'settings.php?page=' . $this->core_slug;
		} else {
			$this->plugin_base = 'tools.php?page=' . $this->core_slug;
		}

		if ( $this->is_addon || $this->is_pro ) {
			$this->pro_addon_construct();
		}

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

	}


	function load_settings() {

		if ( ! is_null( $this->settings ) ) {
			return;
		}

		$update_settings = false;
		$this->settings  = get_site_option( 'wpanalytify_settings' );

		/*
         * Settings were previously stored and retrieved using get_option and update_option respectively.
         * Here we update the subsite option to a network wide option if applicable.
		 */
		if ( false === $this->settings && is_multisite() && is_network_admin() ) {
			$this->settings = get_option( 'wpanalytify_settings' );
			if ( false !== $this->settings ) {
				$update_settings = true;
				delete_option( 'wpanalytify_settings' );
			}
		}

		$default_settings = array(
			'profiles'                     	=> array(),
			'licence'                      	=> '',
			'analytify_posts_stats'	       	=> array( 'post','page' ),
			'post_analytics_disable_back'  	=> 1,
			'post_analytics_settings_back' 	=> array( 'show-overall-back' ),
			'post_analytics_access_back'	=> array( 'editor','administrator' ),
			'display_tracking_code'	       	=> array( 'administrator' ),
			'show_welcome_page'	           	=> 0,
		);

		// if we still don't have settings exist this must be a fresh install, set up some default settings
		if ( false === $this->settings ) {
			$this->settings  = $default_settings;
			$update_settings = true;
		} else {
			/*
             * When new settings are added an existing customer's db won't have the new settings.
             * They're added here to circumvent array index errors in debug mode.
			 */
			foreach ( $default_settings as $key => $value ) {
				if ( ! isset( $this->settings[ $key ] ) ) {
					$this->settings[ $key ] = $value;
					$update_settings        = true;
				}
			}
		}

		if ( $update_settings ) {
			update_site_option( 'wpanalytify_settings', $this->settings );
		}
	}


	/**
	 * Internationalization
	 *
	 * @access      public
	 * @since       2.0
	 * @return      void
	 */
	function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-analytify', false, dirname( plugin_basename( $this->plugin_file_path ) ) . '/languages/' );
	}


	function pro_addon_construct() {

	}
}


?>
