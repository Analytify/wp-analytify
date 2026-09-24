<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Utils GA Trait
 *
 * Contains Google Analytics (GA) utility functions for the Analytify plugin.
 * This trait separates GA utility logic from the main utils class,
 * providing helper functions for GA4 streams, properties, and analytics data.
 *
 * @package WP_Analytify
 * @subpackage Utils
 * @since 8.0.0
 */

trait Analytify_Utils_GA {

	/**
	 * Fetch GA4 streams data.
	 *
	 * Retrieves and formats GA4 measurement streams data for the
	 * configured profile, handling both new and legacy data structures.
	 *
	 * @since 7.0.5
	 * @version 7.0.5
	 * @return array<string, mixed> Array of measurement IDs mapped to stream names.
	 */
	public static function fetch_ga4_streams() {
		$_analytify_profile = get_option( 'wp-analytify-profile' );
		$post_profile       = isset( $_analytify_profile['profile_for_posts'] ) ? $_analytify_profile['profile_for_posts'] : '';
		$post_profile       = explode( ':', $post_profile )[1] ?? false;
		$streams            = array();

		if ( $post_profile ) {
			$properties          = get_option( 'analytify-ga4-streams' );
			$streams_data        = $properties[ $post_profile ] ?? array();
			$using_old_structure = false;

			foreach ( $streams_data as $stream ) {
				if ( ! is_array( $stream ) ) {
					$using_old_structure = true;
					break;
				}
				$streams[ $stream['measurement_id'] ] = $stream['stream_name'];
			}

			if ( $using_old_structure && isset( $streams_data['measurement_id'] ) ) {
				$streams[ $streams_data['measurement_id'] ] = $streams_data['stream_name'];
			}
		}
		return $streams;
	}

	/**
	 * Get current GA mode.
	 *
	 * Returns the current Google Analytics mode being used.
	 * Always returns 'ga4' as Universal Analytics (UA) is deprecated and no longer supported.
	 *
	 * @since 7.0.5
	 * @version 7.0.5
	 * @param string $property_for Property context for mode determination (reserved for future use).
	 * @return string GA mode ('ga4').
	 */
	public static function get_ga_mode( $property_for = 'profile_for_dashboard' ) {
		unset( $property_for ); // Reserved for future use.
		// Universal Analytics (UA) is deprecated and no longer supported by Google.
		// Only GA4 is supported going forward.
		return 'ga4';
	}

	/**
	 * Get reporting property ID.
	 *
	 * Retrieves the property ID used for reporting, extracting it
	 * from the profile configuration and handling GA4 format.
	 *
	 * @since 7.0.5
	 * @version 7.0.5
	 * @return string Property ID for reporting.
	 */
	public static function get_reporting_property() {
		$property_id = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile' );
		if ( false !== strpos( $property_id, 'ga4:' ) ) {
			$property_id = explode( ':', $property_id )[1];
		}
		return $property_id;
	}

	/**
	 * Generate dashboard subtitle section.
	 *
	 * Creates HTML for displaying property information in the dashboard
	 * subtitle, including stream name and URL with proper formatting.
	 *
	 * @since 7.0.5
	 * @version 7.0.5
	 * @return void
	 */
	public static function dashboard_subtitle_section() {
		$name = WP_ANALYTIFY_FUNCTIONS::ga_reporting_property_info( 'stream_name' );
		$url  = WP_ANALYTIFY_FUNCTIONS::ga_reporting_property_info( 'url' );

		if ( $name && $url ) {
			?>
			<span class="analytify_stats_of">
				<a href="<?php echo esc_url( $url ); ?>" target="_blank">
					<?php echo esc_html( $url ); ?>
				</a>
				(<?php echo esc_html( $name ); ?>)
			</span>
			<?php
		}
	}

	/**
	 * Get property URL for analytics.
	 *
	 * Retrieves the website URL associated with the current property,
	 * handling both GA4 and Universal Analytics formats.
	 *
	 * @version 7.0.5
	 * @return string Property website URL.
	 */
	public static function get_property_url() {
		return ( 'ga4' === self::get_ga_mode() )
			? WP_ANALYTIFY_FUNCTIONS::ga_reporting_property_info( 'url' )
			: WP_ANALYTIFY_FUNCTIONS::search_profile_info( self::get_reporting_property(), 'websiteUrl' );
	}

	/**
	 * Process GA4 social statistics.
	 *
	 * Formats raw social statistics data from GA4 into a structured
	 * format for display, mapping session sources to session counts.
	 *
	 * @version 7.0.5
	 * @param array<string, mixed> $social_stats_raw Raw social statistics from GA4.
	 * @return array<string, mixed> Formatted social statistics.
	 */
	public static function ga4_social_stats( $social_stats_raw ) {
		$social_network = array(
			array(
				'sessionSource' => 'facebook',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'instagram',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'wordpress',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'linkedin',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'youtube',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'twitter',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'pinterest',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'yelp',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'tumblr',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'quora',
				'sessions'      => 0,
			),
			array(
				'sessionSource' => 'reddit',
				'sessions'      => 0,
			),
		);

		foreach ( $social_stats_raw as $stat ) {
			foreach ( $social_network as &$item ) {
				if ( false !== strpos( $stat['sessionSource'], $item['sessionSource'] ) ) {
					$item['sessions'] += $stat['sessions'];
					break;
				}
			}
		}

		$social_network = array_filter(
			$social_network,
			function ( $item ) {
				return isset( $item['sessions'] ) && is_numeric( $item['sessions'] ) && (int) $item['sessions'] > 0;
			}
		);

		usort(
			$social_network,
			function ( $a, $b ) {
				return $b['sessions'] - $a['sessions'];
			}
		);

		return $social_network;
	}

	/**
	 * Dashboard GA4 custom dimensions created on property connect.
	 *
	 * @since 9.1.0
	 * @version 9.1.0
	 *
	 * @return array<int, array{parameter_name: string, display_name: string, scope: string}>
	 */
	public static function required_dashboard_dimensions(): array {
		return array(
			array(
				'parameter_name' => 'page_title',
				'display_name'   => 'Page Title',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'page_path',
				'display_name'   => 'Page Path',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'user_type',
				'display_name'   => 'User Type',
				'scope'          => 'USER',
			),
			array(
				'parameter_name' => 'device_category',
				'display_name'   => 'Device Category',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'country',
				'display_name'   => 'Country',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'source',
				'display_name'   => 'Source',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'medium',
				'display_name'   => 'Medium',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'campaign',
				'display_name'   => 'Campaign',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'landing_page',
				'display_name'   => 'Landing Page',
				'scope'          => 'EVENT',
			),
			array(
				'parameter_name' => 'exit_page',
				'display_name'   => 'Exit Page',
				'scope'          => 'EVENT',
			),
		);
	}

	/**
	 * Get required dimensions for GA4.
	 *
	 * Register these in GA4 Admin custom definitions (Event scope) so reports work.
	 * Enhanced Tel Link Analytics uses wpa_category, wpa_link_action, wpa_link_label,
	 * wpa_device_category, wpa_click_hour, wpa_click_day, wpa_traffic_source, plus the
	 * standard dimension country (not listed here).
	 *
	 * @since 1.0.0
	 * @version 9.1.0
	 *
	 * @return array
	 */
	public static function required_dimensions(): array {
		$dimensions = array(
			array(
				'parameter_name' => 'wpa_author',
				'display_name'   => 'WPA Author',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_post_type',
				'display_name'   => 'WPA Post Type',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_published_at',
				'display_name'   => 'WPA Published At',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_category',
				'display_name'   => 'WPA Category',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_tags',
				'display_name'   => 'WPA Tags',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_user_id',
				'display_name'   => 'WPA WP User ID',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_logged_in',
				'display_name'   => 'WPA Logged In',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_seo_score',
				'display_name'   => 'WPA SEO Score',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_focus_keyword',
				'display_name'   => 'WPA Focus Keyword',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_link_action',
				'display_name'   => 'WPA Link Action',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_label',
				'display_name'   => 'WPA Label',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_affiliate_label',
				'display_name'   => 'WPA Affiliate Label',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_email_address',
				'display_name'   => 'WPA Email Address',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_form_id',
				'display_name'   => 'WPA Form Id',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_is_affiliate_link',
				'display_name'   => 'WPA Is Affiliate Link',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_link_label',
				'display_name'   => 'WPA Link Label',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_link_text',
				'display_name'   => 'WPA Link Text',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_outbound',
				'display_name'   => 'WPA Outbound',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_tel_number',
				'display_name'   => 'WPA Tel Number',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_scroll_depth',
				'display_name'   => 'WPA Scroll Depth',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_percentage',
				'display_name'   => 'WPA Percentage',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_post_category',
				'display_name'   => 'WPA Post Category',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_video_provider',
				'display_name'   => 'WPA Video Provider',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_video_action',
				'display_name'   => 'WPA Video Action',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_video_duration',
				'display_name'   => 'WPA Video Duration',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_video_title',
				'display_name'   => 'WPA Video Title',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_archive_type',
				'display_name'   => 'WPA Archive Type',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_llms_post_type',
				'display_name'   => 'WPA LLMS Post Type',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_llms_instructor',
				'display_name'   => 'WPA LLMS Instructor',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_device_category',
				'display_name'   => 'WPA Device Category',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_click_hour',
				'display_name'   => 'WPA Click Hour',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_click_day',
				'display_name'   => 'WPA Click Day',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_traffic_source',
				'display_name'   => 'WPA Traffic Source',
				'scope'          => 1,
			),
			array(
				'parameter_name' => 'wpa_country_code',
				'display_name'   => 'WPA Country Code',
				'scope'          => 1,
			),

		);

		$result = array();
		foreach ( $dimensions as $dimension ) {
			$result[ $dimension['parameter_name'] ] = $dimension;
		}
		return $result;
	}

	/**
	 * Get all stats link.
	 *
	 * Builds and returns a complete GA4 report link.
	 *
	 * @version 7.0.5
	 * @param string      $report_url GA report URL identifier.
	 * @param string      $report     Report type key.
	 * @param string|bool $date_range Optional. Date range parameter (currently unused).
	 * @return string GA4 report link.
	 */
	public static function get_all_stats_link( $report_url, $report, $date_range = false ) {
		unset( $date_range ); // Not used currently, kept for future use.

		$report_link = 'https://analytics.google.com/analytics/web/#/' . $report_url . '/reports/explorer/?';
		$link        = '';

		switch ( $report ) {
			case 'top_pages':
				$link = 'top_pages';
				break;
			case 'top_countries':
				$link = 'top_countries';
				break;
			case 'top_cities':
				$link = 'top_cities';
				break;
			case 'referer':
				$link = 'referer';
				break;
			case 'top_products':
				$link = 'top_products';
				break;
			case 'source_medium':
				$link = $report_link . 'params=_u..nav%3Dmaui${date_parameter}&r=lifecycle-traffic-acquisition-v2&ruid=lifecycle-traffic-acquisition-v2,3078873331,acquisition';
				break;
			case 'top_countries_sales':
				$link = $report_link . 'params=_u..nav%3Dmaui${date_parameter}%26_r.explorerCard..selmet%3D%5B%22activeUsers%22%5D%26_r.explorerCard..seldim%3D%5B%22country%22%5D&r=user-demographics-detail&ruid=user-demographics-detail,user,demographics&collectionId=user';
				break;
			default:
				$link = '';
				break;
		}
		return $link;
	}
}
