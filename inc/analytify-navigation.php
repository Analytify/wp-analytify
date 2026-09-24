<?php
/**
 * Navigation File for Analytify Plugin
 *
 * This file contains all dashboard navigation functionality including
 * menu generation, navigation anchors, && submenu markup.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Navigation Methods for Analytify_General Class
 */
trait Analytify_Navigation {

	/**
	 * Create dashboard navigation anchors.
	 *
	 * @param array $nav_item Single navigation item data array.
	 *
	 * @return mixed $anchor
	 */
	private function navigation_anchors( array $nav_item ) {

		$current_screen     = get_current_screen()->base;
		$current_addon_name = '';

		// Check if child dashboard page for addon/module.
		// Sanitize GET parameters for security.
		if ( isset( $_GET['addon'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for display purposes
			$current_addon_name = sanitize_text_field( wp_unslash( $_GET['addon'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for display purposes
		} elseif ( isset( $_GET['show'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for display purposes
			$current_addon_name = sanitize_text_field( wp_unslash( $_GET['show'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for display purposes
		}

		// Initialize variables to prevent undefined variable errors.
		$nav_link   = '';
		$active_tab = '';

		if ( 'pro_feature' === $nav_item['module_type'] ) {
			// Module availbe in pro version as switchable feature.

			$nav_link   = $this->addon_is_active( 'wp-analytify-pro' ) && 'active' === $this->modules[ $nav_item['addon_slug'] ]['status'] ? admin_url( 'admin.php?page=' . $nav_item['page_slug'] ) : admin_url( 'admin.php?page=analytify-promo&addon=' . $nav_item['addon_slug'] );
			$active_tab = ( 'analytify_page_' . $nav_item['page_slug'] === $current_screen || $nav_item['addon_slug'] === $current_addon_name ) ? 'nav-tab-active' : '';
		} elseif ( 'pro_inner' === $nav_item['module_type'] ) {
			// Module build in pro version.

			$nav_link   = $this->addon_is_active( 'wp-analytify-pro' ) ? admin_url( 'admin.php?page=' . $nav_item['page_slug'] . '&show=' . $nav_item['addon_slug'] ) : admin_url( 'admin.php?page=analytify-promo&addon=' . $nav_item['addon_slug'] );
			$active_tab = ( 'analytify_page_' . $nav_item['page_slug'] === $current_screen || $nav_item['addon_slug'] === $current_addon_name ) ? 'nav-tab-active' : '';
		} elseif ( 'pro_addon' === $nav_item['module_type'] ) {
			// Not inner module, rather a seperate plugin.

			$nav_link   = $this->addon_is_active( $nav_item['addon_slug'] ) ? admin_url( 'admin.php?page=' . $nav_item['page_slug'] ) : admin_url( 'admin.php?page=analytify-promo&addon=' . $nav_item['addon_slug'] );
			$active_tab = ( 'analytify_page_' . $nav_item['page_slug'] === $current_screen || $nav_item['addon_slug'] === $current_addon_name ) ? 'nav-tab-active' : '';
		} elseif ( 'free' === $nav_item['module_type'] ) {
			// Free version main dashboard page.

			$nav_link   = admin_url( 'admin.php?page=' . $nav_item['page_slug'] );
			$active_tab = ( 'toplevel_page_' . $nav_item['page_slug'] === $current_screen && empty( $current_addon_name ) ) ? 'nav-tab-active' : '';
		}

		$anchor  = '<a href="' . esc_url( $nav_link ) . '" class="analytify_nav_tab ' . $active_tab . '">' . $nav_item['name'];
		$anchor .= ( isset( $nav_item['sub_name'] ) && ! empty( $nav_item['sub_name'] ) ) ? '<span>' . $nav_item['sub_name'] . '</span>' : '';
		$anchor .= '</a>';

		return $anchor;
	}

	/**
	 * Generate dashboard navigation markup.
	 *
	 * @param array $nav_items Navigation items data array.
	 */
	private function navigation_markup( array $nav_items ) {
		if ( is_array( $nav_items ) && 0 < count( $nav_items ) ) {
			echo '<div class="analytify_nav_tab_wrapper nav-tab-wrapper">';
			echo wp_kses_post( $this->generate_submenu_markup( $nav_items, 'analytify_nav_tab_wrapper', 'analytify_nav_tab_parent' ) );
			echo '</div>';
		}
	}

	/**
	 * Create HTML markup for navigation on dashboard.
	 *
	 * @param array  $nav_items Navigation items data array.
	 * @param string $wrapper_classes Class attribute for navigation wrapper.
	 * @param string $list_item_classes Class attribute for list item.
	 *
	 * @return mixed $markup
	 */
	private function generate_submenu_markup( array $nav_items, $wrapper_classes = false, $list_item_classes = false ) {

		// Hide tabs filter.
		$hide_tabs = apply_filters( 'analytify_hide_dashboard_tabs', array() );

		// Wrapper.
		$markup  = '<ul';
		$markup .= $wrapper_classes ? ' class="' . $wrapper_classes . '"' : '';
		$markup .= '>';

		// Loop over all the menu items.
		foreach ( $nav_items as $items ) {

			// Exclude hidden tabs from dashboard as in filter.
			if ( $hide_tabs && in_array( $items['name'], $hide_tabs, true ) ) {
				continue;
			}

			$markup .= '<li';
			$markup .= $list_item_classes ? ' class="' . $list_item_classes . '"' : '';
			$markup .= '>';

			// generate anchor.
			$markup .= $this->navigation_anchors( $items );

			// check if the menu has children, then call itself to generate the child menu.
			if ( isset( $items['children'] ) && is_array( $items['children'] ) ) {
				$markup .= $this->generate_submenu_markup( $items['children'] );
			}

			$markup .= '</li>';
		}

		// End wrapper.
		$markup .= '</ul>';

		return $markup;
	}

	/**
	 * Register dashboard navigation menu.
	 *
	 * @version 9.0.0
	 */
	public function dashboard_navigation() {

		$nav_items = apply_filters(
			'analytify_filter_navigation_items',
			array(

				array(
					'name'        => 'Audience',
					'sub_name'    => 'Overview',
					'page_slug'   => 'analytify-dashboard',
					'addon_slug'  => 'wp-analytify',
					'module_type' => 'free',
				),

				array(
					'name'        => 'Conversions',
					'sub_name'    => 'All Events',
					'page_slug'   => 'analytify-forms',
					'addon_slug'  => 'wp-analytify-forms',
					'module_type' => 'pro_addon',
					'children'    => array(
						array(
							'name'        => 'Forms Tracking',
							'sub_name'    => 'View Forms Analytics',
							'page_slug'   => 'analytify-forms',
							'addon_slug'  => 'wp-analytify-forms',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'Events Tracking',
							'sub_name'    => 'Affiliates, clicks & links tracking',
							'page_slug'   => 'analytify-events',
							'addon_slug'  => 'events-tracking',
							'module_type' => 'pro_feature',
						),
						array(
							'name'        => 'Video Tracking',
							'sub_name'    => 'Track actions, duration & events',
							'page_slug'   => 'analytify-dashboard',
							'addon_slug'  => 'video-tracking',
							'module_type' => 'pro_inner',
						),
					),
				),

				array(
					'name'        => 'Acquisition',
					'sub_name'    => 'Goals, Campaigns',
					'page_slug'   => 'analytify-campaigns',
					'addon_slug'  => 'wp-analytify-campaigns',
					'module_type' => 'pro_addon',
					'children'    => array(
						array(
							'name'        => 'Search Console',
							'sub_name'    => 'Google Search Console',
							'page_slug'   => 'analytify-dashboard',
							'addon_slug'  => 'search-console-report',
							'module_type' => 'pro_inner',
						),
						array(
							'name'        => 'Campaigns',
							'sub_name'    => 'UTM Overview',
							'page_slug'   => 'analytify-campaigns',
							'addon_slug'  => 'wp-analytify-campaigns',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'Goals',
							'sub_name'    => 'Key Events',
							'page_slug'   => 'analytify-goals',
							'addon_slug'  => 'wp-analytify-goals',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'PageSpeed Insights',
							'sub_name'    => 'Google Web Performance',
							'page_slug'   => 'analytify-dashboard',
							'addon_slug'  => 'page-speed',
							'module_type' => 'pro_inner',
						),
					),
				),

				array(
					'name'        => 'Monetization',
					'sub_name'    => 'Overview',
					'page_slug'   => 'analytify-woocommerce',
					'addon_slug'  => 'wp-analytify-woocommerce',
					'module_type' => 'pro_addon',
					'clickable'   => true,
					'children'    => array(
						array(
							'name'        => 'WooCommerce',
							'sub_name'    => 'eCommerce Stats',
							'page_slug'   => 'analytify-woocommerce',
							'addon_slug'  => 'wp-analytify-woocommerce',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'EDD',
							'sub_name'    => 'Checkout behavior',
							'page_slug'   => 'edd-dashboard',
							'addon_slug'  => 'wp-analytify-edd',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'LifterLMS',
							'sub_name'    => 'Course Stats',
							'page_slug'   => 'analytify-lifterlms',
							'addon_slug'  => 'wp-analytify-lifterlms',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'Paid Memberships Pro',
							'sub_name'    => 'Membership Analytics',
							'page_slug'   => 'analytify-pmpro',
							'addon_slug'  => 'wp-analytify-pmpro',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'LearnDash',
							'sub_name'    => 'Course Analytics',
							'page_slug'   => 'analytify-learndash',
							'addon_slug'  => 'wp-analytify-learndash',
							'module_type' => 'pro_addon',
						),
					),
				),

				array(
					'name'        => 'Engagement',
					'sub_name'    => 'Authors, Dimensions',
					'page_slug'   => 'analytify-authors',
					'addon_slug'  => 'wp-analytify-authors',
					'module_type' => 'pro_addon',
					'children'    => array(
						array(
							'name'        => 'Authors',
							'sub_name'    => 'Authors Content Overview',
							'page_slug'   => 'analytify-authors',
							'addon_slug'  => 'wp-analytify-authors',
							'module_type' => 'pro_addon',
						),
						array(
							'name'        => 'Demographics',
							'sub_name'    => 'Age, Gender & Interests',
							'page_slug'   => 'analytify-dashboard',
							'addon_slug'  => 'detail-demographic',
							'module_type' => 'pro_inner',
						),
						array(
							'name'        => 'Search Terms',
							'sub_name'    => 'On Site Searches',
							'page_slug'   => 'analytify-dashboard',
							'addon_slug'  => 'search-terms',
							'module_type' => 'pro_inner',
						),
						array(
							'name'        => 'Dimensions',
							'sub_name'    => 'Custom Dimensions',
							'page_slug'   => 'analytify-dimensions',
							'addon_slug'  => 'custom-dimensions',
							'module_type' => 'pro_feature',
						),
					),
				),

				array(
					'name'        => 'Real-Time',
					'sub_name'    => 'Live Stats',
					'page_slug'   => 'analytify-dashboard',
					'addon_slug'  => 'detail-realtime',
					'module_type' => 'pro_inner',
				),
			)
		);

		$this->navigation_markup( $nav_items );
	}
}
