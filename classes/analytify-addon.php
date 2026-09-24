<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Analytify Pro Addon base class.
 *
 * This class serves as the base class for all Analytify Pro addons,
 * providing common functionality and initialization.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */

// phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File name follows project convention

/**
 * Analytify Pro Addon class.
 *
 * @package WP_Analytify
 * @since 1.0.0
 */
class AnalytifyPro_Addon extends Analytify_Base {

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file_path The path to the plugin file.
	 */
	public function __construct( $plugin_file_path ) {
		$this->is_addon = true;
		parent::__construct( $plugin_file_path );
	}
}
