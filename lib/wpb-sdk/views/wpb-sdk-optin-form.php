<?php
/**
 * Shared WPBrigade SDK opt-in splash (default for bundled products).
 *
 * Expects variables set by wpb_sdk_render_optin_form() before include.
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpb_sdk_optin_user = wp_get_current_user();
$wpb_sdk_optin_name = empty( $wpb_sdk_optin_user->user_firstname )
	? $wpb_sdk_optin_user->display_name
	: $wpb_sdk_optin_user->user_firstname;

?>
<style>
	#wpwrap {
		background-color: #f6f9ff;
	}
	#wpcontent {
		padding: 0 !important;
	}
	#wpbody {
		padding-right: 0;
	}
	#wpb-sdk-optin-splash {
		width: calc(100% - 64px);
		max-width: 680px;
		margin: 40px auto 60px;
		text-align: center;
	}
	#wpb-sdk-optin-splash .wpb-sdk-optin-logo-wrap {
		margin-bottom: 0;
	}
	#wpb-sdk-optin-logo {
		max-width: 90px;
		height: auto;
		vertical-align: middle;
	}
	#wpb-sdk-optin-title {
		margin: 0 0 15px;
		font-size: 26px;
		line-height: 32px;
		color: #000;
		font-weight: 600;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	#wpb-sdk-optin-splash-main {
		background-color: #fff;
		border: 2px solid #999797;
		border-radius: 8px;
		max-width: 600px;
		min-height: 320px;
		margin: 0 auto;
		padding: 30px;
		display: flex;
		align-items: center;
		text-align: left;
		box-sizing: border-box;
	}
	#wpb-sdk-optin-splash-main-text {
		font-size: 16px;
		line-height: 1.6;
		padding: 0;
		margin: 0 0 20px;
		color: #000;
	}
	#wpb-sdk-optin-splash-main-text strong {
		font-weight: 700;
	}
	#wpb-sdk-optin-splash-main-text em {
		font-style: italic;
	}
	#wpb-sdk-optin-submit-btn {
		border: 0;
		padding: 15px 20px;
		background-color: #1441d8;
		color: #fff;
		font-size: 17px;
		line-height: 24px;
		font-weight: 500;
		border-radius: 5px;
		cursor: pointer;
		margin-bottom: 20px;
		min-height: auto;
		-webkit-appearance: none;
		display: inline-block;
	}
	#wpb-sdk-optin-submit-btn:hover,
	#wpb-sdk-optin-submit-btn:focus {
		background-color: #5272e1;
		color: #fff;
		box-shadow: none;
	}
	#wpb-sdk-optin-submit-btn:after {
		content: '\279C';
		margin-left: 6px;
	}
	#wpb-sdk-optin-skip-btn {
		background: none !important;
		border: none;
		padding: 0 !important;
		font-size: 14px;
		cursor: pointer;
		margin-bottom: 20px;
		text-decoration: underline;
		text-decoration-style: dashed;
		text-underline-position: under;
		color: rgb(92 118 151 / 80%);
	}
	#wpb-sdk-optin-skip-btn:hover {
		text-decoration: none;
	}
	.wpb-sdk-optin-loader,
	.wpb-sdk-skip-loader {
		vertical-align: middle;
		margin-left: 8px;
	}
	@media only screen and (max-width: 767px) {
		#wpb-sdk-optin-splash {
			width: auto;
			margin-left: 20px;
			margin-right: 20px;
		}
		#wpb-sdk-optin-splash-main {
			padding: 20px;
		}
		#wpb-sdk-optin-submit-btn {
			font-size: 15px;
			line-height: 21px;
		}
		#wpb-sdk-optin-title {
			flex-direction: column;
			font-size: 22px;
		}
	}
	@media only screen and (max-width: 580px) {
		#wpb-sdk-optin-title {
			font-size: 18px;
		}
		#wpb-sdk-optin-logo {
			max-width: 70px;
		}
	}
</style>
<div class="wrap">
	<div id="wpb-sdk-optin-splash">
		<?php if ( ! empty( $wpb_sdk_logo_url ) ) : ?>
			<h1 class="wpb-sdk-optin-logo-wrap">
				<img
					id="wpb-sdk-optin-logo"
					src="<?php echo esc_url( $wpb_sdk_logo_url ); ?>"
					alt="<?php echo esc_attr( $wpb_sdk_product_name ); ?>"
				/>
			</h1>
		<?php endif; ?>
		<h1 id="wpb-sdk-optin-title"><?php echo esc_html( $wpb_sdk_product_name ); ?></h1>
		<input type="hidden" id="wpb-sdk-optin-page-nonce" value="<?php echo esc_attr( $wpb_sdk_optin_nonce ); ?>" />
		<div id="wpb-sdk-optin-splash-main">
			<?php if ( 'yes' !== get_option( $wpb_sdk_optin_option ) ) : ?>
				<div class="wpb-sdk-optin-step">
					<p id="wpb-sdk-optin-splash-main-text">
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: 1: user name, 2: product name (full plugin name), 3: product name in Improve line */
								__(
									'Hey <strong>%1$s</strong>,<br />If you opt-in some data about your installation of <strong>%2$s</strong> will be sent to WPBrigade.com (This doesn\'t include stats) and You will receive new feature updates, security notifications etc <em>No Spam, I promise.</em><br /><br />Help us <strong>Improve %3$s</strong><br /><br />',
									'wpbrigade-sdk'
								),
								esc_html( $wpb_sdk_optin_name ),
								esc_html( $wpb_sdk_product_name ),
								esc_html( $wpb_sdk_product_name )
							)
						);
						?>
					</p>
					<button type="button" id="wpb-sdk-optin-submit-btn" class="button button-primary">
						<?php esc_html_e( 'Allow and Continue', 'wpbrigade-sdk' ); ?>
					</button>
					<img class="wpb-sdk-optin-loader" style="display:none" src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" alt="" />
					<br />
					<button type="button" id="wpb-sdk-optin-skip-btn">
						<?php esc_html_e( 'Skip This Step', 'wpbrigade-sdk' ); ?>
					</button>
					<img class="wpb-sdk-skip-loader" style="display:none" src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" alt="" />
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<script>
	jQuery(function($) {
		var nonce = $('#wpb-sdk-optin-page-nonce').val();
		var redirect = <?php echo wp_json_encode( $wpb_sdk_redirect_url ); ?>;
		var ajaxurl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var ajaxPrefix = <?php echo wp_json_encode( $wpb_sdk_ajax_prefix ); ?>;

		$('#wpb-sdk-optin-submit-btn').on('click', function(e) {
			e.preventDefault();
			$(this).prop('disabled', true);
			$('.wpb-sdk-optin-loader').show();
			$.post(ajaxurl, {
				action: ajaxPrefix + '_optin_yes',
				optin_yes_nonce: nonce
			}).always(function() {
				window.location.href = redirect;
			});
		});

		$('#wpb-sdk-optin-skip-btn').on('click', function(e) {
			e.preventDefault();
			$(this).prop('disabled', true);
			$('.wpb-sdk-skip-loader').show();
			$.post(ajaxurl, {
				action: ajaxPrefix + '_optin_skip',
				optin_skip_nonce: nonce
			}).always(function() {
				window.location.href = redirect;
			});
		});
	});
</script>
