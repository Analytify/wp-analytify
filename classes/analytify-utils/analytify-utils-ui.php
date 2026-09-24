<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Utils UI Trait
 *
 * This trait contains UI-related utility functions for the Analytify plugin.
 * It was created to separate UI utility logic from the main utils class,
 * providing helper functions for date handling, form generation,
 * and user interface components.
 *
 * PURPOSE:
 * - Provides UI utility functions.
 * - Handles date and form components.
 * - Manages user interface elements.
 * - Offers UI helper methods.
 *
 * @package WP_Analytify
 * @subpackage Utils
 * @since 8.0.0
 */

trait Analytify_Utils_UI {

	/**
	 * Generate date selection list HTML.
	 *
	 * Creates an HTML list of predefined date ranges for analytics
	 * including today, yesterday, last 7 days, last 30 days, etc.
	 *
	 * @return void Outputs HTML directly.
	 */
	public static function get_date_list() {
		ob_start();
		?>
		<ul class="analytify_select_date_list">
			<li><?php esc_html_e( 'Today', 'wp-analytify' ); ?> <span data-date-diff="current_day" data-start="" data-end=""><span class="analytify_start_date_data analytify_current_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>
			<li><?php esc_html_e( 'Yesterday', 'wp-analytify' ); ?> <span data-date-diff="yesterday" data-start="" data-end=""><span class="analytify_start_date_data analytify_yesterday"></span> – <span class="analytify_end_date_data analytify_yesterday_date"></span></span></li>
			<li><?php esc_html_e( 'Last 7 days', 'wp-analytify' ); ?> <span data-date-diff="last_7_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_7_days"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>
			<li><?php esc_html_e( 'Last 14 days', 'wp-analytify' ); ?> <span data-date-diff="last_14_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_14_days"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>
			<li><?php esc_html_e( 'Last 30 days', 'wp-analytify' ); ?> <span data-date-diff="last_30_days" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_30_day"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>
			<li><?php esc_html_e( 'This month', 'wp-analytify' ); ?> <span data-date-diff="this_month" data-start="" data-end=""><span class="analytify_start_date_data analytify_this_month_start_date"></span> – <span class="analytify_end_date_data analytify_today_date"></span></span></li>
			<li><?php esc_html_e( 'Last month', 'wp-analytify' ); ?> <span data-date-diff="last_month" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_month_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>
			<li><?php esc_html_e( 'Last 3 months', 'wp-analytify' ); ?> <span data-date-diff="last_3_months" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_3_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>
			<li><?php esc_html_e( 'Last 6 months', 'wp-analytify' ); ?> <span data-date-diff="last_6_months" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_6_months_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>
			<li><?php esc_html_e( 'Last year', 'wp-analytify' ); ?> <span data-date-diff="last_year" data-start="" data-end=""><span class="analytify_start_date_data analytify_last_year_start_date"></span> – <span class="analytify_end_date_data analytify_last_month_end_date"></span></span></li>
			<li><?php esc_html_e( 'Custom Range', 'wp-analytify' ); ?> <span class="custom_range"><?php esc_html_e( 'Select a custom date', 'wp-analytify' ); ?></span></li>
		</ul>
		<?php
		$content = ob_get_clean();
		echo wp_kses_post( $content ? $content : '' );
	}

	/**
	 * Generate date selection form.
	 *
	 * Creates an HTML form for selecting date ranges with start and end
	 * date inputs, submit button, and date list dropdown.
	 *
	 * @param string               $start_date Start date value.
	 * @param string               $end_date   End date value.
	 * @param array<string, mixed> $args       Additional arguments for customization.
	 * @version 9.0.0
	 * @return void
	 */
	public static function date_form( $start_date, $end_date, $args = array() ) {
		$_analytify_profile = get_option( 'wp-analytify-profile' );
		$dashboard_profile  = isset( $_analytify_profile['profile_for_dashboard'] ) ? $_analytify_profile['profile_for_dashboard'] : '';

		if ( empty( $dashboard_profile ) ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin ?page= slug for CSS class only; not form POST handling.

		$class = ( 'edd-dashboard' === $page || 'analytify-woocommerce' === $page || 'analytify-pmpro' === $page || 'analytify-learndash' === $page || 'analytify-lifterlms' === $page ) ? 'ecommerce-stats-class' : $page;

		?>
		<form class="analytify_form_date analytify_form_date_pro <?php echo esc_attr( $class ); ?>" action="" method="post">
			<?php
			if ( ! empty( $args['input_before_field_set'] ) ) {
				echo wp_kses_post( $args['input_before_field_set'] );
			}
			?>
			<div class="analytify_select_date_fields">
				<input type="hidden" name="st_date" id="analytify_start_val">
				<input type="hidden" name="ed_date" id="analytify_end_val">
				<input type="hidden" name="analytify_date_diff" id="analytify_date_diff">
				<input type="hidden" name="analytify_date_start" id="analytify_date_start" value="<?php echo esc_attr( $start_date ); ?>">
				<input type="hidden" name="analytify_date_end" id="analytify_date_end" value="<?php echo esc_attr( $end_date ); ?>">

				<label for="analytify_start"><?php esc_html_e( 'From:', 'wp-analytify' ); ?></label>
				<input type="text" required id="analytify_start" value="">
				<label for="analytify_end"><?php esc_html_e( 'To:', 'wp-analytify' ); ?></label>
				<input type="text" onpaste="return false;" oncopy="return false;" autocomplete="off" required id="analytify_end" value="">
				<div class="analytify_arrow_date_picker"></div>
			</div>
			<?php
			if ( ! empty( $args['input_after_field_set'] ) ) {
				echo wp_kses_post( $args['input_after_field_set'] );
			}
			?>
			<input 
				type="submit"
				value="<?php esc_attr_e( 'View Stats', 'wp-analytify' ); ?>"
				name="view_data"
				class="analytify_submit_date_btn"
				<?php
				if ( ! empty( $args['input_submit_id'] ) ) {
					echo ' id="' . esc_attr( $args['input_submit_id'] ) . '"';
				}
				?>
			>
			<?php self::get_date_list(); ?>
		</form>
		<br>
		<?php
	}

	/**
	 * Create error display box.
	 *
	 * Generates an HTML error box with status and message information
	 * for displaying error messages in a consistent format.
	 *
	 * @param string $status  Error status or code.
	 * @param string $message Error message text.
	 * @param string $heading Error box heading (default: 'Unable To Fetch Reports').
	 * @return void
	 */
	public static function create_error_box( $status, $message, $heading = 'Unable To Fetch Reports' ) {
		?>
		<div class="analytify-email-promo-contianer">
			<div class="analytify-email-premium-overlay">
				<div class="analytify-email-premium-popup">
					<h3 class="analytify-promo-popup-heading" style="text-align: left;"><?php echo esc_html( $heading ); ?></h3>
					<p class="analytify-promo-popup-paragraph analytify-error-popup-paragraph">
						<strong><?php esc_html_e( 'Status:', 'wp-analytify' ); ?> </strong> <?php echo esc_html( $status ); ?>
					</p>
					<p class="analytify-promo-popup-paragraph analytify-error-popup-paragraph">
						<strong><?php esc_html_e( 'Message:', 'wp-analytify' ); ?> </strong> <?php echo esc_html( $message ); ?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}
}
