<?php
if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

/**
 * Compatibility for older versions for Analytify before 2.0
 *
 * @since 2.0
 */
class WP_Analytify_Compatibility_Upgrade{

	protected $profile_settings  = array();
	protected $admin_settings    = array();
	protected $advanced_settings = array();
	protected $dashboard_settings = array();


	function __construct() {

		// add_action( 'plugins_loaded' , array( $this, 'upgrade_routine' ) );
		$this->upgrade_routine();
	}

	public function upgrade_routine() {
		$this->profile_settings();
		$this->admin_settings();
		$this->advanced_settings();
		$this->dashboard_settings();

	}

	public function profile_settings() {

		if ( get_option( 'pt_webprofile' ) ) {

			$this->profile_settings['profile_for_posts'] = get_option( 'pt_webprofile' );
			delete_option( 'pt_webprofile' );
		}

		if ( get_option( 'pt_webprofile_dashboard' ) ) {

			$this->profile_settings['profile_for_dashboard'] = get_option( 'pt_webprofile_dashboard' );
			delete_option( 'pt_webprofile_dashboard' );
		}

		if ( get_option( 'analytify_code' ) == '1' ) {

			$this->profile_settings['install_ga_code'] = 'on';
			delete_option( 'analytify_code' );
		} else {
			delete_option( 'analytify_code' );
		}

		if ( get_option( 'display_tracking_code' ) ) {

			$this->profile_settings['exclude_users_tracking'] = get_option( 'display_tracking_code' );
			delete_option( 'display_tracking_code' );
		}

		if ( ! empty( $this->profile_settings ) ) {
			 update_option( 'wp-analytify-profile', $this->profile_settings );
		}
	}

	public function admin_settings() {

		if ( get_option( 'post_analytics_disable_back' ) == '1' ) {

			$this->admin_settings['disable_back_end'] = 'on';
			delete_option( 'post_analytics_disable_back' );
		} else {
			delete_option( 'post_analytics_disable_back' );
		}

		if ( get_option( 'post_analytics_access_back' ) ) {

			$this->admin_settings['show_analytics_roles_back_end'] = get_option( 'post_analytics_access_back' );
			delete_option( 'post_analytics_access_back' );
		}

		if ( get_option( 'analytify_posts_stats' ) ) {

			$this->admin_settings['show_analytics_post_types_back_end'] = get_option( 'analytify_posts_stats' );
			delete_option( 'analytify_posts_stats' );
		}

		if ( get_option( 'post_analytics_settings_back' ) ) {

			$this->admin_settings['show_panels_back_end'] = get_option( 'post_analytics_settings_back' );
			delete_option( 'post_analytics_settings_back' );
		}

		if ( get_option( 'post_analytics_exclude_posts_back' ) ) {

			$this->admin_settings['exclude_pages_back_end'] = get_option( 'post_analytics_exclude_posts_back' );
			delete_option( 'post_analytics_exclude_posts_back' );
		}

		if ( ! empty( $this->admin_settings ) ) {
			update_option( 'wp-analytify-admin', $this->admin_settings );
		}
	}

	public function advanced_settings() {

		if ( get_option( 'ANALYTIFY_USER_KEYS' ) == 'Yes' ) {

			$this->advanced_settings['user_advanced_keys'] = 'on';
			delete_option( 'ANALYTIFY_USER_KEYS' );
		} else {
			delete_option( 'ANALYTIFY_USER_KEYS' );
		}

		if ( get_option( 'ANALYTIFY_CLIENTID' ) ) {

			$this->advanced_settings['client_id'] = get_option( 'ANALYTIFY_CLIENTID' );
			delete_option( 'ANALYTIFY_CLIENTID' );
		}

		if ( get_option( 'ANALYTIFY_CLIENTSECRET' ) ) {

			$this->advanced_settings['client_secret'] = get_option( 'ANALYTIFY_CLIENTSECRET' );
			delete_option( 'ANALYTIFY_CLIENTSECRET' );
		}

		if ( get_option( 'ANALYTIFY_REDIRECT_URI' ) ) {

			$this->advanced_settings['redirect_uri'] = get_option( 'ANALYTIFY_REDIRECT_URI' );
			delete_option( 'ANALYTIFY_REDIRECT_URI' );
		}

		if ( ! empty( $this->advanced_settings ) ) {
			update_option( 'wp-analytify-advanced', $this->advanced_settings );
		}
	}

	public function dashboard_settings() {

		$this->dashboard_settings['show_analytics_panels_dashboard'] = array(
			'show-real-time',
			'show-compare-stats',
			'show-overall-dashboard',
			'show-top-pages-dashboard',
			'show-geographic-dashboard',
			'show-system-stats',
			'show-keywords-dashboard',
			'show-social-dashboard',
			'show-referrer-dashboard',
			'show-page-stats-dashboard',
		);

		$this->dashboard_settings['show_analytics_roles_dashboard'] = array(
			'administrator'
		);
		update_option( 'wp-analytify-dashboard', 	$this->dashboard_settings );
	}


}

if ( ! get_option( 'analytify_free_upgrade_routine' ) ) {

	$WP_Analytify_Compatibility_Upgrade = new WP_Analytify_Compatibility_Upgrade();
	update_option( 'analytify_free_upgrade_routine', 'done' );
}
?>
