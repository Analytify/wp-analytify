<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists

/**
 * Analytify Utils Notices Trait
 *
 * This trait contains notice and error handling functions for the Analytify plugin.
 * It was created to separate notice logic from the main utils class,
 * providing functions for displaying various types of admin notices
 * and handling Google Analytics API exceptions.
 *
 * PURPOSE:
 * - Provides notice display functions
 * - Handles API exception processing
 * - Manages error message formatting
 * - Offers notice customization options
 *
 * @package WP_Analytify
 * @subpackage Utils
 * @since 8.0.0
 */

trait Analytify_Utils_Notices {

	/**
	 * CSS class for danger/error notices
	 *
	 * @var string
	 */
	private static $notice_danger_class = 'wp-analytify-danger';

	/**
	 * Default help text for error notices
	 *
	 * @var string
	 */
	private static $default_help_text = 'let us know this issue in Help tab of Analytify->settings page.';

	/**
	 * Priority for admin notices
	 *
	 * @var int
	 */
	private static $admin_notices_priority = 9;

	/**
	 * Add admin notice action.
	 *
	 * Helper method to add admin notice actions with consistent priority.
	 *
	 * @param mixed $function_name Function name to call for the notice.
	 * @return void
	 */
	private static function add_admin_notice( $function_name ) {
		if ( is_string( $function_name ) && method_exists( 'WPANALYTIFY_Utils', $function_name ) ) {
			$callback = array( 'WPANALYTIFY_Utils', $function_name );
			if ( is_callable( $callback ) ) {
				add_action( 'admin_notices', $callback, self::$admin_notices_priority );
			}
		}
	}

	/**
	 * Display a formatted error notice.
	 *
	 * Helper method to display error notices with consistent formatting.
	 *
	 * @param mixed $title       Error title/heading.
	 * @param mixed $description Error description.
	 * @param mixed $link_url    Optional URL for help link.
	 * @param mixed $link_text   Optional text for help link.
	 * @param mixed $help_text   Optional custom help text.
	 * @return void
	 */
	private static function display_error_notice( $title, $description, $link_url = '', $link_text = '', $help_text = '' ) {
		$class = self::$notice_danger_class;
		// Build the message with proper formatting.
		$message = sprintf( '%1$s%2$s:%3$s %4$s', '<b>', $title, '</b>', $description );
		// Add help link if provided.
		if ( ! empty( $link_url ) && ! empty( $link_text ) ) {
			$message .= sprintf( ' %1$s<a href="%2$s" target="_blank">%3$s</a>%4$s', '', $link_url, $link_text, '' );
		}

		// Add help text.
		if ( ! empty( $help_text ) ) {
			$message .= ' ' . $help_text;
		} else {
			$message .= ' ' . self::$default_help_text;
		}

		analytify_notice( $message, $class );
	}

	/**
	 * Handle Google Analytics API exceptions
	 *
	 * Processes exception errors from the Google Analytics API and
	 * adds appropriate admin notices based on the error reason.
	 *
	 * @param array<string, mixed> $_exception_errors Array of exception error data.
	 * @return void
	 */
	public static function handle_exceptions( $_exception_errors ) {
		if ( isset( $_exception_errors[0]['reason'] ) && 'dailyLimitExceeded' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'daily_limit_exceed_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'insufficientPermissions' === $_exception_errors[0]['reason'] && 'global' === $_exception_errors[0]['domain'] ) {
			self::add_admin_notice( 'no_profile_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'insufficientPermissions' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'insufficient_permissions_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'usageLimits.userRateLimitExceededUnreg' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'user_rate_limit_unreg_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'userRateLimitExceeded' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'user_rate_limit_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'rateLimitExceeded' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'rate_limit_exceeded_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'quotaExceeded' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'quota_exceeded_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'accessNotConfigured' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'access_not_configured_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'unexpected_profile_error' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'unexpected_profile_error' );
		} elseif ( isset( $_exception_errors[0]['reason'] ) && 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' === $_exception_errors[0]['reason'] ) {
			self::add_admin_notice( 'insufficient_token_scope' );
		}
	}

	/**
	 * Handle GA4-specific exceptions.
	 *
	 * Processes GA4-specific exception errors and adds appropriate
	 * admin notices for measurement protocol and stream creation issues.
	 *
	 * @return void
	 */
	public static function handle_ga4_exceptions() {
		$analytify_ga4_exceptions = get_option( 'analytify_ga4_exceptions' );
		if ( ! empty( $analytify_ga4_exceptions['mp_secret_exception']['reason'] ) && ( 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' === $analytify_ga4_exceptions['mp_secret_exception']['reason'] || 'Request had insufficient authentication scopes.' === $analytify_ga4_exceptions['mp_secret_exception']['reason'] ) ) {
			self::add_admin_notice( 'insufficient_token_scope' );
		}

		if ( ! empty( $analytify_ga4_exceptions['create_stream_exception']['reason'] ) && ( 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' === $analytify_ga4_exceptions['create_stream_exception']['reason'] || 'Request had insufficient authentication scopes.' === $analytify_ga4_exceptions['create_stream_exception']['reason'] ) ) {
			self::add_admin_notice( 'insufficient_token_scope' );
		}
	}

	/**
	 * Display insufficient token scope notice.
	 *
	 * Shows an admin notice when the Google Analytics access token
	 * has insufficient scopes for proper functionality.
	 *
	 * @return void
	 */
	public static function insufficient_token_scope() {
		$description = 'Please reauthenticate Analytify with your Google Analytics account and select all permission scopes at the Auth screen to ensure data from your website is properly tracked in Google Analytics.';
		self::display_error_notice( 'Insufficient Authentication Scopes', $description, '', '', '' );
	}

	/**
	 * Display daily limit exceeded error notice.
	 *
	 * Shows an admin notice when the Google Analytics API daily
	 * quota has been exceeded, with a link to troubleshooting guide.
	 *
	 * @return void
	 */
	public static function daily_limit_exceed_error() {
		$description = 'This Indicates that user has exceeded the daily quota (either per project or per view (profile)). Please follow this tutorial to fix this issue.';
		$link_url    = 'https://analytify.io/doc/fix-403-daily-limit-exceeded/';
		$link_text   = 'follow this tutorial';
		self::display_error_notice( 'Daily Limit Exceeded', $description, $link_url, $link_text );
	}

	/**
	 * Display insufficient permissions error notice.
	 *
	 * Shows an admin notice when the user doesn't have sufficient
	 * permissions for the specified entity in the query.
	 *
	 * @return void
	 */
	public static function insufficient_permissions_error() {
		$description = 'Indicates that the user does not have sufficient permissions for the entity specified in the query.';
		self::display_error_notice( 'Insufficient Permissions', $description );
	}

	/**
	 * Display user rate limit unregistered error notice.
	 *
	 * Shows an admin notice when the application needs to be registered
	 * in the Google API Console due to rate limiting.
	 *
	 * @return void
	 */
	public static function user_rate_limit_unreg_error() {
		$description = 'Indicates that the application needs to be registered in the Google API Console. Read this guide for to make it work.';
		$link_url    = 'https://analytify.io/get-client-id-client-secret-developer-api-key-google-developers-console-application/';
		$link_text   = 'this guide';
		self::display_error_notice( 'usageLimits.userRateLimitExceededUnreg', $description, $link_url, $link_text );
	}

	/**
	 * Display user rate limit exceeded error notice.
	 *
	 * Shows an admin notice when the user rate limit has been exceeded,
	 * with information about increasing limits in Google API Console.
	 *
	 * @return void
	 */
	public static function user_rate_limit_error() {
		$description = 'Indicates that the user rate limit has been exceeded. The maximum rate limit is 10 qps per IP address. The default value set in Google API Console is 1 qps per IP address. You can increase this limit in the Google API Console to a maximum of 10 qps.';
		$link_url    = 'https://console.developers.google.com/';
		$link_text   = 'Google API Console';
		self::display_error_notice( 'User Rate Limit Exceeded', $description, $link_url, $link_text );
	}

	/**
	 * Display rate limit exceeded error notice.
	 *
	 * Shows an admin notice when global or project rate limits
	 * have been exceeded.
	 *
	 * @return void
	 */
	public static function rate_limit_exceeded_error() {
		$description = 'Indicates that the global or overall project rate limits have been exceeded.';
		self::display_error_notice( 'Rate Limit Exceeded', $description );
	}

	/**
	 * Display quota exceeded error notice.
	 *
	 * Shows an admin notice when the 10 concurrent requests per view
	 * (profile) limit in the Core Reporting API has been reached.
	 *
	 * @return void
	 */
	public static function quota_exceeded_error() {
		$description = 'This indicates that the 10 concurrent requests per view (profile) in the Core Reporting API has been reached.';
		self::display_error_notice( 'Quota Exceeded', $description );
	}

	/**
	 * Display access not configured error notice.
	 *
	 * Shows an admin notice when the Google Analytics API has not been
	 * used in the project before or it has not been enabled in the
	 * service account.
	 *
	 * @return void
	 */
	public static function access_not_configured_error() {
		$description = 'Google Analytics API has not been used in this project before or it is disabled. Enable it by visiting your project in Google Project Console then retry. If you enabled this API recently, wait a few minutes for the action to propagate to our systems and retry.';
		$link_url    = 'https://console.developers.google.com/';
		$link_text   = 'Google Project Console';
		self::display_error_notice( 'Access Not Configured', $description, $link_url, $link_text );
	}

	/**
	 * Display unexpected profile error notice.
	 *
	 * Shows an admin notice when an unexpected error occurs
	 * while getting profiles list from Google Analytics account.
	 *
	 * @return void
	 */
	public static function unexpected_profile_error() {
		$description = 'An unexpected error occurred while getting profiles list from the Google Analytics account.';
		self::display_error_notice( 'Unexpected Error', $description );
	}

	/**
	 * Display no profile error notice.
	 *
	 * Shows an admin notice when no website is registered with
	 * the user's email at Google Analytics.
	 *
	 * @return void
	 */
	public static function no_profile_error() {
		$class   = self::$notice_danger_class;
		$message = '<p class="description" style="color:#ed1515">No Website is registered with your Email at <a href="https://analytics.google.com/">Google Analytics</a>.<br/> Please setup your site first, Check out this guide <a href="https://analytify.io/setup-account-google-analytics/">here</a> to setup it properly.</p>';
		analytify_notice( $message, $class );
	}

	/**
	 * Check if force clear cache is requested
	 *
	 * Checks if the force-clear-cache GET parameter is set to '1'
	 * to determine if cache should be forcefully cleared.
	 *
	 * @return bool True if force clear cache is requested, false otherwise
	 */
	public static function force_clear_cache() {
		if ( '1' === WPANALYTIFY_Utils::safe_get_param( 'force-clear-cache' ) ) {
			return true;
		}
		return false;
	}
}
