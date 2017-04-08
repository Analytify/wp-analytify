<?php

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function wpa_enqueue_js( $code ) {
	global $wpa_queued_js;

	if ( empty( $wpa_queued_js ) ) {
		$wpa_queued_js = '';
	}

	$wpa_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function wpa_print_js() {
	global $wpa_queued_js;

	if ( ! empty( $wpa_queued_js ) ) {

		echo "<!-- Analytify footer JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize
		$wpa_queued_js = wp_check_invalid_utf8( $wpa_queued_js );
		$wpa_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpa_queued_js );
		$wpa_queued_js = str_replace( "\r", '', $wpa_queued_js );

		echo $wpa_queued_js . "});\n</script>\n";

		unset( $wpa_queued_js );
	}
}

/**
 * Change the admin footer text on Analytify admin pages
 *
 * @since  1.2.4
 * @param  string $footer_text
 * @return string
 */
function wpa_admin_rate_footer_text( $footer_text ) {

	$rate_text = '';
	$current_screen = get_current_screen();

	// Add the Analytify admin pages
	$wpa_pages[] = 'toplevel_page_analytify-dashboard';
	$wpa_pages[] = 'analytify_page_analytify-campaigns';
	$wpa_pages[] = 'analytify_page_analytify-settings';

	// update_option( 'analytify_admin_footer_text_rated', 0 );
	// Check to make sure we're on a Analytify admin pages
	if ( isset( $current_screen->id ) && in_array( $current_screen->id, $wpa_pages ) ) {
		// Change the footer text
		if ( ! get_option( 'analytify_admin_footer_text_rated' ) ) {
				$rate_text = sprintf( esc_html__( 'If you like %1$s Analytify %2$s please leave us a %5$s %3$s %6$s rating. %4$s A huge thank you from %1$s WPBrigade %2$s in advance!', 'wp-analytify' ), '<strong>', '</strong>', '&#9733;&#9733;&#9733;&#9733;&#9733;', '<br />', '<a href="https://analytify.io/go/rate-analytify" target="_blank" class="wpa-rating-footer" data-rated="Thanks dude ;)">', '</a>' );
					wpa_enqueue_js( "
                        jQuery('a.wpa-rating-footer').click(function() {
                            jQuery.post( '" . admin_url( 'admin-ajax.php' ) . "', { action: 'analytify_rated' } );
                            jQuery(this).parent().text( jQuery(this).data( 'rated' ) );
                        });
                    " );
		} else {
			$rate_text = esc_html_e( 'Thank you for tracking with Analytify.', 'wp-analytify' );
		}

		return $rate_text;
	}

	return $footer_text;
}

/**
 * Delete the cache of dashboard sections
 *
 * @since  1.2.6
 * @param  $start_date Start Date
 * @param  $end_date End Date
 * @return void
 */

function delete_dashboard_transients( $dashboard_profile_ID, $start_date, $end_date ) {

	delete_transient( md5( 'show-overall-dashboard'      . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-top-pages-dashboard'    . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-country-dashboard'      . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-city-dashboard'         . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-keywords-dashboard'     . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-social-dashboard'       . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-browser-dashboard'      . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-os-dashboard'           . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-mobile-dashboard'       . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-referrer-dashboard'     . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-page-stats-dashboard'   . $dashboard_profile_ID . $start_date . $end_date ) );

	delete_transient( md5( 'show-default-overall-dashboard' 			 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-overall-device-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-overall-dashboard-compare'. $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-top-pages-dashboard' 		 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-geographic-countries-dashboard' 	 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-geographic-cities-dashboard' 		 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-browser-dashboard'				 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-os-dashboard'						 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-mobile-dashboard'				 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-keyword-dashboard'				 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-pages-dashboard'					 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-social-dashboard'				 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-reffers-dashboard'				 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-reffers-dashboard'				 . $dashboard_profile_ID . $start_date . $end_date ) );
	delete_transient( md5( 'show-default-new-returning-dashboard'  . $dashboard_profile_ID . $start_date . $end_date ) );


}


/**
 * Dashboard settings backward compatibility till 1.2.5
 *
 * @since 1.2.6
 */
function wpa_dashboard_compatible() {

	$version = get_option( 'wpa_current_version' );

	if ( ! $version ) {
		// Run when version is less or equal than 1.2.5
		update_option( 'access_role_dashboard', array(
			'administrator',
			'editor',
		));
		update_option( 'dashboard_panels',  array(
			'show-real-time',
			'show-overall-dashboard',
			'show-top-pages-dashboard',
			'show-os-dashboard',
			'show-country-dashboard',
			'show-keywords-dashboard',
			'show-social-dashboard',
			'show-browser-dashboard',
			'show-referrer-dashboard',
			'show-page-stats-dashboard',
			'show-mobile-dashboard',
			'show-os-dashboard',
			'show-city-dashboard',
		));

		update_option( 'wpa_current_version', ANALYTIFY_VERSION );

	}

}



/**
 * Return classes for dashboard icons.
 * @param  string $class class name
 * @return string       return class.
 *
 * @since 2.0.0
 */
function pretty_class( $class ) {

	if ( "Google+" === $class ) {
		return 'analytify_google_plus';
	}
	else if ( "(not set)" === $class ) {
		return "analytify_not_set";
	}

 	return	"analytify_" . transliterateString( str_replace( array("’",' & ','-',' '), '_', strtolower( $class ) ) );

}

/**
 * Replace special charters with alphabets
 *
 * @since 2.0.0
 */
function transliterateString( $txt ) {
    $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
    return str_replace( array_keys ( $transliterationTable ), array_values( $transliterationTable ), $txt );
}


/**
 * Helper function for translation.
 */
if ( ! function_exists( 'analytify__' ) ) {
	/**
	 * Wrapper for __() gettext function.
	 * @param  string $string     Translatable text string
	 * @param  string $textdomain Text domain, default: wp-analytify
	 * @return void
	 */
	function analytify__( $string, $textdomain = 'wp-analytify' ) {
		return __( $string, $textdomain );
	}
}

if ( ! function_exists( 'analytify_e' ) ) {
	/**
	 * Wrapper for _e() gettext function.
	 * @param  string $string     Translatable text string
	 * @param  string $textdomain Text domain, default: wp-analytify
	 * @return void
	 */
	function analytify_e( $string, $textdomain = 'wp-analytify' ) {
		echo __( $string, $textdomain );
	}
}

class WP_ANALYTIFY_FUNCTIONS {


	/**
	 * @param  [string] page name
	 * @param  string custom message
	 * @return [boolean] true or false
	 *
	 * @since  [1.3]
	 */
	public static function wpa_check_profile_selection( $type, $message = '' ) {

		$_analytify_profile = get_option( 'wp-analytify-profile' );
		$dashboard_profile = isset ( $_analytify_profile['profile_for_dashboard'] ) ? $_analytify_profile['profile_for_dashboard'] : '';

		if ( empty( $dashboard_profile ) ) {

			if ( $message == '' ) {
				echo sprintf( esc_html__( '%1$s %2$s' . $type . ' Dashboard can\'t be loaded until your select your website profile %3$s here %4$s %5$s %6$s', 'wp-analytify' ), '<div class="error notice is-dismissible">', '<p>', '<a style="text-decoration:none" href="' . menu_page_url( 'analytify-settings', false ) . '#wp-analytify-profile">', '</a>', '</p>', '</div>' ); } else {
				echo $message; }

				return true;
		} else {
			return false;
		}

	}




	/**
	 * General Redirect URL to
	 *
	 * @return [type] [description]
	 */
	public static function generate_login_url() {

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$url = '';

		if ( 'on' == $wp_analytify->settings->get_option( 'user_advanced_keys','wp-analytify-advanced' ) ) {

			$redirect_url   = $wp_analytify->settings->get_option( 'redirect_uri','wp-analytify-advanced' );
			$client_id      = $wp_analytify->settings->get_option( 'client_id','wp-analytify-advanced' );

		} else {

			$redirect_url   = ANALYTIFY_REDIRECT;
			$client_id      = ANALYTIFY_CLIENTID;
		}

		$url = http_build_query( array(
			'next'            => $wp_analytify->pa_setting_url(),
			'scope'           => ANALYTIFY_SCOPE,
			'response_type'   => 'code',
			'state'           => get_admin_url() . 'admin.php?page=analytify-settings',
			'redirect_uri'    => $redirect_url,
			'client_id'       => $client_id,
			'access_type'     => 'offline',
			'approval_prompt' => 'force',
			)
		);

		return $url;

	}



	static function fetch_profiles_list() {

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$profiles = get_transient( 'profiles_list' );

		if ( ! $profiles && get_option( 'pa_google_token' ) ) {

			$profiles = $wp_analytify->pt_get_analytics_accounts();
			set_transient( 'profiles_list' , $profiles, 0 );
		}

		return $profiles;
	}

	/**
	 * Fetch list of all profiles in dropdown
	 *
	 * @since  2.0.0
	 * @return object accounts list
	 */
	static function fetch_profiles_list_summary() {

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$profiles = get_option( 'profiles_list_summary' );

		if ( ! $profiles && get_option( 'pa_google_token' ) ) {

			$profiles = $wp_analytify->pt_get_analytics_accounts_summary();
			update_option( 'profiles_list_summary' , $profiles );
		}

		return $profiles;
	}


	/**
	 * This function is used to fetch the profile name, UA Code from selected account/property.
	 *
	 */
	static function search_profile_info( $id, $index ) {

		if ( ! get_option( 'pa_google_token' ) ) { return; }

		$accounts = self::fetch_profiles_list_summary();

		if ( ! $accounts ) {
			return false;
		}

		foreach ( $accounts->getItems() as $account ) {
			foreach ( $account->getWebProperties() as  $property ) {
				foreach ( $property->getProfiles() as $profile ) {

					// Get Property ID i.e UA Code
					if ( $profile->getId() === $id && $index === 'webPropertyId') {
						return $property->getId();
					}

					// Get Property URL i.e analytify.io
					if ( $profile->getId() === $id && $index === 'websiteUrl') {
						return $property->getWebsiteUrl();
					}

					// Get Profile view i.e All website data
					if ( $profile->getId() === $id && $index === 'name') {
						return $profile->getName();
					}
				}
			}
		}
	}

	/**
	 * Return the UA Code for selected profile.
	 *
	 * @since 2.0.4
	 */
	static function get_UA_code() {

		$_ua_code = get_option( 'analytify_ua_code' );
		if ( $_ua_code ) {
			return $_ua_code;
		} else {
			$_ua_code =	WP_ANALYTIFY_FUNCTIONS::search_profile_info( $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_posts', 'wp-analytify-profile' ), 'webPropertyId' );
			update_option( 'analytify_ua_code', $_ua_code );
			return $_ua_code;
		}

	}

	static function is_connected() {

	}

	static function is_profile_selected() {

		$load_profile_settings = get_option( 'wp-analytify-profile' );

		if ( !empty( $load_profile_settings['profile_for_posts'] ) && !empty( $load_profile_settings['profile_for_dashboard'] ) ) {

			return true;
		}

	}
}
