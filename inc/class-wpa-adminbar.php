<?php


if ( ! defined( 'ABSPATH' ) ) {
	// exit if accessed directly.
	exit;
}


class WP_ANALYTIFY_ADMIN_BAR {

	/**
	 * Initialize Admin Bar.
	 *
	 */
	public function init() {
		add_action( 'admin_bar_menu' , array( $this, 'admin_bar_menu' ) , 90 );
	}


	public function admin_bar_menu( $wp_admin_bar ) {

		global $tag, $wp_the_query;
		$current_object = $wp_the_query->get_queried_object();
		$menus = array();

		$is_access_level = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard', array( 'administrator' ) );

		if ( $GLOBALS['WP_ANALYTIFY']->pa_check_roles( $is_access_level ) ) {

			$wp_admin_bar->add_node(array(
				'id'    => 'analytify',
				'title' => '<span class="ab-icon"></span><span id="ab-analytify" class="ab-label">Analytify</span>',
				'href'  => get_admin_url( null, 'admin.php?page=analytify-dashboard' ),
				'meta'  => array( 'title' => __( 'View complete Analytics of your site', 'wp-analytify' ) ),
			));


			$menus['analytify-dashboard'] = esc_html__( 'Dashboard' , 'wp-analytify' );

			$menus = apply_filters( 'analytify_admin_bar_menu', $menus );

			if ( current_user_can( 'manage_options' ) ) {
				$menus['analytify-settings'] = esc_html__( 'Settings' , 'wp-analytify' );
			}

			if ( ( ! empty( $current_object->post_type )
			&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
			&& current_user_can( 'edit_post', $current_object->ID )
			&& $post_type_object->show_ui && $post_type_object->show_in_admin_bar
			&& $edit_post_link = get_edit_post_link( $current_object->ID ) ) ) {

				$wp_admin_bar->add_menu( array() );

				$wp_admin_bar->add_node(array(
					'parent' => 'analytify',
					'id'     => 'editpage',
					'title'  => __( 'Edit Post', 'wp-analytify' ),
					'href'   => $edit_post_link . '#normal-sortables',
					'meta'   => array( 'class' => 'wpa_admin_color' ),
				) );

				echo '<style>
                #wpadminbar .quicklinks .menupop.hover ul .wpa_admin_color a{
                    color : orange
                }
				</style>';

			}

			foreach ( $menus as $id => $title ) {
				$wp_admin_bar->add_node( array(
					'parent' => 'analytify',
					'id'     => $id,
					'title'  => $title,
					'href'   => get_admin_url( null, 'admin.php?page=' . $id ),

				));
			}
		}

	}
}

$admin_bar = new WP_ANALYTIFY_ADMIN_BAR();
$admin_bar->init();
