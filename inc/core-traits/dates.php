<?php
/**
 * Core Date Functions for WP Analytify
 *
 * This file contains all date and time related functions that were previously
 * in wpa-core-functions.php. Functions are kept as standalone functions for
 * simplicity and backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

/**
 * Validate a dashboard date string (Y-m-d).
 *
 * @since 9.1.0
 * @param string $date Date string.
 * @return bool
 */
function analytify_is_valid_dashboard_date( $date ) {
	if ( ! is_string( $date ) || '' === $date ) {
		return false;
	}

	$parsed = DateTime::createFromFormat( 'Y-m-d', $date );

	return $parsed && $parsed->format( 'Y-m-d' ) === $date;
}

/**
 * Persist the shared dashboard date range across all Analytify admin dashboards.
 *
 * @since 9.1.0
 * @param string      $start_date   Start date (Y-m-d).
 * @param string      $end_date     End date (Y-m-d).
 * @param string|null $date_differ  Preset key (e.g. last_7_days), empty string to clear, or null to leave unchanged.
 * @return void
 */
function analytify_persist_dashboard_dates( $start_date, $end_date, $date_differ = null ) {
	if ( analytify_is_valid_dashboard_date( $start_date ) && analytify_is_valid_dashboard_date( $end_date ) ) {
		update_option( 'analytify_date_start', $start_date );
		update_option( 'analytify_date_end', $end_date );
	}

	if ( null !== $date_differ ) {
		update_option( 'analytify_date_differ', sanitize_text_field( $date_differ ) );
	}
}

/**
 * Apply a saved preset date-differ key to start/end dates.
 *
 * @since 9.1.0
 * @param string $date_differ Preset key.
 * @param string $start_date  Default start date (Y-m-d).
 * @param string $end_date    Default end date (Y-m-d).
 * @return array{start_date: string, end_date: string}
 */
function analytify_apply_dashboard_date_differ( $date_differ, $start_date, $end_date ) {
	if ( 'current_day' === $date_differ ) {
		$start_date = wp_date( 'Y-m-d' );
	} elseif ( 'yesterday' === $date_differ ) {
		$start_date = wp_date( 'Y-m-d', strtotime( '-1 day' ) );
		$end_date   = wp_date( 'Y-m-d', strtotime( '-1 day' ) );
	} elseif ( 'last_7_days' === $date_differ ) {
		$start_date = wp_date( 'Y-m-d', strtotime( '-7 days' ) );
	} elseif ( 'last_14_days' === $date_differ ) {
		$start_date = wp_date( 'Y-m-d', strtotime( '-14 days' ) );
	} elseif ( 'last_30_days' === $date_differ ) {
		$start_date = wp_date( 'Y-m-d', strtotime( '-1 month' ) );
	} elseif ( 'this_month' === $date_differ ) {
		$start_date = wp_date( 'Y-m-01' );
	} elseif ( 'last_month' === $date_differ ) {
		$start_date = wp_date( 'Y-m-01', strtotime( '-1 month' ) );
		$end_date   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
	} elseif ( 'last_3_months' === $date_differ ) {
		$start_date = wp_date( 'Y-m-01', strtotime( '-3 month' ) );
		$end_date   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
	} elseif ( 'last_6_months' === $date_differ ) {
		$start_date = wp_date( 'Y-m-01', strtotime( '-6 month' ) );
		$end_date   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
	} elseif ( 'last_year' === $date_differ ) {
		$start_date = wp_date( 'Y-m-01', strtotime( '-1 year' ) );
		$end_date   = wp_date( 'Y-m-t', strtotime( '-1 month' ) );
	}

	return array(
		'start_date' => $start_date,
		'end_date'   => $end_date,
	);
}

/**
 * This function returns dates for the date picker.
 *
 * @version 9.1.0
 * @since 5.0.4
 * @return array<string, mixed> Array with start_date and end_date
 */
function analytify_datepicker_dates() {

	$wp_analytify = $GLOBALS['WP_ANALYTIFY'];

	$start_date_val = strtotime( '-1 month' );
	$end_date_val   = strtotime( 'now' );
	$start_date     = wp_date( 'Y-m-d', $start_date_val );
	$end_date       = wp_date( 'Y-m-d', $end_date_val );

	/**
	 * Always remember the previously selected date.
	 */
	$_differ = get_option( 'analytify_date_differ' );

	if ( $_differ ) {
		$dates      = analytify_apply_dashboard_date_differ( $_differ, $start_date, $end_date );
		$start_date = $dates['start_date'];
		$end_date   = $dates['end_date'];
	} else {
		$saved_start = get_option( 'analytify_date_start' );
		$saved_end   = get_option( 'analytify_date_end' );

		if ( analytify_is_valid_dashboard_date( $saved_start ) && analytify_is_valid_dashboard_date( $saved_end ) ) {
			$start_date = $saved_start;
			$end_date   = $saved_end;
		}
	}

	/**
	 * Default dates.
	 * $_POST dates are checked incase the Per version is older than 5.0.0.
	 */
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification is handled by caller.
	if ( isset( $_POST['analytify_date_start'] ) && ! empty( $_POST['analytify_date_start'] ) && isset( $_POST['analytify_date_end'] ) && ! empty( $_POST['analytify_date_end'] ) ) {
		$start_date = sanitize_text_field( wp_unslash( $_POST['analytify_date_start'] ) );
		$end_date   = sanitize_text_field( wp_unslash( $_POST['analytify_date_end'] ) );

		$posted_differ = isset( $_POST['analytify_date_diff'] ) ? sanitize_text_field( wp_unslash( $_POST['analytify_date_diff'] ) ) : '';

		if ( analytify_is_valid_dashboard_date( $start_date ) && analytify_is_valid_dashboard_date( $end_date ) ) {
			analytify_persist_dashboard_dates( $start_date, $end_date, $posted_differ );
		}
	}
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	return array(
		'start_date' => $start_date,
		'end_date'   => $end_date,
	);
}

/**
 * Persist shared dashboard dates from any wp-analytify REST request that includes sd/ed.
 *
 * @since 9.1.0
 * @param mixed           $response Result to send.
 * @param array           $handler  Route handler.
 * @param WP_REST_Request $request  Request object.
 * @return mixed
 */
function analytify_persist_dashboard_dates_rest_middleware( $response, $handler, $request ) {
	if ( ! $request instanceof WP_REST_Request ) {
		return $response;
	}

	$route = $request->get_route();
	if ( false === strpos( $route, '/wp-analytify/' ) ) {
		return $response;
	}

	$start_date = $request->get_param( 'sd' );
	$end_date   = $request->get_param( 'ed' );

	if ( ! $start_date || ! $end_date ) {
		return $response;
	}

	$date_differ = $request->get_param( 'd_diff' );
	if ( null === $date_differ ) {
		$date_differ = $request->get_param( 'dd' );
	}

	analytify_persist_dashboard_dates(
		sanitize_text_field( $start_date ),
		sanitize_text_field( $end_date ),
		null !== $date_differ ? sanitize_text_field( $date_differ ) : ''
	);

	return $response;
}

add_action(
	'rest_api_init',
	static function () {
		add_filter( 'rest_request_before_callbacks', 'analytify_persist_dashboard_dates_rest_middleware', 10, 3 );
	},
	20
);
