<?php
/**
 * Analytify page addons file.
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
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
					'url'    		=> 'https://wp-analytify.com/add-ons/woocommerce/?utm_medium=free&utm_source=extensions_page',
					'slug'			=> 'wp-analytify-woocommerce/wp-analytify-woocommerce.php',
					'title'  		=> __( 'Enhaced E-Commerce Tracking for WooCommerce', 'wp-analytify' ),
					'status'		=> '',
					'description'   => __( 'Our WooCommerce Addon takes Analytics into next level. And its absolutely no-setup needed. So you get World Class data on your shop by just enabling an add-on. Sweet!', 'wp-analytify' ),
					),
				'wp_analytify_dash' => (object) array(
					'url'    		=> 'https://wp-analytify.com/',
					'slug'			=> 'wp-analytify-dashboard/wp-analytify-dashboard.php',
					'title'  		=> __( 'Analytify Stats', 'wp-analytify' ) . '<br />' . __( 'on WordPress Dashboard', 'wp-analytify' ),
					'status'		=> 'Coming soon',
					'description'   => __( 'This Add-on dispays your site Analytics as widgets at WordPress Dashboard. <br><br> It is completely Free.', 'wp-analytify' ),
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

				echo '<button class="button-primary">' . __( 'Already Installed', 'wp-analytify' ) . '</button>';

			} else if ( array_key_exists( $slug, $this->plugins_list ) ) {

				echo '<a href="' . admin_url( 'plugins.php' ) . '" class="button-primary">' . __( 'Activate Plugin', 'wp-analytify' ) . '</a>';

			} else if ( is_plugin_inactive( $slug ) ) {

				if ( $extension->status != '' ) {
					echo '<a target="_blank" href="' . $extension->url . '" class="button-primary">' . __( 'Coming Soon', 'wp-analytify' ) . '</a>';
				} else { 					echo '<a target="_blank" href="' . $extension->url . '" class="button-primary">' . __( 'Get this add-on', 'wp-analytify' ) . '</a>'; }
			}
		}
	}

}

$obj_wp_analytify_addons = new WP_Analytify_Addons;
$addons = $obj_wp_analytify_addons->addons();
?>

<div class="wrap">

  <h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img src="<?php echo plugins_url( 'images/wp-analytics-logo.png', dirname( __FILE__ ) );?>" alt=""></span>
	<?php echo __( 'Extend the functionality of Analytify with these awesome Add-ons', 'wp-analytify' ); ?>
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
