<?php
/**
 * Core Utility Functions for WP Analytify
 *
 * This file contains utility functions that were previously in wpa-core-functions.php.
 * Functions are kept as standalone functions for simplicity and backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

/**
 * This function is provided by bcmath extension.
 * and is used by new GA library. check if the
 * function exists if not add our own definition
 * of this function.
 *
 * @since 5.0.3
 */
if ( ! function_exists( 'bccomp' ) ) {
	/**
	 * Fallback implementation of bccomp function.
	 *
	 * @since 5.0.3
	 * @param string $left_operand Left operand.
	 * @param string $right_operand Right operand.
	 * @param int    $scale Scale parameter.
	 * @return int
	 */
	function bccomp( $left_operand, $right_operand, $scale = 0 ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Parameter kept for compatibility with bccomp signature.
		// Implement the bccomp function using regular PHP math operations.
		// Here's a simple example implementation.
		if ( $left_operand > $right_operand ) {
			return 1;
		} elseif ( $left_operand < $right_operand ) {
			return -1;
		} else {
			return 0;
		}
	}
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @since 1.0.0
 * @param string $code JavaScript code.
 * @return void
 */
function wp_analytify_enqueue_js( $code ) {
	global $wpa_queued_js;

	if ( empty( $wpa_queued_js ) ) {
		$wpa_queued_js = '';
	}

	$wpa_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 *
 * @since 1.0.0
 * @return void
 */
function wpa_print_js() {
	global $wpa_queued_js;

	if ( ! empty( $wpa_queued_js ) ) {

		echo "<!-- Analytify footer JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize.
		$wpa_queued_js = wp_check_invalid_utf8( $wpa_queued_js );
		$wpa_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpa_queued_js );
		$wpa_queued_js = str_replace( "\r", '', $wpa_queued_js );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JavaScript code is intentionally output for queued scripts.
		echo $wpa_queued_js . "});\n</script>\n";

		unset( $wpa_queued_js );
	}
}

/**
 * Return classes for dashboard icons.
 *
 * @since 2.0.0
 * @param string $class Class name.
 * @return string Return class.
 */
function pretty_class( $class ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound -- Class parameter name is acceptable.

	if ( 'Google+' === $class ) {
		return 'analytify_google_plus';
	} elseif ( '(not set)' === $class ) {
		return 'analytify_not_set';
	}

	return 'analytify_' . transliterateString( str_replace( array( "'", "'", ' & ', '-', ' ' ), '_', strtolower( $class ) ) );
}

/**
 * Replace special charters with alphabets
 *
 * @since 1.0.0
 * @param string $txt Text to transliterate.
 * @return string
 */
function transliterateString( $txt ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- Function name maintained for backward compatibility.
	$transliteration_table = array( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Variable name maintained for clarity.
		'á' => 'a',
		'Á' => 'A',
		'à' => 'a',
		'À' => 'A',
		'ă' => 'a',
		'Ă' => 'A',
		'â' => 'a',
		'Â' => 'A',
		'å' => 'a',
		'Å' => 'A',
		'ã' => 'a',
		'Ã' => 'A',
		'ą' => 'a',
		'Ą' => 'A',
		'ā' => 'a',
		'Ā' => 'A',
		'ä' => 'ae',
		'Ä' => 'AE',
		'æ' => 'ae',
		'Æ' => 'AE',
		'ḃ' => 'b',
		'Ḃ' => 'B',
		'ć' => 'c',
		'Ć' => 'C',
		'ĉ' => 'c',
		'Ĉ' => 'C',
		'č' => 'c',
		'Č' => 'C',
		'ċ' => 'c',
		'Ċ' => 'C',
		'ç' => 'c',
		'Ç' => 'C',
		'ď' => 'd',
		'Ď' => 'D',
		'ḋ' => 'd',
		'Ḋ' => 'D',
		'đ' => 'd',
		'Đ' => 'D',
		'ð' => 'dh',
		'Ð' => 'Dh',
		'é' => 'e',
		'É' => 'E',
		'è' => 'e',
		'È' => 'E',
		'ĕ' => 'e',
		'Ĕ' => 'E',
		'ê' => 'e',
		'Ê' => 'E',
		'ě' => 'e',
		'Ě' => 'E',
		'ë' => 'e',
		'Ë' => 'E',
		'ė' => 'e',
		'Ė' => 'E',
		'ę' => 'e',
		'Ę' => 'E',
		'ē' => 'e',
		'Ē' => 'E',
		'ḟ' => 'f',
		'Ḟ' => 'F',
		'ƒ' => 'f',
		'Ƒ' => 'F',
		'ğ' => 'g',
		'Ğ' => 'G',
		'ĝ' => 'g',
		'Ĝ' => 'G',
		'ġ' => 'g',
		'Ġ' => 'G',
		'ģ' => 'g',
		'Ģ' => 'G',
		'ĥ' => 'h',
		'Ĥ' => 'H',
		'ħ' => 'h',
		'Ħ' => 'H',
		'í' => 'i',
		'Í' => 'I',
		'ì' => 'i',
		'Ì' => 'I',
		'î' => 'i',
		'Î' => 'I',
		'ï' => 'i',
		'Ï' => 'I',
		'ĩ' => 'i',
		'Ĩ' => 'I',
		'į' => 'i',
		'Į' => 'I',
		'ī' => 'i',
		'Ī' => 'I',
		'ĵ' => 'j',
		'Ĵ' => 'J',
		'ķ' => 'k',
		'Ķ' => 'K',
		'ĺ' => 'l',
		'Ĺ' => 'L',
		'ľ' => 'l',
		'Ľ' => 'L',
		'ļ' => 'l',
		'Ļ' => 'L',
		'ł' => 'l',
		'Ł' => 'L',
		'ṁ' => 'm',
		'Ṁ' => 'M',
		'ń' => 'n',
		'Ń' => 'N',
		'ň' => 'n',
		'Ň' => 'N',
		'ñ' => 'n',
		'Ñ' => 'N',
		'ņ' => 'n',
		'Ņ' => 'N',
		'ó' => 'o',
		'Ó' => 'O',
		'ò' => 'o',
		'Ò' => 'O',
		'ô' => 'o',
		'Ô' => 'O',
		'ő' => 'o',
		'Ő' => 'O',
		'õ' => 'o',
		'Õ' => 'O',
		'ø' => 'oe',
		'Ø' => 'OE',
		'ō' => 'o',
		'Ō' => 'O',
		'ơ' => 'o',
		'Ơ' => 'O',
		'ö' => 'oe',
		'Ö' => 'OE',
		'ṗ' => 'p',
		'Ṗ' => 'P',
		'ŕ' => 'r',
		'Ŕ' => 'R',
		'ř' => 'r',
		'Ř' => 'R',
		'ŗ' => 'r',
		'Ŗ' => 'R',
		'ś' => 's',
		'Ś' => 'S',
		'ŝ' => 's',
		'Ŝ' => 'S',
		'š' => 's',
		'Š' => 'S',
		'ṡ' => 's',
		'Ṡ' => 'S',
		'ş' => 's',
		'Ş' => 'S',
		'ș' => 's',
		'Ș' => 'S',
		'ß' => 'SS',
		'ť' => 't',
		'Ť' => 'T',
		'ṫ' => 't',
		'Ṫ' => 'T',
		'ţ' => 't',
		'Ţ' => 'T',
		'ț' => 't',
		'Ț' => 'T',
		'ŧ' => 't',
		'Ŧ' => 'T',
		'ú' => 'u',
		'Ú' => 'U',
		'ù' => 'u',
		'Ù' => 'U',
		'ŭ' => 'u',
		'Ŭ' => 'U',
		'û' => 'u',
		'Û' => 'U',
		'ů' => 'u',
		'Ů' => 'U',
		'ű' => 'u',
		'Ű' => 'U',
		'ũ' => 'u',
		'Ũ' => 'U',
		'ų' => 'u',
		'Ų' => 'U',
		'ū' => 'u',
		'Ū' => 'U',
		'ư' => 'u',
		'Ư' => 'U',
		'ü' => 'ue',
		'Ü' => 'UE',
		'ẃ' => 'w',
		'Ẃ' => 'W',
		'ẁ' => 'w',
		'Ẁ' => 'W',
		'ŵ' => 'w',
		'Ŵ' => 'W',
		'ẅ' => 'w',
		'Ẅ' => 'W',
		'ý' => 'y',
		'Ý' => 'Y',
		'ỳ' => 'y',
		'Ỳ' => 'Y',
		'ŷ' => 'y',
		'Ŷ' => 'Y',
		'ÿ' => 'y',
		'Ÿ' => 'Y',
		'ź' => 'z',
		'Ź' => 'Z',
		'ž' => 'z',
		'Ž' => 'Z',
		'ż' => 'z',
		'Ż' => 'Z',
		'þ' => 'th',
		'Þ' => 'Th',
		'µ' => 'u',
		'а' => 'a',
		'А' => 'a',
		'б' => 'b',
		'Б' => 'b',
		'в' => 'v',
		'В' => 'v',
		'г' => 'g',
		'Г' => 'g',
		'д' => 'd',
		'Д' => 'd',
		'е' => 'e',
		'Е' => 'E',
		'ё' => 'e',
		'Ё' => 'E',
		'ж' => 'zh',
		'Ж' => 'zh',
		'з' => 'z',
		'З' => 'z',
		'и' => 'i',
		'И' => 'i',
		'й' => 'j',
		'Й' => 'j',
		'к' => 'k',
		'К' => 'k',
		'л' => 'l',
		'Л' => 'l',
		'м' => 'm',
		'М' => 'm',
		'н' => 'n',
		'Н' => 'n',
		'о' => 'o',
		'О' => 'o',
		'п' => 'p',
		'П' => 'p',
		'р' => 'r',
		'Р' => 'r',
		'с' => 's',
		'С' => 's',
		'т' => 't',
		'Т' => 't',
		'у' => 'u',
		'У' => 'u',
		'ф' => 'f',
		'Ф' => 'f',
		'х' => 'h',
		'Х' => 'h',
		'ц' => 'c',
		'Ц' => 'c',
		'ч' => 'ch',
		'Ч' => 'ch',
		'ш' => 'sh',
		'Ш' => 'sh',
		'щ' => 'sch',
		'Щ' => 'sch',
		'ъ' => '',
		'Ъ' => '',
		'ы' => 'y',
		'Ы' => 'y',
		'ь' => '',
		'Ь' => '',
		'э' => 'e',
		'Э' => 'e',
		'ю' => 'ju',
		'Ю' => 'ju',
		'я' => 'ja',
		'Я' => 'ja',
	);
	return str_replace( array_keys( $transliteration_table ), array_values( $transliteration_table ), $txt ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Variable name maintained for clarity.
}

/**
 * Helper function for translation.
 */
if ( ! function_exists( 'analytify__' ) ) {
	/**
	 * Wrapper for __() gettext function.
	 *
	 * @since 1.0.0
	 * @param  string $string     Translatable text string.
	 * @param  string $textdomain Text domain, default: wp-analytify.
	 * @return string
	 */
	function analytify__( $string, $textdomain = 'wp-analytify' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.stringFound -- String parameter name is acceptable.
		return __( $string, $textdomain );	// phpcs:ignore
	}
}

if ( ! function_exists( 'analytify_e' ) ) {
	/**
	 * Wrapper for _e() gettext function.
	 *
	 * @since 1.0.0
	 * @param  string $string     Translatable text string.
	 * @param  string $textdomain Text domain, default: wp-analytify.
	 * @return void
	 */
	function analytify_e( $string, $textdomain = 'wp-analytify' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.stringFound -- String parameter name is acceptable.
		// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain
		echo esc_html( __( $string, $textdomain ) );
	}
}
