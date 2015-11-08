<?php

if( ! defined('ABSPATH') ){
	// exit if accessed directly
	exit;
}

/*
 * Handling all the AJAX calls
 * @since 1.0.8
 * @class WPA_AJAX
 */
class WPA_AJAX {

	public static function init() {

		$ajax_calls = array(
			'rated'	=> false,
			'dismiss_pointer'	=> true
			);

		foreach ($ajax_calls as $ajax_call => $no_priv) {
			# code...
			add_action( 'wp_ajax_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );

			if ( $no_priv ) {
				add_action( 'wp_ajax_nopriv_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );
			}
		}
	}


	/**
	 * Triggered when clicking the rating footer.
	 * @since 1.0.8
	 */
	public static function rated() {

		update_option( 'analytify_admin_footer_text_rated', 1 );
		die();
	}

	/**
	 * Triggered when clicking the dismiss button.
	 * @since 1.0.8
	 */
	public static function dismiss_pointer() {

		$wpa_allow  = isset($_POST['wpa_allow']) ? $_POST['wpa_allow']: 0;

		if( $wpa_allow == 1 ) {
 
			update_option('wpa_allow_tracking', 1);
			send_status_analytify( get_option( 'admin_email' ), 'active');
		}
		
		update_option('show_tracking_pointer', 1);
		die();
	}

}

WPA_AJAX::init();