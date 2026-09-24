<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Module Manager Component for WP Analytify
 *
 * This file contains all module and addon management functions
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Module Manager Component Class
 */
class Analytify_Module_Manager {

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
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Module management hooks.
		add_action( 'admin_init', array( $this, 'modules_fallback_page' ) );
	}

	/**
	 * Fallback addons page if plugin is deactive
	 *
	 * @return void
	 */
	public function modules_fallback_page() {
		$wp_analytify_modules = get_option( 'wp-analytify-modules' );

		// Sanitize server data for security.
		if ( $wp_analytify_modules && $_SERVER ) {
			foreach ( $wp_analytify_modules as $module ) {
				if ( isset( $_SERVER['QUERY_STRING'] ) && 'page=' . $module['page_slug'] === $_SERVER['QUERY_STRING'] && 'active' !== $module['status'] ) {
					wp_safe_redirect( admin_url( '/admin.php?page=analytify-promo&addon=' . $module['slug'] ) );
					exit;
				}
			}
		}
	}

	/**
	 * Show promo screen for addons
	 *
	 * @return void
	 */
	public function addons_promo_screen() {
		include defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/default/admin/addons-promo.php';
	}
}
