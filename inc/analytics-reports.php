<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Analytics Reports Component for WP Analytify
 *
 * This file contains all analytics report generation and single post analytics functions
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Analytics Reports Component Class
 */
class Analytify_Analytics_Reports {

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
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Hooks are now managed by Analytify_Loader class.
		// This prevents duplicate hook registration.
	}


	/**
	 * Show metabox under each Post type to display Analytics of single post/page in wp-admin.
	 *
	 * @return void
	 */
	public function show_admin_single_analytics_add_metabox() {

		// Return if disable post stats is on.
		$disable_post_stats = $this->analytify && $this->analytify->settings ? $this->analytify->settings->get_option( 'enable_back_end', 'wp-analytify-admin', 'on' ) : 'on';
		if ( 'on' !== $disable_post_stats ) {
			return;
		}

		global $post;

		if ( ! isset( $post ) ) {
			return;
		}
		$display_draft_posts = apply_filters( 'analytify_filter_to_display_draft_posts', false );

		// Don't show statistics on posts which are not published.
		if ( 'publish' !== $post->post_status && ! $display_draft_posts ) {
			return;
		}

		$post_types = $this->analytify && $this->analytify->settings ? $this->analytify->settings->get_option( 'show_analytics_post_types_back_end', 'wp-analytify-admin' ) : array();

		// Don't load boxes/sections if no any post type is selected.
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				add_meta_box(
					'pa-single-admin-analytics', // $id
					__( 'Analytify - Stats of this Post/Page', 'wp-analytify' ), // $title.
					array(
						$this,
						'show_admin_single_analytics',
					), // $callback
					$post_type, // $posts
					'normal',   // $context
					'high'      // $priority
				);
			}
		}
	}


	/**
	 * Show Analytics of single post/page in wp-admin under EDIT screen.
	 *
	 * @return void
	 */
	public function show_admin_single_analytics() {
		global $post;

		$back_exclude_posts = false;
		$_exclude_profile   = get_option( 'wp-analytify-admin' );

		if ( isset( $_exclude_profile['exclude_pages_back_end'] ) ) {
			$back_exclude_posts = explode( ',', $_exclude_profile['exclude_pages_back_end'] );
		}

		if ( is_array( $back_exclude_posts ) ) {
			if ( in_array( $post->ID, $back_exclude_posts, true ) ) {
				esc_html_e( 'This post is excluded and will NOT show Analytics.', 'wp-analytify' );

				return;
			}
		}

		$url_post = '';
		$url_post = wp_parse_url( get_permalink( $post->ID ) ? get_permalink( $post->ID ) : '' );

		if ( get_the_time( 'Y', $post->ID ) < 2005 ) {
			$start_date = '2005-01-01';
		} else {
			$start_date = (string) ( get_the_time( 'Y-m-d', $post->ID ) ? get_the_time( 'Y-m-d', $post->ID ) : '2005-01-01' );
		}

		$end_date        = gmdate( 'Y-m-d' );
		$is_access_level = $this->analytify && $this->analytify->settings ? $this->analytify->settings->get_option( 'show_analytics_roles_back_end', 'wp-analytify-admin' ) : array();

		if ( $this->analytify && $this->analytify->pa_check_roles( $is_access_level ) ) {  ?>

			<div class="analytify_setting analytify_wraper">
				<div class="analytify_select_date analytify_select_date_single_page">
					
					<?php WPANALYTIFY_Utils::date_form( (string) $start_date, (string) $end_date, array( 'input_submit_id' => 'view_analytics' ) ); ?>
					<?php do_action( 'after_single_view_stats_buttons' ); ?>
				</div>
			</div>

			<div class="show-hide">
				<?php $this->get_single_admin_analytics( $start_date, $end_date, $post->ID, 0 ); ?>
			</div>

			<?php
		} else {
			esc_html_e( 'You are not allowed to see stats', 'wp-analytify' );
		}
	}

	/**
	 * Get the Analytics data for wp-admin posts/pages.
	 *
	 * @param string $start_date Start date.
	 * @param string $end_date End date.
	 * @param int    $post_id Post ID.
	 * @param int    $ajax AJAX flag.
	 * @return void
	 */
	public function get_single_admin_analytics( $start_date = '', $end_date = '', $post_id = 0, $ajax = 0 ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Parameters required for backwards compatibility
		// This method is deprecated, moved to rest.
	}

	/**
	 * Get the Analytics data from ajax() call
	 *
	 * @return void
	 */
	public function get_ajax_single_admin_analytics() {
		// This method is deprecated, moved to rest.
	}

	/**
	 * Set module state
	 *
	 * @return void
	 */
	public function set_module_state() {
		$analytify_modules = get_option( 'wp_analytify_modules' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Validation and sanitization handled below
		$module_slug = isset( $_POST['module_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['module_slug'] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Validation and sanitization handled below
		$set_state = isset( $_POST['set_state'] ) ? sanitize_text_field( wp_unslash( $_POST['set_state'] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Validation and sanitization handled below
		$internal_module = isset( $_POST['internal_module'] ) ? sanitize_text_field( wp_unslash( $_POST['internal_module'] ) ) : '';
		$return          = 'success';

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verification handles security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'addons' ) ) {
			echo esc_html__( 'Failed', 'wp-analytify' );
			wp_die();
		}

		if ( 'true' === $internal_module ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				echo esc_html__( 'Failed', 'wp-analytify' );
				wp_die();
			}

			// Internal module - update option.
			if ( ! is_array( $analytify_modules ) ) {
				$analytify_modules = array();
			}

			if ( ! isset( $analytify_modules[ $module_slug ] ) ) {
				echo esc_html__( 'Failed', 'wp-analytify' );
				wp_die();
			}

			if ( 'active' === $set_state && 'pixels-tracking' === $module_slug
				&& function_exists( 'wp_analytify_pro_pixels_tracking_module_file_exists' )
				&& class_exists( 'WP_Analytify_Pro', false )
				&& ! wp_analytify_pro_pixels_tracking_module_file_exists()
			) {
				echo esc_html__( 'Failed', 'wp-analytify' );
				wp_die();
			}

			if ( 'active' === $set_state ) {
				$analytify_modules[ $module_slug ]['status'] = 'active';
			} else {
				$analytify_modules[ $module_slug ]['status'] = false;
			}

			update_option( 'wp_analytify_modules', $analytify_modules );
		} else {
			// External plugin - activate/deactivate plugin file.
			// Restore WP core's activate_plugins gate (plugins.php enforces this).
			if ( ! current_user_can( 'activate_plugins' ) ) {
				echo esc_html__( 'Failed', 'wp-analytify' );
				wp_die();
			}

			// $module_slug should be in format: plugin-folder/plugin-file.php.
			// Check if plugin exists in installed plugins.
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$all_plugins  = get_plugins();
			$plugin_found = false;

			// Check if slug matches any installed plugin.
			foreach ( $all_plugins as $plugin_file => $plugin_data ) {
				if ( $plugin_file === $module_slug ) {
					$plugin_found = true;
					break;
				}
			}

			if ( ! $plugin_found ) {
				echo esc_html__( 'Failed', 'wp-analytify' );
				wp_die();
			}

			if ( 'active' === $set_state ) {
				$plugin_change_state = activate_plugin( $module_slug );
			} else {
				$plugin_change_state = deactivate_plugins( $module_slug );
			}

			// Error in response.
			if ( is_wp_error( $plugin_change_state ) || ! empty( $plugin_change_state ) ) {
				$return = 'failed';
			}
		}

		echo esc_html( $return );
		wp_die();
	}

	/**
	 * Add analytics stats to post row actions
	 *
	 * @param array<string, mixed> $actions Post actions.
	 * @param WP_Post              $post Post object.
	 * @return array<string, mixed>
	 */
	public function post_rows_stats( $actions, $post ) {
		// Return if disable post stats is on.
		$disable_post_stats = $this->analytify && $this->analytify->settings ? $this->analytify->settings->get_option( 'enable_back_end', 'wp-analytify-admin', 'on' ) : 'on';
		if ( 'on' !== $disable_post_stats ) {
			return $actions;
		}
		$display_draft_posts = apply_filters( 'analytify_filter_to_display_draft_posts', false );

		if ( 'publish' === $post->post_status || true === $display_draft_posts ) {
			$actions['post_row_stats'] = '<a href="' . admin_url( 'post.php?post=' . (string) $post->ID . '&action=edit#pa-single-admin-analytics' ) . '" title="' . esc_attr__( 'View Stats of', 'wp-analytify' ) . ' "' . esc_attr( get_the_title( $post ) ) . '"">Stats</a>';
		}

		return $actions;
	}

	/**
	 * Add analytics stats to post submit box
	 *
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function post_submitbox_stats_action( $post ) {

		$display_draft_posts = apply_filters( 'analytify_filter_to_display_draft_posts', false );

		// Check if the post is published or if the filter allows displaying draft posts.
		if ( 'publish' === $post->post_status || $display_draft_posts ) {
			if ( in_array( $post->post_type, $this->analytify && $this->analytify->settings ? $this->analytify->settings->get_option( 'show_analytics_post_types_back_end', 'wp-analytify-admin', array() ) : array(), true ) ) {
				echo '<a id="view_stats_analytify" href="' . esc_url( admin_url( 'post.php?post=' . $post->ID . '&action=edit#pa-single-admin-analytics' ) ) . '" title="' . esc_attr__( 'View Stats of', 'wp-analytify' ) . ' "' . esc_attr( get_the_title( $post ) ) . '”" class="button button-primary button-large" style="float:left">' . esc_html__( 'View Stats', 'wp-analytify' ) . '</a>';
			}
		}
	}
}
