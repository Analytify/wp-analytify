<?php
/**
 * Addons Promo View
 *
 * @package WP_Analytify
 */

// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Global variable naming is intentional
$WP_ANALYTIFY = isset( $GLOBALS['WP_ANALYTIFY'] ) ? $GLOBALS['WP_ANALYTIFY'] : null;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
$addon = isset( $_GET['addon'] ) ? sanitize_text_field( wp_unslash( $_GET['addon'] ) ) : '';
?>

<div class="wpanalytify analytify-dashboard-nav">
	<div class="wpb_plugin_wraper">
		<div class="wpb_plugin_header_wraper">
			<div class="graph"></div>
			<div class="wpb_plugin_header">
				<div class="wpb_plugin_header_title"></div>
				<div class="wpb_plugin_header_info">
					<a href="<?php echo esc_url( 'https://analytify.io/changelog/' ); ?>" target="_blank" rel="noopener noreferrer" class="btn"><?php echo esc_html__( 'View Changelog', 'wp-analytify' ); ?></a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL . '/assets/img/logo.svg' ); ?>" alt="<?php echo esc_attr__( 'Analytify', 'wp-analytify' ); ?>">
				</div>
			</div>
		</div>

		<div class="analytify-dashboard-body-container">
			<div class="wpb_plugin_body_wraper">
				<div class="wpb_plugin_body">
					<div class="wpa-tab-wrapper"> 
					<?php
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Global variable naming is intentional
					if ( is_object( $WP_ANALYTIFY ) && method_exists( $WP_ANALYTIFY, 'dashboard_navigation' ) ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Global variable naming is intentional
						$WP_ANALYTIFY->dashboard_navigation();
					}
					?>
					</div>
					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
						<div class="analytify_wraper">

							<?php if ( 'wp-analytify-forms' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Forms Conversions Dashboard', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Would you like to track your WordPress website forms? Analytify Forms Tracking addon helps you to track the number of impressions and forms conversions/submissions. This Addon works with any popular WordPress form plugins like Gravity forms, Ninja Forms, Formidable forms and more, even including Custom WordPress forms.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Custom Forms Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Track Gravity Forms', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Track Contact Form 7', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WPForms Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Track Formidable Forms', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Track submissions, impressions and conversions.', 'wp-analytify' ); ?></li>
												</ul>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Forms+Tracking' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + Forms Tracking bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'events-tracking' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Events Tracking', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Our Events tracking feature helps you to setup and track custom events on your WordPress website. Custom events will help you track and measure the performance of the most important Outbound links like Affiliate links. Setting up custom events is tricky for the beginners. But with the Analytify\'s Events Tracking you can easily acheive this on your WordPress websites.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Affiliate Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links & Clicks Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Enhanced Link Attribution', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Anchor Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'File downloads Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Track outbound links', 'wp-analytify' ); ?></li>
												</ul>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Events+Tracking' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-campaigns' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock UTM Campaigns Tracking', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php printf( /* translators: 1: Coupon code */ esc_html__( 'Use the coupon code "%1$s" to get 60%% discount.', 'wp-analytify' ), 'BFCM60' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Campaigns' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + Campaigns bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-goals' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Goals Tracking in WordPress', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php printf( /* translators: 1: Coupon code */ esc_html__( 'Use the coupon code "%1$s" to get 60%% discount.', 'wp-analytify' ), 'BFCM60' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Goals' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + Goals bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-woocommerce' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Google Analytics Tracking for WooCommerce', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php printf( /* translators: 1: Coupon code */ esc_html__( 'Use the coupon code "%1$s" to get 60%% discount.', 'wp-analytify' ), 'BFCM60' ); ?></p>
												<?php
												echo wp_kses_post(
													activate_module_anchor(
														'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=WooCommerce',
														esc_html__( 'Explore Analytify Pro + WooCommerce Tracking bundle', 'wp-analytify' )
													)
												);
												?>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-edd' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Google Analytics Tracking for Easy Digital Downloads (EDD)', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php printf( /* translators: 1: Coupon code */ esc_html__( 'Use the coupon code "%1$s" to get 60%% discount.', 'wp-analytify' ), 'BFCM60' ); ?></p>
												<?php
												echo wp_kses_post(
													activate_module_anchor(
														'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=EDD',
														esc_html__( 'Explore Analytify Pro + EDD Tracking bundle', 'wp-analytify' )
													)
												);
												?>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-authors' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Authors Tracking in WordPress', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Track your website/blog author’s content performance by analyzing the insights of their published content with Authors Tracking Addon. You can easily view the following author’s analytics.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Total sessions on published content', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Visitors', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Average Time Spent', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Bounce Rate', 'wp-analytify' ); ?></li>
												</ul>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Authors' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + Authors Tracking bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'detail-demographic' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/promo-geographics-dashboard.jpg" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Demographics Dashboard', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Demographics' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'search-terms' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/promo-search-terms-dashboard.jpg" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Search Terms Tracking', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Search+Terms' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-pmpro' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Google Analytics Tracking for Paid Memberships Pro', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Track your membership site performance with detailed analytics for Paid Memberships Pro. Monitor membership conversions, revenue, and member engagement.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Membership Checkout Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Membership Level Changes', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Membership Expiration Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Membership Cancellation Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Payment Success/Failure Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Revenue and Conversion Analytics', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Member Engagement Metrics', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=PMPro' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + PMPro Tracking bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'search-console-report' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/promo-search-console-dashboard.jpg" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Search Console Report Tracking', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Demographics Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Terms Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Search+Console' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'custom-dimensions' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Google Custom Dimensions Tracking', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Custom Dimensions helps you to track Categories, Tags, Post Types, and logged in activities within Google Analytics. Remember, you have to setup Custom Dimensions in Google Analytics as well.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Custom Post Type Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Category Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Tags Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'User-ID Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Track logged in activity', 'wp-analytify' ); ?></li>
												</ul>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Dimensions' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-learndash' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Google Analytics Tracking for LearnDash', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Track your LearnDash courses, lessons, quizzes, and student progress with detailed analytics and insights to improve your online learning platform.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Course Engagement Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Student Progress Analytics', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Quiz Performance Insights', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Lesson Completion Rates', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Revenue Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Enrollment Analytics', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Video Engagement Stats', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Course Funnel Analysis', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=LearnDash' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + LearnDash Tracking bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'wp-analytify-lifterlms' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/analytify_compare.gif" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Google Analytics Tracking for LifterLMS', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Track your LifterLMS course performance with comprehensive analytics. Monitor course views, enrollments, purchases, and revenue to optimize your online learning business.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Course Views & Engagement Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Lesson Completion Analytics', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Enrollment & Purchase Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Revenue & Transaction Reports', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Top Courses Performance', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Traffic Sources Analysis', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'User Journey Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Course Funnel Insights', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Custom Dimensions Support', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Instructor Performance Metrics', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=LifterLMS' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore Analytify Pro + LifterLMS Tracking bundle', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'detail-realtime' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/promo-realtime-dashboard.jpg" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Real-Time Dashboard', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google Optimize', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Real+Time' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'page-speed' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/page-speed.png" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock PageSpeed Insights Dashboard', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google Optimize', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=PageSpeed' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php elseif ( 'video-tracking' === $addon ) : ?>

								<div class="analytify-email-dashboard-wrapper">
									<img src="<?php echo esc_url( ANALYTIFY_PLUGIN_URL ); ?>/assets/img/video-tracking.png" alt="<?php esc_attr_e( 'Upgrade to Pro', 'wp-analytify' ); ?>" style="width:100%">
									<div class="analytify-email-promo-contianer">
										<div class="analytify-email-premium-overlay">
											<div class="analytify-email-premium-popup">
												<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock Video Tracking Dashboard', 'wp-analytify' ); ?></h3>
												<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Upgrading to Analytify Premium plan gives access to a lot of amazing features.', 'wp-analytify' ); ?></p>
												<ul class="analytify-promo-popup-list">
													<li><?php esc_html_e( 'Real-Time Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'PageSpeed Insights Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Search Console Dashboard', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google AMP', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Events Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Links Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Affiliate Links', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Form Conversions', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Authors Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Google Optimize', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'UTM Campaigns Tracking', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'Weekly & Monthly Emails', 'wp-analytify' ); ?></li>
													<li><?php esc_html_e( 'WooCommerce & EDD Tracking', 'wp-analytify' ); ?></li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;"><?php esc_html_e( 'Use the coupon code "BFCM60" to get 60% discount.', 'wp-analytify' ); ?></p>
												<a href="<?php echo esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_campaign=pro-upgrade&utm_content=Video+Tracking' ); ?>" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to Analytify Pro', 'wp-analytify' ); ?></a>
											</div>
										</div>
									</div>
								</div>

							<?php endif; ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
