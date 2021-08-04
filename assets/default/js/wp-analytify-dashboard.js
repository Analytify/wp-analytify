// JS file for dashboard operations.
jQuery(document).ready(function ($) {

	// Dashboards DropDown
	$("#dashboard-options").on('change', function(event) {
		var val     = $(this).val( );
		var action  = $("#dashboard-options-form").attr('action');
		$("#dashboard-options-form").attr('action', action + '&show=' + val).submit();
	});

	$('.analytify_status_tab_header li').on('click', function(){
		var $this = $(this),
		tab_id = $this.attr('data-tab');

		$('.analytify_status_tab_header li').removeClass('analytify_active_stats');
		$('.analytify_panels_data').removeClass('analytify_active_panel');

		$this.addClass('analytify_active_stats');
		$("#"+tab_id).addClass('analytify_active_panel');
	});

	$(document).on('click', function (e){
		var container  = $('.analytify_select_date_list');
			btn_triger = $('.analytify_arrow_date_picker');
		if (!container.is(e.target) && !btn_triger.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0 && btn_triger.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.removeClass('analytify_active_date_list');
			btn_triger.removeClass('analytify_open');
		}
	});

	$('.analytify_stats_setting_bar button').on('click', function(){
		var $this = $(this),
		  button_id = $this.attr('data-graphType');
		  
		$this
			.parent()
			.removeClass('analytify_disabled')
			.siblings()
			.addClass('analytify_disabled');
			
		$("."+button_id).addClass('analytify_active_graph').siblings().removeClass('analytify_active_graph');

		if ( button_id == 'analytify_months_graph_by_visitors') {
			$('.total_month_users').show();
			$('.total_year_users').hide();
		}

		if ( button_id == 'analytify_years_graph_by_visitors') {
			$('.total_month_users').hide();
			$('.total_year_users').show();
		}

		if ( button_id == 'analytify_months_graph_by_view') {
			$('.total_month_views').show();
			$('.total_year_views').hide();
		}

		if ( button_id == 'analytify_years_graph_by_view') {
			$('.total_month_views').hide();
			$('.total_year_views').show();
		}

	});
	
    equalheight = function(container){

		var currentTallest = 0,
			currentRowStart = 0,
			rowDivs = new Array(),
			$el,
			topPosition = 0;

		$(container).each(function() {

			$el = $(this);
			$($el).height('auto');
			topPostion = $el.position().top;

			if (currentRowStart != topPostion) {
				for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
					rowDivs[currentDiv].height(currentTallest);
				}
				rowDivs.length = 0; // empty the array
				currentRowStart = topPostion;
				currentTallest = $el.height();
				rowDivs.push($el);
			} else {
				rowDivs.push($el);
				currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
			}

			for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
				rowDivs[currentDiv].height(currentTallest);

			}
		});
	};

	$(window).on('load', function() {
		setTimeout( function(){
			equalheight('.analytify_general_status_boxes');
		}, 5000 );
	});
	
	wp_analytify_paginated = function(){
		
		$('table.wp_analytify_paginated').each(function() {
		
			var currentPage = 0;
			var numPerPage = $(this).data('product-per-page') ? $(this).data('product-per-page') : 5;
			var $table = $(this);
			var $pager = $('<div class="wp_analytify_pager"></div>');
			
			$(this).closest('.analytify_status_body').next('.analytify_status_footer').find('.wp_analytify_pagination').html($pager);
			
			$table.bind('repaginate', function() {

				$table.find('tbody tr').hide();

				$filteredRows = $table.find('tbody tr');

				$filteredRows.slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();

				var numRows = $filteredRows.length;
				var numPages = Math.ceil(numRows / numPerPage);
				
				$pager.find('.page-number, .previous, .next').remove();

				// Show pagination if page is greate than 1.
				if ( numPages > 1 ) {
					for (var page = 0; page < numPages; page++) {
						var $newPage = $('<span class="page-number"></span>').text(page + 1).bind('click', {
							newPage: page
						}, function(event) {
							currentPage = event.data['newPage'];
							$table.trigger('repaginate');
						});

						if(page == currentPage){
							$newPage.addClass('clickable wp_analytify_active');
						}else{
							$newPage.addClass('clickable');
						}

						$newPage.appendTo($pager);
					}
				}
			});
			
			$table.trigger('repaginate');

		});
	
	};


	$(window).resize(function(){
		equalheight('.analytify_general_status_boxes');
    });

    $('.analytify_arrow_date_picker').on('click', function(){
		$(this).toggleClass("analytify_open");
		$('.analytify_select_date_list').toggleClass("analytify_active_date_list");
    });

    $('.analytify_selected_dashboard_field').on('click', function(){
		$(this).parent().toggleClass("analytify_open_list");
    });

    $('.analytify_dashboards_list li').on('click', function(){
		var $this  = $(this);
			dashboards_name = $this.text();
			
		$('.analytify_selected_dashboard_field').text(dashboards_name);
		$('.analytify_select_dashboard').removeClass("analytify_open_list");
	});
	
	// stop users from changing the start date and end value manually by typing
	$('#analytify_start, #analytify_end').on('keydown', function(event) {
		event.preventDefault();
		return false;
	});

    var	today_date						= moment().format("YYYY-MM-DD"),
		today_date_formated				= moment().format("MMM DD, YYYY"),
		
        last_7_days						= moment().subtract(7,'day').format("YYYY-MM-DD"),
		last_7_days_formated			= moment().subtract(7,'day').format("MMM DD, YYYY"),
		
		last_14_days					= moment().subtract(14,'day').format("YYYY-MM-DD"),
		last_14_days_formated			= moment().subtract(14,'day').format("MMM DD, YYYY"),
		
		last_30_day						= moment().subtract(1,'months').format("YYYY-MM-DD"),
		last_30_day_formated			= moment().subtract(1,'months').format("MMM DD, YYYY"),
		
		last_month_start_date			= moment().subtract(1,'months').startOf('month').format("YYYY-MM-DD"),
		last_month_start_date_formated	= moment().subtract(1,'months').startOf('month').format("MMM DD, YYYY"),

		last_month_end_date				= moment().subtract(1,'months').endOf('month').format("YYYY-MM-DD"),
		last_month_end_date_formated	= moment().subtract(1,'months').endOf('month').format("MMM DD, YYYY"),
		
        this_month_start_date			= moment().startOf('month').format("YYYY-MM-DD"),
		this_month_start_date_formated	= moment().startOf('month').format("MMM DD, YYYY"),
		
		last_3_months_start_date		= moment().subtract(3,'months').startOf('month').format("YYYY-MM-DD"),
		last_3_months_start_date_formated = moment().subtract(3,'months').startOf('month').format("MMM DD, YYYY"),
		
		last_6_months_start_date		= moment().subtract(6,'months').startOf('month').format("YYYY-MM-DD"),
		last_6_months_start_date_formated = moment().subtract(6,'months').startOf('month').format("MMM DD, YYYY"),
		
        last_year_start_date			= moment().subtract(12,'months').startOf('month').format("YYYY-MM-DD"),
		last_year_start_date_formated	= moment().subtract(12,'months').startOf('month').format("MMM DD, YYYY");
		
	
	/*
	 * onload check
	 * sets the default value of the start date
	 * just for apperance, should not servers any other function
	*/
	if ( $('#analytify_start').val() === '' ) {
		let start_date = $('#analytify_date_start').val();
		let start_date_formated = moment(start_date).locale('en').format("MMM DD, YYYY");
		$('#analytify_start').val(start_date_formated);
	}
	
	/*
	 * onload check
	 * sets the default value of the end date
	 * just for apperance, should not servers any other function
	*/
	if ( $('#analytify_end').val() === '' ) {
		let end_date = $('#analytify_date_end').val();
		let end_date_formated = moment(end_date).locale('en').format("MMM DD, YYYY");
		$('#analytify_end').val(end_date_formated);
	}

	// set the today's date text and end data attribute
	$('.analytify_today_date').text(today_date_formated).parent('span').attr('data-end', today_date);

	// set today's date text and start date attribute
	$('.analytify_current_day').text(today_date_formated).parent('span').attr('data-start', today_date);
	
	// set week date text and start date attribute
	$('.analytify_last_7_days').text(last_7_days_formated).parent('span').attr('data-start', last_7_days);
	
	// set 14 days text and start date attribute
    $('.analytify_last_14_days').text(last_14_days_formated).parent('span').attr('data-start', last_14_days);

	// set 1 month text and start date attribute
	$('.analytify_last_30_day').text(last_30_day_formated).parent('span').attr('data-start', last_30_day);

	// set this month for text and start date attribute
	$('.analytify_this_month_start_date').text(this_month_start_date_formated).parent('span').attr('data-start', this_month_start_date);

	// set last month for text and start date attribute
	$('.analytify_last_month_start_date').text(last_month_start_date_formated).parent('span').attr('data-start', last_month_start_date);
	
	// set last month for text and end date attribute
	$('.analytify_last_month_end_date').text(last_month_end_date_formated).parent('span').attr('data-end', last_month_end_date);
	
	// set last 3 month for text and start date attribute
	$('.analytify_last_3_months_start_date').text(last_3_months_start_date_formated).parent('span').attr('data-start', last_3_months_start_date);

	// set last 3 month for text and start date attribute
	$('.analytify_last_6_months_start_date').text(last_6_months_start_date_formated).parent('span').attr('data-start', last_6_months_start_date);

	// set last year for text and start date attribute
	$('.analytify_last_year_start_date').text(last_year_start_date_formated).parent('span').attr('data-start', last_year_start_date);
	
	
	if ( $('#analytify_start').length > 0 || $('#analytify_end').length > 0 ) {
    	var startDate,
        	endDate,
        updateStartDate = function() {
            startPicker.setStartRange(startDate);
            endPicker.setStartRange(startDate);
            endPicker.setMinDate(startDate);
        },
        updateEndDate = function() {
            startPicker.setEndRange(endDate);
            startPicker.setMaxDate(endDate);
            endPicker.setEndRange(endDate);
        },
        startPicker = new Pikaday({
            field: document.getElementById('analytify_start'),
            format: 'MMM DD, YYYY',
            maxDate: new Date(today_date_formated),
			i18n : analytify_dashboard.i18n,
            onSelect: function() {
				jQuery('#analytify_date_start').val(this.getMoment().format("YYYY-MM-DD"));
                startDate = this.getDate();
				updateStartDate();
            }
        }),
        endPicker = new Pikaday({
            field: document.getElementById('analytify_end'),
            position: 'bottom right',
            maxDate: new Date(today_date_formated),
            format: 'MMM DD, YYYY',
			i18n : analytify_dashboard.i18n,
            onSelect: function() {
				jQuery('#analytify_date_end').val(this.getMoment().format("YYYY-MM-DD"));
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
	$('.analytify_select_date_list li').on('click', function(){
		
		var $this = $(this).find("span");

		// if 'custom_range' is clicked, trigger the start input and end becasue we don't need to override any input data
		if($this.hasClass("custom_range")){
			$('#analytify_start').trigger("click");
			return;
		}

		var start_date = jQuery(this).children("span:first").attr('data-start');
		var start_date_formated = moment(start_date).locale('en').format('MMM DD, YYYY');
			
		setTimeout(() => { startPicker.setDate(start_date_formated); }, 1000);
		jQuery('#analytify_date_start').val(start_date);
		jQuery('#analytify_start').val(start_date_formated);
		
		var end_date = jQuery(this).children("span:first").attr('data-end');
		var end_date_formated = moment(end_date).locale('en').format('MMM DD, YYYY');
		
		setTimeout(() => { endPicker.setDate(end_date_formated); }, 1000);
		jQuery('#analytify_date_end').val(end_date);
		jQuery('#analytify_end').val(end_date_formated);
		
		$('#analytify_date_diff').val($this.attr('data-date-diff'));
		
		$('.analytify_select_date_list').removeClass("analytify_active_date_list");
		$('.analytify_arrow_date_picker').removeClass("analytify_open");

	});

	/**
	 * Send ajax on cross of add.
	 *
	 */
	$('.analytify_general_status-icon').on('click', function() {
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
	$('.analytify_nav_tab_parent .nav-tab-active').parents('.analytify_nav_tab_parent').addClass('nav-tab-active');
});