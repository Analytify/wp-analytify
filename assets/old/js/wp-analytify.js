  var height = 0,
  height2 = 0,
  height3 = 0;

  jQuery(document).ready(function ($) {

	/**
	 * [Redirect to the stats box from all posts link]
	 */
	 if( window.location.hash ) {

	 	var hsh = window.location.hash;
	 	hash = hsh.split('#');

	 	if( hash[1] === 'pa-single-admin-analytics' ) {

	 		jQuery('html, body').animate({
	 			scrollTop: (jQuery("#pa-single-admin-analytics").offset().top - 55)
	 		}, 1000, function() {
	 			jQuery('html, body').animate({
	 				scrollTop: (jQuery("#pa-single-admin-analytics").offset().top - 55)
	 			}, 1000);

	 		});
	 	}
	 }

	/**
	 * [Redirect to the stats box from current posts link]
	 */
  $("#view_stats_analytify").on('click', function(event) {

    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();

      // Store hash
      var hash = this.hash;

      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
      $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 800, function(){

        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    } // End if
  });


	 $('.remove-stats').remove();

	 $("#start_date").datepicker({
	 	dateFormat : 'yy-mm-dd',
	 	changeMonth : true,
	 	changeYear : true,
	 	beforeShow: function() {
	 		$('#ui-datepicker-div').addClass('mycalander');
	 	},
	 	yearRange: '-9y:c+nn'
	 });

	 $("#end_date").datepicker({
	 	dateFormat : 'yy-mm-dd',
	 	changeMonth : true,
	 	changeYear : true,
	 	beforeShow: function() {
	 		$('#ui-datepicker-div').addClass('mycalander');
	 	},
	 	yearRange: '-9y:c+nn'
	 }).datepicker('setDate',new Date());


	 $("#view_analytics").click(function (e) {

    e.preventDefault();
	 	var start_date = $("#analytify_start").val();
    start_date = moment(start_date).format("YYYY-MM-DD");
    
	 	var end_date = $("#analytify_end").val();
    end_date =  moment(end_date).format("YYYY-MM-DD");

	 	var urlpost = $("#post_ID").val();

	 	$.ajax({

	 		type: 'POST',
	 		url: ajaxurl,
	 		data: 'action=get_ajax_single_admin_analytics&start_date=' + start_date + "&end_date=" + end_date+"&post_id="+urlpost,

	 		beforeSend: function () {
	 			$(".show-hide").addClass("stats_loading");
	 		},
	 		success: function (data, textStatus, XMLHttpRequest) {
	 			$(".show-hide").html(data).removeClass("stats_loading");
				  equalheight('.analytify_general_status_boxes');
				},
				error: function (MLHttpRequest, textStatus, errorThrown) {
					alert("Oops: Something is wrong, Please contact our support.");
				}
			});

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
           rowDivs[currentDiv].css('min-height', currentTallest +'px');
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
         rowDivs[currentDiv].css('min-height', currentTallest +'px');
       }
     });
    }







    $(window).resize(function(){
      equalheight('.analytify_general_status_boxes');
    });


	  /*$("#disable_front").change(function(){
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
			$(".arrow_btn").on("click",function () {
				$(this).parent().next().slideToggle('slow').next().slideToggle('slow').end().end().toggleClass('close');
			});
			$(".authentication_btn").on("click",function () {
				$(".authentication_table").addClass("active").removeClass("show_btn");
				$(".over_lap_bg").fadeIn();
			});

			$(".over_lap_bg").on("click",function () {
				$(".authentication_table").addClass("show_btn");
				$(this).hide();
			});

			$(".grids_auto_size").each(function(){
				if(($(this).height())>height){
					height = $(this).height();
				}
			});


			$(".grids_auto_size").each(function(){
				$(this).css("min-height",height);
			});
			$(".keywordscont").each(function(){
				if(($(this).height())>height2){
					height2 = $(this).height();
				}
			});
			$(".keywordscont").each(function(){
				$(this).css("min-height",height2);
			});

			$(".stats").each(function(){
				if(($(this).height())>height3){
					height3 = $(this).height();
				}
			});
			$(".stats").each(function(){
				$(this).css("min-height",height3);
			});


		});
