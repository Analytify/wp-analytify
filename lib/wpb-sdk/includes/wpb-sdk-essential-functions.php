<?php
/**
 * Shared helpers for all WPBrigade SDK products.
 *
 * @package wpbrigade_sdk
 */

if ( ! function_exists( 'wpb_sdk_runtime_is_complete' ) ) {
	/**
	 * Whether the full centralized SDK runtime is loaded (not a legacy partial bundle).
	 *
	 * @return bool
	 */
	function wpb_sdk_runtime_is_complete() {
		return class_exists( 'WPBRIGADE_Opt_Manager', false )
			&& class_exists( 'WPBRIGADE_Logger', false );
	}
}

if ( ! function_exists( 'wpb_sdk_register_opt_manager_for_module' ) ) {
	/**
	 * Register opt-in/out row links (required when an old Logger runtime is already loaded).
	 *
	 * @param array<string, mixed> $module Module config.
	 * @return void
	 */
	function wpb_sdk_register_opt_manager_for_module( $module ) {
		if ( ! class_exists( 'WPBRIGADE_Opt_Manager', false ) || ! is_array( $module ) ) {
			return;
		}

		static $initiated = array();

		if ( function_exists( 'wpb_sdk_apply_module_defaults' ) ) {
			$module = wpb_sdk_apply_module_defaults( $module );
		}

		$slug = ! empty( $module['slug'] ) ? (string) $module['slug'] : '';
		if ( '' !== $slug && ! empty( $initiated[ $slug ] ) ) {
			return;
		}

		// Legacy plugins (wpb_dynamic_init, no provider sdk_version) own their own Opt In/Out links.
		if ( empty( $module['sdk_version'] ) || version_compare( (string) $module['sdk_version'], '3.2.0', '<' ) ) {
			return;
		}

		if ( '' !== $slug ) {
			$initiated[ $slug ] = true;
		}

		if ( ! empty( $module['sdk_views_dir'] ) ) {
			WPBRIGADE_Opt_Manager::register_module( $module );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_get_provider_for_slug' ) ) {
	/**
	 * Bundled SDK provider registered by this product's copy of start.php.
	 *
	 * @param string $slug Product slug (provider_key).
	 * @return array<string, mixed>
	 */
	function wpb_sdk_get_provider_for_slug( $slug ) {
		$slug = (string) $slug;
		if (
			isset( $GLOBALS['wpb_sdk_registry']['providers'][ $slug ] )
			&& is_array( $GLOBALS['wpb_sdk_registry']['providers'][ $slug ] )
		) {
			return $GLOBALS['wpb_sdk_registry']['providers'][ $slug ];
		}

		return array();
	}
}

if ( ! function_exists( 'wpb_sdk_detect_bundled_sdk_dir' ) ) {
	/**
	 * Directory of the wpb-sdk folder that registered for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return string Absolute path to lib/wpb-sdk (no trailing slash).
	 */
	function wpb_sdk_detect_bundled_sdk_dir( $slug = '' ) {
		$provider = wpb_sdk_get_provider_for_slug( $slug );
		if ( ! empty( $provider['sdk_dir'] ) ) {
			$dir = function_exists( 'wp_normalize_path' )
				? wp_normalize_path( (string) $provider['sdk_dir'] )
				: (string) $provider['sdk_dir'];
			if ( is_dir( $dir ) ) {
				return $dir;
			}
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- Resolve caller bundle only.
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 12 );
		foreach ( $trace as $frame ) {
			if ( empty( $frame['file'] ) ) {
				continue;
			}
			$file = function_exists( 'wp_normalize_path' )
				? wp_normalize_path( $frame['file'] )
				: str_replace( '\\', '/', $frame['file'] );
			if ( preg_match( '#/wpb-sdk/(?:start|require)\\.php$#', $file ) ) {
				return dirname( $file );
			}
		}

		if ( defined( 'WPBRIGADE_SDK_DIR' ) ) {
			return function_exists( 'wp_normalize_path' )
				? wp_normalize_path( WPBRIGADE_SDK_DIR )
				: WPBRIGADE_SDK_DIR;
		}

		$slug = (string) $slug;
		if ( '' !== $slug && defined( 'WP_PLUGIN_DIR' ) ) {
			$candidate = WP_PLUGIN_DIR . '/' . $slug . '/lib/wpb-sdk';
			if ( is_dir( $candidate ) ) {
				return function_exists( 'wp_normalize_path' )
					? wp_normalize_path( $candidate )
					: $candidate;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_detect_host_plugin_root' ) ) {
	/**
	 * Host plugin root directory (parent of lib/wpb-sdk).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_detect_host_plugin_root( $slug = '' ) {
		$sdk_dir = wpb_sdk_detect_bundled_sdk_dir( $slug );
		if ( '' === $sdk_dir ) {
			return '';
		}

		$sdk_dir = function_exists( 'wp_normalize_path' )
			? wp_normalize_path( $sdk_dir )
			: $sdk_dir;

		if ( preg_match( '#/lib/wpb-sdk$#', $sdk_dir ) ) {
			return dirname( dirname( $sdk_dir ) );
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_detect_calling_plugin_file' ) ) {
	/**
	 * Main plugin PHP file that invoked wpb_sdk_dynamic_init() (Freemius-style).
	 *
	 * @return string Absolute path or empty.
	 */
	function wpb_sdk_detect_calling_plugin_file() {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- Caller plugin file detection.
		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 15 );
		foreach ( $trace as $frame ) {
			if ( empty( $frame['file'] ) ) {
				continue;
			}
			$file = function_exists( 'wp_normalize_path' )
				? wp_normalize_path( $frame['file'] )
				: str_replace( '\\', '/', $frame['file'] );
			if ( false !== strpos( $file, '/lib/wpb-sdk/' ) ) {
				continue;
			}
			if ( false !== strpos( $file, '/wp-content/plugins/' ) && is_readable( $file ) ) {
				return $file;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_detect_plugin_main_file' ) ) {
	/**
	 * Resolve host plugin main file from slug and bundled SDK location.
	 *
	 * @param string $slug Product slug.
	 * @return string Absolute path or empty.
	 */
	function wpb_sdk_detect_plugin_main_file( $slug ) {
		$slug = (string) $slug;
		if ( '' === $slug ) {
			return '';
		}

		$caller = wpb_sdk_detect_calling_plugin_file();
		if ( '' !== $caller && is_readable( $caller ) ) {
			return $caller;
		}

		$root = wpb_sdk_detect_host_plugin_root( $slug );
		if ( '' !== $root ) {
			$candidates = array(
				$root . '/' . basename( $root ) . '.php',
				$root . '/' . $slug . '.php',
			);
			foreach ( $candidates as $path ) {
				if ( is_readable( $path ) ) {
					return function_exists( 'wp_normalize_path' )
						? wp_normalize_path( $path )
						: $path;
				}
			}
		}

		if ( defined( 'WP_PLUGIN_DIR' ) ) {
			$fallback = WP_PLUGIN_DIR . '/' . $slug . '/' . $slug . '.php';
			if ( is_readable( $fallback ) ) {
				return function_exists( 'wp_normalize_path' )
					? wp_normalize_path( $fallback )
					: $fallback;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_default_ajax_prefix' ) ) {
	/**
	 * Default AJAX action prefix from slug (wp-analytify → analytify).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_default_ajax_prefix( $slug ) {
		$slug   = (string) $slug;
		$prefix = preg_replace( '#^wp-#', '', $slug );

		return str_replace( '-', '_', $prefix );
	}
}

if ( ! function_exists( 'wpb_sdk_find_optout_view' ) ) {
	/**
	 * Locate opt-out view in this product's bundled SDK views directory.
	 *
	 * @param string $views_dir Absolute path to views folder.
	 * @param string $slug      Product slug.
	 * @return string Filename or empty.
	 */
	function wpb_sdk_find_optout_view( $views_dir, $slug ) {
		$views_dir = trailingslashit( $views_dir );
		if ( ! is_dir( $views_dir ) ) {
			return '';
		}

		$prefix = wpb_sdk_default_ajax_prefix( $slug );
		$try    = array(
			'wpb-sdk-optout-form.php',
			$prefix . '-optout-form.php',
			$slug . '-optout-form.php',
		);
		$try    = array_unique( $try );
		foreach ( $try as $name ) {
			if ( is_readable( $views_dir . $name ) ) {
				return $name;
			}
		}

		$glob = glob( $views_dir . '*-optout-form.php' );
		if ( ! empty( $glob[0] ) && is_readable( $glob[0] ) ) {
			return basename( $glob[0] );
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_product_manages_own_opt_action_links' ) ) {
	/**
	 * Products that add Opt In/Out on plugins.php via their own plugin_action_links handler.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_product_manages_own_opt_action_links( $slug ) {
		$slug = (string) $slug;

		if (
			isset( $GLOBALS['wpb_sdk_registry']['modules'][ $slug ] )
			&& is_array( $GLOBALS['wpb_sdk_registry']['modules'][ $slug ] )
		) {
			$module = $GLOBALS['wpb_sdk_registry']['modules'][ $slug ];
			if ( empty( $module['sdk_version'] ) || version_compare( (string) $module['sdk_version'], '3.2.0', '<' ) ) {
				return true;
			}

			return false;
		}

		$legacy = array();

		/**
		 * Filter slugs that manage their own Opt In/Out plugin row links (not the SDK).
		 *
		 * @param string[] $legacy Product slugs.
		 * @param string   $slug   Current product slug.
		 */
		$legacy = apply_filters( 'wpb_sdk_legacy_opt_action_link_slugs', $legacy, $slug );

		return in_array( $slug, $legacy, true );
	}
}

if ( ! function_exists( 'wpb_sdk_uses_custom_optin_form' ) ) {
	/**
	 * Products with a bespoke opt-in screen (not the shared SDK default).
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_uses_custom_optin_form( $slug ) {
		$custom = array( 'loginpress', 'wp-analytify' );

		/**
		 * Filter products that use a custom opt-in form instead of wpb-sdk-optin-form.php.
		 *
		 * @param string[] $custom Product slugs.
		 * @param string   $slug   Current product slug.
		 */
		$custom = apply_filters( 'wpb_sdk_custom_optin_slugs', $custom, $slug );

		return in_array( $slug, $custom, true );
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_optin_logo_url' ) ) {
	/**
	 * Logo URL for the shared opt-in splash.
	 *
	 * @param array<string, mixed> $module Module config.
	 * @return string Empty when no logo is configured or found.
	 */
	function wpb_sdk_resolve_optin_logo_url( array $module ) {
		$optin = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		if ( ! empty( $optin['logo_url'] ) ) {
			return (string) $optin['logo_url'];
		}

		$plugin_file = ! empty( $module['plugin_file'] ) ? (string) $module['plugin_file'] : '';
		if ( '' === $plugin_file || ! is_readable( $plugin_file ) ) {
			return '';
		}

		$candidates = array();
		if ( ! empty( $optin['logo_path'] ) ) {
			$candidates[] = (string) $optin['logo_path'];
		}

		$prefix     = wpb_sdk_default_ajax_prefix( (string) $module['slug'] );
		$slug       = (string) $module['slug'];
		$candidates = array_merge(
			$candidates,
			array(
				'assets/images/' . $prefix . '_icon.png',
				'assets/images/' . $prefix . '-icon.png',
				'assets/images/' . $prefix . '_logo.png',
				'assets/images/' . $prefix . '-logo.svg',
				'assets/images/' . $prefix . '-logo.png',
				'assets/images/' . $prefix . '-brand.svg',
				'assets/img/' . $prefix . '-logo.svg',
				'assets/img/' . $prefix . '_logo.png',
				'img/' . $prefix . '.png',
				'img/' . $prefix . '_icon.png',
				'img/icon.png',
				'img/logo.png',
				'img/review-icon.png',
				'asset/img/logo.svg',
				'asset/img/logo.png',
				'assets/images/logo.svg',
				'assets/images/logo.png',
				'assets/images/icon.png',
			)
		);
		if ( str_starts_with( $slug, 'wp-' ) ) {
			$short_slug   = substr( $slug, 3 );
			$candidates[] = 'assets/images/' . $short_slug . '-logo.svg';
			$candidates[] = 'assets/images/' . $short_slug . '_icon.png';
		}

		foreach ( $candidates as $relative ) {
			$path = plugin_dir_path( $plugin_file ) . ltrim( $relative, '/\\' );
			if ( is_readable( $path ) ) {
				return plugins_url( $relative, $plugin_file );
			}
		}

		/**
		 * Last-resort logo URL when no bundled asset exists (e.g. plugins without assets/images/).
		 *
		 * @param string               $url    Empty by default.
		 * @param array<string, mixed> $module Module config.
		 */
		$filtered = apply_filters( 'wpb_sdk_optin_logo_url', '', $module );
		if ( '' !== $filtered ) {
			return (string) $filtered;
		}

		$use_wporg = ! isset( $optin['wporg_logo_fallback'] ) || false !== $optin['wporg_logo_fallback'];
		if ( ! $use_wporg ) {
			return '';
		}

		$wporg_slug = ! empty( $optin['wporg_slug'] ) ? (string) $optin['wporg_slug'] : $slug;
		if ( '' === $wporg_slug ) {
			return '';
		}

		return 'https://ps.w.org/' . rawurlencode( $wporg_slug ) . '/assets/icon-256x256.png';
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_optin_hero_url' ) ) {
	/**
	 * Hero/illustration URL for the shared opt-in splash (falls back to logo).
	 *
	 * @param array<string, mixed> $module Module config.
	 * @return string
	 */
	function wpb_sdk_resolve_optin_hero_url( array $module ) {
		$optin = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		if ( ! empty( $optin['hero_image_url'] ) ) {
			return (string) $optin['hero_image_url'];
		}

		$plugin_file = ! empty( $module['plugin_file'] ) ? (string) $module['plugin_file'] : '';
		if ( '' !== $plugin_file && is_readable( $plugin_file ) ) {
			if ( ! empty( $optin['hero_image_path'] ) ) {
				$relative = (string) $optin['hero_image_path'];
				$path     = plugin_dir_path( $plugin_file ) . ltrim( $relative, '/\\' );
				if ( is_readable( $path ) ) {
					return plugins_url( $relative, $plugin_file );
				}
			}

			$prefix     = wpb_sdk_default_ajax_prefix( (string) $module['slug'] );
			$candidates = array(
				'assets/images/welcome-' . $prefix . '.png',
				'assets/images/' . $prefix . '-welcome.png',
				'assets/images/social_button.svg',
			);
			foreach ( $candidates as $relative ) {
				$path = plugin_dir_path( $plugin_file ) . ltrim( $relative, '/\\' );
				if ( is_readable( $path ) ) {
					return plugins_url( $relative, $plugin_file );
				}
			}
		}

		return wpb_sdk_resolve_optin_logo_url( $module );
	}
}

if ( ! function_exists( 'wpb_sdk_get_optin_view_path' ) ) {
	/**
	 * Absolute path to the opt-in view for a product.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_get_optin_view_path( $slug ) {
		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module['sdk_views_dir'] ) ) {
			return '';
		}

		$views_dir = trailingslashit( (string) $module['sdk_views_dir'] );
		$optin     = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();

		if ( ! empty( $optin['optin_view'] ) ) {
			$path = $views_dir . ltrim( (string) $optin['optin_view'], '/\\' );
			return is_readable( $path ) ? $path : '';
		}

		if ( wpb_sdk_uses_custom_optin_form( $slug ) ) {
			return '';
		}

		$default = $views_dir . 'wpb-sdk-optin-form.php';

		return is_readable( $default ) ? $default : '';
	}
}

if ( ! function_exists( 'wpb_sdk_render_optin_form' ) ) {
	/**
	 * Render the shared (or configured) opt-in admin page for a product.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_render_optin_form( $slug ) {
		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module ) ) {
			return;
		}

		$path = wpb_sdk_get_optin_view_path( $slug );
		if ( '' === $path ) {
			return;
		}

		$optin  = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		$prefix = ! empty( $optin['ajax_prefix'] )
			? sanitize_key( (string) $optin['ajax_prefix'] )
			: wpb_sdk_default_ajax_prefix( $slug );

		$wpb_sdk_product_name = ! empty( $optin['product_name'] )
			? (string) $optin['product_name']
			: $slug;
		$wpb_sdk_ajax_prefix  = $prefix;
		$wpb_sdk_optin_option = ! empty( $optin['option_name'] ) ? (string) $optin['option_name'] : '';
		$wpb_sdk_optin_nonce  = wp_create_nonce( $prefix . '_optin_page_nonce' );
		$wpb_sdk_logo_url     = wpb_sdk_resolve_optin_logo_url( $module );
		$wpb_sdk_optin_page   = ! empty( $optin['optin_page'] ) ? (string) $optin['optin_page'] : '';

		$redirect_override = '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['redirect-page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$redirect_override = sanitize_key( wp_unslash( (string) $_GET['redirect-page'] ) );
		}
		$wpb_sdk_redirect_url = wpb_sdk_resolve_optin_redirect_url( $slug, $redirect_override );

		include $path;
	}
}

if ( ! function_exists( 'wpb_sdk_apply_module_defaults' ) ) {
	/**
	 * Fill module config from bundled SDK path and slug (minimal init like Freemius).
	 *
	 * Required keys only: id, slug, public_key, (+ product-specific optin/settings).
	 *
	 * @param array<string, mixed> $module Module config from the host plugin.
	 * @return array<string, mixed>
	 */
	function wpb_sdk_apply_module_defaults( $module ) {
		if ( ! is_array( $module ) || empty( $module['slug'] ) ) {
			return $module;
		}

		$slug    = (string) $module['slug'];
		$sdk_dir = wpb_sdk_detect_bundled_sdk_dir( $slug );

		if ( empty( $module['type'] ) ) {
			$module['type'] = 'plugin';
		}

		if ( empty( $module['api_endpoint'] ) ) {
			$provider = wpb_sdk_get_provider_for_slug( $slug );
			if ( ! empty( $provider['api_endpoint'] ) ) {
				$module['api_endpoint'] = (string) $provider['api_endpoint'];
			}
		}

		// Inject the plugin's own SDK version from its provider registration.
		// Used downstream to detect whether this plugin manages its own action links
		// (old SDK ≤ 3.1.x) or delegates to WPBRIGADE_Opt_Manager (new SDK ≥ 3.2.0).
		if ( empty( $module['sdk_version'] ) ) {
			$provider = wpb_sdk_get_provider_for_slug( $slug );
			if ( ! empty( $provider['sdk_version'] ) ) {
				$module['sdk_version'] = (string) $provider['sdk_version'];
			}
		}

		if ( empty( $module['plugin_file'] ) ) {
			$detected = wpb_sdk_detect_plugin_main_file( $slug );
			if ( '' !== $detected ) {
				$module['plugin_file'] = $detected;
			}
		}

		if ( empty( $module['sdk_views_dir'] ) && '' !== $sdk_dir ) {
			$module['sdk_views_dir'] = trailingslashit( $sdk_dir ) . 'views';
		}

		if ( empty( $module['text_domain'] ) && ! empty( $module['plugin_file'] ) && is_readable( $module['plugin_file'] ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$header = get_plugin_data( $module['plugin_file'], false, false );
			if ( ! empty( $header['TextDomain'] ) ) {
				$module['text_domain'] = (string) $header['TextDomain'];
			}
		}
		if ( empty( $module['text_domain'] ) ) {
			$module['text_domain'] = $slug;
		}

		$optin = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		if ( empty( $optin['ajax_prefix'] ) ) {
			$optin['ajax_prefix'] = wpb_sdk_default_ajax_prefix( $slug );
		}
		if ( ! empty( $module['sdk_views_dir'] ) ) {
			$shared_optout = trailingslashit( $module['sdk_views_dir'] ) . 'wpb-sdk-optout-form.php';
			if ( is_readable( $shared_optout ) ) {
				$optin['optout_view'] = 'wpb-sdk-optout-form.php';
			} elseif ( empty( $optin['optout_view'] ) ) {
				$found = wpb_sdk_find_optout_view( $module['sdk_views_dir'], $slug );
				if ( '' !== $found ) {
					$optin['optout_view'] = $found;
				}
			}
		}
		if (
			empty( $optin['optin_view'] )
			&& ! wpb_sdk_uses_custom_optin_form( $slug )
			&& ! empty( $module['sdk_views_dir'] )
			&& is_readable( trailingslashit( $module['sdk_views_dir'] ) . 'wpb-sdk-optin-form.php' )
		) {
			$optin['optin_view'] = 'wpb-sdk-optin-form.php';
		}
		if ( empty( $optin['product_name'] ) && ! empty( $module['plugin_file'] ) && is_readable( $module['plugin_file'] ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$header = get_plugin_data( $module['plugin_file'], false, false );
			if ( ! empty( $header['Name'] ) ) {
				$optin['product_name'] = (string) $header['Name'];
			}
		}
		$module['optin'] = $optin;

		$meta = isset( $module['optin_user_meta'] ) && is_array( $module['optin_user_meta'] )
			? $module['optin_user_meta']
			: array();
		if ( ! empty( $meta['token'] ) ) {
			if ( ! empty( $meta['email_verified'] ) ) {
				$meta['email_verified'] = wpb_sdk_normalize_email_verified_meta_key(
					(string) $meta['email_verified'],
					$slug
				);
			} elseif ( ! empty( $meta['verified'] ) ) {
				$meta['email_verified'] = wpb_sdk_normalize_email_verified_meta_key(
					(string) $meta['verified'],
					$slug
				);
			} else {
				$meta['email_verified'] = wpb_sdk_optin_email_verified_meta_key( $slug );
			}
			unset( $meta['verified'], $meta['verified_at'], $meta['verified_v2'], $meta['verified_v2_at'] );
			$module['optin_user_meta'] = $meta;
		}

		/**
		 * Filter module config after SDK defaults are applied.
		 *
		 * @param array<string, mixed> $module Module config.
		 */
		return apply_filters( 'wpb_sdk_module_config', $module );
	}
}

if ( ! function_exists( 'wpb_sdk_get_registered_module' ) ) {
	/**
	 * Module config stored by wpb_sdk_dynamic_init().
	 *
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	function wpb_sdk_get_registered_module( $slug ) {
		if (
			isset( $GLOBALS['wpb_sdk_registry']['modules'][ $slug ] )
			&& is_array( $GLOBALS['wpb_sdk_registry']['modules'][ $slug ] )
		) {
			return $GLOBALS['wpb_sdk_registry']['modules'][ $slug ];
		}

		return array();
	}
}

if ( ! function_exists( 'wpb_sdk_get_optin_decision' ) ) {
	/**
	 * Stored opt-in choice for a product (empty = user has not decided yet).
	 *
	 * @param string $slug Product slug.
	 * @return string yes|no|skip|'' or legacy when product has no opt-in gate.
	 */
	function wpb_sdk_get_optin_decision( $slug ) {
		$module = wpb_sdk_get_registered_module( $slug );
		$optin  = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		$name   = ! empty( $optin['option_name'] ) ? (string) $optin['option_name'] : '';

		if ( '' === $name ) {
			return 'legacy';
		}

		$value = ! empty( $optin['use_site_option'] )
			? get_site_option( $name, '' )
			: get_option( $name, '' );

		return is_string( $value ) ? $value : '';
	}
}

if ( ! function_exists( 'wpb_sdk_sdk_option_is_enabled' ) ) {
	/**
	 * Whether an SDK sharing flag is on (handles "1", 1, true, etc.).
	 *
	 * @param mixed $value Raw option value.
	 * @return bool
	 */
	function wpb_sdk_sdk_option_is_enabled( $value ) {
		if ( true === $value || 1 === $value ) {
			return true;
		}

		return in_array( strtolower( (string) $value ), array( '1', 'true', 'yes', 'on' ), true );
	}
}

if ( ! function_exists( 'wpb_sdk_maybe_normalize_legacy_sdk_sharing_option' ) ) {
	/**
	 * Persist canonical 1/0 sharing flags when legacy stores yes/on/true (e.g. Analytify 9.0.2).
	 *
	 * @param string $slug Product slug.
	 * @return bool True when the option was updated.
	 */
	function wpb_sdk_maybe_normalize_legacy_sdk_sharing_option( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug || ! wpb_sdk_legacy_optin_consent_is_active( $slug ) ) {
			return false;
		}

		$raw = get_option( 'wpb_sdk_' . $slug, '' );
		$sdk = is_string( $raw ) ? json_decode( $raw, true ) : array();
		if ( ! is_array( $sdk ) ) {
			$sdk = array();
		}

		$normalized = array( 'user_skip' => '0' );
		$dirty      = (string) ( $sdk['user_skip'] ?? '' ) !== '0';

		foreach ( array( 'communication', 'diagnostic_info', 'extensions' ) as $flag ) {
			$normalized[ $flag ] = wpb_sdk_sdk_option_is_enabled( $sdk[ $flag ] ?? '0' ) ? '1' : '0';
			if ( (string) ( $sdk[ $flag ] ?? '' ) !== $normalized[ $flag ] ) {
				$dirty = true;
			}
		}

		if ( ! $dirty ) {
			return false;
		}

		update_option( 'wpb_sdk_' . $slug, wp_json_encode( $normalized ), false );

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_default_post_optin_redirect_url' ) ) {
	/**
	 * Safe fallback when a product has no registered settings admin screen.
	 *
	 * @return string
	 */
	function wpb_sdk_default_post_optin_redirect_url() {
		return admin_url( 'plugins.php' );
	}
}

if ( ! function_exists( 'wpb_sdk_admin_screen_path_is_available' ) ) {
	/**
	 * Whether a path under wp-admin exists (e.g. nav-menus.php).
	 *
	 * @param string $admin_path Path relative to wp-admin.
	 * @return bool
	 */
	function wpb_sdk_admin_screen_path_is_available( $admin_path ) {
		$admin_path = ltrim( (string) $admin_path, '/' );
		if ( '' === $admin_path ) {
			return false;
		}

		$file = strtok( $admin_path, '?' );
		if ( ! is_string( $file ) || '' === $file ) {
			return false;
		}

		return file_exists( ABSPATH . 'wp-admin/' . $file );
	}
}

if ( ! function_exists( 'wpb_sdk_admin_menu_page_slug_exists' ) ) {
	/**
	 * Whether ?page= slug is registered in the admin menu.
	 *
	 * @param string $page_slug Admin page query arg.
	 * @return bool
	 */
	function wpb_sdk_admin_menu_page_slug_exists( $page_slug ) {
		$page_slug = sanitize_key( (string) $page_slug );
		if ( '' === $page_slug ) {
			return false;
		}

		global $admin_page_hooks, $submenu;

		if ( isset( $admin_page_hooks[ $page_slug ] ) ) {
			return true;
		}

		if ( ! is_array( $submenu ) ) {
			return false;
		}

		foreach ( $submenu as $items ) {
			if ( ! is_array( $items ) ) {
				continue;
			}
			foreach ( $items as $item ) {
				if ( ! is_array( $item ) || empty( $item[2] ) ) {
					continue;
				}
				$hook = (string) $item[2];
				if ( $page_slug === $hook || false !== strpos( $hook, 'page=' . $page_slug ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'wpb_sdk_admin_url_for_registered_page_slug' ) ) {
	/**
	 * Return the admin.php?page= URL only when that page is registered.
	 *
	 * @param string $page_slug Admin page query arg.
	 * @return string Empty when not registered.
	 */
	function wpb_sdk_admin_url_for_registered_page_slug( $page_slug ) {
		$page_slug = sanitize_key( (string) $page_slug );
		if ( '' === $page_slug || ! wpb_sdk_admin_menu_page_slug_exists( $page_slug ) ) {
			return '';
		}

		return admin_url( 'admin.php?page=' . $page_slug );
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_optin_redirect_url' ) ) {
	/**
	 * Redirect target after opt-in Allow/Skip (or email verification).
	 *
	 * Only registered plugin admin.php?page= slugs are valid. Core screens (e.g. nav-menus.php)
	 * and unknown slugs fall back to plugins.php.
	 *
	 * Order: redirect-page override (if registered), settings_page, product slug, plugins.php.
	 *
	 * @param string $slug              Product slug.
	 * @param string $redirect_override Optional redirect-page query value.
	 * @return string
	 */
	function wpb_sdk_resolve_optin_redirect_url( $slug, $redirect_override = '' ) {
		$fallback = wpb_sdk_default_post_optin_redirect_url();
		$module   = wpb_sdk_get_registered_module( $slug );
		$optin    = ( ! empty( $module['optin'] ) && is_array( $module['optin'] ) )
			? $module['optin']
			: array();

		$redirect_override = sanitize_key( (string) $redirect_override );

		if ( '' !== $redirect_override ) {
			$url = wpb_sdk_admin_url_for_registered_page_slug( $redirect_override );
			if ( '' === $url ) {
				$url = $fallback;
			}

			return apply_filters( 'wpb_sdk_optin_redirect_url', $url, $slug, $optin, $redirect_override );
		}

		if ( ! empty( $optin['settings_page'] ) ) {
			$url = wpb_sdk_admin_url_for_registered_page_slug( (string) $optin['settings_page'] );
			if ( '' !== $url ) {
				return apply_filters( 'wpb_sdk_optin_redirect_url', $url, $slug, $optin, $redirect_override );
			}
		}

		$url = wpb_sdk_admin_url_for_registered_page_slug( $slug );
		if ( '' === $url ) {
			$url = $fallback;
		}

		return apply_filters( 'wpb_sdk_optin_redirect_url', $url, $slug, $optin, $redirect_override );
	}
}

if ( ! function_exists( 'wpb_sdk_build_uninstall_option_names_from_module' ) ) {
	/**
	 * Option names the SDK may persist for a product (for uninstall cleanup).
	 *
	 * @param string               $slug   Product slug.
	 * @param array<string, mixed> $module Module definition.
	 * @return string[]
	 */
	function wpb_sdk_build_uninstall_option_names_from_module( $slug, array $module ) {
		$slug    = sanitize_key( (string) $slug );
		$options = array(
			'wpb_sdk_' . $slug,
			'wpb_sdk_' . $slug . '_initial_log_sent',
			'wpb_sdk_' . $slug . '_fallback_verify_token',
			'wpb_sdk_' . $slug . '_legacy_upgrade_optin',
			'wpb_sdk_' . $slug . '_optin_initiator',
			'wpb_sdk_' . $slug . '_optin_initiator_backfilled',
			'wpb_sdk_' . $slug . '_telemetry_contact_fallback',
			'wpb_sdk_' . $slug . '_telemetry_contact_cohort',
			'wpb_sdk_' . $slug . '_optin_initiator_backfill_version',
			'wpb_sdk_' . $slug . '_uninstall_manifest',
			'wpb_sdk_' . $slug . '_lifecycle_module',
		);

		$optin = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		if ( ! empty( $optin['option_name'] ) ) {
			$options[] = (string) $optin['option_name'];
		}
		if ( ! empty( $optin['legacy_token_option'] ) ) {
			$options[] = (string) $optin['legacy_token_option'];
		}
		if ( ! empty( $optin['verified_by_option'] ) ) {
			$options[] = (string) $optin['verified_by_option'];
		}

		if ( ! empty( $module['settings'] ) && is_array( $module['settings'] ) ) {
			foreach ( array_keys( $module['settings'] ) as $key ) {
				$key = (string) $key;
				if ( '' === $key ) {
					continue;
				}
				if (
					0 === strpos( $key, 'wpb_sdk_' )
					|| 0 === strpos( $key, '_' )
				) {
					$options[] = $key;
				}
			}
		}

		$options[] = 'wpb_sdk_module_id';
		$options[] = 'wpb_sdk_module_slug';

		return array_values( array_unique( array_filter( $options ) ) );
	}
}

if ( ! function_exists( 'wpb_sdk_verification_notice_dismissed_meta_key' ) ) {
	/**
	 * User meta key: verification email admin notice dismissed for a product.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_verification_notice_dismissed_meta_key( $slug ) {
		$slug = sanitize_key( (string) $slug );

		return '' === $slug ? '' : 'wpb_sdk_' . $slug . '_verify_notice_dismissed';
	}
}

if ( ! function_exists( 'wpb_sdk_verification_notice_dismiss_ttl_seconds' ) ) {
	/**
	 * How long a verification notice dismiss hides the notice for one admin.
	 *
	 * @return int Seconds.
	 */
	function wpb_sdk_verification_notice_dismiss_ttl_seconds() {
		return DAY_IN_SECONDS;
	}
}

if ( ! function_exists( 'wpb_sdk_is_verification_notice_dismissed' ) ) {
	/**
	 * Whether the verification notice is snoozed for an admin (per product).
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_is_verification_notice_dismissed( $slug, $user_id = 0 ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return false;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			$user_id = (int) get_current_user_id();
		}
		if ( $user_id < 1 ) {
			return false;
		}

		$dismiss_key = wpb_sdk_verification_notice_dismissed_meta_key( $slug );
		if ( '' === $dismiss_key ) {
			return false;
		}

		$dismissed_at = (int) get_user_meta( $user_id, $dismiss_key, true );
		if ( $dismissed_at < 1 ) {
			return false;
		}

		$ttl = wpb_sdk_verification_notice_dismiss_ttl_seconds();

		return $ttl > 0 && ( time() - $dismissed_at ) < $ttl;
	}
}

if ( ! function_exists( 'wpb_sdk_dismiss_verification_notice' ) ) {
	/**
	 * Snooze the verification notice for an admin.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional WordPress user ID.
	 * @return void
	 */
	function wpb_sdk_dismiss_verification_notice( $slug, $user_id = 0 ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			$user_id = (int) get_current_user_id();
		}
		if ( $user_id < 1 ) {
			return;
		}

		$dismiss_key = wpb_sdk_verification_notice_dismissed_meta_key( $slug );
		if ( '' === $dismiss_key ) {
			return;
		}

		update_user_meta( $user_id, $dismiss_key, (string) time() );
	}
}

if ( ! function_exists( 'wpb_sdk_clear_verification_notice_dismissed' ) ) {
	/**
	 * Clear verification notice dismiss snooze for an admin.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional WordPress user ID.
	 * @return void
	 */
	function wpb_sdk_clear_verification_notice_dismissed( $slug, $user_id = 0 ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			$user_id = (int) get_current_user_id();
		}
		if ( $user_id < 1 ) {
			return;
		}

		$dismiss_key = wpb_sdk_verification_notice_dismissed_meta_key( $slug );
		if ( '' !== $dismiss_key ) {
			delete_user_meta( $user_id, $dismiss_key );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_build_uninstall_user_meta_keys_from_module' ) ) {
	/**
	 * User meta keys used for opt-in verification (for uninstall cleanup).
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return string[]
	 */
	function wpb_sdk_build_uninstall_user_meta_keys_from_module( array $module ) {
		$keys     = array();
		$meta     = isset( $module['optin_user_meta'] ) && is_array( $module['optin_user_meta'] )
			? $module['optin_user_meta']
			: array();
		$token    = ! empty( $meta['token'] ) ? (string) $meta['token'] : '';
		$email_verified = function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
			? wpb_sdk_email_verified_meta_key_from_module( $module )
			: '';

		if ( '' !== $token ) {
			$keys[]  = $token;
			$expires = function_exists( 'wpb_sdk_verification_token_expires_meta_key' )
				? wpb_sdk_verification_token_expires_meta_key( $module )
				: $token . '_expires';
			if ( '' !== $expires ) {
				$keys[] = $expires;
			}
		}
		if ( '' !== $email_verified ) {
			$keys[] = $email_verified;
		}

		if ( function_exists( 'wpb_sdk_legacy_verification_user_meta_keys_from_module' ) ) {
			$keys = array_merge( $keys, wpb_sdk_legacy_verification_user_meta_keys_from_module( $module ) );
		}

		if ( ! empty( $module['slug'] ) ) {
			$dismiss_key = wpb_sdk_verification_notice_dismissed_meta_key( (string) $module['slug'] );
			if ( '' !== $dismiss_key ) {
				$keys[] = $dismiss_key;
			}
		}

		return array_values( array_unique( array_filter( $keys ) ) );
	}
}

if ( ! function_exists( 'wpb_sdk_store_uninstall_cleanup_manifest' ) ) {
	/**
	 * Persist option/user-meta lists so uninstall cleanup works without loading the plugin.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	function wpb_sdk_store_uninstall_cleanup_manifest( array $module ) {
		if ( empty( $module['slug'] ) ) {
			return;
		}

		$slug  = sanitize_key( (string) $module['slug'] );
		$optin = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();

		$manifest = array(
			'options'         => wpb_sdk_build_uninstall_option_names_from_module( $slug, $module ),
			'user_meta'       => wpb_sdk_build_uninstall_user_meta_keys_from_module( $module ),
			'use_site_option' => ! empty( $optin['use_site_option'] ),
			'optin_option'    => ! empty( $optin['option_name'] ) ? (string) $optin['option_name'] : '',
		);

		update_option(
			'wpb_sdk_' . $slug . '_uninstall_manifest',
			wp_json_encode( $manifest ),
			false
		);

		if ( function_exists( 'wpb_sdk_persist_module_for_lifecycle' ) ) {
			wpb_sdk_persist_module_for_lifecycle( $module );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_lifecycle_module_option_key' ) ) {
	/**
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_lifecycle_module_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_lifecycle_module';
	}
}

if ( ! function_exists( 'wpb_sdk_persist_module_for_lifecycle' ) ) {
	/**
	 * Store module config so uninstall/late bootstrap can restore Logger state.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	function wpb_sdk_persist_module_for_lifecycle( array $module ) {
		if ( empty( $module['slug'] ) || empty( $module['plugin_file'] ) || empty( $module['id'] ) ) {
			return;
		}

		$slug = sanitize_key( (string) $module['slug'] );
		update_option(
			wpb_sdk_lifecycle_module_option_key( $slug ),
			wp_json_encode( $module ),
			false
		);
	}
}

if ( ! function_exists( 'wpb_sdk_store_module_in_registry' ) ) {
	/**
	 * Store module config in the shared registry (safe when Logger class is an older SDK copy).
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	function wpb_sdk_store_module_in_registry( array $module ) {
		$key = isset( $module['slug'] ) ? sanitize_key( (string) $module['slug'] ) : '';
		if ( '' === $key ) {
			return;
		}

		if ( ! isset( $GLOBALS['wpb_sdk_registry']['modules'] ) || ! is_array( $GLOBALS['wpb_sdk_registry']['modules'] ) ) {
			$GLOBALS['wpb_sdk_registry']['modules'] = array();
		}

		$GLOBALS['wpb_sdk_registry']['modules'][ $key ] = $module;
	}
}

if ( ! function_exists( 'wpb_sdk_store_module_if_missing_compat' ) ) {
	/**
	 * Restore module config for lifecycle hooks across mixed SDK versions on one site.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	function wpb_sdk_store_module_if_missing_compat( array $module ) {
		wpb_sdk_store_module_in_registry( $module );

		if (
			class_exists( 'WPBRIGADE_Logger', false )
			&& method_exists( 'WPBRIGADE_Logger', 'wpb_sdk_store_module_if_missing' )
		) {
			WPBRIGADE_Logger::wpb_sdk_store_module_if_missing( $module );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_restore_module_for_lifecycle' ) ) {
	/**
	 * Restore persisted module when plugins_loaded already ran (e.g. plugin delete).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_restore_module_for_lifecycle( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		if (
			function_exists( 'wpb_sdk_get_registered_module' )
			&& ! empty( wpb_sdk_get_registered_module( $slug ) )
		) {
			return;
		}

		$raw    = get_option( wpb_sdk_lifecycle_module_option_key( $slug ), '' );
		$module = is_string( $raw ) ? json_decode( $raw, true ) : $raw;
		if ( ! is_array( $module ) || empty( $module['plugin_file'] ) || empty( $module['id'] ) ) {
			return;
		}

		if ( function_exists( 'wpb_sdk_apply_module_defaults' ) ) {
			$module = wpb_sdk_apply_module_defaults( $module );
		}

		if ( function_exists( 'wpb_sdk_store_module_if_missing_compat' ) ) {
			wpb_sdk_store_module_if_missing_compat( $module );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_get_uninstall_cleanup_manifest' ) ) {
	/**
	 * @param string $slug Product slug.
	 * @return array{options: string[], user_meta: string[], use_site_option: bool, optin_option: string}
	 */
	function wpb_sdk_get_uninstall_cleanup_manifest( $slug ) {
		$empty = array(
			'options'         => array(),
			'user_meta'       => array(),
			'use_site_option' => false,
			'optin_option'    => '',
		);

		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return $empty;
		}

		$raw = get_option( 'wpb_sdk_' . $slug . '_uninstall_manifest', '' );
		if ( is_array( $raw ) ) {
			$data = $raw;
		} else {
			$data = json_decode( (string) $raw, true );
		}

		if ( ! is_array( $data ) ) {
			return $empty;
		}

		return array(
			'options'         => ! empty( $data['options'] ) && is_array( $data['options'] )
				? array_map( 'strval', $data['options'] )
				: array(),
			'user_meta'       => ! empty( $data['user_meta'] ) && is_array( $data['user_meta'] )
				? array_map( 'strval', $data['user_meta'] )
				: array(),
			'use_site_option' => ! empty( $data['use_site_option'] ),
			'optin_option'    => ! empty( $data['optin_option'] ) ? (string) $data['optin_option'] : '',
		);
	}
}

if ( ! function_exists( 'wpb_sdk_query_option_names_by_prefix' ) ) {
	/**
	 * @param string $prefix Option name prefix (no trailing %).
	 * @return string[]
	 */
	function wpb_sdk_query_option_names_by_prefix( $prefix ) {
		global $wpdb;

		$prefix = (string) $prefix;
		if ( '' === $prefix ) {
			return array();
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( $prefix ) . '%'
			)
		);

		return is_array( $rows ) ? array_map( 'strval', $rows ) : array();
	}
}

if ( ! function_exists( 'wpb_sdk_delete_all_users_meta_key' ) ) {
	/**
	 * @param string $meta_key User meta key.
	 * @return void
	 */
	function wpb_sdk_delete_all_users_meta_key( $meta_key ) {
		$meta_key = (string) $meta_key;
		if ( '' === $meta_key ) {
			return;
		}

		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Bulk delete on uninstall.
		$wpdb->delete(
			$wpdb->usermeta,
			array( 'meta_key' => $meta_key ),
			array( '%s' )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	}
}

if ( ! function_exists( 'wpb_sdk_foreach_site_on_uninstall' ) ) {
	/**
	 * Run cleanup on the current site or every blog on multisite.
	 *
	 * @param callable $callback Receives blog ID.
	 * @return void
	 */
	function wpb_sdk_foreach_site_on_uninstall( $callback ) {
		if ( ! is_callable( $callback ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			$callback( (int) get_current_blog_id() );
			return;
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		if ( ! is_array( $blog_ids ) ) {
			return;
		}

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( (int) $blog_id );
			$callback( (int) $blog_id );
			restore_current_blog();
		}
	}
}

if ( ! function_exists( 'wpb_sdk_cleanup_data_on_uninstall' ) ) {
	/**
	 * Remove all SDK options and opt-in user meta for a product on uninstall.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_cleanup_data_on_uninstall( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		/**
		 * Whether the SDK should delete its options and user meta on uninstall.
		 *
		 * @param bool   $cleanup Default true.
		 * @param string $slug    Product slug.
		 */
		if ( ! apply_filters( 'wpb_sdk_should_cleanup_on_uninstall', true, $slug ) ) {
			return;
		}

		do_action( 'wpb_sdk_bootstrap_module', $slug );

		$manifest   = wpb_sdk_get_uninstall_cleanup_manifest( $slug );
		$options    = $manifest['options'];
		$user_meta  = $manifest['user_meta'];
		$use_site   = $manifest['use_site_option'];
		$optin_name = $manifest['optin_option'];

		$options = array_unique(
			array_merge( $options, wpb_sdk_query_option_names_by_prefix( 'wpb_sdk_' . $slug ) )
		);

		$module = function_exists( 'wpb_sdk_get_registered_module' )
			? wpb_sdk_get_registered_module( $slug )
			: array();
		if ( ! empty( $module ) ) {
			$options   = array_unique(
				array_merge( $options, wpb_sdk_build_uninstall_option_names_from_module( $slug, $module ) )
			);
			$user_meta = array_unique(
				array_merge( $user_meta, wpb_sdk_build_uninstall_user_meta_keys_from_module( $module ) )
			);
		}

		/**
		 * @param string[] $options  Option names to delete.
		 * @param string   $slug     Product slug.
		 */
		$options = apply_filters( 'wpb_sdk_uninstall_option_names', $options, $slug );

		$dismiss_meta = wpb_sdk_verification_notice_dismissed_meta_key( $slug );
		if ( '' !== $dismiss_meta ) {
			$user_meta[] = $dismiss_meta;
		}
		$user_meta = array_values( array_unique( array_filter( $user_meta ) ) );

		/**
		 * @param string[] $user_meta User meta keys to delete for all users.
		 * @param string   $slug      Product slug.
		 */
		$user_meta = apply_filters( 'wpb_sdk_uninstall_user_meta_keys', $user_meta, $slug );

		delete_transient( 'wpb_sdk_' . $slug . '_pending_verify_notice' );

		if ( $use_site && '' !== $optin_name && is_multisite() ) {
			delete_site_option( $optin_name );
		}

		wpb_sdk_foreach_site_on_uninstall(
			static function () use ( $options, $user_meta, $use_site, $optin_name ) {
				foreach ( $options as $option_name ) {
					if ( '' === $option_name ) {
						continue;
					}
					if ( $use_site && $option_name === $optin_name && is_multisite() ) {
						delete_site_option( $option_name );
					}
					delete_option( $option_name );
				}

				foreach ( $user_meta as $meta_key ) {
					wpb_sdk_delete_all_users_meta_key( $meta_key );
				}
			}
		);
	}
}

if ( ! function_exists( 'wpb_sdk_should_redirect_from_optin_page' ) ) {
	/**
	 * Leave the opt-in admin screen only after the user has allowed (yes).
	 *
	 * Skip and no must stay on the opt-in page so Opt In from Plugins can show the splash again.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_should_redirect_from_optin_page( $slug ) {
		$decision = wpb_sdk_get_optin_decision( $slug );

		/**
		 * Filter whether visiting the opt-in page should redirect away.
		 *
		 * @param bool   $redirect True when decision is yes (already opted in).
		 * @param string $slug     Product slug.
		 * @param string $decision Raw opt-in option value.
		 */
		return (bool) apply_filters(
			'wpb_sdk_should_redirect_from_optin_page',
			'yes' === $decision,
			$slug,
			$decision
		);
	}
}

if ( ! function_exists( 'wpb_sdk_module_requires_optin_consent' ) ) {
	/**
	 * Whether this product shows an opt-in modal before telemetry may be sent.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_module_requires_optin_consent( $slug ) {
		$module = wpb_sdk_get_registered_module( $slug );
		$optin  = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();

		return ! empty( $optin['option_name'] );
	}
}

if ( ! function_exists( 'wpb_sdk_has_telemetry_consent' ) ) {
	/**
	 * True when the admin has completed the opt-in modal (yes, no, or skip).
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_has_telemetry_consent( $slug ) {
		if ( ! wpb_sdk_module_requires_optin_consent( $slug ) ) {
			return true;
		}

		$decision = wpb_sdk_get_optin_decision( $slug );

		return in_array( $decision, array( 'yes', 'no', 'skip' ), true );
	}
}

if ( ! function_exists( 'wpb_sdk_user_skipped_optin' ) ) {
	/**
	 * Whether the user chose "Skip" (no ongoing telemetry).
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_user_skipped_optin( $slug ) {
		if ( 'skip' === wpb_sdk_get_optin_decision( $slug ) ) {
			return true;
		}

		$sdk_data = json_decode( (string) get_option( 'wpb_sdk_' . $slug, '' ), true );

		return is_array( $sdk_data )
			&& isset( $sdk_data['user_skip'] )
			&& '1' === (string) $sdk_data['user_skip'];
	}
}

if ( ! function_exists( 'wpb_sdk_allows_ongoing_telemetry' ) ) {
	/**
	 * Whether recurring / lifecycle telemetry may be sent (opt-in "yes" only).
	 *
	 * Skip and no send at most one activate payload; see wpb_sdk_may_send_telemetry_action().
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_allows_ongoing_telemetry( $slug ) {
		if ( ! wpb_sdk_module_requires_optin_consent( $slug ) ) {
			return true;
		}

		return 'yes' === wpb_sdk_get_optin_decision( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_verification_token_ttl_days' ) ) {
	/**
	 * Days until the email verification link expires (per-product override).
	 *
	 * @param string $slug Product slug.
	 * @return int
	 */
	function wpb_sdk_verification_token_ttl_days( $slug ) {
		$module    = wpb_sdk_get_registered_module( $slug );
		$telemetry = isset( $module['telemetry'] ) && is_array( $module['telemetry'] ) ? $module['telemetry'] : array();
		$days      = isset( $telemetry['verification_token_ttl_days'] )
			? (int) $telemetry['verification_token_ttl_days']
			: 14;

		return max( 1, min( 90, $days ) );
	}
}

if ( ! function_exists( 'wpb_sdk_verification_token_expires_meta_key' ) ) {
	/**
	 * User meta key storing verification token expiry (Unix timestamp).
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return string
	 */
	function wpb_sdk_verification_token_expires_meta_key( array $module ) {
		$token_meta = ! empty( $module['optin_user_meta']['token'] )
			? (string) $module['optin_user_meta']['token']
			: '';

		return '' !== $token_meta ? $token_meta . '_expires' : '';
	}
}

if ( ! function_exists( 'wpb_sdk_verification_token_expires_at' ) ) {
	/**
	 * Unix timestamp when the current verification token should expire.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return int
	 */
	function wpb_sdk_verification_token_expires_at( array $module ) {
		$slug = isset( $module['slug'] ) ? (string) $module['slug'] : '';
		$days = '' !== $slug ? wpb_sdk_verification_token_ttl_days( $slug ) : 14;

		return time() + ( $days * DAY_IN_SECONDS );
	}
}

if ( ! function_exists( 'wpb_sdk_ensure_verification_token_expiry_meta' ) ) {
	/**
	 * Return token expiry timestamp; backfill TTL when a token exists but expiry meta is missing.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional admin user ID.
	 * @return int Unix expiry timestamp, or 0 when no token.
	 */
	function wpb_sdk_ensure_verification_token_expiry_meta( $slug, $user_id = 0 ) {
		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module['optin_user_meta']['token'] ) ) {
			return 0;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 && function_exists( 'wpb_sdk_resolve_optin_verification_user_id' ) ) {
			$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		}
		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}

		if ( $user_id < 1 ) {
			return 0;
		}

		$token_meta   = (string) $module['optin_user_meta']['token'];
		$expires_meta = wpb_sdk_verification_token_expires_meta_key( $module );
		$token        = get_user_meta( $user_id, $token_meta, true );

		if ( ! is_string( $token ) || '' === $token || '' === $expires_meta ) {
			return 0;
		}

		$expires_at = (int) get_user_meta( $user_id, $expires_meta, true );

		if ( $expires_at < 1 ) {
			$expires_at = wpb_sdk_verification_token_expires_at( $module );
			update_user_meta( $user_id, $expires_meta, $expires_at );
		}

		return $expires_at;
	}
}

if ( ! function_exists( 'wpb_sdk_is_verification_token_expired' ) ) {
	/**
	 * Whether the stored verification token is past its TTL.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional admin user ID.
	 * @return bool
	 */
	function wpb_sdk_is_verification_token_expired( $slug, $user_id = 0 ) {
		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module['optin_user_meta']['token'] ) ) {
			return false;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			return false;
		}

		$token_meta = (string) $module['optin_user_meta']['token'];
		$token      = get_user_meta( $user_id, $token_meta, true );
		if ( ! is_string( $token ) || '' === $token ) {
			return false;
		}

		$expires_at = wpb_sdk_ensure_verification_token_expiry_meta( $slug, $user_id );
		if ( $expires_at < 1 ) {
			return false;
		}

		return time() > $expires_at;
	}
}

if ( ! function_exists( 'wpb_sdk_has_pending_verification_token' ) ) {
	/**
	 * Whether a verification email was issued and the link was not used yet.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_has_pending_verification_token( $slug, $user_id = 0 ) {
		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module['optin_user_meta']['token'] ) ) {
			return false;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 && function_exists( 'wpb_sdk_resolve_optin_verification_user_id' ) ) {
			$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		}
		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}

		$token_meta = (string) $module['optin_user_meta']['token'];
		if ( $user_id > 0 ) {
			$pending_token = get_user_meta( $user_id, $token_meta, true );
			if ( is_string( $pending_token ) && '' !== $pending_token ) {
				return true;
			}
		}

		$fallback = get_option( 'wpb_sdk_' . $slug . '_fallback_verify_token', '' );

		return is_string( $fallback ) && '' !== $fallback;
	}
}

if ( ! function_exists( 'wpb_sdk_optin_email_verified_meta_key' ) ) {
	/**
	 * Default trusted email verification user-meta key for a product.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_optin_email_verified_meta_key( $slug ) {
		return '_' . sanitize_key( (string) $slug ) . '_email_verified';
	}
}

if ( ! function_exists( 'wpb_sdk_email_verified_meta_key_from_module' ) ) {
	/**
	 * Resolve the email_verified user-meta key from module config.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return string
	 */
	function wpb_sdk_email_verified_meta_key_from_module( array $module ) {
		$meta = isset( $module['optin_user_meta'] ) && is_array( $module['optin_user_meta'] )
			? $module['optin_user_meta']
			: array();

		if ( ! empty( $meta['email_verified'] ) ) {
			return (string) $meta['email_verified'];
		}

		if ( ! empty( $module['slug'] ) ) {
			return wpb_sdk_optin_email_verified_meta_key( (string) $module['slug'] );
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_normalize_email_verified_meta_key' ) ) {
	/**
	 * Normalize configured meta keys to _slug_email_verified.
	 *
	 * @param string $meta_key Configured or legacy meta key.
	 * @param string $slug     Product slug.
	 * @return string
	 */
	function wpb_sdk_normalize_email_verified_meta_key( $meta_key, $slug ) {
		$meta_key = (string) $meta_key;
		if ( '' === $meta_key ) {
			return wpb_sdk_optin_email_verified_meta_key( $slug );
		}

		if ( function_exists( 'str_ends_with' ) && str_ends_with( $meta_key, '_email_verified' ) ) {
			return $meta_key;
		}

		return wpb_sdk_optin_email_verified_meta_key( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_format_email_verified_meta_value' ) ) {
	/**
	 * Encode status + timestamp for _slug_email_verified user meta.
	 *
	 * @param string $verified_at MySQL datetime.
	 * @return string JSON string.
	 */
	function wpb_sdk_format_email_verified_meta_value( $verified_at = '' ) {
		if ( '' === (string) $verified_at ) {
			$verified_at = current_time( 'mysql' );
		}

		return wp_json_encode(
			array(
				'status'      => 'yes',
				'verified_at' => (string) $verified_at,
			)
		);
	}
}

if ( ! function_exists( 'wpb_sdk_parse_email_verified_meta_value' ) ) {
	/**
	 * Parse _slug_email_verified user meta JSON.
	 *
	 * @param mixed $raw Raw user meta value.
	 * @return array{status: string, verified_at: string}|null
	 */
	function wpb_sdk_parse_email_verified_meta_value( $raw ) {
		if ( ! is_string( $raw ) || '' === $raw ) {
			return null;
		}

		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) && ! empty( $decoded['status'] ) ) {
			return array(
				'status'      => (string) $decoded['status'],
				'verified_at' => isset( $decoded['verified_at'] ) ? (string) $decoded['verified_at'] : '',
			);
		}

		return null;
	}
}

if ( ! function_exists( 'wpb_sdk_is_email_verified_meta_value' ) ) {
	/**
	 * Whether a stored email_verified meta value means verified.
	 *
	 * @param mixed $raw Raw user meta value.
	 * @return bool
	 */
	function wpb_sdk_is_email_verified_meta_value( $raw ) {
		$parsed = wpb_sdk_parse_email_verified_meta_value( $raw );

		return is_array( $parsed ) && 'yes' === strtolower( $parsed['status'] );
	}
}

if ( ! function_exists( 'wpb_sdk_set_user_email_verified' ) ) {
	/**
	 * Persist trusted email verification (status + timestamp in one meta key).
	 *
	 * @param string $slug            Product slug.
	 * @param int    $user_id         WordPress user ID.
	 * @param string $verified_at     Optional MySQL datetime.
	 * @return bool
	 */
	function wpb_sdk_set_user_email_verified( $slug, $user_id, $verified_at = '' ) {
		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			return false;
		}

		$module = wpb_sdk_get_registered_module( $slug );
		$key    = wpb_sdk_email_verified_meta_key_from_module( $module );
		if ( '' === $key ) {
			return false;
		}

		update_user_meta(
			$user_id,
			$key,
			wpb_sdk_format_email_verified_meta_value( $verified_at )
		);

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_legacy_optin_verified_v1_meta_key' ) ) {
	/**
	 * Pre-v2 verified user meta key for a product slug.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_legacy_optin_verified_v1_meta_key( $slug ) {
		return '_' . sanitize_key( (string) $slug ) . '_optin_verified';
	}
}

if ( ! function_exists( 'wpb_sdk_legacy_verification_user_meta_keys_from_module' ) ) {
	/**
	 * Untrusted legacy verification meta keys to remove on uninstall or re-opt-in.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return string[]
	 */
	function wpb_sdk_legacy_verification_user_meta_keys_from_module( array $module ) {
		$slug = ! empty( $module['slug'] ) ? sanitize_key( (string) $module['slug'] ) : '';
		if ( '' === $slug ) {
			return array();
		}

		$keys   = array();
		$v1     = wpb_sdk_legacy_optin_verified_v1_meta_key( $slug );
		$keys[] = $v1;
		$keys[] = $v1 . '_via_email';

		$current = wpb_sdk_email_verified_meta_key_from_module( $module );
		if ( '' !== $current ) {
			$keys[] = $current . '_via_email';
		}

		return array_values( array_unique( array_filter( $keys ) ) );
	}
}

if ( ! function_exists( 'wpb_sdk_bootstrap_ready_for_user_api' ) ) {
	/**
	 * WordPress user APIs (get_users, get_user_by, capabilities) are unsafe before plugins_loaded.
	 *
	 * @return bool
	 */
	function wpb_sdk_bootstrap_ready_for_user_api() {
		return did_action( 'plugins_loaded' ) > 0;
	}
}

if ( ! function_exists( 'wpb_sdk_get_administrator_users' ) ) {
	/**
	 * Query administrators without running third-party pre_get_users filters.
	 *
	 * @param array<string, mixed> $args Optional get_users() arguments.
	 * @return array<int, WP_User|int|string>
	 */
	function wpb_sdk_get_administrator_users( array $args = array() ) {
		if ( ! wpb_sdk_bootstrap_ready_for_user_api() ) {
			return array();
		}

		$query = array_merge(
			array(
				'role'             => 'administrator',
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'suppress_filters' => true,
			),
			$args
		);

		$users = get_users( $query );

		return is_array( $users ) ? $users : array();
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_optin_admin_user_id' ) ) {
	/**
	 * Primary site administrator for opt-in / verification checks.
	 *
	 * @param string $slug    Product slug (unused; reserved for filters).
	 * @param int    $user_id Optional explicit user ID.
	 * @return int
	 */
	function wpb_sdk_resolve_optin_admin_user_id( $slug, $user_id = 0 ) {
		$user_id = (int) $user_id;
		if ( $user_id > 0 ) {
			return $user_id;
		}

		$admins = wpb_sdk_get_administrator_users(
			array(
				'number' => 1,
				'fields' => 'ID',
			)
		);

		return ! empty( $admins[0] ) ? (int) $admins[0] : 0;
	}
}

if ( ! function_exists( 'wpb_sdk_user_has_manage_options' ) ) {
	/**
	 * Whether a user ID is an administrator with manage_options.
	 *
	 * Uses get_user_by() + WP_User::has_cap() so we never call user_can( int )
	 * (that path uses get_userdata(), which is not loaded during early bootstrap).
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_user_has_manage_options( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id < 1 || ! wpb_sdk_bootstrap_ready_for_user_api() || ! function_exists( 'get_user_by' ) ) {
			return false;
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! ( $user instanceof WP_User ) ) {
			return false;
		}

		return in_array( 'administrator', (array) $user->roles, true );
	}
}

if ( ! function_exists( 'wpb_sdk_optin_initiator_option_key' ) ) {
	/**
	 * Site option storing the admin user ID who last clicked Allow.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_optin_initiator_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_optin_initiator';
	}
}

if ( ! function_exists( 'wpb_sdk_set_optin_initiator' ) ) {
	/**
	 * Remember which admin initiated opt-in (verification email recipient).
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id WordPress user ID.
	 * @return void
	 */
	function wpb_sdk_set_optin_initiator( $slug, $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id < 1 || ! wpb_sdk_user_has_manage_options( $user_id ) ) {
			return;
		}

		update_option( wpb_sdk_optin_initiator_option_key( $slug ), $user_id, false );
	}
}

if ( ! function_exists( 'wpb_sdk_optin_initiator_is_valid' ) ) {
	/**
	 * Whether a stored opt-in initiator is still an active administrator.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_optin_initiator_is_valid( $user_id ) {
		return wpb_sdk_user_has_manage_options( (int) $user_id );
	}
}

if ( ! function_exists( 'wpb_sdk_get_active_product_slugs' ) ) {
	/**
	 * Product slugs registered with the SDK this request.
	 *
	 * @return string[]
	 */
	function wpb_sdk_get_active_product_slugs() {
		if (
			! isset( $GLOBALS['wpb_sdk_registry']['modules'] )
			|| ! is_array( $GLOBALS['wpb_sdk_registry']['modules'] )
		) {
			return array();
		}

		return array_keys( $GLOBALS['wpb_sdk_registry']['modules'] );
	}
}

if ( ! function_exists( 'wpb_sdk_clear_verification_dispatch_state' ) ) {
	/**
	 * Clear site-level verification UI state so a new contact can be shown.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_clear_verification_dispatch_state( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		delete_transient( 'wpb_sdk_' . $slug . '_pending_verify_notice' );
		delete_transient( 'wpb_sdk_' . $slug . '_verify_email_dispatched' );
		delete_option( 'wpb_sdk_' . $slug . '_fallback_verify_token' );

		if ( ! wpb_sdk_bootstrap_ready_for_user_api() ) {
			return;
		}

		foreach ( wpb_sdk_get_administrator_users( array( 'fields' => 'ID' ) ) as $admin_id ) {
			delete_transient( 'wpb_sdk_' . $slug . '_verify_resend_' . (int) $admin_id );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_reconcile_telemetry_contact_after_change' ) ) {
	/**
	 * Reset initiator / verification dispatch when the telemetry contact user changed.
	 *
	 * @param string $slug            Product slug.
	 * @param int    $removed_user_id Deleted or demoted WordPress user ID, if known.
	 * @return void
	 */
	function wpb_sdk_reconcile_telemetry_contact_after_change( $slug, $removed_user_id = 0 ) {
		$slug            = sanitize_key( (string) $slug );
		$removed_user_id = (int) $removed_user_id;
		if ( '' === $slug ) {
			return;
		}

		$stored = (int) get_option( wpb_sdk_optin_initiator_option_key( $slug ), 0 );
		if ( $removed_user_id > 0 && $stored === $removed_user_id ) {
			delete_option( wpb_sdk_optin_initiator_option_key( $slug ) );
		} else {
			wpb_sdk_clear_stale_optin_initiator( $slug );
		}

		wpb_sdk_clear_verification_dispatch_state( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_user_was_telemetry_contact_for_product' ) ) {
	/**
	 * Whether a user was the SDK telemetry / verification contact for a product.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_user_was_telemetry_contact_for_product( $slug, $user_id ) {
		$slug    = sanitize_key( (string) $slug );
		$user_id = (int) $user_id;
		if ( '' === $slug || $user_id < 1 ) {
			return false;
		}

		if ( (int) get_option( wpb_sdk_optin_initiator_option_key( $slug ), 0 ) === $user_id ) {
			return true;
		}

		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module['optin_user_meta']['token'] ) ) {
			return false;
		}

		$token_meta = (string) $module['optin_user_meta']['token'];
		$token      = get_user_meta( $user_id, $token_meta, true );
		if ( is_string( $token ) && '' !== $token ) {
			return true;
		}

		$verified_key = wpb_sdk_email_verified_meta_key_from_module( $module );
		if ( '' === $verified_key ) {
			return false;
		}

		return wpb_sdk_is_email_verified_meta_value( get_user_meta( $user_id, $verified_key, true ) );
	}
}

if ( ! function_exists( 'wpb_sdk_handle_deleted_user_telemetry_contact' ) ) {
	/**
	 * When an admin who opted in / verified is deleted, fall back to the next contact.
	 *
	 * @param int      $user_id      Deleted user ID.
	 * @param int|null $reassign_id  Reassignment user ID (unused).
	 * @return void
	 */
	function wpb_sdk_handle_deleted_user_telemetry_contact( $user_id, $reassign_id = null ) {
		unset( $reassign_id );

		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			return;
		}

		$slugs = wpb_sdk_get_active_product_slugs();
		if ( empty( $slugs ) ) {
			global $wpdb;

			$like = $wpdb->esc_like( 'wpb_sdk_' ) . '%' . $wpdb->esc_like( '_optin_initiator' );
			$rows = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
					$like
				)
			);

			foreach ( (array) $rows as $option_name ) {
				if ( preg_match( '#^wpb_sdk_(.+)_optin_initiator$#', (string) $option_name, $matches ) ) {
					$slugs[] = sanitize_key( (string) $matches[1] );
				}
			}

			$slugs = array_values( array_unique( array_filter( $slugs ) ) );
		}

		foreach ( $slugs as $slug ) {
			if ( ! wpb_sdk_user_was_telemetry_contact_for_product( $slug, $user_id ) ) {
				continue;
			}

			wpb_sdk_reconcile_telemetry_contact_after_change( $slug, $user_id );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_clear_stale_optin_initiator' ) ) {
	/**
	 * Remove stored initiator when that WordPress user was deleted or demoted.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_clear_stale_optin_initiator( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		$stored = (int) get_option( wpb_sdk_optin_initiator_option_key( $slug ), 0 );
		if ( $stored < 1 || wpb_sdk_optin_initiator_is_valid( $stored ) ) {
			return;
		}

		delete_option( wpb_sdk_optin_initiator_option_key( $slug ) );
		wpb_sdk_clear_verification_dispatch_state( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_telemetry_contact_fallback_option_key' ) ) {
	/**
	 * How to pick telemetry email when the opt-in initiator is missing.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_telemetry_contact_fallback_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_telemetry_contact_fallback';
	}
}

if ( ! function_exists( 'wpb_sdk_telemetry_contact_cohort_option_key' ) ) {
	/**
	 * Human-readable cohort label for support / debug (not used in payload).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_telemetry_contact_cohort_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_telemetry_contact_cohort';
	}
}

if ( ! function_exists( 'wpb_sdk_set_telemetry_contact_cohort' ) ) {
	/**
	 * @param string $slug   Product slug.
	 * @param string $cohort initiator_fresh|pre_32_login|centralized_32_primary
	 * @return void
	 */
	function wpb_sdk_set_telemetry_contact_cohort( $slug, $cohort ) {
		$slug   = sanitize_key( (string) $slug );
		$cohort = sanitize_key( (string) $cohort );
		if ( '' === $slug ) {
			return;
		}

		$allowed = array( 'initiator_fresh', 'pre_32_login', 'centralized_32_primary' );
		if ( ! in_array( $cohort, $allowed, true ) ) {
			return;
		}

		update_option( wpb_sdk_telemetry_contact_cohort_option_key( $slug ), $cohort, false );
	}
}

if ( ! function_exists( 'wpb_sdk_get_telemetry_contact_cohort' ) ) {
	/**
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_get_telemetry_contact_cohort( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return '';
		}

		return sanitize_key( (string) get_option( wpb_sdk_telemetry_contact_cohort_option_key( $slug ), '' ) );
	}
}

if ( ! function_exists( 'wpb_sdk_optin_initiator_backfill_version' ) ) {
	/**
	 * Bump when tiered backfill rules change (allows one-time re-pin).
	 *
	 * @return int
	 */
	function wpb_sdk_optin_initiator_backfill_version() {
		return 3;
	}
}

if ( ! function_exists( 'wpb_sdk_optin_initiator_backfill_version_option_key' ) ) {
	/**
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_optin_initiator_backfill_version_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_optin_initiator_backfill_version';
	}
}

if ( ! function_exists( 'wpb_sdk_legacy_optin_consent_is_active' ) ) {
	/**
	 * Whether this product has an active Allow (not Skip) opt-in on record.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_legacy_optin_consent_is_active( $slug ) {
		if ( wpb_sdk_user_skipped_optin( $slug ) || 'skip' === wpb_sdk_get_optin_decision( $slug ) ) {
			return false;
		}

		$slug    = sanitize_key( (string) $slug );
		$sdk_raw = get_option( 'wpb_sdk_' . $slug, '' );
		$sdk     = is_string( $sdk_raw ) ? json_decode( $sdk_raw, true ) : $sdk_raw;

		if ( is_array( $sdk ) ) {
			foreach ( array( 'communication', 'diagnostic_info', 'extensions' ) as $flag ) {
				if ( wpb_sdk_sdk_option_is_enabled( $sdk[ $flag ] ?? '0' ) ) {
					return true;
				}
			}

			return false;
		}

		return 'yes' === wpb_sdk_get_optin_decision( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_explain_telemetry_contact_resolution' ) ) {
	/**
	 * Debug snapshot: how the SDK would pick telemetry user_email today.
	 *
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	function wpb_sdk_explain_telemetry_contact_resolution( $slug ) {
		$slug = sanitize_key( (string) $slug );

		$initiator_id = (int) get_option( wpb_sdk_optin_initiator_option_key( $slug ), 0 );
		$contact_id   = wpb_sdk_resolve_telemetry_contact_user_id( $slug );
		$contact      = $contact_id > 0 ? get_user_by( 'id', $contact_id ) : null;

		return array(
			'slug'                      => $slug,
			'cohort'                    => wpb_sdk_get_telemetry_contact_cohort( $slug ),
			'fallback_mode'             => wpb_sdk_get_telemetry_contact_fallback_mode( $slug ),
			'used_centralized_sdk_32'   => wpb_sdk_site_used_centralized_sdk_32( $slug ),
			'initiator_user_id'         => $initiator_id,
			'initiator_valid'           => wpb_sdk_optin_initiator_is_valid( $initiator_id ),
			'resolved_user_id'          => $contact_id,
			'resolved_email'            => ( $contact instanceof WP_User ) ? $contact->user_email : '',
			'legacy_login_admin_id'     => wpb_sdk_resolve_legacy_pre_32_admin_user_id( $slug ),
			'primary_admin_id'          => wpb_sdk_resolve_optin_admin_user_id( $slug, 0 ),
			'backfill_version'          => (int) get_option( wpb_sdk_optin_initiator_backfill_version_option_key( $slug ), 0 ),
			'backfill_complete'         => '1' === (string) get_option( wpb_sdk_optin_initiator_backfill_option_key( $slug ), '' ),
		);
	}
}

if ( ! function_exists( 'wpb_sdk_set_telemetry_contact_fallback_mode' ) ) {
	/**
	 * @param string $slug Product slug.
	 * @param string $mode primary_admin|legacy_login_admin
	 * @return void
	 */
	function wpb_sdk_set_telemetry_contact_fallback_mode( $slug, $mode ) {
		$slug = sanitize_key( (string) $slug );
		$mode = sanitize_key( (string) $mode );
		if ( '' === $slug ) {
			return;
		}

		if ( ! in_array( $mode, array( 'primary_admin', 'legacy_login_admin' ), true ) ) {
			$mode = 'primary_admin';
		}

		update_option( wpb_sdk_telemetry_contact_fallback_option_key( $slug ), $mode, false );
	}
}

if ( ! function_exists( 'wpb_sdk_get_telemetry_contact_fallback_mode' ) ) {
	/**
	 * @param string $slug Product slug.
	 * @return string primary_admin|legacy_login_admin|'' when not set yet.
	 */
	function wpb_sdk_get_telemetry_contact_fallback_mode( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return '';
		}

		$stored = get_option( wpb_sdk_telemetry_contact_fallback_option_key( $slug ), null );
		if ( null === $stored || '' === $stored ) {
			return '';
		}

		$stored = sanitize_key( (string) $stored );
		if ( 'legacy_login_admin' === $stored ) {
			return 'legacy_login_admin';
		}

		return 'primary_admin';
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_legacy_pre_32_admin_user_id' ) ) {
	/**
	 * Pre-3.2.0 bundled SDK (plugin 6.2.2 era): first administrator by login name.
	 *
	 * Matches legacy get_users( role => Administrator )[0] with default login ASC.
	 *
	 * @param string $slug Product slug (unused; reserved for filters).
	 * @return int
	 */
	function wpb_sdk_resolve_legacy_pre_32_admin_user_id( $slug ) {
		$users = get_users(
			array(
				'role'    => 'administrator',
				'orderby' => 'login',
				'order'   => 'ASC',
			)
		);

		if ( empty( $users[0] ) ) {
			$users = get_users(
				array(
					'role'    => 'Administrator',
					'orderby' => 'login',
					'order'   => 'ASC',
				)
			);
		}

		if ( ! empty( $users[0] ) ) {
			$user = $users[0];
			if ( $user instanceof WP_User ) {
				return (int) $user->ID;
			}
			if ( is_numeric( $user ) ) {
				return (int) $user;
			}
		}

		return wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
	}
}

if ( ! function_exists( 'wpb_sdk_site_used_centralized_sdk_32' ) ) {
	/**
	 * Whether this site already ran centralized SDK 3.2.0+ (e.g. LoginPress 6.2.3).
	 *
	 * Pure 6.2.2 / SDK 3.1.x bundles never set these markers.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_site_used_centralized_sdk_32( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return false;
		}

		$option_prefix = 'wpb_sdk_' . $slug;

		if ( '1' === (string) get_option( $option_prefix . '_initial_log_sent', '' ) ) {
			return true;
		}

		if ( '' !== (string) get_option( $option_prefix . '_fallback_verify_token', '' ) ) {
			return true;
		}

		if ( false !== get_option( wpb_sdk_legacy_upgrade_optin_option_key( $slug ), false ) ) {
			return true;
		}

		$module = function_exists( 'wpb_sdk_get_registered_module' )
			? wpb_sdk_get_registered_module( $slug )
			: array();

		$token_meta   = ! empty( $module['optin_user_meta']['token'] )
			? (string) $module['optin_user_meta']['token']
			: '';
		$verified_key = function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
			? wpb_sdk_email_verified_meta_key_from_module( $module )
			: '';

		if ( '' === $token_meta && '' === $verified_key ) {
			return (bool) apply_filters( 'wpb_sdk_site_used_centralized_sdk_32', false, $slug, $module );
		}

		$admins = wpb_sdk_get_administrator_users( array( 'fields' => 'ID' ) );
		foreach ( $admins as $admin_id ) {
			$admin_id = (int) $admin_id;
			if ( $admin_id < 1 ) {
				continue;
			}

			if ( '' !== $token_meta ) {
				$token = get_user_meta( $admin_id, $token_meta, true );
				if ( is_string( $token ) && '' !== $token ) {
					return true;
				}
			}

			if ( '' !== $verified_key ) {
				$verified_raw = get_user_meta( $admin_id, $verified_key, true );
				if ( function_exists( 'wpb_sdk_is_email_verified_meta_value' )
					&& wpb_sdk_is_email_verified_meta_value( $verified_raw ) ) {
					return true;
				}
			}
		}

		return (bool) apply_filters( 'wpb_sdk_site_used_centralized_sdk_32', false, $slug, $module );
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_telemetry_contact_fallback_user_id' ) ) {
	/**
	 * Pick telemetry email when no valid opt-in initiator exists.
	 *
	 * @param string $slug Product slug.
	 * @return int
	 */
	function wpb_sdk_resolve_telemetry_contact_fallback_user_id( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return 0;
		}

		$mode = wpb_sdk_get_telemetry_contact_fallback_mode( $slug );
		if ( 'legacy_login_admin' === $mode ) {
			return wpb_sdk_resolve_legacy_pre_32_admin_user_id( $slug );
		}
		if ( 'primary_admin' === $mode ) {
			return wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}

		// Fresh opt-ins (post-3.3.1 Allow/Skip): lowest-ID admin when initiator is gone.
		if ( 'initiator_fresh' === wpb_sdk_get_telemetry_contact_cohort( $slug ) ) {
			return wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}

		// All legacy opted-in sites (SDK 3.1.x and LoginPress 6.2.3): SDK 3.1.0 login order.
		if ( wpb_sdk_legacy_optin_consent_is_active( $slug ) ) {
			return wpb_sdk_resolve_legacy_pre_32_admin_user_id( $slug );
		}

		return wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_telemetry_contact_user_id' ) ) {
	/**
	 * Telemetry contact: opt-in initiator while valid, otherwise cohort-specific fallback.
	 *
	 * @param string $slug Product slug.
	 * @return int
	 */
	function wpb_sdk_resolve_telemetry_contact_user_id( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return 0;
		}

		wpb_sdk_clear_stale_optin_initiator( $slug );

		$initiator = (int) get_option( wpb_sdk_optin_initiator_option_key( $slug ), 0 );
		if ( wpb_sdk_optin_initiator_is_valid( $initiator ) ) {
			return $initiator;
		}

		return wpb_sdk_resolve_telemetry_contact_fallback_user_id( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_get_telemetry_contact_user' ) ) {
	/**
	 * WordPress user for telemetry payload email and verification notices.
	 *
	 * @param string $slug Product slug.
	 * @return WP_User|null
	 */
	function wpb_sdk_get_telemetry_contact_user( $slug ) {
		$user_id = wpb_sdk_resolve_telemetry_contact_user_id( $slug );
		if ( $user_id < 1 ) {
			return null;
		}

		$user = get_user_by( 'id', $user_id );
		if ( $user instanceof WP_User ) {
			return $user;
		}

		// Stale pointer (deleted user): drop and resolve fallback contact once.
		delete_option( wpb_sdk_optin_initiator_option_key( $slug ) );
		wpb_sdk_clear_verification_dispatch_state( $slug );

		$fallback_id = wpb_sdk_resolve_telemetry_contact_fallback_user_id( $slug );
		if ( $fallback_id < 1 ) {
			return null;
		}

		$user = get_user_by( 'id', $fallback_id );

		return ( $user instanceof WP_User ) ? $user : null;
	}
}

if ( ! function_exists( 'wpb_sdk_optin_initiator_backfill_option_key' ) ) {
	/**
	 * One-time flag: legacy opted-in sites pinned to alphabetical admin as initiator.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_optin_initiator_backfill_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_optin_initiator_backfilled';
	}
}

if ( ! function_exists( 'wpb_sdk_maybe_backfill_optin_initiator_for_legacy_site' ) ) {
	/**
	 * One-time backfill for sites opted in before SDK 3.3.1 initiator tracking.
	 *
	 * All legacy opted-in sites (SDK 3.1.x on six plugins, LoginPress 6.2.2, and
	 * LoginPress 6.2.3) are pinned to the SDK 3.1.0 rule: first administrator by
	 * login name. That restores the original telemetry email and avoids new rows
	 * after the 6.2.3 lowest-ID change.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_maybe_backfill_optin_initiator_for_legacy_site( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug || ! wpb_sdk_bootstrap_ready_for_user_api() ) {
			return;
		}

		$target_version   = wpb_sdk_optin_initiator_backfill_version();
		$stored_version   = (int) get_option( wpb_sdk_optin_initiator_backfill_version_option_key( $slug ), 0 );
		$legacy_flag      = '1' === (string) get_option( wpb_sdk_optin_initiator_backfill_option_key( $slug ), '' );
		$stored_initiator = get_option( wpb_sdk_optin_initiator_option_key( $slug ), null );
		$cohort           = wpb_sdk_get_telemetry_contact_cohort( $slug );

		// Fresh opt-in after 3.3.1 (Allow/Skip): never legacy-backfill; clear dead initiator.
		if ( 'initiator_fresh' === $cohort ) {
			$stored_int = (int) $stored_initiator;
			if ( $stored_int > 0 && ! wpb_sdk_optin_initiator_is_valid( $stored_int ) ) {
				delete_option( wpb_sdk_optin_initiator_option_key( $slug ) );
			}
			if ( $stored_version < $target_version ) {
				update_option( wpb_sdk_optin_initiator_backfill_version_option_key( $slug ), $target_version, false );
			}
			if ( ! $legacy_flag ) {
				update_option( wpb_sdk_optin_initiator_backfill_option_key( $slug ), '1', false );
			}
			return;
		}

		// Legacy alphabetical backfill already completed at v3+.
		if ( $stored_version >= $target_version && $legacy_flag && 'pre_32_login' === $cohort ) {
			return;
		}

		// Re-pin when an older backfill used lowest-ID (3.2.9–3.2.11 tiered rules).
		if ( $legacy_flag && $stored_version < $target_version ) {
			delete_option( wpb_sdk_optin_initiator_option_key( $slug ) );
		} elseif ( $legacy_flag && 'pre_32_login' === $cohort ) {
			return;
		}

		if ( ! wpb_sdk_legacy_optin_consent_is_active( $slug ) ) {
			return;
		}

		$contact_id = wpb_sdk_resolve_legacy_pre_32_admin_user_id( $slug );
		if ( $contact_id > 0 ) {
			wpb_sdk_set_optin_initiator( $slug, $contact_id );
		}

		wpb_sdk_set_telemetry_contact_fallback_mode( $slug, 'legacy_login_admin' );
		wpb_sdk_set_telemetry_contact_cohort( $slug, 'pre_32_login' );
		update_option( wpb_sdk_optin_initiator_backfill_option_key( $slug ), '1', false );
		update_option( wpb_sdk_optin_initiator_backfill_version_option_key( $slug ), $target_version, false );
	}
}

if ( ! function_exists( 'wpb_sdk_enqueue_optin_initiator_backfill' ) ) {
	/**
	 * Run legacy initiator backfill when WordPress user APIs are safe.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_enqueue_optin_initiator_backfill( $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return;
		}

		if ( wpb_sdk_bootstrap_ready_for_user_api() ) {
			wpb_sdk_maybe_backfill_optin_initiator_for_legacy_site( $slug );
			return;
		}

		static $queued = array();
		if ( ! empty( $queued[ $slug ] ) ) {
			return;
		}
		$queued[ $slug ] = true;

		add_action(
			'plugins_loaded',
			static function () use ( $slug ) {
				wpb_sdk_maybe_backfill_optin_initiator_for_legacy_site( $slug );
			},
			20
		);
	}
}

if ( ! function_exists( 'wpb_sdk_find_admin_with_verification_token' ) ) {
	/**
	 * Admin user who currently holds a pending verification token.
	 *
	 * @param string $slug Product slug.
	 * @return int
	 */
	function wpb_sdk_find_admin_with_verification_token( $slug ) {
		$module = wpb_sdk_get_registered_module( $slug );
		if ( empty( $module['optin_user_meta']['token'] ) ) {
			return 0;
		}

		$token_meta = (string) $module['optin_user_meta']['token'];
		$admins     = wpb_sdk_get_administrator_users(
			array(
				'fields' => 'ID',
			)
		);

		foreach ( $admins as $admin_id ) {
			$admin_id = (int) $admin_id;
			$token    = get_user_meta( $admin_id, $token_meta, true );
			if ( is_string( $token ) && '' !== $token ) {
				return $admin_id;
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_optin_verification_user_id' ) ) {
	/**
	 * Admin who should receive verification email and appear in the admin notice.
	 *
	 * @param string $slug Product slug.
	 * @return int
	 */
	function wpb_sdk_resolve_optin_verification_user_id( $slug ) {
		return wpb_sdk_resolve_telemetry_contact_user_id( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_get_optin_verification_user_email' ) ) {
	/**
	 * Email address of the admin tied to pending opt-in verification.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_get_optin_verification_user_email( $slug ) {
		$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		if ( $user_id < 1 ) {
			return '';
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! $user instanceof WP_User || '' === $user->user_email ) {
			return '';
		}

		return sanitize_email( $user->user_email );
	}
}

if ( ! function_exists( 'wpb_sdk_is_user_verified' ) ) {
	/**
	 * Trusted email verification check (_slug_email_verified JSON only).
	 *
	 * Legacy _slug_optin_verified is ignored; upgrades must verify again into the new meta key.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_is_user_verified( $slug, $user_id = 0 ) {
		$module = wpb_sdk_get_registered_module( $slug );
		$key    = wpb_sdk_email_verified_meta_key_from_module( $module );
		if ( '' === $key ) {
			return true;
		}

		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		}
		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}
		if ( $user_id < 1 ) {
			return false;
		}

		$verified_raw = get_user_meta( $user_id, $key, true );

		return wpb_sdk_is_email_verified_meta_value( $verified_raw );
	}
}

if ( ! function_exists( 'wpb_sdk_verification_email_was_issued' ) ) {
	/**
	 * Whether a verification email was already sent and is still pending confirmation.
	 *
	 * Trusted _slug_email_verified meta is the only proof of completion. Until then,
	 * show "Send verification email" unless a live token or explicit dispatch exists.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional verification admin user ID.
	 * @return bool
	 */
	function wpb_sdk_verification_email_was_issued( $slug, $user_id = 0 ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return false;
		}

		$user_id = (int) $user_id;
		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		}
		if ( $user_id < 1 ) {
			return false;
		}

		if ( function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $user_id ) ) {
			return false;
		}

		// Resend / dispatch just fired → "Thanks, check your inbox".
		if ( get_transient( 'wpb_sdk_' . $slug . '_verify_resend_' . $user_id ) ) {
			return true;
		}

		if ( get_transient( 'wpb_sdk_' . $slug . '_verify_email_dispatched' ) ) {
			return true;
		}

		$module    = wpb_sdk_get_registered_module( $slug );
		$token_key = ! empty( $module['optin_user_meta']['token'] )
			? (string) $module['optin_user_meta']['token']
			: '';

		if ( '' !== $token_key ) {
			$token = get_user_meta( $user_id, $token_key, true );
			if ( is_string( $token ) && '' !== $token ) {
				return ! wpb_sdk_is_verification_token_expired( $slug, $user_id );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'wpb_sdk_pin_initiator_on_email_verified' ) ) {
	/**
	 * After verify link, the confirming admin becomes the stable telemetry contact.
	 *
	 * @param WP_User              $user   User who verified.
	 * @param array<string, mixed> $module Product module config.
	 * @return void
	 */
	function wpb_sdk_pin_initiator_on_email_verified( $user, $module ) {
		if ( ! ( $user instanceof WP_User ) || empty( $module['slug'] ) ) {
			return;
		}

		wpb_sdk_set_optin_initiator( (string) $module['slug'], (int) $user->ID );
	}
}
add_action( 'wpb_sdk_optin_verified', 'wpb_sdk_pin_initiator_on_email_verified', 10, 2 );

if ( ! function_exists( 'wpb_sdk_needs_verification_email_dispatch' ) ) {
	/**
	 * Opted in, not verified, and no active (non-expired) verification token exists.
	 *
	 * @param string $slug    Product slug.
	 * @param int    $user_id Optional WordPress user ID.
	 * @return bool
	 */
	function wpb_sdk_needs_verification_email_dispatch( $slug, $user_id = 0 ) {
		if ( function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $user_id ) ) {
			return false;
		}

		if ( ! function_exists( 'wpb_sdk_allows_ongoing_telemetry' ) || ! wpb_sdk_allows_ongoing_telemetry( $slug ) ) {
			return false;
		}

		$module = wpb_sdk_get_registered_module( $slug );
		if ( '' === wpb_sdk_email_verified_meta_key_from_module( $module ) || empty( $module['optin_user_meta']['token'] ) ) {
			return false;
		}

		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		}
		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}
		if (
			function_exists( 'wpb_sdk_has_pending_verification_token' )
			&& wpb_sdk_has_pending_verification_token( $slug, $user_id )
			&& function_exists( 'wpb_sdk_is_verification_token_expired' )
			&& ! wpb_sdk_is_verification_token_expired( $slug, $user_id )
		) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_dispatch_verification_email' ) ) {
	/**
	 * Trigger activation telemetry with a verification token so the API sends the email.
	 *
	 * @param string $slug            Product slug.
	 * @param int    $user_id         Optional WordPress user ID.
	 * @param bool   $force_new_token Issue a fresh token (resend flow).
	 * @return bool True when a dispatch was attempted.
	 */
	function wpb_sdk_dispatch_verification_email( $slug, $user_id = 0, $force_new_token = false ) {
		$slug = sanitize_key( (string) $slug );
		if ( '' === $slug ) {
			return false;
		}

		if ( function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $user_id ) ) {
			return false;
		}

		if ( ! function_exists( 'wpb_sdk_allows_ongoing_telemetry' ) || ! wpb_sdk_allows_ongoing_telemetry( $slug ) ) {
			return false;
		}

		$module = wpb_sdk_get_registered_module( $slug );
		if ( '' === wpb_sdk_email_verified_meta_key_from_module( $module ) || empty( $module['optin_user_meta']['token'] ) ) {
			return false;
		}

		if ( ! $force_new_token && ! wpb_sdk_needs_verification_email_dispatch( $slug, $user_id ) ) {
			return false;
		}

		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_verification_user_id( $slug );
		}
		if ( $user_id < 1 ) {
			$user_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
		}
		$rate_key = $force_new_token
			? 'wpb_sdk_' . $slug . '_verify_resend_' . $user_id
			: 'wpb_sdk_' . $slug . '_verify_email_dispatched';

		if ( get_transient( $rate_key ) ) {
			return false;
		}

		if ( $force_new_token && $user_id > 0 && ! empty( $module['optin_user_meta']['token'] ) ) {
			$token_meta = (string) $module['optin_user_meta']['token'];
			delete_user_meta( $user_id, $token_meta );
			$expires_meta = function_exists( 'wpb_sdk_verification_token_expires_meta_key' )
				? wpb_sdk_verification_token_expires_meta_key( $module )
				: $token_meta . '_expires';
			delete_user_meta( $user_id, $expires_meta );
			delete_option( 'wpb_sdk_' . $slug . '_fallback_verify_token' );
		}

		if ( ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return false;
		}

		$module_id = ! empty( $module['id'] ) ? (string) $module['id'] : '';
		$logger    = WPBRIGADE_Logger::instance( $module_id, $slug, true );
		if ( ! $logger ) {
			return false;
		}

		$logger->log_verification_email_request( $slug, $force_new_token );

		$ttl = $force_new_token ? 2 * MINUTE_IN_SECONDS : 12 * HOUR_IN_SECONDS;
		set_transient( $rate_key, '1', $ttl );
		set_transient( 'wpb_sdk_' . $slug . '_pending_verify_notice', '1', DAY_IN_SECONDS );

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_legacy_upgrade_optin_option_key' ) ) {
	/**
	 * Deprecated site option from SDK 3.2.0–3.2.2 (removed on uninstall / email verify).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_legacy_upgrade_optin_option_key( $slug ) {
		return 'wpb_sdk_' . sanitize_key( (string) $slug ) . '_legacy_upgrade_optin';
	}
}

if ( ! function_exists( 'wpb_sdk_may_send_telemetry_action' ) ) {
	/**
	 * Whether a telemetry action may be sent for this product.
	 *
	 * - No modal yet: nothing.
	 * - Yes: all actions (daily only after email verified).
	 * - Skip: one activate only (until initial log succeeds).
	 * - No: nothing.
	 *
	 * @param string $slug   Product slug.
	 * @param string $action activate|deactivate|uninstall|daily|''.
	 * @return bool
	 */
	function wpb_sdk_may_send_telemetry_action( $slug, $action = '' ) {
		if ( ! wpb_sdk_module_requires_optin_consent( $slug ) ) {
			return true;
		}

		if ( wpb_sdk_allows_ongoing_telemetry( $slug ) ) {
			return true;
		}

		if ( 'activate' === $action && wpb_sdk_user_skipped_optin( $slug ) ) {
			$sent_key = 'wpb_sdk_' . $slug . '_initial_log_sent';

			return '1' !== (string) get_option( $sent_key, '' );
		}

		return false;
	}
}

if ( ! function_exists( 'wpb_sdk_scan_active_plugin_basename' ) ) {
	/**
	 * Find an active plugin basename whose folder or main file matches the slug.
	 *
	 * @param string $slug Product slug (e.g. loginpress, wp-analytify).
	 * @return string Plugin basename relative to wp-content/plugins, or empty.
	 */
	function wpb_sdk_scan_active_plugin_basename( $slug ) {
		$slug = (string) $slug;
		if ( '' === $slug ) {
			return '';
		}

		$files = array();
		foreach ( (array) get_option( 'active_plugins', array() ) as $file ) {
			if ( is_string( $file ) && '' !== $file ) {
				$files[] = $file;
			}
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			foreach ( array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) as $file ) {
				if ( is_string( $file ) && '' !== $file ) {
					$files[] = $file;
				}
			}
		}

		$file_pattern = '#/' . preg_quote( $slug, '#' ) . '\\.php$#';
		foreach ( array_unique( $files ) as $file ) {
			$dir = dirname( $file );
			if ( $dir === $slug || basename( $dir ) === $slug ) {
				return $file;
			}
			if ( preg_match( $file_pattern, $file ) ) {
				return $file;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_get_plugin_path' ) ) {
	/**
	 * Resolve the main plugin file path for a product slug.
	 *
	 * Order: module plugin_file → filter → active plugin scan → {slug}/{slug}.php.
	 *
	 * @param string $slug Product slug.
	 * @return string Absolute path to main plugin file (may not exist yet).
	 */
	function wpb_sdk_get_plugin_path( $slug ) {
		$slug = (string) $slug;
		if ( '' === $slug || ! defined( 'WP_PLUGIN_DIR' ) ) {
			return '';
		}

		$module = wpb_sdk_get_registered_module( $slug );
		if ( ! empty( $module['plugin_file'] ) ) {
			$path = function_exists( 'wp_normalize_path' )
				? wp_normalize_path( (string) $module['plugin_file'] )
				: (string) $module['plugin_file'];
			if ( is_readable( $path ) ) {
				return $path;
			}
		}

		/**
		 * Filter the resolved main plugin file before scanning active plugins.
		 *
		 * @param string $path Empty string or absolute path.
		 * @param string $slug Product slug.
		 */
		$filtered = apply_filters( 'wpb_sdk_plugin_main_file', '', $slug );
		if ( is_string( $filtered ) && '' !== $filtered ) {
			$filtered = function_exists( 'wp_normalize_path' )
				? wp_normalize_path( $filtered )
				: $filtered;
			if ( is_readable( $filtered ) ) {
				return $filtered;
			}
		}

		$basename = wpb_sdk_scan_active_plugin_basename( $slug );
		if ( '' !== $basename ) {
			$path = WP_PLUGIN_DIR . '/' . ltrim( $basename, '/\\' );
			if ( function_exists( 'wp_normalize_path' ) ) {
				$path = wp_normalize_path( $path );
			}
			if ( is_readable( $path ) ) {
				return $path;
			}
		}

		$fallback = WP_PLUGIN_DIR . '/' . $slug . '/' . $slug . '.php';

		return function_exists( 'wp_normalize_path' )
			? wp_normalize_path( $fallback )
			: $fallback;
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_plugin_basename' ) ) {
	/**
	 * Active plugin basename for plugins.php (deactivate link / nonce).
	 *
	 * @param string $slug Product slug.
	 * @return string Plugin basename or empty string.
	 */
	function wpb_sdk_resolve_plugin_basename( $slug ) {
		$path = wpb_sdk_get_plugin_path( $slug );
		if ( is_readable( $path ) ) {
			return plugin_basename( $path );
		}

		$scanned = wpb_sdk_scan_active_plugin_basename( $slug );

		return is_string( $scanned ) ? $scanned : '';
	}
}

if ( ! function_exists( 'wpb_sdk_get_plugin_details' ) ) {
	/**
	 * Get plugin header data by slug.
	 *
	 * @param string $slug Plugin slug.
	 * @return array<string, string>|null
	 */
	function wpb_sdk_get_plugin_details( $slug ) {
		$plugin_path = wpb_sdk_get_plugin_path( $slug );
		if ( ! is_readable( $plugin_path ) ) {
			return null;
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugin_data( $plugin_path );
	}
}

if ( ! function_exists( 'wpb_sdk_product_name_from_slug' ) ) {
	/**
	 * Human-readable product name for admin UI and telemetry.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	function wpb_sdk_product_name_from_slug( $slug ) {
		$module = wpb_sdk_get_registered_module( $slug );
		$optin  = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();

		if ( ! empty( $optin['product_name'] ) ) {
			return (string) $optin['product_name'];
		}

		$details = wpb_sdk_get_plugin_details( $slug );
		if ( ! empty( $details['Name'] ) ) {
			return (string) $details['Name'];
		}

		return (string) $slug;
	}
}

if ( ! function_exists( 'wpb_get_plugin_details' ) ) {
	/**
	 * Legacy wrapper.
	 *
	 * @param string $slug Plugin slug.
	 * @return array<string, string>|null
	 */
	function wpb_get_plugin_details( $slug ) {
		return wpb_sdk_get_plugin_details( $slug );
	}
}

if ( ! function_exists( 'wpb_get_plugin_path' ) ) {
	/**
	 * Legacy wrapper.
	 *
	 * @param string $slug Plugin slug.
	 * @return string
	 */
	function wpb_get_plugin_path( $slug ) {
		return wpb_sdk_get_plugin_path( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_dev_view_resolve_product' ) ) {
	/**
	 * Resolve slug and module id for SDK dev views (account, debug).
	 *
	 * @return array{slug: string, module_id: string}
	 */
	function wpb_sdk_dev_view_resolve_product() {
		$slug      = '';
		$module_id = '1';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Dev-only slug picker; views require manage_options.
		if ( isset( $_GET['wpb_sdk_slug'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$requested = sanitize_key( wp_unslash( (string) $_GET['wpb_sdk_slug'] ) );
			if (
				'' !== $requested
				&& function_exists( 'wpb_sdk_get_registered_module' )
				&& ! empty( wpb_sdk_get_registered_module( $requested ) )
			) {
				$slug = $requested;
			}
		}

		if ( '' === $slug && ! empty( $GLOBALS['wpb_sdk_registry']['modules'] ) && is_array( $GLOBALS['wpb_sdk_registry']['modules'] ) ) {
			$registry_slugs = array_keys( $GLOBALS['wpb_sdk_registry']['modules'] );
			if ( ! empty( $registry_slugs[0] ) ) {
				$slug = (string) $registry_slugs[0];
			}
		}

		if (
			'' === $slug
			&& ! empty( $GLOBALS['wpb_sdk_registry']['initialized_modules'] )
			&& is_array( $GLOBALS['wpb_sdk_registry']['initialized_modules'] )
		) {
			$registry_slugs = array_keys( array_filter( $GLOBALS['wpb_sdk_registry']['initialized_modules'] ) );
			if ( ! empty( $registry_slugs[0] ) ) {
				$slug = (string) $registry_slugs[0];
			}
		}

		if ( '' !== $slug && function_exists( 'wpb_sdk_get_registered_module' ) ) {
			$module = wpb_sdk_get_registered_module( $slug );
			if ( ! empty( $module['id'] ) ) {
				$module_id = (string) $module['id'];
			}
		}

		return array(
			'slug'      => $slug,
			'module_id' => $module_id,
		);
	}
}

if ( ! function_exists( 'wpb_sdk_slug_from_plugin_basename' ) ) {
	/**
	 * Derive product slug from a plugin basename (e.g. loginpress/loginpress.php).
	 *
	 * @param string $basename Plugin basename.
	 * @return string
	 */
	function wpb_sdk_slug_from_plugin_basename( $basename ) {
		$basename = sanitize_text_field( (string) $basename );
		if ( '' === $basename ) {
			return '';
		}
		$dir = dirname( $basename );
		if ( '.' !== $dir && '' !== $dir ) {
			return strtolower( $dir );
		}

		return strtolower( basename( $basename, '.php' ) );
	}
}

if ( ! function_exists( 'wpb_sdk_module_exists_for_slug' ) ) {
	/**
	 * Whether a product slug is known to the SDK.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	function wpb_sdk_module_exists_for_slug( $slug ) {
		if ( '' === $slug ) {
			return false;
		}
		if ( function_exists( 'wpb_sdk_get_registered_module' ) ) {
			return ! empty( wpb_sdk_get_registered_module( $slug ) );
		}

		return false;
	}
}

if ( ! function_exists( 'wpb_sdk_persist_lifecycle_basename_map' ) ) {
	/**
	 * Persist plugin basename → slug for lifecycle hooks (uninstall runs with minimal bootstrap).
	 *
	 * @param string $slug        Product slug.
	 * @param string $plugin_path Absolute main plugin file path.
	 * @return void
	 */
	function wpb_sdk_persist_lifecycle_basename_map( $slug, $plugin_path ) {
		$basename = plugin_basename( $plugin_path );
		$map      = get_option( 'wpb_sdk_lifecycle_basenames', array() );
		if ( ! is_array( $map ) ) {
			$map = array();
		}
		$map[ $basename ] = $slug;
		update_option( 'wpb_sdk_lifecycle_basenames', $map, false );
	}
}

if ( ! function_exists( 'wpb_sdk_remove_lifecycle_basename_map_entry' ) ) {
	/**
	 * Remove basename map entry after uninstall cleanup.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	function wpb_sdk_remove_lifecycle_basename_map_entry( $slug ) {
		$map = get_option( 'wpb_sdk_lifecycle_basenames', array() );
		if ( ! is_array( $map ) ) {
			return;
		}
		foreach ( $map as $basename => $mapped_slug ) {
			if ( (string) $mapped_slug === (string) $slug ) {
				unset( $map[ $basename ] );
			}
		}
		update_option( 'wpb_sdk_lifecycle_basenames', $map, false );
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_product_slug_for_lifecycle' ) ) {
	/**
	 * Resolve product slug during activate / deactivate / uninstall.
	 *
	 * @return string
	 */
	function wpb_sdk_resolve_product_slug_for_lifecycle() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lifecycle admin context.
		$request_basename = isset( $_REQUEST['plugin'] ) && is_string( $_REQUEST['plugin'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) )
			: '';

		if ( '' === $request_basename && defined( 'WP_UNINSTALL_PLUGIN' ) && is_string( WP_UNINSTALL_PLUGIN ) ) {
			$request_basename = sanitize_text_field( WP_UNINSTALL_PLUGIN );
		}

		if ( '' === $request_basename ) {
			return '';
		}

		$map = get_option( 'wpb_sdk_lifecycle_basenames', array() );
		if ( is_array( $map ) && ! empty( $map[ $request_basename ] ) ) {
			return sanitize_key( (string) $map[ $request_basename ] );
		}

		$slug = wpb_sdk_slug_from_plugin_basename( $request_basename );
		if ( wpb_sdk_module_exists_for_slug( $slug ) ) {
			return $slug;
		}

		if ( function_exists( 'wpb_sdk_restore_module_for_lifecycle' ) ) {
			wpb_sdk_restore_module_for_lifecycle( $slug );
			if ( wpb_sdk_module_exists_for_slug( $slug ) ) {
				return $slug;
			}
		}

		if ( ! empty( $GLOBALS['wpb_sdk_registry']['modules'] ) && is_array( $GLOBALS['wpb_sdk_registry']['modules'] ) ) {
			foreach ( $GLOBALS['wpb_sdk_registry']['modules'] as $reg_slug => $module ) {
				if ( ! is_array( $module ) || empty( $module['plugin_file'] ) ) {
					continue;
				}
				if ( plugin_basename( (string) $module['plugin_file'] ) === $request_basename ) {
					return (string) $reg_slug;
				}
			}
		}

		return wpb_sdk_slug_from_plugin_basename( $request_basename );
	}
}

if ( ! function_exists( 'wpb_sdk_dispatch_product_lifecycle' ) ) {
	/**
	 * Shared bootstrap for activate / deactivate / uninstall global callbacks.
	 *
	 * @return string Product slug or empty string.
	 */
	function wpb_sdk_dispatch_product_lifecycle() {
		if ( function_exists( 'wpb_sdk_ensure_runtime_loaded' ) ) {
			wpb_sdk_ensure_runtime_loaded();
		}

		$slug = wpb_sdk_resolve_product_slug_for_lifecycle();
		if ( '' !== $slug && function_exists( 'wpb_sdk_restore_module_for_lifecycle' ) ) {
			wpb_sdk_restore_module_for_lifecycle( $slug );
		}

		return $slug;
	}
}

if ( ! function_exists( 'wpb_sdk_run_product_activation' ) ) {
	/**
	 * Global activation hook callback (PHP 8+ safe; stored by register_activation_hook).
	 *
	 * @param bool $network_wide Whether the plugin is network-activated.
	 * @return void
	 */
	function wpb_sdk_run_product_activation( $network_wide = false ) {
		$slug = wpb_sdk_dispatch_product_lifecycle();
		if ( '' === $slug || ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return;
		}
		if ( method_exists( 'WPBRIGADE_Logger', 'telemetry_handle_activation' ) ) {
			WPBRIGADE_Logger::telemetry_handle_activation( $network_wide, $slug );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_run_product_deactivation' ) ) {
	/**
	 * Global deactivation hook callback (PHP 8+ safe; stored by register_deactivation_hook).
	 *
	 * @return void
	 */
	function wpb_sdk_run_product_deactivation() {
		$slug = wpb_sdk_dispatch_product_lifecycle();
		if ( '' === $slug || ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return;
		}
		if ( method_exists( 'WPBRIGADE_Logger', 'telemetry_handle_deactivation' ) ) {
			WPBRIGADE_Logger::telemetry_handle_deactivation( false, $slug );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_invoke_product_uninstall' ) ) {
	/**
	 * Uninstall handler after runtime is loaded (called from start.php stub).
	 *
	 * @return void
	 */
	function wpb_sdk_invoke_product_uninstall() {
		$slug = wpb_sdk_dispatch_product_lifecycle();
		if ( ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return;
		}
		if ( '' !== $slug && method_exists( 'WPBRIGADE_Logger', 'log_uninstallation_for_slug' ) ) {
			WPBRIGADE_Logger::log_uninstallation_for_slug( $slug );
			return;
		}
		if ( method_exists( 'WPBRIGADE_Logger', 'log_uninstallation' ) ) {
			WPBRIGADE_Logger::log_uninstallation();
		}
	}
}

if ( ! function_exists( 'wpb_sdk_migrate_legacy_lifecycle_callbacks' ) ) {
	/**
	 * Replace WPBRIGADE_Logger::__callStatic uninstall rows with a real PHP 8+ callback.
	 *
	 * @return void
	 */
	function wpb_sdk_migrate_legacy_lifecycle_callbacks() {
		$uninstall_plugins = get_option( 'uninstall_plugins', array() );
		if ( ! is_array( $uninstall_plugins ) || empty( $uninstall_plugins ) ) {
			return;
		}

		$changed = false;
		foreach ( $uninstall_plugins as $basename => $callback ) {
			if ( 'wpb_sdk_run_product_uninstall' === $callback ) {
				continue;
			}
			if ( ! is_array( $callback ) || empty( $callback[0] ) || 'WPBRIGADE_Logger' !== $callback[0] ) {
				continue;
			}
			$uninstall_plugins[ $basename ] = 'wpb_sdk_run_product_uninstall';
			$changed                        = true;
		}

		if ( $changed ) {
			update_option( 'uninstall_plugins', $uninstall_plugins );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_dev_view_load_logs_data' ) ) {
	/**
	 * Build telemetry payload for SDK dev views without calling instance() with empty ids.
	 *
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	function wpb_sdk_dev_view_load_logs_data( $slug ) {
		if ( ! class_exists( 'WPBRIGADE_Logger' ) || '' === $slug ) {
			return array();
		}

		return WPBRIGADE_Logger::get_logs_data( $slug );
	}
}

if ( ! function_exists( 'wpb_sdk_dev_view_default_logs_data' ) ) {
	/**
	 * Empty payload shape for dev views when no module is bootstrapped.
	 *
	 * @return array<string, mixed>
	 */
	function wpb_sdk_dev_view_default_logs_data() {
		return array(
			'user_info'      => array(
				'user_nickname' => '',
				'user_email'    => '',
			),
			'product_info'   => array(
				'name'    => '',
				'version' => '',
			),
			'authentication' => array(
				'public_key' => '',
			),
			'client_info'    => array(
				'ip_address' => '',
				'browser'    => '',
				'os'         => '',
				'device'     => '',
			),
		);
	}
}

if ( ! function_exists( 'wpb_sdk_custom_admin_menu' ) ) {
	/**
	 * Register SDK debug menu in dev mode.
	 *
	 * @return void
	 */
	function wpb_sdk_custom_admin_menu() {
		if ( ! defined( 'WPBRIGADE_SDK__DEV_MODE' ) || true !== WPBRIGADE_SDK__DEV_MODE ) {
			return;
		}

		$version = defined( 'WP_WPBRIGADE_SDK_VERSION' ) ? WP_WPBRIGADE_SDK_VERSION : '';

		add_menu_page(
			__( 'WPB SDK Debug', 'wpbrigade-sdk' ),
			$version ? 'WPB-SDK Debug [' . $version . ']' : 'WPB-SDK Debug',
			'manage_options',
			'wpb-debug-mode',
			'wpb_sdk_custom_page_content'
		);
	}
}

if ( ! function_exists( 'wpb_sdk_custom_page_content' ) ) {
	/**
	 * Render SDK debug page.
	 *
	 * @return void
	 */
	function wpb_sdk_custom_page_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to access this page.', 'wpbrigade-sdk' ),
				'',
				array( 'response' => 403 )
			);
		}

		if ( defined( 'WPBRIGADE_SDK__DEV_MODE' ) && true === WPBRIGADE_SDK__DEV_MODE ) {
			$debug_view = __DIR__ . '/../views/wpb-debug.php';
			if ( is_readable( $debug_view ) ) {
				include_once $debug_view;
			}
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WPB SDK Debug', 'wpbrigade-sdk' ); ?></h1>
		</div>
		<?php
	}
}

if ( ! function_exists( 'wpb_sdk_custom_account_menu' ) ) {
	/**
	 * Register temporary SDK account menu in dev mode.
	 *
	 * @return void
	 */
	function wpb_sdk_custom_account_menu() {
		if ( defined( 'WPBRIGADE_SDK__DEV_MODE' ) && true === WPBRIGADE_SDK__DEV_MODE ) {
			add_menu_page(
				__( 'WPB SDK Account', 'wpbrigade-sdk' ),
				__( 'account', 'wpbrigade-sdk' ),
				'manage_options',
				'account',
				'wpb_sdk_account_page_content'
			);
		}

		add_action( 'admin_enqueue_scripts', 'wpb_sdk_delayed_remove_menu_page' );
	}
}

if ( ! function_exists( 'wpb_sdk_account_page_content' ) ) {
	/**
	 * Render SDK account page.
	 *
	 * @return void
	 */
	function wpb_sdk_account_page_content() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to access this page.', 'wpbrigade-sdk' ),
				'',
				array( 'response' => 403 )
			);
		}

		if ( defined( 'WPBRIGADE_SDK__DEV_MODE' ) && true === WPBRIGADE_SDK__DEV_MODE ) {
			$account_view = __DIR__ . '/../views/account.php';
			if ( is_readable( $account_view ) ) {
				include_once $account_view;
			}
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WPB SDK Account', 'wpbrigade-sdk' ); ?></h1>
		</div>
		<?php
	}
}

if ( ! function_exists( 'wpb_sdk_delayed_remove_menu_page' ) ) {
	/**
	 * Remove temporary SDK account page.
	 *
	 * @return void
	 */
	function wpb_sdk_delayed_remove_menu_page() {
		remove_menu_page( 'account' );
	}
}

if ( ! function_exists( 'wpb_sdk_get_client_ip' ) ) {
	/**
	 * Resolve the client IP from common proxy headers.
	 *
	 * @return string Empty when unavailable (e.g. WP-Cron).
	 */
	function wpb_sdk_get_client_ip() {
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
			if ( empty( $_SERVER[ $ip_field ] ) ) {
				continue;
			}

			$raw = sanitize_text_field( wp_unslash( (string) $_SERVER[ $ip_field ] ) );
			if ( '' === $raw ) {
				continue;
			}

			if ( false !== strpos( $raw, ',' ) ) {
				$parts = explode( ',', $raw );
				$raw   = trim( (string) $parts[0] );
			}

			if ( '' !== $raw ) {
				return $raw;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_parse_user_agent' ) ) {
	/**
	 * Parse browser, OS, and device type from a User-Agent string.
	 *
	 * @param string $user_agent Optional UA; defaults to current request.
	 * @return array{browser: string, os: string, device: string}
	 */
	function wpb_sdk_parse_user_agent( $user_agent = '' ) {
		if ( '' === $user_agent && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$user_agent = sanitize_text_field( wp_unslash( (string) $_SERVER['HTTP_USER_AGENT'] ) );
		}

		$user_agent = (string) $user_agent;
		$browser    = 'Unknown';
		$os         = 'Unknown';
		$device     = 'Desktop';

		if ( '' === $user_agent ) {
			return array(
				'browser' => $browser,
				'os'      => $os,
				'device'  => $device,
			);
		}

		if ( preg_match( '/iPad|Tablet|Android(?!.*Mobile)/i', $user_agent ) ) {
			$device = 'Tablet';
		} elseif ( preg_match( '/Mobile|Android.*Mobile|iPhone|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i', $user_agent ) ) {
			$device = 'Mobile';
		}

		if ( preg_match( '/Windows NT 10/i', $user_agent ) ) {
			$os = 'Windows';
		} elseif ( preg_match( '/Windows NT 6\.3/i', $user_agent ) ) {
			$os = 'Windows 8.1';
		} elseif ( preg_match( '/Windows NT 6\.2/i', $user_agent ) ) {
			$os = 'Windows 8';
		} elseif ( preg_match( '/Windows NT 6\.1/i', $user_agent ) ) {
			$os = 'Windows 7';
		} elseif ( preg_match( '/Windows/i', $user_agent ) ) {
			$os = 'Windows';
		} elseif ( preg_match( '/Mac OS X ([0-9_\.]+)/i', $user_agent, $matches ) ) {
			$os = 'macOS ' . str_replace( '_', '.', $matches[1] );
		} elseif ( preg_match( '/Mac OS X/i', $user_agent ) ) {
			$os = 'macOS';
		} elseif ( preg_match( '/Android ([0-9\.]+)/i', $user_agent, $matches ) ) {
			$os = 'Android ' . $matches[1];
		} elseif ( preg_match( '/Android/i', $user_agent ) ) {
			$os = 'Android';
		} elseif ( preg_match( '/iPhone OS ([0-9_]+)/i', $user_agent, $matches ) ) {
			$os = 'iOS ' . str_replace( '_', '.', $matches[1] );
		} elseif ( preg_match( '/CPU (?:iPhone )?OS ([0-9_]+)/i', $user_agent, $matches ) ) {
			$os = 'iOS ' . str_replace( '_', '.', $matches[1] );
		} elseif ( preg_match( '/Linux/i', $user_agent ) ) {
			$os = 'Linux';
		} elseif ( preg_match( '/CrOS/i', $user_agent ) ) {
			$os = 'Chrome OS';
		}

		if ( preg_match( '/Edg\/([0-9\.]+)/i', $user_agent, $matches ) ) {
			$browser = 'Edge ' . $matches[1];
		} elseif ( preg_match( '/OPR\/([0-9\.]+)/i', $user_agent, $matches ) ) {
			$browser = 'Opera ' . $matches[1];
		} elseif ( preg_match( '/Chrome\/([0-9\.]+)/i', $user_agent, $matches ) && ! preg_match( '/Edg\//i', $user_agent ) ) {
			$browser = 'Chrome ' . $matches[1];
		} elseif ( preg_match( '/Firefox\/([0-9\.]+)/i', $user_agent, $matches ) ) {
			$browser = 'Firefox ' . $matches[1];
		} elseif ( preg_match( '/Version\/([0-9\.]+).*Safari/i', $user_agent, $matches ) ) {
			$browser = 'Safari ' . $matches[1];
		} elseif ( preg_match( '/MSIE ([0-9\.]+)/i', $user_agent, $matches ) ) {
			$browser = 'Internet Explorer ' . $matches[1];
		} elseif ( preg_match( '/Trident\/.*rv:([0-9\.]+)/i', $user_agent, $matches ) ) {
			$browser = 'Internet Explorer ' . $matches[1];
		}

		return array(
			'browser' => $browser,
			'os'      => $os,
			'device'  => $device,
		);
	}
}

if ( ! function_exists( 'wpb_sdk_get_client_info' ) ) {
	/**
	 * Client context for telemetry payloads (IP, browser, OS, device).
	 *
	 * @return array{ip_address: string, browser: string, os: string, device: string}
	 */
	function wpb_sdk_get_client_info() {
		$parsed = wpb_sdk_parse_user_agent();

		return array(
			'ip_address' => wpb_sdk_get_client_ip(),
			'browser'    => $parsed['browser'],
			'os'         => $parsed['os'],
			'device'     => $parsed['device'],
		);
	}
}

add_action( 'plugins_loaded', 'wpb_sdk_migrate_legacy_lifecycle_callbacks', 1 );

add_action( 'wp_wpb_sdk_after_uninstall', 'wpb_sdk_cleanup_data_on_uninstall', 5 );

add_action( 'delete_user', 'wpb_sdk_handle_deleted_user_telemetry_contact', 10, 2 );
add_action( 'wpb_sdk_bootstrap_module', 'wpb_sdk_restore_module_for_lifecycle', 5 );

add_action( 'admin_menu', 'wpb_sdk_custom_admin_menu', 999 );
add_action( 'admin_menu', 'wpb_sdk_custom_account_menu' );
