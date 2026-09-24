<?php
/**
 * Stateless helpers to read Google account email from OAuth id_token / userinfo.
 *
 * @package WP_Analytify
 * @since 9.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read email claim from a Google OAuth id_token (JWT) without verifying the signature.
 * Used only to display which Google account is connected; token came from Google over HTTPS.
 *
 * @since 9.1.0
 * @param string $id_token JWT from the token endpoint.
 * @return string Sanitized email or empty string.
 */
function analytify_parse_google_email_from_id_token( $id_token ) {
	if ( ! is_string( $id_token ) || '' === $id_token ) {
		return '';
	}

	$parts = explode( '.', $id_token );
	if ( count( $parts ) < 2 ) {
		return '';
	}

	$b64 = strtr( $parts[1], '-_', '+/' );
	$pad = strlen( $b64 ) % 4;
	if ( $pad ) {
		$b64 .= str_repeat( '=', 4 - $pad );
	}

	// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- JWT payload decode only.
	$json = base64_decode( $b64, true );
	if ( false === $json ) {
		return '';
	}

	$payload = json_decode( $json, true );
	if ( ! is_array( $payload ) || empty( $payload['email'] ) || ! is_string( $payload['email'] ) ) {
		return '';
	}

	$email = sanitize_email( $payload['email'] );
	return is_email( $email ) ? $email : '';
}

/**
 * Google account email via userinfo (when the access token includes userinfo.email scope).
 *
 * @since 9.1.0
 * @param string $access_token Access token.
 * @return string Sanitized email or empty string.
 */
function analytify_fetch_google_email_from_userinfo( $access_token ) {
	if ( empty( $access_token ) || ! is_string( $access_token ) ) {
		return '';
	}

	$urls = array(
		'https://openidconnect.googleapis.com/v1/userinfo',
		'https://www.googleapis.com/oauth2/v3/userinfo',
	);

	foreach ( $urls as $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			continue;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['email'] ) || ! is_string( $body['email'] ) ) {
			continue;
		}

		$email = sanitize_email( $body['email'] );
		if ( is_email( $email ) ) {
			return $email;
		}
	}

	return '';
}
