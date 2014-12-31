
var height = 0,
	height2 = 0,
	height3 = 0;
	
jQuery(document).ready(function ($) {
			
	$(".analytify-chosen").chosen();
	$('.remove-stats').remove();
	$("#start_date").datepicker({
						dateFormat : 'yy-mm-dd',
						changeMonth : true,
						changeYear : true,
						beforeShow: function() {
							$('#ui-datepicker-div').addClass('mycalander');
						},
						yearRange: '-9y:c+nn'       
					}).datepicker('setDate',new Date());

	$("#end_date").datepicker({
							dateFormat : 'yy-mm-dd',
							changeMonth : true,
							changeYear : true,
							beforeShow: function() {
								$('#ui-datepicker-div').addClass('mycalander');
							},
							yearRange: '-9y:c+nn'       
					}).datepicker('setDate',new Date());
			

	$("#view_analytics").click(function () {

					var start_date = $("#start_date").val();
					var end_date = $("#end_date").val();
					var urlpost = $("#post_ID").val();

					$.ajax({

							type: 'POST',
							url: ajaxurl,
							data: 'action=get_ajax_single_admin_analytics&start_date=' + start_date + "&end_date=" + end_date+"&post_id="+urlpost,

							beforeSend: function () {
									$(".loading").css("display", "block").css( "text-align", "center" );
									$(".show-hide").css("display", "none").html('');
							},
							success: function (data, textStatus, XMLHttpRequest) {
									$(".loading").css("display", "none");
									$(".show-hide").html(data).css("display", "block");
									//$(".show-hide").css("display", "block");
							},
							error: function (MLHttpRequest, textStatus, errorThrown) {
									alert("An error");
							}
					});

			});

	$('input[name="auth_step"]').on("change",function () {

		$('.' + $('input[name="auth_step"]:checked').val()).show();
		$('.' + $('input[name="auth_step"]:not(:checked)').val()).hide();

	});


	$('input[name="auth_step"]').each( function () {
		console.log($(this).val());
		if($(this).is(':checked')) $('.' +$(this).val()).show();
	});



	$('#populate_keys').on("click",function(){

		$.ajax({

				type: 'POST',
				url: ajaxurl,
				data: 'action=get_ajax_secret_keys&n=' + Math.floor((Math.random() * 100) + 1),

				success: function (data, textStatus, XMLHttpRequest) {
					var obj = JSON.parse(data);
					
					$('#analytify_clientid').val(obj[0].id);
					$('#analytify_clientsecret').val(obj[0].secret);
					$('#analytify_apikey').val(obj[0].key);
				},
				error: function (MLHttpRequest, textStatus, errorThrown) {
					alert("Couldn't fetch the keys, Please create your own from Google console.");
				}
		});
	});

			/*$('input[name="auth_step"]').on("click",function () {
				$('.user_access_code').show();
			});*/
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

	jQuery(window).resize(function($){

			jQuery(".grids_auto_size").each(function(){
					jQuery(this).css("min-height",height);
			});

			jQuery(".keywordscont").each(function(){
					jQuery(this).css("min-height",height2);
			});
			
			jQuery(".stats").each(function(){
					jQuery(this).css("min-height",height3);
			});

	});