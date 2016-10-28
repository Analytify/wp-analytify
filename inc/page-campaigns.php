<?php
/**
 * Analytify campaigns file.
 *
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
?>


			<div class="analytify_wraper">
				<div class="analytify_main_title_section">
					<h1 class="analytify_pull_left analytify_main_title"><?php esc_html_e( 'Campaigns Dashboard', 'wp-analytify' ); ?></h1>
					<div class="analytify_select_dashboard analytify_pull_right">
                        <div class="analytify_selected_dashboard_field">Dashboards</div>
                        <ul class="analytify_dashboards_list">
                        	<li><a href="#dashboard_1">REAL TIME</a></li>
                        	<li><a href="#dashboard_2">EDD</a></li>
                        	<li><a href="#dashboard_1">WooCommerce</a></li>
                        	<li><a href="#dashboard_4">Campaigns</a></li>
                        </ul>
                    </div>
				</div>


 

		<?php

		if ( has_action( 'page_campaigns' ) ) {

			do_action( 'page_campaigns' );

		} else {

			?>

			<a target="_blank" class="wrap_pro_section" href="http://analytify.io/upgrade-from-free" title="Upgrade to PRO to enjoy full features of Analytify.">
				<div class="popup">
					<h2>Get PRO Version!</h2>
					<p>Impressed ? <br />This feature is limited to PRO users only.<br/>Click here to see the details.</p>
				</div>
				<div class="background"></div>
					<img class="gray-areas" src="<?php echo esc_url( plugins_url( 'assets/images/campaigns-analytify_pro.png', dirname( __FILE__ ) ) );?>" width="100%" height="auto" alt="Upgrade to PRO to enjoy full features of Analytify." />
			</a>

	    <?php
		}
		?>
	</div>
