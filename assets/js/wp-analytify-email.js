/* global wpAnalytifyEmail */
(function ($) {
	'use strict';

	// Safe reference: wpAnalytifyEmail is localized whenever this script is enqueued (analytify-email bootstrap).
	var emailStrings = typeof wpAnalytifyEmail !== 'undefined' && wpAnalytifyEmail ? wpAnalytifyEmail : {
		sendEmailReport: 'Send Email Report',
		sending: 'Sending...',
		placeholderRecipient: 'Enter recipient email',
		emailReportSent: 'Email Report Sent!'
	};

	$(document).ready(function () {

		$('#add_email').on('submit', function (event) {
			event.preventDefault();
		});

		var doing_license_registration_ajax = false;
		var admin_url = ajaxurl.replace('/admin-ajax.php', ''),
			spinner_url = admin_url + '/images/spinner';

		if (window.devicePixelRatio > 2) {
			spinner_url += '-2x';
		}
		spinner_url += '.gif';

		var ajax_spinner = '<img src="' + spinner_url + '" alt="" class="ajax-spinner general-spinner" />';

		$(document).on('click', '#analytify_email_license_activate', function (e) {

			e.preventDefault();

			if (doing_license_registration_ajax) {
				return;
			}

			$('#email-license-status').removeClass('notification-message error-notice');

			var license_key = $.trim($('#analytify_email_license_key').val());

			if (license_key === '') {
				$('#email-license-status').addClass('notification-message error-notice');
				$('#email-license-status').html(wpanalytify_strings.enter_license_key);
				return;
			}

			$('#email-license-status').empty().removeClass('success-notice');
			doing_license_registration_ajax = true;
			$('#analytify_email_license_activate').after('<img src="' + spinner_url + '" alt="" class="register-license-ajax-spinner general-spinner" />');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				cache: false,
				data: {
					action: 'wpanalytifyemail_activate_license',
					email_license_key: license_key,
					nonce: wpanalytify_data.nonces.activate_license,
					context: 'license'
				},
				error: function (jqXHR, textStatus, errorThrown) {
					doing_license_registration_ajax = false;
					$('.register-license-ajax-spinner').remove();
					$('#email-license-status').html(wpanalytify_strings.register_license_problem);
				},
				success: function (data) {
					doing_license_registration_ajax = false;
					$('.register-license-ajax-spinner').remove();


					if (typeof data.error !== 'undefined') {

						$('#email-license-status').addClass('notification-message error-notice');
						$('#email-license-status').html(data.error);

					} else if (data == '0') {

						$('#email-license-status').addClass('notification-message error-notice');
						$('#email-license-status').html(wpanalytify_strings.register_license_problem);
					} else {
						$('#email-license-status').html(wpanalytify_strings.license_registered).delay(5000).fadeOut(1000);
						$('#email-license-status').addClass('notification-message success-notice');
						$('#analytify_email_license_key, #analytify_email_license_activate').remove();
						$('.email-license-row').prepend(data.masked_license);

					}
				}
			});
		});
		// Send Stats via Email (single click; button disabled until request completes to avoid stacked messages).
		$('#send_single_analytics').off('click.wpAnalytifyEmail').on('click.wpAnalytifyEmail', function (e) {
			e.preventDefault();

			var $btn = $('#send_single_analytics');
			if ($btn.prop('disabled')) {
				return;
			}

			if (typeof moment !== 'function') {
				return;
			}

			var start_date = $('#analytify_start').val();
			start_date = moment(start_date, 'MMM DD, YYYY').format('YYYY-MM-DD');

			var end_date = $('#analytify_end').val();
			end_date = moment(end_date, 'MMM DD, YYYY').format('YYYY-MM-DD');

			var urlpost = $('#post_ID').val();
			var recipient_email = $('#recipient_email').val();

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'send_analytics_email',
					start_date: start_date,
					end_date: end_date,
					post_id: urlpost,
					recipient_email: recipient_email,
					nonce: wpanalytify_data.nonces.send_single_post_email
				},
				beforeSend: function () {
					$btn.attr('disabled', 'disabled').val(emailStrings.sending);
					$('.email-sent-error').remove();
				},
				success: function (data, textStatus, XMLHttpRequest) {
					$('.send_email.stats_loading').css('display', 'none');
					$btn.removeAttr('disabled').val(emailStrings.sendEmailReport);
					$('#recipient_email').val('').attr('placeholder', emailStrings.placeholderRecipient);
					$('#send_email_to_individual').prop('checked', false);
					$('#recipient_email').css('display', 'none');

					$('.email-sent-success').remove();
					var $msg = $('<span class="email-sent-success" style="color: #6ab074; margin-left:7px;"></span>').text(emailStrings.emailReportSent || '');
					$msg.insertAfter($btn)
						.delay(3000)
						.fadeOut(1000, function () {
							$(this).remove();
						});
				},
				error: function (MLHttpRequest, textStatus, errorThrown) {
					$btn.removeAttr('disabled').val(emailStrings.sendEmailReport);
					$('.email-sent-error').remove();
					var $err = $('<span class="email-sent-error" style="color: #a00; margin-left:7px;"></span>').text(emailStrings.emailSendFailed || '');
					if ($err.text()) {
						$err.insertAfter($btn)
							.delay(5000)
							.fadeOut(500, function () {
								$(this).remove();
							});
					}
				}
			});
		});


		$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[value\\]').on('change', function (event) {
			event.preventDefault();
			if ($(this).val() == 'week') {
				$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[week\\]').show();
				$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[month\\]').hide();
			} else {
				$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[week\\]').hide();
				$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[month\\]').show();
			}

		});

	});
})(jQuery);
