<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName,Universal.Files.SeparateFunctionsFromOO.Mixed -- File naming is acceptable and mixed structure is acceptable for this type of file
/**
 * Analytify Email Core Class
 *
 * This file contains the main email functionality for the Analytify plugin,
 * including email sending, scheduling, and rendering capabilities.
 *
 * @package WP_Analytify
 * @since 1.0.0
 * @version 9.0.0
 */

ob_start();

// Include all email traits.
$plugin_dir = defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR : dirname( __DIR__ );
require_once $plugin_dir . '/classes/analytify-email/bootstrap.php';
require_once $plugin_dir . '/classes/analytify-email/settings.php';
// Since 9.0.0 — custom From/To bounds for monthly-style email reports (see `email-custom-range.php`).
require_once $plugin_dir . '/classes/analytify-email/email-custom-range.php';
require_once $plugin_dir . '/classes/analytify-email/scheduler.php';
require_once $plugin_dir . '/classes/analytify-email/render.php';
require_once $plugin_dir . '/classes/analytify-email/single-send.php';

/**
 * Analytify Email Core Class
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class Analytify_Email_Core {

	// Use all the traits.
	use Analytify_Email_Bootstrap;
	use Analytify_Email_Settings;
	use Analytify_Email_Scheduler;
	use Analytify_Email_Render;
	use Analytify_Email_Single_Send;

	// Constructor is now handled by the bootstrap trait.
	// All other methods are now handled by their respective traits.
}

/**
 * Sanitize users email addresses.
 *
 * @param string $str The email string to sanitize.
 * @return string
 *
 * @phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed
 */
function sanitize_multi_email( $str ) {

	if ( is_object( $str ) || is_array( $str ) ) {
		return '';
	}

	$str = (string) $str;

	$filtered = wp_check_invalid_utf8( $str );

	$keep_newlines = false; // Default value.
	$filtered      = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );

	$filtered = trim( $filtered ? $filtered : '' );

	$found = false;

	while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
		$filtered = str_replace( $match[0], '', $filtered );
		$found    = true;
	}

	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) ? preg_replace( '/ +/', ' ', $filtered ) : '' );
	}

	return $filtered;
}

/**
 * Init email reports.
 *
 * @since 3.1.0
 * @return null
 */
function init_analytify_email() {
	new Analytify_Email_Core();
	return null;
}

add_action(
	'init',
	function () {
		init_analytify_email();
	}
);

ob_end_flush();
