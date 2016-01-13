<?php
$wp_analytify = new WP_Analytify();

if (! function_exists( 'curl_init' ) ) {
	esc_html_e('This plugin requires the CURL PHP extension');
	return false;
}

if (! function_exists( 'json_decode' ) ) {
	esc_html_e('This plugin requires the JSON PHP extension');
	return false;
}

if (! function_exists( 'http_build_query' )) {
	esc_html_e('This plugin requires http_build_query()');
	return false;
}

/* Save user specific Keys, ID's and Redirect URI */
if ( isset( $_POST[ 'save_code' ] ) ) {

	if( isset($_POST['auth_step']) and $_POST['auth_step'] == 'Yes' ) {

		update_option('ANALYTIFY_USER_KEYS' , 		sanitize_text_field( $_POST['auth_step'] ));
		update_option('ANALYTIFY_CLIENTID' , 		sanitize_key( $_POST['analytify_clientid'] ));
		update_option('ANALYTIFY_CLIENTSECRET' , 	sanitize_key( $_POST['analytify_clientsecret'] ));
		update_option('ANALYTIFY_DEV_KEY' , 		sanitize_key( $_POST['analytify_apikey'] ));
		update_option('ANALYTIFY_REDIRECT_URI' , 	sanitize_text_field( $_POST['analytify_redirect_uri'] ));

	}

	if( !isset($_POST['auth_step']) ) {

		update_option('ANALYTIFY_USER_KEYS' , 'No' );
	}

/*	if( isset($_POST['auth_step']) and $_POST['auth_step'] == 'user_access_code' ) {

		$key_google_token = $_POST[ 'key_google_token' ];

		if( $wp_analytify->pt_save_data( $key_google_token )){
			$update_message = '<div id="setting-error-settings_updated" class="updated settings-error below-h2"><p><strong>Access code saved.</strong></p></div>';
		}
	}*/


}


if( get_option( 'ANALYTIFY_USER_KEYS') == 'Yes' ) {

	$redirect_url	= get_option('ANALYTIFY_REDIRECT_URI');
	$client_id		= get_option('ANALYTIFY_CLIENTID');

}else{

	$redirect_url	= ANALYTIFY_REDIRECT;
	$client_id		= ANALYTIFY_CLIENTID;
}


$url = http_build_query( array(
	'next'            =>	$wp_analytify->pa_setting_url(),
	'scope'           =>	ANALYTIFY_SCOPE,
	'response_type'   =>	'code',
	'state'			  => 	get_admin_url() . 'admin.php?page=analytify-settings',
	'redirect_uri'    =>	$redirect_url,
	'client_id'       =>	$client_id,
	'access_type'     =>	'offline',
	'approval_prompt' =>	'force'
	)
);

// Saving settings for back end Analytics for Posts and Pages.
if ( isset( $_POST[ 'save_settings_admin' ] ) ) {

	update_option( 'post_analytics_settings_back' , $_POST[ 'backend' ] );
	update_option( 'analytify_posts_stats' , $_POST[ 'posts' ] );
	update_option( 'post_analytics_access_back' , $_POST[ 'access_role_back' ] );
	update_option( 'post_analytics_disable_back' , $_POST[ 'disable_back' ] );
	update_option( 'post_analytics_exclude_posts_back', @$_POST[ 'exclude_posts_back' ]);

	$update_message = '<div id="setting-error-settings_updated" class="updated settings-error below-h2"><p><strong>Admin changes are saved.</strong></p></div>';
	
} // endif



// Saving Profiles
if (isset($_POST[ 'save_profile' ])) {

	$profile_id 	        =	$_POST[ 'webprofile' ];
	$postsProfileName		=	$_POST[ $profile_id."-1-profile-name"];
	$display_tracking_code  =	$_POST[ 'display_tracking_code' ];
	$tracking_code          =	$_POST[ 'tracking_code' ];
	$web_profile_dashboard  =	$_POST[ 'webprofile_dashboard' ];
	$web_profile_url        =	$_POST[ $web_profile_dashboard ];
	$dashboardProfileName 	= 	$_POST[ $web_profile_dashboard . "-profile-name" ];
	$webPropertyId          =	$_POST[ $profile_id."-1"];


	// pt_webprofile_dashboard  === Dashboard Profile ID
	// pt_webprofile 			=== Posts Profile ID

	update_option( 'pt_webprofile', $profile_id );
	update_option( 'webPropertyId', $webPropertyId);
	update_option( 'pt_webprofile_dashboard', $web_profile_dashboard );
	update_option( 'pt_webprofile_url', urldecode( urldecode( $web_profile_url )));
	update_option( 'wp-analytify-dashboard-profile-name', $dashboardProfileName);
	update_option( 'wp-analytify-posts-profile-name', $postsProfileName);

	update_option( 'analytify_tracking_code', $tracking_code);
	update_option( 'display_tracking_code', $display_tracking_code);

	if( isset( $_POST[ 'ga_code' ] ) ) {
		update_option( 'analytify_code', 1 );
	}
	else{
		update_option( 'analytify_code', 0 );
	}

	$update_message = '<div id="setting-error-settings_updated" class="updated notice is-dismissible settings-error below-h2"> 
	<p><strong>Success:</strong> Your Profile tab settings are saved.</p></div>';
}

	// Clear Authorization and other data
if (isset($_POST[ "clear" ])) {

	delete_option( 'pt_webprofile' );
	delete_option( 'pt_webprofile_dashboard' );
	delete_option( 'pt_webprofile_url' );
	delete_option( 'pa_google_token' );
	delete_option( 'pa_welcome_message' );
	delete_option( 'post_analytics_token' );

	$update_message = '<div id="setting-error-settings_updated" class="updated notice is-dismissible settings-error below-h2"> 
	<p><strong>Authentication Cleared login again.</strong></p></div>';
}
?>

<div class="wrap">
	<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url( 'images/wp-analytics-logo.png', dirname(__FILE__) );?>" alt=""></span>
		<?php echo __( 'Analytify Settings', 'wp-analytify'); ?>
	</h2>

	<?php
	if (isset($update_message)) echo $update_message;
	
	if ( isset ( $_GET['tab'] ) ) $wp_analytify->pa_settings_tabs($_GET['tab']); 
	else $wp_analytify->pa_settings_tabs( 'authentication' );

	if ( isset ( $_GET['tab'] ) ) 
		$tab = $_GET['tab']; 
	else 
		$tab = 'authentication';
	
	// Authentication Tab section
	if( $tab == 'authentication' ) {
		?>

		<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" name="settings_form" id="settings_form">
			<table width="1004" class="form-table">
				<tbody>
					<?php if( get_option( 'pa_google_token' ) ) { ?>
						<tr>
							<p class="description"><br /><?php echo esc_html_e( 'You have allowed your site to access the Analytics data from Google. Logout below to disconnect it.', 'wp-analytify' ); ?><p>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" class="button-primary" value="Logout" name="clear" /></td>
						</tr>
					<?php 
					}
					else { ?>

					<tr>
						<td width="877" colspan="2">
							<a target="_self" class="button-primary authentication_btn" href="https://accounts.google.com/o/oauth2/auth?<?php echo $url ?>">Log in with Google Analytics Account</a>
						</td>
					</tr>

					<?php } ?>
				</tbody>
			</table>
		</form>

		<?php
	} // endif
// Choose profiles for dashboard and posts at front/back.
	if( $tab == 'profile' ){

		$profiles = $wp_analytify->pt_get_analytics_accounts();

		//echo count($profiles);

		if( isset( $profiles ) ) { ?>
		<p class="description"><br /><?php esc_html_e( 'Select your profiles for front-end and backend sections.', 'wp-analytify' ); ?></p>

		<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
			<table width="1004" class="form-table">
				<tbody>
					<tr>
						<th width="115"><?php esc_html_e( 'Install Google Analytics tracking code :', 'wp-analytify' ); ?></th>
						<td width="877">
							<input type="checkbox" name="ga_code" value="1" 
							<?php if( get_option( 'analytify_code' ) == 1 ) { echo 'checked'; } ?>>
							<p class="description">Insert Google Analytics JS code in header to track the visitors. You can uncheck this option if you have already insert the GA code in your website.</p>
						</td>
					</tr>
					<tr>
						<th width="115">
							<?php esc_html_e( 'Exclude users from tracking :', 'wp-analytify' ); ?>
						</th>
						<td>
							<select multiple name="display_tracking_code[]" class="analytify-chosen" style="width:306px">
								<?php

								if ( !isset( $wp_roles ) ) {

									$wp_roles = new WP_Roles();
								}

								foreach ( $wp_roles->role_names as $role => $name ) { ?>
								
								<option value="<?php echo $role; ?>"
									<?php

									if ( is_array( get_option( 'display_tracking_code' ) ) ) {

										selected( in_array( $role, get_option('display_tracking_code') ) );

									}

									?>>
									<?php echo $name; ?>
								</option>
								<?php 
							} 
							?>
						</select>
						<p class="description">Don't insert the tracking code for above user roles.</p>
					</td>
				</tr>
				<tr>
					<th width="115"><?php esc_html_e( 'Tracking code type :', 'wp-analytify' ); ?></th>
					<td width="877">
						<select name='tracking_code' class="analytify-chosen">
							<option value="universal" <?php selected( 'universal', get_option( 'analytify_tracking_code' ) ); ?>>
								Universal Code (analytics.js)
							</option>
							<option value="ga"  <?php selected( 'ga', get_option( 'analytify_tracking_code' ) ); ?>>
								Tranditional Code (ga.js)
							</option>

						</select>
						<p class="description">Which type of tracking code to use i-e (Analytics.js , ga.js) Google recommends to use Analytics.js now <a href="https://developers.google.com/analytics/devguides/collection/upgrade/reference/gajs-analyticsjs" target="_blank">Read More</a>.</p>
					</td>
				</tr>
				<tr>
					<th width="115"><?php esc_html_e( 'Profile for posts (Backend/Front-end) :', 'wp-analytify' ); ?></th>
					<td width="877">
						<?php //print_r($profiles->items); ?>
						<select name='webprofile' class="analytify-chosen">
							<?php foreach ( $profiles->items as $profile ) { ?>
							<option value="<?php echo $profile[ 'id' ];?>"
								<?php selected( $profile[ 'id' ], get_option( 'pt_webprofile' ) ); ?>>
								<?php echo $profile[ 'websiteUrl' ];?> - <?php echo $profile[ 'name' ];?>
							</option>
							<?php } ?>
						</select>
						<?php 
						foreach ( $profiles->items as $profile ) { ?>
							<input type="hidden" name="<?php echo $profile[ 'id' ]; ?>-1" value="<?php echo $profile['webPropertyId'] ?>">
							<input type="hidden" name="<?php echo $profile[ 'id' ]; ?>-1-profile-name" value="<?php echo $profile['name'] ?>">
						<?php } ?>
						<p class="description">Select your website profile for wp-admin edit pages and fron-end pages. Select profile which matches your current WordPress website.</p>
					</td>

				</tr>
				<tr>
					<th width="115"><?php esc_html_e( 'Profile for Dashboard :', 'wp-analytify' );?></th>
					<td width="877">
						<select name='webprofile_dashboard' class="analytify-chosen">
							<?php foreach ($profiles->items as $profile) { ?>
							<option value="<?php echo $profile[ 'id' ];?>"
								<?php selected( $profile[ 'id' ], get_option( 'pt_webprofile_dashboard' )); ?>
								>
								<?php echo $profile[ 'websiteUrl' ];?> - <?php echo $profile[ 'name' ];?>
							</option>
							<?php } ?>
						</select>
						<?php 
						foreach ( $profiles->items as $profile ) { ?>
							<input type="hidden" name="<?php echo $profile[ 'id' ]; ?>" value="<?php echo urlencode(urlencode($profile[ 'websiteUrl' ])); ?>">
							<input type="hidden" name="<?php echo $profile[ 'id' ]; ?>-profile-name" value="<?php echo $profile['name'] ?>">
						<?php } ?>
						<p class="description">Select your website profile for Dashboard Stats. You can select your any Website profile. It will show Analytics for your selected website profile.</p>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<p class="submit">
							<input type="submit" name="save_profile" value="Save Changes" class="button-primary">
						</p>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</form>
	<?php }

	// Choose metrics for posts at admin.
	if( $tab == 'admin' ) { ?>

	<p class="description"><br /><?php esc_html_e( 'Following are the settings for Admin side. Google Analytics will appear under the posts, custom post types or pages.', 'wp-analytify' ); ?></p>

	<form action="" method="post">
		<table width="1004" class="form-table">
			<tbody>
				<tr></tr>
				<tr>
					<th><?php _e( 'Disable Analytics under posts/pages (wp-admin):', 'wp-analytify') ?></th>
					<td>
						<input type="checkbox" name="disable_back" value="1" <?php if ( get_option( 'post_analytics_disable_back') == 1 ) { ?> checked <?php } ?>>
						<p class="description">Check it, If you don't want to load Stats by default on all pages. Remember, There is a section under each post/page. You can still view Stats on pages you want.</p>
					</td>

				</tr>
				<tr>
					<th width="115"><?php esc_html_e( 'Show Analytics to (roles) :', 'wp-analytify' ); ?></th>
					<td>
						<select multiple name="access_role_back[]" class="analytify-chosen" style="width:400px">
							<?php
							if ( !isset( $wp_roles ) ){

								$wp_roles = new WP_Roles();
							}

							$i=0;
							foreach ( $wp_roles->role_names as $role => $name ) {

								if ($role!='subscriber'){

									$i++;

									?>
									<option value="<?php echo $role; ?>"
										<?php

										if ( is_array( get_option( 'post_analytics_access_back' ) ) ) {
											selected( in_array( $role, get_option( 'post_analytics_access_back' ) ) );
										}
										?>>
										<?php echo $name; ?>
									</option>
									<?php
								}
							}
							?>
						</select>
						<p class="description">Show Analytics to above selected user roles only.</p>
					</td>
				</tr>
				<tr>
					<!-- Area For backend settings -->
					<th width="115"><?php esc_html_e( 'Analytics on Post types :' ,'wp-analytify'); ?></th>
					<td>
						<select class="analytify-chosen" name="posts[]" multiple="multiple" style="width:400px">

							<option value="post" <?php if ( is_array( get_option( 'analytify_posts_stats' ) ) ) {
								selected(in_array('post', get_option('analytify_posts_stats')));
							}  ?>
							>Posts</option>
							<option value="page" <?php if ( is_array( get_option( 'analytify_posts_stats' ) ) ) {
								selected(in_array('page', get_option('analytify_posts_stats')));
							}  ?>
							>Pages</option>

						</select>
						<p class="description">Show Analytics under the above post types only. Buy <a href="http://wp-analytify.com/" target="_blank">Premium</a> version for Custom Post Types.</p>
					</td>
				</tr>
				<tr>
					<!-- Area For backend settings -->
					<th width="115"><?php esc_html_e( 'Edit pages/posts Analytics panels:' ); ?></th>
					<td>
						<select class="analytify-chosen" name="backend[]" multiple="multiple" style="width:400px">
							<option value="show-overall-back"
							<?php
							if ( is_array( get_option( 'post_analytics_settings_back' ) ) ) {
								selected(in_array("show-overall-back", get_option('post_analytics_settings_back')));
							}
							?>>
							<?php esc_html_e( 'General Stats' )?>
						</option>
						<option value="show-country-back"
						<?php
						if ( is_array( get_option( 'post_analytics_settings_back' ) ) ) {
							selected( in_array( "show-country-back", get_option( 'post_analytics_settings_back' ) ) );
						}
						?>>
						<?php esc_html_e( 'Country Stats' )?>
					</option>

					<option value="show-keywords-back"
					<?php
					if ( is_array ( get_option( 'post_analytics_settings_back' ) ) ) {
						selected( in_array( "show-keywords-back", get_option( 'post_analytics_settings_back' ) ) );
					}
					?>>
					<?php esc_html_e( 'Keywords Stats' ); ?>
				</option>
				<option value="show-social-back"
				<?php
				if ( is_array ( get_option( 'post_analytics_settings_back' ) ) ) {
					selected( in_array( "show-social-back", get_option( 'post_analytics_settings_back' ) ) );
				}
				?>>
				<?php esc_html_e( 'Social Media Stats' ); ?>
			</option>
			<option value="show-browser-back"
			<?php
			if ( is_array( get_option( 'post_analytics_settings_back' ) ) ) {
				selected( in_array( "show-browser-back", get_option( 'post_analytics_settings_back') ) );
			}
			?>>
			<?php esc_html_e( 'Browser Stats' ); ?>
		</option>
		<option value="show-referrer-back"
		<?php
		if ( is_array( get_option( 'post_analytics_settings_back' ) ) ) {
			selected( in_array( "show-referrer-back", get_option( 'post_analytics_settings_back') ) );
		}
		?>>
		<?php esc_html_e( 'Referrers' ); ?>
	</option>
	<option value="show-pages-back"
	<?php
	if ( is_array( get_option( 'post_analytics_settings_back' ) ) ) {
		selected( in_array( "show-pages-back", get_option( 'post_analytics_settings_back' ) ) );
	}
	?>>
	<?php esc_html_e(' Page bounce and exit stats '); ?>
</option>
<option value="show-mobile-back"
<?php
if ( is_array ( get_option( 'post_analytics_settings_back' ) ) ) {
	selected( in_array( "show-mobile-back", get_option( 'post_analytics_settings_back' ) ) );
}
?>>
<?php esc_html_e( 'Mobile Devices Stats' ); ?>
</option>
<option value="show-os-back"
<?php
if ( is_array ( get_option( 'post_analytics_settings_back' ) ) ) {
	selected( in_array( "show-os-back", get_option( 'post_analytics_settings_back' ) ) );
}
?>>
<?php esc_html_e( 'Operating System Stats' ); ?>
</option>
<option value="show-city-back"
<?php
if ( is_array ( get_option( 'post_analytics_settings_back' ) ) ) {
	selected( in_array( "show-city-back", get_option( 'post_analytics_settings_back' ) ) );
}
?>>
<?php esc_html_e( 'City Stats' ); ?>
</option>
</select>
<p class="description">Select which Stats panels you want to display under posts/pages. Only 'General Stats' will visible in Free Version. Buy <a href="http://wp-analytify.com/" target="_blank">Premium</a> version to see the full statistics.</p>
</td>
</tr>
<tr>
	<th width="115"><?php esc_html_e( 'Exclude Analytics on specific pages:', 'wp-analytify' ); ?></th>
	<td>
		<input type="text" name="exclude_posts_back" id="exclude_posts_back" value="<?php echo get_option('post_analytics_exclude_posts_back'); ?>" class="regular-text" />
		<p class="description">Enter ID's of posts or pages separated by commas on which you don't want to show Analytics e.g 11,45,66</p>
	</td>
</tr>       
<tr>
	<th></th>
	<td>
		<p class="submit">
			<input type="submit" name="save_settings_admin" value="Save Changes" class="button-primary">
		</p>
	</td>
</tr>
</tbody>
</table>
</form>
<?php 
}


	// Advanced Tab section
	if( $tab == 'advanced' ) {
		?>

		<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" name="settings_form" id="settings_form">
			<table width="1004" class="form-table">
				<tbody>
					<tr>
						<td width="877" colspan="2">
						<input type="checkbox" <?php if( get_option( 'ANALYTIFY_USER_KEYS' ) == 'Yes') echo 'checked'; ?> name="auth_step" id="auth_step" value="Yes" />
							<?php echo esc_html_e( 'Do you want to use your own API keys ?', 'wp-analytify' ); ?>
						</td>
					</tr>

					<tr class="user_keys">
						<td colspan="2"><p class="description"> You need to create a Project in Google <a target="_blank" href="https://console.developers.google.com/project">Console</a>. Read this simple 3 minutes <a target="_blank" href="http://wp-analytify.com/google-api-tutorial">tutorial</a> to get your ClientID, Client Secret, Redirect URI and API Key and enter them in below inputs.</p></td>
					</tr>

					<tr class="user_keys">

						<th><?php esc_html_e('ClientID:')?></th>
						<td>
							<input type="text" placeholder="<?php esc_html_e('Your ClientID')?>" name="analytify_clientid" id="analytify_clientid" value="<?php echo get_option('ANALYTIFY_CLIENTID'); ?>" style="width:450px;"/>
						</td>
					</tr>

					<tr class="user_keys">
						<th><?php esc_html_e('Client Secret:')?></th>
						<td>
							<input type="text" placeholder="<?php esc_html_e('Your Client Secret')?>" name="analytify_clientsecret" id="analytify_clientsecret" value="<?php echo get_option('ANALYTIFY_CLIENTSECRET'); ?>" style="width:450px;"/>
						</td>
					</tr>

					<tr class="user_keys">
						<th width="115"><?php esc_html_e( 'API Key:' )?></th>
						<td width="877">
							<input type="text" placeholder="<?php esc_html_e('Your API Key')?>" name="analytify_apikey" id="analytify_apikey" value="<?php echo get_option('ANALYTIFY_DEV_KEY'); ?>" style="width:450px;"/>
							<p class="description">(Optional)</p>
						</td>
					</tr>

					<tr class="user_keys">
						<th width="115"><?php esc_html_e( 'Redirect URI:' )?></th>
						<td width="877">
							<input type="text" placeholder="<?php esc_html_e('Your Redirect URI')?>" name="analytify_redirect_uri" id="analytify_redirect_uri" value="<?php echo get_option('ANALYTIFY_REDIRECT_URI'); ?>" style="width:450px;"/>
							<p class="description">(Redirect URI is very important when you are using your own Keys)</p>
						</td>
					</tr>					

					<tr>
						<th></th>
						<td>
							<p class="submit">
								<input type="submit" class="button-primary" value="Save Changes" name="save_code" />
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<?php
	} // endif

?>

</div>
</div>
<div class="right-area">

<div class="cen" style="margin-bottom:10px;">
	Tweet us <a href="https://twitter.com/wpanalytify" style="text-decoration:none;"> @twitter </a> and Like us <a href="https://fb.com/analytify" style="text-decoration:none;">@facebook</a>
</div>

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
		

			<div class="grids_auto_size wpa_side_box" style="width: 95%;">
				<div class="grid_title cen"> UPGRADE to PRO </div>

				<div class="grid_footer cen" style="background-color:white;">
					<a href="http://wp-analytify.com/upgrade-from-free" title="Analytify Support">Buy Now</a> the PRO version of Analytify and get tons of benefits including premium features, support and updates.
				</div>
			</div>
			<div class="grids_auto_size wpa_side_box" style=" width: 95%;">
				<div class="grid_footer cen">
					made with ♥ by <a href="http://wpbrigade.com" title="WPBrigade | A Brigade of WordPress Developers." />WPBrigade</a>
				</div>
			</div>
		</div>
	</div>
</div>