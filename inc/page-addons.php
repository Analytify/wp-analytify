<?php

if ( ! class_exists( 'WP_Analytify_Addons' ) ) {

	class WP_Analytify_Addons {

		protected $plugins_list;
		protected $modules_list;

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->plugins_list = get_plugins();
			$this->modules_list = $this->modules();
		}

		/**
		 * Returns a list of addons
		 *
		 * @return array
		 * @since 1.3
		 */
		 public function addons() {

			// For Testing
			 delete_transient( 'analytify_api_addons' );

			 // Get the transient where the addons are stored on-site.
			 $data = get_transient( 'analytify_api_addons' );

			 // If we already have data, return it.
			 if ( ! empty( $data ) )
			 return $data;

			 // Make sure this matches the exact URL from your site.
			 $url = 'https://analytify.io/wp-json/analytify/v1/plugins';

			 $wp_request_headers = array(
				//'Authorization' => 'Basic ' . base64_encode( 'dev:dev' )
			 );

			 // Get data from the remote URL.
			 $response = wp_remote_get( $url, array( 'timeout' => 20, 'headers' => $wp_request_headers ) );

			 if ( ! is_wp_error( $response ) ) {

				 // Decode the data that we got.
				 $data = json_decode( wp_remote_retrieve_body( $response ) );

				 if ( ! empty( $data ) && is_array( $data ) ) {

					 // Store the data for a week.
					 set_transient( 'analytify_api_addons', $data, 7 * DAY_IN_SECONDS );

					 return $data;
				 }
			 }

			return array();

		 }

		/**
		 * Check plugin status
		 *
		 * @return array
		 * @since 1.3
		 */
		public function check_plugin_status( $slug, $extension ) {

			// Free addon has different filename.
			$addon_file_name = ( 'analytify-analytics-dashboard-widget' === $slug ) ? 'wp-analytify-dashboard' : $slug;
			$slug = $slug . '/'. $addon_file_name .'.php';

			if ( is_plugin_active( $slug ) ) {

				echo sprintf( esc_html__( '%1$s Deactivate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-deactivate-module" data-slug="'. $slug .'" data-set-state="deactive" data-internal-module="false">', '</button>' );

			} else if ( array_key_exists( $slug, $this->plugins_list ) ) {

				$link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
				
				echo sprintf( esc_html__( '%1$s Activate add-on %2$s', 'wp-analytify' ), '<a href="' .  $link . '" class="button-primary analytify-module-state analytify-activate-module" data-slug="'. $slug .'" data-set-state="active" data-internal-module="false" >', '</a>' );

			} else if ( is_plugin_inactive( $slug ) ) {

				if ( isset( $extension->status ) &&	$extension->status != '' ) {
					echo sprintf( esc_html__( '%1$s Download %2$s', 'wp-analytify' ), '<a target="_blank" href="' . $extension->url . '" class="button-primary">', '</a>' ); 
				} else {
					echo sprintf( esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ), '<a target="_blank" href="' . $extension->url . '" class="button-primary">', '</a>' ); 
				}
			}
		}

		/**
		 * Check if pro version is supporitng modules.
		 *
		 * @return bool
		 */
		function check_pro_support() {

			if ( class_exists( 'WP_Analytify_Pro' )  ) {
				$plugins = get_plugins();

				if ( version_compare( $plugins['wp-analytify-pro/wp-analytify-pro.php']['Version'], '4.0', '>=' ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Returns a list of modules.
		 *
		 * @return array
		 */
		public function modules() {

			if ( get_option( 'wp_analytify_modules' ) ) {
				return get_option( 'wp_analytify_modules' );
			}

			return array();
		}

		/**
		 * Check module status.
		 *
		 */
		public function check_module_status( $slug ) {

			if ( 'active' === $this->modules_list[$slug]['status'] && $this->check_pro_support() ) {

				echo sprintf( esc_html__( '%1$s Deactivate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-deactivate-module" data-slug="'. $slug .'" data-set-state="deactive" data-internal-module="true">', '</button>' );

			} else if ( ! $this->modules_list[$slug]['status'] && $this->check_pro_support() ) {

				echo sprintf( esc_html__( '%1$s Activate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-activate-module" data-slug="'. $slug .'" data-set-state="active" data-internal-module="true">', '</button>' );

			} else {

				echo sprintf( esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ), '<a type="button" class="button-primary analytify-activate-module" href=" ' . $this->modules_list[$slug]['url'] . '" target="_blank">', '</a>' );

			}
		}

		/**
		 *  Loaders HTML.
		 *
		 * @param string $addon Addon name.
		 * @param string $logo Logo url.
		 * @return string $html HTML markup string.
		 */
		function loaders( $addon, $logo ) {

			$html =	'<div class="analytify-addons-loader-container"><div class="wp-analytify-addon-enable analytify-loader" style="display:none;">
						<div class="analytify-logo-container">
						<img src="' .  $logo . '" alt="'. $addon .'">
						<svg class="circular-loader" viewBox="25 25 50 50" >
						<circle class="loader-path" cx="50" cy="50" r="18" fill="none" stroke="#d8d8d8" stroke-width="1" />
						</svg>
						</div>
						<p>' .  __( "Activating...", "analytify" ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-install analytify-loader activated" style="display:none">
						<svg class="circular-loader2" viewBox="25 25 50 50" >
						<circle class="loader-path2" cx="50" cy="50" r="18" fill="none" stroke="#00c853" stroke-width="1" />
						</svg>
						<div class="checkmark draw"></div>
						<p>' . __( 'Activated', 'analytify' ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-uninstalling analytify-loader activated" style="display:none">
						<div class="analytify-logo-container">
						<img src="' .  $logo . '" alt="'. $addon .'">
						<svg class="circular-loader" viewBox="25 25 50 50" >
						<circle class="loader-path" cx="50" cy="50" r="18" fill="none" stroke="#d8d8d8" stroke-width="1" />
						</svg>
						</div>
						<p>' .  __( "Deactivating...", "analytify" ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-uninstall analytify-loader activated" style="display:none">
						<svg class="circular-loader2" viewBox="25 25 50 50" >
						<circle class="loader-path2" cx="50" cy="50" r="18" fill="none" stroke="#ff0000" stroke-width="1" />
						</svg>
						<div class="checkmark draw"></div>
						<p>' . __( 'Deactivated', 'analytify' ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-wrong activated analytify-loader" style="display:none">
						<svg class="checkmark_login" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
						<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"></circle>
						<path class="checkmark__check" stroke="#ff0000" fill="none" d="M16 16 36 36 M36 16 16 36"></path>
						</svg>
						<p>' . __( 'Something Went Wrong', 'analytify' ) . '</p>
						</div></div>';

			return $html;
    	}
	}
}

$obj_wp_analytify_addons = new WP_Analytify_Addons;
$addons = $obj_wp_analytify_addons->addons();
$modules = $obj_wp_analytify_addons->modules();
$current_screen = get_current_screen()->base; 
$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION; ?>

<div class="wpanalytify analytify-dashboard-nav">
	<div class="wpb_plugin_wraper">
		<div class="wpb_plugin_header_wraper">
			<div class="graph"></div>
			<div class="wpb_plugin_header">
				<div class="wpb_plugin_header_title"></div>
				<div class="wpb_plugin_header_info">
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">Changelog - v<?php echo $version; ?></a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="<?php echo ANALYTIFY_PLUGIN_URL . '/assets/images/logo.svg'?>" alt="Analytify">
				</div>
			</div>
		</div>
				
		<div class="analytify-settings-body-container">
			<div class="wpb_plugin_body_wraper">
				<div class="wpb_plugin_body">
					<div class="wpa-tab-wrapper">
						<ul class="analytify_nav_tab_wrapper nav-tab-wrapper">
							<li><a href="<?php echo admin_url( 'admin.php?page=analytify-addons' ) ?>" class="analytify_nav_tab <?php echo ( 'analytify_page_analytify-addons' === $current_screen ) ? 'nav-tab-active' : '' ?>" >Addons</a></li>
							<li><a href="<?php echo admin_url( 'admin.php?page=analytify-settings#wp-analytify-license' ) ?>" class="analytify_nav_tab" >License</a></li>
						</ul>
					</div>

					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
						<div class="wrap analytify-addons-wrapper">
 
							<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url( '../assets/images/wp-analytics-logo.png', __FILE__ );?>" alt=""></span>
							<?php esc_html_e( 'Extend the functionality of Analytify with these awesome Add-ons', 'wp-analytify' ); ?>
							</h2>

							<div class="tabwrapper">

								<?php	
								foreach ( $addons as $name => $extension ) : ?>

									<div class="wp-extension <?php echo $name; ?>">
										<a target="_blank" href="<?php echo $extension->url; ?>">
											<h3 style="background-image: url(<?php echo $extension->media->icon->url ?>);"><?php echo $extension->title; ?></h3>
										</a>
										<p><?php echo wpautop( wp_strip_all_tags( $extension->excerpt ) ) ?></p>
										<p><?php $obj_wp_analytify_addons->check_plugin_status( $extension->slug, $extension ); ?></p>

										<?php echo $obj_wp_analytify_addons->loaders( $name, $extension->media->icon->url ); ?>

									</div>

								<?php endforeach; ?>

								<?php foreach ( $modules as $module ) { ?>

										<div class="wp-extension <?php echo $module['slug']; ?>">
											<a target="_blank" href="<?php echo $module['url']; ?>">
												<h3 style="background-image: url(<?php echo $module['image'] ?>);"><?php echo $module['title']; ?></h3>
											</a>
											<p><?php echo $module['description'] ?></p>
											<p><?php $obj_wp_analytify_addons->check_module_status( $module['slug'] ); ?></p>

											<?php echo $obj_wp_analytify_addons->loaders( $module['title'], $module['image'] ); ?>

										</div>
											
									<!-- #TDOD: Fetch from API -->
									<!-- <div class="wp-extension wp-analytify-forms-dashboard">
										<a target="_blank" href="#">
											<h3 style="background-image: url(https://analytify.io/wp-content/uploads/edd/2019/04/dashboard_icon.png);">Forms</h3>
										</a>
										<p>This Add-on will track custom forms in a unique and intiutive way which is very understandable even for non-technical WordPress users</p>
										<p><?php // $obj_wp_analytify_addons->check_plugin_status( 'wp-analytify-forms', '' ); ?></p>

										<?php // echo $obj_wp_analytify_addons->loaders( 'Forms', 'https://analytify.io/wp-content/uploads/edd/2019/04/dashboard_icon.png' ); ?>
									</div> -->

									<!-- #TDOD: Fetch from API -->
									<!-- <div class="wp-extension wp-analytify-authors">
										<a target="_blank" href="#">
											<h3 style="background-image: url(https://analytify.io/wp-content/uploads/edd/2019/04/dashboard_icon.png);">Authors Dashboard</h3>
										</a>
										<p>This Add-on will show authors dashboard in a unique and intiutive way which is very understandable even for non-technical WordPress users.</p>
										<p><?php // $obj_wp_analytify_addons->check_plugin_status( 'wp-analytify-authors', '' ); ?></p>

										<?php // echo $obj_wp_analytify_addons->loaders( 'Authors Dashboard', 'https://analytify.io/wp-content/uploads/edd/2019/04/dashboard_icon.png' ); ?>
									</div> -->
									
								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>