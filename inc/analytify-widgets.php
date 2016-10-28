<?php

class ANALYTIFY_WIDGET_REALTIME extends WP_Widget {

	function __construct() {

		parent::__construct(
			'analytify_live_stats', // Base ID
			esc_html_e( 'Analytify Live Stats', 'wp-analytify' ), // Name
			array( 'description' => esc_html_e( 'It shows Live Stats of your site.', 'wp-analytify' ) ) // Args
		);
	}

	// widget form creation
	function form( $instance ) {

		// Check values
		if ( $instance ) {
			 $title = esc_attr( $instance['title'] );
			 $description = esc_textarea( $instance['description'] );
		} else {
			 $title = '';
			 $description = '';
		}
		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'wp-analytify' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php esc_html_e( 'Description:', 'wp-analytify' ); ?></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo $description; ?></textarea>
		</p>

	<?php
	}

	// update widget
	function update( $new_instance, $old_instance ) {

		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags( $new_instance['title'] );
		  $instance['description'] = strip_tags( $new_instance['description'] );

		 return $instance;
	}

	// display widget
	function widget( $args, $instance ) {

		extract( $args );
		// these are the widget options
		$title = apply_filters( 'widget_title', $instance['title'] );
		$description = $instance['description'];

		echo $before_widget;
		// Display the widget
		echo '<div class="analytify-widget-realtime">';

		// Check if title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		// Check if description is set
		if ( $description ) {
			echo '<p class="analytify-widget-desc">'.$description.'</p>';
		}

		echo '<div class="analytify-widget-realtime-visitors">0</div>';

		echo '</div>';
		echo $after_widget;

		?>
			<script>
			jQuery(document).ready(function  ($) {

			function analytify_realtime_widget(){

				jQuery.post(
							ajax_object.ajax_url, {
										action: "pa_get_online_data",
										pa_security: "<?php echo wp_create_nonce( 'pa_get_online_data' ) ?>"
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

// register widget
add_action( 'widgets_init', create_function( '', 'return register_widget("ANALYTIFY_WIDGET_REALTIME");' ) );

?>
