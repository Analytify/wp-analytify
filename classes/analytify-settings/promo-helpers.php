<?php
/**
 * Analytify Settings Promo Helpers
 *
 * Contains helper functions for displaying promotional content
 * and upgrade prompts in the Analytify settings pages.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Whether the installed Analytify Pro build ships the Pixels Tracking module on disk.
 *
 * Matches paths Pro uses in inc/modules/analytify-modules.php when loading active modules.
 * Current Pro loads class-analytify-pixels-tracking.php; older zip layouts used classes/pixels-tracking.php.
 *
 * @since 9.0.0
 * @return bool
 */
function wp_analytify_pro_pixels_tracking_module_file_exists() {
	if ( ! class_exists( 'WP_Analytify_Pro', false ) || ! defined( 'ANALYTIFY_PRO_ROOT_PATH' ) ) {
		return false;
	}
	$module_file = trailingslashit( ANALYTIFY_PRO_ROOT_PATH ) . 'inc/modules/pixels-tracking/class-analytify-pixels-tracking.php';
	return file_exists( $module_file );
}

/**
 * Create activation anchor for modules.
 *
 * @param mixed $url   The URL for the upgrade link.
 * @param mixed $text  The text for the upgrade link.
 * @param mixed $addon The addon identifier.
 * @version 9.0.0
 * @return string
 */
function activate_module_anchor( $url = '', $text = '', $addon = '' ) {
	if ( empty( $text ) ) {
		$text = __( 'Upgrade to Analytify Pro', 'wp-analytify' );
	}

	if ( ! empty( $addon ) ) {
		if ( 'analytify-forms' === $addon && file_exists( ABSPATH . 'wp-content/plugins/wp-analytify-forms' ) ) {
			return '<a href=" ' . admin_url( 'admin.php?page=analytify-addons' ) . ' " class="analytify-promo-popup-btn">' . esc_html__( 'Activate Addon', 'wp-analytify' ) . '</a>';
		}
	} elseif ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
		return '<a href=" ' . admin_url( 'admin.php?page=analytify-addons' ) . ' " class="analytify-promo-popup-btn">' . esc_html__( 'Activate Addon', 'wp-analytify' ) . '</a>';
	}
	return '<a href="' . $url . '" class="analytify-promo-popup-btn" target="_blank">' . $text . '</a>';
}

/**
 * Whether a Pro internal module is active (slug-keyed option or legacy numeric list).
 *
 * @since 9.0.0
 * @param array  $modules Modules from wp_analytify_modules.
 * @param string $slug    Module slug (e.g. pixels-tracking).
 * @return bool
 */
function wp_analytify_pro_internal_module_active( $modules, $slug ) {
	if ( ! is_array( $modules ) || '' === $slug ) {
		return false;
	}
	if ( isset( $modules[ $slug ] ) && is_array( $modules[ $slug ] ) ) {
		$status = isset( $modules[ $slug ]['status'] ) ? $modules[ $slug ]['status'] : '';
		return ( 'active' === $status || true === $status );
	}
	foreach ( $modules as $module ) {
		if ( is_array( $module ) && isset( $module['slug'] ) && $module['slug'] === $slug ) {
			$status = isset( $module['status'] ) ? $module['status'] : '';
			return ( 'active' === $status || true === $status );
		}
	}
	return false;
}

/**
 * Display tracking accordion promo.
 *
 * @param mixed $accordions The accordions array to modify.
 * @version 9.0.0
 * @return void
 */
function wp_analytify_tracking_accordion_promo( $accordions ) {
	foreach ( $accordions as &$accordion ) {
		if ( 'wp-analytify-events-tracking' === $accordion['id'] ) {
			$accordion['is_active']  = false;
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer">
                <div class="analytify-email-premium-overlay">
                    <div class="analytify-email-premium-popup">
                        <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Events Tracking', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Our Events tracking feature helps you to setup and track custom events on your WordPress website. Custom events will help you track and measure the performance of the most important Outbound links like Affiliate links. Setting up custom events is tricky for the beginners. But with the Analytify\'s Events Tracking you can easily acheive this on your WordPress websites.', 'wp-analytify' ) . '</p>
                        <ul class="analytify-promo-popup-list">
                            <li>' . esc_html__( 'Affiliate Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Links & Clicks Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Enhanced Link Attribution', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Anchor Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'File downloads Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Track outbound links', 'wp-analytify' ) . '</li>
                        </ul>
                        ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=tracking-tab&utm_content=Events+Tracking&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';
		} elseif ( 'wp-analytify-custom-dimensions' === $accordion['id'] ) {
			$accordion['is_active']  = false;
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer">
                <div class="analytify-email-premium-overlay">
                    <div class="analytify-email-premium-popup">
                        <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Google Custom Dimensions Tracking', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Custom Dimensions helps you to track Categories, Tags, Post Types, and logged in activities within Google Analytics. Remember, you have to setup Custom Dimensions in Google Analytics as well.', 'wp-analytify' ) . '</p>
                        <ul class="analytify-promo-popup-list">
                            <li>' . esc_html__( 'Custom Post Type Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Category Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Tags Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Authors Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'User-ID Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Track logged in activity', 'wp-analytify' ) . '</li>
                        </ul>
                        ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=tracking-tab&utm_content=custom-dimensions&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';
		} elseif ( 'wp-analytify-forms' === $accordion['id'] ) {
			$accordion['is_active']  = false;
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer">
                    <div class="analytify-email-premium-overlay">
                        <div class="analytify-email-premium-popup">
                            <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Forms Tracking Addon', 'wp-analytify' ) . '</h3>
                            <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Would you like to track your WordPress website forms? Analytify Forms Tracking addon helps you to track the number of impressions and forms conversions/submissions. This Addon works with any popular WordPress form plugins like Gravity forms, Ninja Forms, Formidable forms and more, even including Custom WordPress forms.', 'wp-analytify' ) . '</p>
                            <ul class="analytify-promo-popup-list">
                                <li>' . esc_html__( 'Custom Forms Tracking', 'wp-analytify' ) . '</li>
                                <li>' . esc_html__( 'Track Gravity Forms', 'wp-analytify' ) . '</li>
                                <li>' . esc_html__( 'Track Contact Form 7', 'wp-analytify' ) . '</li>
                                <li>' . esc_html__( 'WPForms Tracking', 'wp-analytify' ) . '</li>
                                <li>' . esc_html__( 'Track Formidable Forms', 'wp-analytify' ) . '</li>
                                <li>' . esc_html__( 'Track submissions, impressions and conversions.', 'wp-analytify' ) . '</li>
                            </ul>
                            ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=tracking-tab&utm_content=forms+tracking&utm_campaign=pro-upgrade', __( 'Explore Analytify Pro + Forms Tracking bundle', 'wp-analytify' ), 'analytify-forms' ) . '
                        </div>
                    </div>
                </div>';
		} elseif ( 'analytify-google-ads-tracking' === $accordion['id'] ) {
			$accordion['is_active']  = false;
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer analytify-google-ads-container">
                    <div class="analytify-email-premium-overlay analytify-google-ads-overlay">
                        <div class="analytify-google-ads-popup">
                            <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Google Ads Conversion Tracking.', 'wp-analytify' ) . '</h3>
                            <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Track Your Woocommerce and EDD purchases as conversion in Google Ads using our Conversion Tracking addon.', 'wp-analytify' ) . '</p>
                            ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=tracking-tab&utm_content=google-ads-tracking&utm_campaign=pro-upgrade' ) . '
                        </div>
                    </div>
                </div>';
		} elseif ( 'analytify-pixels-tracking' === $accordion['id'] || 'pixels-tracking' === $accordion['id'] ) {
			$accordion['is_active']  = false;
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer analytify-pixels-container">
                <div class="analytify-email-premium-overlay analytify-pixels-overlay">
                    <div class="analytify-pixels-popup">
                        <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Pixels Tracking', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Track your website visitors with Meta Pixel and TikTok Pixel for better conversion tracking and retargeting campaigns. Easily add and manage your pixel tracking codes.', 'wp-analytify' ) . '</p>
                        <ul class="analytify-promo-popup-list">
                            <li>' . esc_html__( 'Meta (Facebook) Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'TikTok Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'LinkedIn Insight Tag', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Pinterest Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'X (Twitter) Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Snapchat Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Microsoft Ads UET', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Easy setup and management', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Conversion tracking and retargeting support', 'wp-analytify' ) . '</li>
                        </ul>
                        ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=tracking-tab&utm_content=pixels-tracking&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';
		}
	}
	wp_analytify_tracking_accordion( $accordions, 'promo' );
}
add_action( 'wp_analytify_tracking_accordion_promo', 'wp_analytify_tracking_accordion_promo' );

/**
 * Display tracking accordion for pro users.
 *
 * @param mixed $accordions The accordions array to modify.
 * @version 9.0.0
 * @return void
 */
function wp_analytify_tracking_accordion_pro( $accordions ) {
	// Pro builds without the module file cannot render Pixels settings (empty accordion + stray Save).
	if ( class_exists( 'WP_Analytify_Pro', false ) && ! wp_analytify_pro_pixels_tracking_module_file_exists() ) {
		$accordions = array_values(
			array_filter(
				$accordions,
				function ( $accordion ) {
					if ( ! isset( $accordion['id'] ) ) {
						return true;
					}
					return ! (
						'analytify-pixels-tracking' === $accordion['id'] ||
						'pixels-tracking' === $accordion['id']
					);
				}
			)
		);
	}

	$analytify_modules = get_option( 'wp_analytify_modules' );
	$analytify_modules = apply_filters( 'analytify_pro_modules', $analytify_modules );
	foreach ( $accordions as &$accordion ) {
		if ( 'wp-analytify-events-tracking' === $accordion['id'] ) {
			$accordion['is_active']  = ( 'active' === $analytify_modules['events-tracking']['status'] );
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer">
                <div class="analytify-email-premium-overlay">
                    <div class="analytify-email-premium-popup">
                    <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Events Tracking', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Our Events tracking feature helps you to setup and track custom events on your WordPress website. Custom events will help you track and measure the performance of the most important Outbound links like Affiliate links. Setting up custom events is tricky for the beginners. But with the Analytify\'s Events Tracking you can easily acheive this on your WordPress websites.', 'wp-analytify' ) . '</p>
                        <ul class="analytify-promo-popup-list">
                            <li>' . esc_html__( 'Affiliate Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Links & Clicks Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Enhanced Link Attribution', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Anchor Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'File downloads Tracking', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Track outbound links', 'wp-analytify' ) . '</li>
                        </ul>
                        ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-pro&utm_medium=tracking-tab&utm_content=Events+Tracking&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';
		} elseif ( 'wp-analytify-custom-dimensions' === $accordion['id'] ) {
			$accordion['is_active']  = ( 'active' === $analytify_modules['custom-dimensions']['status'] );
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer">
                <div class="analytify-email-premium-overlay">
                    <div class="analytify-email-premium-popup">
                    <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Google Custom Dimensions Tracking', 'wp-analytify' ) . '</h3>
                    <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Custom Dimensions helps you to track Categories, Tags, Post Types, and logged in activities within Google Analytics. Remember, you have to setup Custom Dimensions in Google Analytics as well.', 'wp-analytify' ) . '</p>
                    <ul class="analytify-promo-popup-list">
                        <li>' . esc_html__( 'Custom Post Type Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Category Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Tags Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Authors Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'User-ID Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Track logged in activity', 'wp-analytify' ) . '</li>
                    </ul>
                    ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-pro&utm_medium=tracking-tab&utm_content=custom-dimensions&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';
		} elseif ( 'wp-analytify-forms' === $accordion['id'] ) {
			$accordion['is_active']  = class_exists( 'Analytify_Addon_Forms' ) || class_exists( 'Analytify_Forms' );
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer">
                <div class="analytify-email-premium-overlay">
                    <div class="analytify-email-premium-popup">
                    <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Forms Tracking Addon', 'wp-analytify' ) . '</h3>
                    <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Would you like to track your WordPress website forms? Analytify Forms Tracking addon helps you to track the number of impressions and forms conversions/submissions. This Addon works with any popular WordPress form plugins like Gravity forms, Ninja Forms, Formidable forms and more, even including Custom WordPress forms.', 'wp-analytify' ) . '</p>
                    <ul class="analytify-promo-popup-list">
                        <li>' . esc_html__( 'Custom Forms Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Track Gravity Forms', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Track Contact Form 7', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'WPForms Tracking', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Track Formidable Forms', 'wp-analytify' ) . '</li>
                        <li>' . esc_html__( 'Track submissions, impressions and conversions.', 'wp-analytify' ) . '</li>
                    </ul>
                    ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-pro&utm_medium=tracking-tab&utm_content=forms+tracking&utm_campaign=pro-upgrade', __( 'Explore Analytify Pro + Forms Tracking bundle', 'wp-analytify' ), 'analytify-forms' ) . '
                    </div>
                </div>
            </div>';
		} elseif ( 'analytify-google-ads-tracking' === $accordion['id'] ) {
			$accordion['is_active']  = isset( $analytify_modules['google-ads-tracking'] ) && 'active' === $analytify_modules['google-ads-tracking']['status'];
			$accordion['promo_text'] = '<div class="analytify-email-promo-contianer analytify-google-ads-container">
                <div class="analytify-google-ads-overlay">
                    <div class="analytify-google-ads-popup">
                        <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Google Ads Conversion Tracking.', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Track Your Woocommerce and EDD purchases as conversion in Google Ads using our Conversion Tracking addon.', 'wp-analytify' ) . '</p>
                        ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-pro&utm_medium=tracking-tab&utm_content=google-ads-tracking&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';
		} elseif ( 'analytify-pixels-tracking' === $accordion['id'] || 'pixels-tracking' === $accordion['id'] ) {
			$modules_opt            = is_array( $analytify_modules ) ? $analytify_modules : array();
			$pixels_tracking_loaded = class_exists( 'Analytify_Pixels_Tracking', false );
			$pixels_module_active   = wp_analytify_pro_internal_module_active( $modules_opt, 'pixels-tracking' );
			$accordion['is_active'] = $pixels_module_active && $pixels_tracking_loaded;

			$changelog_url = 'https://analytify.io/changelog/?utm_source=analytify-pro&utm_medium=tracking-tab&utm_content=pixels-tracking&utm_campaign=pixels-requires-update';
			$changelog_btn = '<a href="' . esc_url( $changelog_url ) . '" class="analytify-promo-popup-btn" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View changelog & update', 'wp-analytify' ) . '</a>';

			$unlock_dlg = '<div class="analytify-email-promo-contianer analytify-pixels-container">
                <div class="analytify-email-premium-overlay analytify-pixels-overlay">
                    <div class="analytify-pixels-popup">
                        <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Unlock Pixels Tracking', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Track your website visitors with Meta Pixel and TikTok Pixel for better conversion tracking and retargeting campaigns. Easily add and manage your pixel tracking codes.', 'wp-analytify' ) . '</p>
                        <ul class="analytify-promo-popup-list">
                            <li>' . esc_html__( 'Meta (Facebook) Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'TikTok Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'LinkedIn Insight Tag', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Pinterest Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'X (Twitter) Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Snapchat Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Microsoft Ads UET', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Easy setup and management', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Conversion tracking and retargeting support', 'wp-analytify' ) . '</li>
                        </ul>
                        ' . activate_module_anchor( 'https://analytify.io/pricing?utm_source=analytify-pro&utm_medium=tracking-tab&utm_content=pixels-tracking&utm_campaign=pro-upgrade' ) . '
                    </div>
                </div>
            </div>';

			$update_dlg = '<div class="analytify-email-promo-contianer analytify-pixels-container">
                <div class="analytify-email-premium-overlay analytify-pixels-overlay">
                    <div class="analytify-pixels-popup">
                        <h3 class="analytify-promo-popup-heading">' . esc_html__( 'Update Analytify Pro for Pixels Tracking', 'wp-analytify' ) . '</h3>
                        <p class="analytify-promo-popup-paragraph">' . esc_html__( 'Pixels Tracking is enabled for your site, but this copy of Analytify Pro does not include the Pixels module files yet. Update Analytify Pro to the latest release to load the settings screen.', 'wp-analytify' ) . '</p>
                        <ul class="analytify-promo-popup-list">
                            <li>' . esc_html__( 'Meta (Facebook) Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'TikTok Pixel', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'LinkedIn Insight Tag', 'wp-analytify' ) . '</li>
                            <li>' . esc_html__( 'Additional ad pixels', 'wp-analytify' ) . '</li>
                        </ul>
                        ' . $changelog_btn . '
                    </div>
                </div>
            </div>';

			if ( ! $accordion['is_active'] ) {
				$accordion['promo_text'] = ( $pixels_module_active && ! $pixels_tracking_loaded )
					? $update_dlg
					: $unlock_dlg;
			}
		}
	}
	wp_analytify_tracking_accordion( $accordions, 'pro' );
}
add_action( 'wp_analytify_tracking_accordion_pro', 'wp_analytify_tracking_accordion_pro' );

/**
 * Display tracking accordion.
 *
 * @param mixed $accordions The accordions array to display.
 * @param mixed $type       The type of accordion (promo or pro).
 * @return void
 * @version 9.0.0
 */
function wp_analytify_tracking_accordion( $accordions, $type = 'promo' ) {
	?>
	<div class="tracking-accordions-container">
		<div class="tracking-accordions-wrapper">
			<ul>
				<?php foreach ( $accordions as $accordion ) { ?>
					<li class="tracking-accordion event-tracking <?php echo esc_attr( $accordion['id'] ); ?>" data-id="<?php echo esc_attr( $accordion['id'] ); ?>">
						<div class="tracking-accordions-heading">
							<p><?php echo wp_kses_post( $accordion['title'] ); ?></p>
						</div>
						<div class="tracking-accordions-content">
							<?php
							if ( 'pro' === $type && isset( $accordion['is_active'] ) && $accordion['is_active'] ) {
								do_action( 'wp_analytify_tracking_accordion_options', $accordion['id'] );
							} elseif ( isset( $accordion['promo_text'] ) && ! empty( $accordion['promo_text'] ) ) {
									echo wp_kses_post( $accordion['promo_text'] );
							}
							?>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php
}


