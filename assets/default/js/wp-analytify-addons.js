(function($) {

    $(document).ready(function() {

        //console.log(analytify_addons.ajaxurl);
        //console.log(analytify_addons.nonce);

        $(document).on('click', ".analytify-module-state", function(e) {
            e.preventDefault();

            var thisElement = $(this);
            var thisContainer = thisElement.parent().parent();
            var moduleSlug = $(this).attr('data-slug');
            var setState = $(this).attr('data-set-state');
            var internalModule = $(this).attr('data-internal-module');
            //console.log(ajaxurl);
            $.ajax({
                    url: analytify_addons.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'set_module_state',
                        nonce: analytify_addons.nonce,
                        module_slug: moduleSlug,
                        set_state: setState,
                        internal_module: internalModule
                    },
                    beforeSend: function(res) {
                        thisContainer.find('.wp-analytify-addon-enable').show();
                    },
                    error: function(res) {
                        // console.log(res);
                        thisContainer.find('.wp-analytify-addon-enable').hide();
                        thisContainer.find('.wp-analytify-addon-wrong').show();
                    },
                    success: function(res) {
                        if ('failed' === res) {
                            thisContainer.find('.wp-analytify-addon-enable').hide();
                            thisContainer.find('.wp-analytify-addon-wrong').show();
                        } else {
                            thisContainer.find('.wp-analytify-addon-enable').hide();

                            if ('active' === setState) {
                                thisContainer.find('.wp-analytify-addon-install').show();
                            } else {
                                thisContainer.find('.wp-analytify-addon-uninstall').show();
                            }
                        }
                    }
                })
                .done(function(res) {
                    // console.log(res);
                    if ('active' === setState) {
                        thisElement.parent().html('<button type="button" class="button-primary analytify-module-state analytify-deactivate-module" data-internal-module="' + internalModule + '" data-slug="' + moduleSlug + '" data-set-state="deactive">Deactivate add-on</button>');
                    } else {
                        thisElement.parent().html('<button type="button" class="button-primary analytify-module-state analytify-active-module" data-internal-module="' + internalModule + '" data-slug="' + moduleSlug + '" data-set-state="active">Activate add-on</button>');
                    }

                    setTimeout(function() {
                        thisContainer.find('.wp-analytify-addon-install').hide();
                        thisContainer.find('.wp-analytify-addon-uninstall').hide();
                    }, 1800);
                });
        });

    });

})(jQuery)