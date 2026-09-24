<?php
/**
 * Authentication File for Analytify Plugin
 *
 * This file contains all authentication-related functionality including
 * OAuth connection, token management, refresh tokens, and Google API
 * authentication methods.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/analytify-google-oauth-email-helpers.php';

/**
 * Authentication Methods for Analytify_General Class
 * since 8.0
 */
trait Analytify_Authentication {

	/**
	 * Update authentication date with current timestamp.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	private function analytify_update_authentication_date() {
		$this->auth_date_format = gmdate( 'l jS F Y h:i:s A' ) . ' ' . date_default_timezone_get();
		update_option( 'analytify_authentication_date', $this->auth_date_format );
	}

	/**
	 * Get Google token data from options.
	 *
	 * @since 7.0.0
	 * @return array|false Token data or false if not found
	 */
	public function analytify_get_google_token() {
		if ( empty( $this->google_token ) ) {
			$this->google_token = get_option( 'pa_google_token' );
		}
		return $this->google_token;
	}

	/**
	 * Update Google token data in options and class variable.
	 *
	 * @since 7.0.0
	 * @param array $token_data Token data to save.
	 * @return void
	 */
	private function analytify_update_google_token( $token_data ) {
		$this->google_token = $token_data;
		update_option( 'pa_google_token', $token_data );
	}

	/**
	 * Google OAuth email stored under wp-analytify-authentication (same option as manual GA code).
	 *
	 * @since 9.1.0
	 * @return string
	 */
	protected function analytify_auth_settings_get_google_oauth_email() {
		if ( ! defined( 'ANALYTIFY_AUTHENTICATION_OPTION_NAME' ) || ! defined( 'ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY' ) ) {
			return '';
		}
		$opts = get_option( ANALYTIFY_AUTHENTICATION_OPTION_NAME, array() );
		if ( ! is_array( $opts ) || empty( $opts[ ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY ] )
			|| ! is_string( $opts[ ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY ] ) ) {
			return '';
		}
		$email = sanitize_email( $opts[ ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY ] );
		return is_email( $email ) ? $email : '';
	}

	/**
	 * Persist Google OAuth email into wp-analytify-authentication.
	 *
	 * @since 9.1.0
	 * @param string $email Email address.
	 * @return void
	 */
	protected function analytify_auth_settings_set_google_oauth_email( $email ) {
		if ( ! defined( 'ANALYTIFY_AUTHENTICATION_OPTION_NAME' ) || ! defined( 'ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY' ) ) {
			return;
		}
		if ( ! is_string( $email ) || '' === $email ) {
			return;
		}
		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return;
		}
		$opts = get_option( ANALYTIFY_AUTHENTICATION_OPTION_NAME, array() );
		if ( ! is_array( $opts ) ) {
			$opts = array();
		}
		$opts[ ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY ] = $email;
		update_option( ANALYTIFY_AUTHENTICATION_OPTION_NAME, $opts );
	}

	/**
	 * Remove stored Google OAuth email (e.g. on logout).
	 *
	 * @since 9.1.0
	 * @return void
	 */
	protected function analytify_auth_settings_clear_google_oauth_email() {
		if ( ! defined( 'ANALYTIFY_AUTHENTICATION_OPTION_NAME' ) || ! defined( 'ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY' ) ) {
			return;
		}
		$opts = get_option( ANALYTIFY_AUTHENTICATION_OPTION_NAME, array() );
		if ( ! is_array( $opts ) || ! array_key_exists( ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY, $opts ) ) {
			return;
		}
		unset( $opts[ ANALYTIFY_GOOGLE_OAUTH_EMAIL_KEY ] );
		update_option( ANALYTIFY_AUTHENTICATION_OPTION_NAME, $opts );
	}

	/**
	 * Resolve Google account email from id_token / userinfo and store in wp-analytify-authentication.
	 * Does not store email inside pa_google_token (OAuth blob stays API fields only).
	 *
	 * @since 9.1.0
	 * @param array<string, mixed> $token_data Token row (modified in place: legacy email key removed).
	 * @return void
	 */
	private function analytify_capture_google_oauth_email( array &$token_data ) {
		if ( $this->analytify_auth_settings_get_google_oauth_email() ) {
			unset( $token_data['analytify_google_user_email'] );
			return;
		}

		$email = '';
		if ( ! empty( $token_data['id_token'] ) && is_string( $token_data['id_token'] ) ) {
			$email = analytify_parse_google_email_from_id_token( $token_data['id_token'] );
		}

		if ( ! $email && ! empty( $token_data['access_token'] ) && is_string( $token_data['access_token'] ) ) {
			$email = analytify_fetch_google_email_from_userinfo( $token_data['access_token'] );
		}

		if ( $email ) {
			$this->analytify_auth_settings_set_google_oauth_email( $email );
			delete_transient( 'analytify_google_userinfo_probe_skip' );
		}

		unset( $token_data['analytify_google_user_email'] );
	}

	/**
	 * Resolve email for display: auth settings, legacy token key migration, then JWT on token row.
	 *
	 * @since 9.1.0
	 * @param array<string, mixed> $token Token row (by ref; legacy keys stripped).
	 * @return string
	 */
	private function analytify_resolve_google_oauth_email( array &$token ) {
		$stored = $this->analytify_auth_settings_get_google_oauth_email();
		if ( $stored ) {
			if ( isset( $token['analytify_google_user_email'] ) ) {
				unset( $token['analytify_google_user_email'] );
				$this->google_token = $token;
				update_option( 'pa_google_token', $token );
			}
			return $stored;
		}

		if ( ! empty( $token['analytify_google_user_email'] ) && is_string( $token['analytify_google_user_email'] ) ) {
			$legacy_t = sanitize_email( $token['analytify_google_user_email'] );
			if ( is_email( $legacy_t ) ) {
				$this->analytify_auth_settings_set_google_oauth_email( $legacy_t );
				unset( $token['analytify_google_user_email'] );
				$this->google_token = $token;
				update_option( 'pa_google_token', $token );
				return $legacy_t;
			}
		}

		if ( ! empty( $token['id_token'] ) && is_string( $token['id_token'] ) ) {
			$from_jwt = analytify_parse_google_email_from_id_token( $token['id_token'] );
			if ( $from_jwt ) {
				$this->analytify_auth_settings_set_google_oauth_email( $from_jwt );
				delete_transient( 'analytify_google_userinfo_probe_skip' );
				return $from_jwt;
			}
		}

		return '';
	}

	/**
	 * Google account email for the Authentication tab (wp-analytify-authentication option).
	 * Migrates legacy pa_google_token analytify_google_user_email key when present.
	 *
	 * @since 9.1.0
	 * @return string
	 */
	public function analytify_get_connected_google_account_email() {
		$stored = $this->analytify_auth_settings_get_google_oauth_email();
		if ( $stored ) {
			$tok_clean = get_option( 'pa_google_token' );
			if ( is_array( $tok_clean ) && isset( $tok_clean['analytify_google_user_email'] ) ) {
				unset( $tok_clean['analytify_google_user_email'] );
				$this->google_token = $tok_clean;
				update_option( 'pa_google_token', $tok_clean );
			}
			return $stored;
		}

		$token = get_option( 'pa_google_token' );
		if ( ! is_array( $token ) ) {
			return '';
		}

		$email = $this->analytify_resolve_google_oauth_email( $token );
		if ( $email ) {
			return $email;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}

		if ( get_transient( 'analytify_google_userinfo_probe_skip' ) ) {
			return '';
		}

		$access = $this->analytify_pa_connect_v2();
		$token  = get_option( 'pa_google_token' );
		if ( is_array( $token ) ) {
			$email = $this->analytify_resolve_google_oauth_email( $token );
			if ( $email ) {
				return $email;
			}
		}

		if ( is_string( $access ) && '' !== $access ) {
			$from_api = analytify_fetch_google_email_from_userinfo( $access );
			if ( $from_api ) {
				$this->analytify_auth_settings_set_google_oauth_email( $from_api );
				delete_transient( 'analytify_google_userinfo_probe_skip' );
				return $from_api;
			}
			set_transient( 'analytify_google_userinfo_probe_skip', 1, 6 * HOUR_IN_SECONDS );
		}

		return '';
	}

	/**
	 * Get GA4 streams data from options.
	 *
	 * @since 7.0.0
	 * @return array GA4 streams data
	 */
	public function analytify_get_ga4_streams() {
		if ( empty( $this->ga4_streams ) ) {
			$this->ga4_streams = get_option( 'analytify-ga4-streams', array() );
		}
		return $this->ga4_streams;
	}

	/**
	 * Check the tracking method.
	 *
	 * @return void
	 */
	public function analytify_set_tracking_mode() {
		if ( ! defined( 'WP_ANALYTIFY_TRACKING_MODE' ) ) {
			define( 'WP_ANALYTIFY_TRACKING_MODE', $this->settings->get_option( 'gtag_tracking_mode', 'wp-analytify-advanced', 'gtag' ) );
		}
	}

	/**
	 * Connect with Google Analytics API and get authentication token and save it.
	 * Never logs response body (OAuth responses can contain tokens).
	 *
	 * @since 6.0.0
	 * @version 9.1.0
	 *
	 * @return string|false|null Access token, false on error, or null if no auth code.
	 */
	public function analytify_pa_connect_v2() {
		$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;

		// Retrieve stored token data.
		$token_data    = $this->analytify_get_google_token();
		$auth_code     = get_option( 'post_analytics_token' );
		$refresh_token = ! empty( $token_data['refresh_token'] ) ? $token_data['refresh_token'] : null;
		$expires_in    = isset( $token_data['expires_in'] ) ? (int) $token_data['expires_in'] : 0;
		$token_time    = isset( $token_data['created_at'] ) ? (int) $token_data['created_at'] : 0;

		// Return valid access token if available.
		if ( ! empty( $token_data['access_token'] ) && ( 0 === $expires_in || ( time() - $token_time ) < $expires_in ) ) {
			return $token_data['access_token'];
		}

		// Try refreshing using refresh token.
		if ( ! empty( $refresh_token ) ) {
			$access_token_data = $this->analytify_refresh_access_token( $refresh_token );
			if ( $access_token_data && ! empty( $access_token_data['access_token'] ) ) {
				$this->token = $access_token_data['access_token'];
				return $access_token_data['access_token'];
			}
			return null;
		}

		// Fallback: use authorization code.
		if ( empty( $auth_code ) ) {
			return null;
		}

		try {
			$token_uri          = WP_ANALYTIFY_TOKEN_URL;
			$token_request_data = array(
				'client_id'     => WP_ANALYTIFY_CLIENTID,
				'client_secret' => WP_ANALYTIFY_CLIENTSECRET,
				'code'          => $auth_code,
				'redirect_uri'  => WP_ANALYTIFY_REDIRECT,
				'grant_type'    => 'authorization_code',
				'access_type'   => 'offline',
			);

			$response = wp_remote_post(
				$token_uri,
				array(
					'body'    => $token_request_data,
					'headers' => array( 'Referer' => ANALYTIFY_VERSION ),
				)
			);

			if ( is_wp_error( $response ) ) {
				if ( ! get_transient( 'analytify_token_request_error_logged' ) ) {
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Failed to send token request.',
							array(
								'source'             => 'analytify_pa_connect_v2',
								'error'              => sanitize_text_field( $response->get_error_message() ),
								'token_uri'          => esc_url_raw( $token_uri ),
								'has_auth_code'      => ! empty( $auth_code ),
								'request_grant_type' => sanitize_text_field( $token_request_data['grant_type'] ),
							)
						);
					}
					set_transient( 'analytify_token_request_error_logged', true, 24 * HOUR_IN_SECONDS );
				}
				return false;
			}

			$body              = wp_remote_retrieve_body( $response );
			$access_token_data = json_decode( $body, true );

			if ( ! empty( $access_token_data['access_token'] ) ) {
				$access_token_data['created_at'] = time();
				$this->analytify_capture_google_oauth_email( $access_token_data );
				$this->analytify_update_google_token( $access_token_data );
				$this->analytify_update_authentication_date();
				// Reset email notification flag on successful re-authentication.
				delete_option( 'analytify_token_refresh_failed_email_sent' );
				$this->token = $access_token_data['access_token'];
				return $access_token_data['access_token'];
			} else {
				if ( ! get_transient( 'analytify_token_response_error_logged' ) ) {
					// Do not log response_body (OAuth response can contain access/refresh tokens).
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Access token not found in response.',
							array(
								'source'             => 'analytify_pa_connect_v2',
								'response_code'      => absint( wp_remote_retrieve_response_code( $response ) ),
								'has_auth_code'      => ! empty( $auth_code ),
								'has_refresh_token'  => ! empty( $refresh_token ),
								'token_uri'          => esc_url_raw( $token_uri ),
								'request_grant_type' => sanitize_text_field( $token_request_data['grant_type'] ),
							)
						);
					}
					set_transient( 'analytify_token_response_error_logged', true, 24 * HOUR_IN_SECONDS );
				}
				return false;
			}
		} catch ( Exception $e ) {
			if ( ! get_transient( 'analytify_token_exception_error_logged' ) ) {
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning(
						'Exception during token request: ' . $e->getMessage(),
						array(
							'source'             => 'analytify_pa_connect_v2',
							'exception'          => sanitize_text_field( $e->getMessage() ),
							'token_uri'          => esc_url_raw( $token_uri ),
							'has_auth_code'      => ! empty( $auth_code ),
							'request_grant_type' => sanitize_text_field( $token_request_data['grant_type'] ),
							'trace'              => sanitize_textarea_field( $e->getTraceAsString() ),
						)
					);
				}
				set_transient( 'analytify_token_exception_error_logged', true, 24 * HOUR_IN_SECONDS );
			}
			return false;
		}
	}


	/**
	 * Refreshes the access token using the provided refresh token.
	 *
	 * This function is responsible for obtaining a new access token
	 * by using the given refresh token. It is typically used when the
	 * current access token has expired and needs to be renewed.
	 * Never includes response body in logged error message (may contain tokens).
	 *
	 * @version 9.1.0
	 *
	 * @param string $refresh_token The refresh token used to obtain a new access token.
	 * @return mixed The new access token or an error response if the refresh fails.
	 */
	public function analytify_refresh_access_token( $refresh_token ) {
		$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;

		if ( empty( $refresh_token ) ) {
			return false;
		}

		$token_uri    = WP_ANALYTIFY_TOKEN_URL;
		$request_data = array(
			'client_id'     => WP_ANALYTIFY_CLIENTID,
			'client_secret' => WP_ANALYTIFY_CLIENTSECRET,
			'refresh_token' => $refresh_token,
			'grant_type'    => 'refresh_token',
		);

		$response = wp_remote_post(
			$token_uri,
			array(
				'body'    => $request_data,
				'headers' => array( 'Referer' => ANALYTIFY_VERSION ),
			)
		);

		if ( is_wp_error( $response ) ) {
			if ( ! get_transient( 'analytify_token_error_logged' ) ) {
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning(
						'Failed to refresh access token.',
						array(
							'source'                 => 'analytify_refresh_access_token',
							'error'                  => sanitize_text_field( $response->get_error_message() ),
							'refresh_token_provided' => ! empty( $refresh_token ),
						)
					);
				}
				set_transient( 'analytify_token_error_logged', true, HOUR_IN_SECONDS );
			}
			return false;
		}

		$response_code     = wp_remote_retrieve_response_code( $response );
		$body              = wp_remote_retrieve_body( $response );
		$access_token_data = json_decode( $body, true );

		if ( 200 !== $response_code || empty( $access_token_data['access_token'] ) ) {
			// Do not include response body in error message (may contain tokens).
			$error_message = "HTTP {$response_code}: Failed to refresh access token.";

			// Check if email notification is enabled in Advanced settings.
			$advanced_settings = get_option( 'wp-analytify-advanced', array() );
			$email_enabled     = isset( $advanced_settings['enable_token_refresh_failure_email'] ) && 'on' === $advanced_settings['enable_token_refresh_failure_email'];

			// Send one-time email notification if enabled and not already sent.
			if ( $email_enabled ) {
				$email_already_sent = get_option( 'analytify_token_refresh_failed_email_sent', false );

				if ( ! $email_already_sent ) {
					$site_name = get_bloginfo( 'name' );
					$site_url  = home_url();

					// Default email arguments.
					$default_mail_args = array(
						'to'      => get_option( 'admin_email' ),
						'subject' => sprintf(
							/* translators: %s: Site name */
							__( '[%s] Analytify: Google Analytics Token Refresh Failed', 'wp-analytify' ),
							$site_name
						),
						'message' => sprintf(
							/* translators: 1: Site name, 2: Site URL, 3: Error message, 4: Settings URL */
							__(
								'Hello,

Your Google Analytics token refresh has failed on %1$s (%2$s).

Error details: %3$s

Please re-authenticate your Google Analytics connection in the Analytify settings to restore functionality.

You can access the settings here: %4$s

This is an automated notification from Analytify.',
								'wp-analytify'
							),
							$site_name,
							$site_url,
							wp_kses( $error_message, array() ),
							admin_url( 'admin.php?page=analytify-settings' )
						),
						'headers' => array( 'Content-Type: text/plain; charset=UTF-8' ),
					);

					/**
					 * Filter email arguments for token refresh failure notification.
					 *
					 * @since 8.0.0
					 * @param array  $mail_args     Email arguments. Keys: to, subject, message, headers.
					 * @param string $error_message Error message describing the token refresh failure.
					 * @return array Filtered email arguments with keys: to, subject, message, headers.
					 */
					$mail_args = apply_filters( 'analytify_token_refresh_failed_email_args', $default_mail_args, $error_message );

					// Ensure filter result is an array.
					if ( ! is_array( $mail_args ) ) {
						$mail_args = $default_mail_args;
					}

					// Parse with defaults to ensure all required keys exist.
					$mail_args = wp_parse_args( $mail_args, $default_mail_args );

					// Validate and sanitize recipient(s).
					$recipients = $mail_args['to'];
					if ( is_string( $recipients ) ) {
						// Handle comma-separated emails.
						$recipients = array_map( 'trim', explode( ',', $recipients ) );
					} elseif ( ! is_array( $recipients ) ) {
						$recipients = array();
					}

					// Sanitize each email address and filter out empty/invalid ones.
					$sanitized_recipients = array();
					foreach ( $recipients as $recipient ) {
						$sanitized = sanitize_email( $recipient );
						if ( ! empty( $sanitized ) && is_email( $sanitized ) ) {
							$sanitized_recipients[] = $sanitized;
						}
					}

					// Defensive: Skip wp_mail if no valid recipients.
					if ( empty( $sanitized_recipients ) ) {
						if ( $logger && method_exists( $logger, 'warning' ) ) {
							$logger->warning(
								'Token refresh failure email skipped - no valid recipients.',
								array(
									'source'              => 'analytify_refresh_access_token',
									'original_recipients' => $recipients,
								)
							);
						}
					} else {
						// Sanitize subject.
						$subject = wp_strip_all_tags( $mail_args['subject'] );

						// Validate and sanitize headers.
						$headers = $mail_args['headers'];
						if ( is_string( $headers ) ) {
							$headers = array( $headers );
						} elseif ( ! is_array( $headers ) ) {
							$headers = array();
						}

						// Sanitize header strings.
						$sanitized_headers = array();
						foreach ( $headers as $header ) {
							if ( is_string( $header ) && ! empty( trim( $header ) ) ) {
								$sanitized_headers[] = sanitize_text_field( $header );
							}
						}

						// Convert recipients array to comma-separated string for wp_mail.
						$to = implode( ',', $sanitized_recipients );

						// Send email.
						$email_sent = wp_mail( $to, $subject, $mail_args['message'], $sanitized_headers );

						if ( $email_sent ) {
							// Store flag in separate option only if email succeeds.
							update_option( 'analytify_token_refresh_failed_email_sent', true );
						} elseif ( $logger && method_exists( $logger, 'warning' ) ) {
								$logger->warning(
									'Token refresh failure email failed to send via wp_mail.',
									array(
										'source'     => 'analytify_refresh_access_token',
										'recipients' => $sanitized_recipients,
										'subject'    => $subject,
									)
								);
						}
					}
				}
			}

			if ( ! apply_filters( 'analytify_suppress_default_token_error_log', false, $error_message ) ) {
				if ( ! get_transient( 'analytify_token_error_logged' ) ) {
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Token refresh failed.',
							array(
								'source'        => 'analytify_refresh_access_token',
								'error_message' => sanitize_text_field( $error_message ),
								'response_code' => absint( wp_remote_retrieve_response_code( $response ) ),
							)
						);
					}
					set_transient( 'analytify_token_error_logged', true, DAY_IN_SECONDS );
				}
			}

			return false;
		}

		// Merge with existing token data and save.
		$existing_token_data = $this->analytify_get_google_token();
		if ( ! is_array( $existing_token_data ) ) {
			$existing_token_data = array();
		}
		$updated_token_data = array_merge(
			$existing_token_data,
			array(
				'access_token' => $access_token_data['access_token'],
				'expires_in'   => $access_token_data['expires_in'],
				'created_at'   => time(),
			)
		);

		$this->analytify_capture_google_oauth_email( $updated_token_data );
		$this->analytify_update_google_token( $updated_token_data );
		$this->analytify_update_authentication_date();
		// Reset email notification flag on successful token refresh.
		delete_option( 'analytify_token_refresh_failed_email_sent' );

		return $updated_token_data;
	}

	/**
	 * Get a fresh access token.
	 *
	 * @since 7.0.0
	 */
	public function analytify_get_fresh_access_token() {
		// Load the token from your storage.
		$auth_token = $this->client->getAccessToken();

		// Extract the created time and expires_in value.
		$created_time = $auth_token['created'];
		$expires_in   = $auth_token['expires_in'];

		// Get the current time.
		$current_time = time();

		// Check if the token has expired.
		if ( ( $created_time + $expires_in ) < $current_time ) {
			// Token has expired, refresh it.
			if ( $this->client->isAccessTokenExpired() ) {
				$this->client->fetchAccessTokenWithRefreshToken( $this->client->getRefreshToken() );

				// Save the new token to your storage.
				$new_token = $this->client->getAccessToken();
			}
		}

		// Return the access token (fresh or existing).
		$auth_token = $this->client->getAccessToken();
		return $auth_token['access_token'];
	}
}
