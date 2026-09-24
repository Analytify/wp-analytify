<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- File naming is acceptable
/**
 * Analytify Real-Time Widget
 *
 * @package WP_Analytify
 */

/**
 * Analytify Real-Time Widget Class
 *
 * @extends WP_Widget<array<string, mixed>>
 */
class ANALYTIFY_WIDGET_REALTIME extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {

		parent::__construct(
			'analytify_live_stats', // Base ID.
			esc_html_e( 'Analytify Live Stats', 'wp-analytify' ), // Name.
			array( 'description' => esc_html_e( 'It shows Live Stats of your site.', 'wp-analytify' ) ) // Args.
		);
	}

	/**
	 * Widget form creation
	 *
	 * @param array<string, mixed> $instance Widget instance.
	 * @return void
	 */
	public function form( $instance ) {

		// Check values.
		if ( $instance ) {
			$title       = esc_attr( $instance['title'] );
			$description = esc_textarea( $instance['description'] );
		} else {
			$title       = '';
			$description = '';
		}
		?>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'wp-analytify' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Description:', 'wp-analytify' ); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
		</p>

		<?php
	}

	/**
	 * Update widget
	 *
	 * @param array<string, mixed> $new_instance New instance.
	 * @param array<string, mixed> $old_instance Old instance.
	 * @return array<string, mixed>
	 */
	public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;
			// Fields.
			$instance['title']       = wp_strip_all_tags( $new_instance['title'] );
			$instance['description'] = wp_strip_all_tags( $new_instance['description'] );

		return $instance;
	}

	/**
	 * Display widget
	 *
	 * @param array<string, mixed> $args Widget arguments.
	 * @param array<string, mixed> $instance Widget instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {

		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Required for WordPress widget compatibility.
		extract( $args );
		// These are the widget options.
		$title       = apply_filters( 'widget_title', $instance['title'] );
		$description = $instance['description'];

		// Ensure widget variables are defined.
		$before_widget = isset( $before_widget ) ? $before_widget : '<div class="widget">';
		$after_widget  = isset( $after_widget ) ? $after_widget : '</div>';
		$before_title  = isset( $before_title ) ? $before_title : '<h3 class="widget-title">';
		$after_title   = isset( $after_title ) ? $after_title : '</h3>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget output is controlled by theme.
		echo $before_widget;
		// Display the widget.
		echo '<div class="analytify-widget-realtime">';

		// Check if title is set.
		if ( $title ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget output is controlled by theme.
			echo $before_title . esc_html( $title ) . $after_title;
		}

		// Check if description is set.
		if ( $description ) {
			echo '<p class="analytify-widget-desc">' . esc_html( $description ) . '</p>';
		}

		echo '<div class="analytify-widget-realtime-visitors">0</div>';

		echo '</div>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget output is controlled by theme.
		echo $after_widget;

		?>
			<script>
			jQuery(document).ready(function  ($) {

			function analytify_realtime_widget(){

				jQuery.post(
							ajax_object.ajax_url, {
										action: "pa_get_online_data",
										pa_security: "<?php echo esc_attr( wp_create_nonce( 'pa_get_online_data' ) ); ?>"
									},
							function(response){
								var data = jQuery.parseJSON(response);
								$('.analytify-widget-realtime-visitors').html(data["totalsForAllResults"]["ga:activeVisitors"]);
							}
				);

			}

			analytify_realtime_widget();
			setInterval(analytify_realtime_widget, 5000);

			});
			</script>
		<?php
	}
}

// Register widget.
add_action(
	'widgets_init',
	function () {
		return register_widget( 'ANALYTIFY_WIDGET_REALTIME' );
	}
);

