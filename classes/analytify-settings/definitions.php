<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Settings Definitions Trait
 *
 * This trait contains the core settings structure and configuration for the Analytify plugin.
 * It was created to separate the settings definitions logic from the main settings class,
 * making the code more modular and easier to maintain.
 *
 * PURPOSE:
 * - Defines the main settings sections and tabs structure
 * - Manages settings sections and fields arrays
 * - Provides methods to add and retrieve settings configuration
 *
 * @package WP_Analytify
 * @subpackage Settings
 * @since 8.0.0
 */

trait Analytify_Settings_Definitions {

	/**
	 * Set the complete sections array for settings.
	 *
	 * @param array<string, mixed> $sections Array of settings sections.
	 * @return $this For method chaining.
	 */
	public function set_sections( $sections ) {
		$this->settings_sections = $sections;
		return $this;
	}

	/**
	 * Add a new section to the settings.
	 *
	 * @param array<string, mixed> $section Section configuration array.
	 * @return $this For method chaining.
	 */
	public function add_section( $section ) {
		if ( isset( $section['id'] ) ) {
			$this->settings_sections[ $section['id'] ] = $section;
		} else {
			// Generate a unique key for sections without ID.
			$key                             = 'section_' . count( $this->settings_sections );
			$this->settings_sections[ $key ] = $section;
		}
		return $this;
	}

	/**
	 * Set the complete fields array for settings.
	 *
	 * @param array<string, mixed> $fields Array of settings fields.
	 * @return $this For method chaining.
	 */
	public function set_fields( $fields ) {
		$this->settings_fields = $fields;
		return $this;
	}

	/**
	 * Get the main settings sections with tabs configuration.
	 *
	 * This method defines the core structure of the Analytify settings page,
	 * including authentication, profile selection, admin settings, tracking options,
	 * advanced configurations, and help sections.
	 *
	 * @return array<string, mixed> Array of settings sections with tabs and accordion structure.
	 * @version 9.0.0
	 */
	public function get_settings_sections() {
		$tabs = array(
			array(
				'id'       => 'wp-analytify-authentication',
				'title'    => __( 'Authentication', 'wp-analytify' ),
				'priority' => '5',
			),
			array(
				'id'       => 'wp-analytify-profile',
				'title'    => __( 'Profile', 'wp-analytify' ),
				'desc'     => 'Select your profiles for front-end and back-end sections.',
				'priority' => '10',
			),
			array(
				'id'       => 'wp-analytify-admin',
				'title'    => __( 'Admin', 'wp-analytify' ),
				'desc'     => 'Following settings will take effect statistics under the posts, custom post types or pages.',
				'priority' => '20',
			),
			array(
				'id'        => 'wp-analytify-tracking',
				'title'     => __( 'Tracking', 'wp-analytify' ),
				'desc'      => 'This section has options to Track forms, events, conversions, Setup and Custom Dimensions.',
				'accordion' => array(
					array(
						'id'    => 'wp-analytify-events-tracking',
						'title' => __( 'Events Tracking', 'wp-analytify' ),
					),
					array(
						'id'    => 'wp-analytify-custom-dimensions',
						'title' => __( 'Custom Dimensions', 'wp-analytify' ),
					),
					array(
						'id'    => 'wp-analytify-forms',
						'title' => __( 'Forms Tracking', 'wp-analytify' ),
					),
					array(
						'id'    => 'analytify-google-ads-tracking',
						'title' => __( 'Google Ads Tracking', 'wp-analytify' ),
					),
					array(
						'id'    => 'analytify-pixels-tracking',
						'title' => __( 'Pixels Tracking', 'wp-analytify' ),
					),
				),
				'priority'  => '32',
			),
			array(
				'id'       => 'wp-analytify-advanced',
				'title'    => __( 'Advanced', 'wp-analytify' ),
				'desc'     => 'Configure the following settings for advanced analytics tracking.',
				'priority' => '35',
			),
			array(
				'id'       => 'wp-analytify-help',
				'title'    => __( 'Help', 'wp-analytify' ),
				'priority' => '45',
			),
		);

		$setting_tabs = apply_filters( 'wp_analytify_pro_setting_tabs', $tabs );

		// Ensure all tabs have priority and validate data.
		$setting_tabs = array_map(
			function ( $tab ) {
				// Ensure priority exists and is numeric.
				if ( ! isset( $tab['priority'] ) || ! is_numeric( $tab['priority'] ) ) {
						$tab['priority'] = 999; // Default high priority for tabs without priority.
				}

				// Ensure required fields exist.
				if ( ! isset( $tab['id'] ) ) {
					$tab['id'] = 'unknown-tab-' . uniqid();
				}

				if ( ! isset( $tab['title'] ) ) {
					$tab['title'] = __( 'Unknown Tab', 'wp-analytify' );
				}

				return $tab;
			},
			$setting_tabs
		);

		// Sort tabs by priority.
		usort(
			$setting_tabs,
			function ( $a, $b ) {
				return (int) $a['priority'] - (int) $b['priority'];
			}
		);

		// Convert to associative array with id as key.
		$result = array();
		foreach ( $setting_tabs as $tab ) {
			if ( isset( $tab['id'] ) ) {
				$result[ $tab['id'] ] = $tab;
			}
		}

		return $result;
	}
}
