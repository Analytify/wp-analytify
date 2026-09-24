<?php
/**
 * Shared WPBrigade SDK opt-out modal (all products).
 *
 * Expects: $wpb_sdk_plugin_slug, $wpb_sdk_product_name, $wpb_sdk_ajax_prefix, $wpb_sdk_optout_nonce.
 * Optional: $wpb_sdk_plugin_basename (plugin list row matching on plugins.php).
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpb_sdk_sdk_data = json_decode( (string) get_option( 'wpb_sdk_' . $wpb_sdk_plugin_slug ), true );
if ( ! is_array( $wpb_sdk_sdk_data ) ) {
	$wpb_sdk_sdk_data = array();
}

$wpb_sdk_communication   = isset( $wpb_sdk_sdk_data['communication'] ) ? $wpb_sdk_sdk_data['communication'] : '0';
$wpb_sdk_diagnostic_info = isset( $wpb_sdk_sdk_data['diagnostic_info'] ) ? $wpb_sdk_sdk_data['diagnostic_info'] : '0';
$wpb_sdk_extensions      = isset( $wpb_sdk_sdk_data['extensions'] ) ? $wpb_sdk_sdk_data['extensions'] : '0';

if ( function_exists( 'wpb_sdk_sdk_option_is_enabled' ) ) {
	$wpb_sdk_communication   = wpb_sdk_sdk_option_is_enabled( $wpb_sdk_communication ) ? '1' : '0';
	$wpb_sdk_diagnostic_info = wpb_sdk_sdk_option_is_enabled( $wpb_sdk_diagnostic_info ) ? '1' : '0';
	$wpb_sdk_extensions      = wpb_sdk_sdk_option_is_enabled( $wpb_sdk_extensions ) ? '1' : '0';
}

?><input type="hidden" id="<?php echo esc_attr( $wpb_sdk_plugin_slug . '_communication' ); ?>"
	value="<?php echo esc_attr( $wpb_sdk_communication ); ?>" />
<input type="hidden" id="<?php echo esc_attr( $wpb_sdk_plugin_slug . '_diagnostic_info' ); ?>"
	value="<?php echo esc_attr( $wpb_sdk_diagnostic_info ); ?>" />
<input type="hidden" id="<?php echo esc_attr( $wpb_sdk_plugin_slug . '_extensions' ); ?>"
	value="<?php echo esc_attr( $wpb_sdk_extensions ); ?>" />

<style media="screen">
	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal.active {
		display: block;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal {
		position: fixed;
		overflow: auto;
		height: 100%;
		width: 100%;
		top: 0;
		z-index: 100000;
		display: none;
		background: rgba(0, 0, 0, 0.6);
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal.active .<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-dialog {
		top: 10%;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal .<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-dialog {
		background: #fff;
		position: absolute;
		left: 50%;
		transform: translateX(-50%);
		max-width: calc(100% - 30px);
		padding-bottom: 0;
		top: -100%;
		z-index: 100001;
		width: 596px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal .<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-header {
		background: #fbfbfb;
		border-bottom: 1px solid #eee;
		margin-bottom: -3px;
		padding: 15px 20px;
		position: relative;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal .<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-body {
		border-bottom: 0;
		padding: 20px;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal .<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-footer {
		background: #fefefe;
		border: 0;
		padding: 20px;
		border-top: 1px solid #eee;
		text-align: right;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal h4 {
		color: #cacaca;
		font-size: 1.2em;
		font-weight: 700;
		letter-spacing: .6px;
		margin: 0;
		padding: 0;
		text-shadow: 1px 1px 1px #fff;
		text-transform: uppercase;
		-webkit-font-smoothing: antialiased;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal h2 {
		font-weight: bold;
		font-size: 20px;
		margin-top: 0;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal p {
		font-size: 14px;
		color: #333;
	}


	.communication-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin: 0 0 14px;
	}

	.communication-header h2 {
		font-size: 14.3px;
		font-weight: 600;
		color: #3c434a;
		text-transform: uppercase;
		margin: 0;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-link {
		color: #2271b1;
		font-size: 13px;
		text-decoration: underline;
		line-height: 20px;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-link:hover {
		text-decoration: none;
	}

	.info-box {
		display: flex;
		align-items: flex-start;
		margin: 0;
		padding: 17px 15px;
		position: relative;
		border: 1px solid #d3d3d3;
		border-left: 4px solid #72aee6;

		border-radius: 4px;
		gap: 10px;
	}

	.info-box>.dashicons {
		font-size: 30px;
		height: 30px;
		padding: 5px;
		width: 30px;
	}

	.info-box:has(+.info-box) {
		border-radius: 4px 4px 0 0;
	}

	.info-box+.info-box {
		border-radius: 0px 0px 4px 4px;
		border-top: 0;
	}

	.info {
		margin-left: 3px;
	}

	.info-title {
		color: #23282d;
		font-size: 14px;
		font-weight: 500;
		line-height: 1.4em;
		position: relative;
	}

	.info-tooltip {
		margin-left: 5px;
		font-size: 12px;
		color: #a3a3a3;
		cursor: pointer;
	}

	.info-description {
		font-size: 14px;
		color: #3c434a;
		line-height: 1.5;
		margin-top: 2px;
	}

	.wpb-optin-switch {
		background: #ececec;
		border: 1px solid #ccc;
		border: 1px solid rgba(0, 0, 0, .2);
		box-shadow: 0 0 4px rgba(0, 0, 0, .1), inset 0 1px 3px 0 rgba(0, 0, 0, .1);
		color: #ccc;
		cursor: pointer;
		display: inline-block;
		height: 18px;
		padding: 6px 6px 5px;
		position: relative;
		text-shadow: 0 1px 1px hsla(0, 0%, 100%, .8);
		border-radius: 24px;
		padding: 1px 19px;
		margin-left: auto;
		width: 0;
	}

	.wpb-optin-switch input {
		appearance: none;
		opacity: 0;
		position: absolute;
		top: -20px;
		left: -20px;
	}

	.wpb-optin-switch:before {
		border-radius: 18px;
		height: 18px;
		top: 0;
		width: 18px;
		position: absolute;
		left: -1px;
		transition: .4s cubic-bezier(.54, 1.6, .5, 1);
		background-color: #fff;
		background-image: linear-gradient(180deg, #ececec, #fff);
		border: 1px solid rgba(0, 0, 0, .3);
		content: '';
	}

	.wpb-optin-switch:has(input:checked) {
		background: #0085ba;
	}

	.wpb-optin-switch:has(input:checked):before {
		left: 19px;
	}

	.wpb-deactivated .info-box * {
		color: #aaaa;
	}

	.wpb-deactivated .info-box {
		border-left: 1px solid #d3d3d3;
		padding-left: 19px;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-button {
		display: inline-block;
		vertical-align: middle;
		margin-right: 10px;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-footer .wp-core-ui .button-primary {
		vertical-align: middle;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-close {
		border-radius: 20px;
		color: #bbb;
		cursor: pointer;
		padding: 3px;
		position: absolute;
		right: 10px;
		top: 12px;
		transition: all .2s ease-in-out;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-close:hover {
		background: #aaa;
		color: #fff;
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-opt-out-overlay {
		position: fixed;
		inset: 0;
		content: '';
	}

	.<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-body hr {
		border: 0;
		border-top: 1px solid #eee;
		margin: 25px 0 20px;
	}

	.wpb-tooltip {
		background: rgba(0, 0, 0, .8);
		border-radius: 5px;
		bottom: 100%;
		box-shadow: 1px 1px 1px rgba(0, 0, 0, .2);
		color: #fff !important;
		font-family: arial, serif;
		font-size: 12px;
		font-weight: 700;
		left: -17px;
		line-height: 1.3em;
		margin-bottom: 5px;
		opacity: 0;
		padding: 10px;
		position: absolute;
		right: 0;
		text-align: left;
		text-transform: none !important;
		transition: opacity .3s ease-in-out;
		visibility: hidden;
		z-index: 999999;
	}

	.info-title:hover .wpb-tooltip {
		opacity: 1;
		visibility: visible;
	}

	.info-title .wpb-tooltip:after {
		border-color: rgba(0, 0, 0, .8) transparent transparent;
		border-style: solid;
		border-width: 5px 5px 0;
		content: " ";
		display: block;
		height: 0;
		left: 21px;
		position: absolute;
		top: 100%;
		width: 0;
	}


	.wpb-ajax-spinner {
		background: url(/wp-admin/images/wpspin_light-2x.gif);
		background-size: contain;
		border: 0;
		display: inline-block;
		height: 20px;
		margin-bottom: -2px;
		margin-right: 5px;
		vertical-align: sub;
		width: 20px;
		margin-top: -2px;
	}


	.wpb-switch-feedback {
		margin-left: auto;
		margin-right: 10px;
		display: none;
	}

	.wpb-loading .wpb-switch-feedback,
	.wpb-loading .<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-link,
	.wpb-loading .communication-content {
		cursor: wait;
	}

	.wpb-switch-feedback.success {
		color: #71ae00;
	}
</style>
<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal <?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-opt-out">
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-opt-out-overlay"></div>
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-dialog">
		<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-header">
			<div
				class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-close <?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-continue-button">
				<span class="dashicons dashicons-no"></span></div>
			<h4>Opt Out</h4>
		</div>
		<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-body" data-optin="extensions">
			<!-- Communication Section -->
			<div class="communication-container <?php echo '1' === $wpb_sdk_communication ? '' : 'wpb-deactivated'; ?>">
				<div class="communication-header">
					<h2>COMMUNICATION</h2>
					<span class="wpb-communication-switch-feedback wpb-switch-feedback"><i
							class="dashicons dashicons-yes"></i></span>
					<a href="#" class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-link"
						option-name="communication"><?php echo '1' === $wpb_sdk_communication ? 'Opt Out' : 'Opt In'; ?></a>
				</div>
				<div class="communication-content">
					<div class="info-box">
						<i class="dashicons dashicons-admin-users"></i>
						<div class="info">
							<div class="info-title">View Basic Profile Info <i
									class="dashicons dashicons-editor-help"><span class="wpb-tooltip"
										style="width: 200px">Never miss important updates, get security warnings before
										they become public knowledge, and receive notifications about special offers and
										awesome new features.</span></i>
							</div>
							<div class="info-description">
								Your WordPress user's: first &amp; last name, and email address
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<!-- Diagnostic Info Section -->
			<div class="communication-container <?php echo '1' === $wpb_sdk_diagnostic_info ? '' : 'wpb-deactivated'; ?>">
				<div class="communication-header">
					<h2>Diagnostic Info</h2>
					<span class="wpb-diagnostic_info-switch-feedback wpb-switch-feedback"><i
							class="dashicons dashicons-yes"></i></span>
					<a href="#" class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-link"
						option-name="diagnostic_info"><?php echo '1' === $wpb_sdk_diagnostic_info ? 'Opt Out' : 'Opt In'; ?></a>
				</div>
				<div class="communication-content">
					<div class="info-box">
						<i class="dashicons dashicons-admin-links"></i>
						<div class="info">
							<div class="info-title">View Basic Website Info <i
									class="dashicons dashicons-editor-help"><span class="wpb-tooltip"
										style="width: 200px">To provide additional functionality that's relevant to your
										website, avoid WordPress or PHP version incompatibilities that can break your
										website, and recognize which languages &amp; regions the plugin should be
										translated and tailored to.</span></i>
							</div>
							<div class="info-description">
								Homepage URL & title, WP & PHP versions, and site language
							</div>
						</div>
					</div>
					<div class="info-box">
						<i class="dashicons dashicons-admin-plugins"></i>
						<div class="info">
							<div class="info-title">View Basic Plugin Info</div>
							<div class="info-description">
								Current plugin & SDK versions, and if active or uninstalled
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<!-- Extensions Section -->
			<div class="communication-container <?php echo '1' === $wpb_sdk_extensions ? '' : 'wpb-deactivated'; ?>" ">
			<div class=" communication-header">
				<h2>Extensions</h2>
				<span class="wpb-extensions-switch-feedback wpb-switch-feedback"><i
						class="dashicons dashicons-yes"></i></span>
				<a href="#" class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-link"
					option-name="extensions"><?php echo '1' === $wpb_sdk_extensions ? 'Opt Out' : 'Opt In'; ?></a>
			</div>
			<div class="communication-content">
				<div class="info-box">
					<i class="dashicons dashicons-block-default"></i>
					<div class="info">
						<div class="info-title">View Plugins <i class="dashicons dashicons-editor-help"><span
									class="wpb-tooltip" style="width: 200px">To ensure compatibility and avoid conflicts
									with your installed plugins and themes.</span></i>
						</div>
						<div class="info-description">
							Names, slugs, versions, and if active or not
						</div>
					</div>
					<label class="wpb-optin-switch"><input data-checkbox="extensions" type="checkbox" <?php echo '1' === $wpb_sdk_extensions ? 'Checked' : ''; ?>></label>
				</div>
			</div>
		</div>
	</div>
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-body" style="display: none;" data-optin="communication">
		<p>Sharing your name and email allows us to keep you in the loop about new features and important updates, warn
			you about security issues before they become public knowledge, and send you special offers.</p>
		<p>By clicking "Opt Out", <strong><?php echo esc_html( $wpb_sdk_product_name ); ?></strong> will no longer be able to view your name
			and email.</p>
	</div>
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-body" style="display: none;" data-optin="diagnostic_info">
		<p>Sharing diagnostic data helps to provide additional functionality that's relevant to your website, avoid
			WordPress or PHP version incompatibilities that can break the website, and recognize which languages &
			regions the plugin should be translated and tailored to.</p>
		<p>By clicking "Opt Out", diagnostic data will no longer be sent to <strong><?php echo esc_html( $wpb_sdk_product_name ); ?></strong>.
		</p>
	</div>
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-footer" data-optin-footer="extensions">
		<button class="button button-primary <?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-continue-button">Done</button>
	</div>
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-footer" style="display: none;"
		data-optin-footer="communication">
		<a class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-button" data-optin="" href="#">Opt Out</a>
		<button class="button button-primary" id="stay-connected">Stay Connected</button>
	</div>
	<div class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-modal-footer" style="display: none;"
		data-optin-footer="diagnostic_info">
		<a class="<?php echo esc_attr( $wpb_sdk_plugin_slug ); ?>-opt-out-button" data-optin="" href="#">Opt Out</a>
		<button class="button button-primary" id="stay-connected">Keep Sharing</button>
	</div>
</div>
</div>

<script type="text/javascript">
	(function ($) {
		$(function () {

			var pluginSlug = <?php echo wp_json_encode( $wpb_sdk_plugin_slug ); ?>;
			var ajaxPrefix = <?php echo wp_json_encode( $wpb_sdk_ajax_prefix ); ?>;

			// Open modal when the "Opt Out" button is clicked for a specific plugin row.
			$(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .opt-out', function (e) {
				e.preventDefault();
				$('.' + pluginSlug + '-modal-opt-out').addClass('active');  // Show the modal.
			});

			// Also handle generic .opt-out clicks (fallback for plugin action links).
			// This ensures clicks are caught even if the specific selector doesn't match.
			$(document).on('click', '.opt-out', function (e) {
				var $clickedElement = $(this);
				var $parentRow = $clickedElement.closest('tr');
				var rowSlug = $parentRow.attr('data-slug');
				
				// Only handle if it's in the plugin row for this plugin.
				if (rowSlug === pluginSlug) {
					e.preventDefault();
					e.stopPropagation();
					$('.' + pluginSlug + '-modal-opt-out').addClass('active');  // Show the modal.
				}
			});

			// Close the modal and reload the page when the "Done" button is clicked.
			$(document).on('click', '.' + pluginSlug + '-modal .' + pluginSlug + '-continue-button', function (event) {
				event.preventDefault();
				$('.' + pluginSlug + '-modal-opt-out').removeClass('active');  // Hide the modal.

				if (
					$('#' + pluginSlug + '_communication').val() === '0'
					&&
					$('#' + pluginSlug + '_diagnostic_info').val() === '0'
					&&
					$('#' + pluginSlug + '_extensions').val() === '0'
				) {
					location.reload();
				}// Reload the page.
			});
			// Show the extension opt-in modal body when "Stay Connected" is clicked.
			$(document).on('click', '.' + pluginSlug + '-modal  #stay-connected', function (event) {
				event.preventDefault();
				$('.' + pluginSlug + '-modal [data-optin="extensions"]').show().siblings('.' + pluginSlug + '-modal-body').hide();  // Show extensions, hide other modal bodies
				$('.' + pluginSlug + '-modal [data-optin-footer="extensions"]').show().siblings('.' + pluginSlug + '-modal-footer').hide();  // Show extension footer, hide other footers
			});

			// Send opt-out request when the opt-out button in the modal footer is clicked.
			$('.' + pluginSlug + '-modal-footer .' + pluginSlug + '-opt-out-button').on('click', function () {
				var sdk_setting_setting_name = $(this).parent().attr('data-optin-footer');  // Get the setting name.
				var sdk_setting_optin_button = $('.' + pluginSlug + '-modal .' + pluginSlug + '-opt-out-link[option-name="' + sdk_setting_setting_name + '"]')
				send_optin_request(sdk_setting_optin_button, sdk_setting_setting_name, 0);  // Send an opt-out request (0).
			});

			// Trigger opt-out when the extension checkbox is changed.
			$('.' + pluginSlug + '-modal [data-checkbox="extensions"]').on('change', function () {
				var optinName = $(this).attr('data-checkbox');  // Get the opt-in name.
				$('.' + pluginSlug + '-modal .' + pluginSlug + '-opt-out-link[option-name="' + optinName + '"]').trigger('click');  // Simulate opt-out link click
			});

			// Handle toggle between "Opt Out" and "Opt In" when clicked.
			$(document).on('click', '.' + pluginSlug + '-modal .' + pluginSlug + '-opt-out-link', function (e) {
				e.preventDefault();
				const el = $(this),
					getOptionName = el.attr('option-name');  // Get the option name.
				// Show/hide appropriate modal sections based on the option name.
				if ((getOptionName == 'communication' || getOptionName == 'diagnostic_info') && (el.html() == 'Opt Out')) {
					$('.' + pluginSlug + '-modal [data-optin="' + getOptionName + '"]').show().siblings('.' + pluginSlug + '-modal-body').hide();  // Show specific modal body
					$('.' + pluginSlug + '-modal [data-optin-footer="' + getOptionName + '"]').show().siblings('.' + pluginSlug + '-modal-footer').hide();  // Show specific modal footer
					return false;  // Stop further execution for specific cases.
				}
				// Toggle between "Opt In" and "Opt Out".
				let sdk_setting_optionValue = $(this).html() === "Opt In" ? 1 : 0;  // Set option value based on current text.
				// Toggle visual indication for deactivation.
				$(this).closest('.communication-container').toggleClass('wpb-deactivated');

				// Update checkbox status based on opt-in/out state.
				$('.' + pluginSlug + '-modal [data-checkbox="' + getOptionName + '"]').prop('checked', el.html() === 'Opt Out');

				// Send opt-in/out request.
				var sdk_setting_setting_name = $(this).attr('option-name');
				send_optin_request(el, sdk_setting_setting_name, sdk_setting_optionValue);  // Send request with setting name and value.
			});

			// Function to send an AJAX request for opt-in/out changes.
			function send_optin_request(el, setting_name, setting_option_value) {
				var nonceValue = '<?php echo isset( $wpb_sdk_optout_nonce ) ? esc_js( $wpb_sdk_optout_nonce ) : ''; ?>';

				$.ajax({
					type: 'POST',
					url: ajaxurl,  // Use WordPress AJAX URL.
					data: {
						action: ajaxPrefix + '_opt_out_option',
						setting_name: setting_name,  // The setting name (option).
						setting_value: setting_option_value,  // The value (opt-in or opt-out).
						optout_nonce: nonceValue,  // Security nonce.
					},
					beforeSend: function () {
						if (setting_option_value == '0') {
							el.html('Opting Out..')
						}
						if (setting_option_value == '1') {
							el.html('Opting In..')
						}
						$('.' + pluginSlug + '-modal .wpb-' + setting_name + '-switch-feedback').show().html('<span class="wpb-ajax-spinner"></span>');
						el.closest('.communication-container').addClass('wpb-loading');
					},
					error: function (xhr, status, error) {
						//error handling.
					},
					success: function (response) {
						$('#' + pluginSlug + '_' + setting_name).val(setting_option_value);
						if (setting_option_value == '1') {
							el.html('Opt Out')
							$('.' + pluginSlug + '-modal [option-name="' + setting_name + '"]').closest('.communication-container').removeClass('wpb-deactivated');  // Add deactivated class
							if (setting_name === 'extensions') {
								$('.' + pluginSlug + '-modal .wpb-optin-switch input').prop('checked', true);
							}
						}
						if (setting_option_value == '0') {
							el.html('Opt In')
							$('.' + pluginSlug + '-modal [option-name="' + setting_name + '"]').closest('.communication-container').addClass('wpb-deactivated');  // Add deactivated class

							if (setting_name === 'extensions') {
								$('.' + pluginSlug + '-modal .wpb-optin-switch input').prop('checked', false);
							}


						}
						// Update UI after successful opt-in/out request.
						$('.' + pluginSlug + '-modal [data-optin="extensions"]').show().siblings('.' + pluginSlug + '-modal-body').hide();  // Show extensions section.
						$('.' + pluginSlug + '-modal [data-optin-footer="extensions"]').show().siblings('.' + pluginSlug + '-modal-footer').hide();  // Show footer.
						el.closest('.communication-container').removeClass('wpb-loading');

						// Update UI if the user opted out.
						$('.' + pluginSlug + '-modal .wpb-' + setting_name + '-switch-feedback').addClass('success');
						$('.' + pluginSlug + '-modal .wpb-' + setting_name + '-switch-feedback').html('<i class="dashicons dashicons-yes"></i> Saved');
					}
				});
			}
		});
	})(jQuery);
</script>


