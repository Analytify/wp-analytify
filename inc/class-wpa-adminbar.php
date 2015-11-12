<?php


if( ! defined('ABSPATH') ){
	// exit if accessed directly
	exit;
}


class WPA_ADMIN_BAR {

	public function init() {
		add_action( 'admin_bar_menu' , array($this , 'admin_bar_menu') , 90 );
	}


	public function admin_bar_menu($wp_admin_bar) {

		global $tag, $wp_the_query;

		$current_object = $wp_the_query->get_queried_object();

		$wp_admin_bar->add_node(array(
			'id' => 'analytify',
			'title' => '<span class="ab-icon"></span><span id="ab-analytify" class="ab-label">' . esc_html__('Analytify' , 'wp-analytify') . '</span>',
			'href' => get_admin_url(null, 'admin.php?page=analytify-dashboard' . urlencode($_SERVER['REQUEST_URI'])),
			'meta' => array('target' => '_blank', 'title' => __('Analytify' , 'wp-analytify'))
		));

		if ( ( ! empty( $current_object->post_type )
		&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
		&& current_user_can( 'edit_post', $current_object->ID )
		&& $post_type_object->show_ui && $post_type_object->show_in_admin_bar
		&& $edit_post_link = get_edit_post_link( $current_object->ID ) )) {


			$wp_admin_bar->add_menu( array() );

			$wp_admin_bar->add_node(array(
				'parent' => 'analytify',
				'id'     => 'editpage',
				'title'  => 'Edit Post',
				'href'   => $edit_post_link.'#normal-sortables',
				'meta'   => array( 'class' => 'wpa_admin_color')
			) );

				echo '<style>
				#wpadminbar .quicklinks .menupop.hover ul .wpa_admin_color a{
				 color : orange
				 }
				</style>';

		}

			$menus = array(
				'dashboard' => esc_html__( 'Dashboard' , 'wp-analytify' ),
				'campaigns' => esc_html__( 'Campaigns' , 'wp-analytify' ),
				'settings'  => esc_html__( 'Settings' , 'wp-analytify' ),
			);
			foreach ($menus as $id => $title)
			{
				$wp_admin_bar->add_node(array(
					'parent' => 'analytify',
					'id'     => $id,
					'title'  => $title,
					'href'   => get_admin_url(null, 'admin.php?page=analytify-' . $id )

				));
			}

	}

}

$admin_bar = new WPA_ADMIN_BAR();
$admin_bar->init();
 ?>
