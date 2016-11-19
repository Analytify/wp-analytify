<?php
if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_Analytify_Settings' ) ) {

	class WP_Analytify_Settings {

		/**
	     * settings sections array
	     *
	     * @var array
	     */
		protected $settings_sections = array();

	    /**
	     * Settings fields array
	     *
	     * @var array
	     */
	    protected $settings_fields = array();


	    public function __construct() {

	    	add_action( 'admin_init', array( $this, 'admin_init' ) );
	    	add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	    }

		/**
	     * Enqueue scripts and styles
	     */
		function admin_enqueue_scripts() {

			wp_enqueue_script( 'jquery' );
		}


		/**
	     * Set settings sections
	     *
	     * @param array $sections setting sections array
	     */
		function set_sections( $sections ) {
			$this->settings_sections = $sections;

			return $this;
		}

	    /**
	     * Add a single section
	     *
	     * @param array $section
	     */
	    function add_section( $section ) {
	    	$this->settings_sections[] = $section;

	    	return $this;
	    }

		/**
	     * Set settings fields
	     *
	     * @param array $fields settings fields array
	     */
		function set_fields( $fields ) {
			$this->settings_fields = $fields;

			return $this;
		}

	    /**
	     * [add_field description]
	     *
	     * @param [type] $section [description]
	     * @param [type] $field   [description]
	     */
	    function add_field( $section, $field ) {
	    	$defaults = array(
	    		'name'  => '',
	    		'label' => '',
	    		'desc'  => '',
	    		'type'  => 'text',
	    		);

	    	$arg = wp_parse_args( $field, $defaults );
	    	$this->settings_fields[ $section ][] = $arg;

	    	return $this;
	    }


		function get_settings_sections() {

		 	$tabs = array(
						array(
						 'id' => 'wp-analytify-authentication',
						 'title' => __( 'Authentication', 'wp-analytify' ),
						 'priority' => '5',
						),
						array(
						 'id' => 'wp-analytify-profile',
						 'title' => __( 'Profile', 'wp-analytify' ),
						 'desc' => 'Select your profiles for front-end and backend sections.',
						 'priority' => '10',
						),

						array(
						 'id' => 'wp-analytify-admin',
						 'title' => __( 'Admin', 'wp-analytify' ),
						 'desc'	=> 'Following are the settings for Admin side. Google Analytics will appear under the posts, custom post types or pages.',
						 'priority' => '20',
						),
						array(
							'id' => 'wp-analytify-advanced',
							'title' => __( 'Advanced', 'wp-analytify' ),
							'priority' => '35',
						),
						array(
						 'id' => 'wp-analytify-help',
						 'title' => __( 'Help', 'wp-analytify' ),
						 'priority' => '45',
						),
				 	);

			$setting_tabs = apply_filters( 'wp_analytify_pro_setting_tabs', $tabs );

			usort( $setting_tabs, function( $a, $b ) {
				return $a['priority'] - $b['priority'];
			});

			return $setting_tabs;
	    }


		/**
	     * Returns all the settings fields
	     *
	     * @return array settings fields
	     */
		function get_settings_fields() {

			$settings_fields = array(
				'wp-analytify-authentication' => array(
					array(
						'name'              => 'signin-button',
						'label'             => __( 'Text Input', 'wp-analytify' ),
						'desc'              => __( 'Text input description', 'wp-analytify' ),
						'type'              => 'text',
						'default'           => 'Title',
						'sanitize_callback' => 'intval',
						),
					),
				'wp-analytify-profile' => array(
					array(
						'name'              => 'install_ga_code',
						'label'             => __( 'Install Google Analytics tracking code', 'wp-analytify' ),
						'desc'              => __( 'Insert Google Analytics JS code in header to track the visitors. You can uncheck this option if you have already insert the GA code in your website.', 'wp-analytify' ),
						'type'              => 'checkbox',
						),
					array(
						'name'              => 'exclude_users_tracking',
						'label'             => __( 'Exclude users from tracking', 'wp-analytify' ),
						'desc'              => __( 'Don\'t insert the tracking code for above user roles.', 'wp-analytify' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' 			=> $this->get_current_roles(),
						),

					array(
						'name'         => 'profile_for_posts',
						'label'        => __( 'Profile for posts (Backend/Front-end)', 'wp-analytify' ),
						'desc'         => __( 'Select your website profile for Backend/Front-end Stats. You can select your any Website profile. It will show Analytics for your selected website profile', 'wp-analytify' ),
						'type'         => 'select_profile',
						'default'      => 'Choose profile for posts',
						'options'      => WP_ANALYTIFY_FUNCTIONS::fetch_profiles_list_summary(),
						'size'         => ''
						),

					array(
						'name'    => 'profile_for_dashboard',
						'label'   => __( 'Profile for Dashboard', 'wp-analytify' ),
						'desc'    => __( 'Select your website profile for Dashboard Stats. You can select your any Website profile. It will show Analytics for your selected website profile.', 'wp-analytify' ),
						'type'    => 'select_profile',
						'default' => 'Choose profile for dashboard',
						'options' => WP_ANALYTIFY_FUNCTIONS::fetch_profiles_list_summary(),
						),
					array(
						'name'              => 'hide_profiles_list',
						'label'             => __( 'Hide Profiles list', 'wp-analytify' ),
						'desc'              => __( 'Hide the selection of profiles for Dashboard and Posts (Back-end/Front-end). Best to hide for your clients not to see other profiles.', 'wp-analytify' ),
						'type'              => 'checkbox',
						),
					),
				'wp-analytify-admin' => array(
					array(
						'name'              => 'disable_back_end',
						'label'             => __( 'Disable Analytics under posts/pages (wp-admin)', 'wp-analytify' ),
						'desc'              => __( 'Check it, If you don\'t want to load Stats by default on all pages. Remember, There is a section under each post/page. You can still view Stats on pages you want.', 'wp-analytify' ),
						'type'              => 'checkbox',
						),
					array(
						'name'              => 'show_analytics_roles_back_end',
						'label'             => __( 'Show Analytics to (roles)', 'wp-analytify' ),
						'desc'              => __( 'Show Analytics to above selected user roles only.', 'wp-analytify' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => $this->get_current_roles(),
						),

					array(
						'name'              => 'show_analytics_post_types_back_end',
						'label'             => __( 'Analytics on Post types', 'wp-analytify' ),
						'desc'              =>  class_exists( 'WP_Analytify_Pro' )  ?  __( 'Show Analytics under the above post types only', 'wp-analytify' )  :  __( 'Show Analytics under the above post types only. Buy <a href="http://analytify.io/pricing/" target="_blank">Premium</a> version for Custom Post Types.', 'wp-analytify' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => $this->get_current_post_types(),
						),

					array(
						'name'              => 'show_panels_back_end',
						'label'             => __( 'Edit pages/posts Analytics panels:', 'wp-analytify' ),
						'desc'              =>  class_exists( 'WP_Analytify_Pro' )  ? __( 'Select which Stats panels you want to display under posts/pages.' , 'wp-analytify' ) : __( 'Select which Stats panels you want to display under posts/pages. Only "General Stats" will visible in Free Version. Buy <a href="http://analytify.io/pricing/" target="_blank">Premium</a> version to see the full statistics.', 'wp-analytify' ),
						'type'              => 'chosen',
						'default' 			=> array(),
						'options' => array(
							'show-overall-dashboard'     => 'General Stats',
							'show-geographic-dashboard'  => 'Geographic Stats',
							'show-system-stats'          => 'System Stats',
							'show-keywords-dashboard'    => 'Keywords Stats',
							'show-social-dashboard'      => 'Social Media Stats',
							'show-referrer-dashboard'    => 'Referrers Stats',
							),
						),
					array(
						'name'              => 'exclude_pages_back_end',
						'label'             => __( 'Exclude Analytics on specific pages:', 'wp-analytify' ),
						'desc'              => __( 'Enter ID\'s of posts or pages separated by commas on which you don\'t want to show Analytics e.g 11,45,66', 'wp-analytify' ),
						'type'              => 'text',
						'default'           => '0',
						),
					),
					'wp-analytify-advanced' => array(
							array(
								'name'              => 'user_advanced_keys',
								'label'             => __( 'Do you want to use your own API keys ?', 'wp-analytify' ),
								'desc'              => __( 'You need to create a Project in Google <a target=\'_blank\' href=\'https://console.developers.google.com/project\'>Console</a> Read this simple 3 minutes <a target=\'_blank\' href=\'http://analytify.io/google-api-tutorial\'>tutorial</a> to get your ClientID, Client Secret, Redirect URI and API Key and enter them in below inputs.', 'wp-analytify' ),
								'type'              => 'checkbox',
								'class'	=> 'user_advanced_keys',
							),

							array(
								'name'              => 'client_id',
								'label'             => __( 'ClientID:', 'wp-analytify' ),
								'desc'              => __( 'Your Client ID', 'wp-analytify' ),
								'type'              => 'text',
								'class' => 'user_keys',
							),
							array(
								'name'              => 'client_secret',
								'label'             => __( 'Client Secret:', 'wp-analytify' ),
								'desc'              => __( 'Your Client Secret', 'wp-analytify' ),
								'type'              => 'text',
								'class' => 'user_keys',
							),
							array(
								'name'              => 'api_key',
								'label'             => __( 'API Key:', 'wp-analytify' ),
								'desc'              => __( '(Optional)', 'wp-analytify' ),
								'type'              => 'text',
								'class' => 'user_keys',
							),
							array(
								'name'              => 'redirect_uri',
								'label'             => __( 'Redirect URI:', 'wp-analytify' ),
								'desc'              => sprintf(__( '( Redirect URI is very important when you are using your own Keys Use this Redirect URI  %1$s )' , 'wp-analytify' ) ,  '<b>' . admin_url('admin.php?page=analytify-settings') . '</b>' ),
								'type'              => 'text',
								'class' => 'user_keys',
							),

						),
			);

			if ( get_option( 'pa_google_token' ) ) {
				$advance_setting_fields = array(
					array(
						'name'              => 'anonymize_ip',
						'label'             => __( 'Anonymize IP addresses', 'wp-analytify' ),
						'desc'              => __( 'Detailed information about IP Anonymization in Google Analytics can be found <a href=\'https://support.google.com/analytics/answer/2763052\'>Details</a>', 'wp-analytify' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'force_ssl',
						'label'             => __( 'Force Analytics Traffic Over SSL', 'wp-analytify' ),
						'desc'              => __( 'If your site is HTTPS based, Analytics traffic will always go over SSL. If you have an insecure site, but wish Analytics traffic to still be secure, use this option.', 'wp-analytify' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'track_user_id',
						'label'             => __( 'Track User ID', 'wp-analytify' ),
						'desc'              => __( 'Detailed information about Track User ID in Google Analytics can be found <a href=\'https://support.google.com/analytics/answer/3123662\'>Details</a>', 'wp-analytify' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'demographic_interest_tracking',
						'label'             => __( 'Demographic & Interest Tracking', 'wp-analytify' ),
						'desc'              => __( 'This allows you to view extra dimensions about users (Age, Gender, Affinity Categories, In-Market Segments and Other Categories.', 'wp-analytify' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => '404_page_track',
						'label'             => __( 'Page Not Found (404)', 'wp-analytify' ),
						'desc' 							=> __( 'Track all 404 page links' , 'wp-analytify'  ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'javascript_error_track',
						'label'             => __( 'JavaScript Errors', 'wp-analytify' ),
						'desc' 							=> __( 'Track all Javascript errors' , 'wp-analytify'  ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'ajax_error_track',
						'label'             => __( 'AJAX Errors', 'wp-analytify' ),
						'desc' 							=> __( 'Track all AJAX errors' , 'wp-analytify'  ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'linker_cross_domain_tracking',
						'label'             => __( 'Setup Cross-domain Tracking', 'wp-analytify' ),
						'desc' 				=> __( 'This will add <code>allowLinker:true</code> tag to tracking code. Read this <a href=\'https:\//analytify.io/doc/setup-cross-domain-tracking-wordpress\'>guide</a> for more information.' , 'wp-analytify' ),
						'type'              => 'checkbox',
					),
					array(
						'name'              => 'custom_js_code',
						'label'             => __( 'Custom JS Code', 'wp-analytify' ),
						'desc' 				=> __( 'This will add inline tracking code before sending the pageview hit to Google Analytics.' , 'wp-analytify'  ),
						'type'              => 'textarea',
					)
				);

				foreach ( $advance_setting_fields as $advance_setting_field ) {
					array_push( $settings_fields['wp-analytify-advanced'], $advance_setting_field );
				}
			}

			$settings_fields = apply_filters( 'wp_analytify_pro_setting_fields' ,  $settings_fields );

			return $settings_fields;
		}


	    /**
	     * Initialize and registers the settings sections and fileds to WordPress
	     *
	     * Usually this should be called at `admin_init` hook.
	     *
	     * This function gets the initiated settings sections and fields. Then
	     * registers them to WordPress and ready for use.
	     */
	    function admin_init() {

	    	$this->set_sections( $this->get_settings_sections() );
	    	$this->set_fields( $this->get_settings_fields() );

	        // register settings sections
	    	foreach ( $this->settings_sections as $section ) {

	    		if ( false == get_option( $section['id'] ) ) {
	    			add_option( $section['id'] );
	    		}

	    		if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
	    			$section['desc'] = '<div class="inside">'.$section['desc'].'</div>';
	    			$callback = create_function( '', 'echo "'.str_replace( '"', '\"', $section['desc'] ).'";' );
	    		} else if ( isset( $section['callback'] ) ) {
	    			$callback = $section['callback'];
	    		} else {
	    			$callback = null;
	    		}

	    		add_settings_section( $section['id'], '', $callback, $section['id'] );
	    	}

	        // register settings fields
	    	foreach ( $this->settings_fields as $section => $field ) {
	    		foreach ( $field as $option ) {

	    			$type = isset( $option['type'] ) ? $option['type'] : 'text';

	    			$args = array(
	    				'id'                => $option['name'],
	    				'label_for'         => $args['label_for'] = "{$section}[{$option['name']}]",
	    				'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
	    				'name'              => $option['label'],
	    				'section'           => $section,
	    				'size'              => isset( $option['size'] ) ? $option['size'] : null,
	    				'options'           => isset( $option['options'] ) ? $option['options'] : '',
	    				'std'               => isset( $option['default'] ) ? $option['default'] : '',
	    				'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
	    				'class'              => isset( $option['class'] ) ? $option['class'] : '',
	    				'type'              => $type,
	    				);

	    			add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], array( $this, 'callback_' . $type ), $section, $section, $args );
	    		}
	    	}

	        // creates our settings in the options table
	    	foreach ( $this->settings_sections as $section ) {
	    		register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
	    	}
	    }


	    public static function get_current_post_types() {

	    	$post_types_list = array();

	    	$args = array(
				'public'   => true,

				);

			$post_types = get_post_types( $args );

			foreach ( $post_types as $post_type ) {
				$post_types_list[ $post_type ] = $post_type;
			}

			return $post_types_list;
	    }

	    /**
	     * get current list of all roles and display in dropdown
	     *
	     * @return array
	     */
	   	public static function get_current_roles() {

	   		$roles = array();

	   		if ( get_editable_roles() > 0) {

	   			foreach ( get_editable_roles() as $role => $name ) {

	   				$roles[ $role ] = $name['name'];
	   			}
	   		}else{
	   			$roles['empty'] = 'no roles found';
	   		}

	   		return $roles;
	    }


		/**
		 * Get field description for display
		 *
		 * @param array $args settings field args
		 */
		public function get_field_description( $args ) {
			if ( ! empty( $args['desc'] ) ) {
				$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
			} else {
				$desc = '';
			}

			return $desc;
		}

		/**
		 * Displays a text field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_text( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$type  = isset( $args['type'] ) ? $args['type'] : 'text';

			$html  = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"/>', $type, $size, $args['section'], $args['id'], $value );
			$html  .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a text field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_button( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'button';
			$type  = isset( $args['type'] ) ? $args['type'] : 'text';

			$html  = sprintf( '<input type="%1$s" class="%2$s button-primary" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"/>', $type, $size, $args['section'], $args['id'], $value );
			$html  .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a url field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_url( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a number field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_number( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_checkbox( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html  = '<fieldset>';
			$html  .= sprintf( '<label for="%1$s[%2$s]">', $args['section'], $args['id'] );
			$html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
			$html  .= sprintf( '<input type="checkbox" class="checkbox" id="%2$s[%3$s]" name="%2$s[%3$s]" value="on" %4$s />', $args['class'], $args['section'], $args['id'], checked( $value, 'on', false ) );
			$html  .= sprintf( '<span class="dashicons dashicons-editor-help setting-more-info" title="%1$s"></span></label>', $args['desc'] );
			$html  .= '</fieldset>';
				// $html =$args['desc'];
			echo $html;
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_multicheck( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$html  = '<fieldset>';

			foreach ( $args['options'] as $key => $label ) {
				$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
				$html    .= sprintf( '<label for="wp-analytify-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html    .= sprintf( '<input type="checkbox" class="checkbox" id="wp-analytify-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
				$html    .= sprintf( '%1$s</label><br>',  $label );
			}

			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			echo $html;
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_radio( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$html  = '<fieldset>';

			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<label for="wp-analytify-%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key );
				$html .= sprintf( '<input type="radio" class="radio" id="wp-analytify-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
				$html .= sprintf( '%1$s</label><br>', $label );
			}

			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			echo $html;
		}

		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_select( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$html  = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );

			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
			}

			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Displays a chosen selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_chosen( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			// var_dump($this->get_option( $args['id'], $args['section'], $args['std'] ));
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$html  = sprintf( '<select multiple class="%1$s analytify-chosen" name="%2$s[%3$s][]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );

			foreach ( $args['options'] as $key => $label ) {
				$selected	= in_array( $key, $value ) ? 'selected = selected' : '';
				$html 		.= sprintf( '<option value="%s"%s>%s</option>', $key, $selected, $label );
			}

			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_select_profile( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$html  = sprintf( '<select class="%1$s analytify-chosen" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );

			$_analytify_setting = get_option( 'wp-analytify-profile' );
			if ( isset($_analytify_setting['hide_profiles_list']) &&  $_analytify_setting['hide_profiles_list'] === 'on' ) {

				$html .= '<option value="' . $value . '" selected>' . WP_ANALYTIFY_FUNCTIONS::search_profile_info( $value, 'websiteUrl' ) . ' (' .WP_ANALYTIFY_FUNCTIONS::search_profile_info( $value, 'name' ) . ')' . '</option>';
			} else {

				if ( isset( $args['options']->items ) ) {

					$html .= '<option value="">' . $args['std'] . '</option>';
					foreach ( $args['options']->getItems() as  $account ) {

						foreach ( $account->getWebProperties() as  $property ) {

							$html .= '<optgroup label=" ' . $property->getName() . ' ">';

							foreach ( $property->getProfiles() as $profile ) {
								$html .= sprintf( '<option value="%1$s" %2$s>%3$s (%4$s)</option>', $profile->getId(), selected( $value, $profile->getId(), false ), $profile->getName() , $property->getId() );

								// Update the UA code in option on setting save for proile_for_posts.
								if (  $value === $profile->getId() && 'profile_for_posts' === $args['id'] ) {
									update_option( 'analytify_ua_code', $property->getId() );
								}

							}
						}

						$html .= '</optgroup>';
					}
				}
				// else{

				// 	$html .= '<option value="">no profiles found</option>';
				// }
			}


			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_textarea( $args ) {

			$value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value );
			$html  .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args
		 * @return string
		 */
		function callback_html( $args ) {
			echo $this->get_field_description( $args );
		}

		/**
		 * Displays a rich text textarea for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_wysiwyg( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

			echo '<div style="max-width: ' . $size . ';">';

			$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
			'textarea_rows' => 10,
			);

			if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
				$editor_settings = array_merge( $editor_settings, $args['options'] );
			}

			wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

			echo '</div>';

			echo $this->get_field_description( $args );
		}

		/**
		 * Displays a file upload field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_file( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$id    = $args['section']  . '[' . $args['id'] . ']';
			$label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File' );

			$html  = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html  .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
			$html  .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a password field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_password( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html  .= $this->get_field_description( $args );

			echo $html;
		}

		/**
		 * Displays a color picker field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_color( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
			$html  .= $this->get_field_description( $args );

			echo $html;
		}


		/**
		 * [callback_authentication description]
		 *
		 * @return [type] [description]
		 */
		function callback_authentication() {

			$output = '';

			ob_start();

			if ( get_option( 'pa_google_token' ) ) { ?>

				<form action="" method="post">
				<tr>
						<p class="inside"><?php esc_html_e( 'You have allowed your site to access the Analytics data from Google. Logout below to disconnect it.', 'wp-analytify' ); ?><p>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="button-primary" value="Logout" name="wp_analytify_log_out" /></td>
				</tr>
				</form>

				<?php
			} else {
				?>

				<a target="_self" title="Log in with your Google Analytics Account" class="button-primary authentication_btn" href="https://accounts.google.com/o/oauth2/auth?<?php echo WP_ANALYTIFY_FUNCTIONS::generate_login_url(); ?>"><?php esc_html_e( 'Log in with Google Analytics Account', 'wp-analytify' ); ?></a>
			<?php
			}

			$output .= ob_get_contents();
			ob_end_clean();
			echo $output;
		}


		/**
		 * Help tab content
		 *
		 * @since 2.0
		 */
		function callback_help() {
			?>

		<div class="wrap">

			<?php
			if ( has_action( 'anlytify_pro_support_tab' ) ) {

				do_action( 'anlytify_pro_support_tab' );

			} else { ?>

						<h3><?php esc_html_e( 'Support', 'wp-analytify' ); ?></h3>

						<p><?php esc_html_e( 'As this is a free plugin, we do not provide support.', 'wp-analytify' ); ?></p>

						<p><?php echo sprintf( esc_html__( 'You may ask the WordPress community for help by posting to the %1$s WordPress.org support forum %2$s. Response time can range from a few days to a few weeks and will likely be from a non-developer.', 'wp-analytify' ), '<a href="http://wordpress.org/support/plugin/wp-analytify/" target="_blank">', '</a>' ); ?></p>

						<p class="upgrade-to-pro"><?php echo sprintf( esc_html__( 'If you want a %1$s timely response via email from a developer %2$s who works on this plugin, %3$s upgrade to WP Analytify Pro %4$s and send us an email.' ), '<strong>', '</strong>', '<a href="https://analytify.io/?utm_source=insideplugin&amp;utm_medium=web&amp;utm_content=help-tab&amp;utm_campaign=freeplugin" target="_blank">', '</a>' ); ?></p>

						<p><?php echo sprintf( esc_html__( 'If you\'ve found a bug, please %1$s submit an issue at Github %2$s.' ), '<a href="https://github.com/hiddenpearls/wp-analytify/issues" target="_blank">', '</a>' ); ?></p>

					<?php } ?>

			</div>
			<div class="wp-analytify-debug">
					<h3><?php esc_html_e( 'Diagnostic Info &amp; Error Log' ); ?></h3>
				<textarea class="debug-log-textarea" autocomplete="off" readonly="" id="debug-log-textarea"></textarea>
			</div>


			<?php
		}

		function callback_email_form( ) {
			?>
			<form class="" action="" method="post">
				<input type="submit" name="test_email" class="analytify_test_email_btn" value="Test Email" />
				<span class="analytify_setting_note">Note: Please save changes before sending a test email.</span>
			</form>
			<?php
		}

		/**
		 * Sanitize callback for Settings API
		 */
		function sanitize_options( $options ) {

			if ( ! $options ) { return; }

			foreach ( $options as $option_slug => $option_value ) {
				$sanitize_callback = $this->get_sanitize_callback( $option_slug );

				// If callback is set, call it
				if ( $sanitize_callback ) {
					$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
					continue;
				}
			}

			return $options;
		}

		/**
		 * Get sanitization callback for given option slug
		 *
		 * @param string $slug option slug
		 *
		 * @return mixed string or bool false
		 */
		function get_sanitize_callback( $slug = '' ) {
			if ( empty( $slug ) ) {
				return false;
			}

			// Iterate over registered fields and see if we can find proper callback
			foreach ( $this->settings_fields as $section => $options ) {
				foreach ( $options as $option ) {
					if ( $option['name'] != $slug ) {
						continue;
					}

					// Return the callback name
					return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
				}
			}

			return false;
		}

		/**
		 * Get the value of a settings field
		 *
		 * @param string $option  settings field name
		 * @param string $section the section name this field belongs to
		 * @param string $default default text if it's not found
		 * @return string
		 */
		function get_option( $option, $section, $default = '' ) {

			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		/**
		 * [show_tabs description]
		 *
		 * @return [type] [description]
		 */
		function show_tabs() {

			// var_dump(get_option( "wp-analytify-profile" ));
			$html  = '<div id="icon-themes" class="icon32"></div>';
			$html .= '<h2 class="opt-title"><span id="icon-options-general" class="analytics-options"><img src="' . plugins_url( '../assets/images/wp-analytics-logo.png', __FILE__ ) . '" alt="">' . sprintf( esc_html__( '%1$s Settings', 'wp-analytify' ), 'WP Analytify Plugin' ) . '</span></h2>';

			$html .= '<div class="wpa-tab-wrapper" ><h2 class="nav-tab-wrapper">';

			foreach ( $this->settings_sections as $tab ) {
				$html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'] );
			}

			$html .= '</h2>';

			echo $html;
		}


		/**
		 * Show the section settings forms
		 *
		 * This function displays every sections in a different form
		 */
		function show_forms() {

			$this->process_logout();
			settings_errors();

			?>
			<div class="metabox-holder">
			<?php foreach ( $this->settings_sections as $form ) { ?>
			<div id="<?php echo $form['id']; ?>" class="group" style="display: none;">

				<?php


				if ( $form['id'] === 'wp-analytify-authentication' ) {

					$this->callback_authentication();

				} elseif ( $form['id'] === 'wp-analytify-license' ) {

					// Call license tab display data from Pro.
					do_action( 'wp_analytify_license_tab' );

				} elseif ( $form['id'] === 'wp-analytify-help' ) {

					$this->callback_help();

				} else if ( $form['id'] === 'wp-analytify-advanced' ) {
					?>

					<form method="post" action="options.php">
						<?php
							// do_action( 'wsa_form_top_' . $form['id'], $form );
						settings_fields( $form['id'] );
						do_settings_sections( $form['id'] );
							// do_action( 'wsa_form_bottom_' . $form['id'], $form );
						?>
						<div style="padding-left: 10px">
							<?php submit_button(); ?>
						</div>
					</form>

					<?php
				} else {

					if ( get_option( 'pa_google_token' ) ) {
						?>

						<form method="post" action="options.php">
							<?php
								// do_action( 'wsa_form_top_' . $form['id'], $form );
							settings_fields( $form['id'] );
							do_settings_sections( $form['id'] );
								// do_action( 'wsa_form_bottom_' . $form['id'], $form );

							?>
							<div style="padding-left: 10px">
								<?php submit_button(); ?>
							</div>
						</form>

						<?php

						if ( $form['id'] === 'wp-analytify-email' ) {
								$this->callback_email_form();
						}

					}
				}
				?>

			</div>
			<?php } ?>

				</div>
				<?php
				$this->script();
				echo "  </div>";
				echo $this->pro_features();
		}

		// unset profile hidden check on logout
		function process_logout() {

			if ( isset( $_POST['wp_analytify_log_out'] ) ) {

				$_analytify_profile = get_option( 'wp-analytify-profile' );
				if ( isset( $_analytify_profile['hide_profiles_list'] ) ) {
					unset( $_analytify_profile['hide_profiles_list'] );
				}
				update_option( 'wp-analytify-profile', $_analytify_profile );

			}

		}

		/**
		 * Tabbable JavaScript codes & Initiate Color Picker
		 *
		 * This code uses localstorage for displaying active tabs
		 */
		function script() {
			?>

			<script>

			jQuery( document ).ready( function( $ ) {

				// hide this checkbox for hiding profiles.
				<?php $_analytify_profile = get_option( 'wp-analytify-profile' ) ?>
		    	<?php if ( $_analytify_profile && isset( $_analytify_profile['hide_profiles_list'] ) && 'on' === $_analytify_profile['hide_profiles_list'] ) : ?>
					$('#wp-analytify-profile\\[hide_profiles_list\\]').closest('tr').hide();
		    	<?php endif; ?>


				<?php if ( 'analytify-settings' === $_GET['page'] &&	! get_option( 'pa_google_token' ) ){ ?>
						localStorage.setItem('activetab', '#wp-analytify-authentication');
				<?php } ?>

				<?php if ( 'analytify-settings' === $_GET['page'] && get_option( 'pa_google_token' ) ) : ?>

					if(window.location.href.indexOf("#wp-analytify-profile") > -1) {
						localStorage.setItem('activetab', '#wp-analytify-profile');
					}
					if( window.location.href.indexOf("#wp-analytify-email") > -1 ) {
						localStorage.setItem('activetab', '#wp-analytify-email');
					}
				<?php endif; ?>

				// show license tab on license link click from other page.
				if( window.location.href.indexOf("#wp-analytify-license") > -1 ) {
					localStorage.setItem('activetab', '#wp-analytify-license');
				}

				// show license tab on license link click on settings page.
				$('.wp-analytify-license-notice').on('click', function(evt) {

					$('.group').hide();
					$('.nav-tab-wrapper a').removeClass('nav-tab-active');

					if (typeof(localStorage) != 'undefined' ) {
						localStorage.setItem("activetab", '#wp-analytify-license');
					}
					$('#wp-analytify-license-tab').addClass('nav-tab-active').blur();
					$('#wp-analytify-license').fadeIn();
					evt.preventDefault();
				} );

				$('.group').hide();

				var activetab = '';
				if (typeof(localStorage) != 'undefined' ) {
					activetab = localStorage.getItem("activetab");
				}
				if (activetab != '' && $(activetab).length ) {
					$(activetab).fadeIn();
				} else {
					$('.group:first').fadeIn();
				}
				$('.group .collapsed').each(function(){
					$(this).find('input:checked').parent().parent().parent().nextAll().each(
						function(){
							if ($(this).hasClass('last')) {
								$(this).removeClass('hidden');
								return false;
							}
							$(this).filter('.hidden').removeClass('hidden');
						});
				});

				if (activetab != '' && $(activetab + '-tab').length ) {
					$(activetab + '-tab').addClass('nav-tab-active');


				}
				else {
					$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
				}

				// load diagnostic debug log only when help tab is active
				if( $('.nav-tab-active').attr('href') === '#wp-analytify-help' ) refresh_debug_log();

				$('.nav-tab-wrapper a').click(function(evt) {

					$('.nav-tab-wrapper a').removeClass('nav-tab-active');
					$(this).addClass('nav-tab-active').blur();
					var clicked_group = $(this).attr('href');
					if (typeof(localStorage) != 'undefined' ) {
						localStorage.setItem("activetab", $(this).attr('href'));
					}
					$('.group').hide();
					$(clicked_group).fadeIn();

					// load diagnostic debug log only when help tab is active
					if( $('.nav-tab-active').attr('href') === '#wp-analytify-help' ) refresh_debug_log();
					evt.preventDefault();
				});

			});
			</script>

			<style type="text/css">
				/** WordPress 3.8 Fix **/
				.form-table th { padding: 20px 10px; }
				#wpbody-content .metabox-holder { padding-top: 5px; }
			</style>

		<?php
		}


		function pro_features() {

			$html = '	<div class="pro-feature-wrapper" >';
			 ob_start();
			?>

				<p > Tweet us <a href="https://twitter.com/analytify" style="text-decoration:none;"> @analytify </a> and Like us <a href="https://fb.com/analytify" style="text-decoration:none;">@analytify</a> </p>
				<table class="wa_feature_table">
					<tbody>
						<tr>
							<th>Features</th>
							<th>Free</th>
							<th>Pro</th>
						</tr>
						<tr>
							<td><strong>Support</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Dashboard</strong></td>
							<td>Yes</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Live Stats</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>ShortCodes</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>

						<tr>
							<td><strong>Extensions</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Analytics under Posts (admin)</strong></td>
							<td>Yes (limited)</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Analytify under Pages (admin)</strong></td>
							<td>Yes (limited)</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Analytify under Custom Post Types (front/admin)</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
					</tbody>
				</table>
				<div class="postbox-container side">
					<div class="metabox-holder">


						<div class="grids_auto_size wpa_side_box" style="width: 100%;">
							<div class="grid_title cen"> UPGRADE to PRO </div>

							<div class="grid_footer cen" style="background-color:white;">
								<a href="http://analytify.io/upgrade-from-free" title="Analytify Support">Buy Now</a> the PRO version of Analytify and get tons of benefits including premium features, support and updates.
							</div>
						</div>
						<div class="grids_auto_size wpa_side_box" style=" width: 100%;">
							<div class="grid_footer cen">
								made with ♥ by <a href="http://wpbrigade.com" title="WPBrigade | A Brigade of WordPress Developers." />WPBrigade</a>
							</div>
						</div>
					</div>
				</div>


			<?php
			$inner_html = ob_get_clean();
			$html .= apply_filters( 'free-pro-features', $inner_html );
			echo '</div>';
			return $html;
		}

	}

}
