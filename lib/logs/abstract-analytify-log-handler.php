<?php
/**
 * Log handling functionality.
 *
 * @class ANALYTIFY_Log_Handler
 * @package Analytify/Abstracts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Abstract Analytify Log Handler Class
 *
 * @version        1.0.0
 * @package        Analytify/Abstracts
 */
abstract class ANALYTIFY_Log_Handler implements ANALYTIFY_Log_Handler_Interface {

	/**
	 * Whether a context array key should be redacted before JSON encoding.
	 *
	 * @param string $key Context key.
	 * @return bool
	 */
	protected static function is_sensitive_log_context_key( $key ) {
		$k = strtolower( (string) $key );

		$exact = apply_filters(
			'analytify_log_sensitive_context_exact_keys',
			array(
				'password',
				'passwd',
				'api_key',
				'apikey',
				'access_token',
				'refresh_token',
				'client_secret',
				'client_id',
				'authorization',
				'bearer',
				'cookie',
				'secret',
				'secret_key',
				'private_key',
				'consumer_secret',
				'consumer_key',
			)
		);

		if ( ! is_array( $exact ) ) {
			$exact = array();
		}

		foreach ( $exact as $token ) {
			if ( strtolower( (string) $token ) === $k ) {
				return true;
			}
		}

		// Common suffix / prefix patterns (avoid bare "secret" substring on unrelated keys).
		if ( preg_match( '/_(token|secret|password|key)$/', $k ) ) {
			return true;
		}
		if ( preg_match( '/^(oauth|auth)_/i', $k ) ) {
			return true;
		}
		if ( preg_match( '/\b(apikey|access_token|refresh_token|client_secret|private_key)\b/i', $k ) ) {
			return true;
		}

		return (bool) apply_filters( 'analytify_is_sensitive_log_context_key', false, $key );
	}

	/**
	 * Recursively replace sensitive context values before they are written to disk.
	 *
	 * @param array<string, mixed> $context Log context.
	 * @return array<string, mixed> Safe context for serialization.
	 */
	protected static function redact_context_for_log( $context ) {
		if ( ! is_array( $context ) ) {
			return array();
		}

		$out = array();
		foreach ( $context as $key => $value ) {
			if ( self::is_sensitive_log_context_key( (string) $key ) ) {
				$out[ $key ] = '[REDACTED]';
				continue;
			}

			if ( is_array( $value ) ) {
				$out[ $key ] = self::redact_context_for_log( $value );
				continue;
			}

			if ( is_string( $value ) && function_exists( 'is_email' ) && is_email( $value )
				&& false !== stripos( (string) $key, 'email' ) ) {
				$out[ $key ] = '[REDACTED EMAIL]';
				continue;
			}

			$out[ $key ] = $value;
		}

		return $out;
	}

	/**
	 * Formats a timestamp for use in log messages.
	 *
	 * @param int $timestamp Log timestamp.
	 * @return string Formatted time for use in log entry.
	 */
	protected static function format_time( $timestamp ) {
		return date( 'c', $timestamp );
	}

	/**
	 * Builds a log entry text from level, timestamp and message.
	 *
	 * @param int    $timestamp Log timestamp.
	 * @param string $level emergency|alert|critical|error|warning|notice|info|debug.
	 * @param string $message Log message.
	 * @param array  $context Additional information for log handlers.
	 *
	 * @return string Formatted log entry.
	 */
	protected static function format_entry( $timestamp, $level, $message, $context ) {
		$time_string  = self::format_time( $timestamp );
		$level_string = strtoupper( $level );
		$entry        = "{$time_string} {$level_string} {$message}";

		$sanitized = is_array( $context ) ? self::redact_context_for_log( $context ) : array();

		// Include context details if present
		if ( ! empty( $sanitized ) ) {
			$context_string = wp_json_encode( $sanitized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			if ( $context_string ) {
				$entry .= " | Context: {$context_string}";
			}
		}

		return apply_filters(
			'analytify_format_log_entry',
			$entry,
			array(
				'timestamp' => $timestamp,
				'level'     => $level,
				'message'   => $message,
				'context'   => $sanitized,
			)
		);
	}
}
