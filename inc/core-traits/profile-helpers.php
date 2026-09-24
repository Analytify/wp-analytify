<?php
/**
 * Core Profile and Helper Functions for WP Analytify
 *
 * This file contains profile and helper functions that were previously
 * in wpa-core-functions.php. Functions are kept as standalone functions for
 * simplicity and backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

/**
 * Get Analytify site link with refferal data.
 *
 * @param string $url Page url default set to pricing page.
 * @param string $campaing_url Campaings parameters.
 * @since 2.1.21
 * @return string
 */
function analytify_get_update_link( $url = '', $campaing_url = '' ) {

	if ( defined( 'ANALYTIFY_AFFILIATE_ID' ) ) {
		$ref_id = ANALYTIFY_AFFILIATE_ID;
	}

	if ( '' === $url ) {
		$url = 'https://analytify.io/pricing/';
	}

	if ( empty( $ref_id ) ) {
		return $url . $campaing_url;
	}

	return $url . 'ref/' . $ref_id . '/' . $campaing_url;
}

/**
 * Ignore tracking if user excluded.
 *
 * @since 2.1.21
 * @return bool
 */
function analytify_is_track_user() {

	global $current_user;

	$roles    = isset( $current_user->roles ) ? $current_user->roles : array();
	$is_track = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'exclude_users_tracking', 'wp-analytify-profile', array() );

	// If user is logged in, has at least one role, and that role is in the exclude list.
	if ( is_user_logged_in() && ! empty( $roles ) && in_array( $roles[0], $is_track, true ) ) {
		return false;
	}

	return true;
}

/**
 * Add custom admin notice
 *
 * @param string $message Custom Message.
 * @param string $class wp-analytify-success,wp-analytify-danger.
 * @since 2.1.22
 * @return void
 */
function analytify_notice( $message, $class = 'wp-analytify-success' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.classFound -- Class parameter name is acceptable.

	// Only show notices on allowed pages.
	$screen        = get_current_screen();
	$allowed_pages = array(
		'analytify-dashboard',
		'plugins',
		'analytify-settings',
		'analytify-license',
		'analytify-addons',
		'dashboard', // WordPress admin dashboard.
	);

	// Check if we're on an allowed page.
	$is_allowed = false;
	if ( $screen ) {
		// Check parent base (for submenu pages).
		if ( in_array( $screen->parent_base, $allowed_pages, true ) ) {
			$is_allowed = true;
		}
		// Check if it's the plugins page.
		if ( 'plugins' === $screen->id ) {
			$is_allowed = true;
		}
		// Check if it's the WordPress admin dashboard.
		if ( 'dashboard' === $screen->id ) {
			$is_allowed = true;
		}
		// Check if it's an Analytify page by checking the page parameter.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is only checking a page parameter for display logic, not processing form data.
		if ( isset( $_GET['page'] ) && strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'analytify' ) === 0 ) {
			$is_allowed = true;
		}
	}

	// Don't show notice if not on allowed page.
	if ( ! $is_allowed ) {
		return;
	}

	$notice_id = 'analytify_notice_' . md5( $message );
	if ( ! get_option( $notice_id ) ) {
		echo '<div class="wp-analytify-notification ' . esc_attr( $class ) . '" id="' . esc_attr( $notice_id ) . '">
				<a class="wp-analytify-notice-dismiss" href="#" aria-label="Dismiss the notice" onclick="dismissAnalytifyNotice(\'' . esc_js( $notice_id ) . '\'); return false;">&times;</a>
				<div class="wp-analytify-notice-icon">
					<img src="' . esc_url( defined( 'ANALYTIFY_PLUGIN_URL' ) ? ANALYTIFY_PLUGIN_URL : '' ) . 'assets/img/notice-logo.svg" alt="notice">
				</div>
				<div class="wp-analytify-notice-description">
					<p>' . wp_kses_post( $message ) . '</p>
				</div>
			</div>';
	}
}

add_action( 'admin_footer', 'analytify_notice_script' );
/**
 * Output notice dismiss script.
 *
 * @return void
 */
function analytify_notice_script() {
	?>
	<script type="text/javascript">
		function dismissAnalytifyNotice(noticeId) {
			document.getElementById(noticeId).style.display = 'none';
			var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
			var data = {
				'action': 'dismiss_analytify_notice',
				'notice_id': noticeId
			};
			jQuery.post(ajaxurl, data, function(response) {
				console.log(response);
			});
		}

		// Reposition all Analytify notices below the page title inside .wrap.
		(function($){
			$(function(){
				var $wrap  = $('.wrap').first();
				var $title = $wrap.find('h1').first();

				if (!$wrap.length || !$title.length) {
					return;
				}

				$('.wp-analytify-notification').each(function(){
					var $n = $(this);
					// Only move notices not already inside .wrap.
					if ($n.closest('.wrap').length === 0) {
						$n.insertAfter($title);
					}
				});
			});
		})(jQuery);
	</script>
	<?php
}

add_action( 'wp_ajax_dismiss_analytify_notice', 'dismiss_analytify_notice' );
/**
 * Dismiss Analytify notice callback.
 *
 * @return void
 */
function dismiss_analytify_notice() {
	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'You do not have permission to perform this action.', 'wp-analytify' ) );
		return;
	}

	// Verify nonce for security.
	if ( ! check_ajax_referer( 'dismiss_analytify_notice', 'nonce' ) ) {
		wp_send_json_error( __( 'Security check failed.', 'wp-analytify' ) );
		return;
	}

	if ( isset( $_POST['notice_id'] ) ) {
		$notice_id = sanitize_text_field( wp_unslash( $_POST['notice_id'] ) );
		set_transient( $notice_id . '_dismissed', time(), 86400 );
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}

/**
 * Get logger instance.
 *
 * @return mixed
 */
function analytify_get_logger() {
	static $logger = null;

	$class = apply_filters( 'woocommerce_logging_class', 'ANALYTIFY_Logger' );

	if ( null === $logger || ! is_a( $logger, $class ) ) {
		if ( class_exists( $class ) ) {
			$implements = class_implements( $class );

			if ( is_array( $implements ) && ( in_array( 'ANALYTIFY_Logger_Interface', $implements, true ) || interface_exists( 'ANALYTIFY_Logger_Interface' ) ) ) {
				if ( is_object( $class ) ) {
					$logger = $class;
				} else {
					$logger = new $class();
				}
			}
		}
	}

	return $logger;
}

/**
 * Log a message with the Analytify logger if available (DRY helper).
 *
 * @param string               $level   Log level: 'warning', 'error', 'info', etc.
 * @param string               $message Message to log.
 * @param array<string, mixed> $context Optional context array.
 * @since 9.0.0
 * @return void
 */
function analytify_log( $level, $message, $context = array() ) {
	$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;
	if ( $logger && method_exists( $logger, $level ) ) {
		$logger->$level( $message, $context );
	}
}

/**
 * Registers the default log handler.
 *
 * @since 2.1.23
 * @param array<string, mixed> $handlers Handlers.
 * @return array<string, mixed>
 */
function analytify_register_default_log_handler( $handlers ) {
	if ( defined( 'ANALYTIFY_LOG_HANDLER' ) && class_exists( ANALYTIFY_LOG_HANDLER ) ) {
		$handler_class   = ANALYTIFY_LOG_HANDLER;
		$default_handler = new $handler_class();
	} elseif ( class_exists( 'ANALYTIFY_Log_Handler_File' ) ) {
			$default_handler = new ANALYTIFY_Log_Handler_File();
	} else {
		$default_handler = null;
	}

	if ( null !== $default_handler ) {
		array_push( $handlers, $default_handler );
	}

	// Convert to associative array with string keys.
	$result = array();
	foreach ( $handlers as $key => $handler ) {
		$result[ is_string( $key ) ? $key : (string) $key ] = $handler;
	}
	return $result;
}
add_filter( 'analytify_register_log_handlers', 'analytify_register_default_log_handler' );

/**
 * Remove non-Analytify notices from Analytify page.
 *
 * @since 2.1.23
 * @return void
 */
function hide_non_analytify_notice() {
	// Return if not Analytify page.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reading URL parameter for page check.
	if ( empty( $_REQUEST['page'] ) || false === strpos( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ), 'analytify' ) ) {
		return;
	}

	global $wp_filter;
	if ( ! empty( $wp_filter['user_admin_notices']->callbacks ) && is_array( $wp_filter['user_admin_notices']->callbacks ) ) {
		foreach ( $wp_filter['user_admin_notices']->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {

				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
					continue;
				}

				if ( ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && false !== strpos( strtolower( get_class( $arr['function'][0] ) ), 'analytify' ) ) || 'WPANALYTIFY_Utils' === $arr['function'][0] ) {
					continue;
				}
				if ( ! empty( $name ) && false === strpos( $name, 'analytify' ) ) {
					unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}

	if ( ! empty( $wp_filter['admin_notices']->callbacks ) && is_array( $wp_filter['admin_notices']->callbacks ) ) {
		foreach ( $wp_filter['admin_notices']->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
					continue;
				}
				if ( ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && false !== strpos( strtolower( get_class( $arr['function'][0] ) ), 'analytify' ) ) || 'WPANALYTIFY_Utils' === $arr['function'][0] ) {
					continue;
				}
				if ( ! empty( $name ) && false === strpos( $name, 'analytify' ) ) {
					unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}

	if ( ! empty( $wp_filter['all_admin_notices']->callbacks ) && is_array( $wp_filter['all_admin_notices']->callbacks ) ) {
		foreach ( $wp_filter['all_admin_notices']->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {

				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
					continue;
				}
				if ( ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && false !== strpos( strtolower( get_class( $arr['function'][0] ) ), 'analytify' ) ) || 'WPANALYTIFY_Utils' === $arr['function'][0] ) {
					continue;
				}
				if ( ! empty( $name ) && false === strpos( $name, 'analytify' ) ) {
					unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}
add_action( 'admin_print_scripts', 'hide_non_analytify_notice' );
add_action( 'admin_head', 'hide_non_analytify_notice', PHP_INT_MAX );
