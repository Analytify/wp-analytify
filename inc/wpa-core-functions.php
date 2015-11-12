<?php

/**
 * @since 1.2.1
 *
 */
function wpa_check_profile_selection( $type , $message = '' ) {

    if(! get_option( "pt_webprofile" )) {

        if($message == '')
            echo '<div class="error"><p>' . __( $type . ' Dashboard can\'t be loaded until your select your website profile <a href="' . menu_page_url ( 'analytify-settings', false ) . '&tab=profile">here</a> ', 'wp-analytify' ) . '</p></div>';
        else
            echo $message;

        return true;
    }else{
        return false;
    }

}