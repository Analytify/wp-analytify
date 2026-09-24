<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Email Bootstrap Trait
 *
 * This trait contains the core initialization, constructor, hooks, and constants
 * for the Analytify Email functionality. It was created to separate the bootstrap
 * logic from other email operations, keeping the code organized and maintainable.
 *
 * PURPOSE:
 * - Handles class initialization and constructor logic
 * - Sets up WordPress hooks and actions
 * - Manages plugin constants and core setup
 * - Provides backward compatibility checks
 *
 * @package WP_Analytify
 * @subpackage Email
 * @since 8.0.0
 */

trait Analytify_Email_Bootstrap {

	/**
	 * Main Analytify object.
	 *
	 * @var mixed
	 */
	private $WP_ANALYTIFY = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase -- Variable name is acceptable for this context

	/**
	 * Constructor - Initialize the email functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! $this->verify_update() ) {
			return;
		}

		$this->WP_ANALYTIFY = $GLOBALS['WP_ANALYTIFY']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Variable name is acceptable for this context

		$this->setup_constants();
		$this->anaytify_email_check_time();
		$this->hooks();

		if ( isset( $_POST['test_email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification is handled in the callback function
			$this->callback_on_cron_time();
			add_action( 'admin_notices', array( $this, 'analytify_email_notics' ) );
		}
	}

	/**
	 * Setup WordPress hooks and actions.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'analytify_email_scripts' ) );
		add_action( 'analytify_email_cron_function', array( $this, 'callback_on_cron_time' ) );
		add_filter( 'wp_analytify_pro_setting_tabs', array( $this, 'analytify_email_setting_tabs' ), 20, 1 );
		add_filter( 'wp_analytify_pro_setting_fields', array( $this, 'analytifye_email_setting_fields' ), 20, 1 );
		add_action( 'after_single_view_stats_buttons', array( $this, 'single_send_email' ) );
		add_action( 'wp_ajax_send_analytics_email', array( $this, 'send_analytics_email' ) );
		add_action( 'wp_ajax_nopriv_analytify_send_single_email_background', array( $this, 'send_analytics_email_background' ) );
		add_action( 'analytify_settings_logs', array( $this, 'analytify_settings_logs' ) );
	}

	/**
	 * Enqueue scripts for email functionality.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function analytify_email_scripts() {
		wp_enqueue_script( 'analytify_email_script', ( defined( 'ANALYTIFY_PLUGIN_URL' ) ? ANALYTIFY_PLUGIN_URL : '' ) . 'assets/js/wp-analytify-email' . analytify_get_asset_suffix() . '.js', array( 'jquery' ), defined( 'ANALYTIFY_VERSION' ) ? ANALYTIFY_VERSION : '1.0.0', true );

		// Localize nonce data for email script.
		// Get existing nonces if wpanalytify_data was already localized to wp-analytify-script-js.
		$nonces = apply_filters(
			'wpanalytify_nonces',
			array(
				'send_single_post_email' => wp_create_nonce( 'analytify-single-post-email' ),
				'activate_license'       => wp_create_nonce( 'activate-license' ),
			)
		);

		// Localize wpanalytify_data to email script to ensure nonces are available.
		// This will merge with existing wpanalytify_data if it was already defined by wp-analytify-script-js.
		wp_localize_script(
			'analytify_email_script',
			'wpanalytify_data',
			array(
				'nonces' => $nonces,
			)
		);

		// Localized strings for email UI (translation / _x). Always localized when this script is enqueued.
		wp_localize_script(
			'analytify_email_script',
			'wpAnalytifyEmail',
			array(
				'sendEmailReport'      => _x( 'Send Email Report', 'button label', 'wp-analytify' ),
				'sending'              => _x( 'Sending...', 'button state while sending', 'wp-analytify' ),
				'placeholderRecipient' => _x( 'Enter recipient email', 'placeholder for recipient email input', 'wp-analytify' ),
				'emailReportSent'      => _x( 'Email Report Sent!', 'success message after sending single post email', 'wp-analytify' ),
				'emailSendFailed'      => _x( 'Failed to send email. Please try again.', 'error when single post email request fails', 'wp-analytify' ),
			)
		);
	}

	/**
	 * Email submenu placeholder function.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function analytify_email_setting() {
		// Placeholder for email settings.
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function setup_constants() {
		// Setting Global Values.
		$this->define( 'ANALYTIFY_IMAGES_PATH', 'https://analytify.io/assets/email/' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name Constant name.
	 * @param mixed  $value Constant value.
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Check and schedule email cron events.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function anaytify_email_check_time() {
		// Check if event is scheduled before.
		if ( ! wp_next_scheduled( 'analytify_email_cron_function' ) ) {
			wp_schedule_event( time(), 'daily', 'analytify_email_cron_function' );
		}
	}

	/**
	 * Verify email addon.
	 *
	 * Check if email addon is already present and is prior to latest split functionality version.
	 *
	 * @return bool
	 */
	public function verify_update() {
		if ( defined( 'ANALTYIFY_EMAIL_VERSION' ) && '1.2.8' >= ANALTYIFY_EMAIL_VERSION ) {
			return false;
		}

		return true;
	}
}
