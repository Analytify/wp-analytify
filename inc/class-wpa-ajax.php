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

	public static function init(){

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
	 * Triggered when clicking the rating footer.
	 * @since 1.0.8
	 */
	public static function dismiss_pointer() {

		$email = $_POST['email'];
		$name = $_POST['name'];

		//print_r(send_status_analytify( $email, $status));

		die();
	}

}

WPA_AJAX::init();