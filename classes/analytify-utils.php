<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure
/**
 * Analytify Utils Class
 *
 * This class provides utility functions for the Analytify plugin,
 * including core utilities, UI helpers, GA functions, and notices.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// Include split traits.
require_once __DIR__ . '/analytify-utils/analytify-utils-core.php';
require_once __DIR__ . '/analytify-utils/analytify-utils-ui.php';
require_once __DIR__ . '/analytify-utils/analytify-utils-ga.php';
require_once __DIR__ . '/analytify-utils/analytify-utils-notices.php';
require_once __DIR__ . '/class-analytify-admin-assets.php';

/**
 * Analytify Utils Class
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class WPANALYTIFY_Utils {

	use Analytify_Utils_Core;
	use Analytify_Utils_UI;
	use Analytify_Utils_GA;
	use Analytify_Utils_Notices;

	/**
	 * Check if tracking script is allowed on current page.
	 *
	 * @return boolean
	 */
	public static function skip_page_tracking() {

		if ( ! is_singular() ) {
			return false;
		}

		global $post;

		if ( ! is_object( $post ) ) {
			return false;
		}

		return (bool) get_post_meta( isset( $post->ID ) ? $post->ID : 0, '_analytify_skip_tracking', true );
	}

	/**
	 * Check if pro version is active.
	 *
	 * @return bool
	 */
	public static function is_active_pro() {

		return ( is_plugin_active( 'wp-analytify-pro/wp-analytify-pro.php' ) ) ? true : false;
	}

	/**
	 * Returns all the modules available in pro.
	 *
	 * @return array<string, mixed> Pro modules.
	 * @since 5.1.1
	 */
	public static function get_pro_modules() {
		return get_option( 'wp_analytify_modules' ); }

	/**
	 * Check if a module is active via options.
	 *
	 * @param string $slug Slug of the module.
	 * @return boolean
	 */
	public static function is_module_active( $slug ) {

		$wp_analytify_modules = get_option( 'wp_analytify_modules' );

		return ( $wp_analytify_modules && isset( $wp_analytify_modules[ $slug ] ) && isset( $wp_analytify_modules[ $slug ]['status'] ) && 'active' === $wp_analytify_modules[ $slug ]['status'] ) ? true : false;
	}

	/**
	 * Get the last element of array.
	 *
	 * @param array<string, mixed> $array The array to get the last element from.
	 * @return mixed
	 * @since 2.1.12
	 */
	public static function end( $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound -- Array parameter name is acceptable for this context
		return end( $array );
	}
}

// Admin assets deconfliction moved to a dedicated small file to keep this class lean.
