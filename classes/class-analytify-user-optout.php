<?php
/**
 * Analytify User Optout Class
 *
 * This class handles user opt-out functionality for the Analytify plugin,
 * providing shortcodes and methods for users to opt out of analytics tracking.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

/**
 * User Optout.
 */
class Analytify_User_Optout {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_filter( 'analytiy_user_optout_message', array( $this, 'user_optout_message' ) );
		add_filter( 'analytiy_user_optin_message', array( $this, 'user_optin_message' ) );
		add_shortcode( 'analytify_user_optout', array( $this, 'user_optout_shortcode' ) );
		add_shortcode( 'analytify_user_optin', array( $this, 'user_optin_shortcode' ) );
	}

	/**
	 * Add [analytify_user_optout] Shortcode.
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @param mixed $content Shortcode content.
	 * @return string
	 * @since 2.1.16
	 */
	public function user_optout_shortcode( $atts, $content = '' ) {

		$ua_code            = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
		$is_authenticate_in = get_option( 'pa_google_token' );

		if ( ! $ua_code || ! $is_authenticate_in ) {
			if ( current_user_can( 'manage_options' ) ) {
				return ' "Analytify Profile is not selected" ';
			} else {
				return '<!-- Analytify Profile is not selected. -->';
			}
		}

		ob_start();
		?>
		<script>
			var analytify_optout_string =  'ga-disable-' + '<?php echo esc_js( $ua_code ); ?>';
			if ( document.cookie.indexOf( analytify_optout_string + '=true' ) > -1 ) {
				window[ analytify_optout_string ] = true;
			}

			function analytify_analytics_optout() {
				var exp_date = new Date;
				exp_date.setFullYear(exp_date.getFullYear() + 10);

				document.cookie = analytify_optout_string + '=true; expires=' + exp_date.toGMTString() + '; path=/';
				window[ analytify_optout_string ] = true;
				<?php echo esc_js( apply_filters( 'analytiy_user_optout_message', '' ) ); ?>
			}
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( '' === $content ) {
			$content = __( 'Click here to opt-out.', 'wp-analytify' );
		}

		$output .= '<a class="analytify-opt-out" href="javascript:analytify_analytics_optout();">' . $content . '</a>';

		return $output;
	}

	/**
	 * Alert message for user opt-out.
	 *
	 * @return string
	 * @since 2.1.16
	 */
	public function user_optout_message() {

		return "alert('" . __( 'Thanks. Google Analytics data collection is disabled for you.', 'wp-analytify' ) . "')";
	}

	/**
	 * Add [analytify_user_optin] Shortcode.
	 *
	 * @param mixed $atts Shortcode attributes.
	 * @param mixed $content Shortcode content.
	 * @return string
	 * @since 2.1.22
	 */
	public function user_optin_shortcode( $atts, $content = '' ) {
		$ua_code            = WP_ANALYTIFY_FUNCTIONS::get_UA_code();
		$is_authenticate_in = get_option( 'pa_google_token' );

		if ( ! $ua_code || ! $is_authenticate_in ) {
			if ( current_user_can( 'manage_options' ) ) {
				return ' "Analytify Profile is not selected" ';
			} else {
				return '<!-- Analytify Profile is not selected. -->';
			}
		}

		ob_start();
		?>
		<script>
		var analytify_optout_string =  'ga-disable-' + '<?php echo esc_js( $ua_code ); ?>';


		function analytify_analytics_optin() {

			var exp_date = new Date;
			exp_date.setFullYear(exp_date.getFullYear() - 30);

			document.cookie = analytify_optout_string + '=true; expires=' + exp_date.toGMTString() + '; path=/';
			window[ analytify_optout_string ] = true;


			<?php echo esc_js( apply_filters( 'analytiy_user_optin_message', '' ) ); ?>
		}
		</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( '' === $content ) {
			$content = __( 'Click here to opt-in.', 'wp-analytify' );
		}

		$output .= '<a class="analytify-opt-in" href="javascript:analytify_analytics_optin();">' . $content . '</a>';

		return $output;
	}

	/**
	 * Alert message for user opt-in.
	 *
	 * @return string
	 * @since 2.1.22
	 */
	public function user_optin_message() {
		return "alert('" . __( 'Thanks. Google Analytics data collection is enabled for you.', 'wp-analytify' ) . "')";
	}
}

new Analytify_User_Optout();
