<?php
/**
 * Floating license notice on Analytify admin screens (Pro).
 *
 * @package WP_Analytify
 * @since   9.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sticky license activation/renewal prompt.
 */
class Analytify_License_Sticky_Notice {

	const STYLE_HANDLE = 'analytify-license-sticky-notice';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_footer', array( $this, 'render' ), 20 );
	}

	/**
	 * Enqueue assets when the notice will display.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook_suffix ) {
		if ( ! $this->should_display( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			self::STYLE_HANDLE,
			plugins_url( 'assets/css/analytify-license-sticky-notice' . analytify_get_asset_suffix() . '.css', WP_ANALYTIFY_PLUGIN_DIR . '/wp-analytify.php' ),
			array(),
			defined( 'ANALYTIFY_VERSION' ) ? ANALYTIFY_VERSION : '1.0.0'
		);
	}

	/**
	 * Output sticky notice markup.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->should_display() ) {
			return;
		}

		$state = $this->get_notice_state();
		if ( '' === $state ) {
			return;
		}

		$license_url = admin_url( 'admin.php?page=analytify-settings#wp-analytify-license' );
		$pricing_url = 'https://analytify.io/pricing/';
		$is_expired  = ( 'expired' === $state );
		$link_text   = $is_expired
			? __( 'Renew License Now', 'wp-analytify' )
			: __( 'Activate Your License', 'wp-analytify' );

		$title = $is_expired
			? __( 'License Renewal Required', 'wp-analytify' )
			: __( 'License Activation Required', 'wp-analytify' );

		$description = $is_expired
			? __( 'Your Analytify Pro license has expired. Renew now to unlock future updates, premium features, and priority support.', 'wp-analytify' )
			: __( 'Your Analytify Pro license is inactive or has not been added. Activate now to unlock future updates, premium features, and priority support.', 'wp-analytify' );

		?>
		<div class="analytify-license-sticky" role="complementary" aria-label="<?php echo esc_attr( $title ); ?>">
			<div class="analytify-license-sticky__head">
				<div class="analytify-license-sticky__icon" aria-hidden="true"></div>
				<p class="analytify-license-sticky__title"><?php echo esc_html( $title ); ?></p>
			</div>
			<p class="analytify-license-sticky__text"><?php echo esc_html( $description ); ?></p>
			<a class="analytify-license-sticky__secondary" href="<?php echo esc_url( $license_url ); ?>">
				<?php echo esc_html( $link_text ); ?>
			</a>
			<a class="analytify-license-sticky__cta" href="<?php echo esc_url( $pricing_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Purchase New License', 'wp-analytify' ); ?>
				<span class="analytify-license-sticky__external" aria-hidden="true"></span>
			</a>
		</div>
		<?php
	}

	/**
	 * Whether the sticky notice should load on this request.
	 *
	 * @param string $hook_suffix Admin page hook.
	 * @return bool
	 */
	private function should_display( $hook_suffix = '' ) {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( ! $this->is_pro_active() ) {
			return false;
		}

		if ( ! $this->is_analytify_admin_screen( $hook_suffix ) ) {
			return false;
		}

		return '' !== $this->get_notice_state();
	}

	/**
	 * Whether the current request is an Analytify plugin admin screen.
	 *
	 * @param string $hook_suffix Admin page hook from admin_enqueue_scripts.
	 * @return bool
	 */
	private function is_analytify_admin_screen( $hook_suffix = '' ) {
		if ( '' !== $hook_suffix && $this->is_analytify_screen_identifier( $hook_suffix ) ) {
			return true;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen ) {
				if ( $this->is_analytify_screen_identifier( $screen->id ) ) {
					return true;
				}
				if ( $this->is_analytify_screen_identifier( $screen->base ) ) {
					return true;
				}
			}
		}

		global $plugin_page;
		if ( is_string( $plugin_page ) && '' !== $plugin_page && 0 === strpos( $plugin_page, 'analytify' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Match WordPress screen/hook IDs for Analytify admin pages.
	 *
	 * @param string $identifier Screen ID, base, or hook suffix.
	 * @return bool
	 */
	private function is_analytify_screen_identifier( $identifier ) {
		if ( ! is_string( $identifier ) || '' === $identifier ) {
			return false;
		}

		if ( 0 === strpos( $identifier, 'toplevel_page_analytify' ) ) {
			return true;
		}

		if ( 0 === strpos( $identifier, 'analytify_page_' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Pro must be installed; license UI only applies to Pro.
	 *
	 * @return bool
	 */
	private function is_pro_active() {
		return defined( 'ANALYTIFY_PRO_VERSION' ) || class_exists( 'WP_Analytify_Pro_Base', false );
	}

	/**
	 * Notice state: missing (no/invalid license), expired, or hidden.
	 *
	 * Expired is shown when a license key is still saved, but the license is no longer
	 * longer active (stored status or cached EDD check). That covers users who
	 * activated before an update and whose license later expired.
	 *
	 * @return string
	 */
	private function get_notice_state() {
		if ( $this->is_license_valid() ) {
			return '';
		}

		if ( $this->has_license_key() && $this->is_license_expired() ) {
			return 'expired';
		}

		return 'missing';
	}

	/**
	 * Whether a Pro license key is saved on the site.
	 *
	 * @return bool
	 */
	private function has_license_key() {
		$key = get_option( 'analytify_license_key', '' );
		if ( ! is_string( $key ) ) {
			return false;
		}
		return '' !== trim( $key );
	}

	/**
	 * Whether the stored Pro license status is active.
	 *
	 * @return bool
	 */
	private function is_license_valid() {
		$status = $this->get_license_status();
		return in_array( $status, array( 'valid', 'success' ), true );
	}

	/**
	 * Whether the Pro license is expired (option and/or cached EDD response).
	 *
	 * @return bool
	 */
	private function is_license_expired() {
		if ( 'expired' === $this->get_license_status() ) {
			return true;
		}

		return $this->is_license_expired_in_cache();
	}

	/**
	 * Whether a cached license API response reports expired.
	 *
	 * @return bool
	 */
	private function is_license_expired_in_cache() {
		$cached = get_site_transient( 'wpanalytify_license_response' );
		if ( is_string( $cached ) ) {
			$cached = json_decode( $cached );
		}

		if ( is_object( $cached ) && isset( $cached->license ) ) {
			return 'expired' === strtolower( (string) $cached->license );
		}

		$cached = get_site_transient( 'wp_analytify_check_license_expiration' );
		if ( is_object( $cached ) && isset( $cached->license ) ) {
			return 'expired' === strtolower( (string) $cached->license );
		}

		return false;
	}

	/**
	 * Normalized Pro license status from the options table.
	 *
	 * @return string
	 */
	private function get_license_status() {
		$status = get_option( 'analytify_license_status', '' );
		if ( ! is_string( $status ) ) {
			return '';
		}
		return strtolower( trim( $status ) );
	}
}
