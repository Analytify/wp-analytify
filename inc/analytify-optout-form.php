<style media="screen">
.wp-analytify-modal.active {
  display: block;
}
.wp-analytify-modal {
    position: fixed;
    overflow: auto;
    height: 100%;
    width: 100%;
    top: 0;
    z-index: 100000;
    display: none;
    background: rgba(0,0,0,0.6);
}
.wp-analytify-modal.active .wp-analytify-modal-dialog {
    top: 10%;
}
.wp-analytify-modal .wp-analytify-modal-dialog {
    background: transparent;
    position: absolute;
    left: 50%;
    margin-left: -298px;
    padding-bottom: 30px;
    top: -100%;
    z-index: 100001;
    width: 596px;
}
.wp-analytify-modal .wp-analytify-modal-header {
    border-bottom: #eeeeee solid 1px;
    background: #fbfbfb;
    padding: 15px 20px;
    position: relative;
    margin-bottom: -10px;
}
.wp-analytify-modal .wp-analytify-modal-body {
    border-bottom: 0;
}
.wp-analytify-modal .wp-analytify-modal-body, .wp-analytify-modal .wp-analytify-modal-footer {
    border: 0;
    background: #fefefe;
    padding: 20px;
}
.wp-analytify-modal .wp-analytify-modal-body>div {
    margin-top: 10px;
}
.wp-analytify-modal .wp-analytify-modal-body>div h2 {
    font-weight: bold;
    font-size: 20px;
    margin-top: 0;
}
.wp-analytify-modal .wp-analytify-modal-body p {
    font-size: 14px;
}
.wp-analytify-modal .wp-analytify-modal-footer {
    border-top: #eeeeee solid 1px;
    text-align: right;
}
.wp-analytify-modal .wp-analytify-modal-footer>.button:first-child {
    margin: 0;
}
.wp-analytify-modal .wp-analytify-modal-footer>.button {
    margin: 0 7px;
}
.wp-analytify-modal .wp-analytify-modal-body>div h2 {
    font-weight: bold;
    font-size: 20px;
    margin-top: 0;
}
.wp-analytify-modal .wp-analytify-modal-body h2 {
    font-size: 20px;
     line-height: 1.5em;
}
.wp-analytify-modal .wp-analytify-modal-header h4 {
    margin: 0;
    padding: 0;
    text-transform: uppercase;
    font-size: 1.2em;
    font-weight: bold;
    color: #cacaca;
    text-shadow: 1px 1px 1px #fff;
    letter-spacing: 0.6px;
    -webkit-font-smoothing: antialiased;
}

.wp-analytify-optout-spinner{
    display: none;
}
</style>


<div class="wp-analytify-modal wp-analytify-modal-opt-out">
  <div class="wp-analytify-modal-dialog">
    <div class="wp-analytify-modal-header">
      <h4>Opt Out</h4>
    </div>
    <div class="wp-analytify-modal-body">
      <div class="wp-analytify-modal-panel active">
        <h2>We appreciate your help in making the plugin better by letting us track some usage data.</h2>
        <div class="notice notice-error inline opt-out-error-message" style="display: none;">
          <p></p>
        </div>
        <p>Usage tracking is done in the name of making <strong>Analytify</strong> better. Making a better user experience, prioritizing new features, and more good things. We'd really appreciate if you'll reconsider letting us continue with the tracking.</p>
        <p>By clicking "Opt Out", we will no longer be sending any data to <a href="https://analytify.io" target="_blank">Analytify</a>.</p>
      </div>
    </div>
    <div class="wp-analytify-modal-footer">
      <form class="" action="<?php echo admin_url( 'plugins.php' ) ?>" method="post">
        <span class="wp-analytify-optout-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
        <button type='submit' name='analytify-submit-optout' id='analytify_optout_button'  class="button button-secondary button-opt-out" tabindex="1">Opt Out</button>
        <button class="button button-primary button-close" tabindex="2">On second thought - I want to continue helping</button>
      </form>
    </div>
  </div>
</div>



<script type="text/javascript">

(function( $ ) {

  $(function() {
    var pluginSlug = 'wp-analytify';
    // Code to fire when the DOM is ready.

    $(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .opt-out', function(e){
        e.preventDefault();
        $('.wp-analytify-modal-opt-out').addClass('active');
    });

    $(document).on('click', '.button-close', function(event) {
      event.preventDefault();
      $('.wp-analytify-modal-opt-out').removeClass('active');
    });

    $(document).on('click','#analytify_optout_button', function(event) {
      event.preventDefault();
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'analytify_optout_yes'
        },
        beforeSend: function(){
          $(".wp-analytify-optout-spinner").show();
          $(".wp-analytify-popup-allow-deactivate").attr("disabled", "disabled");
        }
      })
      .done(function() {
        $(".wp-analytify-optout-spinner").hide();
        $('.wp-analytify-modal-opt-out').removeClass('active');
        location.reload();
      });

    });

  });

})( jQuery ); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
</script>
