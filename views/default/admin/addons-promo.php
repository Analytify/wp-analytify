<?php
$WP_ANALYTIFY	= $GLOBALS['WP_ANALYTIFY'];
$version		= defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION; 
$addon			= isset( $_GET['addon'] ) ? $_GET['addon'] : '';
$addon_state	= $WP_ANALYTIFY->analytify_module_state( $addon ); ?>

<div class="wpanalytify analytify-dashboard-nav">
	<div class="wpb_plugin_wraper">
		<div class="wpb_plugin_header_wraper">	
			<div class="graph"></div>
			<div class="wpb_plugin_header">
				<div class="wpb_plugin_header_title"></div>
				<div class="wpb_plugin_header_info">
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">Changelog - v<?php echo $version; ?></a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="<?php echo ANALYTIFY_PLUGIN_URL . '/assets/images/logo.svg'?>" alt="Analytify">
				</div>
			</div>
		</div>

		<div class="analytify-dashboard-body-container">
			<div class="wpb_plugin_body_wraper">
				<div class="wpb_plugin_body">
					<div class="wpa-tab-wrapper"> <?php echo $WP_ANALYTIFY->dashboard_navigation(); ?> </div>
					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
						<div class="analytify_wraper">
							
											<?php if ( 'wp-analytify-forms' === $addon ) { ?>
					

												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">

												<h3 class="analytify-promo-popup-heading">Unlock Forms Conversions Dashboard</h3>
												<p class="analytify-promo-popup-paragraph">Would you like to track your WordPress website forms? Analytify Forms Tracking addon helps you to track the number of impressions and forms conversions/submissions. This Addon works with any popular WordPress form plugins like Gravity forms, Ninja Forms, Formidable forms and more, even including Custom WordPress forms.</p>
												<ul class="analytify-promo-popup-list">
													<li>Custom Forms Tracking</li>
													<li>Track Gravity Forms</li>
													<li>Track Contact Form 7</li>
													<li>WPForms Tracking</li>
													<li>Track Formidable Forms</li>
													<li>Track submissions, impressions and conversions.</li>
												</ul>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Forms+Tracking+Dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Explore Analytify Pro + Forms Tracking bundle</a>

												</div>
													</div>
												</div>

											<?php } elseif ( 'events-tracking' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">


												<h3 class="analytify-promo-popup-heading">Unlock Events Tracking</h3>

												<p class="analytify-promo-popup-paragraph">Our Events tracking feature helps you to setup and track custom events on your WordPress website. Custom events will help you track and measure the performance of the most important Outbound links like Affiliate links. Setting up custom events is tricky for the beginners. But with the Analytify's Events Tracking you can easily acheive this on your WordPress websites.</p>
												<ul class="analytify-promo-popup-list">
													<li>Affiliate Tracking</li>
													<li>Links & Clicks Tracking</li>
													<li>Enhanced Link Attribution</li>
													<li>Anchor Tracking</li>
													<li>File downloads Tracking</li>
													<li>Track outbound links</li>
												</ul>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Events+Tracking+Dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Upgrade to Analytify Pro</a>

												</div>
													</div>
												</div>

											<?php } elseif ( 'wp-analytify-campaigns' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">

												<h3 class="analytify-promo-popup-heading">Unlock UTM Campaigns Tracking</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Campaigns-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Explore Analytify Pro + Campaigns bundle</a>
											
												</div>
													</div>
												</div>

											<?php } elseif ( 'wp-analytify-goals' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">

												<h3 class="analytify-promo-popup-heading">Unlock Goals Tracking in WordPress</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Goals-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Explore Analytify Pro + Goals bundle</a>
											
												</div>
													</div>
												</div>

											<?php } elseif ( 'wp-analytify-woocommerce' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">

												<h3 class="analytify-promo-popup-heading">Setup enhanced ecommerce Google Analytics Tracking for WooCommerce</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Woocommerce-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Explore Analytify Pro + WooCommerce Tracking bundle</a>
											
												</div>
													</div>
												</div>

											<?php } elseif ( 'wp-analytify-edd' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">


												<h3 class="analytify-promo-popup-heading">Setup enhanced ecommerce Google Analytics Tracking for EDD</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Edd-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Explore Analytify Pro + EDD Tracking bundle</a>
											
												</div>
													</div>
												</div>


											<?php } elseif ( 'wp-analytify-authors' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">


												<h3 class="analytify-promo-popup-heading">Unlock Authors Tracking in WordPress</h3>
												<p class="analytify-promo-popup-paragraph">Track your website/blog author’s content performance by analyzing the insights of their published content with Authors Tracking Addon. You can easily view the following author’s analytics.</p>
												<ul class="analytify-promo-popup-list">
													<li>Total sessions on published content</li>
													<li> Visitors</li>
													<li>Average Time Spent</li>
													<li>Bounce Rate</li>
												</ul>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Authors-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Explore Analytify Pro + Authors Tracking bundle</a>
											
												</div>
													</div>
												</div>

											<?php } elseif ( 'detail-demographic' === $addon ) { ?>


												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/promo-geographics-dashboard.jpg" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">

												<h3 class="analytify-promo-popup-heading">Unlock Demographics Dashboard</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Demographics-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Upgrade to Analytify Pro</a>
											
												</div>
													</div>
												</div>

											<?php } elseif ( 'search-terms' === $addon ) { ?>



												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/promo-search-terms-dashboard.jpg" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">


												<h3 class="analytify-promo-popup-heading">Unlock Search Terms Tracking</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Search-terms-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Upgrade to Analytify Pro</a>

												</div>
													</div>
												</div>


											<?php } elseif ( 'custom-dimensions' === $addon ) { ?>

												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/analytify_compare.gif" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">

												<h3 class="analytify-promo-popup-heading">Unlock Google Custom Dimensions Tracking</h3>
												<p class="analytify-promo-popup-paragraph">Custom Dimensions helps you to track Categories, Tags, Post Types, and logged in activities within Google Analytics. Remember, you have to setup Custom Dimensions in Google Analytics as well.</p>
												<ul class="analytify-promo-popup-list">
												<li>Custom Post Type Tracking</li>
												<li>Category Tracking</li>
												<li>Tags Tracking</li>
												<li>Authors Tracking</li>
												<li>User-ID Tracking</li>
												<li>Track logged in activity</li>
												</ul>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboardb&utm_content=Custom-dimensions&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Upgrade to Analytify Pro</a>

												</div>
													</div>
												</div>

											<?php } elseif ( 'detail-realtime' === $addon ) { ?>

												<div class="analytify-email-dashboard-wrapper">

													<img src="<?php echo ANALYTIFY_PLUGIN_URL; ?>/assets/images/promo-realtime-dashboard.jpg" alt="Upgrade to Pro" style="width:100%">

														<div class="analytify-email-promo-contianer">
															<div class="analytify-email-premium-overlay">
																<div class="analytify-email-premium-popup">



												<h3 class="analytify-promo-popup-heading">Unlock Real-Time Dashboard</h3>
												<p class="analytify-promo-popup-paragraph">Upgrading to Analytify Premium plan gives access to a lot of amazing features.</p>
												<ul class="analytify-promo-popup-list">
													<li>Real-Time Dashboard</li>
													<li>Demographics Dashboard</li>
													<li>Search Terms Dashboard</li>
													<li>Google AMP</li>
													<li>Events Tracking</li>
													<li>Links Tracking</li>
													<li>Affiliate Links</li>
													<li>Form Conversions</li>
													<li>Authors Tracking</li>
													<li>Google Optimize</li>
													<li>UTM Campaigns Tracking</li>
													<li>Weekly & Monthly Emails</li>
													<li>WooCommerce & EDD Tracking</li>
												</ul>
												<p class="analytify-promo-popup-paragraph" style="padding-top: 10px;">Use the coupon code "GOPRO10" to get 10% discount.</p>
												<a href="https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard&utm_content=Real-time-dashboard&utm_campaign=pro-upgrade" class="analytify-promo-popup-btn" target="_blank">Upgrade to Analytify Pro</a>

																</div>
													</div>
												</div>
											<?php } ?>

										
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>
</div>
