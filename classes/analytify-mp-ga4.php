<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Historical filename matches class context.

// phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed -- Function is part of the class API (global wrapper + class in one module file).
/**
 * This file contains the class that makes the gtag api calls.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'analytify_mp_ga4_build_request_body' ) ) {
	/**
	 * Build GA4 Measurement Protocol request body (client_id + events) after debug + filter hooks.
	 *
	 * @since 9.0.0
	 * @param string            $client_id Client ID for the MP payload.
	 * @param array<int, mixed> $events    Events list (GA4 name / params shape).
	 * @return array<string, mixed> Array with keys client_id and events.
	 */
	function analytify_mp_ga4_build_request_body( $client_id, $events ) {
		$events     = is_array( $events ) ? $events : array();
		$debug_mode = apply_filters( 'analytify_debug_mode', false );
		if ( $debug_mode ) {
			foreach ( $events as $index => $event ) {
				if ( ! is_array( $event ) ) {
					continue;
				}
				if ( ! isset( $event['params'] ) || ! is_array( $event['params'] ) ) {
					$events[ $index ]['params'] = array();
				}
				$events[ $index ]['params']['debug_mode'] = 1;
			}
		}
		$events = apply_filters( 'analytify_ga4_events_for_mp_api_call', $events );
		return array(
			'client_id' => (string) $client_id,
			'events'    => is_array( $events ) ? $events : array(),
		);
	}
}

if ( ! class_exists( 'Analytify_MP_GA4' ) ) {
	/**
	 * Class that makes the gtag api calls.
	 *
	 * Use for server-side calls.
	 *
	 * @package WP_Analytify
	 * @since 1.0.0
	 */
	class Analytify_MP_GA4 {

		/**
		 * The Google Analytics API URL.
		 */
		const GOOGLE_ANALYTICS_API_URL = 'https://www.google-analytics.com/mp/collect';

		/**
		 * The single instance of the class.
		 *
		 * @var self|null
		 */
		private static $instance = null;

		/**
		 * Client ID
		 *
		 * @var string
		 */
		private $client_id = null;

		/**
		 * Measurement ID
		 *
		 * @var string
		 */
		private $measurement_id = null;

		/**
		 * API secret
		 *
		 * @var string
		 */
		private $api_secret = null;

		/**
		 * Analytify global object.
		 *
		 * @var mixed
		 */
		private $wp_analytify = null;

		/**
		 * Returns the single instance of the class.
		 *
		 * @return self Class instance
		 */
		public static function get_instance(): self {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Class constructor.
		 *
		 * @version 8.1.2
		 */
		private function __construct() {
			$this->wp_analytify = $GLOBALS['WP_ANALYTIFY'];

			$raw_secret = $this->wp_analytify->settings->get_option( 'measurement_protocol_secret', 'wp-analytify-advanced', false );
			// GA4 API returns { secretValue, name, ... }; ensure we store/use only the string (back-compat: if saved as array, use secretValue).
			$this->api_secret     = is_array( $raw_secret ) && isset( $raw_secret['secretValue'] ) && is_string( $raw_secret['secretValue'] )
				? trim( sanitize_text_field( $raw_secret['secretValue'] ) )
				: ( is_string( $raw_secret ) ? trim( sanitize_text_field( $raw_secret ) ) : '' );
			$this->measurement_id = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
			$this->client_id      = $this->get_client_id();
		}

		/**
		 * Get client ID.
		 *
		 * @return string
		 */
		private function get_client_id(): string {

			$client_id = '';

			if ( isset( $_COOKIE['_ga'] ) ) {

				$client_id_raw = wp_unslash( $_COOKIE['_ga'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Cookie value is sanitized with wp_unslash
				$parts         = explode( '.', $client_id_raw );
				$client_id     = "{$parts[2]}.{$parts[3]}";

			} else {

				$client_id = ( 'on' === $this->wp_analytify->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced' ) ) ? $this->wp_analytify->settings->get_option( 'client_id', 'wp-analytify-advanced' ) : WP_ANALYTIFY_CLIENTID;

			}

			return $client_id;
		}

		/**
		 * Returns the Google Analytics API URL.
		 *
		 * @return string
		 */
		private function api_url() {
			$url = add_query_arg(
				array(
					'measurement_id' => $this->measurement_id,
					'api_secret'     => $this->api_secret,
				),
				self::GOOGLE_ANALYTICS_API_URL
			);
			return esc_url_raw( $url );
		}

		/**
		 * Send data to Google Analytics.
		 *
		 * @param array<string, mixed> $events Data to send.
		 * @return bool
		 */
		public function send_hit( $events ): bool {

			if ( '' === (string) $this->measurement_id || '' === (string) $this->api_secret ) {
				$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning(
						'Measurement Protocol send skipped: missing measurement ID or API secret.',
						array(
							'source'         => 'send_hit',
							'measurement_id' => $this->measurement_id,
							'has_api_secret' => '' !== (string) $this->api_secret,
						)
					);
				}
				return false;
			}

			$url = $this->api_url();

			$body      = analytify_mp_ga4_build_request_body( $this->client_id, $events );
			$json_body = wp_json_encode( $body );
			$response  = wp_remote_post(
				$url,
				array(
					'timeout' => 5,
					'body'    => $json_body ? $json_body : '',
				)
			);
			if ( is_wp_error( $response ) ) {
				$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;
				if ( $logger && method_exists( $logger, 'error' ) ) {
					$logger->error(
						'Failed to send hit via Measurement Protocol.',
						array(
							'source'         => 'send_hit',
							'error'          => $response->get_error_message(),
							'measurement_id' => $this->measurement_id,
						)
					);
				}
				return false;
			}

			return true;
		}
	}
}

/**
 * Uses the singleton pattern to call the api.
 *
 * @param array<string, mixed> $events Parameters to send to the API.
 * @return bool
 */
function analytify_mp_ga4( $events ): bool {
	$instance = Analytify_MP_GA4::get_instance();
	return $instance->send_hit( $events );
}

add_filter(
	'analytify_ga4_events_for_mp_api_call',
	static function ( $events ) {
		if ( ! isset( $GLOBALS['WP_ANALYTIFY'] ) || ! method_exists( $GLOBALS['WP_ANALYTIFY'], 'analytify_ensure_tracking_dimensions' ) ) {
			return $events;
		}

		$names = array();
		foreach ( (array) $events as $event ) {
			if ( empty( $event['params'] ) || ! is_array( $event['params'] ) ) {
				continue;
			}
			foreach ( array_keys( $event['params'] ) as $key ) {
				if ( is_string( $key ) && 0 === strpos( $key, 'wpa_' ) ) {
					$names[] = $key;
				}
			}
		}

		if ( ! empty( $names ) ) {
			$GLOBALS['WP_ANALYTIFY']->analytify_ensure_tracking_dimensions( $names );
		}

		return $events;
	},
	5
);

add_filter(
	'analytify_gtag_configuration',
	static function ( $configuration ) {
		if ( ! is_array( $configuration ) || ! isset( $GLOBALS['WP_ANALYTIFY'] ) || ! method_exists( $GLOBALS['WP_ANALYTIFY'], 'analytify_ensure_tracking_dimensions' ) ) {
			return $configuration;
		}

		$names = array();
		foreach ( array_keys( $configuration ) as $key ) {
			if ( is_string( $key ) && 0 === strpos( $key, 'wpa_' ) ) {
				$names[] = $key;
			}
		}

		if ( ! empty( $names ) ) {
			$GLOBALS['WP_ANALYTIFY']->analytify_ensure_tracking_dimensions( $names );
		}

		return $configuration;
	},
	99
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		static $scheduled = false;

		if ( $scheduled || ! isset( $GLOBALS['WP_ANALYTIFY'] ) || ! method_exists( $GLOBALS['WP_ANALYTIFY'], 'analytify_ensure_tracking_dimensions_for_enqueued_scripts' ) ) {
			return;
		}

		$scheduled = true;
		add_action(
			'shutdown',
			static function () {
				if ( isset( $GLOBALS['WP_ANALYTIFY'] ) && method_exists( $GLOBALS['WP_ANALYTIFY'], 'analytify_ensure_tracking_dimensions_for_enqueued_scripts' ) ) {
					$GLOBALS['WP_ANALYTIFY']->analytify_ensure_tracking_dimensions_for_enqueued_scripts();
				}
			},
			99
		);
	},
	999
);

// phpcs:enable Universal.Files.SeparateFunctionsFromOO.Mixed
