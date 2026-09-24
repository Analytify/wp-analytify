<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Factory Reset Class for Analytify Plugin.
 *
 * This class is responsible for deleting settings added by the Analytify Plugin.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File name follows project convention

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Factory Reset class.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class Analytify_Factory_Reset {

	/**
	 * Array to store the names of settings to be deleted.
	 *
	 * @var array<string, mixed>
	 */
	public array $settings;

	/**
	 * Constructor method for initializing the class.
	 */
	public function __construct() {
		$this->settings = $this->get_all_settings();
	}

	/**
	 * Retrieve an array of settings to be deleted.
	 *
	 * @since 9.0.0
	 * @version 9.1.0
	 * @return array<string, mixed> An array of setting names.
	 */
	private function get_all_settings(): array {

		$settings = array(
			'wp_analytify_modules',
			'wp_analytify_pro_addons',
			'wp-analytify-tracking',
			'wp-analytify-email',
			'wp-analytify-front',
			'wp-analytify-events-tracking',
			'wp-analytify-custom-dimensions',
			'wp-analytify-forms',
			'analytify_widget_date_differ',
			'wp-analytify-profile',
			'wp-analytify-admin',
			'wp-analytify-dashboard',
			'wp-analytify-advanced',
			'analytify_ua_code',
			'analytify_date_differ',
			'wp_analytify_review_dismiss',
			'analytify_date_start',
			'analytify_date_end',
			'wp_analytify_review_dismiss_4_1_8',
			'wpanalytify_settings',
			'analytify_license_key',
			'analytify_license_status',
			'analytify_campaigns_license_status',
			'analytify_campaigns_license_key',
			'analytify_goals_license_status',
			'analytify_goals_license_key',
			'analytify_forms_license_status',
			'analytify_forms_license_key',
			'analytify_authors_license_status',
			'analytify_authors_license_key',
			'analytify_woo_license_status',
			'analytify_woo_license_key',
			'analytify_email_license_status',
			'analytify_email_license_key',
			'analytify-google-ads-tracking',
			'analytify-pixels-tracking',
			'analytify_pmpro_track_events',
			'analytify_pmpro_track_purchases',
			'analytify_llms_dismissed_notices',
			'_analytify_optin',
			'analytify_cache_timeout',
			'analytify_csv_data',
			'analytify_active_date',
			'analytify_edd_license_status',
			'analytify_edd_license_key',
			'_transient_timeout_analytify_api_addons',
			'_transient_analytify_api_addons',
			'analytify_ga4_exceptions',
			'analytify-ga-properties-summery',
			'analytify-ga4-streams',
			'analytify_tracking_property_info',
			'analytify_reporting_property_info',
			'analytify_gtag_move_to_notice',
			'analytify_current_version',
			'analytify_logs_setup',
			'analytify_pro_default_settings',
			'analytify_pro_active_date',
			'analytify_pro_upgrade_routine',
			'analytify_pro_current_version',
			'WP_ANALYTIFY_PRO_PLUGIN_VERSION',
			'wp-analytify-license',
			'analytify_authentication_date',
			'WP_ANALYTIFY_PLUGIN_VERSION_OLD',
			'WP_ANALYTIFY_PRO_PLUGIN_VERSION_OLD',
			'analytify_default_settings',
			'analytify_free_upgrade_routine',
			'WP_ANALYTIFY_PLUGIN_VERSION',
			'wp_analytify_active_time',
			'wp-analytify-authentication',
			'wp-analytify-help',
			'WP_ANALYTIFY_NEW_LOGIN',
			'profiles_list_summary',
			'pa_google_token',
			'post_analytics_token',
			'analytify_token_refresh_failed_email_sent',
		);

		// Convert to associative array with meaningful keys.
		$result = array();
		foreach ( $settings as $index => $value ) {
			$result[ 'setting_' . $index ] = $value;
		}
		return $result;
	}

	/**
	 * Retrieve user meta keys to delete on factory reset / uninstall.
	 *
	 * @since 9.1.0
	 * @return array<int, string>
	 */
	private function get_user_meta_keys(): array {
		$prefix = defined( 'WP_ANALYTIFY_USER_META_PREFIX' ) ? WP_ANALYTIFY_USER_META_PREFIX : 'wp_analytify_';

		return array(
			$prefix . 'review_later_at',
			$prefix . 'review_dismiss',
		);
	}

	/**
	 * Remove the specified settings.
	 *
	 * @return void
	 *
	 * @version 9.1.0
	 */
	public function remove_settings() {
		foreach ( $this->settings as $setting ) {
			delete_option( $setting );
		}

		$this->remove_user_metas();

		// Delete known transients.
		delete_transient( 'profiles_list' );
		delete_transient( 'analytify_token_request_error_logged' );
		delete_transient( 'analytify_token_error_logged' );
		delete_transient( 'analytify_quota_exception' );

		// Delete dynamic transients (cache).
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query needed for wildcard deletion of transients.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_analytify_%' OR option_name LIKE '_transient_timeout_analytify_%'" );

		// PMPro: GA4 idempotency flags (analytify_pmpro_tracked_{order_id}).
		$pmpro_tracked_like = $wpdb->esc_like( 'analytify_pmpro_tracked_' ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Wildcard cleanup for unbounded per-order options.
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $pmpro_tracked_like ) );

		// LearnDash / PMPro: dismissed admin notices (analytify_notice_ + 32-char md5 only).
		$notice_like = $wpdb->esc_like( 'analytify_notice_' ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Avoid deleting unrelated keys sharing a prefix.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND CHAR_LENGTH(option_name) = %d",
				$notice_like,
				49
			)
		);
	}

	/**
	 * Remove per-user meta written by Analytify (e.g. review notice snooze/dismiss).
	 *
	 * @since 9.1.0
	 * @return void
	 */
	private function remove_user_metas() {
		foreach ( $this->get_user_meta_keys() as $meta_key ) {
			if ( function_exists( 'wpb_sdk_delete_all_users_meta_key' ) ) {
				wpb_sdk_delete_all_users_meta_key( $meta_key );
				continue;
			}

			delete_metadata( 'user', 0, $meta_key, '', true );
		}
	}
}
