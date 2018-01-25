// JS file for dashboard operations.
jQuery(document).ready(function ($) {



  // Dashboards DropDown
  $("#dashboard-options").change(function(event) {

    var val     = $(this).val( );
    var action  = $("#dashboard-options-form").attr('action');

    $("#dashboard-options-form").attr('action', action + '&show=' + val).submit();
  });



  $('.analytify_status_tab_header li').click(function(){
  		var $this = $(this),
          tab_id = $this.attr('data-tab');

  		$('.analytify_status_tab_header li').removeClass('analytify_active_stats');
  		$('.analytify_panels_data').removeClass('analytify_active_panel');

  		$this.addClass('analytify_active_stats');
  		$("#"+tab_id).addClass('analytify_active_panel');
  });

$(document).click(function (e){
    var container  = $('.analytify_select_date_list');
        btn_triger = $('.analytify_arrow_date_picker');
    if (!container.is(e.target) && !btn_triger.is(e.target) // if the target of the click isn't the container...
        && container.has(e.target).length === 0 && btn_triger.has(e.target).length === 0) // ... nor a descendant of the container
    {
        container.removeClass('analytify_active_date_list');
        btn_triger.removeClass('analytify_open');
    }
});

  $('.analytify_stats_setting_bar button').click(function(){
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
       $($el).height('auto')
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
    }

    $(window).load(function() {
      setTimeout( function(){
        equalheight('.analytify_general_status_boxes');
      }  , 5000 );
    });

  wp_analytify_paginated = function(){
    $('table.wp_analytify_paginated td', 'table.wp_analytify_paginated').each(function(i) {
        $(this).text(i+1);
    });

    $('table.wp_analytify_paginated').each(function() {
        var currentPage = 0;
        var numPerPage = 5;
        var $table = $(this);
        var $table_pagination = $(this).parent().next();
        $table.bind('repaginate', function() {
            $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
        });
        $table.trigger('repaginate');
        var numRows = $table.find('tbody tr').length;
        var numPages = Math.ceil(numRows / numPerPage);
        var $pager = $('<div class="wp_analytify_pager"></div>');

        // Show pagination if page is greate than 1.
        if ( numPages > 1 ) {
          for (var page = 0; page < numPages; page++) {
              $('<span class="wp_analytify_page-number"></span>').text(page + 1).bind('click', {
                  newPage: page
              }, function(event) {
                  currentPage = event.data['newPage'];
                  $table.trigger('repaginate');
                  $(this).addClass('wp_analytify_active').siblings().removeClass('wp_analytify_active');
              }).appendTo($pager).addClass('clickable');
          }
          $pager.appendTo(".wp_analytify_pagination").find('span.wp_analytify_page-number:first').addClass('wp_analytify_active');
        }

    });
  }


    $(window).resize(function(){
      equalheight('.analytify_general_status_boxes');
    });

    $('.analytify_arrow_date_picker').click(function(){
      $(this).toggleClass("analytify_open");
      $('.analytify_select_date_list').toggleClass("analytify_active_date_list");
    });


    $('.analytify_selected_dashboard_field').click(function(){
      $(this).parent().toggleClass("analytify_open_list");
    });

    $('.analytify_dashboards_list li').click(function(){
      var $this  = $(this);
          dashboards_name = $this.text();

          $('.analytify_selected_dashboard_field').text(dashboards_name);

          $('.analytify_select_dashboard').removeClass("analytify_open_list");
    });




    $( ".analytify_form_date" ).submit(function( event ) {
      if ( !$('#analytify_start').val() == '' || !$('#analytify_end').val() == '' ) {
      var s_date = $('#analytify_start').val(),
          s_date = moment(s_date, 'MMM DD, YYYY').format("YYYY-MM-DD"),
          e_date = $('#analytify_end').val(),
          e_date = moment(e_date, 'MMM DD, YYYY').format("YYYY-MM-DD");
          $('#analytify_start_val').val(s_date);
          $('#analytify_end_val').val(e_date);
      }else{
         event.preventDefault();
      }
    });



    var momment                     = moment(),
        today_date                  = momment.format("MMM DD, YYYY"),
        last_7_days                 = moment().subtract(7,'day').format("MMM DD, YYYY"),
        last_14_days                = moment().subtract(14,'day').format("MMM DD, YYYY"),
        last_30_day                 = moment().subtract(1,'months').format("MMM DD, YYYY"),
        last_month_end_date         = moment().subtract(1,'months').endOf('month').format("MMM DD, YYYY"),
        this_month_start_date       = moment().startOf('month').format("MMM DD, YYYY"),
        last_month_start_date       = moment().subtract(1,'months').startOf('month').format("MMM DD, YYYY"),
        last_3_months_start_date    = moment().subtract(3,'months').startOf('month').format("MMM DD, YYYY"),
        last_6_months_start_date    = moment().subtract(6,'months').startOf('month').format("MMM DD, YYYY"),
        last_year_start_date        = moment().subtract(12,'months').startOf('month').format("MMM DD, YYYY");

    $('.analytify_today_date').text(today_date);

    if ( $('#analytify_start').val() === '' ) {
      $('#analytify_start').val(last_30_day);
    } else {
      var s_date = $('#analytify_start').val();
          s_date = moment(s_date).format("MMM DD, YYYY");
          $('#analytify_start').val(s_date);
    }

    if ( $('#analytify_end').val() === '' ) {
      $('#analytify_end').val(today_date);
    } else {
      var e_date = $('#analytify_end').val();
          e_date = moment(e_date).format("MMM DD, YYYY");
          $('#analytify_end').val(e_date);
    }

    $('.analytify_last_7_days').text(last_7_days);
    $('.analytify_last_14_days').text(last_14_days);

    $('.analytify_last_30_day').text(last_30_day);
    $('.analytify_last_month_end_date').text(last_month_end_date);
    $('.analytify_last_month_start_date').text(last_month_start_date);
    $('.analytify_this_month_start_date').text(this_month_start_date);
    $('.analytify_last_3_months_start_date').text(last_3_months_start_date);
    $('.analytify_last_6_months_start_date').text(last_6_months_start_date);
    $('.analytify_last_year_start_date').text(last_year_start_date);

    $( ".analytify_select_date_list li" ).each(function() {
        $( this ).children().attr('data-start', $( this ).find(".analytify_start_date_data").text());
        $( this ).children().attr('data-end', $( this ).find(".analytify_end_date_data").text());
    });
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
            maxDate: new Date(today_date),
            onSelect: function() {
                startDate = this.getDate();
                updateStartDate();

            }
        }),
        endPicker = new Pikaday({
            field: document.getElementById('analytify_end'),
            position: 'bottom right',
            maxDate: new Date(today_date),
            format: 'MMM DD, YYYY',

            onSelect: function() {
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


        $('.analytify_select_date_list li').click(function(){

          var $this  = $(this).find("span"),
              start_date = $this.attr('data-start');
              end_date = $this.attr('data-end');

              $('#analytify_start').val(start_date);
              $('#analytify_end').val(end_date);
              $('#analytify_date_diff').val($this.attr('data-date-diff'));

              startPicker.setDate(start_date);
              endPicker.setDate(end_date);

              if($this.hasClass("custom_range")){
                $('#analytify_start').trigger( "click" );
              }

              $('.analytify_select_date_list').removeClass("analytify_active_date_list");
              $('.analytify_arrow_date_picker').removeClass("analytify_open");
        });
}

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

});
