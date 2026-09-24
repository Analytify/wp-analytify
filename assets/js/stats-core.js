'use strict';

/**
 *
 * This file handles all the ajax requests for the core dashboard.
 * This also includes generating charts and populating the data into a view.
 *
 */

jQuery(function ($) {
	const equalizeSystemStatsTables = (target) => {
		const containers = target.find('.analytify_status_body .analytify_one_tree_table');
		let maxHeight = 0;
		containers.each(function () {
			const h = $(this).outerHeight();
			if (h > maxHeight) {
				maxHeight = h;
			}
		});
		containers.css('min-height', maxHeight);
		containers.each(function () {
			const table = $(this).find('table.analytify_data_tables');
			const headH = table.find('thead').outerHeight() || 0;
			const msg = table.find('.analytify-stats-error-msg');
			if (msg.length) {
				msg.css({ 'min-height': Math.max(0, maxHeight - headH) });
				msg.find('.wpb-error-box').css({ display: 'flex', 'align-items': 'center', 'justify-content': 'center' });
			}
		});
	};

	$(window).on('resize', function () {
		const section = $('.analytify_section_system_stats');
		if (section.length) {
			equalizeSystemStatsTables(section);
		}
	});
	function removeHtmlTags(inputString) {
		if (typeof inputString !== 'string') {
			// If inputString is not a string, return it as is
			return inputString;
		}

		return inputString.replace(/<\/?[^>]+(>|$)/g, '');
	}

	/**
	 * Handles the ajax request for the dashboard stats.
	 *
	 * @param {string} endpoint The API endpoint.
	 * @param {object} data     The data to send in the request.
	 *
	 * @returns {object}
	 */
	const fetch_stats = async (endpoint, data = {}) => {

		// start/end dates and date differ.
		if ($('#analytify_date_start').length) {
			data.sd = $('#analytify_date_start').val();
		}
		if ($('#analytify_date_end').length) {
			data.ed = $('#analytify_date_end').val();
		}
		if ($('#analytify_date_diff').length) {
			data.d_diff = $('#analytify_date_diff').val();
		}

		// convert data to url params.
		const params = new URLSearchParams();
		for (const key in data) {
			params.append(key, data[key]);
		}

		const URL = `${analytify_stats_core.url + endpoint + '/' + analytify_stats_core.delimiter}${params.toString()}`;

		const request = await fetch(URL, {
			method: 'GET',
			headers: {
				'X-WP-Nonce': analytify_stats_core.nonce
			}
		});
		return request.json();
	};

	/**
	 * Sets the target element to 'loading' state.
	 * Also clear the element's contents.
	 *
	 * @param {element} target The target element.
	 */
	const prepare_section = (target) => {
		target.find('.analytify_stats_loading').show();
		target.find('.analytify_status_body .stats-wrapper').html('');
		target.find('.analytify_status_footer').remove();
		target.find('.empty-on-loading').html('');
	};

	/**
	 * Element should not be loading.
	 *
	 * @param {element} target The target element.
	 */
	const should_not_be_loading = (target) => {
		target.find('.analytify_stats_loading').hide();
	};

	/**
	 * Sets the content for the element.
	 * Removes the 'loading' state.
	 *
	 * @param {element} target     The target element.
	 * @param {string}  markup     The markup to be set (should be HTML).
	 * @param {string}  footer     The footer markup to be set.
	 * @param {boolean} pagination Whether to show the pagination or not.
	 */
	function set_section(target, markup, footer = false, pagination = false) {
		should_not_be_loading(target);
		target.find('.analytify_status_body .stats-wrapper').html(markup);

		// Ensure only one footer exists per section (prevents double footers).
		target.find('.analytify_status_footer').remove();

		if (footer || pagination) {
			const footer_markup = `<div class="analytify_status_footer">
				${footer ? `<span class="analytify_info_stats">${footer}</span>` : ''}
				${pagination ? `<div class="wp_analytify_pagination"></div>` : ''}
			</div>`;
			target.find('.analytify_status_body').after(footer_markup);
		}

		jQuery('.analytify_data_tables thead th').each(function () {
			var index = jQuery(this).index();
			var value = $(this).clone();
			value = value.find('*').remove().end().html();
			jQuery(this).closest('.analytify_data_tables').find('tbody tr').each(function () {
				jQuery(this).find('td').eq(index).attr('data-heading', value);
			});
		});
	};

	/**
	 * Generates the empty stats message.
	 *
	 * @param {element} target       The target element.
	 * @param {string}  message      The message to be shown.
	 * @param {object}  table_header The table header, if the message needs to be shown in a table.
	 * @param {string}  table_class  The table class.
	 */
	const stats_message = (target = false, message = false, table_header = false, table_class = '') => {
		let markup = `<div class="analytify-stats-error-msg">
			<div class="wpb-error-box">
				<span class="blk"><span class="line"></span><span class="dot"></span></span>
				<span class="information-txt">${message ? message : analytify_stats_core.no_stats_message}</span>
			</div>
		</div>`;

		if (table_header) {
			// If the error message is to be shown in a table.
			let thead = '';
			for (const td_key in table_header) {
				const td = table_header[td_key];
				if (td.label) {
					thead += `<th class="${td.th_class ? td.th_class : ``}">${td.label}</th>`;
				}
			}

			markup = `<table class="${table_class}">${thead !== '' ? `<thead><tr>${thead}</tr></thead>` : ''}<tbody><tr><td class="analytify_td_error_msg" colspan="${Object.keys(table_header).length}">${markup}</td></tr></tbody></table>`;
		}

		if (! target) {
			return markup;
		}
		set_section(target, markup, false, false);
	};

	/**
	 * Generates the red message box.
	 *
	 * @param {element} target       The target element.
	 * @param {string}  message      The message to be shown.
	 * @param {object}  table_header The table header, if the message needs to be shown in a table.
	 * @param {string}  table_class  The table class.
	 */
	const red_stats_message = (target = false, message = false, table_header = false, table_class = '') => {
		let markup = `<div class="analytify-email-promo-contianer">
			<div class="analytify-email-premium-overlay">
				<div class="analytify-email-premium-popup">
					${message.title ? `<h3 class="analytify-promo-popup-heading" style="text-align:left;">${message.title}</h3>` : ``}
					${message.content ? message.content : ``}
				</div>
			</div>
		</div>`;

		if (table_header) {
			// If the error message is to be shown in a table.
			let thead = '';
			for (const td_key in table_header) {
				const td = table_header[td_key];
				if (td.label) {
					thead += `<th class="${td.th_class ? td.th_class : ``}">${td.label}</th>`;
				}
			}

			markup = `<table class="${table_class}">${thead !== '' ? `<thead><tr>${thead}</tr></thead>` : ''}<tbody><tr><td class="analytify_td_error_msg" colspan="${Object.keys(table_header).length}">${markup}</td></tr></tbody></table>`;
		}

		if (! target) {
			return markup;
		}
		set_section(target, markup, false, false);
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
	const generate_stats_table = (headers, stats, table_classes = false, attr = '') => {

		let markup = ``;

		markup += `<table class="${table_classes}" ${attr}>`;

		let thead = '';
		for (const td_key in headers) {
			const td = headers[td_key];
			if (td.label) {
				thead += `<th class="${td.th_class ? td.th_class : ``}">${td.label}</th>`;
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

				if (row[td_key] === null && headers[td_key].type && headers[td_key].type === 'counter') {
					__label = i;
				} else if (row[td_key].label) {
					__label = row[td_key].label;
				} else if (row[td_key].value) {
					__label = row[td_key].value;
				} else {
					__label = row[td_key];
				}

				let __class = '';
				let __heading = '';
				if (row[td_key] && row[td_key].class) {
					__class = row[td_key].class;
				} else if (headers[td_key] && headers[td_key].td_class) {
					__class = headers[td_key].td_class;
					__heading = headers[td_key].label;
					var stringWithoutHtml = removeHtmlTags(__heading);
				}
				markup += `<td data-heading="${stringWithoutHtml}" class="${__class}">${__label}</td>`;
			}
			markup += `</tr>`;
			i++;
		}
		markup += `</tbody>`;

		markup += `</table>`;

		return markup;

	};

	/**
	* Generates the markup for the 'General Stats' section.
	*
	* @param {object}  response The response from the API.
	* @param {element} target   The target element.
	*/
	const generate_general_stats_markup = (response, target) => {

		let markup = '';
		let box_number = 1;
		// loop over response.boxes
		for (const box_key in response.boxes) {
			const box = response.boxes[box_key];

			markup += `<div class="analytify_general_status_boxes${box_number === Object.keys(response.boxes).length ? ' pad_b_0' : ''}">`;
			markup += `<h4>${box.title}</h4>
					<div class="analytify_general_stats_value">
						${box.prepend ? box.prepend : ''}${box.number}${box.append ? box.append : ''}
					</div>
					<p>${box.description ? box.description : ''}</p>`;
			if (box.bottom) {
				markup += `<div class="analytify_general_status_footer_info">
						<span class="analytify_info_value ${box.bottom.arrow_type}">${box.bottom.main_text}</span>
						${box.bottom.sub_text}
					</div>`;
			}
			markup += `</div>`;

			box_number++;
		}

		// loop over response.charts
		for (const chart_key in response.charts) {
			const chart = response.charts[chart_key];

			const chart_stats = encodeURIComponent(JSON.stringify(chart.stats));
			const chart_colors = encodeURIComponent(JSON.stringify(chart.colors));
			const chart_title_markup = chart.description
				? `<h4>${chart.title}<span class="analytify_chart_hint"><span class="dashicons dashicons-info analytify_chart_hint_icon" aria-hidden="true"></span><span class="analytify_chart_hint_tooltip">${chart.description}</span></span></h4>`
				: `<h4>${chart.title}</h4>`;

			markup += `<div class="analytify_general_status_boxes pad_b_0">
				${chart_title_markup}
				<div id="analytify_chart_${chart_key}" style="height:240px;" data-chart-title="${chart.title}" data-stats="${chart_stats}" data-colors="${chart_colors}"></div>
			</div>`;
		}

		set_section(target, markup, response.footer, false);

	};

	/**
	 * Estimates legend label width for layout calculations.
	 *
	 * @param {string} text     Legend label text.
	 * @param {number} fontSize Legend font size.
	 * @returns {number}
	 */
	const estimateLegendLabelWidth = (text, fontSize) => {
		return 24 + String(text).length * fontSize * 0.55;
	};

	/**
	 * Calculates pie center/radius from legend size before rendering.
	 *
	 * @param {HTMLElement} container    Chart container element.
	 * @param {object}      legendOptions ECharts legend options.
	 * @returns {object}
	 */
	const getPieLayoutForLegend = (container, legendOptions) => {
		const defaults = { radius: 70, centerY: 43 };
		const minRadius = 40;
		const minCenterY = 30;

		if (! container || ! legendOptions || ! legendOptions.data || ! legendOptions.data.length) {
			return defaults;
		}

		const containerWidth = container.clientWidth || 300;
		const containerHeight = container.clientHeight || 240;
		const fontSize = ( legendOptions.textStyle && legendOptions.textStyle.fontSize ) || 14;
		const itemGap = typeof legendOptions.itemGap === 'number' ? legendOptions.itemGap : 10;
		const bottomPercent = parseFloat(legendOptions.bottom) || 5;
		const formatter = typeof legendOptions.formatter === 'function' ? legendOptions.formatter : null;
		let totalWidth = 0;

		legendOptions.data.forEach(function (name) {
			const label = formatter ? formatter(name) : name;
			totalWidth += estimateLegendLabelWidth(label, fontSize) + itemGap;
		});

		const rows = Math.max(1, Math.ceil(totalWidth / containerWidth));
		const legendHeight = rows * ( fontSize + 12 );
		const bottomSpace = containerHeight * ( bottomPercent / 100 );
		const legendSpace = legendHeight + bottomSpace + 10;
		const availableHeight = containerHeight - legendSpace;

		if (rows <= 1 && totalWidth <= containerWidth * 0.98) {
			return defaults;
		}

		const centerY = Math.max(
			minCenterY,
			Math.min(defaults.centerY, Math.floor(( ( availableHeight / 2 ) / containerHeight ) * 100))
		);
		const radius = Math.max(
			minRadius,
			Math.min(defaults.radius, Math.floor(( availableHeight / containerHeight ) * 80))
		);

		return { radius: radius, centerY: centerY };
	};

	/**
	 * Applies calculated pie layout to chart options.
	 *
	 * @param {HTMLElement} container Chart container element.
	 * @param {object}      options   ECharts options.
	 * @returns {object}
	 */
	const applyPieLayoutToOptions = (container, options) => {
		const layout = getPieLayoutForLegend(container, options.legend);

		if (options.series && options.series[0]) {
			options.series[0].center = ['50%', layout.centerY + '%'];
			options.series[0].radius = layout.radius + '%';
			options.series[0].emphasis = $.extend({ scale: false }, options.series[0].emphasis || {});
		}

		return layout;
	};

	/**
	 * Initializes a pie chart and sizes it around the legend.
	 *
	 * @param {string} elementId Chart container ID.
	 * @param {object} options   ECharts options.
	 * @returns {object}
	 */
	const initPieChartWithLegendFit = (elementId, options) => {
		const dom = document.getElementById(elementId);
		const existingChart = echarts.getInstanceByDom(dom);

		if (existingChart) {
			existingChart.dispose();
		}

		applyPieLayoutToOptions(dom, options);

		const chart = echarts.init(dom);
		chart.setOption(options);

		$(window).off('resize.analytifyPie.' + elementId);
		$(window).on('resize.analytifyPie.' + elementId, function () {
			chart.resize();
			if (options.series && options.series[0]) {
				options.series[0].center = ['50%', '43%'];
				options.series[0].radius = '70%';
			}
			applyPieLayoutToOptions(dom, options);
			chart.setOption({
				series: [{
					center: options.series[0].center,
					radius: options.series[0].radius
				}]
			});
		});

		return chart;
	};

	/**
	 * Builds charts for the 'General Stats' section.
	 */
	const es_chart_stats_general = () => {

		const analytifyGetKeyByLabel = (statsObj, label) => {
			if (! statsObj) {return null;}
			for (const key in statsObj) {
				if (statsObj[key] && statsObj[key].label === label) {
					return key;
				}
			}
			return null;
		};


		if ($('#analytify_chart_new_vs_returning_visitors').length) {
			const setting_title = $('#analytify_chart_new_vs_returning_visitors').attr('data-chart-title');
			const setting_stats = JSON.parse(decodeURIComponent($('#analytify_chart_new_vs_returning_visitors').attr('data-stats')));
			const setting_colors = JSON.parse(decodeURIComponent($('#analytify_chart_new_vs_returning_visitors').attr('data-colors')));


			if (! setting_stats || ! setting_stats.new || ! setting_stats.returning) {
				// console.error('Setting stats or required properties are undefined.');
				return;
			}

			if (parseInt(setting_stats.new.number) > 0 || parseInt(setting_stats.returning.number) > 0) {
				const new_returning_graph_options = {
					color: setting_colors,
					legend: {
						orient: 'horizontal',
						bottom: '5%',
						textStyle: { fontSize: 14, fontWeight: '500' },
						formatter: function (name) {
							// Try to find the key by label
							const key = analytifyGetKeyByLabel(setting_stats, name);
							if (key && setting_stats[key] && ! isNaN(parseInt(setting_stats[key].number))) {
								let value = parseInt(setting_stats[key].number);
								if (value >= 1000) {
									value = (value / 1000).toFixed(1) + 'k';
								}
								return `${name}: ${value}`;
							}
							// If not found, log an error and return fallback
							// console.error('Could not find key for legend label:', name, setting_stats);
							return `${name}: 0`;
						},
						data: [setting_stats.new.label, setting_stats.returning.label]
					},
					series: [{
						name: setting_title,
						type: 'pie',
						center: ['50%', '43%'],
						radius: '70%',
						label: {
							show: false
						},
						labelLine: {
							show: false
						},
						data: [
							{ name: setting_stats.returning.label, value: parseInt(setting_stats.returning.number) },
							{ name: setting_stats.new.label, value: parseInt(setting_stats.new.number) }
						]
					}]
				};

				initPieChartWithLegendFit('analytify_chart_new_vs_returning_visitors', new_returning_graph_options);
			} else {
				$('#analytify_chart_new_vs_returning_visitors').html(`<div class="analytify_general_stats_value">0</div><p>${analytify_stats_core.no_stats_message}</p>`);
			}
		}

		if ($('#analytify_chart_visitor_devices').length) {
			const setting_title = $('#analytify_chart_visitor_devices').attr('data-chart-title');
			const setting_stats = JSON.parse(decodeURIComponent($('#analytify_chart_visitor_devices').attr('data-stats')));
			const setting_colors = JSON.parse(decodeURIComponent($('#analytify_chart_visitor_devices').attr('data-colors')));


			if (! setting_stats || ! setting_stats.mobile || ! setting_stats.tablet || ! setting_stats.desktop) {
				// console.error('Setting stats or required properties are undefined.');
				return;
			}

			if (setting_stats.desktop.number > 0 || setting_stats.mobile.number > 0 || setting_stats.tablet.number > 0) {
				const user_device_graph_options = {
					color: setting_colors,
					legend: {
						orient: 'horizontal',
						bottom: '5%',
						textStyle: { fontSize: 14, fontWeight: '500' },
						itemGap: 4,
						formatter: function (name) {
							const key = analytifyGetKeyByLabel(setting_stats, name);
							let value = key && setting_stats[key] ? setting_stats[key].number : 0;
							if (value >= 1000) {
								value = (value / 1000).toFixed(1) + 'k';
							}
							return `${name}: ${value}`;
						},
						data: [setting_stats.mobile.label, setting_stats.tablet.label, setting_stats.desktop.label]
					},
					series: [{
						name: setting_title,
						type: 'pie',
						center: ['50%', '43%'],
						radius: '70%',
						label: {
							show: false
						},
						labelLine: {
							show: false
						},
						data: [
							{ name: setting_stats.mobile.label, value: setting_stats.mobile.number },
							{ name: setting_stats.tablet.label, value: setting_stats.tablet.number },
							{ name: setting_stats.desktop.label, value: setting_stats.desktop.number }
						]
					}]
				};

				initPieChartWithLegendFit('analytify_chart_visitor_devices', user_device_graph_options);
			} else {
				$('#analytify_chart_visitor_devices').html(`<div class="analytify_general_stats_value">0</div><p>${analytify_stats_core.no_stats_message}</p>`);
			}
		}

		if ($('#analytify_chart_browser_breakdown').length) {
			const setting_title = $('#analytify_chart_browser_breakdown').attr('data-chart-title');
			const setting_stats = JSON.parse(decodeURIComponent($('#analytify_chart_browser_breakdown').attr('data-stats')));
			const setting_colors = JSON.parse(decodeURIComponent($('#analytify_chart_browser_breakdown').attr('data-colors')));

			if (! setting_stats || Object.keys(setting_stats).length === 0) {
				$('#analytify_chart_browser_breakdown').html(`<div class="analytify_general_stats_value">0</div><p>${analytify_stats_core.no_stats_message}</p>`);
				return;
			}

			// Check if there's any data
			let hasData = false;
			for (const key in setting_stats) {
				if (setting_stats[key].number > 0) {
					hasData = true;
					break;
				}
			}

			if (hasData) {
				// Build legend data and series data dynamically
				const legendData = [];
				const seriesData = [];

				for (const key in setting_stats) {
					if (setting_stats[key].label && setting_stats[key].number) {
						legendData.push(setting_stats[key].label);
						seriesData.push({
							name: setting_stats[key].label,
							value: parseInt(setting_stats[key].number)
						});
					}
				}

				const browser_breakdown_graph_options = {
					color: setting_colors,
					legend: {
						orient: 'horizontal',
						bottom: '5%',
						textStyle: { fontSize: 13, fontWeight: '500' },
						itemGap: 4,
						formatter: function (name) {
							const key = analytifyGetKeyByLabel(setting_stats, name);
							let value = key && setting_stats[key] ? setting_stats[key].number : 0;
							if (value >= 1000) {
								value = (value / 1000).toFixed(1) + 'k';
							}
							return `${name}: ${value}`;
						},
						data: legendData
					},
					series: [{
						name: setting_title,
						type: 'pie',
						center: ['50%', '43%'],
						radius: '70%',
						label: {
							show: false
						},
						labelLine: {
							show: false
						},
						data: seriesData
					}]
				};

				initPieChartWithLegendFit('analytify_chart_browser_breakdown', browser_breakdown_graph_options);
			} else {
				$('#analytify_chart_browser_breakdown').html(`<div class="analytify_general_stats_value">0</div><p>${analytify_stats_core.no_stats_message}</p>`);
			}
		}
	};

	/**
	* Builds map for the 'Geographic' section.
	*
	* @param {object} data Stats data for the map.
	*/
	function es_chart_map(data) {
		const map_data = [];
		for (const key in data.stats) {

			const single_country = {};

			single_country.name = data.stats[key].country;
			single_country.value = data.stats[key].sessions;

			if (single_country.name === 'United States') {
				single_country.name = 'United States of America';
			}

			map_data.push(single_country);
		}

		// Check if the CDN data loads successfully & Ensure the `world` map is available
		if (typeof echarts.getMap('world') === 'undefined') {
			fallbackToLocal();
		} else {
			loadMapAndRender();
		}

		function fallbackToLocal() {
			fetch(geoJsonData.geoJsonUrl)
				.then(response => response.json())
				.then(geoJson => {
					echarts.registerMap('world', geoJson);
					loadMapAndRender();
				})
				.catch(error => {
					// console.error('Error loading local GeoJSON:', error);
				});
		}

		function loadMapAndRender() {
			const geographic_stats_graph = echarts.init(document.getElementById('analytify_geographic_stats_graph'));
			const geographic_stats_graph_option = {
				tooltip: {
					trigger: 'item',
					formatter: function (params) {
						let value = params.value;
						// Check if value is undefined, null, or NaN and handle it
						if (value == null || isNaN(value)) {
							value = 0;
						} else {
							value = Math.floor(value);
						}
						return data.title + '<br />' + params.name + ' : ' + value;
					}
				},
				toolbox: { show: false, orient: 'horizontal', x: 'right', y: '10', feature: { restore: { show: true }, saveAsImage: { show: true } } },
				dataRange: { min: 1, max: parseInt(data.highest), text: [data.label.high, data.label.low], realtime: true, calculable: true, color: data.colors },
				series: [
					{ name: data.title, type: 'map', map: 'world', roam: 'move', scaleLimit: { min: 1, max: 10 }, mapLocation: { y: 60 }, itemStyle: { emphasis: { label: { show: true } } }, data: map_data }
				]
			};

			// Load data into the ECharts instance.
			geographic_stats_graph.setOption(geographic_stats_graph_option);
			window.onresize = function () {
				try {
					geographic_stats_graph.resize();
				} catch (err) {
					// console.log(err);
				}
			};
		}
	}

	/**
	 * Returns the compare start and end date based on given start and end date.
	 *
	 * @param {string} __start_date Start date.
	 * @param {string} __end_date   End date.
	 * @param {bool}   formatted    If true, returns formatted date to be used in the GA's dashboard url.
	 * @returns {object}
	 */
	const calc_compare_date = (__start_date, __end_date, formatted = true) => {

		const start_date = new Date(__start_date);
		const end_date = new Date(__end_date);

		return `%26_u.date00%3D${__start_date.replaceAll('-', '')}%26_u.date01%3D${__end_date.replaceAll('-', '')}`;
	};

	/**
	 * This function generate the complete GA report.
	 *
	 * @param {*} report_id
	 * @param {*} report_type
	 * @param {*} date_parameter
	 * @returns
	 *
	 */
	const generate_ga4_report_link = (report_id, report_type, date_parameter) => {
		if (! report_id) {
			return;
		}

		const report_link = `https://analytics.google.com/analytics/web/#/${report_id}/reports/explorer/?`;

		let link = '';

		switch (report_type) {
			case 'top_pages':
				link = `${report_link}params=_u..nav%3Dmaui${date_parameter}&r=all-pages-and-screens&ruid=all-pages-and-screens,life-cycle,engagement`;
				break;
			case 'top_countries':
				link = `${report_link}params=_u..nav%3Dmaui${date_parameter}%26_r.explorerCard..selmet%3D%5B%22activeUsers%22%5D%26_r.explorerCard..seldim%3D%5B%22country%22%5D&r=user-demographics-detail&ruid=user-demographics-detail,user,demographics&collectionId=user`;
				break;
			case 'top_cities':
				link = `${report_link}params=_r.explorerCard..selmet%3D%5B%22activeUsers%22%5D%26_r.explorerCard..seldim%3D%5B%22city%22%5D%26_u..nav%3Dmaui${date_parameter}&r=user-demographics-detail&ruid=user-demographics-detail,user,demographics&collectionId=user`;
				break;
			case 'referer':
				link = `${report_link}params=_u..nav%3Dmaui${date_parameter}&r=lifecycle-traffic-acquisition-v2&ruid=lifecycle-traffic-acquisition-v2,life-cycle,acquisition&collectionId=life-cycle`;
				break;
			case 'top_products':
				link = `${report_link}params=_u..nav%3Dmaui${date_parameter}%26_r.explorerCard..selmet%3D%5B%22ecommercePurchases%22%5D%26_r.explorerCard..seldim%3D%5B%22itemInfoName%22%5D&r=ecomm-product&collectionId=life-cycle`;
				break;
			case 'source_medium':
				link = `${report_link}params=_u..nav%3Dmaui${date_parameter}&r=lifecycle-traffic-acquisition-v2&ruid=lifecycle-traffic-acquisition-v2,3078873331,acquisition`;
				break;
			case 'top_countries_sales':
				link = `${report_link}params=_u..nav%3Dmaui${date_parameter}%26_r.explorerCard..selmet%3D%5B%22activeUsers%22%5D%26_r.explorerCard..seldim%3D%5B%22country%22%5D&r=user-demographics-detail&ruid=user-demographics-detail,user,demographics&collectionId=user`;
				break;
			default:
				break;
		}

		return link;
	};

	/**
	 * Builds the GA dashboard link for sections.
	 * Attaches the data parameter dynamically.
	 */
	const build_ga_dashboard_link = () => {
		const __start_date = $('#analytify_date_start').val();
		const __end_date = $('#analytify_date_end').val();

		const date_parameter = calc_compare_date(__start_date, __end_date, true);

		$('[data-ga-dashboard-link]').each(function (index, __element) {
			const link = generate_ga4_report_link(
				analytify_stats_core.ga4_report_url,
				$(__element).attr('data-ga-dashboard-link'),
				date_parameter
			);

			$(__element).attr('href', link);
		});
	};

	/**
	* Fetches the data and build the template for all endpoints.
	*
	*/
	const build_sections_free = () => {

		// To trigger same-height js script.
		$(window).trigger('resize');
		try {
			$('[data-endpoint]').each(function (index, __element) {
				const element = $(__element);
				const type = element.attr('data-endpoint');
				const target_body = element.find('.stats-wrapper');

				prepare_section(element);

				fetch_stats(type).then((response) => {
					if (response.success) {

						switch (type) {
							case 'general-stats':
								generate_general_stats_markup(response, element);
								es_chart_stats_general();
								break;

							case 'geographic-stats':
								{
									const table_classes = 'analytify_data_tables analytify_border_th_tp analytify_pull_left analytify_half';

									const map_stats_length = Object.keys(response.map.stats).length;
									const country_stats_length = Object.keys(response.country.stats).length;
									const city_stats_length = Object.keys(response.city.stats).length;

									const markup = `
									${map_stats_length > 0 ? `<div class="analytify_txt_center analytify_graph_wraper"><div id="analytify_geographic_stats_graph" style="height:600px"></div></div>` : ``
}
									<div class="analytify_clearfix">
										${country_stats_length > 0 ? generate_stats_table(response.country.headers, response.country.stats, table_classes) : stats_message(false, false, response.country.headers, table_classes)
}
										${city_stats_length > 0 ? generate_stats_table(response.city.headers, response.city.stats, table_classes) : stats_message(false, false, response.city.headers, table_classes)
}
									</div>`;

									set_section(element, markup, response.footer, false);

									if (map_stats_length > 0) {
										es_chart_map(response.map);
									}
								}
								break;

							case 'system-stats':
								{
									const browser_stats_length = Object.keys(response.browser.stats).length;
									const os_stats_length = Object.keys(response.os.stats).length;
									const mobile_stats_length = Object.keys(response.mobile.stats).length;

									const table_classes = 'analytify_data_tables';

									const markup = `<div class="analytify_clearfix">
										<div class="analytify_one_tree_table">
											${browser_stats_length > 0 ? generate_stats_table(response.browser.headers, response.browser.stats, table_classes) : stats_message(false, false, response.browser.headers, table_classes)
}
										</div>
										<div class="analytify_one_tree_table">
											${os_stats_length > 0 ? generate_stats_table(response.os.headers, response.os.stats, table_classes) : stats_message(false, false, response.os.headers, table_classes)
}
										</div>
										<div class="analytify_one_tree_table">
											${mobile_stats_length > 0 ? generate_stats_table(response.mobile.headers, response.mobile.stats, table_classes) : stats_message(false, false, response.mobile.headers, table_classes)
}
										</div>
									</div>`;

									set_section(element, markup, response.footer, false);

								}
								break;

							case 'top-pages-stats':
							case 'keyword-stats':
							case 'social-stats':
							case 'referer-stats':
							case 'what-is-happening-stats':
								{
									let table_classes;
									let table_attr;
									switch (type) {
										case 'top-pages-stats':
											table_classes = 'analytify_data_tables';
											if (response.pagination) {
												table_classes += ' wp_analytify_paginated';
												table_attr = ' data-product-per-page="10"';
											}
											break;
										case 'keyword-stats':
											table_classes = 'analytify_data_tables analytify_page_stats_table';
											break;
										case 'social-stats':
											table_classes = 'analytify_data_tables analytify_no_header_table';
											break;
										case 'what-is-happening-stats':
											table_classes = 'analytify_data_tables analytify_page_stats_table';
											break;
										case 'referer-stats':
											table_classes = 'analytify_bar_tables';
											if (response.pagination) {
												table_classes += ' wp_analytify_paginated';
												table_attr = ' data-product-per-page="10"';
											}
											break;

										default:
											table_classes = 'analytify_bar_tables';
											break;
									}

									if ( response.stats && Object.keys( response.stats ).length > 0 ) {
										const markup = generate_stats_table(response.headers, response.stats, table_classes, table_attr);
										set_section(element, markup, response.footer, response.pagination);
										if (element.find('.title-total-wrapper').length && response.title_stats) {
											element.find('.title-total-wrapper').html(response.title_stats);
										}
									} else {
										stats_message(element, false, response.headers, table_classes);
									}
								}
								break;

							default:
								break;
						}

						build_ga_dashboard_link();
						wp_analytify_paginated();
						$(window).trigger('resize');

					} else if (response.error_message) {
						should_not_be_loading(element);
						stats_message(element, response.error_message, response.headers);
					} else if (response.error_box) {
						should_not_be_loading(element);
						red_stats_message(element, response.error_box, response.headers);
					} else {
						should_not_be_loading(element);
						stats_message(element, analytify_stats_core.error_message, response.headers);
					}

				}).catch(function (error) {
					// console.log(error);
				});

			});
		} catch (err) {
			// console.log('err');
		}

		$(window).trigger('resize');
	};

	build_sections_free();

	// Call of submission of date form.
	$('form.analytify_form_date').on('submit', function (e) {
		if (analytify_stats_core.load_via_ajax) {
			e.preventDefault();
			const customEvent = new Event('analytify_form_date_submitted');
			document.dispatchEvent(customEvent);
			build_sections_free();
			build_ga_dashboard_link();
		}
	});
});
