<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Settings Promo Trait
 *
 * This trait handles promotional content and pro feature displays in the settings.
 * It was created to separate promotional logic from the main settings class,
 * making it easier to manage and update promotional content.
 *
 * PURPOSE:
 * - Displays pro feature promotions
 * - Manages upgrade messaging
 * - Handles feature comparison displays
 * - Provides promotional content rendering
 *
 * @package WP_Analytify
 * @subpackage Settings
 * @since 8.0.0
 */

trait Analytify_Settings_Promo {
	/**
	 * Get pro features HTML.
	 *
	 * @return string
	 */
	public function pro_features() {
		$html = '   <div class="pro-feature-wrapper" >';
		ob_start();
		?>

				<p > Tweet us <a href="https://twitter.com/analytify" style="text-decoration:none;"> @analytify </a> and Like us <a href="https://fb.com/analytify" style="text-decoration:none;">@analytify</a> </p>
				<table class="wa_feature_table">
					<tbody>
						<tr>
							<th>Features</th>
							<th>Free</th>
							<th>Pro</th>
						</tr>
						<tr>
							<td><strong>Support</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Dashboard</strong></td>
							<td>Yes (limited)</td>
							<td>Yes (Advanced)</td>
						</tr>
						<tr>
							<td><strong>Live Stats</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Comparison Stats</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>ShortCodes</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>

						<tr>
							<td><strong>Extensions</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
						<tr>
							<td><strong>Analytics under Posts (admin)</strong></td>
							<td>Yes (limited)</td>
							<td>Yes (Advanced)</td>
						</tr>
						<tr>
							<td><strong>Analytics under Pages (admin)</strong></td>
							<td>Yes (limited)</td>
							<td>Yes (Advanced)</td>
						</tr>
						<tr>
							<td><strong>Analytics under Custom Post Types (front/admin)</strong></td>
							<td>No</td>
							<td>Yes</td>
						</tr>
					</tbody>
				</table>
				<div class="postbox-container side">
					<div class="metabox-holder">

						<div class="grids_auto_size wpa_side_box" style="width: 100%;">
							<div class="grid_title cen"> UPGRADE to PRO </div>

							<div class="grid_footer cen" style="background-color:white;">
								<a href="https://analytify.io/upgrade-from-free" title="Analytify Support">Buy Now</a> the PRO version of Analytify and get tons of benefits including premium features, support and updates.
							</div>
						</div>
						<div class="grids_auto_size wpa_side_box" style=" width: 100%;">
							<div class="grid_footer cen">
								made with ♥ by <a href="https://wpbrigade.com" title="WPBrigade | A Brigade of WordPress Developers." />WPBrigade</a>
							</div>
						</div>
					</div>
				</div>

		<?php
		$inner_html = ob_get_clean();
		$html      .= apply_filters( 'free-pro-features', $inner_html ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- Hook name maintained for backwards compatibility
		echo '</div>';
		return $html;
	}
}


