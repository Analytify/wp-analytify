<?php
/**
 * WPBrigade Telemetry SDK bootstrap broker.
 *
 * @package wpbrigade_sdk
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WPBRIGADE_SDK_VERSION' ) ) {
	require_once __DIR__ . '/config.php';
}

$wpb_sdk_version = WPBRIGADE_SDK_VERSION;

if ( ! function_exists( 'wpb_sdk_register_provider' ) ) {
	/**
	 * Register an SDK provider from a plugin bundle.
	 *
	 * @param array $provider Provider payload.
	 * @return void
	 */
	function wpb_sdk_register_provider( $provider ) {
		if ( ! isset( $GLOBALS['wpb_sdk_registry'] ) || ! is_array( $GLOBALS['wpb_sdk_registry'] ) ) {
			$GLOBALS['wpb_sdk_registry'] = array(
				'providers'           => array(),
				'active_provider'     => null,
				'runtime_loaded'      => false,
				'initialized_modules' => array(),
			);
		}

		$key = isset( $provider['provider_key'] ) ? $provider['provider_key'] : md5( wp_json_encode( $provider ) );
		$GLOBALS['wpb_sdk_registry']['providers'][ $key ] = $provider;
	}

	/**
	 * Select the active runtime provider.
	 *
	 * @return array|null
	 */
	function wpb_sdk_select_provider() {
		$registry = isset( $GLOBALS['wpb_sdk_registry'] ) ? $GLOBALS['wpb_sdk_registry'] : null;
		if ( empty( $registry['providers'] ) ) {
			return null;
		}

		if ( ! empty( $registry['active_provider'] ) ) {
			return $registry['active_provider'];
		}

		$providers = array_values( $registry['providers'] );
		usort(
			$providers,
			function ( $left, $right ) {
				$left_ver  = isset( $left['sdk_version'] ) ? (string) $left['sdk_version'] : '0';
				$right_ver = isset( $right['sdk_version'] ) ? (string) $right['sdk_version'] : '0';
				$by_ver    = version_compare( $right_ver, $left_ver );
				if ( 0 !== $by_ver ) {
					return $by_ver;
				}
				$left_key  = isset( $left['provider_key'] ) ? (string) $left['provider_key'] : '';
				$right_key = isset( $right['provider_key'] ) ? (string) $right['provider_key'] : '';
				return strcmp( $left_key, $right_key );
			}
		);

		$GLOBALS['wpb_sdk_registry']['active_provider'] = $providers[0];
		return $providers[0];
	}

	/**
	 * Load the selected runtime exactly once.
	 *
	 * @return bool
	 */
	function wpb_sdk_ensure_runtime_loaded() {
		if ( function_exists( 'wpb_sdk_runtime_is_complete' ) && wpb_sdk_runtime_is_complete() ) {
			if ( isset( $GLOBALS['wpb_sdk_registry'] ) && is_array( $GLOBALS['wpb_sdk_registry'] ) ) {
				$GLOBALS['wpb_sdk_registry']['runtime_loaded'] = true;
			}
			return true;
		}

		$provider = wpb_sdk_select_provider();
		if ( empty( $provider ) || empty( $provider['runtime_file'] ) ) {
			return false;
		}

		require_once $provider['runtime_file'];

		if ( ! defined( 'WP_WPBRIGADE_SDK_VERSION' ) ) {
			$runtime_version = ! empty( $provider['sdk_version'] )
				? (string) $provider['sdk_version']
				: ( defined( 'WPBRIGADE_SDK_VERSION' ) ? WPBRIGADE_SDK_VERSION : '3.2.0' );
			define( 'WP_WPBRIGADE_SDK_VERSION', $runtime_version );
		}

		$complete = function_exists( 'wpb_sdk_runtime_is_complete' ) && wpb_sdk_runtime_is_complete();
		if ( $complete && isset( $GLOBALS['wpb_sdk_registry'] ) && is_array( $GLOBALS['wpb_sdk_registry'] ) ) {
			$GLOBALS['wpb_sdk_registry']['runtime_loaded'] = true;
		}

		return $complete;
	}

	/**
	 * Shared dynamic initializer used by all bundled SDK copies.
	 *
	 * @param array $module Module configuration.
	 * @return array|false
	 */
	function wpb_sdk_dynamic_init( $module ) {
		if ( ! is_array( $module ) || empty( $module['slug'] ) || empty( $module['id'] ) ) {
			return false;
		}

		if ( ! wpb_sdk_ensure_runtime_loaded() || ! class_exists( 'WPBRIGADE_Logger' ) ) {
			return false;
		}

		if ( function_exists( 'wpb_sdk_apply_module_defaults' ) ) {
			$module = wpb_sdk_apply_module_defaults( $module );
		}

		$slug = $module['slug'];

		if ( ! isset( $GLOBALS['wpb_sdk_registry']['modules'] ) || ! is_array( $GLOBALS['wpb_sdk_registry']['modules'] ) ) {
			$GLOBALS['wpb_sdk_registry']['modules'] = array();
		}
		$GLOBALS['wpb_sdk_registry']['modules'][ $slug ] = $module;

		if (
			isset( $GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ] ) &&
			true === $GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ]
		) {
			if ( class_exists( 'WPBRIGADE_Logger', false ) ) {
				if ( function_exists( 'wpb_sdk_store_module_if_missing_compat' ) ) {
					wpb_sdk_store_module_if_missing_compat( $module );
				} elseif ( method_exists( 'WPBRIGADE_Logger', 'wpb_sdk_store_module_if_missing' ) ) {
					WPBRIGADE_Logger::wpb_sdk_store_module_if_missing( $module );
				}
			}
			if ( function_exists( 'wpb_sdk_register_opt_manager_for_module' ) ) {
				wpb_sdk_register_opt_manager_for_module( $module );
			}
			if ( function_exists( 'wpb_sdk_enqueue_optin_initiator_backfill' ) ) {
				wpb_sdk_enqueue_optin_initiator_backfill( $slug );
			}
			if ( function_exists( 'wpb_sdk_maybe_normalize_legacy_sdk_sharing_option' ) ) {
				wpb_sdk_maybe_normalize_legacy_sdk_sharing_option( $slug );
			}
			$logger = WPBRIGADE_Logger::instance( $module['id'], $slug, true );
			return array(
				'logger' => $logger,
				'slug'   => $slug,
				'id'     => $module['id'],
			);
		}

		$logger = WPBRIGADE_Logger::instance( $module['id'], $slug, true );
		$logger->wpb_init( $module );

		if ( function_exists( 'wpb_sdk_register_opt_manager_for_module' ) ) {
			wpb_sdk_register_opt_manager_for_module( $module );
		}

		if ( function_exists( 'wpb_sdk_enqueue_optin_initiator_backfill' ) ) {
			wpb_sdk_enqueue_optin_initiator_backfill( $slug );
		}

		if ( function_exists( 'wpb_sdk_maybe_normalize_legacy_sdk_sharing_option' ) ) {
			wpb_sdk_maybe_normalize_legacy_sdk_sharing_option( $slug );
		}

		$GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ] = true;

		return array(
			'logger' => $logger,
			'slug'   => $slug,
			'id'     => $module['id'],
		);
	}

	/**
	 * Derive provider_key from where this start.php lives on disk.
	 *
	 * Bundled copy: {plugin}/lib/wpb-sdk/start.php → plugin folder basename.
	 * Standalone wpb-sdk package: plugins/wpb-sdk/start.php → empty string.
	 *
	 * @param string $start_dir Directory containing start.php (__DIR__).
	 * @return string
	 */
	function wpb_sdk_detect_provider_key_from_path( $start_dir ) {
		$start_dir = function_exists( 'wp_normalize_path' )
			? wp_normalize_path( $start_dir )
			: str_replace( '\\', '/', $start_dir );

		if ( preg_match( '#/lib/wpb-sdk$#', $start_dir ) ) {
			$plugin_root = dirname( dirname( $start_dir ) );
			$folder      = basename( $plugin_root );

			if ( '' !== $folder && '.' !== $folder && '..' !== $folder ) {
				return sanitize_key( $folder );
			}
		}

		return '';
	}

	/**
	 * Resolve the telemetry API endpoint from WPBRIGADE_SDK_API_ENDPOINT.
	 *
	 * Loads this bundle's config.php only when the constant is not set yet.
	 * Path constants (WPBRIGADE_SDK_DIR) are guarded in config.php, so requiring
	 * it here is safe for the endpoint; all synced bundles share the same URL.
	 *
	 * @param string $sdk_dir Path to lib/wpb-sdk.
	 * @return string API base URL without trailing slash, or empty if unavailable.
	 */
	function wpb_sdk_parse_bundle_api_endpoint( $sdk_dir ) {
		if ( ! defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
			$path = trailingslashit( $sdk_dir ) . 'config.php';
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}

		if ( defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
			return rtrim( (string) WPBRIGADE_SDK_API_ENDPOINT, '/' );
		}

		return '';
	}
}

wpb_sdk_register_provider(
	array(
		'provider_key' => wpb_sdk_detect_provider_key_from_path( __DIR__ ),
		'sdk_version'  => $wpb_sdk_version,
		'runtime_file' => __DIR__ . '/require.php',
		'sdk_dir'      => __DIR__,
		'api_endpoint' => wpb_sdk_parse_bundle_api_endpoint( __DIR__ ),
	)
);

if ( ! function_exists( 'wpb_dynamic_init' ) ) {
	/**
	 * Backward-compatible init proxy.
	 *
	 * @param array $module Module configuration.
	 * @return array|false
	 */
	function wpb_dynamic_init( $module ) {
		return wpb_sdk_dynamic_init( $module );
	}
}

if ( ! function_exists( 'wpb_sdk_run_product_uninstall' ) ) {
	/**
	 * Parse-time uninstall entry (stored in uninstall_plugins; must exist before runtime loads).
	 *
	 * @return void
	 */
	function wpb_sdk_run_product_uninstall() {
		if ( function_exists( 'wpb_sdk_ensure_runtime_loaded' ) ) {
			wpb_sdk_ensure_runtime_loaded();
		}
		if ( function_exists( 'wpb_sdk_migrate_legacy_lifecycle_callbacks' ) ) {
			wpb_sdk_migrate_legacy_lifecycle_callbacks();
		}
		if ( function_exists( 'wpb_sdk_invoke_product_uninstall' ) ) {
			wpb_sdk_invoke_product_uninstall();
			return;
		}
		if ( class_exists( 'WPBRIGADE_Logger', false ) && method_exists( 'WPBRIGADE_Logger', 'log_uninstallation' ) ) {
			WPBRIGADE_Logger::log_uninstallation();
		}
	}
}

if ( ! function_exists( 'wpb_sdk_bootstrap_runtime_for_uninstall' ) ) {
	/**
	 * Load Logger before WordPress runs the stored uninstall_plugins callback.
	 *
	 * @return void
	 */
	function wpb_sdk_bootstrap_runtime_for_uninstall() {
		if ( ! function_exists( 'wpb_sdk_ensure_runtime_loaded' ) ) {
			return;
		}
		$should_bootstrap = ( defined( 'WP_UNINSTALL_PLUGIN' ) && WP_UNINSTALL_PLUGIN )
			|| ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && 'delete-plugin' === $_REQUEST['action'] );
		if ( $should_bootstrap ) {
			wpb_sdk_ensure_runtime_loaded();
		}
	}
}
wpb_sdk_bootstrap_runtime_for_uninstall();
