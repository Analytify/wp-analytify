<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure
/**
 * Analytify GDPR Compliance Class
 *
 * CookieYes (WebToffee), Cookie Notice (Hu-manity.co), optional Google Consent Mode v2,
 * consent-deferred gtag for any CMP (plugin or custom) via filters, and server-side CMP hooks.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

/**
 * Analytify GDPR Compliance Class
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class Class_Analytify_GDPR_Compliance {

	/**
	 * Whether CookieYes / CMP hooks were already registered (guards duplicate init).
	 *
	 * @var bool
	 */
	private static $hooks_registered = false;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {

		if ( self::$hooks_registered ) {
			return;
		}

		self::$hooks_registered = true;
		$this->hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	public function hooks() {

		// CookieYes | GDPR Cookie Consent & Compliance Notice (CCPA Ready) By WebToffee.
		add_filter( 'wt_cli_third_party_scripts', array( $this, 'cookie_law_info_blocking' ) );
		add_filter( 'wt_cli_plugin_integrations', array( $this, 'cookie_law_info_integration' ) );
		add_action( 'init', array( $this, 'cookie_law_info_add_settings' ), 9 );
	}

	/**
	 * Whether CookieYes (Cookie Law Info) is active.
	 *
	 * @return bool
	 */
	public static function is_cookie_yes_plugin_active() {

		return defined( 'CLI_VERSION' );
	}

	/**
	 * URL path fragments CookieYes uses to match Analytify / GA scripts.
	 *
	 * @since 9.1.0
	 * @version 9.1.0
	 * @return array<int, string>
	 */
	public static function get_cookie_yes_analytify_block_url_fragments() {

		$fragments = array(
			'www.google-analytics.com/analytics.js',
			'www.googletagmanager.com/gtag/js',
			'wp-analytify/assets/js/scrolldepth.js',
			'wp-analytify/assets/js/video_tracking.js',
			'wp-analytify/assets/js/miscellaneous-tracking.js',
			'wp-analytify-forms/assets/js/tracking.js',
			'wp-analytify-pro/assets/js/script.js',
		);

		if ( defined( 'WP_CONTENT_DIR' ) && defined( 'WP_ANALYTIFY_LOCAL_DIR' ) ) {
			$rel = str_replace( '\\', '/', WP_ANALYTIFY_LOCAL_DIR );
			$cd  = str_replace( '\\', '/', WP_CONTENT_DIR );
			if ( strpos( $rel, $cd ) === 0 ) {
				$rel = trim( substr( $rel, strlen( $cd ) ), '/' );
				if ( '' !== $rel ) {
					$fragments[] = $rel;
				}
			}
		} elseif ( defined( 'WP_ANALYTIFY_LOCAL_DIR' ) ) {
			$fragments[] = 'uploads/analytify';
		}

		if ( class_exists( 'Analytify_Host_Analytics' ) ) {
			$host      = new Analytify_Host_Analytics( 'gtag', false );
			$local_url = $host->local_analytics_file_url();
			if ( is_string( $local_url ) && '' !== $local_url ) {
				$path = wp_parse_url( $local_url, PHP_URL_PATH );
				if ( is_string( $path ) && '' !== $path ) {
					$fragments[] = trim( $path, '/' );
				}
			}
		}

		$fragments = array_values( array_unique( array_filter( array_map( 'strval', $fragments ) ) ) );

		/**
		 * Filter URL fragments for CookieYes script blocking.
		 *
		 * @param array<int, string> $fragments Path substrings.
		 */
		return apply_filters( 'analytify_cookie_yes_block_url_fragments', $fragments );
	}

	/**
	 * Whether gtag is deferred until consent (footer base64 + DOM events, or head listener when not using footer).
	 *
	 * Default is off. Any CMP (Complianz, Borlabs, custom, etc.) opts in with {@see 'analytify_consent_defer_gtag'}.
	 *
	 * @since 9.0.0
	 * @return bool
	 */
	public static function is_consent_deferred_gtag_enabled() {

		$default = false;

		/**
		 * Enable consent-deferred gtag (wp_footer payload + DOM events, or head listener when applicable).
		 *
		 * @param bool $enabled Whether deferred mode is on.
		 */
		return (bool) apply_filters( 'analytify_consent_defer_gtag', $default );
	}

	/**
	 * DOM events that trigger loading deferred gtag (when defer mode is on).
	 *
	 * Default: `analytifyConsentGranted` only. Add your CMP’s events via {@see 'analytify_consent_ready_dom_events'}
	 * or the legacy {@see 'analytify_ccv_consent_event_name'} filter (single string).
	 *
	 * @return array<int, string>
	 */
	public static function get_consent_ready_dom_events() {

		if ( ! self::is_consent_deferred_gtag_enabled() ) {
			return array();
		}

		$defaults = array( 'analytifyConsentGranted' );

		$legacy = apply_filters( 'analytify_ccv_consent_event_name', '' );
		if ( is_string( $legacy ) ) {
			$legacy = trim( $legacy );
			if ( '' !== $legacy ) {
				$defaults[] = $legacy;
			}
		}

		$defaults = array_values( array_unique( $defaults ) );

		/**
		 * Filter DOM events that trigger deferred gtag injection.
		 *
		 * @param array<int, string> $events Event type strings.
		 */
		$events = apply_filters( 'analytify_consent_ready_dom_events', $defaults );

		$out = array();
		foreach ( (array) $events as $event ) {
			if ( ! is_string( $event ) ) {
				continue;
			}
			$event = trim( $event );
			if ( '' === $event ) {
				continue;
			}
			// Safe for addEventListener type; allows common CMP custom names.
			if ( ! preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', $event ) ) {
				continue;
			}
			$out[] = $event;
		}

		$out = array_values( array_unique( $out ) );

		if ( empty( $out ) ) {
			return array( 'analytifyConsentGranted' );
		}

		return $out;
	}

	/**
	 * Global loader name on window for manual deferred gtag injection.
	 *
	 * @return string Empty to skip exposing window[name].
	 */
	public static function get_consent_deferred_js_loader_name() {

		if ( ! self::is_consent_deferred_gtag_enabled() ) {
			return '';
		}

		/**
		 * Whether to expose window[loaderName].
		 *
		 * @param bool $expose Default true when defer is on.
		 */
		if ( ! apply_filters( 'analytify_consent_deferred_expose_global_loader', true ) ) {
			return '';
		}

		$name = apply_filters( 'analytify_consent_deferred_js_loader_name', 'analytifyLoadGtagAfterConsent' );

		if ( ! is_string( $name ) ) {
			return 'analytifyLoadGtagAfterConsent';
		}

		$name = trim( $name );
		if ( '' === $name ) {
			return '';
		}

		if ( ! preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name ) ) {
			return 'analytifyLoadGtagAfterConsent';
		}

		return $name;
	}

	/**
	 * Whether to print gtag('consent', 'default', …) before gtag.js.
	 *
	 * @return bool
	 */
	public static function should_output_gtag_consent_defaults() {

		$explicit = apply_filters( 'analytify_output_gtag_consent_defaults', null );
		if ( null !== $explicit ) {
			return (bool) $explicit;
		}

		/**
		 * Whether Consent Mode defaults run when not explicitly overridden.
		 *
		 * @param bool $enabled Default: CookieYes active only.
		 */
		$enabled = apply_filters(
			'analytify_gtag_consent_defaults_enabled',
			self::is_cookie_yes_plugin_active()
		);

		return (bool) $enabled;
	}

	/**
	 * Default Consent Mode parameters, sanitized.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_gtag_consent_default_params() {

		$defaults = array(
			'ad_storage'              => 'denied',
			'ad_user_data'            => 'denied',
			'ad_personalization'      => 'denied',
			'analytics_storage'       => 'denied',
			'functionality_storage'   => 'denied',
			'personalization_storage' => 'denied',
			'security_storage'        => 'granted',
			'wait_for_update'         => 500,
		);

		/**
		 * Filter default Consent Mode params before sanitization.
		 *
		 * @param array<string, mixed> $defaults Parameters.
		 */
		$defaults = apply_filters( 'analytify_gtag_consent_default_params', $defaults );

		return self::sanitize_gtag_consent_params( $defaults );
	}

	/**
	 * Sanitize Consent Mode parameters for gtag output.
	 *
	 * @param array<string, mixed> $params Raw parameters.
	 * @return array<string, mixed>
	 */
	public static function sanitize_gtag_consent_params( $params ) {

		if ( ! is_array( $params ) ) {
			return array();
		}

		$storage_keys = array(
			'ad_storage',
			'ad_user_data',
			'ad_personalization',
			'analytics_storage',
			'functionality_storage',
			'personalization_storage',
			'security_storage',
		);

		$allowed_keys = array_merge(
			$storage_keys,
			array( 'wait_for_update', 'region' )
		);

		$out = array();

		foreach ( $params as $key => $value ) {
			if ( ! is_string( $key ) || ! in_array( $key, $allowed_keys, true ) ) {
				continue;
			}

			if ( 'wait_for_update' === $key ) {
				$wait = (int) $value;
				if ( $wait < 0 ) {
					$wait = 0;
				}
				if ( $wait > 10000 ) {
					$wait = 10000;
				}
				$out['wait_for_update'] = $wait;
				continue;
			}

			if ( 'region' === $key ) {
				if ( ! is_array( $value ) ) {
					continue;
				}
				$regions = array();
				foreach ( $value as $code ) {
					if ( ! is_string( $code ) ) {
						continue;
					}
					$code = strtoupper( substr( preg_replace( '/[^A-Za-z]/', '', $code ), 0, 2 ) );
					if ( strlen( $code ) === 2 ) {
						$regions[] = $code;
					}
				}
				if ( ! empty( $regions ) ) {
					$out['region'] = array_values( array_unique( $regions ) );
				}
				continue;
			}

			if ( in_array( $key, $storage_keys, true ) && is_string( $value )
				&& in_array( $value, array( 'granted', 'denied' ), true ) ) {
				$out[ $key ] = $value;
			}
		}

		return $out;
	}

	/**
	 * Add Analytify in CookieYes blocking scripts settings.
	 *
	 * @return void
	 */
	public function cookie_law_info_add_settings() {

		global $wt_cli_integration_list;

		$wt_cli_integration_list['wp-analytify'] = array(
			'identifier'  => 'WP_Analytify',
			'label'       => 'Analytify - Google Analytics Dashboard',
			'status'      => 'yes',
			'description' => 'Google Analytics Dashboard Plugin for WordPress by Analytify',
			'category'    => 'analytics',
			'type'        => 1,
		);
	}

	/**
	 * Block scripts based on cookie consent.
	 *
	 * @param array $tags The script tags to process.
	 * @return array
	 */
	public function cookie_law_info_blocking( $tags ): array {

		if ( ! is_array( $tags ) ) {
			$tags = array();
		}

		try {
			global $wpdb;

			$script_table = $wpdb->prefix . 'cli_scripts';
			$status       = false;

			$like_pattern = $script_table;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- CookieYes table probe
			$table_found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $like_pattern ) );

			if ( $table_found === $script_table ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table from $wpdb->prefix only; verified above.
				$script_data = $wpdb->get_results( "SELECT * FROM `{$script_table}`", ARRAY_A );

				if ( ! is_array( $script_data ) ) {
					$script_data = array();
				}

				foreach ( $script_data as $data ) {
					if ( ! is_array( $data ) ) {
						continue;
					}
					if ( isset( $data['cliscript_key'], $data['cliscript_status'] )
						&& 'wp-analytify' === $data['cliscript_key']
						&& ( 'yes' === $data['cliscript_status'] || '1' === $data['cliscript_status'] ) ) {
						$status = true;
					}
				}
			}

			if ( $status ) {
				$tags['wp-analytify'] = self::get_cookie_yes_analytify_block_url_fragments();
			}
		} catch ( \Throwable $th ) {
			unset( $th );
			return $tags;
		}

		return $tags;
	}

	/**
	 * Add Analytify integration settings.
	 *
	 * @param array $integration The integration settings.
	 * @return array
	 */
	public function cookie_law_info_integration( $integration ): array {

		if ( ! is_array( $integration ) ) {
			$integration = array();
		}

		$integration['wp-analytify'] = array(
			'identifier'  => 'WP_Analytify',
			'label'       => 'Analytify - Google Analytics Dashboard',
			'status'      => 'yes',
			'description' => 'Google Analytics Dashboard Plugin for WordPress by Analytify',
			'category'    => 'analytics',
			'type'        => 1,
		);

		return $integration;
	}

	/**
	 * Server-side suppression of Analytify tracking output.
	 *
	 * @return bool
	 */
	public static function is_gdpr_compliance_blocking() {

		/**
		 * Block head/footer Analytify tracking when PHP-level consent is denied.
		 *
		 * @param bool $block Whether to block.
		 */
		if ( true === apply_filters( 'analytify_consent_server_blocks_tracking', false ) ) {
			return true;
		}

		if ( self::is_cookie_yes_plugin_active() ) {
			return false;
		}

		if ( function_exists( 'cn_cookies_accepted' ) && ! cn_cookies_accepted() ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether gtag pageview/config should wait for CookieYes analytics consent.
	 *
	 * When Consent Mode defaults are denied and gtag('config') runs immediately, GA4
	 * may record cookieless pings only — Realtime active users often stay empty until
	 * analytics_storage is granted and config runs again after the CMP accept action.
	 *
	 * @since 9.1.0
	 * @return bool
	 */
	public static function should_defer_gtag_pageview_until_cmp_consent() {

		if ( ! self::is_cookie_yes_plugin_active() || ! self::should_output_gtag_consent_defaults() ) {
			return false;
		}

		if ( self::is_consent_deferred_gtag_enabled() ) {
			return false;
		}

		/**
		 * Defer gtag config/pageview until CookieYes grants analytics (default on).
		 *
		 * @param bool $defer Whether to defer pageview for CookieYes.
		 */
		return (bool) apply_filters( 'analytify_cookie_yes_defer_pageview_until_consent', true );
	}
}
