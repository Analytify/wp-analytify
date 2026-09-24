<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure
/**
 * Analytify Host Analytics Class
 *
 * This class handles locally hosted analytics files for the Analytify plugin,
 * providing functionality to serve Google Analytics files from the local server.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Analytify_Host_Analytics' ) ) {
	/**
	 * Resolves the locally hosted gtag bundle URL and coordinates download, alias storage, and cleanup.
	 */
	class Analytify_Host_Analytics extends Analytify_Host_Analytics_Abstract {


		/**
		 * Gtag file alias.
		 *
		 * @var array<string, mixed>|false
		 */
		public $file_aliases;

		/**
		 * Host Analytics Locally option Off or On.
		 *
		 * @var mixed
		 */
		private $host_analytics_locally;



		const GTAG_URL = 'https://www.googletagmanager.com';

		/**
		 * Define class variables and calls parent class constructor.
		 *
		 * @param string  $tracking_mode Google analytics tracking mode.
		 * @param boolean $doing_cron If the constructor is called by cron hook.
		 * @param boolean $settings_updated If the tracking id in settings is changed.
		 */
		public function __construct( $tracking_mode = 'gtag', $doing_cron = true, $settings_updated = false ) {

			$this->tracking_mode = $tracking_mode;

			$this->file_aliases = get_option( 'analytics_file_aliases' );

			// TODO: Need to change the function name its GA4 but title of function is UA.
			$this->tracking_id = WP_ANALYTIFY_FUNCTIONS::get_UA_code();

			$this->host_analytics_locally = WPANALYTIFY_Utils::get_option( 'locally_host_analytics', 'wp-analytify-advanced', false );

			$this->host_analytics_locally = false === $this->host_analytics_locally || 'off' === $this->host_analytics_locally ? false : $this->host_analytics_locally;

			if ( ! $this->host_analytics_locally && $this->file_already_exist() ) {

				$dir             = WP_ANALYTIFY_LOCAL_DIR;
				$paths_to_remove = array( $dir . 'gtag.js', $dir . 'gtag.min.js' );
				$alias           = $this->get_file_alias();
				if ( $alias ) {
					array_unshift( $paths_to_remove, $dir . $alias );
				}

				foreach ( array_unique( $paths_to_remove ) as $path ) {
					if ( file_exists( $path ) && is_file( $path ) ) {
						wp_delete_file( $path );
					}
				}

				return;
			}

			if ( ! $this->host_analytics_locally || ( ! $settings_updated && $this->file_already_exist() && ! $doing_cron ) ) {
				return;
			}

			parent::__construct();
		}

		/**
		 * Check if the local gtag library file exists.
		 *
		 * @return boolean
		 */
		public function file_already_exist() {

			if ( file_exists( WP_ANALYTIFY_LOCAL_DIR . 'gtag.js' ) || file_exists( WP_ANALYTIFY_LOCAL_DIR . 'gtag.min.js' ) || file_exists( WP_ANALYTIFY_LOCAL_DIR . $this->get_file_alias() ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if the local gtag library file exists.
		 *
		 * @return mixed
		 */
		public function local_analytics_file_url() {

			if ( ! $this->host_analytics_locally || ! $this->file_already_exist() ) {
				return null;
			}

			$url = content_url() . str_replace( WP_CONTENT_DIR, '', WP_ANALYTIFY_LOCAL_DIR ) . 'gtag.min.js';

			if ( strpos( home_url(), 'https://' ) !== false && ! is_ssl() ) {
				$url = str_replace( 'http://', 'https://', $url );
			}

			$file_alias = self::get_file_alias();

			if ( ! $file_alias ) {
				return $url;
			}

			$url = str_replace( 'gtag.min.js', $file_alias, $url );

			return $url;
		}

		/**
		 * This functions check and return file alias for gtag library.
		 *
		 * @return mixed
		 */
		public function get_file_alias() {
			if ( ! $this->file_aliases ) {
				return '';
			}

			return $this->file_aliases['gtag'] ?? '';
		}

		/**
		 * This function update file alias for gtag in database options.
		 *
		 * @param string $key this is the analytics tracking mode gtag.
		 * @param string $alias this is the randomly generated alias for the file.
		 *
		 * @return boolean
		 */
		public function set_file_alias( $key, $alias ) {
			if ( false === $this->file_aliases ) {
				$this->file_aliases = array();
			}
			$this->file_aliases[ $key ] = $alias;
			return update_option( 'analytics_file_aliases', $this->file_aliases );
		}
	}
}
