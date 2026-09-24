<?php
/**
 * Helpers for optional custom From/To dates on scheduled email reports.
 *
 * Values are stored in the `wp-analytify-email` option (e.g. when the Pro Email UI saves them).
 * Calendar bounds use Gregorian `Y-m-d` strings; comparisons use those strings (and `UTC` in
 * strtotime) so GA date ranges stay aligned with stored values, independent of site timezone.
 *
 * Schedule multi-select uses the legacy option key `analytif_email_cron_time` (missing “y”);
 * {@see analytify_email_get_schedule_cron_time_array()} also checks `analytify_email_cron_time`
 * so mis-keyed copies still work. Existing installs keep using the legacy key.
 *
 * @package WP_Analytify
 * @subpackage Email
 * @since 9.0.0
 */

if ( ! function_exists( 'analytify_email_validate_ymd_option' ) ) {
	/**
	 * Sanitize and validate a stored date string as `Y-m-d` (Gregorian).
	 *
	 * @since 9.0.0
	 *
	 * @param mixed $raw Raw option value.
	 * @return string|false Sanitized `Y-m-d` or false if invalid.
	 */
	function analytify_email_validate_ymd_option( $raw ) {
		if ( ! is_scalar( $raw ) ) {
			return false;
		}

		$value = sanitize_text_field( (string) $raw );
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return false;
		}

		$parts = explode( '-', $value, 3 );
		$year  = isset( $parts[0] ) ? (int) $parts[0] : 0;
		$month = isset( $parts[1] ) ? (int) $parts[1] : 0;
		$day   = isset( $parts[2] ) ? (int) $parts[2] : 0;

		if ( ! wp_checkdate( $month, $day, $year, $value ) ) {
			return false;
		}

		return sprintf( '%04d-%02d-%02d', $year, $month, $day );
	}
}

if ( ! function_exists( 'analytify_email_get_schedule_cron_time_array' ) ) {
	/**
	 * Return the email schedule multi-select settings array.
	 *
	 * @since 9.0.0
	 *
	 * @param object $settings Settings object with `get_option( string, string )`.
	 * @return array<string, mixed>
	 */
	function analytify_email_get_schedule_cron_time_array( $settings ) {
		if ( ! is_object( $settings ) || ! method_exists( $settings, 'get_option' ) ) {
			return array();
		}

		$cron = $settings->get_option( 'analytif_email_cron_time', 'wp-analytify-email' );
		if ( is_array( $cron ) && ! empty( $cron ) ) {
			return $cron;
		}

		$alt = $settings->get_option( 'analytify_email_cron_time', 'wp-analytify-email' );
		return is_array( $alt ) ? $alt : array();
	}
}

if ( ! function_exists( 'analytify_email_get_validated_custom_range_bounds' ) ) {
	/**
	 * Validated From/To dates for email custom range.
	 *
	 * @since 9.0.0
	 *
	 * @param object $settings Settings with `get_option`.
	 * @param bool   $require_ordered When true, start must be on or before end (invalid otherwise).
	 * @return array{start: string, end: string}|null
	 */
	function analytify_email_get_validated_custom_range_bounds( $settings, $require_ordered = false ) {
		if ( ! is_object( $settings ) || ! method_exists( $settings, 'get_option' ) ) {
			return null;
		}

		$start = analytify_email_validate_ymd_option( $settings->get_option( 'analytify_email_custom_range_start', 'wp-analytify-email' ) );
		$end   = analytify_email_validate_ymd_option( $settings->get_option( 'analytify_email_custom_range_end', 'wp-analytify-email' ) );

		if ( false === $start || false === $end ) {
			return null;
		}

		if ( $require_ordered && strcmp( $start, $end ) > 0 ) {
			return null;
		}

		return array(
			'start' => $start,
			'end'   => $end,
		);
	}
}

if ( ! function_exists( 'analytify_email_has_saved_custom_range_dates' ) ) {
	/**
	 * Whether valid custom range start and end dates exist in settings.
	 *
	 * @since 9.0.0
	 *
	 * @param object $settings Analytify settings object implementing `get_option( string, string )`.
	 * @return bool True when both bounds are valid `Y-m-d` strings.
	 */
	function analytify_email_has_saved_custom_range_dates( $settings ) {
		return null !== analytify_email_get_validated_custom_range_bounds( $settings, false );
	}
}

if ( ! function_exists( 'analytify_email_custom_range_current_period' ) ) {
	/**
	 * Map saved day-of-month bounds to the current calendar month (clamped to month length).
	 *
	 * Used for recurring monthly-style emails so the report window follows the chosen calendar days.
	 *
	 * @since 9.0.0
	 *
	 * @param object $settings Analytify settings object implementing `get_option( string, string )`.
	 * @return array{start: string, end: string}|null Start and end as `Y-m-d`, or null if unavailable.
	 */
	function analytify_email_custom_range_current_period( $settings ) {
		$bounds = analytify_email_get_validated_custom_range_bounds( $settings, false );
		if ( null === $bounds ) {
			return null;
		}

		$start = $bounds['start'];
		$end   = $bounds['end'];

		$ts_start = strtotime( $start . ' UTC' );
		$ts_end   = strtotime( $end . ' UTC' );
		if ( ! $ts_start || ! $ts_end ) {
			return null;
		}

		$start_day = (int) gmdate( 'j', $ts_start );
		$end_day   = (int) gmdate( 'j', $ts_end );
		$year      = (int) gmdate( 'Y' );
		$month     = (int) gmdate( 'm' );
		$days_in_m = (int) gmdate( 't' );

		$end_clamped   = min( $end_day, $days_in_m );
		$start_clamped = min( $start_day, $days_in_m );
		if ( $start_clamped > $end_clamped ) {
			$start_clamped = min( $start_day, $end_clamped );
		}

		return array(
			'start' => sprintf( '%04d-%02d-%02d', $year, $month, $start_clamped ),
			'end'   => sprintf( '%04d-%02d-%02d', $year, $month, $end_clamped ),
		);
	}
}

if ( ! function_exists( 'analytify_email_scheduled_report_period' ) ) {
	/**
	 * Dates for scheduled email: absolute From/To when Custom Range is enabled, else day-in-current-month (legacy).
	 *
	 * @param object $settings Settings with `get_option`.
	 * @return array{start: string, end: string}|null
	 */
	function analytify_email_scheduled_report_period( $settings ) {
		if ( ! is_object( $settings ) || ! method_exists( $settings, 'get_option' ) ) {
			return null;
		}

		$cron = analytify_email_get_schedule_cron_time_array( $settings );
		if ( isset( $cron['custom_range'] ) && 'enabled' === $cron['custom_range'] ) {
			$bounds = analytify_email_get_validated_custom_range_bounds( $settings, true );
			return $bounds;
		}

		return analytify_email_custom_range_current_period( $settings );
	}
}
