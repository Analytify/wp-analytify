<?php
/**
 * WPB Debug view.
 *
 * HIGH RISK – Admin-only debug UI. Outputs sensitive data (keys, paths, user info).
 * Only load when is_admin(), manage_options, and WPBRIGADE_SDK__DEV_MODE are satisfied.
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
	wp_die(
		esc_html__( 'You do not have permission to access this page.', 'wpbrigade-sdk' ),
		'',
		array( 'response' => 403 )
	);
}

if ( ! defined( 'WPBRIGADE_SDK__DEV_MODE' ) || true !== WPBRIGADE_SDK__DEV_MODE ) {
	wp_die(
		esc_html__( 'Debug mode is not enabled.', 'wpbrigade-sdk' ),
		'',
		array( 'response' => 403 )
	);
}

/**
 * Enqueue CSS file for admin debugging.
 *
 * @param string $hook_suffix Current admin screen hook suffix.
 * @return void
 */
function wpb_debug_enqueue_styles( $hook_suffix ) {
	if ( 'toplevel_page_wpb-debug-mode' !== $hook_suffix || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$suffix   = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$css_path = dirname( __DIR__ ) . "/assets/css/debug{$suffix}.css";
	if ( ! is_readable( $css_path ) ) {
		return;
	}

	wp_enqueue_style(
		'wpb-sdk-debug-style',
		plugins_url( "assets/css/debug{$suffix}.css", dirname( __DIR__ ) . '/start.php' ),
		array(),
		defined( 'WP_WPBRIGADE_SDK_VERSION' ) ? WP_WPBRIGADE_SDK_VERSION : '1.0.0'
	);
}
add_action( 'admin_enqueue_scripts', 'wpb_debug_enqueue_styles' );

/**
 * Verify POST request: method, capability, and nonce for a given action.
 *
 * @param string $action Nonce action (e.g. 'wpb_debug_clear_cache').
 * @return bool True if valid POST with valid nonce and capability.
 */
function wpb_debug_verify_request( $action ) {
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
		return false;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	if ( ! isset( $_POST['_wpnonce'] ) ) {
		return false;
	}
	return (bool) wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $action );
}

/**
 * Mask a sensitive string (show last N chars, rest as bullets).
 *
 * @param string $value  Raw value.
 * @param int    $visible Number of trailing characters to show.
 * @return string Masked value.
 */
function wpb_debug_mask( $value, $visible = 4 ) {
	if ( '' === (string) $value ) {
		return '—';
	}
	$value = (string) $value;
	$len   = strlen( $value );
	if ( $len <= $visible ) {
		return str_repeat( '•', $len );
	}
	return str_repeat( '•', $len - $visible ) . substr( $value, - $visible );
}

/**
 * Mask email for display (e.g. a***@***.com).
 *
 * @param string $email Email address.
 * @return string Masked email.
 */
function wpb_debug_mask_email( $email ) {
	if ( '' === (string) $email || ! is_email( $email ) ) {
		return '—';
	}
	$parts = explode( '@', $email, 2 );
	if ( 2 !== count( $parts ) ) {
		return wpb_debug_mask( $email, 0 );
	}
	$local          = $parts[0];
	$domain         = $parts[1];
	$local_display  = strlen( $local ) > 2 ? substr( $local, 0, 1 ) . str_repeat( '•', strlen( $local ) - 1 ) : '••';
	$domain_display = strlen( $domain ) > 4 ? '•••' . substr( $domain, -4 ) : '••••';
	return $local_display . '@' . $domain_display;
}

/**
 * Shorten path for display (show last two segments to avoid exposing full server path).
 *
 * @param string $path Full path.
 * @return string Shortened path.
 */
function wpb_debug_mask_path( $path ) {
	if ( '' === (string) $path ) {
		return '—';
	}
	$path  = str_replace( array( '\\', '/' ), '/', (string) $path );
	$parts = array_filter( explode( '/', $path ) );
	$tail  = array_slice( $parts, -2 );
	return ( count( $parts ) > 2 ? '…/' : '' ) . implode( '/', $tail );
}

$resolved = function_exists( 'wpb_sdk_dev_view_resolve_product' )
	? wpb_sdk_dev_view_resolve_product()
	: array(
		'slug'      => '',
		'module_id' => '1',
	);
$slug              = isset( $resolved['slug'] ) ? (string) $resolved['slug'] : '';
$wpb_sdk_module_id = isset( $resolved['module_id'] ) ? (string) $resolved['module_id'] : '1';

$all_plugins = array();

$data = function_exists( 'wpb_sdk_dev_view_load_logs_data' )
	? wpb_sdk_dev_view_load_logs_data( $slug )
	: array();

if ( empty( $data ) && function_exists( 'wpb_sdk_dev_view_default_logs_data' ) ) {
	$data = wpb_sdk_dev_view_default_logs_data();
}

$plugin_path            = isset( $data['product_info']['path'] ) ? $data['product_info']['path'] : '';
$installed_plugin_slugs = array_keys( get_plugins() );
$active_plugins         = get_option( 'active_plugins', array() );
$sdk_path               = WPBRIGADE_SDK_DIR;

$this_sdk_path = strstr( $sdk_path, $slug );
if ( false !== $this_sdk_path ) {
	$this_sdk_path = '\\' . ltrim( $this_sdk_path, '\\' );
}

// Clear API cache.
if ( isset( $_POST['wpb_clear_api_cache'] ) && 'true' === $_POST['wpb_clear_api_cache'] && wpb_debug_verify_request( 'wpb_debug_clear_cache' ) ) {
	update_option( 'wpb_api_cache', null );
}

// Clear updates data.
if ( isset( $_POST['wpb_action'] ) && 'clear_updates_data' === $_POST['wpb_action'] && wpb_debug_verify_request( 'wpb_debug_clear_updates' ) ) {
	set_site_transient( 'update_plugins', null );
	set_site_transient( 'update_themes', null );
}

// Background sync.
if ( isset( $_POST['background_sync'] ) && 'true' === $_POST['background_sync'] && wpb_debug_verify_request( 'wpb_debug_background_sync' ) ) {
	$response = wp_remote_post(
		WPBRIGADE_SDK_API_ENDPOINT,
		array(
			'method'  => 'POST',
			'body'    => $data,
			'timeout' => 5,
			'headers' => array(),
		)
	);

	if ( is_wp_error( $response ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- SDK debug UI only.
		error_log( 'WPB SDK debug: background sync failed — ' . $response->get_error_message() );
	} else {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- SDK debug UI only.
		error_log(
			'WPB SDK debug: background sync completed — HTTP ' . (string) wp_remote_retrieve_response_code( $response )
		);
	}
}

/** Option name prefix allowed for load/set DB option tools (strict whitelist by prefix). */
define( 'WPB_DEBUG_OPTION_PREFIX', 'wpb_' );

/**
 * Whether an option name is allowed for debug load/set tools.
 *
 * @param string $option_name Raw option name.
 * @return bool
 */
function wpb_debug_option_name_is_allowed( $option_name ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$option_name = sanitize_key( (string) $option_name );
	if ( '' === $option_name ) {
		return false;
	}

	return 0 === strpos( $option_name, WPB_DEBUG_OPTION_PREFIX );
}

/**
 * Set an option value only if it is in the allowed prefix scope.
 *
 * @param string $option_name  Option name (must start with WPB_DEBUG_OPTION_PREFIX).
 * @param mixed  $option_value Option value.
 * @return bool True on success, false if not allowed.
 */
function wpb_debug_set_option( $option_name, $option_value ) {
	if ( ! wpb_debug_option_name_is_allowed( $option_name ) ) {
		return false;
	}

	$option_name = sanitize_key( (string) $option_name );
	update_option( $option_name, $option_value );

	return true;
}

$wpb_debug_set_option_success   = false;
$wpb_debug_set_option_submitted = false;
if ( isset( $_POST['set_option_name'], $_POST['option_value'] ) && wpb_debug_verify_request( 'wpb_debug_set_option' ) ) {
	$wpb_debug_set_option_submitted = true;
	$option_name                    = sanitize_text_field( wp_unslash( $_POST['set_option_name'] ) );
	$option_value                   = isset( $_POST['option_value'] ) ? sanitize_text_field( wp_unslash( $_POST['option_value'] ) ) : '';
	$wpb_debug_set_option_success   = wpb_debug_set_option( $option_name, $option_value );
}

/**
 * Get an option value from the database (wpb_* options only).
 *
 * @param string $option_name Option name.
 * @return mixed Option value or null when not allowed.
 */
function wpb_debug_get_option_value( $option_name ) {
	if ( ! wpb_debug_option_name_is_allowed( $option_name ) ) {
		return null;
	}

	return get_option( sanitize_key( (string) $option_name ), null );
}

$option_value              = '';
$result_visible            = false;
$wpb_debug_load_option_error = false;
if ( isset( $_POST['load_option_name'] ) && wpb_debug_verify_request( 'wpb_debug_load_option' ) ) {
	$option_name = sanitize_text_field( wp_unslash( $_POST['load_option_name'] ) );
	if ( wpb_debug_option_name_is_allowed( $option_name ) ) {
		$option_value   = wpb_debug_get_option_value( $option_name );
		$result_visible = true;
	} else {
		$wpb_debug_load_option_error = true;
	}
}

$wpb_debug_msg_success    = __( 'Successfully set the option.', 'wpbrigade-sdk' );
$wpb_debug_msg_error      = __( 'Option not set. Name must start with wpb_.', 'wpbrigade-sdk' );
$wpb_debug_msg_load_error = __( 'Option not loaded. Name must start with wpb_.', 'wpbrigade-sdk' );
?>

<h1>WPB Debug - SDK v.<?php echo esc_html( defined( 'WP_WPBRIGADE_SDK_VERSION' ) ? WP_WPBRIGADE_SDK_VERSION : '' ); ?></h1>

<?php if ( $wpb_debug_set_option_submitted ) : ?>
<div id="success_message" class="notice notice-<?php echo esc_attr( $wpb_debug_set_option_success ? 'success' : 'error' ); ?>" role="alert">
	<p><?php echo esc_html( $wpb_debug_set_option_success ? $wpb_debug_msg_success : $wpb_debug_msg_error ); ?></p>
</div>
<?php endif; ?>

<?php if ( $wpb_debug_load_option_error ) : ?>
<div class="notice notice-error" role="alert">
	<p><?php echo esc_html( $wpb_debug_msg_load_error ); ?></p>
</div>
<?php endif; ?>

<p class="notice notice-warning" style="margin: 1em 0;" role="alert">
	<strong><?php esc_html_e( 'Admin-only debug page.', 'wpbrigade-sdk' ); ?></strong>
	<?php esc_html_e( 'This page shows sensitive data (keys, paths, user info). Do not share screenshots or leave unattended.', 'wpbrigade-sdk' ); ?>
</p>

<h2><?php esc_html_e( 'Actions', 'wpbrigade-sdk' ); ?></h2>
<table>
	<tbody>
		<tr>
			<td>
				<!-- Clear API Cache -->
				<form action="" method="POST">
					<?php wp_nonce_field( 'wpb_debug_clear_cache' ); ?>
					<input type="hidden" name="wpb_clear_api_cache" value="true">
					<button class="button button-primary">Clear API Cache</button>
				</form>
			</td>
			<td>
				<!-- Clear Updates Transients -->
				<form action="" method="POST">
					<?php wp_nonce_field( 'wpb_debug_clear_updates' ); ?>
					<input type="hidden" name="wpb_action" value="clear_updates_data">
					<button class="button">Clear Updates Transients</button>
				</form>
			</td>
			<td>
				<!-- Sync Data with Server -->
				<form action="" method="POST">
					<?php wp_nonce_field( 'wpb_debug_background_sync' ); ?>
					<input type="hidden" name="background_sync" value="true">
					<button class="button button-primary">Sync Data From Server</button>
				</form>
			</td>
			<td>
				<!-- Load DB Option -->
				<form method="post">
					<?php wp_nonce_field( 'wpb_debug_load_option' ); ?>
					<button type="button" class="button" id="show_input_button">Load DB Option</button>
					<div id="input_field" style="display: none;">
						<input type="text" name="load_option_name" id="option_name_input">
						<button type="submit" id="submit_option_button">Submit</button>
					</div>
				</form>
				<div id="result" 
				<?php
				if ( ! $result_visible ) {
					echo ' style="' . esc_attr( 'display: none;' ) . '"';
				}
				?>
				>
					<?php
					if ( null === $option_value ) {
						esc_html_e( 'Option not found.', 'wpbrigade-sdk' );
					} elseif ( is_array( $option_value ) ) {
						echo esc_html__( 'Option Value:', 'wpbrigade-sdk' ) . ' ' . esc_html( wp_json_encode( $option_value ) );
					} else {
						echo esc_html__( 'Option Value:', 'wpbrigade-sdk' ) . ' ' . esc_html( (string) $option_value );
					}
					?>
					<button id="clear_result_button">✖</button>
				</div>
			</td>
			<td>
				<!-- Set DB Option (whitelist: wpb_ prefix only) -->
				<button type="button" class="button" id="set_option_button"><?php esc_html_e( 'Set DB Option', 'wpbrigade-sdk' ); ?></button>
				<form id="set_option_form" method="post" style="display: none; margin-right: 10px;">
					<?php wp_nonce_field( 'wpb_debug_set_option' ); ?>
					<div class="option-input-wrapper" style="display: inline-block;">
						<label for="option_name"><?php esc_html_e( 'Option Name (must start with wpb_):', 'wpbrigade-sdk' ); ?></label>
						<input type="text" name="set_option_name" id="option_name" placeholder="wpb_">
					</div>
					<div class="option-input-wrapper">
						<label for="option_value">Option Value:</label>
						<input type="text" name="option_value" id="option_value">
					</div>
					<button type="submit" id="submit_set_option_button">Set Option</button>
				</form>
			</td>
		</tr>
	</tbody>
</table>

<br>

<table class="widefat">
	<thead>
		<tr>
			<th>Key</th>
			<th>Value</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>WP_WPB__REMOTE_ADDR</td>
			<td><?php echo esc_html( wpb_debug_mask( isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '', 6 ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td>WP_WPB__DIR</td>
			<td><?php echo esc_html( wpb_debug_mask_path( WPBRIGADE_SDK_DIR ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td>wp_using_ext_object_cache()</td>
			<td>false</td>
		</tr>
	</tbody>
</table>

<h2>SDK Versions</h2>
<table id="wpb_sdks" class="widefat">
	<thead>
		<tr>
			<th>Version</th>
			<th>SDK Path</th>
			<th>Module Path</th>
			<th>Is Active</th>
		</tr>
	</thead>
	<tbody>
		<tr style="background: #E6FFE6; font-weight: bold">
			<td><?php echo esc_html( defined( 'WP_WPBRIGADE_SDK_VERSION' ) ? WP_WPBRIGADE_SDK_VERSION : '' ); ?></td>
			<td><?php echo esc_html( wpb_debug_mask_path( WPBRIGADE_SDK_DIR ) ); ?></td>
			<td><?php echo esc_html( wpb_debug_mask_path( dirname( WPBRIGADE_SDK_DIR ) ) ); ?></td>
			<td>Active</td>
		</tr>
	</tbody>
</table>

<h2>Plugins</h2>
<table id="wpb_sdks" class="widefat">
	<thead>
		<tr>
			<th>ID</th>
			<th>Slug</th>
			<th>Version</th>
			<th>Title</th>
			<th>API</th>
			<th>Telemetry State</th>
			<th>Module Path</th>
			<th>Public Key</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo esc_html( (string) $wpb_sdk_module_id ); ?></td>
			<td><?php echo esc_html( isset( $data['product_info']['slug'] ) ? $data['product_info']['slug'] : '' ); ?></td>
			<td><?php echo esc_html( isset( $data['product_info']['version'] ) ? $data['product_info']['version'] : '' ); ?></td>
			<td><?php echo esc_html( isset( $data['product_info']['name'] ) ? $data['product_info']['name'] : '' ); ?></td>
			<td></td>
			<td></td>
			<td><?php echo esc_html( wpb_debug_mask_path( dirname( WPBRIGADE_SDK_DIR ) ) ); ?></td>
			<td><?php echo esc_html( wpb_debug_mask( isset( $data['authentication']['public_key'] ) ? $data['authentication']['public_key'] : '', 4 ) ); ?></td>
			<td>
				<button class="button" id="show-account-button" onclick="window.location.href = '<?php echo esc_url( admin_url( 'admin.php?page=account' ) ); ?>'">Account</button>
			</td>
		</tr>
	</tbody>
</table>

<h2>Plugins/Sites</h2>
<table id="wpb_sdks" class="widefat">
	<thead>
		<tr>
			<th>ID</th>
			<th>Slug</th>
			<th>User ID</th>
			<th>License ID</th>
			<th>Plan</th>
			<th>Public Key</th>
			<th>Secret Key</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>3538</td>
			<td><?php echo esc_html( isset( $data['product_info']['slug'] ) ? $data['product_info']['slug'] : '' ); ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo esc_html( wpb_debug_mask( isset( $data['authentication']['public_key'] ) ? $data['authentication']['public_key'] : '', 4 ) ); ?></td>
			<td><?php echo esc_html( wpb_debug_mask( isset( $data['authentication']['public_key'] ) ? $data['authentication']['public_key'] : '', 4 ) ); ?></td>
		</tr>
	</tbody>
</table>

<h2>Users</h2>
<table id="wpb_users" class="widefat">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Email</th>
			<th>Verified</th>
			<th>Public Key</th>
			<th>Secret Key</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>3538</td>
			<td><?php echo esc_html( isset( $data['user_info']['user_nickname'] ) ? $data['user_info']['user_nickname'] : '' ); ?></td>
			<td><?php echo esc_html( wpb_debug_mask_email( isset( $data['user_info']['user_email'] ) ? $data['user_info']['user_email'] : '' ) ); ?></td>
			<td></td>
			<td><?php echo esc_html( wpb_debug_mask( isset( $data['authentication']['public_key'] ) ? $data['authentication']['public_key'] : '', 4 ) ); ?></td>
			<td><?php echo esc_html( wpb_debug_mask( isset( $data['authentication']['public_key'] ) ? $data['authentication']['public_key'] : '', 4 ) ); ?></td>
		</tr>
	</tbody>
</table>


<!-- JavaScript code to show/hide input field -->
<script>
	// Load DB Option
	document.getElementById('show_input_button').addEventListener('click', function() {
		document.getElementById('input_field').style.display = 'block';
	});

	document.getElementById('submit_option_button').addEventListener('click', function() {
		// Hide the input field
		document.getElementById('input_field').style.display = 'none';
		// Set the result container to be visible
		document.getElementById('result').style.display = 'block';
	});

	document.getElementById('clear_result_button').addEventListener('click', function() {
		// Hide the result container
		document.getElementById('result').style.display = 'none';
	});

	// Set DB Option
	document.getElementById('set_option_button').addEventListener('click', function() {
		// Show the form
		document.getElementById('set_option_form').style.display = 'block';
	});

	document.getElementById('set_option_form').addEventListener('submit', function(event) {
		// Hide the form
		document.getElementById('set_option_form').style.display = 'none';
		// Get the option name and value from the form
		var optionName = document.getElementById('option_name').value;
		var optionValue = document.getElementById('option_value').value;
	});

	document.getElementById('submit_set_option_button').addEventListener('click', function() {
		// Hide the input fields
		document.getElementById('option_name').style.display = 'none';
		document.getElementById('option_value').style.display = 'none';
	});

	setTimeout(function() {
		var el = document.getElementById('success_message');
		if (el) {
			el.style.display = 'none';
		}
	}, 3000);
</script>
