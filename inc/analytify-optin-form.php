<style media="screen">
#wpwrap {
background-color: #fdfdfd
}
#wpcontent {
padding: 0!important
}
#analytify-logo-wrapper {
padding: 10px 0;
width: 100%;
border-bottom: solid 1px #d5d5d5
}
#analytify-logo-wrapper-inner {
max-width: 600px;
width: 100%;
margin: auto
}
#analytify-splash {
max-width: 900px;
width: 100%;
margin: auto;
background-color: #fdfdfd;
text-align: center
}
#analytify-splash h1 {
margin-top: 40px;
margin-bottom: 25px;
font-size: 26px
}
#analytify-splash-main {
padding-bottom: 0
}
#analytify-splash-permissions-toggle {
font-size: 12px
}
#analytify-splash-permissions-dropdown h3 {
font-size: 16px;
margin-bottom: 5px
}
#analytify-splash-permissions-dropdown p {
margin-top: 0;
font-size: 14px;
margin-bottom: 20px
}
#analytify-splash-main-text {
font-size: 16px;
padding: 0;
margin: 0
}
#analytify-splash-footer {
width: 100%;
padding: 15px 0;
border-top: 1px solid #d5d5d5;
border-bottom: 1px solid #d5d5d5;
font-size: 10px;
text-align: center;
margin-top: 238px
}
#analytify-ga-optout-btn {
background: none!important;
border: none;
padding: 0!important;
font: inherit;
color: #7f7f7f;
border-bottom: 1px solid #7f7f7f;
cursor: pointer;
margin-bottom: 40px;
font-size: 14px
}
#analytify-ga-submit-btn {
height: 40px;
margin: 30px;
margin-bottom: 15px;
font-size: 16px
}
.analytify-splash-box {
width: 100%;
max-width: 600px;
background-color: #fff;
border: solid 1px #d5d5d5;
margin: auto;
margin-bottom: 20px;
text-align: center;
padding: 15px
}

</style>
<?php


$user = wp_get_current_user();
$name = empty( $user->user_firstname ) ? '' : $user->user_firstname;
$email = $user->user_email;
$site_link = '<a href="' . get_site_url() . '">'. get_site_url() . '</a>';
$website = get_site_url();

echo '<form method="post" action="' . admin_url( 'admin.php?page=analytify-settings' ) . '">';
  echo '<div id="analytify-logo-wrapper">';
    echo '<div id="analytify-logo-wrapper-inner">';
      echo '<img id="analytify-logo-text" src="' . plugins_url( 'assets/images/notice-logo.svg', dirname( __FILE__ ) )  . '">';
    echo '</div>';
  echo '</div>';

  echo "<input type='hidden' name='email' value='$email'>";

  echo '<div id="analytify-splash">';
    echo '<h1>' . __( 'Welcome to Google Analytics by Analytify', 'wp-analytify' ) . '</h1>';

    echo '<div id="analytify-splash-main" class="analytify-splash-box">';
      echo '<p id="analytify-splash-main-text">' .  sprintf ( __( 'In order to enjoy all our features and functionality,%4$s Google Analytics by Analytify needs to connect %1$s your user, %2$s at %3$s, to %4$s<strong>api.wpbrigade.com</strong>.', 'wp-analytify' ), '<br>', '<strong>' . $name . '</strong>', '<strong>' . $website . '</strong>', '<br>' ) . '</p>';
      echo "<button type='submit' id='analytify-ga-submit-btn' class='analytify-ga-button button button-primary' name='analytify-submit-optin' >" . __( 'Connect Google Analytics by Analytify', 'analytify-ga') . "</button><br>";
      echo "<button type='submit' id='analytify-ga-optout-btn' name='analytify-submit-optout' >" . __( 'Skip This Step', 'analytify-ga') . "</button>";
    echo '</div>';

    echo '<div id="analytify-splash-permissions" class="analytify-splash-box">';
      echo '<a id="analytify-splash-permissions-toggle" href="#" >' . __( 'What permission is being granted?', 'wp-analytify' ) . '</a>';
      echo '<div id="analytify-splash-permissions-dropdown" style="display: none;">';
        echo '<h3>' .  __( 'Your Website Info', 'wp-analytify' ) . '</h3>';
        echo '<p>' .  __( 'Your URL, WordPress version, plugins & themes. This data lets us make sure this plugin always stays compatible with the most popular plugins and themes.', 'wp-analytify' ) . '</p>';

        echo '<h3>' .  __( 'Your Info', 'wp-analytify' ) . '</h3>';
        echo '<p>' .  __( 'Your name and email.', 'wp-analytify' ) . '</p>';

        echo '<h3>' .  __( 'Plugin Usage', 'wp-analytify' ) . '</h3>';
        echo '<p>' .  __( "How you use this plugin's features and settings. This is limited to usage data. It does not include any of your sensitive Google Analytics data, such as traffic. This data helps us learn which features are most popular, so we can improve the plugin further.", 'wp-analytify' ) . '</p>';
      echo '</div>';
    echo '</div>';


  echo '</div>';

echo '</form>';

echo '<div id="analytify-splash-footer">';
  echo '<a target="_blank" href="#">' . _x( 'Terms', 'as in terms and conditions', 'wp-analytify' ) . '</a> | <a target="_blank" href="#">' . _x( 'Privacy', 'as in privacy policy', 'wp-analytify' ) . '</a>';
echo '</div>';

?>

<script type="text/javascript">
jQuery(document).ready(function(s) {
  var o = parseInt(s("#analytify-splash-footer").css("margin-top"));
  s("#analytify-splash-permissions-toggle").click(function(a) {
      a.preventDefault(), s("#analytify-splash-permissions-dropdown").toggle(), 1 == s("#analytify-splash-permissions-dropdown:visible").length ? s("#analytify-splash-footer").css("margin-top", o - 208 + "px") : s("#analytify-splash-footer").css("margin-top", o + "px")
  })
});

</script>
