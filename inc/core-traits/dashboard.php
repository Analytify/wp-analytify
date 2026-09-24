<?php
/**
 * Core Dashboard Functions for WP Analytify
 *
 * This file contains dashboard-related functions that were previously
 * in wpa-core-functions.php. Functions are kept as standalone functions for
 * simplicity and backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

/**
 * Delete the cache of dashboard sections
 *
 * @since  1.2.6
 * @param string $dashboard_profile_ID Dashboard profile ID.
 * @param string $start_date Start Date.
 * @param string $end_date End Date.
 * @return void
 */
function delete_dashboard_transients( $dashboard_profile_ID, $start_date, $end_date ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Variable name maintained for backward compatibility.

	delete_transient( md5( 'show-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-country-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-city-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-keywords-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-browser-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-os-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-mobile-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-referrer-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-page-stats-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

	delete_transient( md5( 'show-default-overall-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-overall-device-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-overall-dashboard-compare' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-top-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-geographic-countries-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-geographic-cities-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-browser-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-os-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-mobile-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-keyword-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-pages-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-social-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-reffers-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-reffers-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	delete_transient( md5( 'show-default-new-returning-dashboard' . $dashboard_profile_ID . $start_date . $end_date ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
}

/**
 * Dashboard settings backward compatibility till 1.2.5
 *
 * @since 1.2.6
 * @return void
 */
function wpa_dashboard_compatible() {

	$version = get_option( 'wpa_current_version' );

	if ( ! $version ) {
		// Run when version is less or equal than 1.2.5.
		update_option(
			'access_role_dashboard',
			array(
				'administrator',
				'editor',
			)
		);
		update_option(
			'dashboard_panels',
			array(
				'show-real-time',
				'show-overall-dashboard',
				'show-top-pages-dashboard',
				'show-os-dashboard',
				'show-country-dashboard',
				'show-keywords-dashboard',
				'show-social-dashboard',
				'show-browser-dashboard',
				'show-referrer-dashboard',
				'show-page-stats-dashboard',
				'show-mobile-dashboard',
				'show-os-dashboard',
				'show-city-dashboard',
			)
		);

		update_option( 'wpa_current_version', defined( 'ANALYTIFY_VERSION' ) ? ANALYTIFY_VERSION : '1.0.0' );
	}
}
