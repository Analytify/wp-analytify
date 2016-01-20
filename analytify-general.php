<?php
/**
 * Analytify settings file.
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

/**
 * Base Class to use for the Add-ons
 * It will be used to extend the functionality of Analytify WordPress Plugin.
 */

// Setting Global Values.
define( 'ANALYTIFY_LIB_PATH', dirname( __FILE__ ) . '/lib/' );
define( 'ANALYTIFY_ID', 'wp-analytify-options' );
define( 'ANALYTIFY_NICK', 'Analytify' );
define( 'ANALYTIFY_ROOT_PATH', dirname( __FILE__ ) );
define( 'ANALYTIFY_VERSION', '1.2.5' );
define( 'ANALYTIFY_TYPE', 'FREE' );
define( 'ANALYTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANALYTIFY_CLIENTID', '958799092305-7p6jlsnmv1dn44a03ma00kmdrau2i31q.apps.googleusercontent.com' );
define( 'ANALYTIFY_CLIENTSECRET', 'Mzs1ODgJTpjk8mzQ3mbrypD3' );
define( 'ANALYTIFY_REDIRECT', 'https://wp-analytify.com/api/' );
define( 'ANALYTIFY_DEV_KEY', 'AIzaSyAn-70Vah_wB9qifJqjrOhkl77qzWhAR_w' );
define( 'ANALYTIFY_SCOPE', 'https://www.googleapis.com/auth/analytics' ); // Readonly scope.
define( 'ANALYTIFY_STORE_URL', 'http://wp-analytify.com' );
define( 'ANALYTIFY_PRODUCT_NAME', 'Analytify WordPress Plugin' );

if ( ! class_exists( 'Analytify_General_FREE' ) ) {

	/**
	 * Analytify_General_FREE Class for Analytify Free version
	 */
	class Analytify_General_FREE {

		/**
		 * __construct
		 */
		function __construct() {

			if ( ! class_exists( 'Analytify_Google_Client' ) ) {

				require_once ANALYTIFY_LIB_PATH . 'Google/Client.php';
				require_once ANALYTIFY_LIB_PATH . 'Google/Service/Analytics.php';

			}

			$this->client = new Analytify_Google_Client();
			$this->client->setApprovalPrompt( 'force' );
			$this->client->setAccessType( 'offline' );

			if ( 'Yes' === get_option( 'ANALYTIFY_USER_KEYS' ) ) {

				$this->client->setClientId( get_option( 'ANALYTIFY_CLIENTID' ) );
				$this->client->setClientSecret( get_option( 'ANALYTIFY_CLIENTSECRET' ) );
				$this->client->setRedirectUri( get_option( 'ANALYTIFY_REDIRECT_URI' ) );
				$this->client->setDeveloperKey( get_option( 'ANALYTIFY_DEV_KEY' ) );

			} else {

				$this->client->setClientId( ANALYTIFY_CLIENTID );
				$this->client->setClientSecret( ANALYTIFY_CLIENTSECRET );
				$this->client->setRedirectUri( ANALYTIFY_REDIRECT );
				$this->client->setDeveloperKey( ANALYTIFY_DEV_KEY );

			}

			$this->client->setScopes( ANALYTIFY_SCOPE );

			try {

				$this->service = new Analytify_Google_Service_Analytics( $this->client );
				$this->pa_connect();

			} catch ( Analytify_Google_Service_Exception $e ) {

	        	// Show error message only for logged in users.
	        		// Show error message only for logged in users.
	        	if ( current_user_can( 'manage_options' ) ) {

	        		echo sprintf( esc_html__( '%1$s oOps, Something went wrong. %2$s %5$s %2$s %3$s Don\'t worry, This error message is only visible to Administrators. %4$s %2$s ', 'wp-analytify' ), '<br /><br />', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
	        	}
			} catch ( Analytify_Google_Auth_Exception $e ) {

	        	// Show error message only for logged in users.
	        	if ( current_user_can( 'manage_options' ) ) {

	        		echo sprintf( esc_html__( '%1$s oOps, Try to %2$s Reset %3$s Authentication. %4$s %7$s %4$s %5$s Don\'t worry, This error message is only visible to Administrators. %6$s %4$s', 'wp-analytify' ), '<br /><br />', '<a href="'. esc_url( admin_url( 'admin.php?page=analytify-settings&tab=authentication' ) ) . '" title="Reset">', '</a>', '<br />', '<i>', '</i>', esc_html( $e->getMessage() ) );
				}
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
					return false;
				}

				if ( $access_token ) {

					$this->client->setAccessToken( $access_token );

					update_option( 'pa_google_token', $access_token );

					return true;
				} else {

					return false;
				}
			}

	        $this->token = json_decode( $this->client->getAccessToken() );

	    	return true;
	    }

	    /**
	     * [pa_get_analytics This function is used to fetch analytics from GA.]
	     * @param  string  $metrics     pass a list of metrics in string format.
	     * @param  date    $start_date  start date from.
	     * @param  date    $end_date    end date to.
	     * @param  boolean $dimensions  dimensions.
	     * @param  boolean $sort        sort.
	     * @param  boolean $filter      filter.
	     * @param  boolean $limit       limit pagination.
	     * @return string               results
	     */
	    public function pa_get_analytics( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false ) {

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

	            $profile_id = get_option( 'pt_webprofile' );

	            if ( ! $profile_id ) {
	                return false;
	            }

	            return $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );
	        } catch ( Analytify_Google_Service_Exception $e ) {

	        	// Show error message only for logged in users.
	        	if ( is_user_logged_in() ) { echo esc_html( $e->getMessage() ); }
			}
		}

	    /**
	     * [pa_get_analytics_dashboard This function is used to fetch analytics from GA to display in WordPress dashboard.]
	     * @param  string  $metrics     pass a list of metrics in string format.
	     * @param  date    $start_date  start date from.
	     * @param  date    $end_date    end date to.
	     * @param  boolean $dimensions  dimensions.
	     * @param  boolean $sort        sort.
	     * @param  boolean $filter      filter.
	     * @param  boolean $limit       limit pagination.
	     * @return string               results
	     */
	    public function pa_get_analytics_dashboard( $metrics, $start_date, $end_date, $dimensions = false, $sort = false, $filter = false, $limit = false ) {

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

	            $profile_id = get_option( 'pt_webprofile_dashboard' );
	            if ( ! $profile_id ) {
	                return false;
	            }

	            return $this->service->data_ga->get( 'ga:' . $profile_id, $start_date, $end_date, $metrics, $params );

	        } catch ( Analytify_Google_Service_Exception $e ) {

	        	// Show error message only for logged in users.
	        	if ( is_user_logged_in() ) { echo esc_html( $e->getMessage() ); }
			}
	    }
	}
}
?>
