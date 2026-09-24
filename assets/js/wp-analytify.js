var height = 0,
	height2 = 0,
	height3 = 0;

jQuery(document).ready(function ($) {
	/**
	 * Puts #wpfooter inside #wpbody so the rating/version row uses the same column width as
	 * Analytify content (matches manual fix: $('#wpfooter').appendTo($('#wpbody'))).
	 */
	function analytifyRelocateAdminFooter() {
		var body = document.body;
		if (! body || ! body.className) {
			return;
		}
		var cn = body.className;
		if (cn.indexOf('toplevel_page_analytify') === -1 && cn.indexOf('analytify_page_') === -1) {
			return;
		}
		var $footer = $('#wpfooter');
		var $wpbody = $('#wpbody');
		if (! $footer.length || ! $wpbody.length || $footer.closest('#wpbody').length) {
			return;
		}
		$footer.appendTo($wpbody);
	}
	analytifyRelocateAdminFooter();

	// Fallback method for formating dates.
	function formatDate(date) {
		const d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

		if (month.length < 2) {
			month = '0' + month;
		}
		if (day.length < 2) {
			day = '0' + day;
		}

		return [year, month, day].join('-');
	}

	/**
   * [Redirect to the stats box from all posts link]
   */
	if (window.location.hash) {
		const hsh = window.location.hash;
		hash = hsh.split('#');

		if (hash[1] === 'pa-single-admin-analytics') {
			jQuery('html, body').animate(
				{
					scrollTop: jQuery('#pa-single-admin-analytics').offset().top - 55
				},
				1000,
				function () {
					jQuery('html, body').animate(
						{
							scrollTop: jQuery('#pa-single-admin-analytics').offset().top - 55
						},
						1000
					);
				}
			);
		}
	}

	/**
   * [Redirect to the stats box from current posts link]
   */
	$('#view_stats_analytify').on('click', function (event) {
		// Make sure this.hash has a value before overriding default behavior
		if (this.hash !== '') {
			// Prevent default anchor click behavior
			event.preventDefault();

			// Store hash
			const hash = this.hash;

			// Using jQuery's animate() method to add smooth page scroll
			// The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
			$('html, body').animate(
				{
					scrollTop: $(hash).offset().top
				},
				800,
				function () {
					// Add hash (#) to URL when done scrolling (default click behavior)
					window.location.hash = hash;
				}
			);
		} // End if
	});

	$('.remove-stats').remove();

	const analytifyEqualHeight = function (container) {
		var currentTallest = 0,
			currentRowStart = 0,
			rowDivs = new Array(),
			$el,
			topPosition = 0;
		$(container).each(function () {
			$el = $(this);
			$($el).height('auto');
			topPostion = $el.position().top;

			if (currentRowStart != topPostion) {
				for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
					rowDivs[currentDiv].height(currentTallest);
					rowDivs[currentDiv].css('min-height', currentTallest + 'px');
				}
				rowDivs.length = 0; // empty the array
				currentRowStart = topPostion;
				currentTallest = $el.height();
				rowDivs.push($el);
			} else {
				rowDivs.push($el);
				currentTallest =
          currentTallest < $el.height() ? $el.height() : currentTallest;
			}
			for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
				rowDivs[currentDiv].height(currentTallest);
				rowDivs[currentDiv].css('min-height', currentTallest + 'px');
			}
		});
	};

	$('#view_analytics').on('click', function (e) {
		e.preventDefault();

		/**
     * Handles the ajax request for the dashboard stats.
     *
     * @param {string} endpoint The API endpoint.
     * @param {string} nonce    WP nonce.
     * @param {object} data     The data to send in the request.
     *
     * @returns {object}
     */
		const fetch_stats = async (endpoint, nonce, data = {}) => {
			// convert data to url params.
			const params = new URLSearchParams();
			for (const key in data) {
				params.append(key, data[key]);
			}

			const URL = `${endpoint}${
				wp_analytify_script.delimiter
			}${params.toString()}`;

			const request = await fetch(URL, {
				method: 'GET',
				headers: {
					'X-WP-Nonce': nonce
				}
			});

			return request.json();
		};

		/**
     * Generates table from object.
     * {headers} is to generate <thead> tag and content.
     * {stats} is to generate <tbody> tag and content.
     *
     * @param {object} headers       The headers.
     * @param {object} stats         The stats to be shown.
     * @param {string} table_classes Table classes
     * @param {string} attr          Table attributes
     * @returns {string}
     */
		const generate_stats_table = (
			headers,
			stats,
			table_classes = false,
			attr = ''
		) => {
			let markup = ``;

			markup += `<table class="${table_classes}" ${attr}>`;

			let thead = '';
			for (const td_key in headers) {
				const td = headers[td_key];
				if (td.label) {
					thead += `<th class="${td.th_class ? td.th_class : ``}">${
						td.label
					}</th>`;
				}
			}
			if (thead !== '') {
				markup += `<thead><tr>${thead}</tr></thead>`;
			}

			markup += `<tbody>`;

			let i = 1;
			for (const row_id in stats) {
				const row = stats[row_id];
				markup += `<tr>`;
				for (const td_key in row) {
					let __label = '';

					if (
						row[td_key] === null &&
            headers[td_key].type &&
            headers[td_key].type === 'counter'
					) {
						__label = i;
					} else if (row[td_key].label) {
						__label = row[td_key].label;
					} else if (row[td_key].value) {
						__label = row[td_key].value;
					} else {
						__label = row[td_key];
					}

					let __class = '';
					if (row[td_key] && row[td_key].class) {
						__class = row[td_key].class;
					} else if (headers[td_key] && headers[td_key].td_class) {
						__class = headers[td_key].td_class;
					}

					markup += `<td class="${__class}">${__label}</td>`;
				}
				markup += `</tr>`;
				i++;
			}
			markup += `</tbody>`;

			markup += `</table>`;

			return markup;
		};

		const empty_stats = (message, table_header = false, table_class = '') => {
			let markup = `<div class="analytify-stats-error-msg">
					<div class="wpb-error-box">
						<span class="blk">
							<span class="line"></span>
							<span class="dot"></span>
						</span>
						<span class="information-txt">${message}</span>
					</div>
				</div>`;
			if (table_header) {
				// If the error message is to be shown in a table.
				let thead = '';
				for (const td_key in table_header) {
					const td = table_header[td_key];
					if (td.label) {
						thead += `<th class="${td.th_class ? td.th_class : ``}">${
							td.label
						}</th>`;
					}
				}

				markup = `<table class="${table_class}">${
					thead !== '' ? `<thead><tr>${thead}</tr></thead>` : ''
				}<tbody><tr><td class="analytify_td_error_msg" colspan="${
					Object.keys(table_header).length
				}">${markup}</td></tr></tbody></table>`;
			}
			return markup;
		};

		$('#pa-single-admin-analytics .show-hide')
			.html('')
			.addClass('stats_loading');

		const fetch_data = {};

		fetch_data.post_id = $('#post_ID').val();
		fetch_data.sd = $('#analytify_date_start').val();
		fetch_data.ed = $('#analytify_date_end').val();

		fetch_stats(
			wp_analytify_script.url + 'single-post-stats',
			wp_analytify_script.nonce,
			fetch_data
		)
			.then((response) => {
				let markup = '';

				if (response.success) {
					markup += `<p class="analytify-heading">${response.heading}</p><div class="analytify_wraper analytify_single_post_page">`;

					if (response.general_stats) {
						markup += `<div class="analytify_general_status analytify_status_box_wraper">
						<div class="analytify_status_header">
							<h3>${response.general_stats.title}</h3>
						</div>
						<div class="analytify_status_body">
							<div class="analytify_general_status_boxes_wraper">`;

						for (const box_key in response.general_stats.stats) {
							const box = response.general_stats.stats[box_key];
							markup += `<div class="analytify_general_status_boxes">
									<h4>${box.title}</h4>
									<div class="analytify_general_stats_value">${box.value} ${
	box.append ? box.append : ``
} </div>
									<div class="analytify_info_tooltip">
										<p>${box.description}</p>
									</div>
								</div>`;
						}
						if (response.general_stats.new_vs_returning) {
							let total_visitors = 0;
							for (const new_vs_returning_box_key in response.general_stats
								.new_vs_returning) {
								const new_vs_returning_box =
					response.general_stats.new_vs_returning[
						new_vs_returning_box_key
					];
								total_visitors =
									parseInt(new_vs_returning_box.stats.new.number) +
									parseInt(new_vs_returning_box.stats.returning.number);
								markup += `<div class="analytify_general_status_boxes">
									<h4>${new_vs_returning_box.title}</h4>
									<div class="analytify_info_tooltip">
										<strong>${new_vs_returning_box.stats.new.label} = ${
	(new_vs_returning_box.stats.new.number / total_visitors) * 100 >
											0
		? Math.round(
			(new_vs_returning_box.stats.new.number / total_visitors) *
													100
		)
		: 0
} %</strong><br>
										<strong>${
	new_vs_returning_box.stats.returning.label
} = ${
	(new_vs_returning_box.stats.returning.number / total_visitors) *
											100 >
											0
		? Math.round(
			(new_vs_returning_box.stats.returning.number /
														total_visitors) *
													100
		)
		: 0
} %</strong>
									</div>
								</div>`;
							}
						}
						if (response.general_stats.device_visitors) {
							let total_devices = 0;
							for (const device_visitors_box_key in response.general_stats
								.device_visitors) {
								const device_visitors_box =
                  response.general_stats.device_visitors[device_visitors_box_key];
								total_devices =
                  parseInt(device_visitors_box.stats.mobile.number) +
                  parseInt(device_visitors_box.stats.tablet.number) +
                  parseInt(device_visitors_box.stats.desktop.number);

								markup += `<div class="analytify_general_status_boxes">
                    <h4>${device_visitors_box.title}</h4>
                        <div class="analytify_info_tooltip">
                        <strong>${device_visitors_box.stats.mobile.label} = ${
	(device_visitors_box.stats.mobile.number / total_devices) *
                    100 >
                  0
		? Math.round(
			(device_visitors_box.stats.mobile.number /
                          total_devices) *
                        100
		)
		: 0
} %</strong><br>
                        <strong>${device_visitors_box.stats.tablet.label} = ${
	(device_visitors_box.stats.tablet.number / total_devices) *
                    100 >
                  0
		? Math.round(
			(device_visitors_box.stats.tablet.number /
                          total_devices) *
                        100
		)
		: 0
} %</strong><br>
                        <strong>${device_visitors_box.stats.desktop.label} = ${
	(device_visitors_box.stats.desktop.number / total_devices) *
                    100 >
                  0
		? Math.round(
			(device_visitors_box.stats.desktop.number /
                          total_devices) *
                        100
		)
		: 0
} %</strong>
                        </div>
                  </div>`;
							}
						}
						markup += `</div>
							</div>
							${
	response.general_stats.footer
		? `
								<div class="analytify_status_footer">
									<span class="analytify_info_stats">${response.general_stats.footer}</span>
								</div>`
		: ``
}
						</div>`;
					}

					// scroll_depth
					if (response.scroll_depth) {
						markup += `<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3>${response.scroll_depth.title}</h3>
							</div>
							${
	response.scroll_depth.stats.length
		? generate_stats_table(
			response.scroll_depth.headers,
			response.scroll_depth.stats,
			response.scroll_depth.table_class
		)
		: empty_stats(wp_analytify_script.no_stats_message)
}
						</div>`;
					}

					// geographic
					if (response.geographic) {
						markup += `<div class="analytify_geographic_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3>${response.geographic.title}</h3>
							</div>
							<div class="analytify_status_body">
								<div class="analytify_clearfix">
									<div class="analytify_pull_left analytify_half">
										${
	response.geographic.sections.countries.stats.length
		? generate_stats_table(
			response.geographic.sections.countries.headers,
			response.geographic.sections.countries.stats,
			'analytify_data_tables analytify_border_th_tp'
		)
		: empty_stats(wp_analytify_script.no_stats_message)
}
									</div>
									<div class="analytify_pull_left analytify_half">
										${
	response.geographic.sections.countries.stats.length
		? generate_stats_table(
			response.geographic.sections.cities.headers,
			response.geographic.sections.cities.stats,
			'analytify_data_tables analytify_border_th_tp'
		)
		: empty_stats(wp_analytify_script.no_stats_message)
}
									</div>
								</div>
							</div>
							${
	response.geographic.footer
		? `<div class="analytify_status_footer">
									<span class="analytify_info_stats">${response.geographic.footer}</span>
								</div>`
		: ``
}
						</div>`;
					}

					if (response.system_stats) {
						markup += `<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3>${response.system_stats.title}</h3>
							</div>
							<div class="analytify_status_body analytify_clearfix">
								
								<div class="analytify_one_tree_table">
									${
	response?.system_stats?.sections?.browser?.stats?.length
		? generate_stats_table(
			response.system_stats.sections.browser.headers,
			response.system_stats.sections.browser.stats,
			'analytify_data_tables'
		)
		: empty_stats(
			wp_analytify_script.no_stats_message,
			response?.system_stats?.sections?.browser?.headers,
			'analytify_data_tables'
		)
}
								</div>

								<div class="analytify_one_tree_table">
									${
	response.system_stats.sections.os.stats.length
		? generate_stats_table(
			response.system_stats.sections.os.headers,
			response.system_stats.sections.os.stats,
			'analytify_data_tables'
		)
		: empty_stats(
			wp_analytify_script.no_stats_message,
			response?.system_stats?.sections?.os?.headers,
			'analytify_data_tables'
		)
}
								</div>
								
								<div class="analytify_one_tree_table">
									${
	response.system_stats.sections.mobile.stats.length
		? generate_stats_table(
			response.system_stats.sections.mobile.headers,
			response.system_stats.sections.mobile.stats,
			'analytify_data_tables'
		)
		: empty_stats(
			wp_analytify_script.no_stats_message,
			response?.system_stats?.sections?.mobile?.headers,
			'analytify_data_tables'
		)
}
								</div>

							</div>
							${
	response.system_stats.footer
		? `<div class="analytify_status_footer">
									<span class="analytify_info_stats">${response.system_stats.footer}</span>
								</div>`
		: ``
}
						</div>`;
					}

					if (response.social_media) {
						markup += `<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3>${response.social_media.title}</h3>
							</div>
							<div class="analytify_status_body">
								${
	response.social_media.stats.length
		? generate_stats_table(
			response.social_media.headers,
			response.social_media.stats,
			'analytify_data_tables analytify_no_header_table'
		)
		: empty_stats(wp_analytify_script.no_stats_message)
}
							</div>
							${
	response.social_media.footer
		? `<div class="analytify_status_footer">
									<span class="analytify_info_stats">${response.social_media.footer}</span>
								</div>`
		: ``
}
						</div>`;
					}

					if (response.referer) {
						markup += `<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3>${response.referer.title}</h3>
							</div>
							<div class="analytify_status_body">
								${
	response.referer.stats.length
		? generate_stats_table(
			response.referer.headers,
			response.referer.stats,
			'analytify_bar_tables'
		)
		: empty_stats(wp_analytify_script.no_stats_message)
}
							</div>
							${
	response.referer.footer
		? `<div class="analytify_status_footer">
									<span class="analytify_info_stats">${response.referer.footer}</span>
								</div>`
		: ``
}
						</div>`;
					}

					if (response.video_tracking) {
						markup += `<div class="analytify_general_status analytify_status_box_wraper">
							<div class="analytify_status_header">
								<h3>${response.video_tracking.title}</h3>
							</div>
							<div class="analytify_status_body">
								${
	response.video_tracking.stats.length
		? generate_stats_table(
			response.video_tracking.headers,
			response.video_tracking.stats,
			'analytify_data_tables'
		)
		: empty_stats(wp_analytify_script.no_stats_message)
}
							</div>
							${
	response.video_tracking.footer
		? `<div class="analytify_status_footer">
									<span class="analytify_info_stats">${response.video_tracking.footer}</span>
								</div>`
		: ``
}
						</div>`;
					}

					markup += `</div>`;
				} else {
					// alert('Oops: Something is wrong , Please contact our support.');
				}

				$('#pa-single-admin-analytics .show-hide')
					.html(markup)
					.removeClass('stats_loading');
				analytifyEqualHeight('.analytify_general_status_boxes');
			})
			.catch(function (error) {
				// alert('Oops: Something is wrong, Please contact our support.');
				// console.log(error);
			});
	});

	$(window).on('resize', function () {
		analytifyEqualHeight('.analytify_general_status_boxes');
	});

	/* $("#disable_front").change(function(){
			  var ischecked=$(this).is(':checked');
			  if(ischecked)
				{
				   $(".disable").css("display", "none");
				}
			  else
				{
				  $(".disable").css("display", "block");
				}
			  });*/
	$('.arrow_btn').on('click', function () {
		$(this)
			.parent()
			.next()
			.slideToggle('slow')
			.next()
			.slideToggle('slow')
			.end()
			.end()
			.toggleClass('close');
	});

	$('.authentication_btn').on('click', function () {
		$('.authentication_table').addClass('active').removeClass('show_btn');
		$('.over_lap_bg').fadeIn();
	});

	$('.over_lap_bg').on('click', function () {
		$('.authentication_table').addClass('show_btn');
		$(this).hide();
	});

	$('.grids_auto_size').each(function () {
		if ($(this).height() > height) {
			height = $(this).height();
		}
	});

	$('.grids_auto_size').each(function () {
		$(this).css('min-height', height);
	});
	$('.keywordscont').each(function () {
		if ($(this).height() > height2) {
			height2 = $(this).height();
		}
	});
	$('.keywordscont').each(function () {
		$(this).css('min-height', height2);
	});

	$('.stats').each(function () {
		if ($(this).height() > height3) {
			height3 = $(this).height();
		}
	});
	$('.stats').each(function () {
		$(this).css('min-height', height3);
	});

	/**
	 * Apply dynamic colors to engagement rate progress bars
	 * Based on Analytify style guide: 0-40% red, 40-60% yellow, above 60% green
	 */
	function applyEngagementBarColors() {
		$('.analytify_engagement_bar').each(function () {
			const rate = parseFloat($(this).data('rate'));
			let color = '#ff5252'; // Default red for 0-40%

			if (rate >= 40 && rate < 60) {
				color = '#ffc107'; // Yellow for 40-60%
			} else if (rate >= 60) {
				color = '#00c853'; // Green for above 60%
			}

			// Force the color with !important
			$(this).css('background-color', color + ' !important');

			// Also add a class for additional CSS targeting
			$(this).removeClass('analytify-rate-low analytify-rate-medium analytify-rate-high');
			if (rate >= 60) {
				$(this).addClass('analytify-rate-high');
			} else if (rate >= 40) {
				$(this).addClass('analytify-rate-medium');
			} else {
				$(this).addClass('analytify-rate-low');
			}
		});
	}

	// Apply colors when page loads
	applyEngagementBarColors();

	// Apply colors when new content is loaded via AJAX
	$(document).on('DOMNodeInserted', function () {
		setTimeout(applyEngagementBarColors, 100);
	});

	// Also apply colors when stats are loaded
	$(document).on('analytify_stats_loaded', function () {
		setTimeout(applyEngagementBarColors, 200);
	});

	// Force apply colors after a delay to ensure DOM is ready
	setTimeout(function () {
		applyEngagementBarColors();
	}, 1000);

	// Also run on window load
	$(window).on('load', function () {
		setTimeout(applyEngagementBarColors, 500);
	});
});
