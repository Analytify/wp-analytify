<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure

/**
 * Base class for Analytify functionality.
 *
 * This class provides the foundation for all Analytify classes,
 * including common properties and methods for plugin management.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class Analytify_Base {

	/**
	 * Plugin settings.
	 *
	 * @var mixed
	 */
	protected $settings;
	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	protected $plugin_file_path;
	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	protected $plugin_dir_path;
	/**
	 * Plugin folder name.
	 *
	 * @var string
	 */
	protected $plugin_folder_name;
	/**
	 * Plugin basename.
	 *
	 * @var string
	 */
	protected $plugin_basename;
	/**
	 * Plugin title.
	 *
	 * @var string
	 */
	protected $plugin_title;
	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $plugin_slug;
	/**
	 * Core slug.
	 *
	 * @var string
	 */
	protected $core_slug;
	/**
	 * Template directory.
	 *
	 * @var string
	 */
	protected $template_dir;
	/**
	 * Whether this is an addon.
	 *
	 * @var bool
	 */
	protected $is_addon = false;
	/**
	 * Whether this is the pro version.
	 *
	 * @var bool
	 */
	protected $is_pro = false;

	/**
	 * Plugin base.
	 *
	 * @var string
	 */
	protected $plugin_base;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file_path The plugin file path.
	 */
	public function __construct( $plugin_file_path ) {

		$this->load_settings();

		$this->plugin_file_path   = $plugin_file_path;
		$this->plugin_dir_path    = plugin_dir_path( $plugin_file_path );
		$this->plugin_folder_name = basename( $this->plugin_dir_path );
		$this->plugin_basename    = plugin_basename( $plugin_file_path );
		$this->template_dir       = $this->plugin_dir_path . 'template' . DIRECTORY_SEPARATOR;
		$this->plugin_title       = ucwords( str_ireplace( '-', ' ', basename( $plugin_file_path ) ) );
		$this->plugin_title       = str_ireplace( array( 'wp', 'analytify', 'pro', '.php' ), array( 'WP', 'ANALYTIFY', 'PRO', '' ), $this->plugin_title );

		$this->plugin_slug = basename( $plugin_file_path, '.php' );

		// Used to add admin menus and to identify the core version.
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


	/**
	 * Load plugin settings.
	 *
	 * @return void
	 */
	public function load_settings() {

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
			'profiles'                     => array(),
			'licence'                      => '',
			'analytify_posts_stats'        => array( 'post', 'page' ),
			'post_analytics_disable_back'  => 1,
			'post_analytics_settings_back' => array( 'show-overall-back' ),
			'post_analytics_access_back'   => array( 'editor', 'administrator' ),
			'display_tracking_code'        => array( 'administrator' ),
			'show_welcome_page'            => 0,
		);

		// If we still don't have settings exist this must be a fresh install, set up some default settings.
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
	 * Load plugin textdomain.
	 *
	 * @access      public
	 * @since       2.0
	 * @return      void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-analytify', false, dirname( plugin_basename( $this->plugin_file_path ) ) . '/languages/' );
	}


	/**
	 * Pro addon constructor.
	 *
	 * @return void
	 */
	public function pro_addon_construct() {
	}
}
