<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Analytify_Logs {


	function __construct() {
		$this->run_setup();
    add_action( 'admin_menu', array( $this, 'log_page' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );

	}


	function run_setup() {

    // Run setup only once.
		if ( get_option( 'analytify_logs_setup' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'create_cron_jobs' ) );
		add_action( 'init', array( $this, 'create_files' ) );


		update_option( 'analytify_logs_setup', true );

	}

  /**
   * Setup Cron to clear the debug logs
   *
   */
	function create_cron_jobs() {

		if ( ! wp_next_scheduled( 'analytify_cleanup_logs' ) ) {
			wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'daily', 'analytify_cleanup_logs' );
		}

	}


  /**
   * Create directory for Logs.
   *
   */
	function create_files() {

		// Install files and folders for uploading files and prevent hotlinking.
		$upload_dir = wp_upload_dir();

		$files = array(

			array(
				'base'    => ANALYTIFY_LOG_DIR,
				'file'    => '.htaccess',
				'content' => 'deny from all',
			),
			array(
				'base'    => ANALYTIFY_LOG_DIR,
				'file'    => 'index.html',
				'content' => '',
			),
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
				if ( $file_handle ) {
					fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
					fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				}
			}
		}
	}

  function log_page() {
    // add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
    add_submenu_page( null, __( 'Analytify Logs', 'wp-analytify' ), __( 'Analytify Logs', 'wp-analytify' ), 'manage_options', 'analytify-logs', array( $this, 'add_logs_page' )  );

  }

  function add_logs_page() {

    $logs = self::scan_log_files();

    if ( ! empty( $_REQUEST['log_file'] ) && isset( $logs[ sanitize_title( wp_unslash( $_REQUEST['log_file'] ) ) ] ) ) { // WPCS: input var ok, CSRF ok.
      $viewed_log = $logs[ sanitize_title( wp_unslash( $_REQUEST['log_file'] ) ) ]; // WPCS: input var ok, CSRF ok.
    } elseif ( ! empty( $logs ) ) {
      $viewed_log = current( $logs );
    }

    $handle = ! empty( $viewed_log ) ? self::get_log_file_handle( $viewed_log ) : '';

    if ( ! empty( $_REQUEST['handle'] ) ) { // WPCS: input var ok, CSRF ok.
      self::remove_log();
    }

    include ANALYTIFY_PLUGIN_DIR . 'inc/analytify-logs.php';

  }

  /**
  * Scan the log files.
  *
  * @return array
  */
  public static function scan_log_files() {
    return ANALYTIFY_Log_Handler_File::get_log_files();
  }


  	/**
  	 * Return the log file handle.
  	 *
  	 * @param string $filename Filename to get the handle for.
  	 * @return string
  	 */
  	public static function get_log_file_handle( $filename ) {
  		return substr( $filename, 0, strlen( $filename ) > 48 ? strlen( $filename ) - 48 : strlen( $filename ) - 4 );
  	}

    /**
  	 * Remove/delete the chosen file.
  	 */
  	public static function remove_log() {
  		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'remove_log' ) ) { // WPCS: input var ok, sanitization ok.
  			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wp-analytify' ) );
  		}

  		if ( ! empty( $_REQUEST['handle'] ) ) {  // WPCS: input var ok.
  			$log_handler = new ANALYTIFY_Log_Handler_File();
  			$log_handler->remove( wp_unslash( $_REQUEST['handle'] ) ); // WPCS: input var ok, sanitization ok.
  		}

  		wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=analytify-logs' ) ) );
			exit();
  	}


		/**
		* Output buffering allows admin screens to make redirects later on.
		*/
		public function buffer() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'analytify-logs' ) {
				ob_start();
			}
		}
}

new Analytify_Logs();
