<?php
/**
 * Core Admin Footer Functions for WP Analytify
 *
 * This file contains admin footer and rating related functions that were previously
 * in wpa-core-functions.php. Functions are kept as standalone functions for
 * simplicity and backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

/**
 * Change the admin footer text on Analytify admin pages
 *
 * @since  1.2.4
 * @param  string $footer_text Footer text.
 * @return string
 */
function wpa_admin_rate_footer_text( $footer_text ) {

	$rate_text      = '';
	$current_screen = get_current_screen();

	// Add the Analytify admin pages.
	$wpa_pages[] = 'toplevel_page_analytify-dashboard';
	$wpa_pages[] = 'analytify_page_analytify-campaigns';
	$wpa_pages[] = 'analytify_page_analytify-settings';

	// Check to make sure we're on a Analytify admin pages.
	if ( isset( $current_screen->id ) && in_array( $current_screen->id, $wpa_pages, true ) ) {
		// Change the footer text.
		if ( ! get_option( 'analytify_admin_footer_text_rated' ) ) {
				$rate_text = sprintf(
							// translators: Analytify rating.
					esc_html__( 'If you like %1$s Analytify %2$s please leave us a %5$s %3$s %6$s rating. %4$s A huge thank you from %1$s WPBrigade %2$s in advance!', 'wp-analytify' ),
					'<strong>',
					'</strong>',
					'&#9733;&#9733;&#9733;&#9733;&#9733;',
					'<br />',
					'<a href="https://analytify.io/go/rate-analytify" target="_blank" class="wpa-rating-footer" data-rated="Thanks dude ;)">',
					'</a>'
				);
				wp_analytify_enqueue_js(
					"
                        jQuery('a.wpa-rating-footer').on('click', function() {
                            jQuery.post( '" . admin_url( 'admin-ajax.php' ) . "', { action: 'analytify_rated' } );
                            jQuery(this).parent().text( jQuery(this).data( 'rated' ) );
                        });
                    "
				);
		} else {
			$rate_text = esc_html_e( 'Thank you for tracking with Analytify.', 'wp-analytify' );
		}

		return $rate_text;
	}

	return $footer_text;
}
