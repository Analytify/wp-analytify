<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure

/**
 * Data sanitization class.
 *
 * This class provides methods for sanitizing and validating data
 * according to specified rules and contexts.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class WPANALYTIFY_Sanitize {

	/**
	 * Sanitize and validate data.
	 *
	 * @param array<string, mixed> $data The data to be sanitized.
	 * @param array<string, mixed> $key_rules The keys in the data and the sanitization rule(s) to apply for each key.
	 * @param string               $context Additional context data for messages.
	 * @return mixed The sanitized data, the data if no key rules supplied or false if an unrecognized rule supplied.
	 */
	public static function sanitize_data( $data, $key_rules, $context ) {
		if ( empty( $data ) || empty( $key_rules ) ) {
			return $data;
		}

		return self::_sanitize_data( $data, $key_rules, $context );
	}

	/**
	 * Internal sanitization method.
	 *
	 * @param array<string, mixed> $data The data to be sanitized.
	 * @param array<string, mixed> $key_rules The keys in the data and the sanitization rule(s) to apply for each key.
	 * @param string               $context Additional context data for messages.
	 * @param int                  $recursion_level The current recursion level to prevent infinite loops.
	 * @return mixed The sanitized data.
	 */
	private static function _sanitize_data( $data, $key_rules, $context, $recursion_level = 0 ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Underscore prefix is intentional for internal method
		if ( empty( $data ) || empty( $key_rules ) ) {
			return $data;
		}

		if ( 0 === $recursion_level && is_array( $data ) ) {
			// We always expect associative arrays.
			if ( ! is_array( $key_rules ) ) {
				// translators: Array Error.
				wp_die( sprintf( esc_html__( '%1$s was not expecting data to be an array.', 'wp-analytify' ), esc_html( $context ) ) );
			}
			foreach ( $data as $key => $value ) {
				// If a key does not have a rule it's not ours and can be removed.
				// We should not fail if there is extra data as plugins like Polylang add their own data to each ajax request.
				if ( ! array_key_exists( $key, $key_rules ) ) {
					unset( $data[ $key ] );
					continue;
				}
				$data[ $key ] = self::_sanitize_data( $value, $key_rules[ $key ], $context, ( $recursion_level + 1 ) );
			}
		} elseif ( is_array( $key_rules ) ) {
			foreach ( $key_rules as $rule ) {
				$data = self::_sanitize_data( $data, $rule, $context, ( $recursion_level + 1 ) );
			}
		} elseif ( 'array' === $key_rules ) {
			// Neither $data or $key_rules are a first level array so can be analysed.
			if ( ! is_array( $data ) ) {
				// translators: Array Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting an array but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), esc_html( $data ) ) );
			}
		} elseif ( 'string' === $key_rules ) {
			if ( ! is_string( $data ) ) {
				// translators: String Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a string but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'key' === $key_rules ) {
			if ( ! is_string( $data ) ) {
				// translators: Key Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a string for key sanitization but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$key_name = sanitize_key( $data );
			if ( $key_name !== $data ) {
				// translators: Key Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a valid key but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), esc_html( $data ) ) );
			}
			$data = $key_name;
		} elseif ( 'text' === $key_rules ) {
			if ( ! is_string( $data ) ) {
				// translators: Text Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a string for text sanitization but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$text = sanitize_text_field( $data );
			if ( $text !== $data ) {
				// translators: Text Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting text but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), esc_html( $data ) ) );
			}
			$data = $text;
		} elseif ( 'serialized' === $key_rules ) {
			if ( ! is_string( $data ) || ! is_serialized( $data ) ) {
				// translators: Serialized data error.
				wp_die( sprintf( esc_html__( '%1$s was expecting serialized data but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'numeric' === $key_rules ) {
			if ( ! is_numeric( $data ) ) {
				// translators: Valid numeric error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a valid numeric but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'int' === $key_rules ) {
			// As we are sanitizing form data, even integers are within a string.
			if ( ! is_numeric( $data ) || (int) $data !== $data ) {
				// translators: Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting an integer but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$data = (int) $data;
		} elseif ( 'positive_int' === $key_rules ) {
			if ( ! is_numeric( $data ) ) {
				// translators: Positive Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a positive number (int) but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$data = (int) $data;
			if ( $data <= 0 ) {
				// translators: Positive Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a positive number (int) but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'negative_int' === $key_rules ) {
			if ( ! is_numeric( $data ) ) {
				// translators: Negative Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a negative number (int) but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$data = (int) $data;
			if ( $data >= 0 ) {
				// translators: Negative Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a negative number (int) but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'zero_int' === $key_rules ) {
			if ( ! is_numeric( $data ) ) {
				// translators: Zero Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting 0 (int) but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$data = (int) $data;
			if ( 0 !== $data ) {
				// translators: Zero Integer Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting 0 (int) but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'empty' === $key_rules ) {
			if ( ! empty( $data ) ) {
				// translators: Empty Value Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting an empty value but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
		} elseif ( 'url' === $key_rules ) {
			if ( ! is_string( $data ) ) {
				// translators: URL Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a string for URL sanitization but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$url = esc_url_raw( $data );
			if ( empty( $url ) ) {
				// translators: URL Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a URL but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), esc_html( $data ) ) );
			}
			$data = $url;
		} elseif ( 'bool' === $key_rules ) {
			if ( ! is_string( $data ) ) {
				// translators: Bool Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a string for bool sanitization but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), is_array( $data ) ? 'array' : esc_html( $data ) ) );
			}
			$bool = sanitize_key( $data );
			if ( empty( $bool ) || ! in_array( $bool, array( 'true', 'false' ), true ) ) {
				// translators: Bool Error.
				wp_die( sprintf( esc_html__( '%1$s was expecting a bool but got something else: "%2$s"', 'wp-analytify' ), esc_html( $context ), esc_html( $data ) ) );
			}
			$data = $bool;
		} else {
			// translators: Unknown Error.
			wp_die( sprintf( esc_html__( 'Unknown sanitization rule "%1$s" supplied by %2$s', 'wp-analytify' ), esc_html( $key_rules ), esc_html( $context ) ) );
		}

		return $data;
	}
}
