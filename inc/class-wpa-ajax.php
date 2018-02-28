<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Handling all the AJAX calls in WP Analytify
 *
 * @since 1.2.4
 * @class WPANALYTIFY_AJAX
 */
class WPANALYTIFY_AJAX {

	protected static $show_settings = array();

	public static function init() {


		$_analytify_dashboard = get_option( 'wp-analytify-dashboard' );
		if (  $_analytify_dashboard &&	 array_key_exists( 'show_analytics_panels_dashboard', $_analytify_dashboard ) ) {
			self::$show_settings = $_analytify_dashboard['show_analytics_panels_dashboard'];
		}

		$ajax_calls = array(
			'rated'	=> false,
			'load_general_stats' => false,
			'load_default_general_stats' => false,
			'load_top_pages' => false,
			'load_default_top_pages' => false,
			'load_country_stats' => false,
			'load_city_stats' => false,
			'load_keyword_stats' => false,
			'load_social_stats' => false,
			'load_browser_stats' => false,
			'load_os_stats' => false,
			'load_referrer_stats' => false,
			'load_page_exit_stats' => false,
			'fetch_log' => false,
			'load_online_visitors'	 => true,
			'load_default_geographic' => false,
			'load_default_system' => false,
			'load_default_keyword' => false,
			'load_default_page' => false,
			'load_default_social_media' => false,
			'load_default_reffers' => false,
			'dismiss_pointer'	=> true,
			'remove_comparison_gif' => false,
			'deactivate' => true,
			'optin_yes' => false,
			'optout_yes' => false,
			'optin_skip' => false
			);

		foreach ( $ajax_calls as $ajax_call => $no_priv ) {
			// code...
			add_action( 'wp_ajax_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );

			if ( $no_priv ) {
				add_action( 'wp_ajax_nopriv_analytify_' . $ajax_call, array( __CLASS__, $ajax_call ) );
			}
		}
	}

	/**
	 * Fetch Current Online Visitors
	 */
	public static function load_online_visitors() {

			if (! isset( $_POST['pa_security'] ) OR ! wp_verify_nonce( $_POST['pa_security'] , 'pa_get_online_data' ) ) {
				return;
			}

			if (! function_exists( 'curl_version' ) ) {
				die('cURL not exists.');
			}

			print_r( stripslashes( json_encode( self::pa_realtime_data( ) ) ) );

			die();
		}

		/**
		 * Grab RealTime Data
		 */

		public static function pa_realtime_data() {

			$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
			$profile_id   = $wp_analytify->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
			$metrics      = 'ga:activeVisitors';
			$dimensions   = 'ga:source,ga:keyword,ga:trafficType,ga:visitorType';


			try {

				$data = $wp_analytify->service->data_realtime->get ( 'ga:' . $profile_id, $metrics, array(
							'dimensions' => $dimensions
				) );

			}

			catch ( Exception $e ) {
				update_option ( 'pa_lasterror_occur', esc_html($e));
				return '';
			}

			return $data;
		}

	/**
	 * Triggered when clicking the rating footer.
	 *
	 * @since 1.2.4
	 */
	public static function rated() {

		update_option( 'analytify_admin_footer_text_rated', 1 );
		die( 'rated' );
	}


	public static function load_general_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$compare_start_date = $_GET['compare_start_date'];
		$compare_end_date   = $_GET['compare_end_date'];
		$date_different = $_GET['date_different'];




		if ( is_array( self::$show_settings ) and in_array( 'show-overall-dashboard', self::$show_settings ) ) {

			$stats = get_transient( md5( 'show-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if( $stats === false ) {
				$stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:bounceRate,ga:newUsers,ga:entrances,ga:pageviews,ga:avgSessionDuration,ga:sessionDuration,ga:avgTimeOnPage,ga:users', $start_date, $end_date );
				set_transient( md5( 'show-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $stats, 60 * 60 * 20 );

			}

			// get prev stats
			$compare_stats =  get_transient( md5( 'show-overall-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) );
			// if ( false === $compare_stats ) {
				$compare_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:bounceRate,ga:users', $compare_start_date, $compare_end_date );
				set_transient( md5( 'show-overall-dashboard-compare' . $dashboard_profile_ID . $start_date . $end_date ) , $stats, 60 * 60 * 20 );
			// }

			if ( isset( $stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/general-stats.php';
				pa_include_general( $wp_analytify , $stats , $compare_stats , $date_different );
			}
		}

		die();
	}

	public static function load_default_general_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$compare_start_date = $_GET['compare_start_date'];
		$compare_end_date   = $_GET['compare_end_date'];
		$date_different     = $_GET['date_different'];


			$stats = get_transient( md5( 'show-default-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if( $stats === false ) {
				$stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:pageviewsPerSession,ga:percentNewSessions,ga:newUsers,ga:sessionDuration', $start_date, $end_date );
				set_transient( md5( 'show-default-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $stats, 60 * 60 * 20 );

			}

			// New vs Returning Users
			$new_returning_stats = get_transient( md5( 'show-default-new-returning-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if( $new_returning_stats === false ) {
				$new_returning_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:userType' );
				set_transient( md5( 'show-default-new-returning-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $new_returning_stats, 60 * 60 * 20 );

			}

			// Device Category Stats
			$device_category_stats = get_transient( md5( 'show-default-overall-device-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $device_category_stats === false ) {
				$device_category_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:deviceCategory', '-ga:sessions' );
				set_transient( md5( 'show-default-overall-device-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $device_category_stats, 60 * 60 * 20 );
			}

			// get prev stats
			$compare_stats =  get_transient( md5( 'show-default-overall-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) );
			if ( false === $compare_stats ) {
				$compare_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:users,ga:pageviews,ga:avgSessionDuration,ga:bounceRate,ga:pageviewsPerSession,ga:percentNewSessions,ga:newUsers', $compare_start_date, $compare_end_date );
				set_transient( md5( 'show-default-overall-dashboard-compare' . $dashboard_profile_ID . $compare_start_date . $compare_end_date ) , $compare_stats, 60 * 60 * 20 );
			}

			if ( isset( $stats->totalsForAllResults ) ) {

				include ANALYTIFY_ROOT_PATH . '/views/default/admin/general-stats.php';
				fetch_general_stats( $wp_analytify , $stats , $device_category_stats, $compare_stats , $date_different, $new_returning_stats );
			}


		die();
	}



	public static function load_top_pages() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-top-pages-dashboard', self::$show_settings ) ) {

			$top_page_stats = get_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $top_page_stats === false ) {
				$top_page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', false, 5 );
				set_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $top_page_stats, 60 * 60 * 20 );
			}

			if ( isset( $top_page_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/top-pages-stats.php';
				pa_include_top_pages_stats( $wp_analytify, $top_page_stats );
			}
		}

		die();
	}

	public static function load_default_top_pages(){

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		// Include Top Pages Statistics
		$top_page_stats =  get_transient( md5( 'show-default-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $top_page_stats === false ) {
			$top_page_stats = $wp_analytify->pa_get_analytics_dashboard('ga:pageviews', $start_date, $end_date, 'ga:PageTitle,ga:pagePath', '-ga:pageviews', false, 40 );
			set_transient( md5( 'show-default-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $top_page_stats, 60 * 60 * 20 );
		}

		include ANALYTIFY_ROOT_PATH . '/views/default/admin/top-pages-stats.php';
		fetch_top_pages_stats( $wp_analytify, $top_page_stats );

		wp_die( );
	}



	public static function load_country_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-country-dashboard', self::$show_settings ) ) {

			$country_stats = get_transient( md5( 'show-country-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $country_stats === false ) {
				$country_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:country', '-ga:sessions', false, 5 );
				set_transient( md5( 'show-country-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $country_stats, 60 * 60 * 20 );
			}

			if ( isset( $country_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/country-stats.php';
				pa_include_country( $wp_analytify,$country_stats );
			}
		}

		die();
	}


	public static function load_city_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-city-dashboard', self::$show_settings ) ) {

			$city_stats = get_transient( md5( 'show-city-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $city_stats === false ) {
				$city_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:city', '-ga:sessions', false, 5 );
				set_transient( md5( 'show-city-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $city_stats, 60 * 60 * 20 );
			}

			if ( isset( $city_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/city-stats.php';
				pa_include_city( $wp_analytify,$city_stats );
			}
		}

		die();
	}

	public static function load_keyword_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-keywords-dashboard', self::$show_settings ) ) {

			$keyword_stats = get_transient( md5( 'show-keywords-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $keyword_stats === false ) {
				$keyword_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 10 );
				set_transient( md5( 'show-keywords-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $keyword_stats, 60 * 60 * 20 );
			}

			if ( isset( $keyword_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/keywords-stats.php';
				pa_include_keywords( $wp_analytify,$keyword_stats );
			}
		}

		die();
	}


	public static function load_social_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-social-dashboard', self::$show_settings ) ) {

			$social_stats = get_transient( md5( 'show-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $social_stats === false ) {
				$social_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions', false, 10 );
				set_transient( md5( 'show-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $social_stats, 60 * 60 * 20 );
			}

			if ( isset( $social_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/social-stats.php';
				pa_include_social( $wp_analytify, $social_stats );
			}
		}

		die();
	}


	public static function load_browser_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-browser-dashboard', self::$show_settings ) ) {

			$browser_stats = get_transient( md5( 'show-browser-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $browser_stats === false ) {
				$browser_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:browser,ga:operatingSystem', '-ga:sessions',false,5 );
				set_transient( md5( 'show-browser-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $browser_stats, 60 * 60 * 20 );
			}

			if ( isset( $browser_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/browser-stats.php';
				pa_include_browser( $wp_analytify,$browser_stats );
			}
		}

		die();
	}

	public static function load_os_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-os-dashboard', self::$show_settings ) ) {

			$operating_stats = get_transient( md5( 'show-os-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $operating_stats === false ) {
				$operating_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:operatingSystem,ga:operatingSystemVersion', '-ga:sessions', false, 5 );
				set_transient( md5( 'show-os-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $operating_stats, 60 * 60 * 20 );
			}

			$operating_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:operatingSystem,ga:operatingSystemVersion', '-ga:sessions', false, 5 );
			if ( isset( $city_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/os-stats.php';
				pa_include_operating( $wp_analytify, $operating_stats );
			}
		}

		die();
	}


	public static function load_referrer_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-referrer-dashboard', self::$show_settings ) ) {

			$referr_stats = get_transient( md5( 'show-referrer-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $referr_stats === false ) {
				$referr_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 10 );
				set_transient( md5( 'show-referrer-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $referr_stats, 60 * 60 * 20 );
			}

			if ( isset( $referr_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH.'/views/old/admin/referrers-stats.php';
				pa_include_referrers( $wp_analytify, $referr_stats );
			}
		}

		die();
	}


	public static function load_page_exit_stats() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		if ( is_array( self::$show_settings ) and in_array( 'show-page-stats-dashboard', self::$show_settings ) ) {

			$page_stats = get_transient( md5( 'show-page-stats-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
			if ( $page_stats === false ) {
				$page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:entrances,ga:pageviews,ga:exits', $start_date, $end_date, 'ga:PagePath', '-ga:exits', 'ga:pageTitle!=(not set)', 5 );
				set_transient( md5( 'show-page-stats-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $page_stats, 60 * 60 * 20 );
			}

			if ( isset( $page_stats->totalsForAllResults ) ) {
				include ANALYTIFY_ROOT_PATH . '/views/old/admin/pages-stats.php';
				pa_include_pages_stats( $wp_analytify, $page_stats );
			}
		}

		die();
	}

	public static function load_default_geographic() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$countries_stats =  get_transient( md5( 'show-geographic-countries-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $countries_stats === false ) {
			$countries_stats 	= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:country' , '-ga:sessions' , 'ga:country!=(not set)', false );
			set_transient( md5( 'show-geographic-countries-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $countries_stats, 60 * 60 * 20  );
		}
		// Include Geographic Statistics

		$cities_stats = get_transient( md5( 'show-geographic-cities-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $cities_stats === false ) {
			$cities_stats 		= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:city,ga:country' , '-ga:sessions' , 'ga:city!=(not set);ga:country!=(not set)', 5 );
			set_transient( md5( 'show-geographic-cities-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $cities_stats, 60 * 60 * 20  );

		}

		include ANALYTIFY_ROOT_PATH . '/views/default/admin/geographic-stats.php';
		fetch_geographic_stats( $wp_analytify, $countries_stats, $cities_stats );

		wp_die( );
	}

	public static function load_default_system() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];


		$browser_stats =  get_transient( md5( 'show-default-browser-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $browser_stats === false ) {
			$browser_stats 	= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:browser,ga:operatingSystem' , '-ga:sessions' , 'ga:browser!=(not set);ga:operatingSystem!=(not set)', 5 );
			set_transient( md5( 'show-default-browser-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $browser_stats, 60 * 60 * 20  );
		}

		$os_stats = get_transient( md5( 'show-default-os-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $os_stats === false ) {
			$os_stats 			= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:operatingSystem,ga:operatingSystemVersion' , '-ga:sessions' , 'ga:operatingSystemVersion!=(not set)', 5 );
			set_transient( md5( 'show-default-os-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $os_stats, 60 * 60 * 20  );
		}

		$mobile_stats = get_transient( md5( 'show-default-mobile-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );
		if ( $mobile_stats === false ) {
				$mobile_stats 	= $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:mobileDeviceBranding,ga:mobileDeviceModel' , '-ga:sessions' , 'ga:mobileDeviceModel!=(not set);ga:mobileDeviceBranding!=(not set)', 5 );
				set_transient( md5( 'show-default-mobile-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $mobile_stats, 60 * 60 * 20  );
		}


		include ANALYTIFY_ROOT_PATH . '/views/default/admin/system-stats.php';
		fetch_system_stats( $wp_analytify, $browser_stats, $os_stats, $mobile_stats );

		wp_die();
	}

	public static function load_default_keyword() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$keyword_stats = get_transient( md5( 'show-default-keyword-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $keyword_stats === false ) {
			$keyword_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 8 );
			set_transient( md5( 'show-default-keyword-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $keyword_stats, 60 * 60 * 20  );
		}

		include ANALYTIFY_ROOT_PATH . '/views/default/admin/keywords-stats.php';
		fetch_keywords_stats( $wp_analytify, $keyword_stats );

		wp_die( );
	}

	public static function load_default_page() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$page_stats =  get_transient( md5( 'show-default-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $page_stats === false ) {
			$page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:entrances,ga:exits,ga:entranceRate,ga:exitRate', $start_date, $end_date , 'ga:pageTitle,ga:pagePath' , '-ga:entrances' , false, 5 );
			set_transient( md5( 'show-default-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $page_stats, 60 * 60 * 20  );
		}

		include ANALYTIFY_ROOT_PATH . '/views/default/admin/pages-stats.php';
		fetch_pages_stats( $wp_analytify, $page_stats );

		wp_die();

	}

	public static function load_default_social_media() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$social_stats =  get_transient( md5( 'show-default-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $social_stats === false ) {
			$social_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions', 'ga:socialNetwork!=(not set)', 7 );
			set_transient( md5( 'show-default-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $social_stats, 60 * 60 * 20  );
		}

		include ANALYTIFY_ROOT_PATH . '/views/default/admin/socialmedia-stats.php';
		fetch_socialmedia_stats( $wp_analytify, $social_stats );

		wp_die( );

	}

	public static function load_default_reffers() {

		$wp_analytify         = $GLOBALS['WP_ANALYTIFY'];
		$dashboard_profile_ID = $_GET['dashboard_profile_ID'];
		$start_date           = $_GET['start_date'];
		$end_date             = $_GET['end_date'];

		$referr_stats = get_transient( md5( 'show-default-reffers-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) );

		if ( $referr_stats === false ) {
			$referr_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 7 );
			set_transient( md5( 'show-default-reffers-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) , $referr_stats, 60 * 60 * 20  );
		}

		include ANALYTIFY_ROOT_PATH . '/views/default/admin/referrers-stats.php';
		fetch_referrers_stats( $wp_analytify, $referr_stats );

		wp_die();

	}

	static function fetch_log() {

		// $this->check_ajax_referer( 'fetch-log' );
		ob_start();
		self::output_diagnostic_info();
		$result = ob_get_contents();
		ob_end_clean();
		echo $result;
		die();
	}


	/**
	 * Outputs diagnostic info for debugging.
	 *
	 * Outputs useful diagnostic info text at the Diagnostic Info & Error Log
	 * section under the Help tab so the information can be viewed or
	 * downloaded and shared for debugging.
	 *
	 * If you would like to add additional diagnostic information use the
	 * `wpanalytify_diagnostic_info` action hook (see {@link https://developer.wordpress.org/reference/functions/add_action/}).
	 *
	 * <code>
	 * add_action( 'wpanalytify_diagnostic_info', 'my_diagnostic_info' ) {
	 *     echo "Additional Diagnostic Info: \r\n";
	 *     echo "...\r\n";
	 * }
	 * </code>
	 *
	 * @return void
	 */
	static function output_diagnostic_info() {
		global $wpdb;
		$table_prefix = $wpdb->base_prefix;

		echo 'site_url(): ';
		echo esc_html( site_url() );
		echo "\r\n";

		echo 'home_url(): ';
		echo esc_html( home_url() );
		echo "\r\n";

		echo 'WordPress: ';
		echo bloginfo( 'version' );
		if ( is_multisite() ) {
			echo ' Multisite';
		}
		echo "\r\n";

		echo 'Web Server: ';
		echo esc_html( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '' );
		echo "\r\n";

		echo 'PHP: ';
		if ( function_exists( 'phpversion' ) ) {
			echo esc_html( phpversion() );
		}
		echo "\r\n";

		echo 'MySQL: ';
		echo esc_html( empty( $wpdb->use_mysqli ) ? mysql_get_server_info() : mysqli_get_server_info( $wpdb->dbh ) );
		echo "\r\n";

		echo 'ext/mysqli: ';
		echo empty( $wpdb->use_mysqli ) ? 'no' : 'yes';
		echo "\r\n";

		echo 'WP Memory Limit: ';
		echo esc_html( WP_MEMORY_LIMIT );
		echo "\r\n";

		echo 'Blocked External HTTP Requests: ';
		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL ) {
			echo 'None';
		} else {
			$accessible_hosts = ( defined( 'WP_ACCESSIBLE_HOSTS' ) ) ? WP_ACCESSIBLE_HOSTS : '';

			if ( empty( $accessible_hosts ) ) {
				echo 'ALL';
			} else {
				echo 'Partially (Accessible Hosts: ' . esc_html( $accessible_hosts ) . ')';
			}
		}
		echo "\r\n";

		echo 'WP Locale: ';
		echo esc_html( get_locale() );
		echo "\r\n";

		echo 'DB Charset: ';
		echo esc_html( DB_CHARSET );
		echo "\r\n";

		if ( function_exists( 'ini_get' ) && $suhosin_limit = ini_get( 'suhosin.post.max_value_length' ) ) {
			echo 'Suhosin Post Max Value Length: ';
			echo esc_html( is_numeric( $suhosin_limit ) ? size_format( $suhosin_limit ) : $suhosin_limit );
			echo "\r\n";
		}

		if ( function_exists( 'ini_get' ) && $suhosin_limit = ini_get( 'suhosin.request.max_value_length' ) ) {
			echo 'Suhosin Request Max Value Length: ';
			echo esc_html( is_numeric( $suhosin_limit ) ? size_format( $suhosin_limit ) : $suhosin_limit );
			echo "\r\n";
		}

		echo 'Debug Mode: ';
		echo esc_html( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No' );
		echo "\r\n";

		echo 'WP Max Upload Size: ';
		echo esc_html( size_format( wp_max_upload_size() ) );
		echo "\r\n";

		echo 'PHP Time Limit: ';
		if ( function_exists( 'ini_get' ) ) {
			echo esc_html( ini_get( 'max_execution_time' ) );
		}
		echo "\r\n";

		echo 'PHP Error Log: ';
		if ( function_exists( 'ini_get' ) ) {
			echo esc_html( ini_get( 'error_log' ) );
		}
		echo "\r\n";

		echo 'fsockopen: ';
		if ( function_exists( 'fsockopen' ) ) {
			echo 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'OpenSSL: ';
		if ( defined( 'OPENSSL_VERSION_TEXT' ) ) {
			echo esc_html( OPENSSL_VERSION_TEXT );
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'cURL: ';
		if ( function_exists( 'curl_init' ) ) {
			echo 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		$theme_info = wp_get_theme();
		echo 'Active Theme Name: ' . esc_html( $theme_info->Name ) . "\r\n";
		echo 'Active Theme Folder: ' . esc_html( basename( $theme_info->get_stylesheet_directory() ) ) . "\r\n";
		if ( $theme_info->get( 'Template' ) ) {
			echo 'Parent Theme Folder: ' . esc_html( $theme_info->get( 'Template' ) ) . "\r\n";
		}
		if ( ! file_exists( $theme_info->get_stylesheet_directory() ) ) {
			echo "WARNING: Active Theme Folder Not Found\r\n";
		}

		echo "\r\n";

		echo "Active Plugins:\r\n";

		if ( isset( $GLOBALS['wpanalytify_compatibility'] ) ) {
			remove_filter( 'option_active_plugins', 'wpanalytifyc_exclude_plugins' );
			remove_filter( 'site_option_active_sitewide_plugins', 'wpanalytifyc_exclude_site_plugins' );
			$blacklist = array_flip( (array) $this->settings['blacklist_plugins'] );
		} else {
			$blacklist = array();
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_active_plugins = wp_get_active_network_plugins();
			$active_plugins         = array_map( array( $this, 'remove_wp_plugin_dir' ), $network_active_plugins );
		}

		foreach ( $active_plugins as $plugin ) {
			$suffix = ( isset( $blacklist[ $plugin ] ) ) ? '*' : '';
			self::print_plugin_details( WP_PLUGIN_DIR . '/' . $plugin, $suffix );
		}

		if ( isset( $GLOBALS['wpanalytify_compatibility'] ) ) {
			add_filter( 'option_active_plugins', 'wpanalytifyc_exclude_plugins' );
			add_filter( 'site_option_active_sitewide_plugins', 'wpanalytifyc_exclude_site_plugins' );
		}

		$mu_plugins = wp_get_mu_plugins();
		if ( $mu_plugins ) {
			echo "\r\n";

			echo "Must-use Plugins:\r\n";

			foreach ( $mu_plugins as $mu_plugin ) {
				self::print_plugin_details( $mu_plugin );
			}
		}

		echo "\r\n";


		echo "Analytify Profile Setting:\r\n";

		$analytify_profile = get_option( 'wp-analytify-profile' );
		print_r( $analytify_profile );

		echo "\r\n";


		echo "Analytify Front Setting:\r\n";

		$analytify_front = get_option( 'wp-analytify-front' );
		print_r( $analytify_front );

		echo "\r\n";


		echo "Analytify Admin Setting:\r\n";

		$analytify_admin = get_option( 'wp-analytify-admin' );
		print_r( $analytify_admin );

		echo "\r\n";


		echo "Analytify Dashboard Setting:\r\n";

		$analytify_dashboard = get_option( 'wp-analytify-dashboard' );
		print_r( $analytify_dashboard );

		echo "\r\n";


		echo "Analytify Advance Setting:\r\n";

		$analytify_advance = get_option( 'wp-analytify-advanced' );
		// if keys not set, show default.
		if ( ! isset( $analytify_advance['user_advanced_keys'] ) || $analytify_advance['user_advanced_keys'] == 'off' ) {

			// set as array if its string.
			if ( ! is_array( $analytify_advance ) ) { $analytify_advance = array(); }

			$analytify_advance['client_id'] = ANALYTIFY_CLIENTID;
			$analytify_advance['client_secret'] = ANALYTIFY_CLIENTSECRET;
		}
		print_r( $analytify_advance );



	}


	function output_log_file() {
			$this->load_error_log();
		if ( isset( $this->error_log ) ) {
			echo $this->error_log;
		}
	}


	static function print_plugin_details( $plugin_path, $suffix = '' ) {
		$plugin_data = get_plugin_data( $plugin_path );
		if ( empty( $plugin_data['Name'] ) ) {
			return;
		}

		printf( "%s%s (v%s) by %s\r\n", $plugin_data['Name'], $suffix, $plugin_data['Version'], $plugin_data['AuthorName'] );
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

		update_option('show_tracking_pointer_1', 1);
		die();
	}

	/**
	 * Remove Gif Add
	 *
	 * @since 2.0.11
	 */
	public static function remove_comparison_gif() {
		update_option( 'analytify_remove_comparison_gif', 'yes' );
		wp_die();
	}

	public static function  deactivate() {

		$email         = get_option( 'admin_email' );
		$_reason       = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
		$reason_detail = sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) );
		$reason        = '';

		if ( $_reason == '1' ) {
			$reason = 'I only needed the plugin for a short period';
		} elseif ( $_reason == '2' ) {
			$reason = 'I found a better plugin';
		} elseif ( $_reason == '3' ) {
			$reason = 'The plugin broke my site';
		} elseif ( $_reason == '4' ) {
			$reason = 'The plugin suddenly stopped working';
		} elseif ( $_reason == '5' ) {
			$reason = 'I no longer need the plugin';
		} elseif ( $_reason == '6' ) {
			$reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
		} elseif ( $_reason == '7' ) {
			$reason = 'Other';
		}

		$fields = array(
			'action'            => 'Deactivate',
			'reason'            => $reason,
			'reason_detail'     => $reason_detail,
		);

		analytify_send_data( $fields );

		wp_die();
	}


	// Add opt-in bacon
	function optin_yes() {

		// Track in user database
		update_site_option( '_analytify_optin', 'yes' );

		$fields = array(
			'action'	=>	'Activate',
			'track_mailchimp' =>	'yes'
			);
		analytify_send_data( $fields );
		wp_die();
	}

	// Delete opt-in bacon
	function optout_yes() {
		update_site_option( '_analytify_optin', 'no' );
		wp_die();
	}

	// Optin skip.
	function optin_skip() {

		update_site_option( '_analytify_optin', 'no' );

		$fields = array(
			'action'	=>	'Skip',
		);
		analytify_send_data( $fields );
		wp_die();
	}

} // End of WPANALYTIFY_AJAX .

function wp_analytify_ajax_load() {

	return WPANALYTIFY_AJAX::init();
}

$GLOBALS['WPANALYTIFY_AJAX'] = wp_analytify_ajax_load();
