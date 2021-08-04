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

			 $attach_hours = '';
			 $attach_min = '';
			 $attach_sec = '';
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
			 return $value['hours'] . $attach_hours . $value['minutes'] . $attach_min . $value['seconds'] . $attach_sec;
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

		if ( $_exception_errors[0]['reason'] == 'dailyLimitExceeded' ) {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','daily_limit_exceed_error' ), 9 );
		} else if ( $_exception_errors[0]['reason'] == 'insufficientPermissions' && $_exception_errors[0]['domain'] == 'global') {
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','no_profile_error' ), 9 );
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
		} else if ( $_exception_errors[0]['reason'] == 'unexpected_profile_error' ){
			add_action( 'admin_notices', array( 'WPANALYTIFY_Utils','unexpected_profile_error' ), 9 );
		}
	}

	/**
	 * Indicates that user has exceeded the daily quota (either per project or per view (profile)).
	 *
	 * @since 2.0.5
	 */
	public static function daily_limit_exceed_error() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://analytify.io/doc/fix-403-daily-limit-exceeded/';
		$message = sprintf( __( '%1$sDaily Limit Exceeded:%2$s This Indicates that user has exceeded the daily quota (either per project or per view (profile)). Please %3$sfollow this tutorial%4$s to fix this issue. let us know this issue (if it still doesn\'t work) in the Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>', '<a href="'. $link .'" target="_blank">', '</a>' );
		analytify_notice( $message, $class );
	}

	/**
	 * Indicates that the user does not have sufficient permissions.
	 *
	 * @since 2.0.5
	 */
	public static function insufficent_permissions_error() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( 'Insufficient Permissions: Indicates that the user does not have sufficient permissions for the entity specified in the query. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), $link );
		analytify_notice( $message, $class );
	}

	/**
	 * Indicates that the application needs to be registered in the Google Console
	 *
	 * @since 2.0.5
	 */
	public static function user_rate_limit_unreg_error() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://analytify.io/get-client-id-client-secret-developer-api-key-google-developers-console-application/';
		$message = sprintf( __( '%1$susageLimits.userRateLimitExceededUnreg:%2$s Indicates that the application needs to be registered in the Google API Console. Read %3$sthis guide%4$s for to make it work. let us know this issue in (if it still doesn\'t work) Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>', '<a href="'. $link .'">', '</a>'  );
		analytify_notice( $message, $class );
	}

	/**
	 * 	Indicates that the user rate limit has been exceeded. The maximum rate limit is 10 qps per IP address.
	 *
	 * @since 2.0.5
	 */
	public static function user_rate_limit_error() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://console.developers.google.com/';

		$message = sprintf( __( '%1$sUser Rate Limit Exceeded:%2$s Indicates that the user rate limit has been exceeded. The maximum rate limit is 10 qps per IP address. The default value set in Google API Console is 1 qps per IP address. You can increase this limit in the %3$sGoogle API Console%4$s to a maximum of 10 qps. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>', '<a href="'. $link .'">', '</a>'  );
		analytify_notice( $message, $class );
	}

	/**
	 * 	Indicates that the global or overall project rate limits have been exceeded.
	 *
	 * @since 2.0.5
	 */
	public static function rate_limit_exceeded_error() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( '%1$sRate Limit Exceeded:%2$s Indicates that the global or overall project rate limits have been exceeded. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>' );
		analytify_notice( $message, $class );
	}

	/**
	 * 	Indicates that the 10 concurrent requests per view (profile) in the Core Reporting API has been reached.
	 *
	 * @since 2.0.5
	 */
	public static function quota_exceeded_error() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://analytics.google.com/';
		$message = sprintf( __( '%1$sQuota Exceeded:%2$s This indicates that the 10 concurrent requests per view (profile) in the Core Reporting API has been reached. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>' );
		analytify_notice( $message, $class );
	}

	/**
	 * 	Access Not Configured.
	 *
	 * @since 2.0.5
	 */
	public static function accessNotConfigured() {

		$class   = 'wp-analytify-danger';
		$link    = 'https://console.developers.google.com/';

		$message = sprintf( __( '%1$sAccess Not Configured:%2$s Google Analytics API has not been used in this project before or it is disabled. Enable it by visiting your project in %3$sGoogle Project Console%4$s then retry. If you enabled this API recently, wait a few minutes for the action to propagate to our systems and retry.', 'wp-analytify' ), '<b>', '</b>', '<a href="'. $link .'">', '</a>' );
		analytify_notice( $message, $class );
	}

	/**
	 * 	Access Not Configured.
	 *
	 * @since 2.0.5
	 */
	public static function unexpected_profile_error() {

		$class   = 'wp-analytify-danger';

		$message = sprintf( __( '%1$sUnexpected Error:%2$s An unexpected error occurred while getting profiles list from the Google Analytics account. let us know this issue in Help tab of Analytify->settings page.', 'wp-analytify'), '<b>', '</b>' );
		analytify_notice( $message, $class );
	}

	/**
	 * Indicates that user have no register site on Google Analytics.
	 *
	 * @since 2.1.22
	 */
	public static function no_profile_error() {

		$class   = 'wp-analytify-danger';
		$message = '<p class="description" style="color:#ed1515">No Website is registered with your Email at <a href="https://analytics.google.com/">Google Analytics</a>.<br/> Please setup your site first, Check out this guide <a href="https://analytify.io/setup-account-google-analytics/">here</a> to setup it properly.</p>';
		analytify_notice( $message, $class );
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

	/**
	 * Get the Date Selection List.
	 *
	 * @since 2.1.14
	 */
	public static function get_date_list() {
		ob_start();
		?>
			<ul class="analytify_select_date_list">

				<li><?php _e( 'Today', 'wp-analytify' )?> <span data-date-diff="current_day" data-start="" data-end=""><span class="analytify_start_date_data analytify_current_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

				<li><?php _e( 'Last 7 days', 'wp-analytify' )?> <span data-date-diff="last_7_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_7_days"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

				<li><?php _e( 'Last 14 days', 'wp-analytify' )?> <span data-date-diff="last_14_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_14_days"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

				<li><?php _e( 'Last 30 days', 'wp-analytify' )?> <span data-date-diff="last_30_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_30_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

				<li><?php _e( 'This month', 'wp-analytify' )?> <span data-date-diff="this_month" data-start="" data-end=""><span class="analytify_start_date_data analytify_this_month_start_date"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>

				<li><?php _e( 'Last month', 'wp-analytify' )?> <span data-date-diff="last_month" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_month_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

				<li><?php _e( 'Last 3 months', 'wp-analytify' )?> <span data-date-diff="last_3_months" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_3_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

				<li><?php _e( 'Last 6 months', 'wp-analytify' )?> <span data-date-diff="last_6_months" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_6_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

				<li><?php _e( 'Last year', 'wp-analytify' )?> <span data-date-diff="last_year" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_year_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>

				<li><?php _e( 'Custom Range', 'wp-analytify' )?> <span class="custom_range"><?php _e( 'Select a custom date', 'wp-analytify' )?></span></li>
			</ul>
		<?php
		$content = ob_get_clean();
		echo $content;
	}

	/**
	 * Remove WordPress plugin directory.
	 *
	 * @since 2.1.14
	 */
	public static function remove_wp_plugin_dir( $name ) {
		$plugin = str_replace( WP_PLUGIN_DIR, '', $name );

		return substr( $plugin, 1 );
	}

	/**
	 * Renders date form.
	 * This form is used in the dashboard pages.
	 *
	 * @param string $start_date Start date value.
	 * @param string $end_date End date value.
	 */
	public static function date_form( $start_date, $end_date, $args = array() ) { 

		$_analytify_profile	= get_option( 'wp-analytify-profile' );
		$dashboard_profile	= isset ( $_analytify_profile['profile_for_dashboard'] ) ? $_analytify_profile['profile_for_dashboard'] : '';
		
		if ( empty( $dashboard_profile ) ) {
			return;
		} ?>
		
		<form class="analytify_form_date" action="" method="post">

			<?php if( isset($args['input_before_field_set']) and !empty($args['input_before_field_set']) ){
				echo $args['input_before_field_set'];
			} ?>
			
			<div class="analytify_select_date_fields">
				<input type="hidden" name="st_date" id="analytify_start_val">
				<input type="hidden" name="ed_date" id="analytify_end_val">
				<input type="hidden" name="analytify_date_diff" id="analytify_date_diff">

				<input type="hidden" name="analytify_date_start" id="analytify_date_start" value="<?php echo isset( $start_date ) ? $start_date : '' ?>">
				<input type="hidden" name="analytify_date_end" id="analytify_date_end" value="<?php echo isset( $end_date ) ? $end_date : '' ?>">

				<label for="analytify_start"><?php _e( 'From:', 'wp-analytify' )?></label>
				<input type="text" required id="analytify_start" value="">
				<label for="analytify_end"><?php _e( 'To:', 'wp-analytify' )?></label>
				<input type="text" onpaste="return: false;" oncopy="return: false;" autocomplete="off" required id="analytify_end" value="">

				<div class="analytify_arrow_date_picker"></div>
			</div>

			<?php if( isset($args['input_after_field_set']) and !empty($args['input_after_field_set']) ){
				echo $args['input_after_field_set'];
			} ?>

			<input type="submit" value="<?php _e( 'View Stats', 'wp-analytify' ) ?>" name="view_data" class="analytify_submit_date_btn"<?php if( isset($args['input_submit_id']) and !empty($args['input_submit_id']) ){ ?> id="<?php echo $args['input_submit_id']; ?>"<?php } ?>>

			<?php echo self::get_date_list(); ?>

		</form>
	<?php 
	}

	/**
	 * Prints the settings fields and values in presentable way.
	 *
	 * @param array $settings_array
	 */
	public static function print_settings_array( $settings_array ) {

		if ( is_array( $settings_array ) ) {
			foreach ( $settings_array as $key => $value ) {
				if ( is_array( $value ) ) {
					echo "$key:\r\n";
					echo print_r( $value, true ) . "\r\n";
				} else {
					echo "$key: $value\r\n";
				}
			}
		}
	}

	/**
	 * Check tracking code is enabled on site.
	 * The method checks for both manual or profile selection for tracking code.
	 *
	 * @param bool $only_auth Tracking only with authentication, no manual code.
	 * @return boolean
	 */
	public static function is_tracking_available( $only_auth = false ) {

		$is_track = false;
		
		if ( get_option( 'pa_google_token' ) ) { // Authenticated, check profiles selection and tracking option
			if ( 'on' === $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'install_ga_code', 'wp-analytify-profile', 'off' ) && WP_ANALYTIFY_FUNCTIONS::get_UA_code() ) { 
				$is_track = true;
			}
		} else if ( ! $only_auth && $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'manual_ua_code', 'wp-analytify-authentication', false ) ) { // Not authenticated,manual code
			$is_track = true;
		}

		// Check for GDPR compliance.
		if ( Analytify_GDPR_Compliance::is_gdpr_compliance_blocking() ) {
			return;
		}

		return $is_track;
	}

}

/**
 * Remove assets of other plugin/theme.
 *
 * @since 2.1.22
 */
function analytify_remove_conflicting_asset_files( $page ) {

	if ( 'toplevel_page_analytify-dashboard' !== $page ) {
		return;
	}

	wp_dequeue_script( 'default' ); // Bridge theme.
	wp_dequeue_script( 'bridge-admin-default' ); // Bridge theme.
	// wp_dequeue_script( 'jquery-ui-datepicker' );
	// wp_deregister_script( 'jquery-ui-datepicker' );
	wp_dequeue_script( 'gdlr-tax-meta' ); // MusicClub theme.
	wp_dequeue_script( 'woosb-backend' ); // WooCommerce Product Bundle.
	wp_deregister_script( 'bf-admin-plugins' ); // Better Ads Manager plugin.
	wp_dequeue_script( 'bf-admin-plugins' ); // Better Ads Manager plugin.
	wp_deregister_script( 'unite-ace-js' ); // Brooklyn theme.

	wp_deregister_script( 'elementor-common' ); // Elementor plugin.
	wp_dequeue_script( 'jquery-widgetopts-option-tabs' ); // Widget Options plugin.
	wp_dequeue_script( 'rml-default-folder' ); // WP Real Media Library plugin.
  wp_dequeue_script( 'resume_manager_admin_js' ); // WP Job Manager - Resume Manager plugin.

	if ( class_exists( 'Woocommerce_Pre_Order' ) ) {
		wp_dequeue_script( 'plugin-js' ); // Woocommerce Pre Order.
	}

	if ( class_exists( 'GhostPool_Setup' ) ) {
		wp_dequeue_script( 'theme-setup' ); // Huber theme.
  }
  
  if ( class_exists( 'WPcleverWoobt' ) ) {
		wp_dequeue_script( 'woobt-backend' ); // WPC Frequently Bought Together.
	}
}
add_action( 'admin_enqueue_scripts', 'analytify_remove_conflicting_asset_files', 999 );
