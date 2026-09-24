<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Email Render Trait
 *
 * This trait contains all the UI rendering functionality for the Analytify Email system.
 * It was created to separate display logic from other email operations, keeping the code
 * organized and maintainable.
 *
 * PURPOSE:
 * - Handles UI rendering and display elements
 * - Manages single post email button display
 * - Provides email interface components
 * - Handles visual email elements and forms
 *
 * @package WP_Analytify
 * @subpackage Email
 * @since 8.0.0
 */

trait Analytify_Email_Render {

	/**
	 * Show Send Email button on Single Page/Post.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function single_send_email() {
		echo '<div class="analytify-single-mail-submit">
  		<input type="submit" value="' . esc_attr__( 'Send Email Report', 'wp-analytify' ) . '" name="send_email" class="analytify_submit_date_btn"  id="send_single_analytics">';

		// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found -- Assignment in condition is intentional
		if ( apply_filters( 'wpa_display_email_single_input_field', $display = false ) ) {
			echo '<input type="email" name="recipient_email" placeholder="' . esc_attr__( 'Enter Recipient Email', 'wp-analytify' ) . '" id="recipient_email" style="min-height: 46px; min-width: 250px; margin-left: 4px;">';
		}

		echo '<span style=\'min-height:30px;min-width:150px;display:none\' class=\'send_email stats_loading\'></span></div>';
	}
}
