<?php
/*
* Plugin Name: Analytify - Google Analytics Dashboard
* Plugin URI: hhttp://wp-analytify.com/details
* Description: Analytify brings a brand new and modern feeling Google Analytics superbly integrated with WordPress Dashboard. It presents the statistics in a beautiful way under the WordPress Posts/Pages at front end, backend and in its own Dashboard. This provides Stats from Country, Referrers, Social media, General stats, New visitors, Returning visitors, Exit pages, Browser wise and Top keywords. This plugin provides the RealTime statistics in a new UI which is easy to understand and looks good.
* Version: 1.0.4
* Author: WPBrigade
* Author URI: http://wpbrigade.com/
* License: GPLv2+
* Text Domain: wp-analytify
* Min WP Version: 3.0.1
* Max WP Version: 4.1.1
* Domain Path: /lang
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

ini_set( 'include_path', dirname(__FILE__) . '/lib/' );

if ( !class_exists( 'WP_Analytify' ) ) {

    if ( !class_exists( 'Analytify_General_FREE' ) ){
            
        require_once WP_PLUGIN_DIR .'/wp-analytify/analytify-general.php';
    }

class WP_Analytify extends Analytify_General_FREE{

    public $token  = false;
    public $client = null;

    // Constructor
    function __construct() {
        
        parent::__construct();
        
        if ( !class_exists( 'Analytify_Google_Client' ) ) {

            require_once ANALYTIFY_LIB_PATH . 'Google/Client.php';
            require_once ANALYTIFY_LIB_PATH . 'Google/Service/Analytics.php';
        }

        add_action( 'plugin_action_links', array( 
                    $this,
                    'pa_plugin_links'
                ),10,2);

        add_action( 'plugin_row_meta', array( 
                    $this,
                    'analytify_plugin_row_meta'
                ),10,2);

        add_action( 'admin_enqueue_scripts', array( 
                    $this,
                    'pa_scripts'
                ));
        add_action( 'admin_enqueue_scripts', array( 
                    $this,
                    'pa_styles'
                ));
    
        
        add_action( 'wp_enqueue_scripts', array( 
                    $this,
                    'pa_front_scripts'
                ));
        add_action( 'wp_enqueue_scripts', array( 
                    $this,
                    'pa_front_styles'
                ));
    
        add_action( 'admin_menu', array(
                    $this,
                    'wpa_add_menu'
                ));

        // Insert Google Analytics Code
        if( get_option( 'analytify_code') == 1  ) {

            add_action( 'wp_head', array(
                    $this,
                    'analytify_add_analytics_code'
                ));
        }

        add_action( 'wp_ajax_nopriv_get_ajax_single_admin_analytics', array(
                    $this,
                    'get_ajax_single_admin_analytics'
                ));

        add_action( 'wp_ajax_get_ajax_single_admin_analytics', array(
                    $this,
                    'get_ajax_single_admin_analytics'
                ));

        add_action( 'wp_ajax_get_ajax_secret_keys', array(
                    $this,
                    'get_ajax_secret_keys'
                ));

        if( get_option( 'analytify_disable_front') == 0 ) {

            add_filter( 'the_content', array(
                        $this,
                        'get_single_front_analytics'
            ));        
        }

        /* 
         * load Analytics under the EDIT POST Screen
         * add action runs only for admin section and load metabox.
        */

        if ( is_admin() ) {
            add_action( 'load-post.php', array(
                        $this,
                        'load_metaboxes'
                    ));
        }

        // Show welcome message when user activate plugin.
        if ( get_option( 'pa_welcome_message' ) == 0 ) {
                
            add_action( 'admin_print_footer_scripts', array( 
                        $this, 
                        'pa_welcome_message' 
                       ) );
        }    

        /*
         * localization language file filter
         */

        add_action( 'init', array( 
                    $this, 
                    'analytify_textdomain') );

        //add_action( 'admin_footer', array( &$this, 'profile_warning' ) );

        register_activation_hook( __FILE__,   array( $this, 'install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
    }

    /*
     * Set plugin language/localizations files directory
     */
    public function analytify_textdomain() {

        $plugin_dir = basename( dirname(__FILE__) );
        load_plugin_textdomain( 'wp-analytify', false , $plugin_dir . '/lang/');

    }

    /*
     * Show analytics sections under the posts/pages in the metabox.
     */

    function load_metaboxes() {

        add_action( 'add_meta_boxes', array(
                    $this,
                    'show_admin_single_analytics_add_metabox'
        ));
    }

    /* 
     * Show metabox under each Post type to display Analytics of single post/page in wp-admin
     */
    public function show_admin_single_analytics_add_metabox() {

        $post_types = get_option( 'analytify_posts_stats' );

        // Don't load boxes/sections if no any post type is selected.
        if( !empty($post_types))
            foreach ( $post_types as $post_type ) {
                    
                add_meta_box('pa-single-admin-analytics', // $id
                        'Analytify: Google Analytics of this page.', // $title
                        array(
                            'WP_Analytify',
                            'show_admin_single_analytics'
                        ), // $callback
                        
                        $post_type, // $posts
                        'normal',   // $context
                        'high'      // $priority
                    ); 
            } //$post_types as $post_type
    }


    public static function get_ajax_secret_keys(){

        $response = wp_remote_get( "http://wp-analytify.com/secret/keys.json" );
        if( is_wp_error( $response ) ) {
           $error_message = $response->get_error_message();
           echo "Something went wrong: $error_message";
        } else {
           print_r($response['body']);
        }
        die();
    }

    /* 
     * Show Analytics of single post/page in wp-admin under EDIT screen.
     */
    public static function show_admin_single_analytics() {

        global $post;

        $back_exclude_posts = explode( ',', get_option( 'post_analytics_exclude_posts_back' ));

        if ( is_array( $back_exclude_posts ) ) {
                        
            if ( in_array( $post->ID, $back_exclude_posts ) ) {
                            
                _e('This post is excluded and will not show Analytics.');
                            
                return;
            }
        }
            
        $urlPost = '';
        $wp_analytify  = new WP_Analytify();
        $urlPost = parse_url( get_permalink( $post->ID ) );
        $start_date = ''; $end_date = ''; $urlpost = '' ;
        $is_access_level = get_option( 'post_analytics_access_back' );
            
        if( $wp_analytify->pa_check_roles( $is_access_level ) ){ ?>

            <div class="pa-filter">
                <table cellspacing="0" cellpadding="0" width="400">
                    <tbody>
                        <tr>
                            <td width="0">
                                <input type="text" id="start_date" name="start_date">
                            </td>
                            <td width="0">
                                <input type="text" id="end_date" name="end_date">
                            </td>
                                <input type="hidden" name="urlpost" id="urlpost" value="<?php echo $urlPost['path']; ?>">
                            <td width="0">
                                <input type="button" id="view_analytics" name="view_analytics" value="View Analytics" class="button-primary btn-green">
                            </td>
                       </tr>
                    </tbody>
                </table>
            </div>
            <div class="loading" style="display:none">
                <img src="<?php echo plugins_url('images/loading.gif', __FILE__);?>">
            </div>
            <div class="show-hide">
                    <?php $wp_analytify->get_single_admin_analytics( $start_date, $end_date, $post->ID, 0 ); ?>
            </div>
            <?php
        }
        else{
            echo _e( 'You are not allowed to see stats', 'wp-analytify' );
        }
    }

    // Add Google Analytics JS code
    public function analytify_add_analytics_code() {
   
        global $current_user;

        $roles = $current_user->roles;

        if ( isset( $roles[0] ) and in_array( $roles[0], get_option( 'display_tracking_code' ) )) {

        }
        else{

            echo '<!-- This code is added by Analytify v - ' . ANALYTIFY_VERSION . ' http://wp-analytify.com/ !--> ';

            if ( get_option( 'analytify_tracking_code' ) == 'universal' ) { ?>

                <script>
                      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
                      ga('create', '<?php echo get_option( "webPropertyId" );?>', 'auto');
                      ga('send', 'pageview');
                </script>

            <?php 
            }

            if ( get_option( 'analytify_tracking_code' ) == 'ga' ) { ?>
                
                <script type="text/javascript">//<![CDATA[
                    var _gaq = _gaq || [];
                    _gaq.push(['_setAccount', '<?php echo get_option( "webPropertyId" );?>']);
                    _gaq.push(['_trackPageview']);
                    (function () {
                        var ga = document.createElement('script');
                        ga.type = 'text/javascript';
                        ga.async = true;
                        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(ga, s);
                    })();
                    //]]>
                </script>

            <?php
            }

            echo '<!-- This code is added by Analytify  v - ' . ANALYTIFY_VERSION . ' !-->';
        }
    }

    /**
    * Add a link to the settings page to the plugins list
    */
    public function pa_plugin_links( $links, $file ) {

            static $this_plugin;
            
            if ( empty( $this_plugin ) ){ 
                
                $this_plugin = 'wp-analytify/wp-analytify.php';
            }

            if ( $file == $this_plugin ) {
                
                $settings_link = '<a href="' . admin_url("admin.php?page=analytify-settings") . '">' . __( 'Settings', 'wp-analytify' ) . '</a> | <a href="' . admin_url("admin.php?page=analytify-dashboard") . '">' . __( 'Dashboard', 'wp-analytify' ) . '</a>';
                array_unshift( $links, $settings_link );
            }
            
            return $links;
    }

    /**
     * Plugin row meta links
     *
     * @since 1.1
     * @param array $input already defined meta links
     * @param string $file plugin file path and name being processed
     * @return array $input
     */
    function analytify_plugin_row_meta( $input, $file ) {
        if ( $file != 'wp-analytify/wp-analytify.php' )
            return $input;

        $links = array(
            '<a href="http://wp-analytify.com/">' . esc_html__( 'Buy Pro Version', 'edd' ) . '</a>',
            '<a href="http://wp-analytify.com/add-ons/">' . esc_html__( 'Add Ons', 'edd' ) . '</a>',
        );

        $input = array_merge( $input, $links );

        return $input;
    }


    /**
     * Display warning if profiles are not selected.
     */
    public function pa_check_warnings(){
            
        add_action( 'admin_footer', array( 
                    &$this, 
                    'profile_warning' 
                ));
    }

    /**
     * Get current screen details
     */
    public static function pa_page_file_path() {
    
        $screen = get_current_screen();

        if ( strpos( $screen->base, 'analytify-settings' ) !== false ) {
            include( ANALYTIFY_ROOT_PATH . '/inc/analytify-settings.php' );
        } 
        else {
            include( ANALYTIFY_ROOT_PATH . '/inc/analytify-dashboard.php' );
        }
    }

    /**
     * Styling: loading stylesheets for the plugin.
     */
    public function pa_styles( $page ) {
            
            wp_enqueue_style( 'wp-analytify-style', plugins_url('css/wp-analytify-style.css', __FILE__));

           // wp_enqueue_style( 'ui-css', plugins_url('jquery-ui-theme/jquery-ui.css', __FILE__));
            wp_enqueue_style( 'chosen', plugins_url('css/chosen.css', __FILE__));
           // wp_enqueue_style( 'jquery-ui-tooltip-css', plugins_url('css/jquery.ui.tooltip.html.css', __FILE__) );

            if ( get_option( 'pa_welcome_message' ) == '0' ) {
                
                wp_enqueue_style( 'wp-pointer' );
            
            }
    }

    public function pa_front_styles( $page ) {
        
        if( get_option( 'analytify_disable_front') == 0 ) {

            wp_enqueue_style( 'front-end-style', plugins_url('css/frontend_styles.css', __FILE__),false,ANALYTIFY_VERSION);            
        }
    }

    /**
     * Loading scripts js for the plugin.
     */
    public function pa_scripts( $page ) {

        wp_enqueue_script ( 'jquery' );
        wp_enqueue_script ( 'charts_api_js', 'https://www.google.com/jsapi', false, ANALYTIFY_VERSION );
        wp_enqueue_script ( 'chosen-js', plugins_url('js/chosen.jquery.js', __FILE__), false, ANALYTIFY_VERSION );
        wp_enqueue_script ( 'script-js', plugins_url('js/wp-analytify.js', __FILE__), false, ANALYTIFY_VERSION );
        wp_enqueue_script ( 'jquery-ui-tooltip' );
        wp_enqueue_script ( 'jquery-ui-datepicker');
            
        if ( get_option( 'pa_welcome_message' ) == '0' ) {
             
            wp_enqueue_script( 'wp-pointer' );
        }   
    }

    /**
     * Loading scripts js for the plugin.
     */
    public function pa_front_scripts( $page ) {
        
        if( get_option( 'analytify_disable_front') == 0 ){

            wp_enqueue_script ('jquery');
            wp_enqueue_script ('analytify-classie', plugins_url('js/classie.js', __FILE__), false, ANALYTIFY_VERSION);
            wp_enqueue_script ('analytify-selectfx', plugins_url('js/selectFx.js', __FILE__), false, ANALYTIFY_VERSION);
            wp_enqueue_script ('analytify-script', plugins_url('js/script.js', __FILE__), false, ANALYTIFY_VERSION);

        }
    }

    /** 
     * Create Analytics menu at the left side of dashboard
     */
    public static function wpa_add_menu() {

        add_menu_page( ANALYTIFY_NICK, 'Analytify', 'manage_options', 'analytify-dashboard', array(
                          __CLASS__,
                         'pa_page_file_path'
                        ), plugins_url('images/wp-analytics-logo.png', __FILE__),'2.1.9');

            add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . ' Dashboard', ' Dashboard', 'manage_options', 'analytify-dashboard', array(
                              __CLASS__,
                             'pa_page_file_path'
                            ));
            
            add_submenu_page( 'analytify-dashboard', ANALYTIFY_NICK . ' Settings', '<b style="color:#f9845b">Settings</b>', 'manage_options', 'analytify-settings', array(
                              __CLASS__,
                             'pa_page_file_path'
                            ));
    }

    /** 
     * Creating tabs for settings
     * @since 1.0
     */

    public function pa_settings_tabs( $current = 'authentication' ) {
            
            $tabs = array( 'authentication' =>  'Authentication', 
                            'profile'       =>  'Profile',
                            
                            'admin'         =>  'Admin'
                    );

            echo '<div class="left-area">';

            echo '<div id="icon-themes" class="icon32"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';

            foreach( $tabs as $tab => $name ) {

                $class = ( $tab == $current ) ? ' nav-tab-active' : '';
                echo "<a class='nav-tab$class' href='?page=analytify-settings&tab=$tab'>$name</a>";
            }

            echo '</h2>';
    }

    /**
     * Get profiles from user Google Analytics account profiles.
     */
    public function pt_get_analytics_accounts() {

            try {

                if( get_option( 'pa_google_token' ) !='' ) {
                    $profiles = $this->service->management_profiles->listManagementProfiles( "~all", "~all" );
                    return $profiles;
                }
                
                else{
                    echo '<br /><p class="description">' . __( 'You must authenticate to access your web profiles.', 'wp-analytify' ) . '</p>';
                }

            }
            
            catch (Exception $e) {
                die('An error occured: ' . $e->getMessage() . '\n');
            }
    }

    public function pa_setting_url() {
        
        return admin_url('admin.php?page=analytify-settings');
    
    }


    public function pt_save_data( $key_google_token ) {

        update_option( 'post_analytics_token', $key_google_token );
        $this->pa_connect();

        return true;
    }

    /**
     * Warning messages.
     */
    public function profile_warning() {

            $profile_id     =   get_option( "pt_webprofile" );
            $acces_token    =   get_option( "post_analytics_token" );

            if (! isset( $acces_token ) || empty( $acces_token )) {
               
               echo "<div id='message' class='error'><p><strong>" . __( "Analytify is not active. Please <a href='" . menu_page_url ( 'analytify-settings', false ) ."'>Authenticate</a> in order to get started using this plugin.", 'wp-analytify' )."</p></div>"; 
            }
            else{
                
                if (! isset( $profile_id ) || empty( $profile_id )){
                    echo '<div class="error"><p><strong>' . __( 'Google Analytics Profile is not set. Set the <a href="' . menu_page_url ( 'analytify-settings', false ) . '&tab=profile">Profile</a> ', 'wp-analytify' ) . '</p></div>';
                }
            }
    }


    public function get_single_front_analytics( $content ) {
        
        global $post, $wp_analytify;

        $front_access = get_option( 'post_analytics_access' );

        if ( is_single() || is_page() ) {
            
            $post_type = get_post_type( $post->ID );
            
            if( strlen( get_option( 'analytify_posts_stats_front' ) < 3) ) {
                return $content;
            }

            if( is_array( get_option( 'analytify_posts_stats_front' )) and !in_array( $post_type, get_option( 'analytify_posts_stats_front' ) ) ) {

                return $content;
            }

            if ( is_array( get_option( 'post_analytics_exclude_posts_front' ) ) ) {
                    
                if ( in_array( get_the_ID(), get_option( 'post_analytics_exclude_posts_front' ) ) ) {
                            
                    return $content;
                }
            }

            // Showing stats to guests
            if ( $front_access[0] == 'every-one' || $this->pa_check_roles( $front_access )) {
                
                $post_analytics_settings_front = array();
                $post_analytics_settings_front = get_option( 'post_analytics_settings_front' );
              
                $urlPost =  parse_url( get_permalink( $post->ID ) );
            
                if ( $urlPost['host'] == 'localhost' ) 
                    $filter = 'ga:pagePath==/'; //.$u_post['path'];
                else 
                    $filter = 'ga:pagePath==' .$urlPost['path']. '';  

                if( get_the_time('Y', $post->ID) < 2005 ) {

                    $start_date = '2005-01-01';
                }
                else {

                     $start_date = get_the_time('Y-m-d', $post->ID);   
                }

                $end_date = date('Y-m-d');
                
                ob_start();

                include ANALYTIFY_ROOT_PATH . '/inc/front-menus.php';

                if(! empty( $post_analytics_settings_front )){

                        if (is_array( $post_analytics_settings_front )){

                            $stats = $this->pa_get_analytics( 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users',$start_date, $end_date, false, false, $filter);

                            if ( isset( $stats->totalsForAllResults ) ) {

                                include ANALYTIFY_ROOT_PATH . '/views/front/general-stats.php'; 
                                pa_include_general( $this, $stats);
                            }
                        }
                }
                      
                $content .= ob_get_contents();
                ob_get_clean();
            }

        }
        
        return $content;

    }

    /*
     * get the Analytics data from ajax() call
     * 
     */

    public function get_ajax_single_admin_analytics() {

        $startDate = '';
        $endDate   = '';
        $postID    = 0 ;
        $startDate = $_POST['start_date'];
        $endDate   = $_POST['end_date'];
        $postID    = $_POST['post_id'];
        $this->get_single_admin_analytics( $startDate, $endDate, $postID, 1 );

        die();
    }

    /*
     * get the Analytics data for wp-admin posts/pages.
     * 
     */
    public function get_single_admin_analytics( $start_date = '', $end_date = '', $postID = 0, $ajax = 0 ) {

        global $post;
            
            //$urlPost = parse_url( get_permalink( $post->ID ) );
            
        if ( $postID == 0 ) {
            $u_post = '/'; //$urlPost['path'];
        }
        else{
            $u_post = parse_url( get_permalink( $postID ) );
        }

        if ( $u_post['host'] == 'localhost' ) 
            $filter = false;
        else
           $filter = 'ga:pagePath==' .$u_post['path']. '';


        if ( $start_date == '' ) {
            
            $s_date = get_the_time('Y-m-d', $post->ID);
            if(get_the_time('Y', $post->ID) < 2005){
                $s_date = '2005-01-01';
            }
        }
        else{
            $s_date = $start_date;
        }

        if ( $end_date == '' ) {
            $e_date = date('Y-m-d');
        }   
        else{
                $e_date = $end_date;
        }

        $show_settings = array();
        $show_settings = get_option('post_analytics_settings_back');

        // Stop here, if user has disable backend analytics i.e OFF
        if ( get_option( 'post_analytics_disable_back' ) == 1 and $ajax == 0) {
            return;
        }

        echo '<p> Displaying Analytics of this page from ' . date("jS F, Y", strtotime($s_date)) . ' to '. date("jS F, Y", strtotime($e_date)) . '</p>';

        if( !empty( $show_settings )){

            if (is_array( $show_settings )){
                    
                if (in_array( 'show-overall-back', $show_settings )) {
                       
                    $stats = $this->pa_get_analytics( 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users',$s_date, $e_date, false, false, $filter);

                    if ( isset( $stats->totalsForAllResults ) ) {

                        include ANALYTIFY_ROOT_PATH . '/views/admin/single-general-stats.php'; 
                        wpa_single_include_general( $this, $stats);                      
                    }
                }
            }
        }
    }

    
    /*
     * Pretty numbers
     */
    function wpa_pretty_numbers( $num ) {

        return round(($num/1000),2).'k';
    }

    /*
     * format numbers
     */
    function wpa_number_format( $num ) {

        return number_format($num);
    }

    /*
     * Pretty time to display
     */
    function pa_pretty_time( $time ) {

            // Check if numeric
            if ( is_numeric($time) ) {

                $value = array(
                    "years" => '00',
                    "days" => '00',
                    "hours" => '',
                    "minutes" => '',
                    "seconds" => ''
                );
                
                if ($time >= 31556926) {
                    $value["years"] = floor($time / 31556926);
                    $time           = ($time % 31556926);
                } //$time >= 31556926

                if ($time >= 86400)
                    {
                    $value["days"] = floor($time / 86400);
                    $time          = ($time % 86400);
                    } //$time >= 86400
                if ($time >= 3600)
                    {
                    $value["hours"] = str_pad(floor($time / 3600), 1, 0, STR_PAD_LEFT);
                    $time           = ($time % 3600);
                    } //$time >= 3600
                if ($time >= 60)
                    {
                    $value["minutes"] = str_pad(floor($time / 60), 1, 0, STR_PAD_LEFT);
                    $time             = ($time % 60);
                    } //$time >= 60
                    $value["seconds"] = str_pad(floor($time), 1, 0, STR_PAD_LEFT);
                # Get the hour:minute:second version
                if($value['hours']!=''){
                    $attach_hours='<sub>h</sub> ';
                }
                if($value['minutes']!=''){
                    $attach_min='<sub>min</sub> ';
                }
                if($value['seconds']!=''){
                    $attach_sec='<sub>sec</sub>';
                }
                return $value['hours'] .@$attach_hours. $value['minutes'] .@$attach_min. $value['seconds'].$attach_sec;
                //return $value['hours'] . ':' . $value['minutes'] . ':' . $value['seconds'];
                } //is_numeric($time)
            else
                {
                return false;
                }
    }


    public function pa_check_roles( $access_level ) {
            
        if ( is_user_logged_in () && isset ( $access_level ) ) {
                
            global $current_user;
            $roles = $current_user->roles;

                /*if ( ( current_user_can ( 'manage_options' ) ) ) {
                    
                    return true;
                }*/
                
            if ( in_array ( $roles[0], $access_level ) ) {
                    
                return true;
            }
            else {
                    
                return false;
            }
        }
    }

    public function pa_welcome_message() {

        $pointer_content = '<h3>Analytify - Google Analytics for WordPress.</h3>';
        $pointer_content .= '<p>Thank you for activating Analytify Plugin. Enjoy Google Analytics for everything in WordPress.</p>';
        ?>

        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {

                $('#toplevel_page_pa-dashboard').pointer({
                        content: '<?php echo $pointer_content; ?>',
                        position: 'left',
                        close: function() {
                            <?php update_option("pa_welcome_message",1) ?>
                        }
                }).pointer('open');
            });
            //]]>
        </script>
        
        <?php
    }

    /*
     * Activate options by default on installing the plugin. 
     */
    static function install() {

        update_option( 'analytify_posts_stats', array( 'post','page' ));
        update_option( 'post_analytics_disable_back'  ,   1 );
        update_option( 'analytify_code'  ,   1 );
        update_option( 'post_analytics_settings_back' , array( 'show-overall-back' ) );
        update_option( 'post_analytics_access_back'   , array( 'editor','administrator' ) );
        update_option( 'display_tracking_code'        , array( 'administrator' ) );
       

    }

    static function uninstall() {

        delete_option( 'analytify_posts_stats' );
        delete_option( 'pa_google_token' );
        delete_option( 'pa_welcome_message' );
        delete_option( 'post_analytics_token' );
            
     }

}

$wp_analytify =   new WP_Analytify();

$wp_analytify->pa_check_warnings();

} //end if

/* Display a notice that can be dismissed */

add_action('admin_notices', 'analytify_admin_notice');

function analytify_admin_notice() {
if ( current_user_can( 'install_plugins' ) )
   {
    global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'analytify_ignore_notice') ) {
        echo '<div class="updated"><p>'; 
        printf(__('<b>[Notice]</b> Thank you for using <strong><a href="https://wp-analytify.com/details" target="_blank">Analytify</a>!</strong> Do you know you could get detailed <a href="https://wp-analytify.com/details" target="_blank"><strong>Keyword Analytics</strong></a> per post, right below your <strong>Post Edit Panel</strong>?  Here is <strong>Exclusive $5 off Coupon "<em><a href="https://wp-analytify.com/upgrade-from-free" target="_blank">Analytify2015</a>"</em></strong><a href="https://wp-analytify.com/upgrade-from-free" target="_blank"><em>,</em></a> only for <strong>You</strong>, existing user. <a href="%1$s">[Hide Notice]</a>'),  admin_url( 'admin.php?page=analytify-dashboard&analytify_nag_ignore=0' ));
        echo "</p></div>";
    }
    }
}

add_action('admin_init', 'analytify_nag_ignore');

function analytify_nag_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['analytify_nag_ignore']) && '0' == $_GET['analytify_nag_ignore'] ) {
             add_user_meta($user_id, 'analytify_ignore_notice', 'true', true);
    }
}

?>

