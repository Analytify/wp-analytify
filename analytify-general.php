<?php

/**
 * Base Class to use for the Add-ons
 * It will be used to extend the functionality of Analytify WordPress Plugin.
 */

    // Setting Global Values
    define( 'ANALYTIFY_LIB_PATH', dirname(__FILE__) . '/lib/' );
    define( 'ANALYTIFY_ID', 'wp-analytify-options' );
    define( 'ANALYTIFY_NICK', 'Analytify' );
    define( 'ANALYTIFY_ROOT_PATH', dirname(__FILE__) );
    define( 'ANALYTIFY_VERSION', '1.0.1');
    define( 'ANALYTIFY_TYPE', 'FREE');
    define( 'ANALYTIFY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    define( 'ANALYTIFY_REDIRECT', 'urn:ietf:wg:oauth:2.0:oob' );  // This will redirect to window where we can copy Access code.
    define( 'ANALYTIFY_SCOPE', 'https://www.googleapis.com/auth/analytics' ); // readonly scope

    define( 'ANALYTIFY_STORE_URL', 'http://wp-analytify.com' );
    define( 'ANALYTIFY_PRODUCT_NAME', 'Analytify WordPress Plugin' );

if (! class_exists( 'Analytify_General_FREE' ) ) {

	class Analytify_General_FREE {

		function __construct() {

			if (! class_exists('Analytify_Google_Client') ) {

				require_once ANALYTIFY_LIB_PATH . 'Google/Client.php';
				require_once ANALYTIFY_LIB_PATH . 'Google/Service/Analytics.php';

		   }

			$this->client = new Analytify_Google_Client();
			$this->client->setApprovalPrompt( 'force' );
			$this->client->setAccessType( 'offline' );
			$this->client->setClientId( get_option('ANALYTIFY_CLIENTID'));
			$this->client->setClientSecret( get_option('ANALYTIFY_CLIENTSECRET') );
			$this->client->setRedirectUri( ANALYTIFY_REDIRECT );
			$this->client->setScopes( ANALYTIFY_SCOPE );
			$this->client->setDeveloperKey( get_option('ANALYTIFY_DEV_KEY') ); 

			try{
				
				$this->service = new Analytify_Google_Service_Analytics( $this->client );

				$this->pa_connect();
				
			}
			catch ( Analytify_Google_Service_Exception $e ) {
				
			}
			
		}

	    /**
	     * Connect with Google Analytics API and get authentication token and save it.
	     */

	    public function pa_connect() {
			
			$ga_google_authtoken = get_option('pa_google_token');

	        if (! empty( $ga_google_authtoken )) {
	                
	                $this->client->setAccessToken( $ga_google_authtoken );
	        } 
	        else{
	                
	        	$authCode = get_option( 'post_analytics_token' );
	                
	                if ( empty( $authCode ) ) return false;

	                try {

	                    $accessToken = $this->client->authenticate( $authCode );
	                }
	                catch ( Exception $e ) {
	                    return false;
	                }

	                if ( $accessToken ) {
	                    
	                    $this->client->setAccessToken( $accessToken );
	                    
	                    update_option( 'pa_google_token', $accessToken );
	                    
	                    return true;
	                } //$accessToken
	                else {

	                    return false;
	                }
	            }

	            $this->token = json_decode($this->client->getAccessToken());

	    	return true;
	    }

	    /*
	     * This function grabs the data from Google Analytics
	     * For individual posts/pages.
	     */
	    public function pa_get_analytics( $metrics, $startDate, $endDate, $dimensions = false, $sort = false, $filter = false, $limit = false ) {

	    	try{
				
				$this->service = new Analytify_Google_Service_Analytics($this->client);
	            $params        = array();
	           
	            if ($dimensions){
	                $params['dimensions'] = $dimensions;
	            } //$dimensions
	           
	            if ($sort){
	                $params['sort'] = $sort;
	            } //$sort
	            
	            if ($filter){
	                $params['filters'] = $filter;
	            } //$filter
	            
	            if ($limit){
	                $params['max-results'] = $limit;
	            } //$limit

	            $profile_id = get_option("pt_webprofile");

	            if (!$profile_id){
	                return false;
	            }
	            
	            return $this->service->data_ga->get('ga:' . $profile_id, $startDate, $endDate, $metrics, $params);
	        }

	        catch ( Analytify_Google_Service_Exception $e ) {

	        	// Show error message only for logged in users.
	        	if ( is_user_logged_in() ) echo $e->getMessage();

	        }
		}

	    /*
	     * This function grabs the data from Google Analytics
	     * For dashboard.
	     */
	    public function pa_get_analytics_dashboard( $metrics, $startDate, $endDate, $dimensions = false, $sort = false, $filter = false, $limit = false ) {

	    	try{

	            $this->service = new Analytify_Google_Service_Analytics( $this->client );
	            $params        = array();

	            if ($dimensions){
	                $params['dimensions'] = $dimensions;
	            }
	            if ($sort){
	                $params['sort'] = $sort;
	            } 
	            if ($filter){
	                $params['filters'] = $filter;
	            }
	            if ($limit){
	                $params['max-results'] = $limit;
	            } 
	            
	            $profile_id = get_option("pt_webprofile_dashboard");
	            if (!$profile_id){
	                return false;
	            }
	            
	            return $this->service->data_ga->get('ga:' . $profile_id, $startDate, $endDate, $metrics, $params);

	        }

	        catch ( Analytify_Google_Service_Exception $e ) {
	        	
	        	// Show error message only for logged in users.
	        	if ( is_user_logged_in() ) echo $e->getMessage();

	        }
	    }

	}
}
?>