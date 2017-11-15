<style>
    .wp-analytify-hidden{
      overflow: hidden;
    }
    .wp-analytify-popup-overlay .wp-analytify-internal-message{
      margin: 3px 0 3px 22px;
      display: none;
    }
    .wp-analytify-reason-input{
      margin: 3px 0 3px 22px;
      display: none;
    }
    .wp-analytify-pro-message{
      margin: 3px 0 3px 22px;
      display: none;
      color: #ed1515;
      font-size: 14px;
      font-weight: 600;
    }
    .wp-analytify-reason-input input[type="text"]{
      width: 100%;
      display: block;
    }
  .wp-analytify-popup-overlay{
    background: rgba(0,0,0, .8);
    position: fixed;
    top:0;
    left: 0;
    height: 100%;
    width: 100%;
    z-index: 1000;
    overflow: auto;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s ease-in-out:
  }
  .wp-analytify-popup-overlay.wp-analytify-active{
    opacity: 1;
    visibility: visible;
  }
  .wp-analytify-serveypanel{
    width: 600px;
    background: #fff;
    margin: 65px auto 0;
  }
  .wp-analytify-popup-header{
    background: #f1f1f1;
    padding: 20px;
    border-bottom: 1px solid #ccc;
  }
  .wp-analytify-popup-header h2{
    margin: 0;
  }
  .wp-analytify-popup-body{
      padding: 10px 20px;
  }
  .wp-analytify-popup-footer{
    background: #f9f3f3;
    padding: 10px 20px;
    border-top: 1px solid #ccc;
  }
  .wp-analytify-popup-footer:after{
    content:"";
    display: table;
    clear: both;
  }
  .action-btns{
    float: right;
  }
  .wp-analytify-anonymous{
    display: none;
  }
  .attention, .error-message {
    color: red;
    font-weight: 600;
    display: none;
  }
  .wp-analytify-spinner{
    display: none;
  }
  .wp-analytify-spinner img{
    margin-top: 3px;
  }

</style>
<div class="wp-analytify-popup-overlay">
  <div class="wp-analytify-serveypanel">
    <form action="#" method="post" id="wp-analytify-deactivate-form">
    <div class="wp-analytify-popup-header">
      <h2><?php _e( 'Quick feedback about Analytify (Google Analytics for WordPress)', 'wp-analytify' ); ?></h2>
    </div>
    <div class="wp-analytify-popup-body">
      <h3><?php _e( 'If you have a moment, please let us know why you are deactivating:', 'wp-analytify' ); ?></h3>
      <ul id="wp-analytify-reason-list">
        <li class="wp-analytify-reason wp-analytify-reason-pro" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="pro">
            </span>
            <span><?php _e( 'I upgraded to Analytify Pro', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-pro-message"><?php _e( 'No need to deactivate this Analytify Core version. Pro version works as an add-on with Core version.', 'wp-analytify' ) ?></div>
        </li>
        <li class="wp-analytify-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="1">
            </span>
            <span><?php _e( 'I only needed the plugin for a short period', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
        </li>
        <li class="wp-analytify-reason has-input" data-input-type="textfield">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="2">
            </span>
            <span><?php _e( 'I found a better plugin', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
          <div class="wp-analytify-reason-input"><span class="message error-message"><?php _e( 'Kindly tell us the name of plugin', 'wp-analytify' ); ?></span><input type="text" name="better_plugin" placeholder="What's the plugin's name?"></div>
        </li>
        <li class="wp-analytify-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="3">
            </span>
            <span><?php _e( 'The plugin broke my site', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
        </li>
        <li class="wp-analytify-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="4">
            </span>
            <span><?php _e( 'The plugin suddenly stopped working', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
        </li>
        <li class="wp-analytify-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="5">
            </span>
            <span><?php _e( 'I no longer need the plugin', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
        </li>
        <li class="wp-analytify-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="6">
            </span>
            <span><?php _e( "It's a temporary deactivation. I'm just debugging an issue.", 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
        </li>
        <li class="wp-analytify-reason has-input" data-input-type="textfield" >
          <label>
            <span>
              <input type="radio" name="wp-analytify-selected-reason" value="7">
            </span>
            <span><?php _e( 'Other', 'wp-analytify' ); ?></span>
          </label>
          <div class="wp-analytify-internal-message"></div>
          <div class="wp-analytify-reason-input"><span class="message error-message "><?php _e( 'Kindly tell us the reason so we can improve.', 'wp-analytify' ); ?></span><input type="text" name="other_reason" placeholder="Would you like to share what's other reason ?"></div>
        </li>
      </ul>
    </div>
    <div class="wp-analytify-popup-footer">
      <label class="wp-analytify-anonymous"><input type="checkbox" /><?php _e( 'Anonymous feedback', 'wp-analytify' ); ?></label>
        <input type="button" class="button button-secondary button-skip wp-analytify-popup-skip-feedback" value="Skip &amp; Deactivate" >
      <div class="action-btns">
        <span class="wp-analytify-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
        <input type="submit" class="button button-secondary button-deactivate wp-analytify-popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
        <a href="#" class="button button-primary wp-analytify-popup-button-close"><?php _e( 'Cancel', 'wp-analytify' ); ?></a>

      </div>
    </div>
  </form>
    </div>
  </div>


  <script>
    (function( $ ) {

      $(function() {

        var pluginSlug = 'wp-analytify';
        // Code to fire when the DOM is ready.

        $(document).on('click', 'tr[data-slug="' + pluginSlug + '"] .deactivate', function(e){
          e.preventDefault();
          $('.wp-analytify-popup-overlay').addClass('wp-analytify-active');
          $('body').addClass('wp-analytify-hidden');
        });
        $(document).on('click', '.wp-analytify-popup-button-close', function () {
          close_popup();
        });
        $(document).on('click', ".wp-analytify-serveypanel,tr[data-slug='" + pluginSlug + "'] .deactivate",function(e){
          e.stopPropagation();
        });

        $(document).click(function(){
          close_popup();
        });
        $('.wp-analytify-reason label').on('click', function(){
          if($(this).find('input[type="radio"]').is(':checked')){
            //$('.wp-analytify-anonymous').show();
            $(this).next().next('.wp-analytify-reason-input').show().end().end().parent().siblings().find('.wp-analytify-reason-input').hide();
          }
          $('.wp-analytify-pro-message').hide();
        });
        $('.wp-analytify-reason-pro label').on('click', function(){
          if($(this).find('input[type="radio"]').is(':checked')){
            $(this).next('.wp-analytify-pro-message').show().end().end().parent().siblings().find('.wp-analytify-reason-input').hide();
            $('.wp-analytify-popup-allow-deactivate').attr('disabled', 'disabled');
            $('.wp-analytify-popup-skip-feedback').attr('disabled', 'disabled');
          }
        });
        $('input[type="radio"][name="wp-analytify-selected-reason"]').on('click', function(event) {
          $(".wp-analytify-popup-allow-deactivate").removeAttr('disabled');
          $(".wp-analytify-popup-skip-feedback").removeAttr('disabled');
        });
        $(document).on('submit', '#wp-analytify-deactivate-form', function(event) {
          event.preventDefault();

          var _reason =  $(this).find('input[type="radio"][name="wp-analytify-selected-reason"]:checked').val();
          var _reason_details = '';
          if ( _reason == 2 ) {
            _reason_details = $(this).find("input[type='text'][name='better_plugin']").val();
          } else if ( _reason == 7 ) {
            _reason_details = $(this).find("input[type='text'][name='other_reason']").val();
          }

          if ( ( _reason == 7 || _reason == 2 ) && _reason_details == '' ) {
            $('.message.error-message').show();
            return ;
          }

          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action        : 'analytify_deactivate',
              reason        : _reason,
              reason_detail : _reason_details,
            },
            beforeSend: function(){
              $(".wp-analytify-spinner").show();
              $(".wp-analytify-popup-allow-deactivate").attr("disabled", "disabled");
            }
          })
          .done(function() {
            $(".wp-analytify-spinner").hide();
            $(".wp-analytify-popup-allow-deactivate").removeAttr("disabled");
            window.location.href =  $("tr[data-slug='"+ pluginSlug +"'] .deactivate a").attr('href');
          });

        });

        $('.wp-analytify-popup-skip-feedback').on('click', function(e){
          window.location.href =  $("tr[data-slug='"+ pluginSlug +"'] .deactivate a").attr('href');
        })

        function close_popup() {
          $('.wp-analytify-popup-overlay').removeClass('wp-analytify-active');
          $('#wp-analytify-deactivate-form').trigger("reset");
          $(".wp-analytify-popup-allow-deactivate").attr('disabled', 'disabled');
          $(".wp-analytify-reason-input").hide();
          $('body').removeClass('wp-analytify-hidden');
          $('.message.error-message').hide();
        }
        });

        })( jQuery ); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
  </script>
