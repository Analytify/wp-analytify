<?php
/**
 * Deactivation feedback modal markup.
 *
 * @package wpbrigade_sdk
 */

?>
<style>
	.wpb-sdk_deactivation-frm-hidden {
		overflow: hidden;
	}

	.wpb-sdk_deactivation-frm-popup-overlay .wpb-sdk_deactivation-frm-internal-message {
		margin: 3px 0 3px 22px;
		display: none;
	}

	.wpb-sdk_deactivation-frm-reason-input {
		margin: 3px 0 3px 22px;
		display: none;
	}

	.wpb-sdk_deactivation-frm-pro-message {
		margin: 3px 0 3px 22px;
		display: none;
		color: #ed1515;
		font-size: 14px;
		font-weight: 600;
	}

	.wpb-sdk_deactivation-frm-reason-input input[type="text"] {
		width: 100%;
		display: block;
	}

	.wpb-sdk_deactivation-frm-popup-overlay {
		background: rgba(0, 0, 0, .8);
		position: fixed;
		top: 0;
		left: 0;
		height: 100%;
		width: 100%;
		z-index: 10000;
		overflow: auto;
		visibility: hidden;
		opacity: 0;
		transition: opacity 0.3s ease-in-out;
	}

	.wpb-sdk_deactivation-frm-popup-overlay.wpb-sdk_deactivation-frm-active {
		opacity: 1;
		visibility: visible;
	}

	.wpb-sdk_deactivation-frm-serveypanel {
		width: 600px;
		background: #fff;
		margin: 65px auto 0;
	}

	.wpb-sdk_deactivation-frm-popup-header {
		background: #f1f1f1;
		padding: 20px;
		border-bottom: 1px solid #ccc;
	}

	.wpb-sdk_deactivation-frm-popup-header h2 {
		margin: 0;
	}

	.wpb-sdk_deactivation-frm-popup-body {
		padding: 10px 20px;
	}

	.wpb-sdk_deactivation-frm-popup-footer {
		background: #f9f3f3;
		padding: 10px 20px;
		border-top: 1px solid #ccc;
	}

	.wpb-sdk_deactivation-frm-popup-footer:after {
		content: "";
		display: table;
		clear: both;
	}

	.action-btns {
		float: right;
	}

	.wpb-sdk_deactivation-frm-anonymous {
		display: none;
	}

	.attention,
	.error-message {
		color: red;
		font-weight: 600;
		display: none;
	}

	.wpb-sdk_deactivation-frm-spinner {
		display: none;
	}

	.wpb-sdk_deactivation-frm-spinner img {
		margin-top: 3px;
	}
</style>
<div class="<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper">
	<div class="wpb-sdk_deactivation-frm-popup-overlay">
		<div class="wpb-sdk_deactivation-frm-serveypanel">
			<form action="#" method="post" class="wpb-sdk_deactivation-frm-deactivate-form">
				<div class="wpb-sdk_deactivation-frm-popup-header">
					<h2><?php echo esc_html( sprintf( /* translators: %s: product name */ __( 'Quick feedback about %s', 'wpbrigade-sdk' ), $product_name ) ); ?></h2>
				</div>
				<div class="wpb-sdk_deactivation-frm-popup-body">
					<h3><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating:', 'wpbrigade-sdk' ); ?></h3>
					<ul id="wpb-sdk_deactivation-frm-reason-list">
						<li class="wpb-sdk_deactivation-frm-reason" data-input-type="" data-input-placeholder="">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="1"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( 'I only needed the plugin for a short period', 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
						</li>
						<li class="wpb-sdk_deactivation-frm-reason has-input" data-input-type="textfield">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="2"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( 'I found a better plugin', 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
							<div class="wpb-sdk_deactivation-frm-reason-input">
								<span class="message error-message"><?php esc_html_e( 'Kindly tell us the name of plugin', 'wpbrigade-sdk' ); ?></span>
								<input type="text" name="better_plugin" placeholder="<?php esc_html_e( "What's the plugin's name?", 'wpbrigade-sdk' ); ?>">
							</div>
						</li>
						<li class="wpb-sdk_deactivation-frm-reason" data-input-type="" data-input-placeholder="">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="3"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( 'The plugin broke my site', 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
						</li>
						<li class="wpb-sdk_deactivation-frm-reason" data-input-type="" data-input-placeholder="">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="4"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( 'The plugin suddenly stopped working', 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
						</li>
						<li class="wpb-sdk_deactivation-frm-reason" data-input-type="" data-input-placeholder="">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="5"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( 'I no longer need the plugin', 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
						</li>
						<li class="wpb-sdk_deactivation-frm-reason" data-input-type="" data-input-placeholder="">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="6"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( "It's a temporary deactivation. I'm just debugging an issue.", 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
						</li>
						<li class="wpb-sdk_deactivation-frm-reason has-input" data-input-type="textfield">
							<label>
								<span class="wpb-sdk_deactivation-frm-radio"><input type="radio" name="wpb-sdk_deactivation-frm-selected-reason" value="7"></span>
								<span class="wpb-sdk_deactivation-frm-reason-text"><?php esc_html_e( 'Other', 'wpbrigade-sdk' ); ?></span>
							</label>
							<div class="wpb-sdk_deactivation-frm-internal-message"></div>
							<div class="wpb-sdk_deactivation-frm-reason-input">
								<span class="message error-message"><?php esc_html_e( 'Kindly tell us the reason so we can improve.', 'wpbrigade-sdk' ); ?></span>
								<input type="text" name="other_reason" placeholder="<?php esc_html_e( "Would you like to share what's other reason?", 'wpbrigade-sdk' ); ?>">
							</div>
						</li>
					</ul>
				</div>
				<div class="wpb-sdk_deactivation-frm-popup-footer">
					<label class="wpb-sdk_deactivation-frm-anonymous">
						<input type="checkbox" />
						<?php esc_html_e( 'Anonymous feedback', 'wpbrigade-sdk' ); ?>
					</label>
					<input type="button" class="button button-secondary button-skip wpb-sdk_deactivation-frm-popup-skip-feedback" value="Skip &amp; Deactivate">
					<div class="action-btns">
						<span class="wpb-sdk_deactivation-frm-spinner"><img src="<?php echo esc_url( admin_url( '/images/spinner.gif' ) ); ?>" alt="<?php esc_attr_e( 'spinner', 'wpbrigade-sdk' ); ?>"></span>
						<input type="submit" class="button button-secondary button-deactivate wpb-sdk_deactivation-frm-popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
						<a href="#" class="button button-primary wpb-sdk_deactivation-frm-popup-button-close">
							<?php esc_html_e( 'Cancel', 'wpbrigade-sdk' ); ?>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	/**
	 * WPBRIGADE_Logger prints this on admin_print_footer_scripts priority 100 so jQuery exists.
	 */
	(function() {
		if (typeof window.jQuery === 'undefined') {
			return;
		}
		window.jQuery(function($) {

				var pluginSlug = "<?php echo esc_js( $product_slug ); ?>";
				var pluginFile = <?php echo wp_json_encode( (string) $plugin_file_basename ); ?>;
				var pluginName = "<?php echo esc_js( $product_name ); ?>";
				var loggerDeactiveNonce;
				var deactivateReturnUrl = '';

				function deactivateHrefIsOurs(href) {
					if (!href || href.indexOf('action=deactivate') === -1) {
						return false;
					}
					var m = href.match(/[?&]plugin=([^&]+)/);
					if (!m || !m[1]) {
						return false;
					}
					try {
						return decodeURIComponent(m[1].replace(/\+/g, ' ')) === pluginFile;
					} catch (err) {
						return false;
					}
				}

				/**
				 * Core prints tr[data-plugin] with the same string as plugin= (WP_Plugins_List_Table).
				 * Prefer that so we still intercept when the href encoding does not match byte-for-byte.
				 */
				function deactivateLinkIsOurs($a) {
					if (!$a || !$a.length) {
						return false;
					}
					var $tr = $a.closest('tr[data-plugin]');
					if ($tr.length) {
						var dp = $tr.attr('data-plugin') || '';
						if (dp === pluginFile) {
							return true;
						}
					}
					return deactivateHrefIsOurs($a.attr('href') || '');
				}

				// Define the reason details mapping
				var reasonDetailsMap = {
					'1': 'I only needed the plugin for a short period',
					'3': 'The plugin broke my site',
					'4': 'The plugin suddenly stopped working',
					'5': 'I no longer need the plugin',
					'6': 'It\'s a temporary deactivation. I\'m just debugging an issue.'
				};

				$(document).on('click', 'a[href*="action=deactivate"]', function(e) {
					var $link = $(this);
					if (!deactivateLinkIsOurs($link)) {
						return;
					}
					e.preventDefault();
					var href = $link.attr('href') || '';
					deactivateReturnUrl = href;
					var nonceMatch = href.match(/[?&]_wpnonce=([^&]+)/);
					loggerDeactiveNonce = nonceMatch ? nonceMatch[1] : href.split('wpnonce=')[1];
					$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-overlay').addClass('wpb-sdk_deactivation-frm-active');
					$('body').addClass('wpb-sdk_deactivation-frm-hidden');
				});

				$(document).on('click', '.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-button-close', function() {
					close_popup();
				});

				$(document).on(
					'click',
					".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-serveypanel",
					function(e) {
						e.stopPropagation();
					}
				);

				$(document).on('click', function(e) {
					if ($(e.target).closest('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper').length) {
						return;
					}
					var $a = $(e.target).closest('a[href*="action=deactivate"]');
					if ($a.length && deactivateLinkIsOurs($a)) {
						return;
					}
					close_popup();
				});

				$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-reason label').on('click', function() {
					if ($(this).find('input[type="radio"]').is(':checked')) {
						$(this).closest('li').siblings().find('.wpb-sdk_deactivation-frm-reason-input').hide();
						$(this).closest('li').siblings().find('.wpb-sdk_deactivation-frm-internal-message').hide();
						$(this).closest('li').find('.wpb-sdk_deactivation-frm-reason-input').show();
						$(this).closest('li').find('.wpb-sdk_deactivation-frm-internal-message').show();
					}
					$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-pro-message').hide();
				});

				$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper input[type="radio"][name="wpb-sdk_deactivation-frm-selected-reason"]').on('click', function(event) {
					$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-allow-deactivate").removeAttr('disabled');
					$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-skip-feedback").removeAttr('disabled');
				});

				$(document).on('submit', '.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-deactivate-form', function(event) {
					event.preventDefault();
					var reason = $(this).find('input[type="radio"][name="wpb-sdk_deactivation-frm-selected-reason"]:checked').val();
					var reasonDetails = '';

					if (reason == '2') {
						reasonDetails = $(this).find("input[type='text'][name='better_plugin']").val();
					} else if (reason == '7') {
						reasonDetails = $(this).find("input[type='text'][name='other_reason']").val();
					} else if (reasonDetailsMap[reason]) {
						reasonDetails = reasonDetailsMap[reason];  // Use mapped detail for other reasons
					}

					if ((reason == '2' || reason == '7') && reasonDetails == '') {
						$('.message.error-message').show();
						return;
					}

					if (!deactivateReturnUrl) {
						return;
					}

					send_log(
						'wpb_sdk_' + pluginSlug + '_deactivation',
						reason,
						reasonDetails,
						loggerDeactiveNonce,
						deactivateReturnUrl
					);
				});

				$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-skip-feedback').on('click', function(e) {
					if (!deactivateReturnUrl) {
						return;
					}
					send_log(
						'wpb_sdk_' + pluginSlug + '_deactivation',
						9,
						'',
						loggerDeactiveNonce,
						deactivateReturnUrl
					);
				});

				function close_popup() {
					$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-overlay').removeClass('wpb-sdk_deactivation-frm-active');
					$('.<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-deactivate-form').trigger("reset");
					$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-allow-deactivate").attr('disabled', 'disabled');
					$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-reason-input").hide();
					$('body').removeClass('wpb-sdk_deactivation-frm-hidden');
					$('.message.error-message').hide();
					deactivateReturnUrl = '';
				}

				function send_log(_action, _reason, _reasonDetails, _nonce, returnURL) {
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: _action,
							reason: _reason,
							reason_detail: _reasonDetails,
							nonce: _nonce
						},
						beforeSend: function() {
							$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-spinner").show();
							$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-allow-deactivate").attr("disabled", "disabled");
							$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-skip-feedback").attr("disabled", "disabled");
						}
					}).done(function(res) {
						$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-spinner").hide();
						$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-allow-deactivate").removeAttr("disabled");
						$(".<?php echo esc_attr( $product_slug ); ?>-deactivate-wrapper .wpb-sdk_deactivation-frm-popup-skip-feedback").removeAttr("disabled");
						window.location.href = returnURL;
					});
				}
		});
	})();
</script>



