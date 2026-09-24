<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Plugin Lifecycle Component for WP Analytify
 *
 * This file contains all plugin activation, deactivation, and uninstall functions
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Lifecycle Component Class
 */
class Analytify_Plugin_Lifecycle {

	/**
	 * Main plugin instance
	 *
	 * @var WP_Analytify
	 */
	private $analytify;

	/**
	 * Constructor
	 *
	 * @param WP_Analytify $analytify Main plugin instance.
	 */
	public function __construct( $analytify ) {
		$this->analytify = $analytify;
	}

	/**
	 * Run on plugin activation.
	 *
	 * @since       1.2.2
	 * @return      void
	 */
	public function activate() {

		// update version.
		if ( ! get_option( 'pa_google_token' ) ) {
			update_option( 'wpa_current_version', '2.1.2' );
		}

		// Return if settings already added in DB.
		$_admin_settings = get_option( 'wp-analytify-admin' );
		if ( $_admin_settings && 'on' === $_admin_settings['enable_back_end'] && ! empty( $_admin_settings['show_analytics_roles_back_end'] ) ) {
			return;
		}

		// Load default settings on new install.
		if ( ! get_option( 'analytify_default_settings' ) ) {
			$profile_tab_settings = array(
				'exclude_users_tracking' => array( 'administrator' ),
			);

			update_option( 'wp-analytify-profile', $profile_tab_settings );

			$admin_tab_settings = array(
				'enable_back_end'                    => 'on',
				'show_analytics_roles_back_end'      => array( 'administrator', 'editor' ),
				'show_analytics_post_types_back_end' => array( 'post', 'page' ),
				'show_panels_back_end'               => array( 'show-overall-dashboard', 'show-social-dashboard', 'show-geographic-dashboard', 'show-system-stats', 'show-keywords-dashboard', 'show-referrer-dashboard' ),
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

			$dashboard_tab_settings['show_analytics_roles_dashboard'] = array( 'administrator' );

			update_option( 'wp-analytify-dashboard', $dashboard_tab_settings );

			$advanced_tab_settings = array(
				'gtag_tracking_mode'       => 'gtag',
				'google_analytics_version' => 'ga4',
			);

			update_option( 'wp-analytify-advanced', $advanced_tab_settings );

			// Update meta so default settings load only one time.
			update_option( 'analytify_default_settings', 'done' );

			update_option( 'analytify_active_date', gmdate( 'l jS F Y h:i:s A' ) . date_default_timezone_get() );
		}
	}

	/**
	 * Delete option values on plugin deactivation.
	 *
	 * @since       1.2.2
	 * @return      void
	 */
	public function deactivate() {
		// Delete welcome page check on de-activate.
		delete_option( 'show_welcome_page' );
	}

	/**
	 * Delete plugin settings meta on deleting the plugin
	 *
	 * @return void
	 */
	public function uninstall() {

		$plugin_dir = defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR : dirname( __DIR__ );
		require_once $plugin_dir . '/classes/analytify-utils.php';

		$remove_settings_on_uninstall = WPANALYTIFY_Utils::get_option( 'uninstall_analytify_settings', 'wp-analytify-advanced', false );

		if ( $remove_settings_on_uninstall && 'on' === $remove_settings_on_uninstall ) {

			require_once $plugin_dir . '/classes/analytify-factory-reset.php';

			// Remove all the settings on uninstall.
			( new Analytify_Factory_Reset() )->remove_settings();
		}
	}

	/**
	 * Send status of subscriber who opt-in for improving the product.
	 *
	 * @param string $email Users email.
	 * @param string $status Plugin status.
	 * @return void
	 */
	public function send_status( $email, $status ) {
		$url = 'https://analytify.io/plugin-manager/';

		if ( '' === $email ) {
			$email = 'track@analytify.io';
		}

		$fields = array(
			'email'  => $email,
			'site'   => get_site_url(),
			'status' => $status,
			'type'   => 'FREE',
		);

		wp_remote_post(
			$url,
			array(
				'method'      => 'POST',
				'timeout'     => 5,
				'httpversion' => '1.0',
				'blocking'    => false,
				'headers'     => array(),
				'body'        => $fields,
			)
		);
	}
}
