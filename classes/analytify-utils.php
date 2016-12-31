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
				$attach_hours = '<span class="analytify_xl_f">h </span> ';
			}
			if ( '' != $value['minutes'] ) {
				$attach_min = '<span class="analytify_xl_f">m </span>';
			}
			if ( '' != $value['seconds'] ) {
				$attach_sec = '<span class="analytify_xl_f">s</span>';
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
	 * Show notice if some exception occur.
	 * @param  array $exception exception details
	 *
	 * @since 2.0.5
	 */
	public static function handle_exceptions( $exception ) {
		$_exception_errors = $exception->getErrors();
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
		}
	}

	/**
	 * Indicates that user has exceeded the daily quota (either per project or per view (profile)).
	 *
	 * @since 2.0.5
	 */
	public static function daily_limit_exceed_error() {
		$class   = 'notice notice-warning';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( 'Daily Limit Exceeded. We highly recommend to use your own API keys in Advanced tab. <a href="%1$s">Register</a> your account first.', 'wp-analytify'), $link );
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
		$message = sprintf( __( 'Limit Quoata. <a href="%1$s">Register</a> your account first.', 'wp-analytify'), $link );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * Indicates that the application needs to be registered in the Google Console
	 *
	 * @since 2.0.5
	 */
	public static function user_rate_limit_unreg_error() {
		$class   = 'notice notice-warning';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( 'Limit Quoata. <a href="%1$s">Register</a> your account first.', 'wp-analytify'), $link );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}

	/**
	 * 	Indicates that the user rate limit has been exceeded. The maximum rate limit is 10 qps per IP address.
	 *
	 * @since 2.0.5
	 */
	public static function user_rate_limit_error() {
		$class   = 'notice notice-warning';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( 'Limit Quoata. <a href="%1$s">Register</a> your account first.', 'wp-analytify'), $link );
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
		$message = sprintf( __( 'Limit Quoata. <a href="%1$s">Register</a> your account first.', 'wp-analytify'), $link );
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
		$message = sprintf( __( 'Limit Quoata. <a href="%1$s">Register</a> your account first.', 'wp-analytify'), $link );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message, $link );
	}


}
