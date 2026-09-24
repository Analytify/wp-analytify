<?php
/**
 * WP Brigade SDK: telemetry logger (schedule, collect, POST to API).
 *
 * @package wpbrigade_sdk
 * @subpackage WPB_SDK
 */

/**
 * Registers hooks, builds payloads, and sends telemetry for WP Brigade products.
 *
 * Product behavior is driven by each plugin's wpb_sdk_dynamic_init() module array.
 */
class WPBRIGADE_Logger {

	/**
	 * Singleton instances keyed by product slug.
	 *
	 * @var array<string, self>
	 */
	private static $instances = array();

	/**
	 * Module config from wpb_init(), keyed by slug.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private static $product_data = array();

	/**
	 * Slugs that already registered WordPress hooks via hooks().
	 *
	 * @var array<string, bool>
	 */
	private static $hooked_slugs = array();

	/**
	 * Slugs that already registered wp_wpb_sdk_after_uninstall lifecycle cleanup.
	 *
	 * @var array<string, bool>
	 */
	private static $lifecycle_uninstall_hooked = array();

	/**
	 * Last module id passed to instance() (used by uninstall static entry).
	 *
	 * @var mixed
	 */
	private static $module_id;

	/**
	 * Slug set on the instance used for uninstall telemetry.
	 *
	 * @var string|null
	 */
	private static $current_uninstall_slug = null;

	/**
	 * In-flight verification email dispatch (activate log with token).
	 *
	 * @var array{slug: string, force: bool}|null
	 */
	private static $verification_email_dispatch = null;

	/**
	 * Constructor for the Logger class.
	 *
	 * @param mixed $module_id Module id.
	 * @param mixed $slug      Product slug.
	 * @param bool  $is_init   Whether this is an init call.
	 */
	private function __construct( $module_id, $slug = false, $is_init = false ) {
		if ( ! $is_init && ! is_numeric( $module_id ) && ! is_string( $slug ) ) {
			return;
		}
	}

	/**
	 * Create or retrieve a Logger instance (singleton per slug).
	 *
	 * @param mixed $module_id Module id.
	 * @param mixed $slug      Product slug.
	 * @param bool  $is_init   Whether this is an init call.
	 * @return self|false
	 */
	public static function instance( $module_id, $slug = false, $is_init = false ) {
		if ( empty( $module_id ) ) {
			return false;
		}
		if ( ! $is_init && true === $slug ) {
			$is_init = true;
		}

		if ( ! isset( self::$instances[ $slug ] ) ) {
			self::$instances[ $slug ] = new WPBRIGADE_Logger( $module_id, $slug, $is_init );
		}

		return self::$instances[ $slug ];
	}


	/**
	 * Store module config and register WordPress hooks for this product.
	 *
	 * @param array<string, mixed> $module Module definition (slug, keys, settings, etc.).
	 * @return void
	 */
	public function wpb_init( array $module ) {
		if ( empty( $module['plugin_file'] ) || ! is_string( $module['plugin_file'] ) ) {
			return;
		}
		if ( ! file_exists( $module['plugin_file'] ) ) {
			return;
		}
		$key                        = $module['slug'];
		self::$product_data[ $key ] = array_merge(
			self::$product_data[ $key ] ?? array(),
			array(
				'module' => $module,
			)
		);
		if ( function_exists( 'wpb_sdk_store_uninstall_cleanup_manifest' ) ) {
			wpb_sdk_store_uninstall_cleanup_manifest( $module );
		}
		$this->hooks( $module['slug'] );
	}

	/**
	 * Restore in-memory module config when init was short-circuited but static data was lost.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	public static function wpb_sdk_store_module_if_missing( array $module ) {
		$key = isset( $module['slug'] ) ? (string) $module['slug'] : '';
		if ( '' === $key ) {
			return;
		}
		self::$product_data[ $key ] = array_merge(
			self::$product_data[ $key ] ?? array(),
			array(
				'module' => $module,
			)
		);

		if ( function_exists( 'wpb_sdk_store_module_in_registry' ) ) {
			wpb_sdk_store_module_in_registry( $module );
		}
		if ( function_exists( 'wpb_sdk_store_uninstall_cleanup_manifest' ) ) {
			wpb_sdk_store_uninstall_cleanup_manifest( $module );
		}
	}

	/**
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	private static function get_module_config( $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return array();
		}
		if (
			isset( self::$product_data[ $slug ]['module'] )
			&& is_array( self::$product_data[ $slug ]['module'] )
		) {
			return self::$product_data[ $slug ]['module'];
		}
		if ( function_exists( 'wpb_sdk_get_registered_module' ) ) {
			$module = wpb_sdk_get_registered_module( $slug );
			if ( is_array( $module ) && ! empty( $module ) ) {
				return $module;
			}
		}

		return array();
	}

	/**
	 * Lets the host plugin run wpb_sdk_dynamic_init() when the SDK needs module config early.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function bootstrap_module_if_needed( $slug ) {
		if ( ! empty( self::$product_data[ $slug ]['module'] ) ) {
			return;
		}
		if ( function_exists( 'wpb_sdk_get_registered_module' ) && ! empty( wpb_sdk_get_registered_module( $slug ) ) ) {
			return;
		}
		do_action( 'wpb_sdk_bootstrap_module', $slug );
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return array<string, mixed>
	 */
	private static function get_telemetry_config( array $module ) {
		$telemetry = isset( $module['telemetry'] ) && is_array( $module['telemetry'] )
			? $module['telemetry']
			: array();

		return wp_parse_args(
			$telemetry,
			array(
				'user_info_format'            => 'legacy',
				'optout_submit_key'           => '',
				'verification_token_ttl_days' => 14,
			)
		);
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return bool
	 */
	private static function uses_laravel_user_info( array $module ) {
		return 'laravel' === self::get_telemetry_config( $module )['user_info_format'];
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return bool
	 */
	private static function is_optout_form_submission( array $module ) {
		$key = (string) self::get_telemetry_config( $module )['optout_submit_key'];
		if ( '' === $key ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Product opt-out form uses its own nonce.
		return isset( $_POST[ $key ] );
	}

	/**
	 * Optional product lifecycle class (activate / deactivate / uninstall) from module config.
	 *
	 * @param string $slug  Product slug.
	 * @param string $which activate|deactivate|uninstall.
	 * @return void
	 */
	private static function run_module_lifecycle( $slug, $which ) {
		if ( ! in_array( $which, array( 'activate', 'deactivate', 'uninstall' ), true ) ) {
			return;
		}

		$module    = self::get_module_config( $slug );
		$lifecycle = isset( $module['lifecycle'] ) && is_array( $module['lifecycle'] ) ? $module['lifecycle'] : array();
		$class     = isset( $lifecycle['class'] ) ? (string) $lifecycle['class'] : '';
		if ( '' === $class ) {
			return;
		}

		$main_file = wpb_sdk_get_plugin_path( $slug );
		if ( ! file_exists( $main_file ) ) {
			return;
		}

		$plugin_dir = trailingslashit( dirname( $main_file ) );
		foreach ( (array) ( $lifecycle['bootstrap_files'] ?? array() ) as $relative ) {
			$path = $plugin_dir . ltrim( (string) $relative, '/\\' );
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}

		if ( 'uninstall' === $which && ! empty( $lifecycle['uninstall_requires'] ) ) {
			$required = $plugin_dir . ltrim( (string) $lifecycle['uninstall_requires'], '/\\' );
			if ( ! is_readable( $required ) ) {
				return;
			}
		}

		if ( ! class_exists( $class, false ) ) {
			return;
		}

		$args = isset( $lifecycle['constructor_args'] ) ? (array) $lifecycle['constructor_args'] : array( null );
		if ( 0 === count( $args ) ) {
			$instance = new $class();
		} elseif ( 1 === count( $args ) ) {
			$instance = new $class( $args[0] );
		} else {
			$ref      = new ReflectionClass( $class );
			$instance = $ref->newInstanceArgs( $args );
		}

		if ( is_object( $instance ) && is_callable( array( $instance, $which ) ) ) {
			call_user_func( array( $instance, $which ) );
		}
	}

	/**
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function register_lifecycle_uninstall_hook( $slug ) {
		$module = self::get_module_config( $slug );
		if ( empty( $module['lifecycle']['class'] ) || ! empty( self::$lifecycle_uninstall_hooked[ $slug ] ) ) {
			return;
		}

		self::$lifecycle_uninstall_hooked[ $slug ] = true;
		add_action(
			'wp_wpb_sdk_after_uninstall',
			static function ( $uninstall_slug ) use ( $slug ) {
				if ( $uninstall_slug !== $slug ) {
					return;
				}
				self::run_module_lifecycle( $slug, 'uninstall' );
			},
			5
		);
	}

	/**
	 * Singleton logger for a slug.
	 *
	 * @param string $slug Product slug.
	 * @return self|false
	 */
	private static function logger_for_slug( $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return false;
		}
		self::bootstrap_module_if_needed( $slug );
		$mid = isset( self::$product_data[ $slug ]['module']['id'] )
			? self::$product_data[ $slug ]['module']['id']
			: '1';
		if ( empty( $mid ) ) {
			$mid = '1';
		}
		return self::instance( $mid, $slug, true );
	}

	/**
	 * Activation hook entry (static so the callback survives any instance edge cases).
	 *
	 * @param bool $network_wide Whether the plugin is network-activated.
	 * @return void
	 */
	public static function telemetry_handle_activation( $network_wide = false, $slug = '' ) {
		unset( $network_wide );
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}
		self::ensure_module_registered_for_slug( $slug );
		// First opt-in yes/skip is sent from Opt_Manager AJAX; re-activation sends for opted-in users only.
		if ( function_exists( 'wpb_sdk_allows_ongoing_telemetry' ) && wpb_sdk_allows_ongoing_telemetry( $slug ) ) {
			$logger = self::logger_for_slug( $slug );
			if ( $logger ) {
				$logger->log_activation( $slug );
			}
		}
		self::run_module_lifecycle( $slug, 'activate' );
	}

	/**
	 * Deactivation hook entry (static so the callback survives any instance edge cases).
	 *
	 * @param bool $network_wide Whether the plugin is network-activated.
	 * @return void
	 */
	public static function telemetry_handle_deactivation( $network_wide = false, $slug = '' ) {
		unset( $network_wide );
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}
		self::ensure_module_registered_for_slug( $slug );
		$logger = self::logger_for_slug( $slug );
		if ( ! $logger ) {
			return;
		}
		$logger->product_deactivation( $slug );
		self::run_module_lifecycle( $slug, 'deactivate' );
	}


	/**
	 * Register cron, AJAX, activation/deactivation, and uninstall hooks.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function hooks( $slug ) {
		if ( ! empty( self::$hooked_slugs[ $slug ] ) ) {
			return;
		}
		self::$hooked_slugs[ $slug ] = true;

		$plugin_path = wpb_sdk_get_plugin_path( $slug );
		if ( empty( $plugin_path ) || ! file_exists( $plugin_path ) ) {
			return;
		}

		add_action(
			'init',
			function () use ( $slug ) {
				$this->set_logs_schedule( $slug );
			}
		);

		// Daily log plugin execution.
		add_action(
			'wpb_data_sync_' . $slug,
			function () use ( $slug ) {
				$this->daily_log_plugin( $slug );
			}
		);

		// Deactivation survey: load after footer scripts so jQuery exists on plugins.php.
		add_action(
			'admin_print_footer_scripts',
			function () use ( $slug ) {
				$this->deactivation_model( $slug );
			},
			100
		);

		// Some admin screens defer jQuery; modal script needs it before our inline block runs.
		add_action(
			'admin_enqueue_scripts',
			static function ( $hook_suffix ) {
				if ( 'plugins.php' !== $hook_suffix ) {
					return;
				}
				wp_enqueue_script( 'jquery' );
			}
		);

		// AJAX handler for plugin deactivation feedback (per-slug action avoids cross-plugin collision).
		add_action(
			'wp_ajax_wpb_sdk_' . $slug . '_deactivation',
			function () use ( $slug ) {
				$this->ajax_deactivation( $slug );
			}
		);

		// Real global callbacks only — PHP 8+ rejects __callStatic names in call_user_func_array().
		register_activation_hook( $plugin_path, 'wpb_sdk_run_product_activation' );
		register_deactivation_hook( $plugin_path, 'wpb_sdk_run_product_deactivation' );

		if ( function_exists( 'wpb_sdk_persist_lifecycle_basename_map' ) ) {
			wpb_sdk_persist_lifecycle_basename_map( $slug, $plugin_path );
		}
		register_uninstall_hook( $plugin_path, 'wpb_sdk_run_product_uninstall' );

		self::register_lifecycle_uninstall_hook( $slug );

		if ( class_exists( 'WPBRIGADE_Optin_Verification' ) ) {
			$module = isset( self::$product_data[ $slug ]['module'] ) ? self::$product_data[ $slug ]['module'] : array();
			if (
				! empty( $module['optin_user_meta']['token'] )
				&& function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
				&& '' !== wpb_sdk_email_verified_meta_key_from_module( $module )
			) {
				WPBRIGADE_Optin_Verification::register_module( $module );
			}
		}

		if ( class_exists( 'WPBRIGADE_Opt_Manager' ) ) {
			$module = isset( self::$product_data[ $slug ]['module'] ) ? self::$product_data[ $slug ]['module'] : array();
			if ( ! empty( $module['sdk_views_dir'] ) ) {
				WPBRIGADE_Opt_Manager::register_module( $module );
			}
		}
	}

	/**
	 * On init: optionally log opt-out submission, then ensure daily cron is scheduled.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function set_logs_schedule( $slug ) {
		$module = self::get_module_config( $slug );
		if ( function_exists( 'wpb_sdk_user_skipped_optin' ) && wpb_sdk_user_skipped_optin( $slug ) ) {
			self::remove_logs_schedule( $slug );
			if ( ! self::is_optout_form_submission( $module ) ) {
				return;
			}
		}
		if ( self::is_optout_form_submission( $module ) ) {
			$this->log_activation( $slug );
		}

		// Remove legacy cron hook names if present.
		wp_clear_scheduled_hook( 'wpb_logger_cron_' . $slug );
		wp_clear_scheduled_hook( 'wpb_daily_sync_cron_' . $slug );

		// Schedule the next daily sync if not already scheduled.
		$daily_start_time = strtotime( '+1 day' );

		if ( ! wp_next_scheduled( 'wpb_data_sync_' . $slug ) ) {
			wp_schedule_event( $daily_start_time, 'daily', 'wpb_data_sync_' . $slug );
		}
	}

	/**
	 * Reset daily sync cron hooks for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public static function reset_logs_schedule( $slug ) {
		wp_clear_scheduled_hook( 'wpb_logger_cron_' . $slug );
		wp_clear_scheduled_hook( 'wpb_daily_sync_cron_' . $slug );

		$daily_start_time = strtotime( '+1 day' );

		if ( ! wp_next_scheduled( 'wpb_data_sync_' . $slug ) ) {
			wp_schedule_event( $daily_start_time, 'daily', 'wpb_data_sync_' . $slug );
		}
	}

	/**
	 * Remove the daily sync event for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public static function remove_logs_schedule( $slug ) {
		wp_clear_scheduled_hook( 'wpb_data_sync_' . $slug );
	}


	/**
	 * Cron callback: send daily telemetry when SDK toggles allow it.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function daily_log_plugin( $slug ) {
		$logs_data = self::get_logs_data( $slug, 'daily' );

		if ( ! empty( $logs_data ) ) {
			$this->send_telemetry_with_explicit(
				$slug,
				$logs_data,
				array(
					'action' => 'daily',
				)
			);
		}
	}


	/**
	 * Send activation telemetry (used by register_activation_hook and opt-in flows).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function log_activation( $slug ) {
		if ( ! self::may_send_telemetry( $slug, 'activate' ) ) {
			return;
		}
		$logs_data = self::get_logs_data( $slug, 'activate' );
		$this->send_telemetry_with_explicit(
			$slug,
			$logs_data,
			array(
				'action' => 'activate',
			)
		);
	}

	/**
	 * Send activation telemetry with a verification token (initial or resend).
	 *
	 * @param string $slug            Product slug.
	 * @param bool   $force_new_token Replace any existing pending token first.
	 * @return void
	 */
	public function log_verification_email_request( $slug, $force_new_token = false ) {
		self::$verification_email_dispatch = array(
			'slug'  => (string) $slug,
			'force' => (bool) $force_new_token,
		);

		$this->log_activation( $slug );

		self::$verification_email_dispatch = null;
	}

	/**
	 * Basename used in plugins.php `plugin=` param (from active_plugins), not assumed `slug/slug.php`.
	 *
	 * @param string $slug Telemetry slug.
	 * @return string
	 */
	private static function resolve_plugin_basename_for_list_table( $slug ) {
		if ( function_exists( 'wpb_sdk_resolve_plugin_basename' ) ) {
			return wpb_sdk_resolve_plugin_basename( $slug );
		}

		$path = wpb_sdk_get_plugin_path( $slug );
		return file_exists( $path ) ? plugin_basename( $path ) : '';
	}

	/**
	 * Output the deactivation survey modal on the plugins screen.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function deactivation_model( $slug ) {
		global $pagenow;

		$on_plugins_screen = is_string( $pagenow ) && 'plugins.php' === $pagenow;
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen ) {
				$on_plugins_screen = $on_plugins_screen
					|| ( 'plugins.php' === $screen->parent_file )
					|| ( 'plugins' === $screen->id )
					|| ( 'plugins-network' === $screen->id );
			}
		}
		if ( ! $on_plugins_screen ) {
			return;
		}
		$plugin_data = wpb_sdk_get_plugin_details( $slug );
		if ( empty( $plugin_data ) || ! is_array( $plugin_data ) ) {
			return;
		}
		$module               = self::get_module_config( $slug );
		$product_name         = $plugin_data['Name'];
		$product_slug         = $slug;
		$plugin_file_basename = self::resolve_plugin_basename_for_list_table( $slug );
		include dirname( __DIR__ ) . '/views/wpb-sdk-deactivate-form.php';
	}


	/**
	 * AJAX: verify nonce, then log deactivation telemetry.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function ajax_deactivation( $slug ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified below.
		if ( ! isset( $_POST['nonce'] ) || '' === $_POST['nonce'] ) {
			wp_die( -1, '', array( 'response' => 403 ) );
		}

		$nonce        = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		$plugin_file  = self::resolve_plugin_basename_for_list_table( $slug );
		$verify_nonce = wp_verify_nonce( $nonce, 'deactivate-plugin_' . $plugin_file );

		if ( ! $verify_nonce ) {
			wp_die( -1, '', array( 'response' => 403 ) );
		}

		$this->log_deactivation( $slug );
		set_transient( self::deactivation_logged_transient_key( $slug ), 1, MINUTE_IN_SECONDS );

		wp_die();
	}


	/**
	 * Core deactivation hook: log telemetry and clear daily cron (no POST body).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function product_deactivation( $slug ) {
		if ( get_transient( self::deactivation_logged_transient_key( $slug ) ) ) {
			delete_transient( self::deactivation_logged_transient_key( $slug ) );
		} else {
			$this->log_deactivation( $slug );
		}
		wp_clear_scheduled_hook( 'wpb_data_sync_' . $slug );
	}

	/**
	 * Transient key: modal AJAX already sent deactivate telemetry for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private static function deactivation_logged_transient_key( $slug ) {
		return 'wpb_sdk_deact_logged_' . sanitize_key( $slug );
	}


	/**
	 * Build deactivation payload; reads optional reason fields when the modal POSTed them.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function log_deactivation( $slug ) {
		if ( ! self::may_send_telemetry( $slug, 'deactivate' ) ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce checked in ajax_deactivation(); core hook has no POST.
		$reason        = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['reason'] ) ) : '';
		$reason_detail = isset( $_POST['reason_detail'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['reason_detail'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		$logs_data = self::get_logs_data( $slug, 'deactivate' );
		if ( ! empty( $logs_data ) ) {
			$this->send_telemetry_with_explicit(
				$slug,
				$logs_data,
				array(
					'action'        => 'deactivate',
					'reason'        => sanitize_text_field( wp_unslash( $reason ) ),
					'reason_detail' => sanitize_text_field( wp_unslash( $reason_detail ) ),
				)
			);
		}
	}


	/**
	 * Resolve product slug from the plugin basename in the current delete request.
	 *
	 * @return string Product slug or empty string.
	 */
	private static function resolve_uninstall_slug_from_request() {
		if ( ! function_exists( 'wpb_sdk_resolve_product_slug_for_lifecycle' ) ) {
			return '';
		}

		$slug = wpb_sdk_resolve_product_slug_for_lifecycle();

		return is_string( $slug ) ? $slug : '';
	}

	/**
	 * Static uninstall callback for a specific product slug.
	 *
	 * @param string $slug Product slug being uninstalled.
	 * @return void
	 */
	public static function log_uninstallation_for_slug( $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}
		self::$current_uninstall_slug = $slug;
		self::log_uninstallation();
	}

	/**
	 * Static uninstall callback: send uninstall telemetry then fire after-uninstall action.
	 *
	 * @param mixed $_unused Reserved for WordPress uninstall hook signature compatibility.
	 * @return void
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- WordPress uninstall hook signature.
	public static function log_uninstallation( $_unused = '' ) {
		unset( $_unused );
		$slug = self::$current_uninstall_slug;
		if ( ! is_string( $slug ) || '' === $slug ) {
			$slug = self::resolve_uninstall_slug_from_request();
		}
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}

		$mid = self::$module_id;
		if ( empty( $mid ) && ! empty( self::$product_data[ $slug ]['module']['id'] ) ) {
			$mid = self::$product_data[ $slug ]['module']['id'];
		}
		if ( empty( $mid ) ) {
			$mid = '1';
		}

		self::$current_uninstall_slug = $slug;
		self::$module_id              = $mid;

		self::bootstrap_module_if_needed( $slug );

		$logger = self::instance( $mid, $slug, true );
		if ( ! $logger ) {
			do_action( 'wp_wpb_sdk_after_uninstall', $slug );
			return;
		}

		if ( ! self::may_send_telemetry( $slug, 'uninstall' ) ) {
			do_action( 'wp_wpb_sdk_after_uninstall', $slug );
			return;
		}

		$logs_data = self::get_logs_data( $slug, 'uninstall' );

		if ( ! empty( $logs_data ) ) {
			$logger->send_telemetry_with_explicit(
				$slug,
				$logs_data,
				array(
					'action' => 'uninstall',
				)
			);
		}

		// Allow products to remove options after uninstall telemetry.
		do_action( 'wp_wpb_sdk_after_uninstall', $slug );
	}


	/**
	 * Restore module config on lifecycle hooks (e.g. plugin re-activated after deactivate).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function ensure_module_registered_for_slug( $slug ) {
		self::bootstrap_module_if_needed( $slug );

		$module = array();
		if ( ! empty( self::$product_data[ $slug ]['module'] ) ) {
			$module = self::$product_data[ $slug ]['module'];
		} elseif ( function_exists( 'wpb_sdk_get_registered_module' ) ) {
			$module = wpb_sdk_get_registered_module( $slug );
			if ( ! empty( $module ) && function_exists( 'wpb_sdk_store_module_if_missing_compat' ) ) {
				wpb_sdk_store_module_if_missing_compat( $module );
			}
		}

		if ( empty( $module ) ) {
			return;
		}

		$module_id = isset( $module['id'] ) ? $module['id'] : '1';
		$logger    = self::instance( $module_id, $slug, true );
		if ( $logger ) {
			$logger->wpb_init( $module );
		}
	}

	/**
	 * Only the first opt-in "activate" may include a token (one verification email).
	 *
	 * @param string               $slug    Product slug.
	 * @param string               $action  Telemetry action.
	 * @param array<string, mixed> $module  Module definition.
	 * @param int                  $user_id Admin user ID.
	 * @return bool
	 */
	/**
	 * Opt-in initiator when still valid; otherwise primary administrator.
	 *
	 * @param string $slug Product slug.
	 * @return WP_User|null
	 */
	private static function resolve_telemetry_contact_user( $slug ) {
		if ( function_exists( 'wpb_sdk_get_telemetry_contact_user' ) ) {
			$user = wpb_sdk_get_telemetry_contact_user( $slug );
			if ( $user instanceof WP_User ) {
				return $user;
			}
		}

		return null;
	}

	private static function should_include_verification_token( $slug, $action, array $module, $user_id = 0 ) {
		if ( empty( $module['optin_user_meta']['token'] ) ) {
			return false;
		}
		if ( function_exists( 'wpb_sdk_user_skipped_optin' ) && wpb_sdk_user_skipped_optin( $slug ) ) {
			return false;
		}
		if ( 'activate' !== $action ) {
			return false;
		}
		if ( function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $user_id ) ) {
			return false;
		}

		if ( self::is_verification_email_dispatch( $slug ) ) {
			return true;
		}

		// First opt-in Allow: one verification email.
		if ( '1' !== (string) get_option( self::wpb_sdk_initial_log_option_key( $slug ), '' ) ) {
			return true;
		}

		// Plugin re-activated while still unverified: reminder only after token TTL (Freemius-style, not every toggle).
		if ( function_exists( 'wpb_sdk_is_verification_token_expired' ) ) {
			return wpb_sdk_is_verification_token_expired( $slug, $user_id );
		}

		return false;
	}

	/**
	 * Unverified: token + empty email_verification string. Verified: email_verification "yes", no token.
	 *
	 * @param int                  $user_id WordPress user ID for the telemetry contact, or 0 during non-interactive sends.
	 * @param string               $slug    Product slug.
	 * @param array<string, mixed> $module  Module definition.
	 * @return string Non-empty token, or empty string on failure.
	 */
	private static function ensure_verification_token( $user_id, $slug, array $module ) {
		$user_id    = (int) $user_id;
		$token_meta = ! empty( $module['optin_user_meta']['token'] )
			? (string) $module['optin_user_meta']['token']
			: '';

		if ( $user_id > 0 && '' !== $token_meta ) {
			$expires_meta = function_exists( 'wpb_sdk_verification_token_expires_meta_key' )
				? wpb_sdk_verification_token_expires_meta_key( $module )
				: $token_meta . '_expires';
			$existing     = get_user_meta( $user_id, $token_meta, true );
			$expires_at   = function_exists( 'wpb_sdk_ensure_verification_token_expiry_meta' )
				? wpb_sdk_ensure_verification_token_expiry_meta( $slug, $user_id )
				: (int) get_user_meta( $user_id, $expires_meta, true );
			$is_expired   = $expires_at > 0 && time() > $expires_at;

			$force_new = self::is_verification_email_dispatch( $slug )
				&& ! empty( self::$verification_email_dispatch['force'] );

			if ( ! $force_new && is_string( $existing ) && '' !== $existing && ! $is_expired ) {
				return $existing;
			}

			$token = wp_generate_password( 40, false, false );
			update_user_meta( $user_id, $token_meta, $token );
			if ( function_exists( 'wpb_sdk_verification_token_expires_at' ) ) {
				update_user_meta( $user_id, $expires_meta, wpb_sdk_verification_token_expires_at( $module ) );
			}

			return $token;
		}

		$opt_key = 'wpb_sdk_' . $slug . '_fallback_verify_token';
		$stored  = get_option( $opt_key, '' );
		if ( is_string( $stored ) && '' !== $stored ) {
			return $stored;
		}
		$token = wp_generate_password( 40, false, false );
		update_option( $opt_key, $token, false );
		return $token;
	}

	/**
	 * Best-effort MySQL server version string for telemetry (may use mysqli handle).
	 *
	 * @param \wpdb $wpdb WordPress database object.
	 * @return string
	 */
	private static function wpb_sdk_get_mysql_server_version( $wpdb ) {
		if ( ! method_exists( $wpdb, 'db_version' ) ) {
			return 'unknown';
		}
		if ( function_exists( 'mysqli_get_server_info' ) && isset( $wpdb->dbh ) && $wpdb->dbh instanceof mysqli ) {
			// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysqli_get_server_info,WordPress.DB.DirectDatabaseQuery.DirectQuery -- Telemetry diagnostic only.
			return mysqli_get_server_info( $wpdb->dbh );
		}
		return $wpdb->db_version();
	}

	/**
	 * Shared site_info diagnostics (site_meta_info + location_details) for all WPB SDK collectors.
	 *
	 * @param \wpdb       $wpdb WordPress database object.
	 * @param \WP_Theme   $theme_data Output of wp_get_theme().
	 * @param string      $ip Client IP.
	 * @param mixed       $location Result from get_location_details().
	 * @param array|false $multisites Result from get_multisites().
	 * @return array{site_meta_info: array, location_details: mixed}
	 */
	private static function build_site_info_diagnostics( $wpdb, $theme_data, $ip, $location, $multisites ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$curl_version          = '';
		$external_http_blocked = '';
		$users_count           = '';

		if ( function_exists( 'count_users' ) ) {
			$uc          = count_users();
			$users_count = isset( $uc['total_users'] ) ? (int) $uc['total_users'] : '';
		}

		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL ) {
			$external_http_blocked = 'none';
		} else {
			$external_http_blocked = defined( 'WP_ACCESSIBLE_HOSTS' )
				? 'partially (accessible hosts: ' . esc_html( WP_ACCESSIBLE_HOSTS ) . ')'
				: 'all';
		}

		if ( function_exists( 'curl_init' ) ) {
			$curl         = curl_version();
			$curl_version = '(' . $curl['version'] . ' ' . $curl['ssl_version'] . ')';
		}

		$site_meta_info = array(
			'is_multisite'          => is_multisite(),
			'multisites'            => $multisites,
			'php_version'           => phpversion(),
			'wp_version'            => get_bloginfo( 'version' ),
			'server'                => isset( $_SERVER['SERVER_SOFTWARE'] )
				? sanitize_text_field( wp_unslash( (string) $_SERVER['SERVER_SOFTWARE'] ) )
				: '',
			'timezoneoffset'        => gmdate( 'P' ),
			'ext/mysqli'            => isset( $wpdb->use_mysqli ) && ! empty( $wpdb->use_mysqli ),
			'mysql_version'         => self::wpb_sdk_get_mysql_server_version( $wpdb ),
			'memory_limit'          => defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : ini_get( 'memory_limit' ),
			'external_http_blocked' => $external_http_blocked,
			'wp_locale'             => get_locale(),
			'db_charset'            => defined( 'DB_CHARSET' ) ? DB_CHARSET : '',
			'debug_mode'            => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'wp_max_upload'         => size_format( wp_max_upload_size() ),
			'php_time_limit'        => function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : '',
			'php_error_log'         => function_exists( 'ini_get' ) ? ini_get( 'error_log' ) : '',
			'fsockopen'             => function_exists( 'fsockopen' ),
			'open_ssl'              => defined( 'OPENSSL_VERSION_TEXT' ) ? OPENSSL_VERSION_TEXT : '',
			'curl'                  => $curl_version,
			'ip'                    => $ip,
			'user_count'            => $users_count,
			'admin_email'           => sanitize_email( get_bloginfo( 'admin_email' ) ),
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- WP_Theme API uses Name and Version.
			'theme_name'            => sanitize_text_field( $theme_data->Name ),
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- WP_Theme API uses Name and Version.
			'theme_version'         => sanitize_text_field( $theme_data->Version ),
		);

		return array(
			'site_meta_info'   => $site_meta_info,
			'location_details' => null !== $location ? $location : '',
		);
	}

	/**
	 * Shared entry: cron/activation call this statically.
	 *
	 * @param string $slug Product slug.
	 * @param string $action Telemetry action.
	 * @return array
	 */
	public static function get_logs_data( $slug, $action = '' ) {
		self::bootstrap_module_if_needed( $slug );
		if ( empty( self::$product_data[ $slug ]['module'] ) ) {
			return array();
		}
		$mid = self::$product_data[ $slug ]['module']['id'] ?? '1';
		if ( empty( $mid ) ) {
			$mid = 1;
		}
		$inst = self::instance( $mid, $slug, true );
		return $inst->collect_logs_data( $slug, $action );
	}

	/**
	 * Forward for SDK views that still call $logger->get_logs_data().
	 *
	 * @param string $name Method name.
	 * @param array  $args Arguments.
	 * @return mixed
	 */
	public function __call( $name, $args ) {
		if ( 'get_logs_data' === $name ) {
			return self::get_logs_data( isset( $args[0] ) ? $args[0] : '', isset( $args[1] ) ? $args[1] : '' );
		}
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Intentional guard for unsupported magic calls.
		trigger_error( 'Call to undefined method ' . __CLASS__ . '::' . esc_html( $name ), E_USER_WARNING );
	}

	/**
	 * Build telemetry payload from module config (user_info format, opt-out key, etc.).
	 *
	 * @param string $slug Product slug.
	 * @param string $action Telemetry action.
	 * @return array
	 */
	private function collect_logs_data( $slug, $action = '' ) {
		global $wpdb;

		if ( empty( self::$product_data[ $slug ]['module'] ) ) {
			return array();
		}

		$module         = self::$product_data[ $slug ]['module'];
		$laravel_format = self::uses_laravel_user_info( $module );
		$data           = array();
		$theme_data     = wp_get_theme();

		$sdk_data = json_decode( get_option( 'wpb_sdk_' . $slug ), true );
		if ( ! is_array( $sdk_data ) ) {
			$sdk_data = array();
		}
		$send_wpb_sdk_communication   = isset( $sdk_data['communication'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['communication'] );
		$send_wpb_sdk_diagnostic_info = isset( $sdk_data['diagnostic_info'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['diagnostic_info'] );
		$send_wpb_sdk_extensions      = isset( $sdk_data['extensions'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['extensions'] );
		$user_skipped                 = isset( $sdk_data['user_skip'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['user_skip'] );

		if (
			! $send_wpb_sdk_communication
			&& ! $send_wpb_sdk_diagnostic_info
			&& ! $send_wpb_sdk_extensions
		) {
			self::remove_logs_schedule( $slug );
			return array();
		}
		self::reset_logs_schedule( $slug );

		$data['authentication']['public_key'] = $module['public_key'];

		$include_user_info = $send_wpb_sdk_communication;

		if ( $include_user_info ) {
			if ( $laravel_format ) {
				$admin_user_id = 0;
				$admin_obj     = self::resolve_telemetry_contact_user( $slug );
				$admin_meta    = array();

				if ( $admin_obj instanceof WP_User ) {
					$admin_user_id = (int) $admin_obj->ID;
					$admin_meta    = get_user_meta( $admin_user_id );
				}

				$first = isset( $admin_meta['first_name'][0] )
					? sanitize_text_field( (string) $admin_meta['first_name'][0] )
					: '';
				$last  = isset( $admin_meta['last_name'][0] )
					? sanitize_text_field( (string) $admin_meta['last_name'][0] )
					: '';
				$email = ( $admin_obj instanceof WP_User )
					? sanitize_email( $admin_obj->user_email )
					: '';
				if ( '' === $email && function_exists( 'wpb_sdk_resolve_optin_admin_user_id' ) ) {
					$fallback_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
					if ( $fallback_id > 0 ) {
						$fallback_user = get_user_by( 'id', $fallback_id );
						if ( $fallback_user instanceof WP_User ) {
							$email = sanitize_email( $fallback_user->user_email );
						}
					}
				}
				if ( '' === $email ) {
					$email = sanitize_email( (string) get_bloginfo( 'admin_email' ) );
				}

				$display_name = '';
				if ( $admin_obj instanceof WP_User ) {
					$display_name = sanitize_text_field( (string) $admin_obj->display_name );
					if ( '' === $display_name ) {
						$display_name = sanitize_text_field( (string) $admin_obj->user_login );
					}
				}
				if ( '' === $display_name ) {
					$display_name = trim( $first . ' ' . $last );
				}
				if ( '' === $display_name && '' !== $email ) {
					$local        = strstr( $email, '@', true );
					$display_name = false !== $local ? $local : '';
				}

				$payload_verified = function_exists( 'wpb_sdk_is_user_verified' )
					&& wpb_sdk_is_user_verified( $slug, $admin_user_id );

				$data['user_info'] = array(
					'user_email' => $email,
					'name'       => $display_name,
				);
				if ( $user_skipped ) {
					$data['user_info']['email_verification'] = 'skip';
				} elseif ( $payload_verified ) {
					$data['user_info']['email_verification'] = 'yes';
				} elseif ( self::should_include_verification_token( $slug, $action, $module, $admin_user_id ) ) {
					$data['user_info']['email_verification'] = '';
					$data['user_info']['token']              = self::ensure_verification_token( $admin_user_id, $slug, $module );
				} else {
					$data['user_info']['email_verification'] = 'pending';
				}
			} else {
				$contact_user = self::resolve_telemetry_contact_user( $slug );
				$admin        = ( $contact_user instanceof WP_User ) ? $contact_user->data : null;
				$admin_meta   = ( $contact_user instanceof WP_User ) ? get_user_meta( $contact_user->ID ) : array();

				$data['user_info'] = array(
					'user_email'     => ! empty( $admin ) ? sanitize_email( $admin->user_email ) : '',
					'user_nickname'  => ! empty( $admin ) ? sanitize_text_field( $admin->user_nicename ) : '',
					'user_firstname' => isset( $admin_meta['first_name'][0] )
						? sanitize_text_field( (string) $admin_meta['first_name'][0] )
						: '',
					'user_lastname'  => isset( $admin_meta['last_name'][0] )
						? sanitize_text_field( (string) $admin_meta['last_name'][0] )
						: '',
				);

				if (
					! empty( $module['optin_user_meta']['token'] )
					&& function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
					&& '' !== wpb_sdk_email_verified_meta_key_from_module( $module )
				) {
					$token_uid = ! empty( $admin ) ? (int) $admin->ID : 0;
					if ( $token_uid < 1 && function_exists( 'wpb_sdk_resolve_telemetry_contact_user_id' ) ) {
						$token_uid = wpb_sdk_resolve_telemetry_contact_user_id( $slug );
					}
					$payload_verified = ( $token_uid > 0 )
						&& function_exists( 'wpb_sdk_is_user_verified' )
						&& wpb_sdk_is_user_verified( $slug, $token_uid );

					if ( $user_skipped ) {
						$data['user_info']['email_verification'] = 'skip';
					} elseif ( $payload_verified ) {
						$data['user_info']['email_verification'] = 'yes';
					} elseif ( self::should_include_verification_token( $slug, $action, $module, $token_uid ) ) {
						$verification_token = $token_uid > 0
							? self::ensure_verification_token( $token_uid, $slug, $module )
							: '';
						$data['user_info']['email_verification'] = '';
						$data['user_info']['token']              = is_string( $verification_token ) ? $verification_token : '';
					} else {
						$data['user_info']['email_verification'] = 'pending';
					}
				}
			}
		}

		$data['product_info']                = $this->get_product_data( $slug );
		$data['product_info']['sdk_version'] = WP_WPBRIGADE_SDK_VERSION;

		if ( function_exists( 'wpb_sdk_get_client_info' ) ) {
			$data['client_info'] = wpb_sdk_get_client_info();
		}

		if ( $send_wpb_sdk_diagnostic_info ) {
			$data['product_info']['product_settings'] = $this->get_product_settings( $slug );

			$data['site_info'] = array(
				'site_url' => site_url(),
				'home_url' => home_url(),
			);

			$ip                                    = $this->get_ip();
			$location                              = $this->get_location_details( $ip );
			$diag                                  = self::build_site_info_diagnostics(
				$wpdb,
				$theme_data,
				$ip,
				$location,
				$this->get_multisites()
			);
			$data['site_info']['site_meta_info']   = $diag['site_meta_info'];
			$data['site_info']['location_details'] = $diag['location_details'];
		}

		if ( $send_wpb_sdk_extensions ) {
			$data['site_plugins'] = $this->get_all_plugins();
		}

		return $data;
	}



	/**
	 * Retrieve plugin settings related to the product.
	 *
	 * @param string $slug The slug of the product.
	 * @return array
	 */
	private function get_product_settings( $slug ) {
		$product_data   = self::$product_data[ $slug ]['module'] ?? array();
		$plugin_options = array();

		// Pull settings data from db.
		foreach ( $product_data['settings'] as $option_name => $default_value ) {
			$get_option       = get_option( $option_name );
			$plugin_options[] = array(
				'option' => $option_name,
				'value'  => ! empty( $get_option ) ? wp_json_encode( $get_option ) : $default_value,
			);
		}

		return $plugin_options;
	}


	/**
	 * Collect multisite data.
	 *
	 * @return array|false
	 */
	private function get_multisites() {
		if ( ! is_multisite() ) {
			return false;
		}

		$sites_info = array();
		$sites      = get_sites();

		foreach ( $sites as $site ) {
			$sites_info[ $site->blog_id ] = array(
				'name'   => get_blog_details( $site->blog_id )->blogname,
				'domain' => $site->domain,
				'path'   => $site->path,
			);
		}

		return $sites_info;
	}


	/**
	 * Get user IP information.
	 *
	 * @return string|null
	 */
	private function get_ip() {
		if ( function_exists( 'wpb_sdk_get_client_ip' ) ) {
			$ip = wpb_sdk_get_client_ip();
			return '' !== $ip ? $ip : null;
		}

		$fields = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $fields as $ip_field ) {
			if ( ! empty( $_SERVER[ $ip_field ] ) ) {
				return sanitize_text_field( wp_unslash( (string) $_SERVER[ $ip_field ] ) );
			}
		}

		return null;
	}

	/**
	 * Collect plugins information: Active/Inactive plugins.
	 *
	 * @return array
	 */
	private function get_all_plugins() {
		$all_plugins      = array_keys( get_plugins() );
		$active_plugins   = get_option( 'active_plugins', array() );
		$inactive_plugins = array_diff( $all_plugins, $active_plugins );

		return array(
			'active'   => $active_plugins,
			'inactive' => $inactive_plugins,
		);
	}

	/**
	 * Get location details based on IP.
	 *
	 * @param string|null $ip IP address or null.
	 * @return array<string, mixed>
	 */
	private function get_location_details( $ip ) {
		$location_details = array();
		if ( ! $ip ) {
			return array(
				'response_code' => '400',
				'message'       => 'Error: IP address is required.',
			);
		}

		try {
			// phpcs:disable WordPress.WP.AlternativeFunctions.curl_curl_init,WordPress.WP.AlternativeFunctions.curl_curl_setopt,WordPress.WP.AlternativeFunctions.curl_curl_exec -- Third-party geo API.
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "https://api.iplocation.net/?ip={$ip}" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$execute = curl_exec( $ch );
			// phpcs:enable WordPress.WP.AlternativeFunctions.curl_curl_init,WordPress.WP.AlternativeFunctions.curl_curl_setopt,WordPress.WP.AlternativeFunctions.curl_curl_exec

			$result = json_decode( $execute );

			if ( $result && '200' === (string) $result->response_code ) {
				if ( '-' !== $result->country_name && '-' !== $result->country_code2 ) {
					$location_details = array(
						'response_code' => $result->response_code,
						'message'       => 'Success',
						'data'          => array(
							'country_name' => $result->country_name,
							'country_code' => $result->country_code2,
						),
					);
				} else {
					$missing_info = array();
					if ( '-' === $result->country_name ) {
						$missing_info[] = 'country_name';
					}
					if ( '-' === $result->country_code2 ) {
						$missing_info[] = 'country_code';
					}
					$location_details = array(
						'response_code' => '400',
						'message'       => 'Error: Missing information for ' . implode( ', ', $missing_info ) . ' for the IP Address: ' . $ip,
					);
				}
			} else {
				$location_details = array(
					'response_code' => '400',
					'message'       => 'Error: Invalid response code or data for the IP Address: ' . $ip,
				);
			}

			return $location_details;
		} catch ( \Exception $e ) {
			return array(
				'response_code' => '400',
				'message'       => 'Error: ' . $e->getMessage(),
			);
		}
	}

	/**
	 * Get product data.
	 *
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	private function get_product_data( $slug ) {
		$plugin_data = wpb_sdk_get_plugin_details( $slug );
		$mod         = self::$product_data[ $slug ]['module'] ?? array();
		$pid         = $mod['id'] ?? '1';
		return array(
			'name'    => $plugin_data['Name'] ?? $plugin_data['Title'],
			'slug'    => $slug,
			'id'      => $pid,
			'type'    => 'Plugin',
			'path'    => wpb_sdk_get_plugin_path( $slug ),
			'version' => $plugin_data['Version'],
		);
	}

	/**
	 * Option: first successful logger payload for this slug (per site).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private static function wpb_sdk_initial_log_option_key( $slug ) {
		return 'wpb_sdk_' . $slug . '_initial_log_sent';
	}

	/**
	 * Whether an explicit verification-email dispatch is in progress for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	private static function is_verification_email_dispatch( $slug ) {
		return is_array( self::$verification_email_dispatch )
			&& (string) self::$verification_email_dispatch['slug'] === (string) $slug;
	}

	/**
	 * Whether telemetry may be sent for this product and action.
	 *
	 * @param string $slug   Product slug.
	 * @param string $action activate|deactivate|uninstall|daily|''.
	 * @return bool
	 */
	private static function may_send_telemetry( $slug, $action = '' ) {
		if ( function_exists( 'wpb_sdk_may_send_telemetry_action' ) ) {
			return wpb_sdk_may_send_telemetry_action( $slug, $action );
		}

		if ( function_exists( 'wpb_sdk_has_telemetry_consent' ) ) {
			return wpb_sdk_has_telemetry_consent( $slug );
		}

		return true;
	}

	/**
	 * Merge explicit_logs into payload and send (no-op when logs_data is empty).
	 *
	 * @param string               $slug Product slug.
	 * @param array<string, mixed> $logs_data Collected payload.
	 * @param array<string, mixed> $explicit_logs Keys for explicit_logs (must include action).
	 * @return void
	 */
	private function send_telemetry_with_explicit( $slug, $logs_data, array $explicit_logs ) {
		$action = isset( $explicit_logs['action'] ) ? (string) $explicit_logs['action'] : '';
		if ( empty( $logs_data ) || ! self::may_send_telemetry( $slug, $action ) ) {
			return;
		}
		$this->send(
			$slug,
			array_merge(
				$logs_data,
				array(
					'explicit_logs' => $explicit_logs,
				)
			)
		);
	}

	/**
	 * Send log data to the API.
	 *
	 * @param string $slug The product slug.
	 * @param array  $payload The log data payload.
	 */
	private function send( $slug, $payload ) {
		$payload['sent_at'] = current_time( 'mysql', 1 );

		$initial_key           = self::wpb_sdk_initial_log_option_key( $slug );
		$first_done            = get_option( $initial_key, '' );
		$payload['log_status'] = ( '1' === (string) $first_done ) ? 'returning' : 'new';

		$this->send_data_to_api( $slug, $payload );
	}


	/**
	 * API base URL for a product (each bundled copy may use a different endpoint).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private function resolve_api_endpoint_for_slug( $slug ) {
		$module = self::get_module_config( $slug );
		if ( ! empty( $module['api_endpoint'] ) ) {
			$endpoint = (string) $module['api_endpoint'];
		} elseif ( function_exists( 'wpb_sdk_get_provider_for_slug' ) ) {
			$provider = wpb_sdk_get_provider_for_slug( $slug );
			$endpoint = ! empty( $provider['api_endpoint'] ) ? (string) $provider['api_endpoint'] : '';
		} else {
			$endpoint = '';
		}

		if ( '' === $endpoint && defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
			$endpoint = (string) WPBRIGADE_SDK_API_ENDPOINT;
		}

		$endpoint = rtrim( $endpoint, '/' );

		/**
		 * Filter telemetry API base URL for a product.
		 *
		 * @param string $endpoint Base URL (no trailing slash).
		 * @param string $slug     Product slug.
		 */
		return (string) apply_filters( 'wpb_sdk_api_endpoint', $endpoint, $slug );
	}

	private function send_data_to_api( $slug, $data ) {
		$token = self::$product_data[ $slug ]['module']['public_key'] ?? null;

		if ( ! $token ) {
			return;
		}

		$api_endpoint = $this->resolve_api_endpoint_for_slug( $slug );
		if ( '' === $api_endpoint ) {
			return;
		}

		$response = wp_remote_post(
			$api_endpoint . '/logger',
			array(
				'method'  => 'POST',
				'body'    => wp_json_encode( $data ),
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Content-Type'  => 'application/json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code >= 200 && $code < 300 && isset( $data['log_status'] ) && 'new' === $data['log_status'] ) {
			update_option( self::wpb_sdk_initial_log_option_key( $slug ), '1', false );
		}
		if (
			$code >= 200
			&& $code < 300
			&& function_exists( 'wpb_sdk_user_skipped_optin' )
			&& wpb_sdk_user_skipped_optin( $slug )
		) {
			self::remove_logs_schedule( $slug );
		}
	}
}
