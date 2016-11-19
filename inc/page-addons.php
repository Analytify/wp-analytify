<?php

if ( ! class_exists( 'WP_Analytify_Addons' ) ) {

	class WP_Analytify_Addons {

		protected $plugins_list;

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->plugins_list = get_plugins();

		}

		/**
		 * Returns a list of addons
		 *
		 * @return array
		 * @since 1.3
		 */
		public function addons() {

			$addons = array(
				'wp_analytify_woo' 	=> (object) array(
					'url' 		    => 'https://analytify.io/add-ons/woocommerce/?utm_source=wordpress&utm_medium=plugin&utm_campaign=addons_page&utm_content=woocommerce',
					'slug'			 => 'wp-analytify-woocommerce/wp-analytify-woocommerce.php',
					'title' 		  => esc_html__( 'Enhanced E-Commerce Tracking for WooCommerce', 'wp-analytify' ),
					'status'		  => '',
					'description' => esc_html__( 'This Add-on will track the e-commerce behaviour of users, digital Sales, Transactions and Revenues in Google Analytics and Displays Stats in a unique and intiutive way which is very understandable even for non-technical WordPress users.', 'wp-analytify' ),
					),
				'wp_analytify_edd' => (object) array(
						'url' 		    => 'https://analytify.io/add-ons/easy-digital-downloads/?utm_source=wordpress&utm_medium=plugin&utm_campaign=addons_page&utm_content=easy-digital-downloads',
						'slug'			  => 'wp-analytify-edd/wp-analytify-edd.php',
						'title' 		  => esc_html__( 'Enhaced E-Commerce Tracking for Easy Digital Downloads', 'wp-analytify' ),
						'status'		  => '',
						'description' => esc_html__( 'This Add-on will track the e-commerce behaviour of users, digital Sales, Transactions and Revenues in Google Analytics and Displays Stats in a unique and intiutive way which is very understandable even for non-technical WordPress users.', 'wp-analytify' ),
						),
					'wp_analytify_campaings' => (object) array(
						'url' 		    => 'https://analytify.io/add-ons/campaigns/?utm_source=wordpress&utm_medium=plugin&utm_campaign=addons_page&utm_content=campaigns',
						'slug'			  => 'wp-analytify-campaigns/wp-analytify-campaigns.php',
						'title' 		  => esc_html__( 'Campaings Tracking ', 'wp-analytify' ),
						'status'		  => '',
						'description' => esc_html__( 'Everyone runs campaigns through social media or create backlinks. This add-on helps you to show your campaigns stats in a beautiful dashboard.', 'wp-analytify' ),
						),
				'wp_analytify_dash' => (object) array(
					'url' 		    => 'https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/?utm_source=wordpress&utm_medium=plugin&utm_campaign=addons_page&utm_content=google-analytics-dashboard-widget-wordpress',
					'slug'			  => 'wp-analytify-dashboard/wp-analytify-dashboard.php',
					'title' 		  => sprintf( esc_html__( 'Google Analytics Dashboard widget', 'wp-analytify' ), '<br />' ),
					'status'		  => '',
					'description' => sprintf( esc_html__( 'This Add-on dispays Analytics as a widget at WordPress Dashboard. %1$s It is completely Free.', 'wp-analytify' ), '<br /><br />' ),
					),
					
						'wp_analytify_email' => (object) array(
							'url' 		    => 'https://analytify.io/add-ons/email-notifications/?utm_source=wordpress&utm_medium=plugin&utm_campaign=addons_page&utm_content=email-notifications',
							'slug'			  => 'wp-analytify-email/wp-analytify-email.php',
							'title' 		  =>  esc_html__( 'Email Notifications', 'wp-analytify' ),
							'status'		  => '',
							'description' => esc_html__( 'Our Analytify For Email Notifications add-on sends your website analytics reports in email weekly and monthly.', 'wp-analytify' ),
							),
				);

			return $addons;
		}


		/**
		 * Check plugin status
		 *
		 * @return array
		 * @since 1.3
		 */
		public function check_plugin_status( $slug, $extension ) {

			if ( is_plugin_active( $slug ) ) {

				echo sprintf( esc_html__( '%1$s Already Installed %2$s', 'wp-analytify' ), '<button class="button-primary">', '</button>' );

			} else if ( array_key_exists( $slug, $this->plugins_list ) ) {

				$link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
				echo sprintf( esc_html__( '%1$s Activate Plugin %2$s', 'wp-analytify' ), '<a href="' .  $link . '" class="button-primary">', '</a>' );

			} else if ( is_plugin_inactive( $slug ) ) {

				if ( $extension->status != '' ) {
					echo sprintf( esc_html__( '%1$s Download %2$s', 'wp-analytify' ), '<a target="_blank" href="' . $extension->url . '" class="button-primary">', '</a>' ); } else {
					echo sprintf( esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ), '<a target="_blank" href="' . $extension->url . '" class="button-primary">', '</a>' ); }
			}
		}
	}

}

$obj_wp_analytify_addons = new WP_Analytify_Addons;
$addons = $obj_wp_analytify_addons->addons();
?>

<div class="wrap">

	<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url( '../assets/images/wp-analytics-logo.png', __FILE__ );?>" alt=""></span>
	<?php esc_html_e( 'Extend the functionality of Analytify with these awesome Add-ons', 'wp-analytify' ); ?>
	</h2>

	<div class="tabwrapper">
		<?php
		foreach ( $addons as $name => $extension ) :
			?>
			<div class="wp-extension <?php echo $name; ?>">
				<a target="_blank" href="<?php echo $extension->url; ?>">

					<h3><?php echo $extension->title; ?></h3>
				</a>

				<p><?php echo $extension->description; ?></p>
				<p>
					<?php $obj_wp_analytify_addons->check_plugin_status( $extension->slug, $extension ); ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>

</div>
