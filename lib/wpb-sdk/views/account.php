<style>
	label.fs-tag,
	span.fs-tag {
		background: #ffba00;
		color: #fff;
		display: inline-block;
		border-radius: 3px;
		padding: 5px;
		font-size: 11px;
		line-height: 11px;
		vertical-align: baseline
	}

	label.fs-tag.fs-warn,
	span.fs-tag.fs-warn {
		background: #ffba00
	}

	label.fs-tag.fs-info,
	span.fs-tag.fs-info {
		background: #00a0d2
	}

	label.fs-tag.fs-success,
	span.fs-tag.fs-success {
		background: #46b450
	}

	label.fs-tag.fs-error,
	span.fs-tag.fs-error {
		background: #dc3232
	}

	.fs-notice[data-id=license_not_whitelabeled].success,
	.fs-notice[data-id=license_whitelabeled].success {
		color: inherit;
		border-left-color: #00a0d2
	}

	.fs-notice[data-id=license_not_whitelabeled].success label.fs-plugin-title,
	.fs-notice[data-id=license_whitelabeled].success label.fs-plugin-title {
		display: none
	}

	#fs_account .postbox,
	#fs_account .widefat {
		max-width: 800px
	}

	#fs_account h3 {
		font-size: 1.3em;
		padding: 12px 15px;
		margin: 0 0 12px 0;
		line-height: 1.4;
		border-bottom: 1px solid #f1f1f1
	}

	#fs_account h3 .dashicons {
		width: 26px;
		height: 26px;
		font-size: 1.3em
	}

	#fs_account i.dashicons {
		font-size: 1.2em;
		height: 1.2em;
		width: 1.2em
	}

	#fs_account .dashicons {
		vertical-align: middle
	}

	#fs_account .fs-header-actions {
		position: absolute;
		top: 17px;
		right: 15px;
		font-size: .9em
	}

	#fs_account .fs-header-actions ul {
		margin: 0
	}

	#fs_account .fs-header-actions li {
		float: left
	}

	#fs_account .fs-header-actions li form {
		display: inline-block
	}

	#fs_account .fs-header-actions li a {
		text-decoration: none
	}

	#fs_account_details .button-group {
		float: right
	}

	.rtl #fs_account .fs-header-actions {
		left: 15px;
		right: auto
	}

	.fs-key-value-table {
		width: 100%
	}

	.fs-key-value-table form {
		display: inline-block
	}

	.fs-key-value-table tr td:first-child {
		text-align: right
	}

	.fs-key-value-table tr td:first-child nobr {
		font-weight: bold
	}

	.fs-key-value-table tr td:first-child form {
		display: block
	}

	.fs-key-value-table tr td.fs-right {
		text-align: right
	}

	.fs-key-value-table tr.fs-odd {
		background: #ebebeb
	}

	.fs-key-value-table td,
	.fs-key-value-table th {
		padding: 10px
	}

	.fs-key-value-table code {
		line-height: 28px
	}

	.fs-key-value-table var,
	.fs-key-value-table code,
	.fs-key-value-table input[type=text] {
		color: #0073aa;
		font-size: 16px;
		background: none
	}

	.fs-key-value-table input[type=text] {
		width: 100%;
		font-weight: bold
	}

	.fs-field-beta_program label {
		margin-left: 7px
	}

	label.fs-tag {
		background: #ffba00;
		color: #fff;
		display: inline-block;
		border-radius: 3px;
		padding: 5px;
		font-size: 11px;
		line-height: 11px;
		vertical-align: baseline
	}

	label.fs-tag.fs-warn {
		background: #ffba00
	}

	label.fs-tag.fs-success {
		background: #46b450
	}

	label.fs-tag.fs-error {
		background: #dc3232
	}




	@media screen and (max-width: 639px) {
		#fs_account .fs-header-actions {
			position: static;
			padding: 0 15px 12px 15px;
			margin: 0 0 12px 0
		}

		#fs_account .fs-header-actions li {
			float: none;
			display: inline-block
		}

		#fs_account #fs_account_details {
			display: block
		}

		#fs_account #fs_account_details tbody,
		#fs_account #fs_account_details tr,
		#fs_account #fs_account_details td,
		#fs_account #fs_account_details th {
			display: block
		}
	}

	/*# sourceMappingURL=account.css.map */
</style>

<?php
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

$resolved = function_exists( 'wpb_sdk_dev_view_resolve_product' )
	? wpb_sdk_dev_view_resolve_product()
	: array(
		'slug'      => '',
		'module_id' => '1',
	);
$slug = isset( $resolved['slug'] ) ? (string) $resolved['slug'] : '';

$Data = function_exists( 'wpb_sdk_dev_view_load_logs_data' )
	? wpb_sdk_dev_view_load_logs_data( $slug )
	: array();

if ( empty( $Data ) && function_exists( 'wpb_sdk_dev_view_default_logs_data' ) ) {
	$Data = wpb_sdk_dev_view_default_logs_data();
}
?>
<div class="wrap fs-section">
	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active">Account</a>

	</h2>

	<div id="fs_account">
		<div class="has-sidebar has-right-sidebar">
			<div class="has-sidebar-content">
				<div class="postbox">
					<h3><span class="dashicons dashicons-businessman"></span> Account Details</h3>
					<div class="fs-header-actions">

					</div>
					<div class="inside">
						<table id="fs_account_details" cellspacing="0" class="fs-key-value-table">
							<tbody>
								<tr class="fs-field-user_name alternate">
									<td>
										<nobr>Name:</nobr>
									</td>
									<td>
										<code><?php echo esc_html( $Data['user_info']['user_nickname'] ?? '' ); ?></code>
									</td>
									<td class="fs-right">
										<form action="#" method="POST" onsubmit="var val = prompt('What is your Name?', 'Admin'); if (null == val || '' === val) return false; jQuery('input[name=fs_user_name_login-customizer]').val(val); return true;">
											<input type="hidden" name="fs_action" value="update_user_name">
											<input type="hidden" name="fs_user_name_login-customizer" value="">
											<!-- <input type="hidden" id="_wpnonce" name="_wpnonce" value="fd180d056a"><input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=login-customizer-account"> <input type="submit" class="button button-small " value="Edit"> -->
										</form>
									</td>
								</tr>
								<tr class="fs-field-email">
									<td>
										<nobr>Email:</nobr>
									</td>
									<td>
										<code><?php echo esc_html( $Data['user_info']['user_email'] ?? '' ); ?></code>
									</td>
									<td class="fs-right">
										<form action="http://new-freemius-test.com/wp-admin/admin.php?page=login-customizer-account" method="POST" onsubmit="var val = prompt('What is your Email?', 'hamzabhatti151@gmail.com'); if (null == val || '' === val) return false; jQuery('input[name=fs_email_login-customizer]').val(val); return true;">
											<input type="hidden" name="fs_action" value="update_email">
											<input type="hidden" name="fs_email_login-customizer" value="">
											<!-- <input type="hidden" id="_wpnonce" name="_wpnonce" value="a626a0e9c8"><input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=login-customizer-account"> <input type="submit" class="button button-small button-edit-email-address" value="Edit"> -->
										</form>
									</td>
								</tr>
								<tr class="fs-field-user_id alternate">
									<td>
										<nobr>User ID:</nobr>
									</td>
									<td>
										<code>7984785</code>
									</td>
									<td class="fs-right">
									</td>
								</tr>
								<tr class="fs-field-product">
									<td>
										<nobr>Plugin:</nobr>
									</td>
									<td>
										<code><?php echo $Data['product_info']['name']; ?></code>
									</td>
									<td class="fs-right">
									</td>
								</tr>
								<tr class="fs-field-product_id alternate">
									<td>
										<nobr>Plugin ID:</nobr>
									</td>
									<td>
										<code>8656</code>
									</td>
									<td class="fs-right">
									</td>
								</tr>
								<tr class="fs-field-site_id">
									<td>
										<nobr>Site ID:</nobr>
									</td>
									<td>
										<code>14510925</code>
									</td>
									<td class="fs-right">
									</td>
								</tr>
								<tr class="fs-field-site_public_key alternate">
									<td>
										<nobr>Public Key:</nobr>
									</td>
									<td>
										<code><?php echo esc_html( $Data['authentication']['public_key'] ?? '' ); ?></code>
									</td>
									<td class="fs-right">
									</td>
								</tr>
								<!-- HTML markup for the "Show" button and elements to toggle -->
								<tr class="fs-field-site_secret_key">
									<td>
										<nobr>Secret Key:</nobr>
									</td>
									<td>
										<code>sk_w?M•••••••••••••••••••••••s*L</code>
										<input type="text" value="sk_w?M5RcM9y;j6IbUip%A!EP6h]Js*L" style="display: none;" readonly="">
									</td>
									<td class="fs-right">
										<button class="button button-small fs-toggle-visibility">Show</button>
									</td>
								</tr>

								<!-- JavaScript code to toggle visibility -->
								<script>
									// jQuery document ready function
									jQuery(document).ready(function($) {
										// Add click event listener to the "Show" button
										$('.fs-toggle-visibility').click(function() {
											// Find the <code> element and toggle its visibility
											$(this).closest('tr').find('code').toggle();
											// Find the <input> element and toggle its visibility
											$(this).closest('tr').find('input').toggle();
											// Change the button text based on visibility
											var buttonText = ($(this).text() === 'Show') ? 'Hide' : 'Show';
											$(this).text(buttonText);
										});
									});
								</script>

								<tr class="fs-field-version alternate">
									<td>
										<nobr>Version:</nobr>
									</td>
									<td>
										<code><?php echo esc_html( $Data['product_info']['version'] ?? '' ); ?></code>
									</td>
									<td class="fs-right">
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
