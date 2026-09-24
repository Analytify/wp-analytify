<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Email Settings Trait
 *
 * This trait contains all the settings-related functionality for the Analytify Email system.
 * It was created to separate settings configuration from other email operations, keeping
 * the code organized and maintainable.
 *
 * PURPOSE:
 * - Manages email settings tabs and fields
 * - Handles settings configuration and options
 * - Provides settings validation and sanitization
 * - Manages email settings display and form handling
 *
 * @package WP_Analytify
 * @subpackage Email
 * @since 8.0.0
 */

trait Analytify_Email_Settings {

	/**
	 * Add email settings tabs to the main settings page.
	 *
	 * @param array<string, mixed> $old_tabs Existing tabs array.
	 * @return array<string, mixed> Modified tabs array.
	 * @since 1.0.0
	 */
	public function analytify_email_setting_tabs( $old_tabs ) {
		$pro_tabs = array(
			'email_tab' => array(
				'id'       => 'wp-analytify-email',
				'title'    => __( 'Email', 'wp-analytify' ),
				'priority' => '32',
			),
		);

		return array_merge( $old_tabs, $pro_tabs );
	}

	/**
	 * Add email settings fields to the main settings page.
	 *
	 * @param array<string, mixed> $old_fields Existing fields array.
	 * @return array<string, mixed> Modified fields array.
	 * @since 1.0.0
	 */
	public function analytifye_email_setting_fields( $old_fields ) {
		$email_fields = array(
			'wp-analytify-email' => array(
				array(
					'name'  => 'disable_email_reports',
					'label' => __( 'Disable Email Reporting', 'wp-analytify' ),
					'desc'  => __( 'This option will stop sending all email reports, including test emails.', 'wp-analytify' ),
					'type'  => 'checkbox',
				),
				array(
					'name'              => 'analytiy_from_email',
					'label'             => __( 'Sender Email Address', 'wp-analytify' ),
					'desc'              => __( 'Sender Email Address.', 'wp-analytify' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_email',
					'tooltip'           => false,
				),
				array(
					'name'    => 'analytify_email_user_email',
					'label'   => __( 'Receiver Email Address', 'wp-analytify' ),
					'desc'    => '',
					'default' => '',
					'type'    => 'email_receivers',
				),
			),
		);

		if ( ! class_exists( 'WP_Analytify_Email' ) && ! class_exists( 'WP_Analytify_Addon_Email' ) ) {
			array_push(
				$email_fields['wp-analytify-email'],
				array(
					'name'  => 'analytify_email_promo',
					'type'  => 'email_promo',
					'label' => '',
					'desc'  => '',
				)
			);
		}

		return array_merge( $old_fields, $email_fields );
	}

	/**
	 * Display email settings notices.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function analytify_email_notics() {
		$email_options = get_option( 'wp-analytify-email' );

		if ( isset( $email_options['disable_email_reports'] ) && 'on' === $email_options['disable_email_reports'] ) {
			$class   = 'wp-analytify-danger';
			$message = esc_html( 'Analytify email reports and test emails disabled.' );
		} else {
			$class   = 'wp-analytify-success wp-analytify-email-report-sent';
			$message = esc_html__( 'Analytify report sent.', 'wp-analytify' );
		}

		analytify_notice( $message, $class );
	}

	/**
	 * Custom PHPMailer initialization for SMTP.
	 *
	 * @param mixed $PHPMailer PHPMailer instance.
	 * @return void
	 * @since 1.0.0
	 */
	public function custom_phpmailer_init( $PHPMailer ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer variable naming is acceptable for this context
		if ( is_object( $PHPMailer ) && method_exists( $PHPMailer, 'IsSMTP' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			$PHPMailer->IsSMTP(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer variable naming is acceptable for this context
			if ( property_exists( $PHPMailer, 'SMTPAuth' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
				$PHPMailer->SMTPAuth = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			}
			if ( property_exists( $PHPMailer, 'SMTPSecure' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
				$PHPMailer->SMTPSecure = 'ssl'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			}
			if ( property_exists( $PHPMailer, 'Host' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
				$PHPMailer->Host = 'smtp.gmail.com'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			}
			if ( property_exists( $PHPMailer, 'Port' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
				$PHPMailer->Port = 465; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			}
			if ( property_exists( $PHPMailer, 'Username' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
				$PHPMailer->Username = 'test@gmail.com'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			}
			if ( property_exists( $PHPMailer, 'Password' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
				$PHPMailer->Password = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase,WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPMailer property naming is acceptable for this context
			}
		}
	}

	/**
	 * Add email settings in diagnostic information.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function analytify_settings_logs() {
		echo "\r\n";
		echo "-- Analytify Email Setting --\r\n \r\n";

		$analytify_email = get_option( 'wp-analytify-email' );

		WPANALYTIFY_Utils::print_settings_array( $analytify_email );
	}
}
