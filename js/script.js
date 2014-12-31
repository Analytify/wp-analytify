jQuery(document).ready(function  ($) {
	(function() {
        [].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {  
          new SelectFx(el);
        } );
      })();
		$('.test').click(function () {
			$('#modal-content').modal({
				show: true
			});
		});
		$(".cs-options li a").click(function() {
		    $($(this).data("target")).fadeIn('slow'); 
	  	});
		$(".analytify_popup_clsbtn").click(function() {
		    $(this).closest(".analytify_popup").fadeOut('slow'); 
	  	});
		$(".trigger").click(function() {
	    
		    $(".menu").toggleClass("active"); 
		});
		$(".btn-icon a").click(function() {
	 
			$(".menu").removeClass("active"); 
		});
});
