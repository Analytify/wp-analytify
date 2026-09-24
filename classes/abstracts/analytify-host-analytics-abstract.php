<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure
/**
 * Analytify Host Analytics Abstract Class
 *
 * This abstract class provides the base functionality for hosting analytics files
 * locally, handling different tracking modes and file management.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Analytify_Host_Analytics_Abstract' ) ) {
	/**
	 * This class will be used as base class that
	 * will do all the heavy lifting for hosting analytics files.
	 * based on the type of tracking mode passed.
	 */
	abstract class Analytify_Host_Analytics_Abstract {

		/**
		 * Remote File URL.
		 *
		 * @var string
		 */
		public $remote_file_url;

		/**
		 * Tracking ID.
		 *
		 * @var string
		 */
		public $tracking_id;

		/**
		 * Tracking mode.
		 *
		 * @var string
		 */
		public $tracking_mode;

		/**
		 * Constructor - sets up the analytics hosting.
		 *
		 * Set's the remote file url, creates Analytify cache directory
		 * and call the function to download gtag library from google servers.
		 *
		 * @since 5.0.6
		 */
		public function __construct() {

			$this->set_all_values();

			$this->create_dir_rec();

			$this->download_file();
		}

		/**
		 * Builds the remote gtag.js download URL. Called from the constructor after tracking_id is set.
		 *
		 * @return void
		 * @since 5.0.6
		 */
		public function set_all_values() {
			if ( empty( $this->tracking_id ) ) {
				$this->remote_file_url = '';
				return;
			}

			$this->remote_file_url = Analytify_Host_Analytics::GTAG_URL . '/gtag/js?id=' . rawurlencode( $this->tracking_id ) . '&l=dataLayer&dl=1';
		}

		/**
		 * Check if Analytify cache directory exists and create it if not.
		 *
		 * @return void
		 * @since 5.0.6
		 */
		public function create_dir_rec() {

			if ( ! file_exists( defined( 'WP_ANALYTIFY_LOCAL_DIR' ) ? WP_ANALYTIFY_LOCAL_DIR : '' ) ) {

				wp_mkdir_p( defined( 'WP_ANALYTIFY_LOCAL_DIR' ) ? WP_ANALYTIFY_LOCAL_DIR : '' );

			}
		}

		/**
		 * Download the gtag library and generate a random alias for the file.
		 *
		 * This function is responsible for downloading the gtag library
		 * and deleting the existing locally hosted file.
		 * It generates a new random alias for newly downloaded file and
		 * assigns the alias to that file.
		 *
		 * @return void
		 * @since 5.0.6
		 */
		public function download_file() {

			if ( empty( $this->remote_file_url ) ) {
				return;
			}

			$response = wp_remote_get( $this->remote_file_url );
			$logger   = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;

			if ( class_exists( 'QM' ) ) {
				QM::info( 'Analytify: Downloading analytics file from remote URL.' );
			}

			if ( is_wp_error( $response ) ) {

				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning( sprintf( 'Error occured while downloading analytics file: %1$s - %2$s', $response->get_error_code(), $response->get_error_message() ), array( 'source' => 'analytify_analytics_file_errors' ) );
				}
				if ( class_exists( 'QM' ) ) {
					QM::warning( 'Analytify: Error occured while downloading analytics file: ' . $response->get_error_code() . ' - ' . $response->get_error_message(), array( 'source' => 'analytify_analytics_file_errors' ) );
				}

				return;

			}

			$body = wp_remote_retrieve_body( $response );
			if ( '' === $body ) {
				return;
			}

			$local_dir = defined( 'WP_ANALYTIFY_LOCAL_DIR' ) ? WP_ANALYTIFY_LOCAL_DIR : '';

			$old_alias = $this->get_file_alias();
			$deleted   = true;
			if ( is_string( $old_alias ) && '' !== $old_alias ) {
				$old_path = $local_dir . $old_alias;
				if ( file_exists( $old_path ) && is_file( $old_path ) ) {
					wp_delete_file( $old_path );
					$deleted = ! file_exists( $old_path );
				}
			}

			if ( ! $deleted ) {
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning( 'File could not be deleted due to some error', array( 'source' => 'analytify_analytics_file_errors' ) );
				}
				if ( class_exists( 'QM' ) ) {
					QM::warning( 'Analytify: File could not be deleted due to some error', array( 'source' => 'analytify_analytics_file_errors' ) );
				}
			}

			// Random basename with .min.js to reflect the minified gtag bundle from Google.
			$file_alias = bin2hex( random_bytes( 4 ) ) . '.min.js';

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Direct file operations are acceptable for analytics file hosting
			$write = file_put_contents( $local_dir . $file_alias, $body );

			if ( false === $write ) {
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning( 'File could not be saved due to some error', array( 'source' => 'analytify_analytics_file_errors' ) );
				}
				if ( class_exists( 'QM' ) ) {
					QM::warning( 'Analytify: File could not be saved due to some error', array( 'source' => 'analytify_analytics_file_errors' ) );
				}
				return;
			}

			$this->set_file_alias( $this->tracking_mode, $file_alias );
		}

		/**
		 * Return the current locally hosted gtag filename alias, if any.
		 *
		 * @return string
		 */
		abstract public function get_file_alias();

		/**
		 * Persist the new filename alias for the tracking mode.
		 *
		 * @param string $tracking_mode Tracking mode key.
		 * @param string $file_alias    Generated basename (e.g. abcd1234.min.js).
		 * @return mixed                Implementation may return bool (e.g. from update_option).
		 */
		abstract public function set_file_alias( $tracking_mode, $file_alias );
	}
}
