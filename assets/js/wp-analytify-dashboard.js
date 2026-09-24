// JS file for dashboard operations.
jQuery(document).ready(function ($) {
	function removeHtmlTags(inputString) {
		if (typeof inputString !== 'string') {
			// If inputString is not a string, return it as is
			return inputString;
		}

		return inputString.replace(/<\/?[^>]+(>|$)/g, '');
	}

	jQuery('.analytify_data_tables th').each(function () {
		var index = jQuery(this).index();
		var value = removeHtmlTags(jQuery(this).text());
		jQuery(this)
			.closest('.analytify_data_tables')
			.find('tbody tr')
			.each(function () {
				// Find the corresponding td in each row and set the data attribute
				jQuery(this).find('td').eq(index).attr('data-heading', value);
			});
	});

	// moment-js timezone filter call this
	// Filter was remove after 4.2.1

	// Dashboards DropDown
	$('#dashboard-options').on('change', function (event) {
		var val = $(this).val();
		var action = $('#dashboard-options-form').attr('action');
		$('#dashboard-options-form')
			.attr('action', action + '&show=' + val)
			.submit();
	});

	$(document).on('click', '.analytify_status_tab_header li', function () {
		var $this = $(this),
			tab_id = $this.attr('data-tab');

		$('body .analytify_status_tab_header li').removeClass(
			'analytify_active_stats'
		);
		$('body .analytify_panels_data').removeClass('analytify_active_panel');

		$this.addClass('analytify_active_stats');
		$('#' + tab_id).addClass('analytify_active_panel');
	});

	$(document).on('click', function (e) {
		var container = $('.analytify_select_date_list');
		btn_triger = $('.analytify_arrow_date_picker');
		if (
			! container.is(e.target) &&
			! btn_triger.is(e.target) && // if the target of the click isn't the container...
			container.has(e.target).length === 0 &&
			btn_triger.has(e.target).length === 0
		) {
			// ... nor a descendant of the container
			container.removeClass('analytify_active_date_list');
			btn_triger.removeClass('analytify_open');
		}
	});

	$(document).on('click', '.analytify_stats_setting_bar button', function () {
		var $this = $(this),
			button_id = $this.attr('data-graphType');

		$this
			.parent()
			.removeClass('analytify_disabled')
			.siblings()
			.addClass('analytify_disabled');

		$('.' + button_id)
			.addClass('analytify_active_graph')
			.siblings()
			.removeClass('analytify_active_graph');

		if (button_id == 'analytify_months_graph_by_visitors') {
			$('.total_month_users').show();
			$('.total_year_users').hide();
		}

		if (button_id == 'analytify_years_graph_by_visitors') {
			$('.total_month_users').hide();
			$('.total_year_users').show();
		}

		if (button_id == 'analytify_months_graph_by_view') {
			$('.total_month_views').show();
			$('.total_year_views').hide();
		}

		if (button_id == 'analytify_years_graph_by_view') {
			$('.total_month_views').hide();
			$('.total_year_views').show();
		}
	});

	equalheight = function (container) {
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
			}
		});
	};

	$(window).on('load', function () {
		setTimeout(function () {
			equalheight('.analytify_general_status_boxes');
		}, 5000);
	});

	wp_analytify_paginated = function () {
		$('table.wp_analytify_paginated:not(.is-paginated)').each(function () {
			$(this).addClass('is-paginated');

			var numPerPage = 10; // Default rows per page
			var $table = $(this);
			var $pager = $('<div class="wp_analytify_pager"></div>');

			// Count total rows
			var totalRows = $table.find('tbody tr').length;
			if (totalRows <= 10) {
				// Don't show pagination controls if less than 10 rows
				// Also hide/remove the pagination container if it exists
				$(this)
					.closest('.analytify_status_body')
					.next('.analytify_status_footer')
					.find('.wp_analytify_pagination')
					.hide();
				return;
			}
			var rowsOptions = [10, 25, 50, 100];

			// Determine which buttons to show
			var shownOptions = rowsOptions.filter(function (opt) {
				return opt < totalRows;
			});

			// Always show the next highest option (if any)
			var nextHighest = rowsOptions.find(function (opt) {
				return opt >= totalRows;
			});
			if (nextHighest && ! shownOptions.includes(nextHighest)) {
				shownOptions.push(nextHighest);
			}
			if (shownOptions.length === 0) { shownOptions = [rowsOptions[0]]; }

			// Add buttons for shownOptions
			var $buttons = $();
			shownOptions.forEach(function (num) {
				$buttons = $buttons.add(
					$('<span class="rows-per-page page-number" data-rows="' + num + '">' + num + '</span>')
				);
			});

			// Append buttons to the pager
			$pager.append($buttons);

			// Append the pager to the pagination container
			const $pagination = $( this )
				.closest( '.analytify_status_body' )
				.next( '.analytify_status_footer' )
				.find( '.wp_analytify_pagination' );

			if ( ! $pagination.length ) {
				return;
			}

			$pagination.html( $pager );

			// Function to update the table based on rows per page
			function updateTable() {
				$table.find('tbody tr').hide();

				var $filteredRows = $table.find('tbody tr');
				$filteredRows.slice(0, numPerPage).show();

				var numRows = $filteredRows.length;

				// Ensure the pager is always visible
				$pager.show();
			}

			// Handle button clicks to change rows per page
			$pager.on('click', '.rows-per-page', function () {
				numPerPage = parseInt($(this).data('rows'), 10);

				// Highlight the active button
				$pager.find('.rows-per-page').removeClass('wp_analytify_active');
				$(this).addClass('wp_analytify_active');

				// Reset and update the table
				updateTable();
			});

			// Initialize with the default rows per page
			$pager.find('.rows-per-page[data-rows="10"]').addClass('clickable wp_analytify_active');
			updateTable();
		});
	};

	$(window).resize(function () {
		equalheight('.analytify_general_status_boxes');
	});

	$('.analytify_arrow_date_picker').on('click', function () {
		$(this).toggleClass('analytify_open');
		$('.analytify_select_date_list').toggleClass('analytify_active_date_list');
	});

	$('.analytify_selected_dashboard_field').on('click', function () {
		$(this).parent().toggleClass('analytify_open_list');
	});

	$('.analytify_dashboards_list li').on('click', function () {
		var $this = $(this);
		dashboards_name = $this.text();

		$('.analytify_selected_dashboard_field').text(dashboards_name);
		$('.analytify_select_dashboard').removeClass('analytify_open_list');
	});

	// stop users from changing the start date and end value manually by typing
	$('#analytify_start, #analytify_end').on('keydown', function (event) {
		event.preventDefault();
		return false;
	});

	var __yesterday = moment().subtract(1, 'day'),
		__last_7_days = moment().subtract(7, 'day'),
		__last_14_days = moment().subtract(14, 'day');
	(__last_30_day = moment().subtract(1, 'months')),
	(__last_month_start_date = moment().subtract(1, 'months').startOf('month')),
	(__last_month_end_date = moment().subtract(1, 'months').endOf('month')),
	(__this_month_start_date = moment().startOf('month')),
	(__last_3_months_start_date = moment()
		.subtract(3, 'months')
		.startOf('month')),
	(__last_6_months_start_date = moment()
		.subtract(6, 'months')
		.startOf('month')),
	(__last_year_start_date = moment().subtract(12, 'months').startOf('month'));

	var today_date = moment().format('YYYY-MM-DD'),
		today_date_formated = moment().format('MMM DD, YYYY'),
		yesterday_date = __yesterday.format('YYYY-MM-DD'),
		yesterday_date_formated = __yesterday.format('MMM DD, YYYY'),
		last_7_days = __last_7_days.format('YYYY-MM-DD'),
		last_7_days_formated = __last_7_days.format('MMM DD, YYYY'),
		last_14_days = __last_14_days.format('YYYY-MM-DD'),
		last_14_days_formated = __last_14_days.format('MMM DD, YYYY'),
		last_30_day = __last_30_day.format('YYYY-MM-DD'),
		last_30_day_formated = __last_30_day.format('MMM DD, YYYY'),
		last_month_start_date = __last_month_start_date.format('YYYY-MM-DD'),
		last_month_start_date_formated =
			__last_month_start_date.format('MMM DD, YYYY'),
		last_month_end_date = __last_month_end_date.format('YYYY-MM-DD'),
		last_month_end_date_formated = __last_month_end_date.format('MMM DD, YYYY'),
		this_month_start_date = __this_month_start_date.format('YYYY-MM-DD'),
		this_month_start_date_formated =
			__this_month_start_date.format('MMM DD, YYYY'),
		last_3_months_start_date = __last_3_months_start_date.format('YYYY-MM-DD'),
		last_3_months_start_date_formated =
			__last_3_months_start_date.format('MMM DD, YYYY'),
		last_6_months_start_date = __last_6_months_start_date.format('YYYY-MM-DD'),
		last_6_months_start_date_formated =
			__last_6_months_start_date.format('MMM DD, YYYY'),
		last_year_start_date = __last_year_start_date.format('YYYY-MM-DD'),
		last_year_start_date_formated =
			__last_year_start_date.format('MMM DD, YYYY');

	/*
   * onload check
   * sets the default value of the start date
   * just for appearances, should not servers any other function
   */
	if ($('#analytify_start').val() === '') {
		const start_date = $('#analytify_date_start').val();
		const start_date_formated = moment(start_date)
			.locale('en')
			.format('MMM DD, YYYY');
		$('#analytify_start').val(start_date_formated);
	}

	/*
   * onload check
   * sets the default value of the end date
   * just for appearance, should not servers any other function
   */
	if ($('#analytify_end').val() === '') {
		const end_date = $('#analytify_date_end').val();
		const end_date_formated = moment(end_date)
			.locale('en')
			.format('MMM DD, YYYY');
		$('#analytify_end').val(end_date_formated);
	}

	// set the today's date text and end data attribute
	$('.analytify_today_date')
		.text(today_date_formated)
		.parent('span')
		.attr('data-end', today_date);

	// set today's date text and start date attribute
	$('.analytify_current_day')
		.text(today_date_formated)
		.parent('span')
		.attr('data-start', today_date);

	$('.analytify_yesterday')
		.text(yesterday_date_formated)
		.parent('span')
		.attr('data-start', yesterday_date);
	$('.analytify_yesterday_date')
		.text(yesterday_date_formated)
		.parent('span')
		.attr('data-end', yesterday_date);

	// set week date text and start date attribute
	$('.analytify_last_7_days')
		.text(last_7_days_formated)
		.parent('span')
		.attr('data-start', last_7_days);

	// set 14 days text and start date attribute
	$('.analytify_last_14_days')
		.text(last_14_days_formated)
		.parent('span')
		.attr('data-start', last_14_days);

	// set 1 month text and start date attribute
	$('.analytify_last_30_day')
		.text(last_30_day_formated)
		.parent('span')
		.attr('data-start', last_30_day);

	// set this month for text and start date attribute
	$('.analytify_this_month_start_date')
		.text(this_month_start_date_formated)
		.parent('span')
		.attr('data-start', this_month_start_date);

	// set last month for text and start date attribute
	$('.analytify_last_month_start_date')
		.text(last_month_start_date_formated)
		.parent('span')
		.attr('data-start', last_month_start_date);

	// set last month for text and end date attribute
	$('.analytify_last_month_end_date')
		.text(last_month_end_date_formated)
		.parent('span')
		.attr('data-end', last_month_end_date);

	// set last 3 month for text and start date attribute
	$('.analytify_last_3_months_start_date')
		.text(last_3_months_start_date_formated)
		.parent('span')
		.attr('data-start', last_3_months_start_date);

	// set last 3 month for text and start date attribute
	$('.analytify_last_6_months_start_date')
		.text(last_6_months_start_date_formated)
		.parent('span')
		.attr('data-start', last_6_months_start_date);

	// set last year for text and start date attribute
	$('.analytify_last_year_start_date')
		.text(last_year_start_date_formated)
		.parent('span')
		.attr('data-start', last_year_start_date);

	if ($('#analytify_start').length > 0 || $('#analytify_end').length > 0) {
		var startDate,
			endDate,
			updateStartDate = function () {
				startPicker.setStartRange(startDate);
				endPicker.setStartRange(startDate);
				endPicker.setMinDate(startDate);
			},
			updateEndDate = function () {
				startPicker.setEndRange(endDate);
				startPicker.setMaxDate(endDate);
				endPicker.setEndRange(endDate);
			},
			startPicker = new Pikaday({
				field: document.getElementById('analytify_start'),
				format: 'MMM DD, YYYY',
				maxDate: new Date(today_date_formated),
				i18n: analytify_dashboard.i18n,
				onSelect: function () {
					jQuery('#analytify_date_start').val(
						this.getMoment().format('YYYY-MM-DD')
					);
					jQuery('#analytify_date_diff').val('');
					startDate = this.getDate();
					updateStartDate();
				}
			}),
			endPicker = new Pikaday({
				field: document.getElementById('analytify_end'),
				position: 'bottom right',
				maxDate: new Date(today_date_formated),
				format: 'MMM DD, YYYY',
				i18n: analytify_dashboard.i18n,
				onSelect: function () {
					jQuery('#analytify_date_end').val(
						this.getMoment().format('YYYY-MM-DD')
					);
					jQuery('#analytify_date_diff').val('');
					endDate = this.getDate();
					updateEndDate();
				}
			}),
			_startDate = startPicker.getDate(),
			_endDate = endPicker.getDate();

		if (_startDate) {
			startDate = _startDate;
			updateStartDate();
		}

		if (_endDate) {
			endDate = _endDate;
			updateEndDate();
		}
	}

	/*
   * triggers when a date is selected from the dropdown
   * sets the view values and data values for [start date] and [end date]
   * sets the Picker start and end date
   */
	$('.analytify_select_date_list li').on('click', function () {
		var $this = $(this).find('span');

		// if 'custom_range' is clicked, trigger the start input and end becasue we don't need to override any input data
		if ($this.hasClass('custom_range')) {
			$('#analytify_date_diff').val('');
			$('#analytify_start').trigger('click');
			return;
		}

		var start_date = jQuery(this).children('span:first').attr('data-start');
		var start_date_formated = moment(start_date)
			.locale('en')
			.format('MMM DD, YYYY');

		setTimeout(() => {
			startPicker.setDate(start_date_formated);
		}, 1000);
		jQuery('#analytify_date_start').val(start_date);
		jQuery('#analytify_start').val(start_date_formated);

		var end_date = jQuery(this).children('span:first').attr('data-end');
		var end_date_formated = moment(end_date)
			.locale('en')
			.format('MMM DD, YYYY');

		// reset the max date for start date
		startPicker.setMaxDate(end_date_formated);

		setTimeout(() => {
			endPicker.setDate(end_date_formated);
		}, 1000);
		jQuery('#analytify_date_end').val(end_date);
		jQuery('#analytify_end').val(end_date_formated);

		$('#analytify_date_diff').val($this.attr('data-date-diff'));
		$('#analytify_widget_date_differ').val($this.attr('data-date-diff'));

		$('.analytify_select_date_list').removeClass('analytify_active_date_list');
		$('.analytify_arrow_date_picker').removeClass('analytify_open');
	});

	/**
   * Send ajax on cross of add.
   *
   */
	$('.analytify_general_status-icon').on('click', function () {
		$(this).parent().remove();
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'analytify_remove_comparison_gif'
			}
		});
	});

	// Add active class on dashboard parent navigation item.
	$('.analytify_nav_tab_parent .nav-tab-active')
		.parents('.analytify_nav_tab_parent')
		.addClass('nav-tab-active');

	// Dashboard export dropdown.
	$( document ).on( 'click', '.analytify_export_btn', function ( e ) {
		e.preventDefault();
		e.stopPropagation();

		const $dropdown = $( this ).closest( '.analytify_export_dropdown' );
		const isOpen = $dropdown.hasClass( 'is-open' );

		$( '.analytify_export_dropdown' ).removeClass( 'is-open' )
			.find( '.analytify_export_btn' ).attr( 'aria-expanded', 'false' );

		if ( ! isOpen ) {
			$dropdown.addClass( 'is-open' );
			$( this ).attr( 'aria-expanded', 'true' );
		}
	} );

	$( document ).on( 'click', function ( e ) {
		if ( ! $( e.target ).closest( '.analytify_export_dropdown' ).length ) {
			$( '.analytify_export_dropdown' ).removeClass( 'is-open' )
				.find( '.analytify_export_btn' ).attr( 'aria-expanded', 'false' );
		}
	} );

	$( document ).on( 'click', '.analytify_export_menu_item', function ( e ) {
		e.preventDefault();

		const exportType = $( this ).data( 'export-type' );

		$( '.analytify_export_dropdown' ).removeClass( 'is-open' )
			.find( '.analytify_export_btn' ).attr( 'aria-expanded', 'false' );

		if ( exportType === 'pdf' ) {
			exportDashboardToPdf();
		} else if ( exportType === 'csv' ) {
			exportDashboardToCsv();
		} else if ( exportType === 'excel' ) {
			exportDashboardToExcel();
		}
	} );

	function getExportFilename( extension ) {
		const start = $( '#analytify_date_start' ).val();
		const end = $( '#analytify_date_end' ).val();

		return `analytify-dashboard-${ start }-to-${ end }.${ extension }`;
	}

	function getDashboardMeta() {
		const title = $( '.analytify_main_title' ).first().clone()
			.find( '.analytify_export_dropdown' ).remove().end()
			.text().replace( /\s+/g, ' ' ).trim();

		const statsOfElement = $( '.analytify_stats_of' );
		const streamUrl = statsOfElement.find( 'a' ).text().trim();
		const streamText = statsOfElement.text().trim();
		let webStream = streamText;
		const streamNameMatch = streamText.match( /\(([^)]+)\)/ );

		if ( streamNameMatch && streamNameMatch[1] ) {
			webStream = `${ streamUrl } (${ streamNameMatch[1] })`;
		} else if ( streamUrl ) {
			webStream = streamUrl;
		}

		const startDate = $( '#analytify_date_start' ).val();
		const endDate = $( '#analytify_date_end' ).val();
		const dateRange = `${ formatDate( startDate ) } - ${ formatDate( endDate ) }`;

		return {
			title,
			stream: webStream,
			dateRange
		};
	}

	function getCellExportValue( $cell ) {
		const $clone = $cell.clone()
			.find( '.analytify-export-data, img, .analytify_tooltiptext, .analytify_bar_graph' ).remove().end();

		// Skip placeholder / JS-only anchors so Excel does not get Target: "#".
		const $link = $clone.find( 'a[href]' ).filter( function () {
			const raw = ( $( this ).attr( 'href' ) || '' ).trim();

			if ( ! raw || raw === '#' ) {
				return false;
			}

			// Case-/whitespace-insensitive scheme check (CWE-style javascript: bypasses).
			const scheme = raw.replace( /[\s\t\r\n\f\v]+/g, '' ).toLowerCase();

			if ( scheme.indexOf( 'javascript:' ) === 0
				|| scheme.indexOf( 'vbscript:' ) === 0
				|| scheme.indexOf( 'data:' ) === 0 ) {
				return false;
			}

			return true;
		} ).first();

		if ( $link.length ) {
			// prop('href') resolves relative paths to absolute URLs (SheetJS needs that).
			const href = ( $link.prop( 'href' ) || $link.attr( 'href' ) || '' ).trim();
			const $title = $clone.find( '.analytify_page_name' ).first();
			let text = $title.length ? $title.text().trim() : $link.text().trim();

			if ( ! text ) {
				text = $clone.text().replace( /\s+/g, ' ' ).trim();
			}

			if ( href && text ) {
				return { text, link: href };
			}
		}

		return {
			text: $clone.text().replace( /\s+/g, ' ' ).trim(),
			link: ''
		};
	}

	function cellHasValue( cell ) {
		if ( cell == null ) {
			return false;
		}

		if ( typeof cell === 'object' ) {
			return !! ( cell.text || cell.link );
		}

		return !! String( cell ).trim();
	}

	function normalizeExportCell( cell ) {
		if ( cell == null ) {
			return '';
		}

		if ( typeof cell === 'object' ) {
			return cell;
		}

		return { text: String( cell ), link: '' };
	}

	function getCellPlainText( cell ) {
		const normalized = normalizeExportCell( cell );

		return normalized.text || '';
	}

	function formatCsvCellValue( cell ) {
		const normalized = normalizeExportCell( cell );

		if ( normalized.link && normalized.text ) {
			const link = String( normalized.link ).trim();

			// Only emit executable HYPERLINK for http(s) targets.
			if ( ! /^https?:\/\//i.test( link ) ) {
				return normalized.text || '';
			}

			const url = link.replace( /"/g, '""' );
			const text = String( normalized.text ).replace( /"/g, '""' );

			return `=HYPERLINK("${ url }","${ text }")`;
		}

		return normalized.text || '';
	}

	function formatChartExportValue( num ) {
		const value = parseInt( num, 10 );

		if ( Number.isNaN( value ) ) {
			return String( num == null ? '' : num );
		}

		if ( value >= 1000 ) {
			return ( value / 1000 ).toFixed( 1 ) + 'k';
		}

		return String( value );
	}

	function extractChartTableFromElement( $chart, chartTitle ) {
		const rows = [];
		let stats = null;

		try {
			stats = JSON.parse( decodeURIComponent( $chart.attr( 'data-stats' ) || '' ) );
		} catch ( error ) {
			return rows;
		}

		if ( ! stats || typeof stats !== 'object' ) {
			return rows;
		}

		rows.push( [ chartTitle ] );
		rows.push( [ 'Label', 'Value' ] );

		Object.keys( stats ).forEach( ( key ) => {
			const item = stats[key];

			if ( ! item || item.label == null ) {
				return;
			}

			const raw = item.number != null ? item.number : item.sessions;

			if ( raw == null || raw === '' ) {
				return;
			}

			rows.push( [ item.label, formatChartExportValue( raw ) ] );
		} );

		return rows.length > 2 ? rows : [];
	}

	function extractTableFromElement( $table ) {
		const rows = [];

		$table.find( 'thead tr' ).each( function () {
			const row = [];

			$( this ).find( 'th, td' ).each( function () {
				row.push( getCellExportValue( $( this ) ) );
			} );

			if ( row.some( ( cell ) => cellHasValue( cell ) ) ) {
				rows.push( row );
			}
		} );

		$table.find( 'tbody tr' ).each( function () {
			const row = [];

			$( this ).find( 'td, th' ).each( function () {
				row.push( getCellExportValue( $( this ) ) );
			} );

			if ( row.some( ( cell ) => cellHasValue( cell ) ) ) {
				rows.push( row );
			}
		} );

		return rows;
	}

	function collectDashboardSections() {
		const sections = [];
		const $wrapper = $( '.analytify-dashboard-content .analytify_wraper' );

		$wrapper.find( '.analytify_status_box_wraper' ).each( function () {
			const $section = $( this );

			if ( $section.find( '.analytify_stats_loading:visible' ).length ) {
				return;
			}

			const sectionTitle = $section.find( '.analytify_status_header h3' ).first().clone()
				.find( '.analytify-export-data, img, .analytify_tooltiptext, span.analytify_tooltiptext' ).remove().end()
				.text().replace( /\s+/g, ' ' ).trim();

			if ( ! sectionTitle ) {
				return;
			}

			const section = {
				title: sectionTitle,
				stats: [],
				tables: []
			};

			$section.find( '.analytify_general_status_boxes' ).each( function () {
				const $box = $( this );
				const label = $box.find( 'h4' ).first().clone()
					.find( '.analytify_chart_hint' ).remove().end()
					.text().replace( /\s+/g, ' ' ).trim();
				const $chart = $box.find( '[id^="analytify_chart_"][data-stats]' );

				if ( $chart.length ) {
					const chartTable = extractChartTableFromElement( $chart.first(), label );

					if ( chartTable.length ) {
						section.tables.push( chartTable );
						return;
					}
					// Empty/undecodable data-stats: fall through to label/value text.
				}

				const value = $box.find( '.analytify_general_stats_value, .large-count, .count-visits' ).first()
					.text().replace( /\s+/g, ' ' ).trim();

				if ( label && value ) {
					section.stats.push( [ label, value ] );
				}
			} );

			$section.find( '.analytify_status_body .stats-wrapper table.analytify_data_tables, .analytify_status_body .stats-wrapper table.analytify_bar_tables' ).each( function () {
				const $table = $( this );
				const $parentTab = $table.closest( '.analytify_visitors, .analytify_views' );

				if ( $parentTab.length && ! $parentTab.hasClass( 'analytify_active_stats' ) ) {
					return;
				}

				const tableData = extractTableFromElement( $table );

				if ( tableData.length ) {
					section.tables.push( tableData );
				}
			} );

			if ( section.stats.length || section.tables.length ) {
				sections.push( section );
			}
		} );

		return sections;
	}

	function escapeCsvField( value, trustedHyperlink ) {
		let str = String( value == null ? '' : value );

		// Only trust HYPERLINK formulas we built ourselves — never raw cell text
		// that merely starts with "=HYPERLINK(" (CWE-1236 bypass).
		if ( trustedHyperlink && /^=HYPERLINK\(/i.test( str ) ) {
			if ( /[",\n\r]/.test( str ) ) {
				return `"${ str.replace( /"/g, '""' ) }"`;
			}

			return str;
		}

		// Neutralize formula injection (CWE-1236): a leading =, +, -, @, tab, or CR
		// makes Excel/Sheets evaluate the cell as a formula when opened from CSV.
		if ( /^[=+\-@\t\r]/.test( str ) ) {
			str = "'" + str;
		}

		if ( /[",\n\r]/.test( str ) ) {
			return `"${ str.replace( /"/g, '""' ) }"`;
		}

		return str;
	}

	function serializeExportRow( row ) {
		return row.map( ( cell ) => {
			if ( typeof cell === 'object' && cell.link && cell.text ) {
				const formatted = formatCsvCellValue( cell );

				return {
					value: formatted,
					trustedHyperlink: /^=HYPERLINK\(/i.test( formatted )
				};
			}

			if ( typeof cell === 'object' && ( cell.text || cell.link ) ) {
				return { value: formatCsvCellValue( cell ), trustedHyperlink: false };
			}

			return { value: cell == null ? '' : cell, trustedHyperlink: false };
		} );
	}

	function buildExportRows( meta, sections ) {
		const rows = [
			[ meta.title ],
			[ meta.stream ],
			[ meta.dateRange ],
			[]
		];

		sections.forEach( ( section ) => {
			rows.push( [ section.title ] );

			if ( section.stats.length ) {
				rows.push( [ 'Metric', 'Value' ] );
				section.stats.forEach( ( statRow ) => {
					rows.push( statRow );
				} );
				rows.push( [] );
			}

			section.tables.forEach( ( table ) => {
				table.forEach( ( tableRow ) => {
					rows.push( tableRow );
				} );
				rows.push( [] );
			} );

			rows.push( [] );
		} );

		return rows;
	}

	function downloadBlob( content, filename, mimeType ) {
		const blob = new Blob( [ content ], { type: mimeType } );
		const blobURL = URL.createObjectURL( blob );
		const link = document.createElement( 'a' );

		link.href = blobURL;
		link.download = filename;
		document.body.appendChild( link );
		link.click();
		document.body.removeChild( link );
		URL.revokeObjectURL( blobURL );
	}

	function exportDashboardToCsv() {
		const meta = getDashboardMeta();
		const sections = collectDashboardSections();
		const rows = buildExportRows( meta, sections );
		const csvContent = '\uFEFF' + rows.map( ( row ) => serializeExportRow( row ).map( ( field ) => escapeCsvField( field.value, field.trustedHyperlink ) ).join( ',' ) ).join( '\n' );

		downloadBlob( csvContent, getExportFilename( 'csv' ), 'text/csv;charset=utf-8;' );
	}

	function writeWorksheetRows( worksheet, rows ) {
		let maxCol = 0;

		rows.forEach( ( row, rowIndex ) => {
			row.forEach( ( cell, colIndex ) => {
				const normalized = normalizeExportCell( cell );
				const cellRef = XLSX.utils.encode_cell( { r: rowIndex, c: colIndex } );

				if ( normalized.link && normalized.text && /^https?:\/\//i.test( String( normalized.link ).trim() ) ) {
					worksheet[cellRef] = {
						t: 's',
						v: normalized.text,
						l: { Target: normalized.link, Tooltip: normalized.text }
					};
				} else {
					worksheet[cellRef] = {
						t: 's',
						v: getCellPlainText( cell )
					};
				}

				if ( colIndex > maxCol ) {
					maxCol = colIndex;
				}
			} );
		} );

		worksheet['!ref'] = XLSX.utils.encode_range( {
			s: { r: 0, c: 0 },
			e: { r: Math.max( 0, rows.length - 1 ), c: maxCol }
		} );

		return worksheet;
	}

	function exportDashboardToExcel() {
		if ( typeof XLSX === 'undefined' ) {
			return;
		}

		const meta = getDashboardMeta();
		const sections = collectDashboardSections();
		const rows = buildExportRows( meta, sections );
		const workbook = XLSX.utils.book_new();
		const worksheet = writeWorksheetRows( {}, rows );

		XLSX.utils.book_append_sheet( workbook, worksheet, 'Dashboard' );
		XLSX.writeFile( workbook, getExportFilename( 'xlsx' ) );
	}

	function exportDashboardToPdf() {
		const exportButton = $( '.analytify_export_btn' );
		const originalButtonText = exportButton.text();

		exportButton.text( 'Generating PDF...' ).prop( 'disabled', true );

		const visitorsTabActive = $( '.analytify_visitors' ).hasClass( 'analytify_active_stats' );
		const viewsTabActive = $( '.analytify_views' ).hasClass( 'analytify_active_stats' );
		const content = $( '.analytify-dashboard-content' );
		const HTML_Width = content.width();
		const HTML_Height = content.height();
		const pxToPtRatio = 72 / 96;
		const PDF_Width = HTML_Width * pxToPtRatio;
		const PDF_Height = HTML_Height * pxToPtRatio;

		const canvasOptions = {
			allowTaint: true,
			letterRendering: true,
			useCORS: true,
			ignoreElements: function ( element ) {
				if ( visitorsTabActive ) {
					return shouldIgnoreElement( element, 'views' );
				} else if ( viewsTabActive ) {
					return shouldIgnoreElement( element, 'visitors' );
				}

				return shouldIgnoreElement( element );
			}
		};

		html2canvas( content[0], canvasOptions ).then( function ( canvas ) {
			const imgData = canvas.toDataURL( 'image/png', 1.0 );
			const JsPDF = typeof jsPDF !== 'undefined' ? jsPDF : ( window.jspdf && window.jspdf.jsPDF );

			if ( ! JsPDF ) {
				exportButton.text( originalButtonText ).prop( 'disabled', false );
				return;
			}

			const pdf = new JsPDF( 'p', 'pt', [ PDF_Width, PDF_Height ] );

			pdf.addImage( imgData, 'PNG', 0, 0, PDF_Width, PDF_Height );

			const meta = getDashboardMeta();

			addPDFHeader( pdf, meta.title, meta.stream, meta.dateRange );

			const blob = pdf.output( 'blob' );
			const blobURL = URL.createObjectURL( blob );

			window.open( blobURL, '_blank' );
			exportButton.text( originalButtonText ).prop( 'disabled', false );
		} ).catch( function () {
			exportButton.text( originalButtonText ).prop( 'disabled', false );
		} );
	}

	// Helper function to determine which elements to ignore
	function shouldIgnoreElement( element, tabType ) {
		const ignoredClasses = [
			'wp_analytify_pagination',
			'analytify_tooltip',
			'analytify_main_setting_bar',
			'analytify_export_dropdown',
			'analytify_export_btn',
			'analytify_export_menu',
			'analytify_disabled',
			'analytify_dashboard_title'
		];

		if (tabType === 'views' && element.classList.contains('analytify_views') && ! element.classList.contains('analytify_active_stats')) {
			return true;
		}

		if (tabType === 'visitors' && element.classList.contains('analytify_visitors') && ! element.classList.contains('analytify_active_stats')) {
			return true;
		}

		return ignoredClasses.some(className => element.classList.contains(className));
	}

	// Helper function to format date
	function formatDate(dateString) {
		return moment(dateString).locale('en').format('MMMM DD, YYYY');
	}

	// Helper function to add header information to the PDF
	function addPDFHeader(pdf, dashboardHeading, webStream, dateRange) {
		const textX = 25;
		let textY = 20;
		const lineSpacing = 15;

		// Set font size and style for the dashboard heading
		pdf.setFontSize(25);
		pdf.setFont('helvetica', 'bold');
		pdf.setTextColor(34, 40, 44);
		pdf.text(dashboardHeading, textX, textY);

		textY += lineSpacing;

		pdf.setFontSize(12);
		pdf.setFont('helvetica', 'normal');
		pdf.setTextColor(0, 0, 255);
		pdf.text(webStream, textX, textY);

		textY += lineSpacing;

		pdf.setFontSize(12);
		pdf.setTextColor(34, 40, 44);
		pdf.text(dateRange, textX, textY);
	}


});
