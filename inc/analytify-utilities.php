<?php
/**
 * Utilities File for Analytify Plugin
 *
 * This file contains all utility and helper methods including
 * exception handling, cache management, and other helper functions.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility Methods for Analytify_General Class
 */
trait Analytify_General_Utilities {

	/**
	 * Echo value to be returned in ajax response.
	 *
	 * @param boolean $response The response value to echo.
	 *
	 * @return void
	 */
	public function end_ajax( $response = false ) {

		$response = apply_filters( 'wpanalytify_before_response', $response );

		// Detect if response is JSON and don't escape it (JSON must be output as-is).
		if ( false !== $response && is_string( $response ) ) {
			// Check if it's JSON by trying to decode it.
			$decoded = json_decode( $response, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				// It's valid JSON, set proper headers and output without HTML escaping.
				header( 'Content-Type: application/json; charset=utf-8' );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON output should not be escaped
				echo $response;
				exit;
			}
		}

		echo ( false === $response ) ? '' : esc_html( $response );
		exit;
	}

	/**
	 * Check ajax referer facade.
	 *
	 * @param string $action The action to check.
	 *
	 * @return void
	 */
	public function check_ajax_referer( $action ) {

		$result = check_ajax_referer( $action, 'nonce', false );

		if ( false === $result ) {
			$return = array(
				'wpanalytify_error' => 1,
				// translators: %s is the action name.
				'body'              => sprintf( __( 'Invalid nonce for: %s', 'wp-analytify' ), $action ),
			);
			$this->end_ajax( wp_json_encode( $return ) );
		}

		$cap = ( is_multisite() ) ? 'manage_network_options' : 'export';
		$cap = apply_filters( 'wpanalytify_ajax_cap', $cap );

		if ( ! current_user_can( $cap ) ) {
			$return = array(
				'wpanalytify_error' => 1,
				// translators: %s is the action name.
				'body'              => sprintf( __( 'Access denied for: %s', 'wp-analytify' ), $action ),
			);
			$this->end_ajax( wp_json_encode( $return ) );
		}
	}

	/**
	 * Returns the function name that called the function using this function.
	 *
	 * @return string
	 */
	public function get_caller_function() {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- debug_backtrace is used here to retrieve the actual calling function name, not for debugging purposes.
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 3 );
		$caller    = '';

		if ( isset( $backtrace[2]['function'] ) ) {
			$caller = $backtrace[2]['function'];
		}

		return $caller;
	}

	/**
	 * Set $this->state_data from $_POST, potentially un-slashed and sanitized.
	 *
	 * @param array  $key_rules An optional associative array of expected keys and their sanitization rule(s).
	 * @param string $context   The method that is specifying the sanitization rules. Defaults to calling method.
	 *
	 * @since 2.0
	 * @return array
	 */
	public function set_post_data( $key_rules = array(), $context = '' ) {

		if ( defined( 'DOING_WPANALYTIFY_TESTS' ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- This is a utility function for internal use
			$this->state_data = $_POST;
		} elseif ( is_null( $this->state_data ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- This is a utility function for internal use
			$this->state_data = WPANALYTIFY_Utils::safe_wp_unslash( $_POST );
		} else {
			return $this->state_data;
		}

		// From this point on we're handling data originating from $_POST, so original $key_rules apply.
		global $wpanalytify_key_rules;

		if ( empty( $key_rules ) && ! empty( $wpanalytify_key_rules ) ) {
			$key_rules = $wpanalytify_key_rules;
		}

		// Sanitize the new state data.
		if ( ! empty( $key_rules ) ) {
			$wpanalytify_key_rules = $key_rules;

			$context          = empty( $context ) ? $this->get_caller_function() : trim( $context );
			$this->state_data = WPANALYTIFY_Sanitize::sanitize_data( $this->state_data, $key_rules, $context );

			if ( false === $this->state_data ) {
				exit;
			}
		}

		return $this->state_data;
	}

	/**
	 * Create no records markup.
	 *
	 * @return void
	 */
	public function no_records() {
		?>

		<div class="analytify-stats-error-msg">
			<div class="wpb-error-box">
				<span class="blk">
					<span class="line"></span>
					<span class="dot"></span>
				</span>
				<span class="information-txt"><?php esc_html_e( 'No Activity During This Period.', 'wp-analytify' ); ?></span>
			</div>
		</div>

		<?php
	}

	/**
	 * Get Exception value.
	 *
	 * @since 2.1.22
	 */
	public function get_exception() {
		return $this->exception;
	}

	/**
	 * Set Exception value.
	 *
	 * @param mixed $exception The exception to set.
	 * @since 2.1.22
	 */
	public function set_exception( $exception ) {
		$this->exception = $exception;
	}

	/**
	 * Get ga4 Exception value.
	 *
	 * @since 5.0.0
	 */
	public function get_ga4_exception() {
		return $this->ga4_exception;
	}

	/**
	 * Set Exception value.
	 *
	 * @param mixed $exception The exception to set.
	 * @since 5.0.0
	 */
	public function set_ga4_exception( $exception ) {
		$this->ga4_exception = $exception;
	}

	/**
	 * Generate the Error box.
	 *
	 * @param string $message The error message to display.
	 * @since 2.1.23
	 */
	protected function show_error_box( $message ) {
		$error = '<div class="analytify-stats-error-msg">
			<div class="wpb-error-box">
				<span class="blk">
					<span class="line"></span>
					<span class="dot"></span>
				</span>
				<span class="information-txt">'
				. $message .
				'</span>
			</div>
		</div>';

		return $error;
	}

	/**
	 * If error, return cache result else return error.
	 *
	 * @param mixed $exception The exception to handle.
	 * @param mixed $cache_result The cached result to return if available.
	 * @since 2.1.23
	 */
	public function tackle_exception( $exception, $cache_result ) {

		if ( $cache_result ) {
			return $cache_result;
		}

		echo wp_kses_post( $this->show_error_box( $exception ) );
	}

	/**
	 * Set Cache time for Stats.
	 *
	 * @version 5.0.4
	 * @since 2.2.1
	 */
	public function set_cache_time() {
		$this->cache_timeout = $this->settings->get_option( 'delete_dashboard_cache', 'wp-analytify-dashboard', 'off' ) === 'on' ? apply_filters( 'analytify_stats_cache_time', 60 * 60 * 10 ) : apply_filters( 'analytify_stats_cache_time', 60 * 60 * 24 );
	}

	/**
	 * Get Cache time for Stats.
	 *
	 * @version 5.0.4
	 *
	 * @since 2.2.1
	 */
	public function get_cache_time() {
		return $this->cache_timeout;
	}

	/**
	 * Check the active/deactive state of addon/moudle.
	 *
	 * @param string $slug Slug of addon./moudle.
	 * @return string $addon_state: active or deactive
	 * @version 9.0.0
	 */
	public function analytify_module_state( $slug ) {

		$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
		$addon_state  = '';

		$pro_inner    = array(
			'detail-realtime',
			'detail-demographic',
			'search-terms',
			'page-speed',
			'search-console-report',
			'video-tracking',
		);
		$pro_addon    = array(
			'wp-analytify-woocommerce',
			'wp-analytify-goals',
			'wp-analytify-authors',
			'wp-analytify-learndash',
			'wp-analytify-edd',
			'wp-analytify-forms',
			'wp-analytify-campaigns',
			'wp-analytify-lifterlms',
			'wp-analytify-pmpro',
		);
		$pro_features = array(
			'custom-dimensions',
			'events-tracking',
		);

		if ( in_array( $slug, $pro_features, true ) ) {
			$analytify_modules = get_option( 'wp_analytify_modules' );

			if ( 'active' === $analytify_modules[ $slug ]['status'] ) {
				$addon_state = 'active';
			} else {
				$addon_state = 'deactive';
			}
		} elseif ( in_array( $slug, $pro_addon, true ) || in_array( $slug, $pro_inner, true ) ) {
			if ( in_array( $slug, $pro_inner, true ) ) {
				$slug = 'wp-analytify-pro';
			}

			if ( $wp_analytify->addon_is_active( $slug ) ) {
				$addon_state = 'active';
			} else {
				$addon_state = 'deactive';
			}
		}

		return $addon_state;
	}

	/**
	 * Check if external addon is active.
	 *
	 * @param string $slug Slug of addon.
	 *
	 * @return bool $addon_active
	 * @version 9.0.0
	 */
	public function addon_is_active( $slug ) {

		$addon_active = false;

		switch ( $slug ) {
			case 'wp-analytify':
				if ( class_exists( 'Analytify_General' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-goals':
				if ( class_exists( 'WP_Analytify_Goals' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-woocommerce':
				if ( class_exists( 'WP_Analytify_WooCommerce' ) || class_exists( 'WP_Analytify_WooCommerce_Addon' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-campaigns':
				if ( class_exists( 'ANALYTIFY_PRO_CAMPAIGNS' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-authors':
				if ( class_exists( 'Analytify_Authors' ) || class_exists( 'Analytify_Addon_Authors' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-edd':
				if ( class_exists( 'WP_Analytify_Edd' ) || class_exists( 'WP_Analytify_Edd_Addon' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-pmpro':
				if ( class_exists( 'WP_Analytify_Pmpro_Addon' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-forms':
				if ( class_exists( 'Analytify_Forms' ) || class_exists( 'Analytify_Addon_Forms' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-pro':
				if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-lifterlms':
				if ( class_exists( 'WP_Analytify_LifterLMS_Addon' ) ) {
					$addon_active = true;
				}
				break;

			case 'wp-analytify-learndash':
				if ( class_exists( 'WP_Analytify_LearnDash_Addon' ) || class_exists( 'WP_Analytify_LearnDash' ) ) {
					$addon_active = true;
				}
				break;

			default:
				$addon_active = false;
				break;
		}

		return $addon_active;
	}

	/**
	 * Handle caching of GA4 reports data and store it as transient
	 *
	 * @since 7.0.0
	 * @param string $cache_key The cache key to use.
	 * @param mixed  $data The data to cache.
	 * @param string $name The report name.
	 * @param bool   $should_cache Whether to cache the data.
	 * @return bool|void
	 */
	private function analytify_handle_report_cache( $cache_key, $data, $name, $should_cache = true ) {
		// Don't cache if caching is disabled or for specific reports.
		if ( ! $should_cache || 'show-worldmap-front' === $name ) {
			return false;
		}

		// Set the cache with the configured timeout.
		set_transient( $cache_key, $data, $this->get_cache_time() );
	}
}
