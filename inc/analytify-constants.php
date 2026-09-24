<?php
/**
 * Analytify Constants File
 *
 * This file contains all constants used throughout the Analytify plugin.
 * Centralizing constants here makes them easier to manage and update.
 *
 * @package WP_Analytify
 * @since 8.0.0
 * @version 9.1.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================================.
// PLUGIN VERSION & IDENTIFICATION.
// ============================================================================.

if ( ! defined( 'ANALYTIFY_VERSION' ) ) {
	define( 'ANALYTIFY_VERSION', '9.1.2' );
}

if ( ! defined( 'WP_ANALYTIFY_PLUGIN_VERSION' ) ) {
	define( 'WP_ANALYTIFY_PLUGIN_VERSION', '9.1.2' );
}

if ( ! function_exists( 'analytify_get_asset_suffix' ) ) {
	/**
	 * Filename suffix for choosing minified vs unminified assets.
	 *
	 * @since 9.1.2
	 * @return string '' when SCRIPT_DEBUG is on, '.min' otherwise.
	 */
	function analytify_get_asset_suffix() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}
}

if ( ! defined( 'WP_ANALYTIFY_ID' ) ) {
	define( 'WP_ANALYTIFY_ID', 'wp-analytify-options' );
}

if ( ! defined( 'ANALYTIFY_NICK' ) ) {
	define( 'ANALYTIFY_NICK', 'Analytify' );
}

if ( ! defined( 'ANALYTIFY_PRODUCT_NAME' ) ) {
	define( 'ANALYTIFY_PRODUCT_NAME', 'Analytify WordPress Plugin' );
}

if ( ! defined( 'WP_ANALYTIFY_USER_META_PREFIX' ) ) {
	define( 'WP_ANALYTIFY_USER_META_PREFIX', 'wp_analytify_' );
}
// Nested key under wp-analytify-authentication for OAuth “connected as” email display.
// Both use defined() guards so site-specific or drop-in code may predefine without PHP notices.
if ( ! defined( 'ANALYTIFY_AUTHENTICATION_OPTION_NAME' ) ) {
	define( 'ANALYTIFY_AUTHENTICATION_OPTION_NAME', 'wp-analytify-authentication' );
}
if ( ! defined( 'ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY' ) ) {
	define( 'ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY', 'google_oauth_connected_email' );
}

// ============================================================================.
// FILE SYSTEM PATHS & URLs.
// ============================================================================.

if ( ! defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ) {
	define( 'WP_ANALYTIFY_PLUGIN_DIR', dirname( __DIR__ ) );
}

if ( ! defined( 'ANALYTIFY_PLUGIN_URL' ) ) {
	define( 'ANALYTIFY_PLUGIN_URL', plugin_dir_url( dirname( __DIR__ ) . '/wp-analytify.php' ) );
}

if ( ! defined( 'WP_ANALYTIFY_ROOT_PATH' ) ) {
	define( 'WP_ANALYTIFY_ROOT_PATH', WP_ANALYTIFY_PLUGIN_DIR );
}

if ( ! defined( 'WP_ANALYTIFY_LIB_PATH' ) ) {
	define( 'WP_ANALYTIFY_LIB_PATH', WP_ANALYTIFY_PLUGIN_DIR . '/lib/' );
}

if ( ! defined( 'WP_ANALYTIFY_LOCAL_DIR' ) ) {
	define( 'WP_ANALYTIFY_LOCAL_DIR', WP_CONTENT_DIR . apply_filters( 'analytify_dir_to_host_analytics', '/uploads/analytify/' ) );
}

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', dirname( __DIR__ ) );
}

// ============================================================================.
// EXTERNAL SERVICE URLs.
// ============================================================================.

if ( ! defined( 'ANALYTIFY_GOOGLE_SEARCH_CONSOLE_API_URL' ) ) {
	define( 'ANALYTIFY_GOOGLE_SEARCH_CONSOLE_API_URL', 'https://www.googleapis.com/webmasters/v3/sites/' );
}

// ============================================================================.
// GOOGLE OAUTH CREDENTIALS & ENDPOINTS.
// ============================================================================.

// Free version OAuth credentials.
if ( get_option( 'wpa_current_version' ) ) { // Pro Keys.
	define( 'WP_ANALYTIFY_CLIENTID', '707435375568-9lria1uirhitcit2bhfg0rgbi19smjhg.apps.googleusercontent.com' );
	define( 'WP_ANALYTIFY_CLIENTSECRET', 'b9C77PiPSEvrJvCu_a3dzXoJ' );
} else { // Free Keys.
	define( 'WP_ANALYTIFY_CLIENTID', '958799092305-7p6jlsnmv1dn44a03ma00kmdrau2i31q.apps.googleusercontent.com' );
	define( 'WP_ANALYTIFY_CLIENTSECRET', 'Mzs1ODgJTpjk8mzQ3mbrypD3' );
}

// OAuth endpoints.
if ( ! defined( 'WP_ANALYTIFY_AUTH_URL' ) ) {
	define( 'WP_ANALYTIFY_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth?' );
}

if ( ! defined( 'WP_ANALYTIFY_REDIRECT' ) ) {
	define( 'WP_ANALYTIFY_REDIRECT', 'https://analytify.io/api/' );
}

if ( ! defined( 'WP_ANALYTIFY_TOKEN_URL' ) ) {
	define( 'WP_ANALYTIFY_TOKEN_URL', 'https://oauth2.googleapis.com/token' );
}
// ============================================================================.
// GOOGLE API SCOPES & PERMISSIONS.
// ============================================================================.

// Basic read & write + identity (id_token + userinfo.email for “connected as …” in settings).
if ( ! defined( 'WP_ANALYTIFY_SCOPE' ) ) {
	define(
		'WP_ANALYTIFY_SCOPE',
		'https://www.googleapis.com/auth/analytics.readonly https://www.googleapis.com/auth/analytics.edit '
		. 'openid email https://www.googleapis.com/auth/userinfo.email'
	);
}

// Full read & write and extra permissions.
if ( ! defined( 'WP_ANALYTIFY_SCOPE_FULL' ) ) {
	define(
		'WP_ANALYTIFY_SCOPE_FULL',
		'https://www.googleapis.com/auth/analytics.readonly https://www.googleapis.com/auth/analytics '
		. 'https://www.googleapis.com/auth/analytics.edit https://www.googleapis.com/auth/webmasters '
		. 'openid email https://www.googleapis.com/auth/userinfo.email'
	);
}

// ============================================================================.
// GOOGLE API ENDPOINTS & KEYS.
// ============================================================================.

if ( ! defined( 'ANALYTIFY_DEV_KEY' ) ) {
	define( 'ANALYTIFY_DEV_KEY', 'AIzaSyDXjBezSlaVMPk8OEi8Vw5aFvteouXHZpI' );
}

if ( ! defined( 'WP_ANALYTIFY_GA_ADMIN_API_BASE' ) ) {
	define( 'WP_ANALYTIFY_GA_ADMIN_API_BASE', 'https://analyticsadmin.googleapis.com/v1alpha' );
}

// v1beta base used for acknowledgeUserDataCollection (required before creating MP secrets).
if ( ! defined( 'WP_ANALYTIFY_GA_ADMIN_API_BASE_V1BETA' ) ) {
	define( 'WP_ANALYTIFY_GA_ADMIN_API_BASE_V1BETA', 'https://analyticsadmin.googleapis.com/v1beta' );
}

// ============================================================================.
// EXTERNAL SERVICES & STORE.
// ============================================================================.

if ( ! defined( 'ANALYTIFY_STORE_URL' ) ) {
	define( 'ANALYTIFY_STORE_URL', 'https://analytify.io' );
}

// ============================================================================.
// LOGGING & DEBUGGING.
// ============================================================================.

// Note: WP_ANALYTIFY_LOG_DIR is defined dynamically in the main class.
// based on WordPress upload directory, but we can provide a default here.
if ( ! defined( 'WP_ANALYTIFY_LOG_DIR' ) ) {
	$upload_dir = wp_upload_dir();
	define( 'WP_ANALYTIFY_LOG_DIR', $upload_dir['basedir'] . '/analytify-logs/' );
}

// ============================================================================.
// TRACKING & ANALYTICS.
// ============================================================================.

// Note: WP_ANALYTIFY_TRACKING_MODE is defined dynamically in authentication trait.
// based on plugin settings.

// ============================================================================.
// PRO VERSION CONSTANTS (if available).
// ============================================================================.

if ( ! defined( 'WP_ANALYTIFY_PRO_ID' ) ) {
	define( 'WP_ANALYTIFY_PRO_ID', 10 );
}

// ============================================================================.
// DASHBOARD WIDGET CONSTANTS.
// ============================================================================.

if ( ! defined( 'WP_ANALYTIFY_DASHBOARD_VERSION' ) ) {
	define( 'WP_ANALYTIFY_DASHBOARD_VERSION', '7.0.0' );
}

if ( ! defined( 'WP_ANALYTIFY_DASHBOARD_ROOT_PATH' ) ) {
	define( 'WP_ANALYTIFY_DASHBOARD_ROOT_PATH', dirname( __DIR__ ) );
}

if ( ! defined( 'WP_ANALYTIFY_WIDGET_PATH' ) ) {
	define( 'WP_ANALYTIFY_WIDGET_PATH', plugin_dir_url( dirname( __DIR__ ) . '/wp-analytify.php' ) );
}

// ============================================================================.
// FORMS ADDON CONSTANTS.
// ============================================================================.

if ( ! defined( 'WP_ANALYTIFY_FORMS_VERSION' ) ) {
	define( 'WP_ANALYTIFY_FORMS_VERSION', '5.0.0' );
}

if ( ! defined( 'WP_ANALYTIFY_FORMS_ADDON_CUSTOM_FORM_CLASS' ) ) {
	define( 'WP_ANALYTIFY_FORMS_ADDON_CUSTOM_FORM_CLASS', 'analytify_form_custom' );
}

// ============================================================================.
// UTILITY FUNCTIONS.
// ============================================================================.

/**
 * Get the appropriate client ID based on whether Pro is active
 *
 * @return string The appropriate client ID
 */
function analytify_get_client_id() {
	if ( get_option( 'wpa_current_version' ) ) {
		return WP_ANALYTIFY_PRO_CLIENTID;
	}
	return WP_ANALYTIFY_CLIENTID;
}

/**
 * Get the appropriate client secret based on whether Pro is active
 *
 * @return string The appropriate client secret
 */
function analytify_get_client_secret() {
	if ( get_option( 'wpa_current_version' ) ) {
		return WP_ANALYTIFY_PRO_CLIENTSECRET;
	}
	return WP_ANALYTIFY_CLIENTSECRET;
}

/**
 * Get the appropriate scope based on whether Pro is active
 *
 * @return string The appropriate scope
 */
function analytify_get_scope() {
	if ( class_exists( 'WP_Analytify_Pro' ) ) {
		return WP_ANALYTIFY_SCOPE_FULL;
	}
	return WP_ANALYTIFY_SCOPE;
}
