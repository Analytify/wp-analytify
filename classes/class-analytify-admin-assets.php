<?php
/**
 * Admin assets deconfliction isolated from utils to keep files small.
 *
 * @package WP_Analytify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove conflicting asset files on specific pages.
 *
 * @param mixed $page The current page identifier.
 * @return void
 */
function analytify_remove_conflicting_asset_files( $page ) {
	if ( 'toplevel_page_analytify-dashboard' !== $page ) {
		return;
	}
	wp_dequeue_script( 'default' );
	wp_dequeue_script( 'bridge-admin-default' );
	wp_dequeue_script( 'gdlr-tax-meta' );
	wp_dequeue_script( 'woosb-backend' );
	wp_deregister_script( 'bf-admin-plugins' );
	wp_dequeue_script( 'bf-admin-plugins' );
	wp_deregister_script( 'unite-ace-js' );
	wp_deregister_script( 'elementor-common' );
	wp_dequeue_script( 'jquery-widgetopts-option-tabs' );
	wp_dequeue_script( 'rml-default-folder' );
	wp_dequeue_script( 'resume_manager_admin_js' );
	if ( class_exists( 'Woocommerce_Pre_Order' ) ) {
		wp_dequeue_script( 'plugin-js' );
	}
	if ( class_exists( 'GhostPool_Setup' ) ) {
		wp_dequeue_script( 'theme-setup' );
	}
	if ( class_exists( 'WPcleverWoobt' ) ) {
		wp_dequeue_script( 'woobt-backend' );
	}
}
add_action( 'admin_enqueue_scripts', 'analytify_remove_conflicting_asset_files', 999 );
