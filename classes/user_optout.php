<?php

/**
 * User Optout.
 */
class Analytify_User_Optout {

	function __construct() {

		add_filter( 'analytiy_user_optout_message', array( $this, 'user_optout_message' ) );
		add_filter( 'analytiy_user_optin_message', array( $this, 'user_optin_message' ) );
		add_shortcode( 'analytify_user_optout', array( $this, 'user_optout_shortcode' ) );
		add_shortcode( 'analytify_user_optin', array( $this, 'user_optin_shortcode' ) );
	}

	/**
	 * Add [analytify_user_optout] Shortcode.
	 *
	 * @since 2.1.16
	 */
	function user_optout_shortcode( $atts, $content = '' ) {

		$UA_CODE = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
		$is_authenticate_in = get_option( 'pa_google_token' );

		if ( ! $UA_CODE || ! $is_authenticate_in ) {
			if ( current_user_can( 'manage_options' ) ) {
				return ' "Analytify Profile is not selected" ';
			} else {
				return '<!-- Analytify Profile is not selected. -->';
			}
		}

		ob_start();
		?>
		<script>
			var analytify_optout_string =  'ga-disable-' + '<?php echo $UA_CODE; ?>';
			if ( document.cookie.indexOf( analytify_optout_string + '=true' ) > -1 ) {
				window[ analytify_optout_string ] = true;
			}

			function analytify_analytics_optout() {
				var exp_date = new Date;
				exp_date.setFullYear(exp_date.getFullYear() + 10);

				document.cookie = analytify_optout_string + '=true; expires=' + exp_date.toGMTString() + '; path=/';
				window[ analytify_optout_string ] = true;
				<?php echo apply_filters( 'analytiy_user_optout_message', '' ); ?>
			}
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( '' == $content ) {
			$content = __( 'Click here to opt-out.', 'wp-analytify' );
		}

		$output .= '<a class="analytify-opt-out" href="javascript:analytify_analytics_optout();">' . $content . '</a>';

		return $output;
	}

	/**
	 * Alert Message
	 *
	 * @since 2.1.16
	 */
	function user_optout_message() {

		return "alert('" . __( 'Thanks. Google Analytics data collection is disabled for you.', 'wp-analytify' ) . "')";
	}

	/**
	 * Add [analytify_user_optin] Shortcode.
	 *
	 * @since 2.1.22
	 */
	function user_optin_shortcode( $atts, $content = ''  ) {
		$UA_CODE = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
		$is_authenticate_in = get_option( 'pa_google_token' );

		if ( ! $UA_CODE || ! $is_authenticate_in ) {
			if ( current_user_can( 'manage_options' ) ) {
				return ' "Analytify Profile is not selected" ';
			} else {
				return '<!-- Analytify Profile is not selected. -->';
			}
		}

		ob_start();
		?>
		<script>
		var analytify_optout_string =  'ga-disable-' + '<?php echo $UA_CODE; ?>';


		function analytify_analytics_optin() {

			var exp_date = new Date;
			exp_date.setFullYear(exp_date.getFullYear() - 30);

			document.cookie = analytify_optout_string + '=true; expires=' + exp_date.toGMTString() + '; path=/';
			window[ analytify_optout_string ] = true;


			<?php echo apply_filters( 'analytiy_user_optin_message', '' ); ?>
		}
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( '' == $content ) {
			$content = __( 'Click here to opt-in.', 'wp-analytify' );
		}

		$output .= '<a class="analytify-opt-in" href="javascript:analytify_analytics_optin();">' . $content . '</a>';

		return $output;
	}

	/**
	 * Alert Message for Optin.
	 *
	 * @since 2.1.22
	 */
	function user_optin_message() {
		return "alert('" . __( 'Thanks. Google Analytics data collection is enabled for you.', 'wp-analytify' ) . "')";
	}

}

new Analytify_User_Optout();

