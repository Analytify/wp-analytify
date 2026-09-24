/**
 * 404 / JavaScript / AJAX error tracking (GA4 + gtag).
 *
 * @package WP_Analytify
 * @since 5.0.0
 * @version 9.1.0
 */
jQuery(document).ready(function ($) {
	if (typeof miscellaneous_tracking_options === 'undefined' || typeof gtag === 'undefined') {
		return;
	}

	var opts = miscellaneous_tracking_options;
	var track404 = opts.track_404_page || {};

	// Track 404 page errors.
	if (track404.should_track === 'on' && track404.is_404) {
		gtag('event', '404_error', {
			wpa_category: '404 Error',
			wpa_label: track404.current_url
		});
	}

	// Track JavaScript errors.
	if (opts.track_js_error === 'on') {
		window.addEventListener('error', function (e) {
			gtag('event', 'js_error', {
				wpa_category: 'JavaScript Error',
				wpa_action: e.message,
				wpa_label: e.filename + ': ' + e.lineno,
				non_interaction: true
			});
		}, false);
	}

	// Track jQuery AJAX errors.
	if (opts.track_ajax_error === 'on') {
		$(document).ajaxError(function (e, request, settings) {
			gtag('event', 'ajax_error', {
				wpa_category: 'Ajax Error',
				wpa_action: request.statusText,
				wpa_label: settings.url,
				non_interaction: true
			});
		});
	}
});
