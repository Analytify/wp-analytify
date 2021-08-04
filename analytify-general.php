<?php
/**
 * Base Class to use for the Add-ons
 * It will be used to extend the functionality of Analytify WordPress Plugin.
 *
 *  @package WP_Analytify
 */

// Setting Global Values.
define( 'ANALYTIFY_LIB_PATH', dirname( __FILE__ ) . '/lib/' );
define( 'ANALYTIFY_ID', 'wp-analytify-options' );
define( 'ANALYTIFY_NICK', 'Analytify' );
define( 'ANALYTIFY_ROOT_PATH', dirname( __FILE__ ) );
define( 'ANALYTIFY_VERSION', '4.1.1' );
define( 'ANALYTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANALYTIFY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Grab ClientID and ClientSecret from https://console.developers.google.com/ after creating a project there.

if ( get_option( 'wpa_current_version' ) ) { // Pro Keys

	define( 'ANALYTIFY_CLIENTID', '707435375568-9lria1uirhitcit2bhfg0rgbi19smjhg.apps.googleusercontent.com' );
	define( 'ANALYTIFY_CLIENTSECRET', 'b9C77PiPSEvrJvCu_a3dzXoJ' );
} else { // Free Keys

	define( 'ANALYTIFY_CLIENTID', '958799092305-7p6jlsnmv1dn44a03ma00kmdrau2i31q.apps.googleusercontent.com' );
	define( 'ANALYTIFY_CLIENTSECRET', 'Mzs1ODgJTpjk8mzQ3mbrypD3' );
}

define( 'ANALYTIFY_REDIRECT', 'https://analytify.io/api/' );
define( 'ANALYTIFY_SCOPE', 'https://www.googleapis.com/auth/analytics.readonly' ); // Readonly scope.
define( 'ANALYTIFY_DEV_KEY', 'AIzaSyDXjBezSlaVMPk8OEi8Vw5aFvteouXHZpI' );

define( 'ANALYTIFY_STORE_URL', 'https://analytify.io' );
define( 'ANALYTIFY_PRODUCT_NAME', 'Analytify WordPress Plugin' );

// require_once WP_PLUGIN_DIR . '/wp-analytify-pro/inc/class-analytify-logging.php';
include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-settings.php';
include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-utils.php';
include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-sanitize.php';

// Update routine.
include_once ANALYTIFY_PLUGIN_DIR . '/classes/analytify-update-routine.php';

if ( ! class_exists( 'Analytify_General' ) ) {

	/**
	 * Analytify_General Class for Analytify.
	 */
	class Analytify_General {

		public $settings;
		protected $state_data;
		protected $transient_timeout;
		protected $load_settings;
		protected $plugin_base;
		protected $plugin_settings_base;
		protected $cache_timeout;
		private $exception;

		// option for modules
		private $modules;

		/**
		 * Constructer of analytify-general class.
		 */
		function __construct() {

			$this->transient_timeout    = 60 * 60 * 12;
			// $this->cache_timeout    		= 60 * 60 * 24; // 24 hours into seconds. Use for transient cache.
			$this->plugin_base          = 'admin.php?page=analytify-dashboard';
			$this->plugin_settings_base = 'admin.php?page=analytify-settings';
			$this->exception            = get_option( 'analytify_profile_exception' );
			$this->modules				= get_option( 'wp_analytify_modules' );

			if ( ! class_exists( 'Analytify_Google_Client' ) ) {

				require_once ANALYTIFY_LIB_PATH . 'Google/Client.php';
				require_once ANALYTIFY_LIB_PATH . 'Google/Service/Analytics.php';

			}

			// Setup Settings.
			$this->settings = new WP_Analytify_Settings();

			$this->client = new Analytify_Google_Client();
			$this->client->setApprovalPrompt( 'force' );
			$this->client->setAccessType( 'offline' );

			if ( $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced', '' ) == 'on' ) {

				$this->client->setClientId( $this->settings->get_option( 'client_id' ,'wp-analytify-advanced' ) );
				$this->client->setClientSecret( $this->settings->get_option( 'client_secret', 'wp-analytify-advanced' ));
				$this->client->setRedirectUri( $this->settings->get_option( 'redirect_uri', 'wp-analytify-advanced' ) );
				// $this->client->setDeveloperKey( get_option( 'ANALYTIFY_DEV_KEY' ) );
			} else {

				$this->client->setClientId( ANALYTIFY_CLIENTID );
				$this->client->setClientSecret( ANALYTIFY_CLIENTSECRET );
				$this->client->setRedirectUri( ANALYTIFY_REDIRECT );
				// $this->client->setDeveloperKey( ANALYTIFY_DEV_KEY );
			}

			$this->client->setScopes( ANALYTIFY_SCOPE );

			try {

				$this->service = new Analytify_Google_Service_Analytics( $this->client );

				$this->pa_connect();

				// This function refresh token and use for debugging
				//$this->client->refreshToken( $this->token->refresh_token );


			} catch ( Analytify_Google_Service_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s ', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
				}
			} catch ( Analytify_Google_Auth_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops, Try to %2$s Reset %3$s Authentication. %4$s %7$s %4$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %4$s', 'wp-analytify' ), '<br /><br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . 'title="Reset">', '</a>', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
				}
			}

			add_action( 'admin_init', array( $this, 'set_cache_time' ) );

			$this->set_tracking_mode();
		}

		/**
		 * Check the tracking method.
		 *
		 * @return string ga/gtag
		 */
		public function set_tracking_mode() {

			if ( ! defined( 'ANALYTIFY_TRACKING_MODE' ) ) {
				define( 'ANALYTIFY_TRACKING_MODE', $this->settings->get_option( 'gtag_tracking_mode', 'wp-analytify-advanced', 'ga' ) );
			}
		}

		/**
		 * Connect with Google Analytics API and get authentication token and save it.
		 */

		public function pa_connect() {

			$ga_google_authtoken = get_option( 'pa_google_token' );

			if ( ! empty( $ga_google_authtoken ) ) {

				$this->client->setAccessToken( $ga_google_authtoken );
			} else {

				$auth_code = get_option( 'post_analytics_token' );

				if ( empty( $auth_code ) ) { return false; }

				try {

					$access_token = $this->client->authenticate( $auth_code );
				} catch ( Exception $e ) {
					echo 'Analytify (Bug): ' . esc_textarea( $e->getMessage() );
					return false;
				}

				if ( $access_token ) {

					$this->client->setAccessToken( $access_token );

					update_option( 'pa_google_token', $access_token );
					update_option( 'analytify_authentication_date', date( 'l jS F Y h:i:s A' ) . date_default_timezone_get() );

					return true;
				} else {

					return false;
				}
			}

			$this->token = json_decode( $this->client->getAccessToken() );

			return true;
		}

		/**
		 * This function grabs the data from Google Analytics
		 * For individual posts/pages.
		 */
		public function pa_get_analytics( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false, $name = ''  ) {

			try {

				$this->service = new Analytify_Google_Service_Analytics( $this->client );
				$params        = array();

				if ( $dimensions ) {
					$params['dimensions'] = $dimensions;
				} //$dimensions

				if ( $sort ) {
					$params['sort'] = $sort;
				} //$sort

				if ( $filter ) {
					$params['filters'] = $filter;
				} //$filter

				if ( $limit ) {
					$params['max-results'] = $limit;
				} //$limit

				$profile_id = $this->settings->get_option( 'profile_for_posts', 'wp-analytify-profile' );

				if ( ! $profile_id ) {
					return false;
				}

				$transient_key = 'analytify_transient_';
				$cache_result  = get_transient( $transient_key . md5( $name . $profile_id . $start_date . $end_date . $filter ) );
				$is_custom_api = $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced' );

				if ( 'on' !== $is_custom_api ) {
					// if exception, return if the cache result else return the error.
					if ( $exception = get_transient( 'analytify_quota_exception' ) ) {
						return $this->tackle_exception( $exception, $cache_result );
					}
				}

				// if custom keys set. Fetch fresh result always.
				if ( 'on' === $is_custom_api || $cache_result === false ) {
					$result = $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );
					set_transient( $transient_key . md5( $name . $profile_id . $start_date . $end_date . $filter ) , $result, $this->cache_timeout );
					return $result;

				} else {
					return $cache_result;
				}

			} catch ( Analytify_Google_Service_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					echo "<div class='wp_analytify_error_msg'>";
					echo sprintf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					echo "</div>";
				}
			} catch ( Analytify_Google_Auth_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					echo "<div class='wp_analytify_error_msg'>";
					echo sprintf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . ' title="Reset">', '</a>', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
					echo "</div>";
				}
			} catch ( Analytify_Google_IO_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
					echo "<div class='wp_analytify_error_msg'>";
					echo sprintf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					echo "</div>";
				}
			}

		}

		/**
		 * This function grabs the data from Google Analytics
		 * For dashboard.
		 */
		public function pa_get_analytics_dashboard( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false, $name = '' ) {


			try {

				//$this->service = new Analytify_Google_Service_Analytics( $this->client );
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

				// $profile_id = get_option("pt_webprofile_dashboard");
				$profile_id = $this->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );

				if ( ! $profile_id ) {
					return false;
				}

				$transient_key = 'analytify_transient_';

				$is_custom_api = $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced' );
				$cache_result = get_transient( $transient_key . md5( $name . $profile_id . $start_date . $end_date . $filter ) );

				if ( 'on' !== $is_custom_api ) {
					// if exception, return if the cache result else return the error.
					if ( $exception = get_transient( 'analytify_quota_exception' ) ) {
						return $this->tackle_exception( $exception, $cache_result );
					}
				}

				// if custom keys set. Fetch fresh result always.
				if ( 'on' === $is_custom_api || $cache_result === false ) {
					$result = $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );
					set_transient( $transient_key . md5( $name . $profile_id . $start_date . $end_date . $filter ) , $result, $this->cache_timeout );
					return $result;

				} else {
					return $cache_result;
				}


			} catch ( Analytify_Google_Service_Exception $e ) {

				$logger = analytify_get_logger();
				$logger->warning( $e->getMessage(), array( 'source' => 'analytify_fetch_data' ) );

				set_transient( 'analytify_quota_exception', $e->getMessage(), HOUR_IN_SECONDS );

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {
				  $error_code = $e->getErrors();
				  if ( $error_code[0]['reason'] == 'userRateLimitExceeded' ) {
				    echo $this->show_error_box( 'API error: User Rate Limit Exceeded <a href="https://analytify.io/user-rate-limit-exceeded-guide" target="_blank" class="error_help">help?</a>' );
				  } elseif( $error_code[0]['reason'] == 'dailyLimitExceeded' ) {
						echo $this->show_error_box( 'API error: Daily Limit Exceeded <a href="https://analytify.io/daily-limit-exceeded" target="_blank" class="error_help">help?</a>' );
					} else{
				    echo $this->show_error_box( $e->getMessage() );
				  }
				}
			} catch ( Analytify_Google_Auth_Exception $e ) {

				$logger = analytify_get_logger();
				$logger->warning( $e->getMessage(), array( 'source' => 'analytify_fetch_data' ) );

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . ' title="Reset">', '</a>', '<i>', '</i>', esc_html( $e->getMessage() ) );
				}
			} catch ( Analytify_Google_IO_Exception $e ) {

				$logger = analytify_get_logger();
				$logger->warning( $e->getMessage(), array( 'source' => 'analytify_fetch_data' ) );

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
				}
			}
		}



		/**
		 * This function grabs the data from Google Analytics For dashboard.
		 *
		 * @param  [string] $profile    Google Analytic Profile Id.
		 * @param  [string] $metrics    Metrics.
		 * @param  [string] $start_date Start date of stats.
		 * @param  [string] $end_date   End date of stats.
		 * @param  [string] $dimensions Dimensions.
		 * @param  [string] $sort       Sort.
		 * @param  [string] $filter     Filter.
		 * @param  [string] $limit      How many stats to show.
		 * @return [array]             Return array of stats
		 */
		public function wpa_get_analytics( $profile, $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false ) {

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

				if ( 'single' == $profile ) {
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
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
				}
			} catch ( Analytify_Google_Auth_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . ' title="Reset">', '</a>', '<i>', '</i>', esc_textarea( $e->getMessage() ) );
				}
			} catch ( Analytify_Google_IO_Exception $e ) {

				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					echo sprintf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
				}
			}

		}


		/**
		 * @param mixed $return Value to be returned as response.
		 */
		function end_ajax( $return = false ) {

			$return = apply_filters( 'wpanalytify_before_response', $return );
			echo ( false === $return ) ? '' : $return;
			exit;
		}

		function check_ajax_referer( $action ) {

			$result = check_ajax_referer( $action, 'nonce', false );

			if ( false === $result ) {
				$return = array( 'wpanalytify_error' => 1, 'body' => sprintf( __( 'Invalid nonce for: %s', 'wp-analytify' ), $action ) );
				$this->end_ajax( json_encode( $return ) );
			}

			$cap = ( is_multisite() ) ? 'manage_network_options' : 'export';
			$cap = apply_filters( 'wpanalytify_ajax_cap', $cap );

			if ( ! current_user_can( $cap ) ) {
				$return = array( 'wpanalytify_error' => 1, 'body' => sprintf( __( 'Access denied for: %s', 'wp-analytify' ), $action ) );
				$this->end_ajax( json_encode( $return ) );
			}
		}


		/**
		* Returns the function name that called the function using this function.
		*
		* @return string
		*/
		function get_caller_function() {
			list( , , $caller ) = debug_backtrace( false );

			if ( ! empty( $caller['function'] ) ) {
				$caller = $caller['function'];
			} else {
				$caller = '';
			}

			return $caller;
		}

		/**
		 * Sets $this->state_data from $_POST, potentially un-slashed and sanitized.
		 *
		 * @param array  $key_rules An optional associative array of expected keys and their sanitization rule(s).
		 * @param string $context   The method that is specifying the sanitization rules. Defaults to calling method.
		 *
		 * @since 2.0
		 * @return array
		 */
		function set_post_data( $key_rules = array(), $context = '' ) {

			if ( defined( 'DOING_WPANALYTIFY_TESTS' ) ) {
				$this->state_data = $_POST;
			} elseif ( is_null( $this->state_data ) ) {
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
		* [no_records description].
		*/
		function no_records() {
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
		function get_exception() {
			return $this->exception;
		}

		/**
		 * Set Exception value.
		 *
		 * @since 2.1.22
		 */
		function set_exception( $exception ) {
			$this->exception = $exception;
		}

		/**
		* This function grabs the data from Google Analytics
		* For dashboard.
		*/
		public function pa_get_analytics_dashboard_via_rest( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false, $name = '' ) {

			try {

				//$this->service = new Analytify_Google_Service_Analytics( $this->client );
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

				// $profile_id = get_option("pt_webprofile_dashboard");
				$profile_id = $this->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );

				if ( ! $profile_id ) {
					return false;
				}

				$is_custom_api = $this->settings->get_option( 'user_advanced_keys', 'wp-analytify-advanced' );
				$cache_result = get_transient( md5( $name . $profile_id . $start_date . $end_date . $filter ) );

				if ( 'on' !== $is_custom_api ) {

					// if exception, return if the cache result else return the error.
					if ( $exception = get_transient( 'analytify_quota_exception' ) ) {
						if ( $cache_result ) {
							return $cache_result;
						}

						return array( 'api_error' => $this->show_error_box( $exception ) );
					}
				}

				// if custom keys set. Fetch fresh result always.
				if ( 'on' === $is_custom_api || $cache_result === false ) {
					$result = $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );
					set_transient( md5( $name . $profile_id . $start_date . $end_date . $filter ) , $result, $this->cache_timeout );
					return $result;

				} else {
					return $cache_result;
				}


			} catch ( Analytify_Google_Service_Exception $e ) {

				set_transient( 'analytify_quota_exception', $e->getMessage(), HOUR_IN_SECONDS );
				$logger = analytify_get_logger();
				$logger->warning( $e->getMessage(), array( 'source' => 'analytify_fetch_data' ) );
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

				  $error_code = $e->getErrors();
					$error = "<div class=\"analytify-stats-error-msg\">
					<div class=\"wpb-error-box\">
					<span class=\"blk\">
					<span class=\"line\"></span>
					<span class=\"dot\"></span>
					</span>
					<span class=\"information-txt\">";
					if ( $error_code[0]['reason'] == 'userRateLimitExceeded'  ) {
						$error .= 'API error: User Rate Limit Exceeded <a href="https://analytify.io/user-rate-limit-exceeded-guide" target="_blank" class="error_help">help</a>';
					} elseif( $error_code[0]['reason'] == 'dailyLimitExceeded' ) {
						$error .= 'API error: Daily Limit Exceeded <a href="https://analytify.io/daily-limit-exceeded" target="_blank" class="error_help">help?</a>';
					} else{
						$error .= $e->getMessage();
					}
					$error .= "</span>
					</div>
					</div>";

					return array( 'api_error' => $error ) ;

				}
			} catch ( Analytify_Google_Auth_Exception $e ) {

				$logger = analytify_get_logger();
				$logger->warning( $e->getMessage(), array( 'source' => 'analytify_fetch_data' ) );
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					$error = sprintf( esc_html__( '%1$s Oops, Try to %3$s Reset %4$s Authentication. %2$s %7$s %2$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<a href=' . esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . ' title="Reset">', '</a>', '<i>', '</i>', esc_html( $e->getMessage() ) );
					return array( 'api_error' => $error ) ;

				}
			} catch ( Analytify_Google_IO_Exception $e ) {

				$logger = analytify_get_logger();
				$logger->warning( $e->getMessage(), array( 'source' => 'analytify_fetch_data' ) );
				// Show error message only for logged in users.
				if ( current_user_can( 'manage_options' ) ) {

					$error = sprintf( esc_html__( '%1$s Oops! %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
					return array( 'api_error' => $error ) ;

				}
			}
		}

		/**
		 * Generate the Error box.
		 *
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
		 * @since 2.1.23
		 */
		function tackle_exception ( $exception, $cache_result ) {
			if ( $cache_result ) {
				return $cache_result;
			}

			echo $this->show_error_box( $exception );
		}



		/**
		 * Set Cache time for Stats.
		 *
		 * @since 2.2.1
		 */
		function set_cache_time() {
			$this->cache_timeout = $this->get_cache_time();
		}

		/**
		 * Get Cache time for Stats.
		 *
		 * @since 2.2.1
		 */
		function get_cache_time() {

			// if cache is on set cache time to 10hours else 24hours.
			$cache_time = $this->settings->get_option( 'delete_dashboard_cache','wp-analytify-dashboard','off' ) === 'on' ?  60 * 60 * 10 :  60 * 60 * 24;

			if ( 'on' == $this->settings->get_option( 'user_advanced_keys','wp-analytify-advanced' ) ) {
				$cache_time = apply_filters( 'analytify_stats_cache_time', $cache_time );
			}

			return $cache_time;

		}

		/**
		 * Check the active/deactive state of addon/moudle.
		 * 
		 * @param string $slug Slug of addon/moudle 
		 * @return string $addon_state: active or deactive
		 */
		public function analytify_module_state( $slug ) {

			$WP_ANALYTIFY = $GLOBALS['WP_ANALYTIFY'];
			$addon_state = '';

			$pro_inner = [
				'detail-realtime',
				'detail-demographic',
				'search-terms'
			];
			$pro_addon = [
				'wp-analytify-woocommerce',
				'wp-analytify-goals',
				'wp-analytify-authors',
				'wp-analytify-edd',
				'wp-analytify-forms',
				'wp-analytify-campaigns'
			];
			$pro_features = [
				'custom-dimensions',
				'events-tracking'
			];

			if ( in_array( $slug, $pro_features ) ) {
				$analytify_modules = get_option( 'wp_analytify_modules' );

				if ( 'active' === $analytify_modules[$slug]['status'] ) {
					$addon_state = 'active';
				}

				$addon_state = 'deactive';

			} elseif ( in_array( $slug, $pro_addon ) || in_array( $slug, $pro_inner ) ) {

				if ( in_array( $slug, $pro_inner ) ) {
					$slug = 'wp-analytify-pro';
				}

				if ( $WP_ANALYTIFY->addon_is_active( $slug ) ) {
					$addon_state = 'active';
				}

				$addon_state = 'deactive';
			}

			return $addon_state;
		}

		/**
		 * Check if external addon is active.
		 * 
		 * @param string $slug Slug of addon 
		 * 
		 * @return bool $addon_active
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
					if ( class_exists( 'WP_Analytify_Woocommerce' ) ) {
						$addon_active = true;
					}
					break;

				case 'wp-analytify-campaigns':
					if ( class_exists( 'ANALYTIFY_PRO_CAMPAINGS' ) ) {
						$addon_active = true;
					}
					break;

				case 'wp-analytify-authors':
					if ( class_exists( 'Analytify_Authors' ) ) {
						$addon_active = true;
					}
					break;

				case 'wp-analytify-edd':
					if ( class_exists( 'WP_Analytify_Edd' ) ) {
						$addon_active = true;
					}
					break;

				case 'wp-analytify-forms':
					if ( class_exists( 'Analytify_Forms' ) ) {
						$addon_active = true;
					}
					break;

				case 'wp-analytify-pro':
					if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
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
		 * Create dashboard navigation anchors.
		 * 
		 * @param array $nav_item Single navigation item data array.
		 * 
		 * @return mixed $anchor
		 */
		private function navigation_anchors( array $nav_item ) {
			
			$current_screen = get_current_screen()->base;
			$current_addon_name = '';

			// Check if child dashboard page for addon/module.
			if ( isset( $_GET['addon'] ) ) {
				$current_addon_name = $_GET['addon'];
			} elseif ( isset( $_GET['show'] ) ) {
				$current_addon_name = $_GET['show'];
			}

			if ( 'pro_feature' === $nav_item['module_type'] ) {
				// Module availbe in pro version as switchable feature.

				$nav_link = $this->addon_is_active( 'wp-analytify-pro' ) && 'active' === $this->modules[ $nav_item['addon_slug'] ]['status'] ? admin_url( 'admin.php?page=' . $nav_item['page_slug'] ) : admin_url( 'admin.php?page=analytify-promo&addon=' . $nav_item['addon_slug'] );
				$active_tab = ( 'analytify_page_' . $nav_item['page_slug'] === $current_screen || $nav_item['addon_slug'] === $current_addon_name ) ? 'nav-tab-active' : '';
			
			} elseif ( 'pro_inner' === $nav_item['module_type'] ) {
				// Module build in pro version.

				$nav_link = $this->addon_is_active( 'wp-analytify-pro' ) ? admin_url( 'admin.php?page=' . $nav_item['page_slug'] .'&show=' . $nav_item['addon_slug'] ) : admin_url( 'admin.php?page=analytify-promo&addon=' . $nav_item['addon_slug'] );
				$active_tab = ( 'analytify_page_' . $nav_item['page_slug'] === $current_screen || $nav_item['addon_slug'] === $current_addon_name ) ? 'nav-tab-active' : '';
			
			} elseif ( 'pro_addon' === $nav_item['module_type'] ) {
				// Not inner module, rather a seperate plugin.

				$nav_link = $this->addon_is_active( $nav_item['addon_slug'] ) ? admin_url( 'admin.php?page=' . $nav_item['page_slug'] ) : admin_url( 'admin.php?page=analytify-promo&addon=' . $nav_item['addon_slug'] );
				$active_tab = ( 'analytify_page_' . $nav_item['page_slug'] === $current_screen || $nav_item['addon_slug'] === $current_addon_name ) ? 'nav-tab-active' : '';
			
			} elseif ( 'free' === $nav_item['module_type'] ) {
				// Free version main dashboard page.
				
				$nav_link = admin_url( 'admin.php?page='. $nav_item['page_slug'] );
				$active_tab = ( 'toplevel_page_' . $nav_item['page_slug'] === $current_screen && empty( $current_addon_name ) ) ? 'nav-tab-active' : '';

			}

			$anchor = '<a href="' . esc_url( $nav_link ) . '" class="analytify_nav_tab ' . $active_tab. '">' . $nav_item['name'];
			$anchor .= (isset($nav_item['sub_name']) AND !empty($nav_item['sub_name'])) ? '<span>'.$nav_item['sub_name'].'</span>' : '';
			$anchor .= '</a>';

			return $anchor;
		}

		/**
		 * Generate dashboard navigation markup.
		 * 
		 * @param array $nav_items Navigation items data array.
		 */
		private function navigation_markup( array $nav_items ) {
			if ( is_array( $nav_items ) && 0 < count( $nav_items ) ) {
				echo '<div class="analytify_nav_tab_wrapper nav-tab-wrapper">';
				echo $this->generate_submenu_markup( $nav_items, 'analytify_nav_tab_wrapper', 'analytify_nav_tab_parent' );
				echo '</div>';
			}
		}

		/**
		 * Create HTML markup for navigation on dashboard.
		 * 
		 * @param array $nav_items Navigation items data array.
		 * @param string $wrapper_classes Class attribute for navigation wrapper.
		 * @param string $list_item_classes Class attribute for list item.
		 * 
		 * @return mixed $markup
		 */
		private function generate_submenu_markup( array $nav_items, $wrapper_classes = false, $list_item_classes = false ) {

			// Hide tabs filter.
			$hide_tabs = apply_filters( 'analytify_hide_dashboard_tabs', array() );
			
			// Wrapper
			$markup = '<ul';
			$markup .= $wrapper_classes ? ' class="'.$wrapper_classes.'"' : '';
			$markup .= '>';

			// Loop over all the menu items
			foreach ( $nav_items as $items ) {

				// Exclude hidden tabs from dashboard as in filter.
				if ( $hide_tabs && in_array( $items['name'], $hide_tabs ) ) {
					continue;
				}

				$markup .= '<li';
				$markup .= $list_item_classes ? ' class="'.$list_item_classes.'"' : '';
				$markup .= '>';

				// generate anchor
				$markup .= $this->navigation_anchors( $items );
				
				// check if the menu has children, then call itself to generate the child menu
				if ( isset( $items['children'] ) && is_array( $items['children'] ) ) {
					$markup .= $this->generate_submenu_markup( $items['children'] );
				}

				$markup .= '</li>';
			}

			// End wrapper
			$markup .= '</ul>';

			return $markup;
		}

		/**
		 * Register dashboard navigation menu.
		 * 
		 */
		function dashboard_navigation() {

			$nav_items = array(

				array(
					'name'			=> 'Audience',
					'sub_name'		=> 'Overview',
					'page_slug'		=> 'analytify-dashboard',
					'addon_slug'	=> 'wp-analytify',
					'module_type'	=> 'free',
				),

				array(
					'name'			=> 'Conversions',
					'sub_name'		=> 'All Events',
					'page_slug'		=> 'analytify-forms',
					'addon_slug'	=> 'wp-analytify-forms',
					'module_type'	=> 'pro_addon',
					'children' 		=> array(
						array(
							'name'			=> 'Forms Tracking',
							'sub_name'		=> 'View Forms Analytics',
							'page_slug'		=> 'analytify-forms',
							'addon_slug'	=> 'wp-analytify-forms',
							'module_type'	=> 'pro_addon',
						),
						array(
							'name'			=> 'Events Tracking',
							'sub_name'		=> 'Affiliates, clicks and links tracking',
							'page_slug'		=> 'analytify-events',
							'addon_slug'	=> 'events-tracking',
							'module_type'	=> 'pro_feature',
						)
					)
				),

				array(
					'name'			=> 'Acquisition',
					'sub_name'		=> 'Goals, Campaigns',
					'page_slug'		=> 'analytify-campaigns',
					'addon_slug'	=> 'wp-analytify-campaigns',
					'module_type'	=> 'pro_addon',
					'children'		=> array(
						array(
							'name'			=> 'Campaigns',
							'sub_name'		=> 'UTM Overview',
							'page_slug'		=> 'analytify-campaigns',
							'addon_slug'	=> 'wp-analytify-campaigns',
							'module_type'	=> 'pro_addon',
						),
						array(
							'name'			=> 'Goals',
							'sub_name'		=> 'Overview',
							'page_slug'		=> 'analytify-goals',
							'addon_slug'	=> 'wp-analytify-goals',
							'module_type'	=> 'pro_addon',
						)
					)
				),

				array(
					'name'			=> 'Monetization',
					'sub_name'		=> 'Overview',
					'page_slug'		=> 'analytify-woocommerce',
					'addon_slug'	=> 'wp-analytify-woocommerce',
					'module_type'	=> 'pro_addon',
					'clickable'		=> true,
					'children' 		=> array(
						array(
							'name'			=> 'WooCommerce',
							'sub_name'		=> 'eCommerce Stats',
							'page_slug'		=> 'analytify-woocommerce',
							'addon_slug'	=> 'wp-analytify-woocommerce',
							'module_type'	=> 'pro_addon',
						),
						array(
							'name'			=> 'EDD',
							'sub_name'		=> 'Checkout behavior',
							'page_slug'		=> 'edd-dashboard',
							'addon_slug'	=> 'wp-analytify-edd',
							'module_type'	=> 'pro_addon',
						)
					)
				),

				array(
					'name'			=> 'Engagement',
					'sub_name'		=> 'Authors, Dimensions',
					'page_slug'		=> 'analytify-authors',
					'addon_slug'	=> 'wp-analytify-authors',
					'module_type'	=> 'pro_addon',
					'children'		=> array(
						array(
							'name'			=> 'Authors',
							'sub_name'		=> 'Authors Content Overview',
							'page_slug'		=> 'analytify-authors',
							'addon_slug'	=> 'wp-analytify-authors',
							'module_type'	=> 'pro_addon',
						),
						array(
							'name'			=> 'Demographics',
							'sub_name'		=> 'Age & Gender Overview',
							'page_slug'		=> 'analytify-dashboard',
							'addon_slug'	=> 'detail-demographic',
							'module_type'	=> 'pro_inner',
						),
						array(
							'name'			=> 'Search Terms',
							'sub_name'		=> 'On Site Searches',
							'page_slug'		=> 'analytify-dashboard',
							'addon_slug'	=> 'search-terms',
							'module_type'	=> 'pro_inner',
						),
						array(
							'name'			=> 'Dimensions',
							'sub_name'		=> 'Custom Dimensions',
							'page_slug'		=> 'analytify-dimensions',
							'addon_slug'	=> 'custom-dimensions',
							'module_type'	=> 'pro_feature',
						)
					)
				),

				array(
					'name'			=> 'Real-Time',
					'sub_name'		=> 'Live Stats',
					'page_slug'		=> 'analytify-dashboard',
					'addon_slug'	=> 'detail-realtime',
					'module_type'	=> 'pro_inner',
				)
			);

			$this->navigation_markup( $nav_items );
		}

	}

}