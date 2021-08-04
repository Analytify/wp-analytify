jQuery(document).ready(function($) {

	$("#add_email").on('submit', function(event) {
			event.preventDefault();
	});

	var doing_license_registration_ajax = false;
	var admin_url = ajaxurl.replace('/admin-ajax.php', ''),
			spinner_url = admin_url + '/images/spinner';

	if (2 < window.devicePixelRatio) {
			spinner_url += '-2x';
	}
	spinner_url += '.gif';

	var ajax_spinner = '<img src="' + spinner_url + '" alt="" class="ajax-spinner general-spinner" />';

	$(document).on('click', "#analytify_email_license_activate", function(e) {

			e.preventDefault();

			if (doing_license_registration_ajax) {
					return;
			}

			$('#email-license-status').removeClass('notification-message error-notice');

			var license_key = $.trim($("#analytify_email_license_key").val());

			if ('' === license_key) {
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
					error: function(jqXHR, textStatus, errorThrown) {
							doing_license_registration_ajax = false;
							$('.register-license-ajax-spinner').remove();
							$('#email-license-status').html(wpanalytify_strings.register_license_problem);
					},
					success: function(data) {
							doing_license_registration_ajax = false;
							$('.register-license-ajax-spinner').remove();


							if ('undefined' !== typeof data.error) {

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


	// Send Stats via Email
	$("#send_single_analytics").on('click', function (e) {

		e.preventDefault();
		var start_date = $("#analytify_start").val();
		start_date = moment(start_date, 'MMM DD, YYYY').format("YYYY-MM-DD");

		var end_date = $("#analytify_end").val();
		end_date =  moment(end_date, 'MMM DD, YYYY').format("YYYY-MM-DD");

		var urlpost = $("#post_ID").val();

		$.ajax({

			type: 'POST',
			url: ajaxurl,
			data: 'action=send_analytics_email&start_date=' + start_date + "&end_date=" + end_date+"&post_id="+urlpost,

			beforeSend: function () {
				$('.send_email.stats_loading').css('display', 'inline-block');
				$('#send_single_analytics').attr('disabled', 'disabled');
			},
			success: function (data, textStatus, XMLHttpRequest) {
				$('.send_email.stats_loading').css('display', 'none');
				$('#send_single_analytics').removeAttr('disabled');
			},
			error: function (MLHttpRequest, textStatus, errorThrown) {
				alert("Oops: Something is wrong, Please contact our support.");
			}
		});

	});


	$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[value\\]').on('change', function(event) {
		event.preventDefault();
		if( $(this).val() == 'week' ){
			$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[week\\]').show();
			$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[month\\]').hide();
		}else{
			$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[week\\]').hide();
			$('#wp-analytify-email\\[analytif_email_cron_time\\]\\[month\\]').show();
		}

	});

});
