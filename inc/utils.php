<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Utils Component for WP Analytify
 *
 * This file contains all utility functions and helpers
 * that were previously in the main plugin file.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utils Component Class
 *
 * Static utility functions for the plugin
 */
class Analytify_Utils {

	/**
	 * Format number for display
	 *
	 * @param int $number Number to format.
	 * @return string Formatted number
	 */
	public static function format_number( $number ) {
		if ( $number >= 1000000 ) {
			return round( $number / 1000000, 1 ) . 'M';
		} elseif ( $number >= 1000 ) {
			return round( $number / 1000, 1 ) . 'K';
		}
		return number_format( $number );
	}

	/**
	 * Format time for display
	 *
	 * @param int $seconds Seconds to format.
	 * @return string Formatted time
	 */
	public static function format_time( $seconds ) {
		$hours   = floor( $seconds / 3600 );
		$minutes = floor( ( $seconds % 3600 ) / 60 );
		$secs    = $seconds % 60;

		if ( $hours > 0 ) {
			return sprintf( '%02d:%02d:%02d', $hours, $minutes, $secs );
		} else {
			return sprintf( '%02d:%02d', $minutes, $secs );
		}
	}

	/**
	 * Check if current user can view analytics
	 *
	 * @return bool Whether user can view analytics
	 */
	public static function can_view_analytics() {
		$allowed_roles = get_option( 'wp-analytify-admin' );
		if ( ! $allowed_roles || ! isset( $allowed_roles['show_analytics_roles_back_end'] ) ) {
			return current_user_can( 'manage_options' );
		}

		$user       = wp_get_current_user();
		$user_roles = $user->roles;

		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $allowed_roles['show_analytics_roles_back_end'], true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get current page URL
	 *
	 * @return string Current page URL
	 */
	public static function get_current_page_url() {
		global $wp;
		return home_url( add_query_arg( array(), $wp->request ) );
	}

	/**
	 * Sanitize and validate date
	 *
	 * @param string $date Date string.
	 * @return string|false Sanitized date or false if invalid
	 */
	public static function sanitize_date( $date ) {
		$timestamp = strtotime( $date );
		if ( false === $timestamp ) {
			return false;
		}
		return gmdate( 'Y-m-d', $timestamp );
	}

	/**
	 * Pretty numbers (in thousands) - Legacy function from main file
	 *
	 * @param int $num number.
	 * @return string
	 */
	public static function wpa_pretty_numbers( $num ) {
		return round( ( $num / 1000 ), 2 ) . 'k';
	}

	/**
	 * Format numbers - Legacy function from main file
	 *
	 * @param int $num number.
	 * @return string
	 */
	public static function wpa_number_format( $num ) {
		return number_format( $num );
	}

	/**
	 * Pretty time to display - Legacy function from main file
	 *
	 * @param int $time time.
	 * @return string
	 */
	public static function pa_pretty_time( $time ) {
		// Check if numeric.
		if ( is_numeric( $time ) ) {
			$value = array(
				'years'   => '00',
				'days'    => '00',
				'hours'   => '',
				'minutes' => '',
				'seconds' => '',
			);

			$attach_hours = '';
			$attach_min   = '';
			$attach_sec   = '';

			$time = floor( $time );

			if ( $time >= 31556926 ) {
				$value['years'] = floor( $time / 31556926 );
				$time           = ( $time % 31556926 );
			} //$time >= 31556926

			if ( $time >= 86400 ) {
				$value['days'] = floor( $time / 86400 );
				$time          = ( $time % 86400 );
			} //$time >= 86400
			if ( $time >= 3600 ) {
				$value['hours'] = str_pad( (string) floor( $time / 3600 ), 1, '0', STR_PAD_LEFT );
				$time           = ( $time % 3600 );
			} //$time >= 3600
			if ( $time >= 60 ) {
				$value['minutes'] = str_pad( (string) floor( $time / 60 ), 1, '0', STR_PAD_LEFT );
				$time             = ( $time % 60 );
			} //$time >= 60
			$value['seconds'] = str_pad( (string) floor( $time ), 1, '0', STR_PAD_LEFT );
			// Get the hour:minute:second version.
			if ( '' !== $value['hours'] ) {
				$attach_hours = '<sub>' . _x( 'h', 'Hour Time', 'wp-analytify' ) . '</sub> ';
			}
			if ( '' !== $value['minutes'] ) {
				$attach_min = '<sub>' . _x( 'm', 'Minute Time', 'wp-analytify' ) . '</sub> ';
			}
			if ( '' !== $value['seconds'] ) {
				$attach_sec = '<sub>' . _x( 's', 'Second Time', 'wp-analytify' ) . '</sub>';
			}

			return $value['hours'] . $attach_hours . $value['minutes'] . $attach_min . $value['seconds'] . $attach_sec;
		} else {
			return '';
		}
	}

	/**
	 * Whether the current user is a subscriber blocked from Analytify admin UI.
	 *
	 * @since 9.1.0
	 * @version 9.1.0
	 * @return bool
	 */
	public static function is_subscriber_blocked_from_analytify() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user  = wp_get_current_user();
		$roles = (array) $user->roles;

		return in_array( 'subscriber', $roles, true ) && ! in_array( 'administrator', $roles, true );
	}

	/**
	 * Check current user role - Legacy function from main file
	 *
	 * @since 1.2.2
	 * @version 9.1.0
	 * @param array<string, mixed> $access_level selected access level.
	 * @return boolean
	 */
	public static function pa_check_roles( $access_level ) {

		// Convert string to array if none of the role selected.
		$access_level = (array) $access_level;

		if ( is_user_logged_in() && ! empty( $access_level ) ) {

			if ( self::is_subscriber_blocked_from_analytify() ) {
				return false;
			}

			global $current_user;
			$roles = $current_user->roles;

			if ( array_intersect( $roles, $access_level ) ) {
				return true;
			} elseif ( is_super_admin( $current_user->ID ) && is_multisite() ) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}
}
