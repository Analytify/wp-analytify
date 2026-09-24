<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Analytify General Class
 *
 * This is the core class that sets the foundation for the Analytify plugin.
 * It handles analytics wrappers, SDK calls to fetch data from Google Analytics,
 * and provides the base functionality for all other plugin components.
 *
 * @package WP_Analytify
 * @since 1.0.0
 * @version 8.0.0
 *
 * @author Analytify Team
 * @license GPL-2.0+
 *
 * @see https://analytify.io/
 * @see https://wordpress.org/plugins/wp-analytify/
 */


		// Include core classes first.
		require_once __DIR__ . '/classes/analytify-utils.php';
		require_once __DIR__ . '/classes/analytify-settings.php';
		require_once __DIR__ . '/classes/analytify-mp-ga4.php';

		// Include all trait files.
		require_once __DIR__ . '/inc/analytify-authentication.php';
		require_once __DIR__ . '/inc/analytify-ga4-core.php';
		require_once __DIR__ . '/inc/analytify-utilities.php';
		require_once __DIR__ . '/inc/analytify-navigation.php';

if ( ! class_exists( 'Analytify_General' ) ) {

	/**
	 * Analytify_General Class for Analytify.
	 */
	class Analytify_General {

		// Use all the traits.
		use Analytify_Authentication;
		use Analytify_GA4_Core;
		use Analytify_General_Utilities;
		use Analytify_Navigation;

		/**
		 * Plugin settings object.
		 *
		 * @var object
		 */
		public $settings;

		/**
		 * Google Analytics service object.
		 *
		 * @var object
		 */
		public $service;

		/**
		 * Google Analytics client object.
		 *
		 * @var object
		 */
		public $client;

		/**
		 * Authentication token.
		 *
		 * @var string
		 */
		public $token;

		/**
		 * State data for authentication.
		 *
		 * @var array
		 */
		protected $state_data;

		/**
		 * Transient timeout duration.
		 *
		 * @var int
		 */
		protected $transient_timeout;

		/**
		 * Load settings flag.
		 *
		 * @var bool
		 */
		protected $load_settings;

		/**
		 * Plugin base URL.
		 *
		 * @var string
		 */
		protected $plugin_base;

		/**
		 * Plugin settings base URL.
		 *
		 * @var string
		 */
		protected $plugin_settings_base;

		/**
		 * Cache timeout duration.
		 *
		 * @var int
		 */
		protected $cache_timeout;

		/**
		 * Exception data.
		 *
		 * @var mixed
		 */
		private $exception;

		/**
		 * GA4 exception data.
		 *
		 * @var mixed
		 */
		private $ga4_exception;

		/**
		 * Available modules.
		 *
		 * @var array
		 */
		private $modules;

		/**
		 * GA4 reporting flag.
		 *
		 * @var bool
		 */
		protected $is_reporting_in_ga4;

		/**
		 * User added client ID.
		 *
		 * @var string
		 */
		private $user_client_id;

		/**
		 * User added client secret.
		 *
		 * @var string
		 */
		private $user_client_secret;

		/**
		 * Authentication date format.
		 *
		 * @var string
		 */
		protected $auth_date_format;

		/**
		 * Google token data.
		 *
		 * @var array|false
		 */
		protected $google_token;

		/**
		 * GA4 streams data.
		 *
		 * @var array
		 */
		protected $ga4_streams;

		/**
		 * Constructor of analytify-general class.
		 *
		 * Initializes the core plugin settings, authentication data, and prepares
		 * the environment for Google Analytics operations.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set cache timeout to 12 hours (60 * 60 * 12 seconds).
			$this->transient_timeout = 60 * 60 * 12;

			// Define admin page URLs for navigation.
			$this->plugin_base          = 'admin.php?page=analytify-dashboard';
			$this->plugin_settings_base = 'admin.php?page=analytify-settings';

			// Set authentication date format with timezone.
			$this->auth_date_format = gmdate( 'l jS F Y h:i:s A' ) . ' ' . date_default_timezone_get();
			// Sanitize page parameter for security.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
			$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			if ( $current_page && strpos( $current_page, 'analytify-settings' ) === 0 ) {
				$this->exception     = get_option( 'analytify_profile_exception' );
				$this->ga4_exception = get_option( 'analytify_ga4_exceptions' );
			}
			$this->modules = WPANALYTIFY_Utils::get_pro_modules();
			// Setup Settings.
			if ( class_exists( 'WP_Analytify_Settings' ) ) {
				$this->settings = new WP_Analytify_Settings();
			}

			$this->is_reporting_in_ga4 = 'ga4' === WPANALYTIFY_Utils::get_ga_mode() ? true : false;

			// Initialize connection on init hook to ensure themes are loaded.
			add_action( 'init', array( $this, 'init_connection' ) );
		}

		/**
		 * Initialize connection to Google Analytics.
		 *
		 * This method is hooked to 'init' to ensure that themes (functions.php) are loaded
		 * before the connection is attempted. This allows custom hooks to fire correctly.
		 *
		 * @since 7.1.4
		 */
		public function init_connection() {
			if ( true === $this->is_reporting_in_ga4 ) {
				// Rankmath Instant Indexing addon Compatibility.
				// Sanitize page parameter for security.
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
				$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				if ( ( $current_page && 'instant-indexing' === $current_page ) || strpos( wp_get_referer(), 'instant-indexing' ) !== false ) {
					return;
				}

				if ( 'on' === $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced', '' ) ) {
					$this->user_client_id     = $this->settings->get_option( 'client_id', 'wp-analytify-advanced' );
					$this->user_client_secret = $this->settings->get_option( 'client_secret', 'wp-analytify-advanced' );
				}

				try {
					$this->analytify_pa_connect_v2();
				} catch ( Exception $e ) {
					// Show error message only for logged in users.
					if ( current_user_can( 'manage_options' ) ) {
						// translators: Reset authentication error message.
						printf( esc_html__( '%1$s Oops, Try to %2$s Reset %3$s Authentication. %4$s %7$s %4$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . 'title="Reset">', '</a>', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
					}
				}
			}

			// Set cache time directly since after_setup_theme has already fired before init.
			$this->set_cache_time();

			$this->analytify_set_tracking_mode();
		}

		/**
		 * This function grabs the data from Google Analytics for individual posts/pages.
		 *
		 * @param string  $metrics     The metrics to retrieve.
		 * @param string  $start_date  The start date for the report.
		 * @param string  $end_date    The end date for the report.
		 * @param boolean $dimensions  Optional dimensions for the report.
		 * @param boolean $sort        Optional sorting for the report.
		 * @param boolean $filter      Optional filters for the report.
		 * @param boolean $limit       Optional limit for the report.
		 * @param string  $name        Optional name for caching.
		 * @return void
		 */
		public function pa_get_analytics( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false, $name = '' ) {

			if ( $this->is_reporting_in_ga4 ) {
				return;
			}

			try {
				$this->service = new Analytify_Google_Service_Analytics( $this->client );
				$params        = array();

				if ( $dimensions ) {
					$params['dimensions'] = $dimensions;
				}

				if ( $sort ) {
					$params['sort'] = $sort;
				}

				if ( $filter ) {
					$params['filters'] = $filter;
				}

				if ( $limit ) {
					$params['max-results'] = $limit;
				}

				$profile_id = $this->settings->get_option( 'profile_for_posts', 'wp-analytify-profile' );

				if ( ! $profile_id ) {
					return false;
				}

				$transient_key = 'analytify_transient_';
				$cache_result  = get_transient( $transient_key . md5( $name . $profile_id . $start_date . $end_date . $filter ) );

				// Note: This hard coded setting should be removed in future versions.

				$is_custom_api = $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced' );

				if ( 'on' !== $is_custom_api ) {
					// If exception, return if the cache result else return the error.
					$exception = get_transient( 'analytify_quota_exception' );
					if ( $exception ) {
						return $this->tackle_exception( $exception, $cache_result );
					}
				}

				// If custom keys set. Fetch fresh result always.
				if ( 'on' === $is_custom_api || false === $cache_result ) {
					$result = $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );
					set_transient( $transient_key . md5( $name . $profile_id . $start_date . $end_date . $filter ), $result, $this->get_cache_time() );
					return $result;

				} else {
					return $cache_result;
				}
			} catch ( Analytify_Google_Service_Exception $e ) {
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					echo "<div class='wp_analytify_error_msg'>";
					// translators: Error message for logged in users.
					printf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s ', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					echo '</div>';
				}
			} catch ( Analytify_Google_Auth_Exception $e ) {
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					echo "<div class='wp_analytify_error_msg'>";
					// translators: Reset authentication error message.
					printf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . ' title="Reset">', '</a>', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
					echo '</div>';
				}
			} catch ( Analytify_Google_IO_Exception $e ) {
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					echo "<div class='wp_analytify_error_msg'>";
					// translators: Error message.
					printf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					echo '</div>';
				}
			}
		}

		/**
		 * Mock Function to resist GA3 removal conflicts.
		 *
		 * @param string  $metrics     The metrics to retrieve.
		 * @param string  $start_date  The start date for the report.
		 * @param string  $end_date    The end date for the report.
		 * @param boolean $dimensions  Optional dimensions for the report.
		 * @param boolean $sort        Optional sorting for the report.
		 * @param boolean $filter      Optional filters for the report.
		 * @param boolean $limit       Optional limit for the report.
		 * @param string  $name        Optional name for caching.
		 * @return null|false
		 */
		public function pa_get_analytics_dashboard( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false, $name = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			if ( $this->is_reporting_in_ga4 ) {
				return null;
			}
			return false;
		}

		/**
		 * Mock Function to resist GA3 removal conflicts.
		 *
		 * @param string  $metrics     The metrics to retrieve.
		 * @param string  $start_date  The start date for the report.
		 * @param string  $end_date    The end date for the report.
		 * @param boolean $dimensions  Optional dimensions for the report.
		 * @param boolean $sort        Optional sorting for the report.
		 * @param boolean $filter      Optional filters for the report.
		 * @param boolean $limit       Optional limit for the report.
		 * @param string  $name        Optional name for caching.
		 * @return null|false
		 */
		public function pa_get_analytics_dashboard_via_rest( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false, $name = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			if ( $this->is_reporting_in_ga4 ) {
				return null;
			}
			return false;
		}

		/**
		 * This function grabs the data from Google Analytics For dashboard.
		 *
		 * @param string $profile    Google Analytic Profile Id.
		 * @param string $metrics    Metrics.
		 * @param string $start_date Start date of stats.
		 * @param string $end_date   End date of stats.
		 * @param string $dimensions Dimensions.
		 * @param string $sort       Sort.
		 * @param string $filter     Filter.
		 * @param string $limit      How many stats to show.
		 *
		 * @return array Return array of stats.
		 */
		public function analytify_get_analytics( $profile, $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false ) {

			if ( $this->is_reporting_in_ga4 ) {
				return null;
			}
			try {
				if ( class_exists( 'Analytify_Google_Service_Analytics' ) ) {
					$this->service = new Analytify_Google_Service_Analytics( $this->client );
				}
				$params = array();

				if ( $dimensions ) {
					$params['dimensions'] = $dimensions;
				}
				if ( $sort ) {
					$params['sort'] = $sort;
				}
				if ( $filter ) {
					$params['filters'] = $filter;
				}
				if ( $limit ) {
					$params['max-results'] = $limit;
				}

				if ( 'single' === $profile ) {
					$profile_id = $this->settings->get_option( 'profile_for_posts', 'wp-analytify-profile' );
				} else {
					$profile_id = $this->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
				}

				if ( ! $profile_id ) {
					return false;
				}

				return $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );
			} catch ( Analytify_Google_Service_Exception $e ) {
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					// translators: Error message.
					printf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s ', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
				}
			} catch ( Analytify_Google_Auth_Exception $e ) {
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					// translators: Error message.
					printf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . ' title="Reset">', '</a>', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
				}
			} catch ( Analytify_Google_IO_Exception $e ) {
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					// translators: Error message.
					printf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					echo '</div>';
				}
			}
		}

		/**
		 * Fetch reports from Google Analytics Data API.
		 *
		 * @param string        $name 'test-report-name' Its the key used to store reports in transient as cache.
		 * @param array         $metrics Array of metrics to fetch.
		 * @param array         $date_range Date range for the report.
		 * @param array         $dimensions Array of dimensions.
		 * @param array         $order_by Sorting configuration.
		 * @param array         $filters Filter configuration.
		 * @param integer array $limit       Positive integer to limit report rows.
		 * @param boolean       $cached      Whether to use cached results.
		 *
		 * @return array {
		 *     'headers' => {
		 *         ...
		 *     },
		 *     'rows' => {
		 *         ...
		 *     }
		 * }
		 * @version 7.0.1
		 * @throws Exception When API request fails.
		 */
		public function get_reports( $name, $metrics, $date_range, $dimensions = array(), $order_by = array(), $filters = array(), $limit = 0, $cached = true ) {
			$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;

			$property_id = WPANALYTIFY_Utils::get_reporting_property();

			// Don't use cache if custom API keys are in use.
			if ( 'on' === $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced' ) ) {
				$cached = false;
			}

			// To override the caching.
			$cached = apply_filters( 'analytify_set_caching_to', $cached );

			if ( $cached ) {
				$cache_key    = 'analytify_transient_' . md5( $name . $property_id . $date_range['start'] . $date_range['end'] );
				$report_cache = get_transient( $cache_key );

				if ( $report_cache ) {
					return $report_cache;
				}
			}

			$reports           = array();
			$dimension_filters = array();

			// Default response array.
			$default_response = array(
				'headers'      => array(),
				'rows'         => array(),
				'error'        => array(),
				'aggregations' => array(),
			);

			try {
				// Main request body for the report.
				$request_body = array(
					'dateRanges'         => array(
						array(
							'startDate' => isset( $date_range['start'] ) ? $date_range['start'] : 'today',
							'endDate'   => isset( $date_range['end'] ) ? $date_range['end'] : 'today',
						),
					),
					'metricAggregations' => array( 1 ), // TOTAL = 1; COUNT = 4; MINIMUM = 5; MAXIMUM = 6.
				);

				// Set metrics.
				if ( $metrics ) {
					$send_metrics = array();
					foreach ( $metrics as $value ) {
						$send_metrics[] = array( 'name' => $value );
					}
					$request_body['metrics'] = $send_metrics;
				}

				// Add dimensions.
				if ( $dimensions ) {
					$send_dimensions = array();
					foreach ( $dimensions as $value ) {
						$send_dimensions[] = array( 'name' => $value );
					}
					$request_body['dimensions'] = $send_dimensions;
				}

				// Order report by metric or dimension.
				if ( $order_by ) {
					$order_by_request = array();
					$is_desc          = ( empty( $order_by['order'] ) || 'desc' !== $order_by['order'] ) ? false : true;

					if ( 'metric' === $order_by['type'] ) {
						$order_by_request = array(
							'metric' => array(
								'metric_name' => isset( $order_by['name'] ) ? $order_by['name'] : '',
							),
							'desc'   => $is_desc,
						);
					} elseif ( 'dimension' === $order_by['type'] ) {
						$order_by_request = array(
							'dimension' => array(
								'dimension_name' => $order_by['name'],
							),
							'desc'      => $is_desc,
						);
					}

					$request_body['orderBys'] = array( $order_by_request );
				}

				// Filters for the report.
				if ( $filters ) {
					$dimension_filters = array(); // Initialize an empty array for filters.

					foreach ( $filters['filters'] as $filter_data ) {
						if ( 'dimension' === $filter_data['type'] ) {
							if ( isset( $filter_data['not_expression'] ) && $filter_data['not_expression'] ) {
								// Handle 'not_expression' logic.
								$dimension_filters[] = array(
									'not_expression' => array(
										'filter' => array(
											'field_name' => $filter_data['name'],
											'string_filter' => array(
												'match_type' => $filter_data['match_type'],
												'value' => $filter_data['value'],
												'case_sensitive' => true,
											),
										),
									),
								);
							} else {
								// Standard dimension filter.
								$dimension_filters[] = array(
									'filter' => array(
										'field_name'    => $filter_data['name'],
										'string_filter' => array(
											'match_type' => $filter_data['match_type'],
											'value'      => $filter_data['value'],
											'case_sensitive' => true,
										),
									),
								);
							}
						} elseif ( 'metric' === $filter_data['type'] ) {
							// Note: Add metric filter handling here.
							// Currently no implementation for metric filters.
							// This is intentionally left empty for future implementation.
							// No action needed for metric filters at this time.
							// Skip metric filters without affecting dimension_filters array.
							continue;
						}
					}

					if ( $dimension_filters ) {
						$group_type = ( isset( $filters['logic'] ) && 'OR' === $filters['logic'] ) ? 'or_group' : 'and_group';

						$dimension_filter_construct = array(
							$group_type => array(
								'expressions' => $dimension_filters,
							),
						);

						$request_body['dimensionFilter'] = $dimension_filter_construct;
					}
				}

				// Set limit.
				if ( 0 < $limit ) {
					$request_body['limit'] = $limit;
				}

				// Get access token (this function should be implemented by you).
				$token = $this->analytify_get_google_token();

				// Validate that token is an array and has the expected structure.
				if ( ! is_array( $token ) || ! isset( $token['access_token'] ) ) {
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Invalid or missing Google Analytics token in get_reports.',
							array(
								'source'           => 'get_reports',
								'report_name'      => $name,
								'token_type'       => gettype( $token ),
								'has_access_token' => isset( $token['access_token'] ),
							)
						);
					}
					return array();
				}

				$access_token = $token['access_token'];

				// Prepare the cURL request URL for GA4 API.
				$url = 'https://analyticsdata.googleapis.com/v1beta/properties/' . $property_id . ':runReport';

				// Send the request using wp_remote_post.
				$response = wp_remote_post(
					$url,
					array(
						'headers' => array(
							'Authorization' => 'Bearer ' . $access_token,
							'Content-Type'  => 'application/json',
						),
						'body'    => wp_json_encode( $request_body ),
					)
				);

				// Check for errors in the response.
				if ( is_wp_error( $response ) ) {
					throw new Exception( $response->get_error_message() );
				}

				// Parse the response body.
				$reports = json_decode( wp_remote_retrieve_body( $response ), true );

				// If the response doesn't contain rows, handle it accordingly.
				if ( ! isset( $reports['rows'] ) ) {
					return $default_response;
				}
			} catch ( \Throwable $th ) {
				if ( method_exists( $th, 'getStatus' ) && method_exists( $th, 'getBasicMessage' ) ) {
					$default_response['error'] = array(
						'status'  => $th->getStatus(),
						'message' => $th->getBasicMessage(),
					);
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Exception in get_reports API call.',
							array(
								'source'         => 'get_reports',
								'report_name'    => $name,
								'status'         => $th->getStatus(),
								'message'        => $th->getBasicMessage(),
								'exception_type' => get_class( $th ),
							)
						);
					}
				} elseif ( method_exists( $th, 'getMessage' ) ) {
					$default_response['error'] = array(
						'status'  => 'Token Expired',
						'message' => $th->getMessage(),
					);
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Exception in get_reports API call - token expired.',
							array(
								'source'         => 'get_reports',
								'report_name'    => $name,
								'message'        => $th->getMessage(),
								'exception_type' => get_class( $th ),
							)
						);
					}
				}

				return $default_response;
			}

			// Format the reports using your existing function.
			$formatted_reports = $this->analytify_format_ga_reports( $reports );

			if ( empty( $formatted_reports ) ) {
				return $default_response;
			}

			// Cache the response if caching is enabled.
			if ( $cached ) {
				$this->analytify_handle_report_cache( $cache_key, $formatted_reports, $name, $cached );
			}

			return $formatted_reports;
		}

		/**
		 * Format reports data fetched from Google Analytics Data API.
		 *
		 * For references check folder for class definitions: lib\Google\vendor\google\analytics-data\src\V1beta.
		 *
		 * @param array $reports The reports data to format.
		 * @return array
		 */
		public function analytify_format_ga_reports( $reports ) {
			$metric_header_data    = array();
			$dimension_header_data = array();
			$aggregations          = array();
			$rows                  = array();

			// Get metric headers.
			if ( isset( $reports['metricHeaders'] ) ) {
				foreach ( $reports['metricHeaders'] as $metric_header ) {
					$metric_header_data[] = $metric_header['name'];
				}
			}

			// Get dimension headers.
			if ( isset( $reports['dimensionHeaders'] ) ) {
				foreach ( $reports['dimensionHeaders'] as $dimension_header ) {
					$dimension_header_data[] = $dimension_header['name'];
				}
			}

			$headers = array_merge( $metric_header_data, $dimension_header_data );

			// Bind metrics and dimensions to rows.
			if ( isset( $reports['rows'] ) ) {
				foreach ( $reports['rows'] as $row ) {
					$metric_data    = array();
					$dimension_data = array();

					// Process metric values.
					if ( isset( $row['metricValues'] ) ) {
						$index_metric = 0;
						foreach ( $row['metricValues'] as $value ) {
							$metric_data[ $metric_header_data[ $index_metric ] ] = $value['value'];
							++$index_metric;
						}
					}

					// Process dimension values.
					if ( isset( $row['dimensionValues'] ) ) {
						$index_dimension = 0;
						foreach ( $row['dimensionValues'] as $value ) {
							$dimension_data[ $dimension_header_data[ $index_dimension ] ] = $value['value'];
							++$index_dimension;
						}
					}

					// Combine metric and dimension data.
					$rows[] = array_merge( $metric_data, $dimension_data );
				}
			}

			// Get metric aggregations (totals).
			if ( isset( $reports['totals'] ) ) {
				foreach ( $reports['totals'] as $total ) {
					$index_metric = 0;

					if ( isset( $total['metricValues'] ) ) {
						foreach ( $total['metricValues'] as $value ) {
							$aggregations[ $metric_header_data[ $index_metric ] ] = $value['value'];
							++$index_metric;
						}
					}
				}
			}

			// Format and return the data.
			$formatted_data = array(
				'headers'      => $headers,
				'rows'         => $rows,
				'aggregations' => $aggregations,
			);

			return $formatted_data;
		}


		/**
		 * Query the search console api and return the response.
		 * Since SC can have two types of domain properties.
		 * We will first go with the sc-domain prefix with property
		 * if it fails we will use the second domain type using 'https://'
		 *
		 * @param string $transient_name The transient name for caching.
		 * @param array  $dates The date range for the query.
		 * @param int    $limit The limit for the results.
		 *
		 * @since 5.0.0
		 * @version 9.0.0
		 */
		public function get_search_console_stats( $transient_name, $dates = array(), $limit = 10 ) {

			$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;

			if ( class_exists( 'QM' ) ) {
				QM::info( 'Analytify: Getting Google Analytics token for Search Console stats.' );
			}

			$token = $this->analytify_get_google_token();

			if ( ! is_array( $token ) || ! isset( $token['access_token'] ) ) {
				return array( 'error' => array( 'Invalid or missing Google Analytics token.' ) );
			}

			$access_token = $token['access_token'];

			$tracking_stream_info = get_option( 'analytify_tracking_property_info' );

			try {
				$stream_url = ( isset( $tracking_stream_info['url'] ) && ! empty( $tracking_stream_info['url'] ) ) ? $tracking_stream_info['url'] : null;
			} catch ( \Throwable $th ) {
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning(
						'Error fetching stream URL',
						array(
							'source'  => 'analytify_fetch_stream_url',
							'message' => $th->getMessage(),
						)
					);
				}

				if ( empty( $stream_url ) ) {
					return array(
						'error' => array(
							'status'  => 'No Stats Available',
							'message' => __( 'No URL found for the selected stream', 'wp-analytify' ),
						),
					);
				}
			}

			// Validate stream URL.
			if ( empty( $stream_url ) ) {
				return array(
					'error' => array(
						'status'  => 'No Stats Available',
						'message' => __( 'No URL found for the selected stream', 'wp-analytify' ),
					),
				);
			}

			// Sanitize URL.
			$stream_url = trim( $stream_url );
			$stream_url = esc_url_raw( $stream_url );

			// Extract domain (handles ports and IPv6).
			$domain_stream_url_filtered = preg_replace( '/^(https?:\/\/)?(www\.)?([^\/\s:]+(?::\d+)?|\[[^\]]+\])(\/.*)?$/i', '$3', $stream_url );
			$domain_stream_url_filtered = preg_replace( '/:\d+$/', '', $domain_stream_url_filtered ); // Remove port.
			$domain_stream_url_filtered = str_replace( array( '[', ']' ), '', $domain_stream_url_filtered ); // Remove IPv6 brackets.

			// Build candidate URLs for Search Console API.
			$urls = array(
				'sc-domain:' . $domain_stream_url_filtered,
				'https://' . $domain_stream_url_filtered,
				'https://www.' . $domain_stream_url_filtered,
				'http://' . $domain_stream_url_filtered,
				'http://www.' . $domain_stream_url_filtered,
				'https://' . rtrim( $domain_stream_url_filtered, '/' ) . '/', // URL-prefix format.
			);

			// Remove duplicates to avoid redundant API calls.
			$urls = array_unique( $urls );

			$base_url   = ANALYTIFY_GOOGLE_SEARCH_CONSOLE_API_URL;
			$start_date = $dates['start'] ?? 'yesterday';
			$end_date   = $dates['end'] ?? 'today';

			// Track responses: prefer domains with data, fallback to any accepted domain.
			$accepted_domains_with_data = array();
			$accepted_domains_no_data   = array();

			foreach ( $urls as $url ) {
				try {
					$query_data = array(
						'startDate'  => $start_date,
						'endDate'    => $end_date,
						'dimensions' => array( 'query' ),
						'rowLimit'   => $limit,
					);

					// Make request to Search Console API using WordPress HTTP API.
					$http_response = wp_remote_post(
						$base_url . rawurlencode( $url ) . '/searchAnalytics/query',
						array(
							'headers'   => array(
								'Authorization' => 'Bearer ' . $access_token,
								'Content-Type'  => 'application/json',
							),
							'body'      => wp_json_encode( $query_data ),
							'timeout'   => 30,
							'sslverify' => true, // Explicitly ensure SSL verification.
						)
					);

					if ( is_wp_error( $http_response ) ) {
						if ( $logger && method_exists( $logger, 'warning' ) ) {
							$logger->warning(
								sprintf( 'HTTP request failed for domain "%s": %s', $url, $http_response->get_error_message() ),
								array(
									'source' => 'analytify_fetch_search_console_stats',
									'domain' => $url,
								)
							);
						}
						continue; // Continue to next URL.
					}

					$http_code     = wp_remote_retrieve_response_code( $http_response );
					$response_body = wp_remote_retrieve_body( $http_response );

					// Log all HTTP responses for debugging, but categorize them.
					if ( 200 === $http_code ) {
						$decoded = json_decode( $response_body, true );

						// Validate JSON decode result.
						if ( json_last_error() !== JSON_ERROR_NONE ) {
							if ( $logger && method_exists( $logger, 'error' ) ) {
								$logger->error(
									sprintf( 'JSON decode failed for domain "%s": %s', $url, json_last_error_msg() ),
									array(
										'source' => 'analytify_fetch_search_console_stats',
										'domain' => $url,
									)
								);
							}
							continue;
						}

						// Ensure decoded result is an array.
						if ( ! is_array( $decoded ) ) {
							if ( $logger && method_exists( $logger, 'error' ) ) {
								$logger->error(
									sprintf( 'Unexpected JSON response for domain "%s": not an array', $url ),
									array(
										'source' => 'analytify_fetch_search_console_stats',
										'domain' => $url,
									)
								);
							}
							continue;
						}

						$row_count = count( $decoded['rows'] ?? array() );

						// Log domain check result for debugging.
						if ( $logger && method_exists( $logger, 'info' ) ) {
							$logger->info(
								sprintf( 'Domain "%s" - HTTP %d, %d rows found', $url, $http_code, $row_count ),
								array(
									'source'    => 'analytify_fetch_search_console_stats',
									'domain'    => $url,
									'http_code' => $http_code,
									'row_count' => $row_count,
								)
							);
						}

						// Store this response - prefer domains with data.
						if ( $row_count > 0 ) {
							$accepted_domains_with_data[] = array(
								'url'  => $url,
								'data' => $decoded,
							);
							if ( $logger && method_exists( $logger, 'info' ) ) {
								$logger->info(
									'Domain categorized as HAVING DATA',
									array(
										'source'    => 'analytify_fetch_search_console_stats',
										'domain'    => $url,
										'row_count' => $row_count,
									)
								);
							}
						} else {
							$accepted_domains_no_data[] = array(
								'url'  => $url,
								'data' => $decoded,
							);
							if ( $logger && method_exists( $logger, 'info' ) ) {
								$logger->info(
									'Domain categorized as NO DATA',
									array(
										'source'    => 'analytify_fetch_search_console_stats',
										'domain'    => $url,
										'row_count' => $row_count,
									)
								);
							}
						}
					} elseif ( 200 !== $http_code ) {
						// Log non-200 with code only; do not log response body (may contain sensitive data).
						if ( $logger && method_exists( $logger, 'warning' ) ) {
							$logger->warning(
								sprintf( 'Domain "%s" returned HTTP %d', $url, $http_code ),
								array(
									'source'    => 'analytify_fetch_search_console_stats',
									'domain'    => $url,
									'http_code' => $http_code,
								)
							);
						}
					}
				} catch ( \Throwable $th ) {
					// Continue to next URL on exception.
					continue;
				}
			}

			// Choose the best domain after checking ALL URLs.
			$chosen_domain = null;

			// Priority 1: Any domain with actual keyword data (prefer first one found).
			if ( ! empty( $accepted_domains_with_data ) ) {
				$chosen_domain = $accepted_domains_with_data[0]; // Use first domain that has data.
			} // phpcs:ignore Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace
			// Priority 2: If NO domains have data, use first accepted domain (fallback).
			elseif ( ! empty( $accepted_domains_no_data ) ) {
				$chosen_domain = $accepted_domains_no_data[0]; // Use first accepted domain as fallback.
			}

			// Return the chosen domain's data.
			if ( $chosen_domain ) {
				return array(
					'response' => $chosen_domain['data'],
				);
			}

			// No domains were accepted at all.
			if ( $logger && method_exists( $logger, 'warning' ) ) {
				$logger->warning(
					'FINAL FAILURE: No domain accepted',
					array(
						'source' => 'analytify_fetch_search_console_stats',
						'site'   => $domain_stream_url_filtered,
					)
				);
			}

			return array(
				'error' => array(
					'status'  => "No Stats Available for $domain_stream_url_filtered",
					'message' => __( 'Analytify gets GA4 keyword stats from Search Console. Make sure the site is verified and you have owner access.', 'wp-analytify' ),
				),
			);
		}
	} // End of class
} // End of if class exists
