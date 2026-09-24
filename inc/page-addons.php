<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable for this plugin structure.
/**
 * Addons Page Handler
 *
 * @package WP_Analytify
 */

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable

if ( ! class_exists( 'WP_Analytify_Addons' ) ) {

	/**
	 * WP Analytify Addons Class
	 */
	class WP_Analytify_Addons {

		/**
		 * List of plugins.
		 *
		 * @var array<string, mixed>
		 */
		protected $plugins_list;

		/**
		 * List of modules.
		 *
		 * @var array<string, mixed>
		 */
		protected $modules_list;

		/**
		 * Constructor
		 *
		 * @version 7.0.5
		 * @return void
		 */
		public function __construct() {
			$this->plugins_list = get_plugins();
			$this->modules_list = $this->modules();

			// Use priority 20 to ensure it runs after scripts-styles.php (which uses default priority 10).
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 20 );
		}

		/**
		 * Returns a list of addons
		 *
		 * @return array<string, mixed>
		 * @since 1.3
		 */
		public function addons() {
			$logger = function_exists( 'analytify_get_logger' ) ? analytify_get_logger() : null;

			if ( ! class_exists( 'WP_Analytify_Pro' ) || ( defined( 'ANALYTIFY_PRO_VERSION' ) && version_compare( ANALYTIFY_PRO_VERSION, '6.0.0', '<' ) ) ) {

				// Get the transient where the addons are stored on-site.
				$data = get_transient( 'analytify_api_addons' );

				// If we already have data, return it.
				if ( ! empty( $data ) && is_array( $data ) && count( $data ) > 0 ) {
					// Validate the cached data structure.
					$valid_count = 0;
					foreach ( $data as $item ) {
						if ( is_object( $item ) && isset( $item->slug ) && isset( $item->title ) ) {
							++$valid_count;
						}
					}

					// Only return if we have valid addon data.
					if ( $valid_count > 0 ) {
						return $data;
					} else {
						// Clear bad cached data.
						delete_transient( 'analytify_api_addons' );
					}
				}

				// Make sure this matches the exact URL from your site.
				$url = 'https://analytify.io/wp-json/analytify/v1/plugins';

				// Allow SSL verification to be disabled for local development.
				$sslverify = apply_filters( 'analytify_api_sslverify', true );

				$response = wp_remote_get(
					$url,
					array(
						'timeout'   => 20,
						'sslverify' => $sslverify,
					)
				);

				if ( ! is_wp_error( $response ) ) {
					$response_code = wp_remote_retrieve_response_code( $response );

					// Only process if we got a successful response.
					if ( 200 === (int) $response_code ) {
						// Decode the data that we got.
						$body = wp_remote_retrieve_body( $response );

						if ( ! empty( $body ) ) {
							$data = json_decode( $body, false );

							// Validate decoded data - can be array or object.
							if ( json_last_error() === JSON_ERROR_NONE ) {
								// Convert object to array if needed.
								if ( is_object( $data ) ) {
									$data = json_decode( $body, true );
								}

								// Ensure we have a valid array with at least one valid addon.
								if ( ! empty( $data ) && is_array( $data ) ) {
									// Validate structure - ensure we have objects with required fields.
									$valid_data = array();
									foreach ( $data as $key => $item ) {
										// Convert array items to objects if needed.
										if ( is_array( $item ) ) {
											$item = (object) $item;
										}
										if ( is_object( $item ) && isset( $item->slug ) && isset( $item->title ) ) {
											$valid_data[ $key ] = $item;
										}
									}

									if ( ! empty( $valid_data ) ) {
										// Store the data for a week.
										set_transient( 'analytify_api_addons', $valid_data, 7 * DAY_IN_SECONDS );
										return $valid_data;
									}
								}
							}
						}
					}
				} else {
					// Log the error for debugging, but don't expose it to users.
					if ( $logger && method_exists( $logger, 'warning' ) ) {
						$logger->warning(
							'Failed to fetch addons from API.',
							array(
								'source'     => 'addons',
								'error'      => $response->get_error_message(),
								'ssl_verify' => $sslverify,
							)
						);
					}
					// Try again with SSL verification disabled if it was enabled.
					if ( $sslverify ) {
						$retry_response = wp_remote_get(
							$url,
							array(
								'timeout'   => 20,
								'sslverify' => false,
							)
						);

						if ( ! is_wp_error( $retry_response ) ) {
							$retry_code = wp_remote_retrieve_response_code( $retry_response );
							if ( 200 === (int) $retry_code ) {
								$retry_body = wp_remote_retrieve_body( $retry_response );
								if ( ! empty( $retry_body ) ) {
									$retry_data = json_decode( $retry_body, false );
									if ( json_last_error() === JSON_ERROR_NONE ) {
										// Convert object to array if needed.
										if ( is_object( $retry_data ) ) {
											$retry_data = json_decode( $retry_body, true );
										}

										// Validate structure.
										if ( ! empty( $retry_data ) && is_array( $retry_data ) ) {
											$valid_data = array();
											foreach ( $retry_data as $key => $item ) {
												if ( is_array( $item ) ) {
													$item = (object) $item;
												}
												if ( is_object( $item ) && isset( $item->slug ) && isset( $item->title ) ) {
													$valid_data[ $key ] = $item;
												}
											}

											if ( ! empty( $valid_data ) ) {
												set_transient( 'analytify_api_addons', $valid_data, 7 * DAY_IN_SECONDS );
												return $valid_data;
											}
										}
									}
								}
							}
						}
					}
				}

				return array();
			}

			// API URL to fetch addons.
			$url = 'https://analytify.io/wp-json/analytify/v1/plugins';

			// Allow SSL verification to be disabled for local development.
			$sslverify = apply_filters( 'analytify_api_sslverify', true );

			// Fetch data from the API.
			$response = wp_remote_get(
				$url,
				array(
					'timeout'   => 20,
					'sslverify' => $sslverify,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$response_code = wp_remote_retrieve_response_code( $response );

				// Only process if we got a successful response.
				if ( 200 === (int) $response_code ) {
					// Decode the JSON response.
					$body = wp_remote_retrieve_body( $response );
					if ( ! empty( $body ) ) {
						$data = json_decode( $body, false );

						// Validate decoded data - can be array or object.
						if ( json_last_error() === JSON_ERROR_NONE ) {
							// Convert object to array if needed.
							if ( is_object( $data ) ) {
								$data = json_decode( $body, true );
							}

							// Ensure we have a valid array with at least one valid addon.
							if ( ! empty( $data ) && is_array( $data ) ) {
								// Validate structure - ensure we have objects with required fields.
								$valid_data = array();
								foreach ( $data as $key => $item ) {
									if ( is_array( $item ) ) {
										$item = (object) $item;
									}
									if ( is_object( $item ) && isset( $item->slug ) && isset( $item->title ) ) {
										$valid_data[ $key ] = $item;
									}
								}

								if ( ! empty( $valid_data ) ) {
									// Cache the data for a week.
									set_transient( 'analytify_api_addons', $valid_data, 7 * DAY_IN_SECONDS );
								}
							}
						}
					}
				}
			} else {
				// Log the error for debugging, but don't expose it to users.
				if ( $logger && method_exists( $logger, 'warning' ) ) {
					$logger->warning(
						'Failed to fetch addons from API (retry section).',
						array(
							'source'     => 'addons',
							'error'      => $response->get_error_message(),
							'ssl_verify' => $sslverify,
						)
					);
				}
				// Try again with SSL verification disabled if it was enabled.
				if ( $sslverify ) {
					$retry_response = wp_remote_get(
						$url,
						array(
							'timeout'   => 20,
							'sslverify' => false,
						)
					);

					if ( ! is_wp_error( $retry_response ) ) {
						$retry_code = wp_remote_retrieve_response_code( $retry_response );
						if ( 200 === (int) $retry_code ) {
							$retry_body = wp_remote_retrieve_body( $retry_response );
							if ( ! empty( $retry_body ) ) {
								$retry_data = json_decode( $retry_body, false );
								if ( json_last_error() === JSON_ERROR_NONE ) {
									// Convert object to array if needed.
									if ( is_object( $retry_data ) ) {
										$retry_data = json_decode( $retry_body, true );
									}

									if ( ! empty( $retry_data ) && is_array( $retry_data ) ) {
										$valid_data = array();
										foreach ( $retry_data as $key => $item ) {
											if ( is_array( $item ) ) {
												$item = (object) $item;
											}
											if ( is_object( $item ) && isset( $item->slug ) && isset( $item->title ) ) {
												$valid_data[ $key ] = $item;
											}
										}

										if ( ! empty( $valid_data ) ) {
											set_transient( 'analytify_api_addons', $valid_data, 7 * DAY_IN_SECONDS );
										}
									}
								}
							}
						}
					}
				}
			}

			// Fetch the cached API data or an empty array if not available.
			$api_data = get_transient( 'analytify_api_addons' );
			$api_data = $api_data ? $api_data : array();

			// Filter out WooCommerce and EDD add-ons.
			if ( version_compare( ANALYTIFY_PRO_VERSION, '6.1.0', '<' ) ) {
				$filtered_addons = array_filter(
					$api_data,
					function ( $addon ) {
						return ! (
						isset( $addon->slug ) && (
							strpos( $addon->slug, 'woocommerce' ) !== false ||
							strpos( $addon->slug, 'edd' ) !== false ||
							strpos( $addon->slug, 'campaigns' ) !== false ||
							strpos( $addon->slug, 'authors' ) !== false
						)
						);
					}
				);
			} else {
				$filtered_addons = array_filter(
					$api_data,
					function ( $addon ) {
						return ! (
						isset( $addon->slug ) && (
							strpos( $addon->slug, 'woocommerce' ) !== false ||
							strpos( $addon->slug, 'edd' ) !== false ||
							strpos( $addon->slug, 'campaigns' ) !== false ||
							strpos( $addon->slug, 'authors' ) !== false ||
							strpos( $addon->slug, 'forms' ) !== false ||
							strpos( $addon->slug, 'email' ) !== false ||
							strpos( $addon->slug, 'goals' ) !== false ||
							strpos( $addon->slug, 'lifterlms' ) !== false ||
							strpos( $addon->slug, 'pmpro' ) !== false ||
							strpos( $addon->slug, 'learndash' ) !== false

						)
						);
					}
				);
			}
			// Return the filtered addons as associative array.
			return $filtered_addons;
		}
		/**
		 * Enqueue admin scripts and localize variables.
		 *
		 * @version 9.1.1
		 * @return void
		 */
		public function enqueue_admin_scripts() {
			// Get current screen to check if we're on addons page.
			$screen = get_current_screen();
			if ( ! $screen || strpos( $screen->base, 'analytify-addons' ) === false ) {
				return;
			}

			$addons = $this->addons();

			// Extract slugs from addons array for JavaScript validation.
			$slugs = array();

			// Add Pro addon slugs from the pro_addons option.
			$pro_addons_local = get_option( 'wp_analytify_pro_addons' );
			if ( ! empty( $pro_addons_local ) && is_array( $pro_addons_local ) ) {
				foreach ( $pro_addons_local as $slug => $data ) {
					$slugs[] = $slug;
					// Also add the full plugin path format for compatibility.
					$slugs[] = $slug . '/' . $slug . '.php';
				}
			}

			// Add API addon slugs.
			if ( ! empty( $addons ) && is_array( $addons ) ) {
				foreach ( $addons as $key => $addon ) {
					// Handle both object and array formats.
					if ( is_object( $addon ) && isset( $addon->slug ) ) {
						$slug = $addon->slug;
						if ( ! in_array( $slug, $slugs, true ) ) {
							$slugs[] = $slug;
						}
						// Also add the full plugin path format for compatibility.
						$plugin_path = $slug . '/' . ( 'analytify-analytics-dashboard-widget' === $slug ? 'wp-analytify-dashboard' : $slug ) . '.php';
						if ( ! in_array( $plugin_path, $slugs, true ) ) {
							$slugs[] = $plugin_path;
						}
					} elseif ( is_array( $addon ) && isset( $addon['slug'] ) ) {
						$slug = $addon['slug'];
						if ( ! in_array( $slug, $slugs, true ) ) {
							$slugs[] = $slug;
						}
						$plugin_path = $slug . '/' . ( 'analytify-analytics-dashboard-widget' === $slug ? 'wp-analytify-dashboard' : $slug ) . '.php';
						if ( ! in_array( $plugin_path, $slugs, true ) ) {
							$slugs[] = $plugin_path;
						}
					} elseif ( is_string( $key ) ) {
						// If key is the slug (for associative arrays).
						if ( ! in_array( $key, $slugs, true ) ) {
							$slugs[] = $key;
						}
						$plugin_path = $key . '/' . ( 'analytify-analytics-dashboard-widget' === $key ? 'wp-analytify-dashboard' : $key ) . '.php';
						if ( ! in_array( $plugin_path, $slugs, true ) ) {
							$slugs[] = $plugin_path;
						}
					}
				}
			}

			// Add module slugs.
			$modules_local = $this->modules();
			if ( ! empty( $modules_local ) && is_array( $modules_local ) ) {
				foreach ( $modules_local as $slug => $data ) {
					if ( is_array( $data ) && isset( $data['slug'] ) ) {
						$module_slug = $data['slug'];
						if ( ! in_array( $module_slug, $slugs, true ) ) {
							$slugs[] = $module_slug;
						}
					} elseif ( is_string( $slug ) ) {
						if ( ! in_array( $slug, $slugs, true ) ) {
							$slugs[] = $slug;
						}
					}
				}
			}

			// Ensure script is enqueued (should already be done by scripts-styles.php).
			if ( ! wp_script_is( 'analytify-addons-js', 'enqueued' ) ) {
				$plugin_file = defined( 'WP_ANALYTIFY_PLUGIN_DIR' ) ? WP_ANALYTIFY_PLUGIN_DIR . '/wp-analytify.php' : dirname( __DIR__ ) . '/wp-analytify.php';
				wp_enqueue_script( 'analytify-addons-js', plugins_url( 'assets/js/wp-analytify-addons' . analytify_get_asset_suffix() . '.js', $plugin_file ), array( 'jquery' ), ANALYTIFY_VERSION, false );
			}

			// Localize to the correct script handle.
			// wp_localize_script replaces any previous localization.
			$localized = wp_localize_script(
				'analytify-addons-js',
				'analytify_addons',
				array(
					'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
					'nonce'                    => wp_create_nonce( 'addons' ),
					'install_nonce'            => wp_create_nonce( 'updates' ),
					'activate_dashboard_nonce' => wp_create_nonce( 'activate-analytify-dashboard' ),
					'allowed_slugs'            => $slugs,
					'i18n'                     => array(
						'installing' => __( 'Installing...', 'wp-analytify' ),
						'activating' => __( 'Activating...', 'wp-analytify' ),
						'installed'  => __( 'Installed & Activated', 'wp-analytify' ),
						'get_addon'  => __( 'Get this add-on', 'wp-analytify' ),
					),
				)
			);
		}

		/**
		 * Check plugin status
		 *
		 * @param string $slug Plugin slug.
		 * @param string $extension_or_status Extension or status.
		 * @return array<string, mixed>
		 * @since 1.3
		 */
		public function pro_addons_status( $slug, $extension_or_status ) {
			$nonce = wp_create_nonce( $slug );

			if ( 'active' === $extension_or_status ) {
				printf(   // translators: Deactivate add-on.
					esc_html__( '%1$s Deactivate add-on %2$s', 'wp-analytify' ),
					'<button type="button" class="button-primary analytify-addon-state analytify-deactivate-addon" data-slug="' . esc_attr( $slug ) . '" data-set-state="deactive" data-nonce="' . esc_attr( $nonce ) . '" >',
					'</button>'
				);
				return array(
					'status' => 'active',
					'slug'   => $slug,
				);
			} else {
				printf(   // translators: Activate add-on.
					esc_html__( '%1$s Activate add-on %2$s', 'wp-analytify' ),
					'<button type="button" class="button-primary analytify-addon-state analytify-activate-addon" data-slug="' . esc_attr( $slug ) . '" data-set-state="active" data-nonce="' . esc_attr( $nonce ) . '" >',
					'</button>'
				);
				return array(
					'status' => 'inactive',
					'slug'   => $slug,
				);
			}
		}

		/**
		 * Get addon status.
		 *
		 * @param string $slug Plugin slug.
		 * @param mixed  $extension Extension data.
		 * @version 9.1.1
		 * @return void
		 */
		public function addons_status( $slug, $extension ) {
			$folder_slug = $slug;
			// Free addon has different filename.
			$addon_file_name = ( 'analytify-analytics-dashboard-widget' === $slug ) ? 'wp-analytify-dashboard' : $slug;
			$plugin_file     = $slug . '/' . $addon_file_name . '.php';

			if ( is_plugin_active( $plugin_file ) ) {
				// translators: Deactivate add-on.
				printf( esc_html__( '%1$s Deactivate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-deactivate-module" data-slug="' . esc_attr( $plugin_file ) . '" data-set-state="deactive" data-internal-module="false">', '</button>' );

			} elseif ( array_key_exists( $plugin_file, $this->plugins_list ) ) {
				// translators: Activate add-on.
				printf( esc_html__( '%1$s Activate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-activate-module" data-slug="' . esc_attr( $plugin_file ) . '" data-set-state="active" data-internal-module="false">', '</button>' );

			} elseif ( is_plugin_inactive( $plugin_file ) ) {

				if ( isset( $extension->status ) && '' !== $extension->status ) {
					// translators: Simple shortcodes.
					printf( esc_html__( '%1$s Download %2$s', 'wp-analytify' ), '<a target="_blank" href="' . esc_url( isset( $extension->url ) ? $extension->url : '#' ) . '" class="button-primary">', '</a>' );
				} elseif ( 'analytify-analytics-dashboard-widget' === $folder_slug && current_user_can( 'install_plugins' ) ) {
					printf(
						/* translators: 1: opening button tag, 2: closing button tag */
						esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ),
						'<button type="button" class="button-primary analytify-install-dashboard-addon" data-slug="' . esc_attr( $plugin_file ) . '" data-install-nonce="' . esc_attr( wp_create_nonce( 'updates' ) ) . '" data-activate-nonce="' . esc_attr( wp_create_nonce( 'activate-analytify-dashboard' ) ) . '">',
						'</button>'
					);
				} else {
					// translators: Get add-on.
					printf( esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ), '<a target="_blank" href="' . esc_url( isset( $extension->url ) ? $extension->url : '#' ) . '" class="button-primary">', '</a>' );
				}
			}
		}


		/**
		 * Check if pro version is supporitng modules.
		 *
		 * @version 7.0.5
		 * @return bool
		 */
		public function check_pro_support() {

			if ( class_exists( 'WP_Analytify_Pro' ) ) {
				$plugins = get_plugins();

				if ( version_compare( $plugins['wp-analytify-pro/wp-analytify-pro.php']['Version'], '4.0', '>=' ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Whether a bundled Pro module main file exists (Pro ZIP includes the integration).
		 *
		 * @param string $slug Module slug, e.g. wp-analytify-pmpro.
		 * @return bool
		 */
		public function pro_bundled_module_is_installed( $slug ) {
			if ( ! is_string( $slug ) || '' === $slug ) {
				return false;
			}
			if ( ! defined( 'ANALYTIFY_PRO_ROOT_PATH' ) ) {
				return false;
			}
			// Pixels ships as class-analytify-pixels-tracking.php (not slug/slug.php).
			if ( 'pixels-tracking' === $slug ) {
				$main = ANALYTIFY_PRO_ROOT_PATH . '/inc/modules/pixels-tracking/class-analytify-pixels-tracking.php';
				return file_exists( $main );
			}
			$main = ANALYTIFY_PRO_ROOT_PATH . '/inc/modules/' . $slug . '/' . $slug . '.php';
			return file_exists( $main );
		}

		/**
		 * Changelog URL for “Update Analytify Pro” CTAs on the Add-ons screen.
		 *
		 * @param string $utm_content UTM content slug for analytics.
		 * @return string
		 */
		public function get_analytify_pro_changelog_url( $utm_content = 'addons' ) {
			return 'https://analytify.io/changelog/?utm_source=analytify-pro&utm_medium=addons&utm_campaign=update-pro&utm_content=' . rawurlencode( (string) $utm_content );
		}

		/**
		 * Returns a list of modules.
		 *
		 * @return array<string, mixed>
		 */
		public function modules() {

			if ( get_option( 'wp_analytify_modules' ) ) {
				return get_option( 'wp_analytify_modules' );
			}

			return array();
		}

		/**
		 * Reorder an associative list keyed by slug (e.g. pro add-ons or modules) for the Add-ons screen.
		 *
		 * @param array<string, mixed> $items      Rows keyed by slug.
		 * @param array<int, string>   $slug_order Preferred slug order.
		 * @return array<string, mixed>
		 */
		public function reorder_assoc_by_slug_list( $items, $slug_order ) {
			if ( empty( $items ) || ! is_array( $items ) ) {
				return $items;
			}
			if ( ! is_array( $slug_order ) ) {
				$slug_order = array();
			}
			$ordered = array();
			foreach ( $slug_order as $slug ) {
				if ( isset( $items[ $slug ] ) ) {
					$ordered[ $slug ] = $items[ $slug ];
				}
			}
			foreach ( $items as $slug => $row ) {
				if ( ! isset( $ordered[ $slug ] ) ) {
					$ordered[ $slug ] = $row;
				}
			}
			return $ordered;
		}

		/**
		 * Reorder API add-on objects (each has a slug) for the Add-ons screen.
		 *
		 * @param array<int, object> $extensions Objects with a slug property.
		 * @param array<int, string> $slug_order   Slugs to show first, in order.
		 * @return array<int, object>
		 */
		public function reorder_extension_list_by_slugs( $extensions, $slug_order ) {
			if ( empty( $extensions ) || ! is_array( $extensions ) ) {
				return $extensions;
			}
			if ( ! is_array( $slug_order ) ) {
				$slug_order = array();
			}
			$by_slug = array();
			$no_slug = array();
			foreach ( $extensions as $item ) {
				if ( is_object( $item ) && isset( $item->slug ) && '' !== $item->slug ) {
					$by_slug[ $item->slug ] = $item;
				} else {
					$no_slug[] = $item;
				}
			}
			$out = array();
			foreach ( $slug_order as $slug ) {
				if ( isset( $by_slug[ $slug ] ) ) {
					$out[] = $by_slug[ $slug ];
					unset( $by_slug[ $slug ] );
				}
			}
			foreach ( $by_slug as $item ) {
				$out[] = $item;
			}
			foreach ( $no_slug as $item ) {
				$out[] = $item;
			}
			return $out;
		}

		/**
		 * Check module status.
		 *
		 * @param string $slug Plugin slug.
		 * @return void
		 */
		public function check_module_status( $slug ) {

			if ( ! isset( $this->modules_list[ $slug ] ) || ! is_array( $this->modules_list[ $slug ] ) ) {
				return;
			}

			if ( 'pixels-tracking' === $slug && class_exists( 'WP_Analytify_Pro', false )
				&& function_exists( 'wp_analytify_pro_pixels_tracking_module_file_exists' )
				&& ! wp_analytify_pro_pixels_tracking_module_file_exists()
			) {
				$update_url = 'https://analytify.io/changelog/?utm_source=wp-analytify&utm_medium=addons&utm_campaign=pixels-pro-update';
				printf(
					/* translators: %1$s: Opening anchor tag, %2$s: Closing anchor tag. */
					esc_html__( '%1$s Update Analytify Pro %2$s', 'wp-analytify' ),
					'<a target="_blank" rel="noopener noreferrer" class="button-primary" href="' . esc_url( $update_url ) . '">',
					'</a>'
				);
				return;
			}

			$nonce = wp_create_nonce( $slug );

			$pro_module_cards = array(
				'wp-analytify-pmpro',
				'wp-analytify-learndash',
				'wp-analytify-lifterlms',
			);

			if ( in_array( $slug, $pro_module_cards, true ) && class_exists( 'WP_Analytify_Pro' ) && ! $this->pro_bundled_module_is_installed( $slug ) ) {
				$changelog = $this->get_analytify_pro_changelog_url( $slug );
				printf(
					/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag. */
					esc_html__( '%1$s Update Analytify Pro %2$s', 'wp-analytify' ),
					'<a class="button-primary" href="' . esc_url( $changelog ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				);
				return;
			}

			if ( 'active' === $this->modules_list[ $slug ]['status'] && $this->check_pro_support() ) {
				// translators: Deactivate add-on.
				printf( esc_html__( '%1$s Deactivate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-deactivate-module" data-slug="' . esc_attr( $slug ) . '" data-set-state="deactive" data-internal-module="true" data-nonce="' . esc_attr( $nonce ) . '" >', '</button>' );

			} elseif ( ! $this->modules_list[ $slug ]['status'] && $this->check_pro_support() ) {
				// translators: Activate add-on.
				printf( esc_html__( '%1$s Activate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-activate-module" data-slug="' . esc_attr( $slug ) . '" data-set-state="active" data-internal-module="true" data-nonce="' . esc_attr( $nonce ) . '" >', '</button>' );

			} elseif ( 'deactive' === $this->modules_list[ $slug ]['status'] && $this->check_pro_support() ) {
				// translators: Activate add-on.
				printf( esc_html__( '%1$s Activate add-on %2$s', 'wp-analytify' ), '<button type="button" class="button-primary analytify-module-state analytify-activate-module" data-slug="' . esc_attr( $slug ) . '" data-set-state="active" data-internal-module="true" data-nonce="' . esc_attr( $nonce ) . '" >', '</button>' );

			} else {
				// translators: Get add-on.
				printf( esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ), '<a type="button" class="button-primary analytify-activate-module" href=" ' . esc_url( $this->modules_list[ $slug ]['url'] ) . '?utm_source=analytify-lite" target="_blank">', '</a>' );
			}
		}

		/**
		 * Get addon icon URL.
		 *
		 * @param string $slug Plugin slug.
		 * @return string
		 */
		public function get_addon_icon( $slug ) {

			return ( defined( 'ANALYTIFY_PLUGIN_URL' ) ? ANALYTIFY_PLUGIN_URL : '' ) . '/assets/img/addons-svgs/' . $slug . '.svg';
		}

		/**
		 * Print one API-sourced add-on card (Add-ons admin screen).
		 *
		 * @param object     $extension Add-on object from addons().
		 * @param string|int $loop_key  Key for loaders(); use slug when known.
		 * @return void
		 */
		public function render_api_addon_card( $extension, $loop_key = '' ) {
			if ( ! is_object( $extension ) || ! isset( $extension->url ) || ! isset( $extension->title ) ) {
				return;
			}

			$icon_url = '';
			if ( isset( $extension->media ) && is_object( $extension->media ) &&
				isset( $extension->media->icon ) && is_object( $extension->media->icon ) &&
				isset( $extension->media->icon->url ) ) {
				$icon_url = (string) $extension->media->icon->url;
			}

			$excerpt = isset( $extension->excerpt ) ? $extension->excerpt : '';
			$slug    = isset( $extension->slug ) ? $extension->slug : '';
			$loader  = ( '' !== $loop_key && is_scalar( $loop_key ) ) ? (string) $loop_key : $slug;
			$class   = $slug ? $slug : $loader;
			$class   = sanitize_html_class( str_replace( '/', '-', $class ) );

			$title_class = 'analytify-addon-card__title';
			if ( ! empty( $icon_url ) ) {
				$title_class .= ' analytify-addon-card__title--has-icon';
			}

			echo '<div class="wp-extension ' . esc_attr( $class ) . '">';
			echo '<a target="_blank" href="' . esc_url( $extension->url ) . '">';
			echo '<h3 class="' . esc_attr( $title_class ) . '"';
			if ( ! empty( $icon_url ) ) {
				echo ' style="' . esc_attr( '--analytify-addon-icon: url(' . esc_url( $icon_url ) . ')' ) . '"';
			}
			echo '>' . esc_html( $extension->title ) . '</h3></a>';
			echo '<p>' . wp_kses_post( wpautop( wp_strip_all_tags( $excerpt ) ) ) . '</p><p>';
			$this->addons_status( $slug, $extension );
			echo '</p>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe internal HTML from loaders().
			echo $this->loaders( $loader, $icon_url );
			echo '</div>';
		}

		/**
		 * Print one internal module add-on card (Add-ons admin screen).
		 *
		 * @param array<string, mixed> $module Row from wp_analytify_modules.
		 * @return void
		 */
		public function render_module_addon_card( $module ) {
			if ( ! is_array( $module ) || empty( $module['slug'] ) ) {
				return;
			}

			$slug  = (string) $module['slug'];
			$title = isset( $module['title'] ) ? (string) $module['title'] : '';
			$url   = isset( $module['url'] ) ? (string) $module['url'] : '';
			$image = isset( $module['image'] ) ? (string) $module['image'] : '';
			$desc  = isset( $module['description'] ) ? (string) $module['description'] : '';

			echo '<div class="wp-extension ' . esc_attr( $slug ) . '">';
			echo '<a target="_blank" href="' . esc_url( $url ) . '">';
			echo '<h3 class="analytify-addon-card__title analytify-addon-card__title--has-icon"';
			echo ' style="' . esc_attr( '--analytify-addon-icon: url(' . esc_url( $image ) . ')' ) . '">';
			echo esc_html( $title );
			echo '</h3></a>';
			echo '<p>' . esc_html( $desc ) . '</p><p>';
			$this->check_module_status( $slug );
			echo '</p>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe internal HTML from loaders().
			echo $this->loaders( $title, $image );
			echo '</div>';
		}

		/**
		 * Slugs present in an API add-ons list.
		 *
		 * @param array<int, object> $addons Add-ons from addons().
		 * @return array<string, true>
		 */
		public function get_api_addon_slugs( $addons ) {
			$slugs = array();
			if ( ! is_array( $addons ) ) {
				return $slugs;
			}
			foreach ( $addons as $item ) {
				if ( is_object( $item ) && ! empty( $item->slug ) ) {
					$slugs[ (string) $item->slug ] = true;
				}
			}
			return $slugs;
		}

		/**
		 * Print one Pro-bundled add-on card (saved in wp_analytify_pro_addons).
		 *
		 * @param string               $slug Folder slug, e.g. wp-analytify-learndash.
		 * @param array<string, mixed> $meta  name, url, description, status.
		 * @return void
		 */
		public function render_pro_addon_card( $slug, $meta ) {
			if ( ! is_array( $meta ) || empty( $meta['name'] ) ) {
				return;
			}

			$url  = isset( $meta['url'] ) ? (string) $meta['url'] : '';
			$desc = isset( $meta['description'] ) ? (string) $meta['description'] : '';
			$name = (string) $meta['name'];
			$icon = $this->get_addon_icon( $slug );

			$use_pro_actions = class_exists( 'WP_Analytify_Pro' ) && defined( 'ANALYTIFY_PRO_VERSION' ) &&
				version_compare( ANALYTIFY_PRO_VERSION, '6.0.0', '>=' );

			echo '<div class="wp-extension ' . esc_attr( $name ) . '">';
			echo '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $url ) . '">';
			echo '<h3 class="analytify-addon-card__title analytify-addon-card__title--has-icon"';
			echo ' style="' . esc_attr( '--analytify-addon-icon: url(' . esc_url( $icon ) . ')' ) . '">';
			echo esc_html( $name );
			echo '</h3></a>';
			echo '<p>' . wp_kses_post( wpautop( wp_strip_all_tags( $desc ) ) ) . '</p><p>';
			if ( $use_pro_actions ) {
				$this->pro_addons_status( $slug, isset( $meta['status'] ) ? $meta['status'] : 'inactive' );
			} else {
				$buy_url = '' !== $url ? $url : 'https://analytify.io/pricing';
				printf(
					/* translators: %1$s: Opening anchor tag, %2$s: Closing anchor tag for the purchase link. */
					esc_html__( '%1$s Get this add-on %2$s', 'wp-analytify' ),
					'<a target="_blank" rel="noopener noreferrer" class="button-primary" href="' . esc_url( $buy_url ) . '">',
					'</a>'
				);
			}
			echo '</p>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe internal HTML from loaders().
			echo $this->loaders( $name, $icon );
			echo '</div>';
		}

		/**
		 *  Loaders HTML.
		 *
		 * @version 7.0.5
		 * @param string $addon Addon name.
		 * @param string $logo Logo url.
		 * @return string $html HTML markup string.
		 */
		public function loaders( $addon, $logo ) {

			$html  = '<div class="analytify-addons-loader-container"><div class="wp-analytify-addon-enable analytify-loader" style="display:none !important;">
						<div class="analytify-logo-container">
						<img src="' . $logo . '" alt="' . $addon . '">
						<svg class="circular-loader" viewBox="25 25 50 50" >
						<circle class="loader-path" cx="50" cy="50" r="18" fill="none" stroke="#d8d8d8" stroke-width="1" />
						</svg>
						</div>
						<p>' . __( 'Activating...', 'wp-analytify' ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-install analytify-loader activated" style="display:none !important;">
						<svg class="circular-loader2" viewBox="25 25 50 50" >
						<circle class="loader-path2" cx="50" cy="50" r="18" fill="none" stroke="#00c853" stroke-width="1" />
						</svg>
						<div class="checkmark draw"></div>
						<p>' . __( 'Activated', 'wp-analytify' ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-uninstalling analytify-loader activated" style="display:none !important;">
						<div class="analytify-logo-container">
						<img src="' . $logo . '" alt="' . esc_attr( $addon ) . '">
						<svg class="circular-loader" viewBox="25 25 50 50">
							<circle class="loader-path" cx="50" cy="50" r="18" fill="none" stroke="#d8d8d8" stroke-width="1" />
						</svg>
						</div>
						<p>' . __( 'Deactivating...', 'wp-analytify' ) . '</p>
					  </div>';
			$html .= '<div class="wp-analytify-addon-uninstall analytify-loader activated" style="display:none !important;">
						<svg class="circular-loader2" viewBox="25 25 50 50" >
						<circle class="loader-path2" cx="50" cy="50" r="18" fill="none" stroke="#ff0000" stroke-width="1" />
						</svg>
						<div class="checkmark draw"></div>
						<p>' . __( 'Deactivated', 'wp-analytify' ) . '</p>
						</div>';
			$html .= '<div class="wp-analytify-addon-wrong activated analytify-loader" style="display:none !important;">
						<svg class="checkmark_login" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
						<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"></circle>
						<path class="checkmark__check" stroke="#ff0000" fill="none" d="M16 16 36 36 M36 16 16 36"></path>
						</svg>
						<p>' . __( 'Something Went Wrong', 'wp-analytify' ) . '</p>
						</div></div>';

			return $html;
		}
	}
}

$obj_wp_analytify_addons = new WP_Analytify_Addons();

// Clear transient if requested for debugging (add ?clear_addons_cache=1 to URL).
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Cache clearing is a safe admin action, nonce verified via capability check.
if ( isset( $_GET['clear_addons_cache'] ) && '1' === $_GET['clear_addons_cache'] && current_user_can( 'manage_options' ) ) {
	delete_transient( 'analytify_api_addons' );
}

$addons     = $obj_wp_analytify_addons->addons();
$pro_addons = get_option( 'wp_analytify_pro_addons' );
$modules    = $obj_wp_analytify_addons->modules();

$pro_addons_slug_order = apply_filters(
	'analytify_pro_addons_display_slug_order',
	array(
		'wp-analytify-forms',
		'wp-analytify-authors',
		'wp-analytify-goals',
		'wp-analytify-email',
		'wp-analytify-campaigns',
		'wp-analytify-edd',
		'wp-analytify-woocommerce',
	)
);
if ( is_array( $pro_addons ) ) {
	$pro_addons = $obj_wp_analytify_addons->reorder_assoc_by_slug_list( $pro_addons, $pro_addons_slug_order );
}

$api_addons_slug_order = apply_filters(
	'analytify_api_addons_display_slug_order',
	array( 'analytify-analytics-dashboard-widget' )
);
if ( is_array( $addons ) ) {
	$addons = $obj_wp_analytify_addons->reorder_extension_list_by_slugs( $addons, $api_addons_slug_order );
}

$modules_slug_order = apply_filters(
	'analytify_modules_display_slug_order',
	array(
		'pixels-tracking',
		'wp-analytify-pmpro',
		'wp-analytify-learndash',
		'wp-analytify-lifterlms',
		'custom-dimensions',
		'amp',
		'events-tracking',
		'google-ads-tracking',
	)
);
if ( is_array( $modules ) ) {
	$modules = $obj_wp_analytify_addons->reorder_assoc_by_slug_list( $modules, $modules_slug_order );
}

// When Pro add-ons load first, pull GA4 widget out of the API list so it can render 3rd (after Authors).
$ga4_dashboard_addon = null;
$ga4_slug            = 'analytify-analytics-dashboard-widget';
$use_pro_addons_grid = class_exists( 'WP_Analytify_Pro' ) && defined( 'ANALYTIFY_PRO_VERSION' ) &&
	version_compare( ANALYTIFY_PRO_VERSION, '6.0.0', '>=' ) && ! empty( $pro_addons );

if ( $use_pro_addons_grid && is_array( $addons ) ) {
	foreach ( $addons as $idx => $extension ) {
		if ( is_object( $extension ) && isset( $extension->slug ) && $ga4_slug === $extension->slug ) {
			$ga4_dashboard_addon = $extension;
			unset( $addons[ $idx ] );
			break;
		}
	}
	$addons = array_values( $addons );
}

// When Pro add-ons load first, pull Pixels out of the module list so it can sit after WooCommerce.
$pixels_module_after_woo = null;
$pixels_slug             = 'pixels-tracking';
if ( $use_pro_addons_grid && is_array( $modules ) && is_array( $pro_addons ) &&
	isset( $pro_addons['wp-analytify-woocommerce'] ) ) {
	if ( isset( $modules[ $pixels_slug ] ) && is_array( $modules[ $pixels_slug ] ) ) {
		$pixels_module_after_woo = $modules[ $pixels_slug ];
		unset( $modules[ $pixels_slug ] );
	} else {
		foreach ( $modules as $mkey => $mrow ) {
			if ( is_array( $mrow ) && isset( $mrow['slug'] ) && $pixels_slug === $mrow['slug'] ) {
				$pixels_module_after_woo = $mrow;
				unset( $modules[ $mkey ] );
				break;
			}
		}
	}
}

// When Pixels renders after WooCommerce, keep PMPro / LearnDash / LifterLMS module cards next to it.
$lms_modules_after_pixels = array();
$lms_slugs_after_pixels   = array(
	'wp-analytify-pmpro',
	'wp-analytify-learndash',
	'wp-analytify-lifterlms',
);
if ( $use_pro_addons_grid && is_array( $modules ) && $pixels_module_after_woo ) {
	foreach ( $lms_slugs_after_pixels as $lms_slug ) {
		$found_lms = null;
		$found_key = null;
		if ( isset( $modules[ $lms_slug ] ) && is_array( $modules[ $lms_slug ] ) ) {
			$found_lms = $modules[ $lms_slug ];
			$found_key = $lms_slug;
		} else {
			foreach ( $modules as $mkey => $mrow ) {
				if ( is_array( $mrow ) && isset( $mrow['slug'] ) && $lms_slug === $mrow['slug'] ) {
					$found_lms = $mrow;
					$found_key = $mkey;
					break;
				}
			}
		}
		if ( null !== $found_lms && null !== $found_key ) {
			$lms_modules_after_pixels[] = $found_lms;
			unset( $modules[ $found_key ] );
		}
	}
}

$analytify_screen = get_current_screen() ? get_current_screen()->base : '';
$version          = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ( defined( 'ANALYTIFY_VERSION' ) ? ANALYTIFY_VERSION : '1.0.0' ); ?>

<div class="wpanalytify analytify-dashboard-nav">
	<div class="wpb_plugin_wraper">
		<div class="wpb_plugin_header_wraper">
			<div class="graph"></div>
			<div class="wpb_plugin_header">
				<div class="wpb_plugin_header_title"></div>
				<div class="wpb_plugin_header_info">
					<a href="https://analytify.io/changelog/" target="_blank" class="btn">View Changelog</a>
				</div>
				<div class="wpb_plugin_header_logo">
					<img src="<?php echo esc_url( ( defined( 'ANALYTIFY_PLUGIN_URL' ) ? ANALYTIFY_PLUGIN_URL : '' ) . 'assets/img/logo.svg' ); ?>" alt="Analytify">
				</div>
			</div>
		</div>

		<div class="analytify-settings-body-container">
			<div class="wpb_plugin_body_wraper">
				<div class="wpb_plugin_body">
					<div class="wpa-tab-wrapper">
						<ul class="analytify_nav_tab_wrapper nav-tab-wrapper">
							<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=analytify-addons' ) ); ?>"
									class="analytify_nav_tab <?php echo ( 'analytify_page_analytify-addons' === $analytify_screen ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Addons', 'wp-analytify' ); ?></a>
							</li>
							<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=analytify-settings#wp-analytify-license' ) ); ?>"
									class="analytify_nav_tab">License</a></li>
						</ul>
					</div>

					<div class="wpb_plugin_tabs_content analytify-dashboard-content">
						<div class="wrap analytify-addons-wrapper">

							<h2 class='opt-title'><span id='icon-options-general' class='analytics-options'><img
										src="<?php echo esc_url( plugins_url( '../assets/img/wp-analytics-logo.png', __FILE__ ) ); ?>"
										alt="analytics"></span>
								<?php esc_html_e( 'Powerful Add-ons to Get More from Analytify.', 'wp-analytify' ); ?>
							</h2>

							<div class="tabwrapper analytify-addons-grid">

								<?php

								if ( class_exists( 'WP_Analytify_Pro' ) && defined( 'ANALYTIFY_PRO_VERSION' ) && version_compare( ANALYTIFY_PRO_VERSION, '6.0.0', '>=' ) && ! empty( $pro_addons ) ) {
									foreach ( $pro_addons as $slug => $meta ) :
										$obj_wp_analytify_addons->render_pro_addon_card( $slug, $meta );
										if ( 'wp-analytify-authors' === $slug && $ga4_dashboard_addon ) {
											$obj_wp_analytify_addons->render_api_addon_card(
												$ga4_dashboard_addon,
												$ga4_slug
											);
										}
										if ( 'wp-analytify-woocommerce' === $slug && $pixels_module_after_woo ) {
											$obj_wp_analytify_addons->render_module_addon_card( $pixels_module_after_woo );
											foreach ( $lms_modules_after_pixels as $lms_module_row ) {
												$obj_wp_analytify_addons->render_module_addon_card( $lms_module_row );
											}
										}
									endforeach;
									foreach ( $addons as $name => $extension ) :
										$obj_wp_analytify_addons->render_api_addon_card( $extension, $name );
									endforeach;

								} else {
									foreach ( $addons as $name => $extension ) :
										$obj_wp_analytify_addons->render_api_addon_card( $extension, $name );
									endforeach;

									// Pro-only rows (LearnDash, LifterLMS, PMPro, …) live in wp_analytify_pro_addons; the API
									// list omits them when Pro is off — still show cards using saved option + purchase links.
									$api_addon_slugs = $obj_wp_analytify_addons->get_api_addon_slugs( $addons );
									if ( ! empty( $pro_addons ) && is_array( $pro_addons ) ) {
										$pro_for_lite         = $obj_wp_analytify_addons->reorder_assoc_by_slug_list(
											$pro_addons,
											$pro_addons_slug_order
										);
										$pro_rows_now_modules = array(
											'wp-analytify-pmpro',
											'wp-analytify-learndash',
											'wp-analytify-lifterlms',
										);
										foreach ( $pro_for_lite as $slug => $meta ) {
											if ( ! is_array( $meta ) || isset( $api_addon_slugs[ $slug ] ) ) {
												continue;
											}
											if ( in_array( $slug, $pro_rows_now_modules, true ) ) {
												continue;
											}
											$obj_wp_analytify_addons->render_pro_addon_card( $slug, $meta );
										}
									}

									// Show message if no valid addons found.
									$valid_addons_count = 0;
									foreach ( $addons as $extension ) {
										if ( is_object( $extension ) && isset( $extension->url ) && isset( $extension->title ) ) {
											++$valid_addons_count;
										}
									}
									if ( ! empty( $pro_addons ) && is_array( $pro_addons ) ) {
										foreach ( $pro_addons as $slug => $meta ) {
											if ( is_array( $meta ) && ! isset( $api_addon_slugs[ $slug ] ) ) {
												++$valid_addons_count;
											}
										}
									}

									if ( 0 === $valid_addons_count && empty( $modules ) ) {
										?>
										<div class="notice notice-error">
											<p><?php esc_html_e( 'Unable to load addons. Please check your internet connection and try refreshing the page.', 'wp-analytify' ); ?></p>
										</div>
										<?php
									}
								}
								?>

								<?php
								foreach ( $modules as $module ) :
									$obj_wp_analytify_addons->render_module_addon_card( $module );
								endforeach;
								?>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
