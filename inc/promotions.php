<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Promotions Component for WP Analytify
 *
 * This file contains all promotional notices and messages
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promotions Component Class
 */
class Analytify_Promotions {

	/**
	 * User meta: when the current user snoozed the review notice ("Maybe Later").
	 */
	private const REVIEW_LATER_USER_META = WP_ANALYTIFY_USER_META_PREFIX . 'review_later_at';

	/**
	 * User meta: permanent review notice dismissal for the current user.
	 */
	private const REVIEW_DISMISS_USER_META = WP_ANALYTIFY_USER_META_PREFIX . 'review_dismiss';

	/**
	 * Review notice delay in seconds (15 days).
	 */
	private const REVIEW_NOTICE_DELAY = 1296000;

	/**
	 * Main plugin instance
	 *
	 * @var WP_Analytify
	 */
	private $analytify;

	/**
	 * Constructor
	 *
	 * @version 7.0.5
	 * @param WP_Analytify $analytify Main plugin instance.
	 */
	public function __construct( $analytify ) {
		$this->analytify = $analytify;
	}

	/**
	 * Add deactivation modal
	 *
	 * @version 7.0.5
	 * @return void
	 */
	public function add_deactive_modal() {
		?>
		<div id="analytify-deactivate-modal" style="display: none;">
			<div class="analytify-deactivate-modal-content">
				<h3><?php esc_html_e( 'We\'re sorry to see you go!', 'wp-analytify' ); ?></h3>
				<p><?php esc_html_e( 'Before you deactivate Analytify, would you mind sharing why you\'re leaving?', 'wp-analytify' ); ?></p>
				<textarea id="analytify-deactivation-reason" placeholder="<?php esc_attr_e( 'Please share your feedback...', 'wp-analytify' ); ?>"></textarea>
				<div class="analytify-deactivate-modal-actions">
					<button id="analytify-deactivate-submit" class="button button-primary"><?php esc_html_e( 'Submit & Deactivate', 'wp-analytify' ); ?></button>
					<button id="analytify-deactivate-cancel" class="button"><?php esc_html_e( 'Cancel', 'wp-analytify' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Addons promo screen
	 *
	 * @return void
	 */
	public function addons_promo_screen() {
		include_once defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/addons-list.php';
	}

	/**
	 * Render optin
	 *
	 * @version 7.0.5
	 * @return void
	 */
	public function render_optin() {
		include_once defined( 'WP_ANALYTIFY_ROOT_PATH' ) ? WP_ANALYTIFY_ROOT_PATH : WP_ANALYTIFY_PLUGIN_DIR . '/views/optin-form.php';
	}

	/**
	 * Settings tabs
	 *
	 * @param string $current Current tab.
	 * @return void
	 */
	public function pa_settings_tabs( $current = 'authentication' ) {
		$tabs = array(
			'authentication' => __( 'Authentication', 'wp-analytify' ),
			'profile'        => __( 'Profile', 'wp-analytify' ),
			'admin'          => __( 'Admin', 'wp-analytify' ),
			'dashboard'      => __( 'Dashboard', 'wp-analytify' ),
			'front'          => __( 'Frontend', 'wp-analytify' ),
			'advanced'       => __( 'Advanced', 'wp-analytify' ),
			'help'           => __( 'Help', 'wp-analytify' ),
		);

		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab === $current ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=analytify-settings&tab=' . esc_attr( $tab ) . '">' . esc_html( $name ) . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * Profile warning
	 *
	 * @return void
	 */
	public function profile_warning() {
		if ( ! $this->analytify->pa_check_roles( 'manage_options' ) ) {
			return;
		}

		$profile = get_option( 'wp-analytify-profile' );
		if ( empty( $profile['profile_for_dashboard'] ) ) {
			// translators: %s is the admin URL.
			echo '<div class="notice notice-warning is-dismissible"><p>' . wp_kses( sprintf( __( 'Please <a href="%s">select your website profile</a> to view Analytics.', 'wp-analytify' ), esc_url( admin_url( 'admin.php?page=analytify-settings&tab=profile' ) ) ), array( 'a' => array( 'href' => array() ) ) ) . '</p></div>';
		}
	}

	/**
	 * GTAG move to notice
	 *
	 * @version 8.1.1
	 * @return void
	 */
	public function gtag_move_to_notice() {
		if ( ! $this->analytify->pa_check_roles( 'manage_options' ) ) {
			return;
		}

		$gtag_notice = get_option( 'analytify_gtag_move_to_notice' );
		if ( ! $gtag_notice ) {
			// translators: %s is the admin URL.
			echo '<div class="notice notice-info is-dismissible"><p>' . wp_kses( sprintf( /* translators: 1: Opening anchor tag, 2: Closing anchor tag. */ __( 'Analytify now supports GA4! %1$sClick here%2$s to learn more about the new features.', 'wp-analytify' ), '<a href="' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=advanced' ) ) . '">', '</a>' ), array( 'a' => array( 'href' => array() ) ) ) . '</p></div>';
		}
	}

	/**
	 * Dismiss notices
	 *
	 * @return void
	 */
	public function dismiss_notices() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verification handles security
		if ( isset( $_GET['analytify_dismiss'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'analytify_dismiss' ) ) {
			update_option( 'analytify_gtag_move_to_notice', true );
			wp_safe_redirect( remove_query_arg( array( 'analytify_dismiss', '_wpnonce' ) ) );
			exit;
		}
	}

	/**
	 * Whether the current user may see the review notice (dashboard roles + administrator).
	 *
	 * @return bool
	 */
	private function wp_analytify_user_can_see_dashboard_review_notice() {
		if ( ! is_user_logged_in() || ! $this->analytify || ! isset( $this->analytify->settings ) ) {
			return false;
		}

		$allowed_roles   = $this->analytify->settings->get_option(
			'show_analytics_roles_dashboard',
			'wp-analytify-dashboard',
			array()
		);
		$allowed_roles[] = 'administrator';

		return (bool) $this->analytify->pa_check_roles( $allowed_roles );
	}

	/**
	 * Whether the review notice was permanently dismissed for the current user.
	 *
	 * @since 9.1.0
	 * @return bool
	 */
	private function wp_analytify_is_review_notice_permanently_dismissed() {
		// Legacy site-option key used before 9.1; keep reading it for existing dismissals.
		if ( 'yes_v7' === get_site_option( 'wp_analytify_review_dismiss_4_1_8' ) ) {
			return true;
		}

		$user_id = get_current_user_id();
		if ( $user_id && 'yes_v7' === get_user_meta( $user_id, self::REVIEW_DISMISS_USER_META, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether the review notice is snoozed for the current user ("Maybe Later").
	 *
	 * @return bool
	 */
	private function wp_analytify_is_review_notice_snoozed_for_user() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		$snoozed_at = (int) get_user_meta( $user_id, self::REVIEW_LATER_USER_META, true );
		if ( ! $snoozed_at ) {
			return false;
		}

		return ( time() - $snoozed_at ) < self::REVIEW_NOTICE_DELAY;
	}

	/**
	 * Review notice dismissal
	 *
	 * @return void
	 */
	public function review_dismissal() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled by safe_verify_nonce
		if ( ! is_admin() || ! $this->wp_analytify_user_can_see_dashboard_review_notice() || ! WPANALYTIFY_Utils::safe_verify_nonce( '_wpnonce', 'analytify-review-nonce', 'GET' ) || ! isset( $_GET['wp_analytify_review_dismiss'] ) ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( $user_id ) {
			update_user_meta( $user_id, self::REVIEW_DISMISS_USER_META, 'yes_v7' );
		}

		wp_safe_redirect( remove_query_arg( array( 'wp_analytify_review_dismiss', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Review notice
	 *
	 * @return void
	 */
	public function analytify_review_notice() {
		if ( ! $this->wp_analytify_user_can_see_dashboard_review_notice() ) {
			return;
		}

		$this->review_dismissal();
		$this->review_prending();

		if ( $this->wp_analytify_is_review_notice_permanently_dismissed() || $this->wp_analytify_is_review_notice_snoozed_for_user() ) {
			return;
		}

		$activation_time = get_site_option( 'wp_analytify_active_time' );

		if ( ! $activation_time ) {
			$activation_time = time();
			add_site_option( 'wp_analytify_active_time', $activation_time );
		}

		if ( time() - $activation_time > self::REVIEW_NOTICE_DELAY ) {
			add_action( 'admin_notices', array( $this, 'analytify_review_notice_message' ) );
		}
	}

	/**
	 * Review pending
	 *
	 * @return void
	 */
	public function review_prending() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled by safe_verify_nonce
		if ( ! is_admin() || ! $this->wp_analytify_user_can_see_dashboard_review_notice() || ! WPANALYTIFY_Utils::safe_verify_nonce( '_wpnonce', 'analytify-review-nonce', 'GET' ) || ! isset( $_GET['wp_analytify_review_later'] ) ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( $user_id ) {
			update_user_meta( $user_id, self::REVIEW_LATER_USER_META, time() );
		}

		wp_safe_redirect( remove_query_arg( array( 'wp_analytify_review_later', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Review notice message
	 *
	 * @return void
	 */
	public function analytify_review_notice_message() {
		if ( ! $this->wp_analytify_user_can_see_dashboard_review_notice() ) {
			return;
		}

		// Sanitize server data for security.
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$scheme      = ( wp_parse_url( $request_uri, PHP_URL_QUERY ) ) ? '&' : '?';
		$url         = $request_uri . $scheme . 'wp_analytify_review_dismiss=yes_v7';
		$dismiss_url = wp_nonce_url( $url, 'analytify-review-nonce' );

		$_later_link = $request_uri . $scheme . 'wp_analytify_review_later=yes';
		$later_url   = wp_nonce_url( $_later_link, 'analytify-review-nonce' );
		?>

	<div class="analytify-review-notice">
		<div class="analytify-review-thumbnail">
			<img src="<?php echo esc_url( plugins_url( 'assets/img/notice-logo.svg', __DIR__ ) ); ?>" alt="notice">
		</div>
		<div class="analytify-review-text">
			<h3><?php esc_html_e( 'How\'s Analytify going, impressed?', 'wp-analytify' ); ?></h3>
			<p><?php esc_html_e( 'We hope you\'ve enjoyed using Analytify! Would you consider leaving us a 5-star review on WordPress.org?', 'wp-analytify' ); ?></p>
			<ul class="analytify-review-ul">
				<li><a href="https://wordpress.org/support/view/plugin-reviews/wp-analytify?rate=5#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php esc_html_e( 'Sure! I\'d love to!', 'wp-analytify' ); ?></a></li>
				<li><a href="<?php echo esc_url( $dismiss_url ); ?>"><span class="dashicons dashicons-smiley"></span><?php esc_html_e( 'I\'ve already left a 5-star review', 'wp-analytify' ); ?></a></li>
				<li><a href="<?php echo esc_url( $later_url ); ?>"><span class="dashicons dashicons-calendar-alt"></span><?php esc_html_e( 'Maybe Later', 'wp-analytify' ); ?></a></li>
				<li><a href="<?php echo esc_url( $dismiss_url ); ?>"><span class="dashicons dashicons-dismiss"></span><?php esc_html_e( 'Never show again', 'wp-analytify' ); ?></a></li>
				</ul>
			</div>
		</div>

		<?php
	}

	/**
	 * Buy pro notice
	 *
	 * @return void
	 */
	public function analytify_buy_pro_notice() {
		// Don't show upgrade notice if Pro version is active.
		if ( defined( 'ANALYTIFY_PRO_VERSION' ) || class_exists( 'WP_Analytify_Pro' ) ) {
			return;
		}

		$this->buy_pro_notice_dismissal();

		$activation_time  = get_site_option( 'wp_analytify_buy_pro_active_time' );
		$review_dismissal = get_site_option( 'wp_analytify_buy_pro_notice' );

		if ( 'yes' === $review_dismissal ) {
			return;
		}

		if ( ! $activation_time ) {
			$activation_time = time();
			add_site_option( 'wp_analytify_buy_pro_active_time', $activation_time );
		}

		// 604800 = 7 Days in seconds.
		if ( time() - $activation_time > 604800 ) {
			add_action( 'admin_notices', array( $this, 'analytify_buy_pro_message' ) );
		}
	}

	/**
	 * Dismiss Buy Pro Notice.
	 *
	 * @since 2.1.23
	 * @return void
	 */
	public function buy_pro_notice_dismissal() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'wp_analytify_buy_pro_notice' ) || ! isset( $_GET['wp_analytify_buy_pro_notice_dismiss'] ) ) {
			return;
		}

		add_site_option( 'wp_analytify_buy_pro_notice', 'yes' );
	}

	/**
	 * Show Buy Pro Notice.
	 *
	 * @since 2.1.23
	 * @return void
	 */
	public function analytify_buy_pro_message() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$scheme      = ( wp_parse_url( $request_uri, PHP_URL_QUERY ) ) ? '&' : '?';
		$url         = $request_uri . $scheme . 'wp_analytify_buy_pro_notice_dismiss=yes';
		$dismiss_url = wp_nonce_url( $url, 'wp_analytify_buy_pro_notice' );

		$class = 'wp-analytify-success';

		$message = sprintf( 'Analytify now powering %1$s30,000+%2$s websites. Use the coupon code %1$sBFCM60%2$s to redeem a %1$s60%% %2$s discount on Pro. %3$sApply Coupon%4$s %5$s I\'m good with free.%6$s', '<strong>', '</strong>', '<a href="https://analytify.io/pricing/?discount=BFCM60" target="_blank" class="wp-analytify-notice-link"><span class="dashicons dashicons-smiley"></span> ', '</a>', '<a href="' . $dismiss_url . '" class="wp-analytify-notice-link"><span class="dashicons dashicons-dismiss"></span>', '</a>' );

		analytify_notice( $message, $class );
	}

	/**
	 * Add rating icon
	 *
	 * @param mixed $meta_fields Plugin meta fields.
	 * @param mixed $file Plugin file.
	 * @return mixed
	 */
	public function add_rating_icon( $meta_fields, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$meta_fields[] = '<a href="https://wordpress.org/support/view/plugin-reviews/wp-analytify?rate=5#postform" target="_blank" class="button button-primary" title="' . __( 'Rate Analytify 5 Stars on WordPress.org', 'wp-analytify' ) . '">' . __( 'Rate 5 Stars', 'wp-analytify' ) . '</a>';
		}
		return $meta_fields;
	}
}
