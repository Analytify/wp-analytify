<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure.
/**
 * Main AJAX Class for WP Analytify
 *
 * This file contains the main WPANALYTIFY_AJAX class that uses all the
 * AJAX traits for better organization and maintainability.
 *
 * @package WP_Analytify
 * @subpackage AJAX
 * @since 8.0.0
 */

/**
 * Handling all the AJAX calls in WP Analytify
 *
 * @since 1.2.4
 * @class WPANALYTIFY_AJAX
 */
class WPANALYTIFY_AJAX {

	// Use all the AJAX traits.
	use Analytify_AJAX_Dashboard;
	use Analytify_AJAX_Geographic;
	use Analytify_AJAX_Content;
	use Analytify_AJAX_System;
	use Analytify_AJAX_Utility;

	/**
	 * Show settings array.
	 *
	 * @var array<string, mixed>
	 */
	protected static $show_settings = array();

	/**
	 * Initialize AJAX handler.
	 *
	 * @return object
	 */
	public static function init() {
		$_analytify_dashboard = get_option( 'wp-analytify-dashboard' );
		if ( $_analytify_dashboard && array_key_exists( 'show_analytics_panels_dashboard', $_analytify_dashboard ) ) {
			self::$show_settings = $_analytify_dashboard['show_analytics_panels_dashboard'];
		}

		$ajax_calls = array(
			'rated'                      => false,
			'load_general_stats'         => false,
			'load_default_general_stats' => false,
			'load_top_pages'             => false,
			'load_default_top_pages'     => false,
			'load_country_stats'         => false,
			'load_city_stats'            => false,
			'load_keyword_stats'         => false,
			'load_social_stats'          => false,
			'load_page_exit_stats'       => false,
			'fetch_log'                  => false,
			'load_default_geographic'    => false,
			'load_default_system'        => false,
			'load_default_keyword'       => false,
			'load_default_social_media'  => false,
			'dismiss_pointer'            => true,
			'remove_comparison_gif'      => false,
			'deactivate'                 => false,
			'optin_yes'                  => false,
			'optout_yes'                 => false,
			'optin_skip'                 => false,
			'export_settings'            => false,
			'import_settings'            => false,
		);

		foreach ( $ajax_calls as $ajax_call => $no_priv ) {
			add_action( 'wp_ajax_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );

			if ( $no_priv ) {
				add_action( 'wp_ajax_nopriv_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );
			}
		}

		return new self();
	}
}
