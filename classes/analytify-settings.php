<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Settings management class.
 *
 * This class handles all settings-related functionality for the Analytify plugin.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File name follows project convention
if ( ! class_exists( 'WP_Analytify_Settings' ) ) {

	// Include split traits for settings.
	require_once __DIR__ . '/analytify-settings/analytify-settings-sanitizers.php';
	require_once __DIR__ . '/analytify-settings/definitions.php';
	require_once __DIR__ . '/analytify-settings/render.php';
	require_once __DIR__ . '/analytify-settings/helpers.php';
	require_once __DIR__ . '/analytify-settings/actions.php';
	require_once __DIR__ . '/analytify-settings/fields.php';
	require_once __DIR__ . '/analytify-settings/promo.php';
	require_once __DIR__ . '/analytify-settings/promo-helpers.php';

	/**
	 * Settings class.
	 *
	 * @package WP_Analytify
	 * @since 1.0.0
	 */
	class WP_Analytify_Settings {

		use Analytify_Settings_Definitions;
		use Analytify_Settings_Render;
		use Analytify_Settings_Helpers;
		use Analytify_Settings_Actions {
			analytify_delete_cache as trait_analytify_delete_cache;
		}
		use Analytify_Settings_Fields {
			get_settings_fields as trait_get_settings_fields;
		}
		use Analytify_Settings_Promo {
			pro_features as trait_pro_features;
		}

		/**
		 * Settings sections array.
		 *
		 * @var array<string, mixed>
		 */
		protected $settings_sections = array();

		/**
		 * Settings fields array.
		 *
		 * @var array<string, mixed>
		 */
		protected $settings_fields = array();

		/**
		 * Constructor.
		 */
		public function __construct() {

			if ( current_user_can( 'manage_options' ) && ! wp_doing_ajax() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_post_analytify_delete_cache', array( $this, 'analytify_delete_cache' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'analytify_email_enqueue_media' ) );
		}

		/**
		 * Get current user roles.
		 *
		 * Back-compat: allow static calls from Pro to get roles list.
		 * Also safe to call as instance method.
		 *
		 * @return array<string, mixed> Array of user roles.
		 */
		public static function get_current_roles() {
			$roles          = array();
			$editable_roles = get_editable_roles();
			if ( is_array( $editable_roles ) && ! empty( $editable_roles ) ) {
				foreach ( $editable_roles as $role_slug => $role_info ) {
					if ( isset( $role_info['name'] ) ) {
						$roles[ $role_slug ] = $role_info['name'];
					}
				}
			} else {
				$roles['empty'] = 'no roles found';
			}
			return $roles;
		}

		/**
		 * Get current post types.
		 *
		 * Back-compat: allow static calls from Pro to get public post types.
		 * Also safe to call as instance method.
		 *
		 * @return array<string, mixed> Array of post types.
		 */
		public static function get_current_post_types() {
			$post_types_list = array();
			$post_types      = get_post_types( array( 'public' => true ) );
			if ( is_array( $post_types ) ) {
				foreach ( $post_types as $post_type ) {
					$post_types_list[ $post_type ] = $post_type;
				}
			}
			return $post_types_list;
		}

		/**
		 * Add field to a section.
		 *
		 * @param string               $section Section ID.
		 * @param array<string, mixed> $field Field arguments.
		 * @return $this
		 */
		public function add_field( $section, $field ) {
			$defaults = array(
				'name'  => '',
				'label' => '',
				'desc'  => '',
				'type'  => 'text',
			);

			$args                                = wp_parse_args( $field, $defaults );
			$this->settings_fields[ $section ][] = $args;

			return $this;
		}
	}
}
