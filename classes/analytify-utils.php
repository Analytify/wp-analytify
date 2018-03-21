<?php

class WPANALYTIFY_Utils {

	/**
	 * Use wp_unslash if available, otherwise fall back to stripslashes_deep
	 *
	 * @param string|array $arg
	 *
	 * @since  2.0
	 * @return string|array
	 */
	public static function safe_wp_unslash( $arg ) {
		if ( function_exists( 'wp_unslash' ) ) {
			return wp_unslash( $arg );
		} else {
			return stripslashes_deep( $arg );
		}
	}

	/**
	 * Pretty time to display.
	 *
	 * @param int $time time.
	 *
	 * @since  2.0
	 */
	static function pretty_time( $time ) {

			// Check if numeric.
		if ( is_numeric( $time ) ) {

			$value = array(
				'years'   => '00',
				'days'    => '00',
				'hours'   => '',
				'minutes' => '',
				'seconds' => '',
				);

			if ( $time >= 31556926 ) {
				$value['years'] = floor( $time / 31556926 );
				$time           = ($time % 31556926);
			} //$time >= 31556926

			if ( $time >= 86400 ) {
				$value['days'] = floor( $time / 86400 );
				$time          = ($time % 86400);
			} //$time >= 86400
			if ( $time >= 3600 ) {
				$value['hours'] = str_pad( floor( $time / 3600 ), 1, 0, STR_PAD_LEFT );
				$time           = ($time % 3600);
			} //$time >= 3600
			if ( $time >= 60 ) {
				$value['minutes'] = str_pad( floor( $time / 60 ), 1, 0, STR_PAD_LEFT );
				$time             = ($time % 60);
			} //$time >= 60
					$value['seconds'] = str_pad( floor( $time ), 1, 0, STR_PAD_LEFT );
				// Get the hour:minute:second version.
			if ( '' != $value['hours'] ) {
				$attach_hours = '<span class="analytify_xl_f">' . _x( 'h', 'Hour Time', 'wp-analytify' ) . ' </span> ';
			}
			if ( '' != $value['minutes'] ) {
				$attach_min = '<span class="analytify_xl_f">' . _x( 'm', 'Minute Time', 'wp-analytify' ) . ' </span>';
			}
			if ( '' != $value['seconds'] ) {
				$attach_sec = '<span class="analytify_xl_f">' . _x( 's', 'Second Time', 'wp-analytify' ) . '</span>';
			}
					return $value['hours'] . @$attach_hours . $value['minutes'] . @$attach_min . $value['seconds'] . $attach_sec;
				// return $value['hours'] . ':' . $value['minutes'] . ':' . $value['seconds'];
		} //is_numeric($time)
		else {
			return false;
		}
	}

	/**
	 * Pretty numbers to display.
	 *
	 * @param int $time time.
	 *
	 * @since  2.0
	 */
	static function pretty_numbers( $num ) {

		if ( is_numeric( $num ) ){

			if( $num > 10000){

				return round( ($num / 1000),2 ) . 'k';

			}else{
				return number_format( $num );
			}

		}else{
			return $num;
		}

	}


	/**
	 * show coupon message to Free users Only.
	 */
	static function is_active_pro() {

		if ( is_plugin_active( 'wp-analytify-pro/wp-analytify-pro.php' ) )
			return true;
		else
			return false;

	}

	/**
	 * Show notices if some exception occurs.
	 *
	 * @param  array $exception exception details
	 *
	 * @since 2.0.5
	 */
	public static function handle_exceptions( $_exception_errors ) {

		//var_dump($exception);

		if ( $_exception_errors[0]['reason'] == 'dailyLimitExceeded' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','daily_limit_exceed_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'insufficientPermissions' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','insufficent_permissions_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'usageLimits.userRateLimitExceededUnreg' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','user_rate_limit_unreg_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'userRateLimitExceeded' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','user_rate_limit_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'rateLimitExceeded' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','rate_limit_exceeded_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'quotaExceeded' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','quota_exceeded_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'accessNotConfigured' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','accessNotConfigured' ), 9 );
		}
	}

	/**
	 * Indicates that user has exceeded the daily quota (either per project or per view (profile)).
	 *
	 * @since 2.0.5
	 */
	public static function daily_limit_exceed_error() {

		$class   = 'notice notice-warning';
		$link    = 'https://analytify.io/doc/fix-403-daily-limit-exceeded/';
		$message = sprintf( __( '%1$sDaily Limit Exceeded:%2$s This Indicates that user has exceeded the daily quota (either per project or per view (profile)). Please %3$sfollow this tutorial%4$s to fix this issue. let us know this issue (if it still doesn\'t work) in the Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>', '<a href="'. $link .'" target="_blank">', '</a>' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * Indicates that the user does not have sufficient permissions.
	 *
	 * @since 2.0.5
	 */
	public static function insufficent_permissions_error() {

		$class   = 'notice notice-warning';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( 'Insufficient Permissions: Indicates that the user does not have sufficient permissions for the entity specified in the query. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), $link );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * Indicates that the application needs to be registered in the Google Console
	 *
	 * @since 2.0.5
	 */
	public static function user_rate_limit_unreg_error() {

		$class   = 'notice notice-warning';
		$link    = 'https://analytify.io/get-client-id-client-secret-developer-api-key-google-developers-console-application/';
		$message = sprintf( __( '%1$susageLimits.userRateLimitExceededUnreg:%2$s Indicates that the application needs to be registered in the Google API Console. Read %3$sthis guide%4$s for to make it work. let us know this issue in (if it still doesn\'t work) Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>', '<a href="'. $link .'">', '</a>'  );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * 	Indicates that the user rate limit has been exceeded. The maximum rate limit is 10 qps per IP address.
	 *
	 * @since 2.0.5
	 */
	public static function user_rate_limit_error() {

		$class   = 'notice notice-warning';
		$link    = 'https://console.developers.google.com/';

		$message = sprintf( __( '%1$sUser Rate Limit Exceeded:%2$s Indicates that the user rate limit has been exceeded. The maximum rate limit is 10 qps per IP address. The default value set in Google API Console is 1 qps per IP address. You can increase this limit in the %3$sGoogle API Console%4$s to a maximum of 10 qps. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>', '<a href="'. $link .'">', '</a>'  );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * 	Indicates that the global or overall project rate limits have been exceeded.
	 *
	 * @since 2.0.5
	 */
	public static function rate_limit_exceeded_error() {

		$class   = 'notice notice-warning';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( '%1$sRate Limit Exceeded:%2$s Indicates that the global or overall project rate limits have been exceeded. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * 	Indicates that the 10 concurrent requests per view (profile) in the Core Reporting API has been reached.
	 *
	 * @since 2.0.5
	 */
	public static function quota_exceeded_error() {

		$class   = 'notice notice-warning';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( '%1$sQuota Exceeded:%2$s This indicates that the 10 concurrent requests per view (profile) in the Core Reporting API has been reached. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * 	Access Not Configured.
	 *
	 * @since 2.0.5
	 */
	public static function accessNotConfigured() {

		$class   = 'notice notice-warning';
		$link    = 'https://console.developers.google.com/';

		$message = sprintf( __( '%1$sAccess Not Configured:%2$s Google Analytics API has not been used in this project before or it is disabled. Enable it by visiting your project in %3$sGoogle Project Console%4$s then retry. If you enabled this API recently, wait a few minutes for the action to propagate to our systems and retry.', 'wp-analytify' ), '<b>', '</b>', '<a href="'. $link .'">', '</a>' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}

	/**
	* Clear cache when query string is set.
	* @return bool
	*
	* @since 2.1.9
	*/
	public static function force_clear_cache() {

		if ( isset( $_GET[ 'force-clear-cache' ] ) && '1' == $_GET[ 'force-clear-cache' ] ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the last element of array.
	 *
	 * @since 2.1.12
	 */
	static function end( $array ) {
		return end( $array );
	}

}
