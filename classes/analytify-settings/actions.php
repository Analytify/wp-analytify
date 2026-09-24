<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists

/**
 * Analytify Settings Actions Trait
 *
 * This trait handles all action-related functionality for the Analytify settings.
 * It was created to separate action processing logic from the main settings class,
 * making the code more organized and easier to test.
 *
 * PURPOSE:
 * - Processes form submissions and actions
 * - Handles AJAX requests for settings
 * - Manages settings updates and deletions
 * - Provides action hooks and callbacks
 *
 * @package WP_Analytify
 * @subpackage Settings
 * @since 8.0.0
 */

trait Analytify_Settings_Actions {

	/**
	 * Enqueue media scripts for email settings.
	 *
	 * @return void
	 */
	public function analytify_email_enqueue_media() {
		if ( WPANALYTIFY_Utils::is_current_page( 'analytify-settings' ) ) {
			wp_enqueue_media();
		}
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Initialize admin settings.
	 *
	 * @return void
	 */
	public function admin_init() {
		global $pagenow;
		WPANALYTIFY_Utils::handle_ga4_exceptions();
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
		if ( ( 'admin.php' === $pagenow && $current_page && 'analytify-settings' === $current_page ) || 'options.php' === $pagenow ) {
			$this->set_sections( $this->get_settings_sections() );
			$this->set_fields( $this->get_settings_fields() );
			foreach ( $this->settings_sections as $section ) {
				register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
			}
		}
	}

	/**
	 * Render settings sections and fields.
	 *
	 * @return void
	 */
	public function rendered_settings() {
		foreach ( $this->settings_sections as $section ) {
			if ( false === get_option( $section['id'] ) ) {
				add_option( $section['id'] );
			}
			if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
				$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
				$callback        = call_user_func( array( $this, 'get_description' ), $section['desc'] );
			} elseif ( isset( $section['callback'] ) ) {
				$callback = $section['callback'];
			} else {
				$callback = null;
			}
			add_settings_section( $section['id'], '', $callback, $section['id'] );
		}
		foreach ( $this->settings_fields as $section => $field ) {
			foreach ( $field as $option ) {
				// Skip invalid options that don't have required fields.
				if ( ! isset( $option['name'] ) || ! isset( $option['label'] ) ) {
					continue;
				}

				$type            = isset( $option['type'] ) ? $option['type'] : 'text';
				$args            = array(
					'id'                => $option['name'],
					'label_for'         => "{$section}[{$option['name']}]",
					'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
					'name'              => $option['label'],
					'section'           => $section,
					'size'              => isset( $option['size'] ) ? $option['size'] : null,
					'options'           => isset( $option['options'] ) ? $option['options'] : '',
					'std'               => isset( $option['default'] ) ? $option['default'] : '',
					'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
					'class'             => isset( $option['class'] ) ? $option['class'] : '',
					'type'              => $type,
					'tooltip'           => isset( $option['tooltip'] ) ? $option['tooltip'] : true,
					'input_format'      => isset( $option['input_format'] ) ? $option['input_format'] : '',
					'maxlength'         => isset( $option['maxlength'] ) ? $option['maxlength'] : '',
				);
				$callback_method = 'callback_' . $type;
				if ( method_exists( $this, $callback_method ) ) {
					$callback = array( $this, $callback_method );
					if ( is_callable( $callback ) ) {
						add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], $callback, $section, $section, $args );
					}
				}
			}
		}
	}

	/**
	 * Display settings tabs.
	 *
	 * @return void
	 */
	public function show_tabs() {
		$html  = '<div class="wpa-tab-wrapper">';
		$html .= '<ul class="analytify_nav_tab_wrapper nav-tab-wrapper">';

		// Get accordion IDs to exclude from main tabs.
		$accordion_ids = array();
		foreach ( $this->settings_sections as $tab ) {
			if ( isset( $tab['accordion'] ) && is_array( $tab['accordion'] ) ) {
				foreach ( $tab['accordion'] as $accordion_item ) {
					if ( isset( $accordion_item['id'] ) ) {
						$accordion_ids[] = $accordion_item['id'];
					}
				}
			}
		}

		foreach ( $this->settings_sections as $tab ) {
			// Skip invalid tabs that don't have required fields.
			if ( ! isset( $tab['id'] ) || ! isset( $tab['title'] ) ) {
				continue;
			}

			// Skip accordion items from appearing as separate tabs.
			// But never skip the main tracking tab itself.
			if ( in_array( $tab['id'], $accordion_ids, true ) && 'wp-analytify-tracking' !== $tab['id'] ) {
				continue;
			}

			if ( 'wp-analytify-tracking' === $tab['id'] ) {
				if ( 0 !== $tab['priority'] ) {
					$html .= '<li>';
					$html .= '<a href="#wp-analytify-tracking" class="analytify_nav_tab" id="wp-analytify-tracking-tab">Tracking</a>';
					$html .= '<ul class="tabs-dropdown-menu">';

					// Check if accordion exists and is an array before looping.
					if ( isset( $tab['accordion'] ) && is_array( $tab['accordion'] ) ) {
						foreach ( $tab['accordion'] as $accordion ) {
							// Skip invalid accordion items that don't have required fields.
							if ( ! isset( $accordion['id'] ) || ! isset( $accordion['title'] ) ) {
								continue;
							}

							$html .= '<li><a href="javascript:void(0)" id="' . $accordion['id'] . '" class="wp-analytify-events-tab-item">' . $accordion['title'] . '</a>';
							if ( ! class_exists( 'WP_Analytify_Pro' ) ) {
								$html .= '<span>Upgrade your plan to unlock Premium Reports and Tracking Modules</span>';
							} elseif ( 'wp-analytify-events-tracking' === $accordion['id'] ) {
								$html .= '<span>Track Links, Clicks, Affiliates and files.</span>';
							} elseif ( 'wp-analytify-custom-dimensions' === $accordion['id'] ) {
								$html .= '<span>Setup custom dimensions tracking in Google Analytics.</span>';
							} elseif ( 'wp-analytify-forms-dashboard' === $accordion['id'] ) {
								$html .= '<span>Track Forms submissions, impressions and conversions in Google Analytics.</span>';
							} elseif ( 'analytify-google-ads-tracking' === $accordion['id'] ) {
								$html .= '<span>Track Ads Conversions Woocommerce and EDD purchases.</span>';
							}
							$html .= '</li>';
						}
					}
					$html .= '</ul></li>';
				}
			} elseif ( 0 !== $tab['priority'] ) {
				$html .= sprintf( '<li><a href="#%1$s" class="analytify_nav_tab" id="%1$s-tab">%2$s</a></li>', esc_attr( $tab['id'] ), esc_html( $tab['title'] ) );
			}
		}
		if ( ! class_exists( 'WP_Analytify_Pro' ) ) {
			$html .= sprintf( '<a href="%1$s" class="wp-analytify-premium" target="_blank"><span class="dashicons dashicons-star-filled"></span>%2$s</a>', esc_url( analytify_get_update_link( '', '?utm_source=analytify-lite&amp;utm_medium=tab&amp;utm_campaign=pro-upgrade' ) ), esc_html( 'Upgrade to Pro for More Features' ) );
		}
		$html .= '</ul></div>';
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Get description for settings section.
	 *
	 * @param mixed $desc Description text.
	 * @return mixed Description text.
	 */
	public function get_description( $desc ) {
		return $desc;
	}

	/**
	 * Display settings sections for a page.
	 *
	 * @param mixed $page Page identifier.
	 * @return void
	 */
	public function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
		if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}
		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			echo '<h3>' . esc_html( $section['title'] ) . "</h3>\n";
			echo $section['callback']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Callback output is controlled
			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
				continue;
			}
			echo '<table class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}

	/**
	 * Display settings forms.
	 *
	 * @return void
	 */
	public function show_forms() {
		global $wp_settings_sections, $wp_settings_fields;
		settings_errors();
		$is_authenticate = get_option( 'pa_google_token' );
		?>
		<div class="metabox-holder">
		<?php
		foreach ( $this->settings_sections as $form ) {
			// Skip invalid forms that don't have required fields.
			if ( ! isset( $form['id'] ) ) {
				continue;
			}

			if ( 0 !== $form['priority'] ) {
				?>
							<?php $class = ( ! $is_authenticate && ( 'wp-analytify-profile' === $form['id'] || 'wp-analytify-admin' === $form['id'] || 'wp-analytify-dashboard' === $form['id'] || 'wp-analytify-email' === $form['id'] ) ) ? 'analytify_not_authenticate' : ''; ?>
			<div id="<?php echo esc_attr( $form['id'] ); ?>" class="group <?php echo esc_attr( $class ); ?>" style="display: none;">
							<?php
							if ( 'wp-analytify-authentication' === $form['id'] ) {
								$this->callback_authentication();
								if ( ! get_option( 'pa_google_token' ) ) {
									?>
					<form method="post" action="options.php">
									<?php
									settings_fields( $form['id'] );
									// Only show GA Tracking ID field when not logged in, not the toggle.
									global $wp_settings_fields;
									if ( isset( $wp_settings_fields['wp-analytify-authentication']['wp-analytify-authentication'] ) ) {
										echo '<table class="form-table">';
										foreach ( $wp_settings_fields['wp-analytify-authentication']['wp-analytify-authentication'] as $field_id => $field ) {
											// Only show manual_ua_code field, skip the toggle.
											if ( 'wp-analytify-authentication[manual_ua_code]' === $field_id ) {
												?>
												<tr>
													<th scope="row"><?php echo esc_html( isset( $field['title'] ) ? $field['title'] : '' ); ?></th>
													<td>
														<?php call_user_func( $field['callback'], $field['args'] ); ?>
													</td>
												</tr>
												<?php
											}
										}
										echo '</table>';
									}
									?>
						<div style="padding-left: 10px"><?php submit_button(); ?></div>
					</form>
									<?php
								}
							} elseif ( 'wp-analytify-license' === $form['id'] ) {
								do_action( 'wp_analytify_license_tab' );
							} elseif ( 'wp-analytify-help' === $form['id'] ) {
								$this->callback_help();
							} elseif ( 'wp-analytify-advanced' === $form['id'] ) {
								?>
				<form method="post" action="options.php">
									<?php
									settings_fields( $form['id'] );
									$this->do_settings_sections( $form['id'] );
									?>
					<div style="padding-left: 10px"><?php submit_button(); ?></div>
				</form>
								<?php
							} else {
								if ( ! $is_authenticate && ( 'wp-analytify-profile' === $form['id'] || 'wp-analytify-admin' === $form['id'] || 'wp-analytify-dashboard' === $form['id'] || 'wp-analytify-email' === $form['id'] ) ) {
									echo "<span class='analytify_need_authenticate_first'><a href='#'>You have to Authenticate the Google Analytics first.</a></span>";
								} elseif ( 'wp-analytify-front' === $form['id'] ) {
									echo '<div class="analytify-email-premium-overlay"><div class="analytify-email-premium-popup"><h3 class="analytify-promo-popup-heading" style="text-align:left;">Unable To Fetch Front-end Settings</h3><p class="analytify-promo-popup-paragraph analytify-error-popup-paragraph">This is deprecated and not supported in GA4</p></div></div>';
								} elseif ( 'wp-analytify-tracking' === $form['id'] && isset( $form['accordion'] ) ) {
									// Render Tracking accordions (promo or pro).
									if ( class_exists( 'WP_Analytify_Pro' ) ) {
										do_action( 'wp_analytify_tracking_accordion_pro', $form['accordion'] );
									} else {
										do_action( 'wp_analytify_tracking_accordion_promo', $form['accordion'] );
									}
								}
								if ( 'wp-analytify-front' !== $form['id'] && 'wp-analytify-tracking' !== $form['id'] ) {
									?>
					<form method="post" action="options.php">
									<?php
									settings_fields( $form['id'] );
									$this->do_settings_sections( $form['id'] );
									?>
						<div id='profile-save-button-wrapper' style="padding-left: 10px;">
									<?php submit_button(); ?>
							<div id="setup-wait-message-disabled">
								<img src="<?php echo esc_url( ( defined( 'ANALYTIFY_PLUGIN_URL' ) ? ANALYTIFY_PLUGIN_URL : '' ) . 'assets/img/loaaader.gif' ); ?>">
								<p>Hold on! It may take upto 2 minutes (max) while we setup GA4 tracking.</p>
							</div>
						</div>
					</form>
								<?php } ?>
								<?php
								if ( 'wp-analytify-email' === $form['id'] ) {
										$this->callback_email_form();
								}
								?>
							<?php } ?>
			</div>
					<?php
			}
		}
		?>
		</div>
		<?php
	}



	/**
	 * Delete Analytify cache.
	 *
	 * @return void
	 */
	public function analytify_delete_cache() {
		$delete_cache_from_bar      = isset( $_GET['analytify_delete_cache_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['analytify_delete_cache_nonce'] ) ), 'analytify_delete_cache' );
		$delete_cache_from_settings = isset( $_POST['analytify_delete_cache_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['analytify_delete_cache_nonce'] ) ), 'analytify_delete_cache' );
		if ( $delete_cache_from_bar || $delete_cache_from_settings ) {
			// Clear GA4 dashboard transients.
			delete_transient( 'analytify_quota_exception' );
			global $wpdb;
			$esc_key = '%' . $wpdb->esc_like( '_analytify_transient' ) . '%';
			$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}options` WHERE option_name LIKE %s", $esc_key ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Cache deletion requires direct query

			// Clear Search Console cache.
			delete_option( 'analytify_search_console_data' );

			status_header( 200 );
			$goback = add_query_arg( 'analytify-cache', 'true', wp_get_referer() );
			wp_safe_redirect( $goback );
			die();
		}
	}
}


