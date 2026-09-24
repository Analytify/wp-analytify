<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * GDPR Compliance Component for WP Analytify
 *
 * This file contains all GDPR compliance and meta box functions
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GDPR Compliance Component Class
 */
class Analytify_GDPR_Compliance {

	/**
	 * Main plugin instance
	 *
	 * @var WP_Analytify
	 */
	private $analytify;

	/**
	 * Constructor
	 *
	 * @param WP_Analytify $analytify Main plugin instance.
	 */
	public function __construct( $analytify ) {
		$this->analytify = $analytify;
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		// GDPR compliance hooks.
		add_action( 'init', array( $this, 'init_gdpr_compliance' ), 1 );
		add_action( 'add_meta_boxes', array( $this, 'add_exclusion_meta_box' ) );
	}

	/**
	 * Initialize GDPR compliance
	 *
	 * @return void
	 */
	public function init_gdpr_compliance() {
		if ( $this->analytify && method_exists( $this->analytify, 'init_gdpr_compliance' ) ) {
			$this->analytify->init_gdpr_compliance();
		}
	}

	/**
	 * Add exclusion meta box
	 *
	 * @return void
	 */
	public function add_exclusion_meta_box() {
		$post_types = $this->analytify && $this->analytify->settings ? $this->analytify->settings->get_option( 'show_analytics_post_types_back_end', 'wp-analytify-admin', array( 'post', 'page' ) ) : array( 'post', 'page' );

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'analytify-exclusion',
				__( 'Analytify - Exclude from Analytics', 'wp-analytify' ),
				array( $this, 'exclusion_meta_box_callback' ),
				$post_type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Exclusion meta box callback
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function exclusion_meta_box_callback( $post ) {
		wp_nonce_field( 'analytify_exclusion_meta_box', 'analytify_exclusion_meta_box_nonce' );

		$exclude_from_analytics = get_post_meta( $post->ID, '_analytify_exclude_from_analytics', true );
		?>
		<label for="analytify_exclude_from_analytics">
			<input type="checkbox" id="analytify_exclude_from_analytics" name="analytify_exclude_from_analytics" value="1" <?php checked( $exclude_from_analytics, '1' ); ?> />
			<?php esc_html_e( 'Exclude this page from Analytics tracking', 'wp-analytify' ); ?>
		</label>
		<?php
	}

	/**
	 * Check if GDPR compliance is blocking tracking
	 *
	 * Delegates to {@see Class_Analytify_GDPR_Compliance::is_gdpr_compliance_blocking()} when loaded,
	 * which applies {@see 'analytify_consent_server_blocks_tracking'} and Cookie Notice / CookieYes rules.
	 *
	 * @return bool
	 */
	public static function is_gdpr_compliance_blocking() {

		if ( class_exists( 'Class_Analytify_GDPR_Compliance' ) ) {
			return Class_Analytify_GDPR_Compliance::is_gdpr_compliance_blocking();
		}

		return (bool) apply_filters( 'analytify_consent_server_blocks_tracking', false );
	}
}
