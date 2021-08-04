// JS file for Settings operations.
jQuery(document).ready(function($) {

    // hide this checkbox for hiding profiles.
    if (analytify_settings.is_hide_profile != 'off') {
        $('#wp-analytify-profile\\[hide_profiles_list\\]').closest('tr').hide();
    }
    /*if (pagenow == 'analytify_page_analytify-settings' && analytify_settings.is_authenticate == '') {
        localStorage.setItem('activetab', '#wp-analytify-authentication');
    }*/
    // open profile tab after authenticate.
    if (pagenow == 'analytify_page_analytify-settings' && analytify_settings.is_authenticate == '1') {
        if (window.location.href.indexOf("#wp-analytify-profile") > -1) {
            localStorage.setItem('activetab', '#wp-analytify-profile');
        }
        if (window.location.href.indexOf("#wp-analytify-email") > -1) {
            localStorage.setItem('activetab', '#wp-analytify-email');
        }
    }

    // Apply Chosen Style on Select DropDowns
    $(".analytify-chosen").chosen();

    function mobilecheck() {
        var check = false;
        (function(a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    }

    // Advance Tab API keys
    if ($('.user_advanced_keys input[type=checkbox]').is(":checked")) {
        $('.user_keys').show();
    }

    $(".user_advanced_keys input[type=checkbox]").on("change", function() {

        if ($('.user_advanced_keys input[type=checkbox]').is(":checked")) {
            $('.user_keys').show();
        } else {
            $('.user_keys').hide();
        }

    });


    // Show the text field for linked domain when 'Setup Cross-domain Tracking' is checked
    if ($('.user_linker_tracking input[type=checkbox]').is(":checked")) {
        $('.linker_tracking').show();
    }

    $(".user_linker_tracking input[type=checkbox]").on("change", function() {

        if ($('.user_linker_tracking input[type=checkbox]').is(":checked")) {
            $('.linker_tracking').show();
        } else {
            $('.linker_tracking').hide();
        }

    });

    // show license tab on license link click from other page.
    if (window.location.href.indexOf("#wp-analytify-license") > -1) {
        localStorage.setItem('activetab', '#wp-analytify-license');
    } else if (window.location.href.indexOf("#wp-analytify-help") > -1) {
        localStorage.setItem('activetab', '#wp-analytify-help');
    }

    // show license tab on license link click on settings page.
    $('.wp-analytify-license-notice').on('click', function(evt) {

        $('.group').hide();
        $('.analytify_nav_tab_wrapper a').removeClass('nav-tab-active');

        if (typeof(localStorage) != 'undefined') {
            localStorage.setItem("activetab", '#wp-analytify-license');
        }
        $('#wp-analytify-license-tab').addClass('nav-tab-active').blur();
        $('#wp-analytify-license').fadeIn();
        evt.preventDefault();
    });

    $('.group').hide();

    var activetab = '';
    if (typeof(localStorage) != 'undefined') {
        activetab = localStorage.getItem("activetab");
    }
    if (activetab != '' && $(activetab).length) {
        $(activetab).fadeIn();
    } else {
        $('.group:first').fadeIn();
    }
    $('.group .collapsed').each(function() {
        $(this).find('input:checked').parent().parent().parent().nextAll().each(
            function() {
                if ($(this).hasClass('last')) {
                    $(this).removeClass('hidden');
                    return false;
                }
                $(this).filter('.hidden').removeClass('hidden');
            });
    });

    if (activetab != '' && $(activetab + '-tab').length) {
        $(activetab + '-tab').addClass('nav-tab-active');


    } else {
        $('.analytify_nav_tab_wrapper a:first').addClass('nav-tab-active');
    }

    $('#toplevel_page_analytify-dashboard ul.wp-submenu li.current, #toplevel_page_analytify-dashboard ul.wp-submenu li a.current').removeClass('current');

    // change '.current' class to admin submenu based on tab
    if( '#wp-analytify-license' === activetab ) {

        // set 'current' admin submenu for license tab

        let admin_submenu_license = $('li#toplevel_page_analytify-dashboard .wp-submenu').find('a[href$="analytify-settings#wp-analytify-license"]');
        admin_submenu_license.addClass('current');
        admin_submenu_license.parent().addClass('current');

    } else if ( '#wp-analytify-help' === activetab ) {
        
        // set 'current' admin submenu for help tab            

        let admin_submenu_help = $('li#toplevel_page_analytify-dashboard .wp-submenu').find('a[href$="analytify-settings#wp-analytify-help"]');
        admin_submenu_help.addClass('current');
        admin_submenu_help.parent().addClass('current');

    } else {
        
        // set 'current' admin submenu to settings

        let admin_submenu_setting = $('li#toplevel_page_analytify-dashboard .wp-submenu').find('a[href$="analytify-settings"]');
        admin_submenu_setting.addClass('current');
        admin_submenu_setting.parent().addClass('current');

    }
    

    // load diagnostic debug log only when help tab is active
    if ($('.nav-tab-active').attr('href') === '#wp-analytify-help') refresh_debug_log();

    // wp admin submenu trigger for license tab
    $('li#toplevel_page_analytify-dashboard .wp-submenu a[href$="#wp-analytify-license"]').on('click', function(evt) {

        evt.preventDefault();
        $('.analytify_nav_tab_wrapper a').removeClass('nav-tab-active');
        
        $('.analytify_nav_tab_wrapper a#wp-analytify-license-tab').addClass('nav-tab-active').blur();
        var clicked_group = '#wp-analytify-license';

        if (typeof(localStorage) != 'undefined') {
            localStorage.setItem("activetab", clicked_group);
        }

        $('.group').hide();
        $(clicked_group).fadeIn();

        $('#toplevel_page_analytify-dashboard ul.wp-submenu li.current, #toplevel_page_analytify-dashboard ul.wp-submenu li a.current').removeClass('current');
        $(this).addClass('current');
        $(this).parent().addClass('current');

    });
    
    // wp admin submenu trigger for help tab
    $('li#toplevel_page_analytify-dashboard .wp-submenu a[href$="#wp-analytify-help"]').on('click', function(evt) {

        evt.preventDefault();
        $('.analytify_nav_tab_wrapper a').removeClass('nav-tab-active');
        
        $('.analytify_nav_tab_wrapper a#wp-analytify-help-tab').addClass('nav-tab-active').blur();
        var clicked_group = '#wp-analytify-help';

        if (typeof(localStorage) != 'undefined') {
            localStorage.setItem("activetab", clicked_group);
        }

        $('.group').hide();
        $(clicked_group).fadeIn();

        refresh_debug_log();

        $('#toplevel_page_analytify-dashboard ul.wp-submenu li.current, #toplevel_page_analytify-dashboard ul.wp-submenu li a.current').removeClass('current');
        $(this).addClass('current');
        $(this).parent().addClass('current');

    });

    // wp admin submenu trigger for settings tab
    $('li#toplevel_page_analytify-dashboard .wp-submenu a[href$="analytify-settings"]').on('click', function(evt) {

        evt.preventDefault();
        $('.analytify_nav_tab_wrapper a').removeClass('nav-tab-active');
        
        $('.analytify_nav_tab_wrapper a#wp-analytify-profile-tab').addClass('nav-tab-active').blur();
        var clicked_group = '#wp-analytify-profile';

        if (typeof(localStorage) != 'undefined') {
            localStorage.setItem("activetab", clicked_group);
        }

        $('.group').hide();
        $(clicked_group).fadeIn();

        $('#toplevel_page_analytify-dashboard ul.wp-submenu li.current, #toplevel_page_analytify-dashboard ul.wp-submenu li a.current').removeClass('current');
        $(this).addClass('current');
        $(this).parent().addClass('current');

    });

    $('.analytify_nav_tab_wrapper a[href*="#"]').on('click', function(evt) {

        $('.analytify_nav_tab_wrapper a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active').blur();
        var clicked_group = $(this).attr('href');
        if (typeof(localStorage) != 'undefined') {
            localStorage.setItem("activetab", $(this).attr('href'));
        }
        $('.group').hide();
        $(clicked_group).fadeIn();

        // remove '.current' from admin submenu
        $('#toplevel_page_analytify-dashboard ul.wp-submenu li.current, #toplevel_page_analytify-dashboard ul.wp-submenu li a.current').removeClass('current');

        // change '.current' class to admin submenu based on tab
        if( '#wp-analytify-license' === clicked_group ) {

            // set 'current' admin submenu for license tab

            let admin_submenu_license = $('li#toplevel_page_analytify-dashboard .wp-submenu').find('a[href$="analytify-settings#wp-analytify-license"]');
            admin_submenu_license.addClass('current');
            admin_submenu_license.parent().addClass('current');

        } else if ( '#wp-analytify-help' === clicked_group ) {
            
            // set 'current' admin submenu for help tab            

            let admin_submenu_help = $('li#toplevel_page_analytify-dashboard .wp-submenu').find('a[href$="analytify-settings#wp-analytify-help"]');
            admin_submenu_help.addClass('current');
            admin_submenu_help.parent().addClass('current');

        } else {
            
            // set 'current' admin submenu to settings

            let admin_submenu_setting = $('li#toplevel_page_analytify-dashboard .wp-submenu').find('a[href$="analytify-settings"]');
            admin_submenu_setting.addClass('current');
            admin_submenu_setting.parent().addClass('current');

        }

        // load diagnostic debug log only when help tab is active
        if ($('.nav-tab-active').attr('href') === '#wp-analytify-help') refresh_debug_log();
        evt.preventDefault();

    });

    // move to authenticate tab.
    $('.analytify_need_authenticate_first a').on('click', function(event) {
        event.preventDefault();
        $('.analytify_nav_tab_wrapper a').removeClass('nav-tab-active');
        $('#wp-analytify-authentication-tab').addClass('nav-tab-active');
        $('.group').hide();
        $('#wp-analytify-authentication').fadeIn();
    });

    //Email Logo
    $('.analytify_email_clear').on('click', function(event) {
        event.preventDefault();
        $('.wpsa-image-id').val('');
        $('.wpsa-image-preview img').attr('src', '');
    });
    $('.wpsa-browse').on('click', function(event) {
        event.preventDefault();
        var self = $(this);
        // Create the media frame.
        var file_frame = wp.media.frames.file_frame = wp.media({
                title: self.data('uploader_title'),
                button: {
                    text: self.data('uploader_button_text'),
                },
                multiple: false
            })
            .on('select', function() {
                attachment = file_frame.state().get('selection').first().toJSON();
                self.prev('.wpsa-url').val(attachment.url).change();
            })
            // Finally, open the modal
            .open();
    });

    $('.wpsa-image-browse').on('click', function(event) {
        event.preventDefault();
        var self = $(this);
        // Create the media frame.
        var file_frame = wp.media.frames.file_frame = wp.media({
                title: self.data('uploader_title'),
                button: {
                    text: self.data('uploader_button_text'),
                },
                multiple: false,
                library: { type: 'image' }
            })
            .on('select', function() {
                attachment = file_frame.state().get('selection').first().toJSON();
                var url;
                if (attachment.sizes && attachment.sizes.thumbnail)
                    url = attachment.sizes.thumbnail.url;
                else
                    url = attachment.url;
                self.parent().children('.wpsa-image-id').val(attachment.id).change();
                self.parent().children('.wpsa-image-preview').children('img').attr('src', url);
            })
            // Finally, open the modal
            .open();
    });

    // Tooltip
    $('.setting-more-info').tooltip({
        content: function() {
            return $(this).prop('title');
        },
        show: null,
        close: function(event, ui) {
            ui.tooltip.hover(
                function() {
                    $(this).stop(true).fadeTo(400, 1);
                },
                function() {
                    $(this).fadeOut("400", function() {
                        $(this).remove();
                    });
                });
        },
        position: {
            my: 'left center',
            at: 'right+25 center',
            track: false,
            using: function(position, feedback) {
                jQuery(this).css(position);
                jQuery("<div>")
                    .addClass("arrow-tooltip")
                    .addClass(feedback.vertical)
                    .addClass(feedback.horizontal)
                    .appendTo(this);
            }
        }
    });

    /*
     * Accordion script
     */
    // hide all .tracking-accordions-content
    $('.tracking-accordions-wrapper .tracking-accordions-content').hide();

    // display the first .tracking-accordions-content and add .show
    // add .show to the .tracking-accordions-heading
    $('.tracking-accordions-wrapper ul li:first-child .tracking-accordions-heading').addClass('show');
    $('.tracking-accordions-wrapper ul li:first-child .tracking-accordions-content').addClass('show').show();

    // main accordion function
    $('.tracking-accordions-wrapper .tracking-accordions-heading').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        if ($this.next().hasClass('show')) {
            $this.removeClass('show');
            $this.next().removeClass('show');
            $this.next().slideUp(350);
        } else {
            $this.parent().parent().find('li .tracking-accordions-heading').removeClass('show');
            $this.parent().parent().find('li .tracking-accordions-content').removeClass('show').slideUp(350);
            $this.toggleClass('show');
            $this.next().toggleClass('show');
            $this.next().slideToggle(350);
        }
    });

    // Trigger click on events tab
    $('.wp-analytify-events-tab-item').on('click', function(e) {
        let elementId = $(this).attr('id');
        let containerElement = $('.tracking-accordion[data-id="' + elementId + '"] .tracking-accordions-heading');

        if (!$('#wp-analytify-tracking-tab').hasClass('nav-tab-active')) {

            $(this).parent().parent().parent().find('.analytify_nav_tab').trigger('click');

        }

        if (!containerElement.hasClass('show')) {

            containerElement.parent().parent().find('li .tracking-accordions-heading').removeClass('show');
            containerElement.parent().parent().find('li .tracking-accordions-content').removeClass('show').slideUp(350);
            containerElement.toggleClass('show');
            containerElement.next().toggleClass('show');
            containerElement.next().slideToggle(350);

        }
    })

});

// log data container
var analytify_debug_log = false;

// updates the debug log when the user switches to the help tab
function refresh_debug_log() {

    if( analytify_debug_log ){ return; }

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'text',
        cache: false,
        data: {
            action: 'analytify_fetch_log',
            nonce: wpanalytify_data.nonces.fetch_log
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //alert( wpanalytify_strings.update_log_problem );
        },
        success: function(data) {
            analytify_debug_log = data;
            jQuery('.debug-log-textarea').val(data);
        }
    });

}

function authenticationPopupWindow(url, title) {
    var left = (screen.width / 2) - (700 / 2);
    var top = (screen.height / 2) - (500 / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width= 700, height= 500, top=' + top + ', left=' + left);
}

jQuery(window).resize(function($) {

    jQuery(".grids_auto_size").each(function() {
        jQuery(this).css("min-height", height);
    });

    jQuery(".keywordscont").each(function() {
        jQuery(this).css("min-height", height2);
    });

    jQuery(".stats").each(function() {
        jQuery(this).css("min-height", height3);
    });

});