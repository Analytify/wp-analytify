<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure
/**
 * Analytify Logs Class
 *
 * This class handles logging functionality for the Analytify plugin,
 * including log file creation, log writing, and log management.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Analytify Logs Class
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class Analytify_Logs {


	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->run_setup();
		add_action( 'admin_menu', array( $this, 'log_page' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
	}


	/**
	 * Run setup process.
	 *
	 * @return void
	 */
	public function run_setup() {

		// Run setup only once.
		if ( get_option( 'analytify_logs_setup' ) ) {
			return;
		}

		add_action( 'init', array( $this, 'create_cron_jobs' ) );
		add_action( 'init', array( $this, 'create_files' ) );

		update_option( 'analytify_logs_setup', true );
	}

	/**
	 * Create cron jobs.
	 *
	 * @return void
	 */
	public function create_cron_jobs() {

		if ( ! wp_next_scheduled( 'analytify_cleanup_logs' ) ) {
			wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'daily', 'analytify_cleanup_logs' );
		}
	}


	/**
	 * Create log files.
	 *
	 * @return void
	 */
	public function create_files() {

		// Install files and folders for uploading files and prevent hotlinking.
		$upload_dir = wp_upload_dir();

		$files = array(

			array(
				'base'    => WP_ANALYTIFY_LOG_DIR,
				'file'    => '.htaccess',
				'content' => 'deny from all',
			),
			array(
				'base'    => WP_ANALYTIFY_LOG_DIR,
				'file'    => 'index.html',
				'content' => '',
			),
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_read_fopen,WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Direct file operations needed for log file creation
				if ( $file_handle ) {
					fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite,WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Direct file operations needed for log file creation
					fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose,WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Direct file operations needed for log file creation
				}
			}
		}
	}

	/**
	 * Add log page to admin menu.
	 *
	 * @return void
	 */
	public function log_page() {
		// Add submenu page for Analytify Logs.
		add_submenu_page( 'admin.php', __( 'Analytify Logs', 'wp-analytify' ), __( 'Analytify Logs', 'wp-analytify' ), 'manage_options', 'analytify-logs', array( $this, 'add_logs_page' ) );
	}

	/**
	 * Display logs page.
	 *
	 * @return void
	 */
	public function add_logs_page() {

		$logs       = self::scan_log_files();
		$viewed_log = '';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled by WordPress admin context
		if ( ! empty( $_REQUEST['log_file'] ) && isset( $logs[ sanitize_title( wp_unslash( $_REQUEST['log_file'] ) ) ] ) ) { // WPCS: input var ok, CSRF ok.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled by WordPress admin context
			$viewed_log = $logs[ sanitize_title( wp_unslash( $_REQUEST['log_file'] ) ) ]; // WPCS: input var ok, CSRF ok.
		} elseif ( ! empty( $logs ) ) {
			$viewed_log = current( $logs );
		}

		$handle = ! empty( $viewed_log ) ? self::get_log_file_handle( $viewed_log ) : '';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled by WordPress admin context
		if ( ! empty( $_REQUEST['handle'] ) ) { // WPCS: input var ok, CSRF ok.
			self::remove_log();
		}

		include WP_ANALYTIFY_PLUGIN_DIR . '/inc/analytify-logs.php';
	}

	/**
	 * Scan the log files.
	 *
	 * @return array<string, mixed>
	 */
	public static function scan_log_files(): array {
		// Check if class exists before calling.
		if ( class_exists( 'ANALYTIFY_Log_Handler_File' ) ) {
			return ANALYTIFY_Log_Handler_File::get_log_files();
		}
		return array();
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
	 * Remove/delete the chosen log file.
	 *
	 * Verifies nonce, delegates to the file log handler, then clears output buffers
	 * and redirects so the Delete log button works when admin buffering is active.
	 *
	 * @return void
	 * @since 1.0.0
	 * @version 9.0.0
	 */
	public static function remove_log() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to delete logs.', 'wp-analytify' ), '', array( 'response' => 403 ) );
		}
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'remove_log' ) ) { // WPCS: input var ok, sanitization ok.
			wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wp-analytify' ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above
		if ( ! empty( $_REQUEST['handle'] ) && class_exists( 'ANALYTIFY_Log_Handler_File' ) ) {
			$log_handler = new ANALYTIFY_Log_Handler_File();
			$log_handler->remove( sanitize_text_field( wp_unslash( $_REQUEST['handle'] ) ) ); // WPCS: input var ok, sanitization ok.
		}

		// Clear any output buffer so redirect is sent (fixes Delete log button when buffering is active).
		while ( ob_get_level() ) {
			ob_end_clean();
		}
		wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=analytify-logs' ) ) );
		exit();
	}


	/**
	 * Output buffering allows admin screens to make redirects later on.
	 *
	 * @return void
	 */
	public function buffer() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled by WordPress admin context
		if ( isset( $_GET['page'] ) && 'analytify-logs' === $_GET['page'] ) {
			ob_start();
		}
	}
}

if ( class_exists( 'Analytify_Logs' ) ) {
	new Analytify_Logs();
}
