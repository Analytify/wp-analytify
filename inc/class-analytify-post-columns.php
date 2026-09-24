<?php
/**
 * Analytify Post Columns Class
 *
 * @package WP_Analytify
 * @since 8.0.0
 * @version 8.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Analytify_Post_Columns' ) ) {

	/**
	 * Class Analytify_Post_Columns
	 *
	 * Handles the display of the Sessions column in the posts list table.
	 *
	 * @since 8.0.0
	 */
	class Analytify_Post_Columns {

		/**
		 * Constructor
		 *
		 * @since 8.0.0
		 */
		public function __construct() {
			// Add columns for default post type.
			add_filter( 'manage_edit-post_columns', array( $this, 'wpa_add_column' ), 99 );
			add_action( 'manage_posts_custom_column', array( $this, 'wpa_render_column' ), 10, 2 );

			// Add columns for pages.
			add_filter( 'manage_edit-page_columns', array( $this, 'wpa_add_column' ), 99 );
			add_action( 'manage_pages_custom_column', array( $this, 'wpa_render_column' ), 10, 2 );

			// Add columns for all registered custom post types.
			add_action( 'init', array( $this, 'wpa_register_cpt_columns' ), 20 );
			add_action( 'restrict_manage_posts', array( $this, 'wpa_add_date_filter' ) );
			add_action( 'admin_footer', array( $this, 'wpa_footer_scripts' ) );
			add_action( 'wp_ajax_analytify_get_post_sessions', array( $this, 'wpa_ajax_get_sessions' ) );
		}

		/**
		 * Register column hooks for all custom post types.
		 *
		 * @since 8.0.0
		 * @return void
		 */
		public function wpa_register_cpt_columns() {
			$post_types = get_post_types(
				array(
					'public'   => true,
					'_builtin' => false,
				),
				'names'
			);

			foreach ( $post_types as $post_type ) {
				// Add column filter for custom post types (using the standard filter).
				add_filter( "manage_edit-{$post_type}_columns", array( $this, 'wpa_add_column' ), 99 );
				// Add column rendering action for custom post types.
				add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'wpa_render_column' ), 10, 2 );
			}
		}

		/**
		 * Add the column to the posts table.
		 *
		 * @since 8.0.0
		 * @version 8.1.1
		 * @param array $columns Existing columns.
		 * @return array Modified columns.
		 */
		public function wpa_add_column( $columns ) {
			$screen = get_current_screen();
			if ( ! $screen || 'edit' !== $screen->base ) {
				$columns['analytify_sessions'] = __( 'Sessions', 'wp-analytify' );
				return $columns;
			}

			/**
			 * Filters the default date range for session data in post columns.
			 *
			 * This filter allows you to customize the default date range used when displaying
			 * session data in the post list table columns. The date range determines how far back
			 * The analytics data is retrieved from Google Analytics.
			 *
			 * @since 8.1.1
			 *
			 * @param string $default_range The default date range. Default '30days'.
			 *                              Possible values: '7days', '30days', '90days', '1year', 'alltime'.
			 * @param string $post_type     The current post type being displayed.
			 */
			$default_range = apply_filters( 'analytify_session_date_range', '30days', $screen->post_type );

			// Allow URL parameter override for backward compatibility.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for display purposes
			$date_range                    = isset( $_GET['analytify_sessions_range'] ) ? sanitize_text_field( wp_unslash( $_GET['analytify_sessions_range'] ) ) : sanitize_text_field( $default_range );
			$label                         = $this->wpa_get_date_range_label( $date_range );
			$columns['analytify_sessions'] = __( 'Sessions', 'wp-analytify' ) . ' (' . $label . ')';
			return $columns;
		}

		/**
		 * Get label for date range.
		 *
		 * @since 8.0.0
		 * @param string $range Date range key.
		 * @return string Label for the date range.
		 */
		private function wpa_get_date_range_label( $range ) {
			$labels = array(
				'7days'   => __( '7 Days', 'wp-analytify' ),
				'30days'  => __( '30 Days', 'wp-analytify' ),
				'90days'  => __( '90 Days', 'wp-analytify' ),
				'1year'   => __( 'Last Year', 'wp-analytify' ),
				'alltime' => __( 'All Time', 'wp-analytify' ),
			);
			return isset( $labels[ $range ] ) ? $labels[ $range ] : $labels['30days'];
		}

		/**
		 * Add styling for sessions column.
		 *
		 * @since 8.0.0
		 * @version 8.1.1
		 * @param string $post_type Post type.
		 * @return void
		 */
		public function wpa_add_date_filter( $post_type ) {
			$screen = get_current_screen();
			if ( ! $screen || 'edit' !== $screen->base ) {
				return;
			}

			// Only show on public post types.
			$post_type_obj = get_post_type_object( $post_type );
			if ( ! $post_type_obj || ! $post_type_obj->public ) {
				return;
			}
			?>
			<style>
				.column-analytify_sessions {
					width: 140px;
					min-width: 140px;
					text-align: center !important;
				}
				th.column-analytify_sessions {
					white-space: nowrap;
				}
			</style>
			<?php
		}

		/**
		 * Render the column content.
		 *
		 * @since 8.0.0
		 * @param string $column Column name.
		 * @param int    $post_id Post ID.
		 * @return void
		 */
		public function wpa_render_column( $column, $post_id ) {
			if ( 'analytify_sessions' === $column ) {
				// Use static array to prevent duplicate rendering for the same post.
				static $rendered_posts = array();
				$key                   = $post_id . '_' . $column;

				if ( isset( $rendered_posts[ $key ] ) ) {
					return; // Already rendered for this post.
				}
				$rendered_posts[ $key ] = true;

				echo '<span class="analytify-sessions-wrapper" data-post-id="' . esc_attr( $post_id ) . '"><span class="spinner is-active" style="float:none;margin:0;"></span></span>';
			}
		}

		/**
		 * Output JavaScript in the admin footer to handle AJAX.
		 *
		 * @since 8.0.0
		 * @return void
		 */
		public function wpa_footer_scripts() {
			$screen = get_current_screen();

			// We want this on the 'edit' screen for any post type.
			if ( ! $screen || 'edit' !== $screen->base || ! isset( $screen->post_type ) ) {
				return;
			}

			// Only show on public post types.
			$post_type = get_post_type_object( $screen->post_type );
			if ( ! $post_type || ! $post_type->public ) {
				return;
			}

			// Prevent duplicate script output.
			static $script_output = false;
			if ( $script_output ) {
				return;
			}
			$script_output = true;

			// Get the filtered default date range to pass to JavaScript.
			$default_range = apply_filters( 'analytify_session_date_range', '30days', $screen->post_type );
			?>
		<script>
	(function($) {
		// Function to get date range from URL parameter or default from PHP filter
		function wpaGetDateRange() {
			var urlParams = new URLSearchParams(window.location.search);
			// Use PHP-filtered default instead of hardcoded '30days'
			return urlParams.get('analytify_sessions_range') || '<?php echo esc_js( $default_range ); ?>';
		}

		// Function to load sessions data
		function wpaLoadSessionsData() {
			// Reset all wrappers
			$('.analytify-sessions-wrapper').each(function() {
				$(this).html('<span class="spinner is-active" style="float:none;margin:0;"></span>').data('updated', false);
			});

			var postIds = [];
			var seenIds = {};
			$('.analytify-sessions-wrapper').each(function() {
				var postId = $(this).data('post-id');
				// Prevent duplicate post IDs.
				if ( postId && ! seenIds[postId] ) {
					postIds.push(postId);
					seenIds[postId] = true;
				}
			});

			if ( postIds.length > 0 ) {
				var dateRange = wpaGetDateRange();
				// Process in chunks of 5 to avoid timeouts.
				var chunkSize = 5;
				for (var i = 0; i < postIds.length; i += chunkSize) {
					var chunk = postIds.slice(i, i + chunkSize);
					$.post(ajaxurl, {
						action: 'analytify_get_post_sessions',
						post_ids: chunk,
						date_range: dateRange,
						nonce: '<?php echo esc_js( wp_create_nonce( 'analytify_sessions_nonce' ) ); ?>'
					}, function(response) {
						if ( response.success ) {
							$.each(response.data, function(id, sessions) {
								// Only update the first matching element to prevent duplicates.
								var $wrapper = $('.analytify-sessions-wrapper[data-post-id="' + id + '"]').first();
								if ( $wrapper.length && ! $wrapper.data('updated') ) {
									$wrapper.html(sessions).data('updated', true);
								}
							});
						}
					});
				}
			}
		}

		$(document).ready(function() {
			// Use a unique identifier to prevent duplicate processing.
			var processedKey = 'analytify_sessions_processed';
			if ( window[processedKey] ) {
				return;
			}
			window[processedKey] = true;

			// Load sessions on page load
			wpaLoadSessionsData();
		});
	})(jQuery);
	</script>
			<?php
		}

		/**
		 * AJAX handler to fetch sessions.
		 *
		 * @since 8.0.0
		 * @return void
		 */
		/**
		 * Convert date range to GA4 date format.
		 *
		 * @since 8.0.0
		 * @param string $range Date range key.
		 * @param int    $post_id Post ID for alltime range.
		 * @return array Array with 'start' and 'end' dates.
		 */
		private function wpa_get_date_range( $range, $post_id = 0 ) {
			$end_date = 'today';

			switch ( $range ) {
				case '7days':
					$start_date = '7daysAgo';
					break;
				case '30days':
					$start_date = '30daysAgo';
					break;
				case '90days':
					$start_date = '90daysAgo';
					break;
				case '1year':
					$start_date = '365daysAgo';
					break;
				case 'alltime':
					// For all time, use post publish date or a very old date.
					if ( $post_id > 0 ) {
						$post_obj = get_post( $post_id );
						if ( $post_obj ) {
							$post_date = get_the_time( 'Y-m-d', $post_obj );
							// GA4 data typically starts from 2005, so use that as minimum.
							if ( get_the_time( 'Y', $post_obj ) < 2005 ) {
								$start_date = '2005-01-01';
							} else {
								$start_date = $post_date;
							}
						} else {
							$start_date = '2005-01-01';
						}
					} else {
						$start_date = '2005-01-01';
					}
					break;
				default:
					$start_date = '30daysAgo';
			}

			return array(
				'start' => $start_date,
				'end'   => $end_date,
			);
		}

		/**
		 * AJAX handler to fetch sessions.
		 *
		 * @since 8.0.0
		 * @return void
		 */
		public function wpa_ajax_get_sessions() {
			check_ajax_referer( 'analytify_sessions_nonce', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Unauthorized' );
			}

			$post_ids   = isset( $_POST['post_ids'] ) ? array_map( 'intval', $_POST['post_ids'] ) : array();
			$date_range = isset( $_POST['date_range'] ) ? sanitize_text_field( wp_unslash( $_POST['date_range'] ) ) : '30days';
			$data       = array();

			if ( ! class_exists( 'WP_Analytify' ) || ! class_exists( 'WPANALYTIFY_Utils' ) ) {
				wp_send_json_error( 'Required classes missing' );
			}

			// Use the main instance which has the authenticated client.
			$analytify = WP_Analytify::get_instance();

			$property_id = WPANALYTIFY_Utils::get_reporting_property();

			if ( empty( $property_id ) ) {
				wp_send_json_error( 'No Property ID found' );
			}

			foreach ( $post_ids as $id ) {
				// Use slug only, avoiding date-based permalinks.
				$slug = get_post_field( 'post_name', $id );
				$path = '/' . $slug . '/';

				// Get date range for this post.
				$dates = $this->wpa_get_date_range( $date_range, $id );

				// Fetch from GA4.
				$filters = array(
					'logic'   => 'AND',
					'filters' => array(
						array(
							'type'       => 'dimension',
							'name'       => 'pagePath',
							'match_type' => 'EXACT',
							'value'      => $path,
						),
						// Exclude (not set) as per core logic.
						array(
							'type'           => 'dimension',
							'name'           => 'pagePath',
							'match_type'     => 'PARTIAL_REGEXP',
							'value'          => '(not set)',
							'not_expression' => true,
						),
					),
				);

				try {
					// Include date range in cache key to prevent cache conflicts.
					$cache_key = 'post_sessions_' . $id . '_' . $date_range;

					// get_reports handles caching internally using 'analytify_transient_' prefix.
					$report = $analytify->get_reports(
						$cache_key,
						array( 'sessions' ),
						$dates,
						array( 'pagePath' ), // dimensions.
						array(
							'type'  => 'metric',
							'name'  => 'sessions',
							'order' => 'desc',
						), // order.
						$filters
					);

					$sessions = 0;
					if ( isset( $report['rows'][0]['sessions'] ) ) {
						$sessions = $report['rows'][0]['sessions'];
					}

					// Check for error in report response structure.
					if ( isset( $report['error'] ) && ! empty( $report['error'] ) ) {
						$sessions = 'Error';
					}
				} catch ( Exception $e ) {
					$sessions = 'Error';
				}

				$data[ $id ] = $sessions;
			}

			wp_send_json_success( $data );
		}
	}

	new Analytify_Post_Columns();
}
