<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing -- File doc comment exists
/**
 * Analytify Settings Render Trait
 *
 * This trait handles all the rendering and display logic for the Analytify settings page.
 * It was created to separate the UI rendering concerns from the main settings class,
 * making the code more maintainable and following the single responsibility principle.
 *
 * PURPOSE:
 * - Renders the settings page HTML structure
 * - Handles form submissions and processing
 * - Manages admin notices and messages
 * - Provides UI components for settings fields
 *
 * @package WP_Analytify
 * @subpackage Settings
 * @since 8.0.0
 */

trait Analytify_Settings_Render {

	// Field callbacks.

	/**
	 * Text field callback.
	 *
	 * @since 1.0.0
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 * @version 9.1.0
	 */
	public function callback_text( $args ) {
		$option_value = $this->get_option( $args['id'], $args['section'], $args['std'] );

		// Handle array values gracefully - extract secretValue if it exists.
		if ( is_array( $option_value ) && isset( $option_value['secretValue'] ) ) {
			$value = $option_value['secretValue'];
		} elseif ( is_array( $option_value ) ) {
			$value = ''; // Clear invalid array data.
		} else {
			$value = $option_value;
		}

		$value        = esc_attr( $value );
		$size         = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type         = isset( $args['type'] ) ? $args['type'] : 'text';
		$extra_attrs  = '';
		$input_format = isset( $args['input_format'] ) ? $args['input_format'] : '';

		if ( ! empty( $input_format ) ) {
			$extra_attrs .= sprintf( ' data-input-format="%s"', esc_attr( $input_format ) );

			if ( 'digits' === $input_format ) {
				$extra_attrs .= ' inputmode="numeric" pattern="[0-9]*"';
			}
		}

		if ( ! empty( $args['maxlength'] ) ) {
			$extra_attrs .= sprintf( ' maxlength="%d"', absint( $args['maxlength'] ) );
		}

		$html = sprintf(
			'<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>',
			esc_attr( $type ),
			esc_attr( $size ),
			esc_attr( $args['section'] ),
			esc_attr( $args['id'] ),
			$value,
			$extra_attrs
		);

		if ( ! empty( $args['tooltip'] ) ) {
			$tooltip_text = is_string( $args['tooltip'] ) ? $args['tooltip'] : $args['desc'];
			$html        .= sprintf( '<span class="dashicons dashicons-editor-help setting-more-info" title="%1$s"></span>', esc_attr( $tooltip_text ) );

			if ( $tooltip_text === $args['desc'] ) {
				$args['desc'] = '';
			}
		}

		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Button field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_button( $args ) {
		$option_value = $this->get_option( $args['id'], $args['section'], $args['std'] );

		// Handle array values gracefully - extract secretValue if it exists.
		if ( is_array( $option_value ) && isset( $option_value['secretValue'] ) ) {
			$value = $option_value['secretValue'];
		} elseif ( is_array( $option_value ) ) {
			$value = ''; // Clear invalid array data.
		} else {
			$value = $option_value;
		}

		$value = esc_attr( $value );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'button';
		$type  = isset( $args['type'] ) ? $args['type'] : 'text';
		$html  = sprintf( '<input type="%1$s" class="%2$s button-primary" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"/>', esc_attr( $type ), esc_attr( $size ), esc_attr( $args['section'] ), esc_attr( $args['id'] ), $value );
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * URL field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_url( $args ) {
		$this->callback_text( $args );
	}
	/**
	 * Number field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_number( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 * @version 9.1.0
	 */
	public function callback_checkbox( $args ) {
		$option_value = $this->get_option( $args['id'], $args['section'], $args['std'] );

		// Handle array values gracefully - extract secretValue if it exists.
		if ( is_array( $option_value ) && isset( $option_value['secretValue'] ) ) {
			$value = $option_value['secretValue'];
		} elseif ( is_array( $option_value ) ) {
			$value = ''; // Clear invalid array data.
		} else {
			$value = $option_value;
		}

		$value                  = esc_attr( $value );
		$is_disabled            = ( isset( $args['options']['disabled'] ) && $args['options']['disabled'] ) ? 'disabled' : '';
		$defalut_value          = ( 'disabled' === $is_disabled && 'on' === $value ) ? 'on' : 'off';
		$checkbox_value         = ( isset( $args['options']['disabled'] ) && $args['options']['disabled'] ) ? 'off' : $value;
		$checkbox_name_override = ( isset( $args['options']['disabled'] ) && $args['options']['disabled'] ) ? '__' : '';
		$html                   = '<fieldset>';
		$html                  .= sprintf( '<label for="%1$s[%2$s]"></label>', esc_attr( $args['section'] ), esc_attr( $args['id'] ) );
		$html                  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="%3$s" />', esc_attr( $args['section'] ), esc_attr( $args['id'] ), esc_attr( $defalut_value ) );
		$html                  .= sprintf( '<div class="toggle"><input type="checkbox" class="checkbox" id="%1$s%2$s[%3$s]" name="%1$s%2$s[%3$s]" value="on" %4$s %5$s /><span class="btn-nob"></span><span class="texts"></span><span class="bg"></span></div>', esc_attr( $checkbox_name_override ), esc_attr( $args['section'] ), esc_attr( $args['id'] ), checked( $checkbox_value, 'on', false ), esc_attr( $is_disabled ) );

		if ( ! empty( $args['tooltip'] ) ) {
			$tooltip_text = is_string( $args['tooltip'] ) ? $args['tooltip'] : $args['desc'];
			$html        .= sprintf( '<span class="dashicons dashicons-editor-help setting-more-info" title="%1$s"></span>', esc_attr( $tooltip_text ) );

			if ( $tooltip_text === $args['desc'] ) {
				$args['desc'] = '';
			}
		}

		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_multicheck( $args ) {
		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$html  = '<fieldset>';
		foreach ( $args['options'] as $key => $label ) {
			$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
			$html   .= sprintf( '<label for="wp-analytify-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
			$html   .= sprintf( '<input type="checkbox" class="checkbox" id="wp-analytify-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
			$html   .= sprintf( '%1$s</label><br>', $label );
		}
		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_radio( $args ) {
		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$html  = '<fieldset>';
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<label for="wp-analytify-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
			$html .= sprintf( '<input type="radio" class="radio" id="wp-analytify-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
			$html .= sprintf( '%1$s</label><br>', $label );
		}
		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_select( $args ) {
		$option_value = $this->get_option( $args['id'], $args['section'], $args['std'] );

		// Handle array values gracefully - extract secretValue if it exists.
		if ( is_array( $option_value ) && isset( $option_value['secretValue'] ) ) {
			$value = $option_value['secretValue'];
		} elseif ( is_array( $option_value ) ) {
			$value = ''; // Clear invalid array data.
		} else {
			$value = $option_value;
		}

		$value = esc_attr( $value );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = sprintf( '<select class="%1$s analytify-settings-select" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
		}
		$html .= '</select>';
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_multi_select( $args ) {
		$value = ( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = '';
		foreach ( $args['options']['main'] as $key => $label ) {
			$html .= '<div class="analytify-multiselect-container">';
			$html .= sprintf( '<select class="%1$s" name="%2$s[%3$s][' . $key . ']" id="%2$s[%3$s][value]">', esc_attr( $size ), esc_attr( $args['section'] ), esc_attr( $args['id'] ) );
			if ( isset( $args['options']['value'][ $key ] ) && is_array( $args['options']['value'][ $key ] ) ) {
				foreach ( $args['options']['value'][ $key ] as $k => $v ) {
					$selected_value = isset( $value[ $key ] ) ? $value[ $key ] : '';
					$html          .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $k, $selected_value, false ), esc_html( $v ) );
				}
			}
			$html .= '</select>';
			$html .= '<span class="analytify-multiselect-label">' . esc_html( $label ) . '</span>';
			$html .= '</div>';
		}
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_ga_dimensions_repeater( $args ) {
		$html                 = '';
		$count                = 0;
		$available_dimensions = array();

		foreach ( $args['options'] as $key => $value ) {
			if ( true !== $value['is_enable'] ) {
				continue;
			}

			$available_dimensions[ $key ] = $value;
		}

		wp_add_inline_script(
			'analytify_dimension_script',
			'
			var wpAnalytifyDimensionOptions = ' . wp_json_encode( $available_dimensions ) . ';
		'
		);
		?>

		<?php
		$html .= '<table id="wp-analytify-dimension-table">
		<thead>
		  <tr>
			<th>' . __( 'Type', 'wp-analytify' ) . '</th>
		  </tr>
		</thead>
		<tbody>';

		$current_values = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$current_values = array_values( $current_values );

		if ( empty( $current_values ) ) {
			$html .= '';
		} else {
			foreach ( $current_values as $current_value => $vals ) {
				$html .= '<tr class="single_dimension"><td>';
				$html .= sprintf( '<select class="select-dimension" name="%1$s[%2$s][' . $count . '][type]" id="%1$s[%2$s]">', $args['section'], $args['id'] );

				$saved_type = ! empty( $vals['type'] ) ? $vals['type'] : '';

				// Only loop if options exist and are iterable.
				if ( ! empty( $args['options'] ) && is_array( $args['options'] ) ) {
					foreach ( $args['options'] as $key => $value ) {
						// Skip SEO options if Yoast is not available, unless it's the saved value.
						if ( in_array( $key, array( 'seo_score', 'focus_keyword' ), true )
							&& ! class_exists( 'WPSEO_Frontend' )
							&& $saved_type !== $key
						) {
							continue;
						}

						// Get title safely.
						$title = is_array( $value ) ? ( $value['title'] ?? '' ) : ( is_object( $value ) ? ( $value->title ?? '' ) : '' );

						$html .= sprintf( '<option value="%s" %s>%s</option>', $key, $saved_type === $key ? 'selected' : '', esc_html( $title ) );
					}
				}

				$html .= '</select></td>';
				$html .= '<td><span class="wp-analytify-rmv-dimension"></span></td>
			  </tr>';

				++$count;
			}
		}

		$html .= '<div class="inside">' . $args['desc'] . '</div>';
		$html .= '</tbody></table><button type="button" class="button wp-analytify-add-dimension">Add Dimension</button>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is built from sanitized inputs and WordPress functions.
		echo $html;
	}



	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_email_receivers( $args ) {
		$current_values = $this->get_option( $args['id'], $args['section'], $args['std'] );
		if ( ! is_array( $current_values ) ) {
			$basic_values   = explode( ',', $current_values );
			$current_values = array();
			foreach ( $basic_values as $value ) {
				$current_values[] = array(
					'name'  => '',
					'email' => $value,
				);
			}
		}
		$current_values = array_values( $current_values );
		$count          = 0;
		$html           = '<table id="wp-analytify-email-table"><thead><tr><th>' . esc_html__( 'Name', 'wp-analytify' ) . '</th><th>' . esc_html__( 'Email Address', 'wp-analytify' ) . '</th></tr></thead><tbody>';
		if ( ! empty( $current_values ) ) {
			foreach ( $current_values as $current_value => $vals ) {
				$html .= '<tr class="single_email">';
				$html .= sprintf(
					'<td><input type="text" name="%1$s[%2$s][%3$s][name]" id="%1$s[%2$s]"" value="%4$s" placeholder="%5$s"></td>',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $count ),
					esc_attr( trim( $vals['name'] ) ),
					esc_attr__( 'Name', 'wp-analytify' )
				);
				$html .= sprintf(
					'<td><input type="email" name="%1$s[%2$s][%3$s][email]" id="%1$s[%2$s]" value="%4$s" placeholder="%5$s" required></td>',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $count ),
					esc_attr( sanitize_email( $vals['email'] ) ),
					esc_attr__( 'Email Address', 'wp-analytify' )
				);
				$html .= '<td><span class="wp-analytify-rmv-email"></span></td></tr>';
				++$count;
			}
		}
		$html .= '<div class="inside">' . wp_kses_post( isset( $args['desc'] ) && ! empty( $args['desc'] ) ? $args['desc'] : '' ) . '</div>';
		$html .= '</tbody></table><button type="button" class="button wp-analytify-add-email">' . esc_html__( 'Add Receiver', 'wp-analytify' ) . '</button>';
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_affiliates_repeater( $args ) {
		$count          = 0;
		$html           = '<table id="wp-analytify-affiliates-table"><thead><tr><p>' . esc_html( $args['desc'] ) . '</p><th>' . esc_html__( 'Path (example: /refer/)', 'wp-analytify' ) . '</th><th>' . esc_html__( 'Label (example: loginpress link)', 'wp-analytify' ) . '</th></tr></thead><tbody>';
		$current_values = $this->get_option( $args['id'], $args['section'], $args['std'] );
		if ( empty( $current_values ) ) {
			$html .= '<tr class="single_affiliates">';
			$html .= sprintf(
				'<td><input type="text" class="affiliates-path" name="%1$s[%2$s][%3$s][path]" id="%1$s[%2$s]" placeholder="%4$s" value="" ></td>',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $count ),
				esc_attr__( '/refer/', 'wp-analytify' )
			);
			$html .= sprintf(
				'<td><input type="text" class="affiliates-label" name="%1$s[%2$s][%3$s][label]" id="%1$s[%2$s]" placeholder="%4$s" value="" ></td>',
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $count ),
				esc_attr__( 'loginpress link', 'wp-analytify' )
			);
			$html .= '<td><span class="wp-analytify-rmv-affiliates"></span></td></tr>';
		} else {
			$current_values = array_values( $current_values );
			foreach ( $current_values as $current_value => $vals ) {
				$html .= '<tr class="single_affiliates">';
				$html .= sprintf(
					'<td><input type="text" class="affiliates-path" placeholder="%4$s" name="%1$s[%2$s][%3$s][path]" id="%1$s[%2$s]" value="%5$s"></td>',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $count ),
					esc_attr__( '/refer/', 'wp-analytify' ),
					esc_attr( $vals['path'] )
				);
				$html .= sprintf(
					'<td><input type="text" class="affiliates-label" placeholder="%4$s" name="%1$s[%2$s][%3$s][label]" id="%1$s[%2$s]" value="%5$s"></td>',
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					esc_attr( $count ),
					esc_attr__( 'loginpress link', 'wp-analytify' ),
					esc_attr( $vals['label'] )
				);
				$html .= '<td><span class="wp-analytify-rmv-affiliates"></span></td></tr>';
				++$count;
			}
		}
		$html .= '</tbody></table><p class="affiliates-err">' . esc_html__( "Affiliates can't be empty!", 'wp-analytify' ) . '</p><button type="button" class="button wp-analytify-add-affiliates">' . esc_html__( 'Add Affiliate Link', 'wp-analytify' ) . '</button>';
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_chosen( $args ) {
		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = sprintf( '<select multiple class="%1$s analytify-chosen" name="%2$s[%3$s][]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
		foreach ( $args['options'] as $key => $label ) {
			$selected = in_array( $key, $value, true ) ? 'selected = selected' : '';
			$html    .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $key ), $selected, esc_html( $label ) );
		}
		$html .= '</select>';
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_select_streams( $args ) {
		$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = sprintf( '<select class="%1$s analytify-settings-select" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $key, $value, false ), esc_html( $label ) );
		}
		$html .= '</select>';
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Select profile field callback.
	 *
	 * @param mixed $args Field arguments.
	 * @return void
	 */
	public function callback_select_profile( $args ) {
		$exception = $GLOBALS['WP_ANALYTIFY']->get_exception();
		if ( $exception ) {
			if ( isset( $exception[0]['reason'] ) && 'dailyLimitExceeded' === $exception[0]['reason'] ) {
				$link = 'https://analytify.io/doc/fix-403-daily-limit-exceeded/';
				// translators: %1$s is bold opening tag, %2$s is bold closing tag, %3$s is tutorial link opening tag, %4$s is tutorial link closing tag, %5$s is paragraph opening tag, %6$s is paragraph closing tag.
				printf( __( '%5$s%1$sDaily Limit Exceeded:%2$s This Indicates that user has exceeded the daily quota (either per project or per view (profile)). Please %3$sfollow this tutorial%4$s to fix this issue. let us know this issue (if it still doesn\'t work) in the Help tab of Analytify->settings page.%6$s', 'wp-analytify' ), '<b>', '</b>', '<a href="' . esc_url( $link ) . '" target="_blank">', '</a>', '<p class="description" style="color:#ed1515">', '</p>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- printf with proper escaping
				return;
			} elseif ( isset( $exception[0]['reason'] ) && 'insufficientPermissions' === $exception[0]['reason'] && 'global' === $exception[0]['domain'] ) {
				echo '<p class="description" style="color:#ed1515">' . esc_html__( 'Insufficient Permissions:', 'wp-analytify' ) . ' ' . esc_html( $exception[0]['message'] ) . ' <br>' . esc_html__( 'Check out', 'wp-analytify' ) . ' <a href="https://analytify.io/setup-account-google-analytics/" target="_blank">' . esc_html__( 'this guide here', 'wp-analytify' ) . '</a> ' . esc_html__( 'to setup it properly.', 'wp-analytify' ) . '</p>';
				return;
			} elseif ( isset( $exception[0]['reason'] ) && 'unexpected_profile_error' === $exception[0]['reason'] ) {
				echo '<p class="description" style="color:#ed1515">' . esc_html__( 'An unexpected error occurred while getting profiles list from the Google Analytics account.', 'wp-analytify' ) . ' <br> ' . esc_html__( 'let us know this issue from the Help tab.', 'wp-analytify' ) . '</p>';
				return;
			} elseif ( isset( $exception[0]['reason'] ) && 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' === $exception[0]['reason'] ) {
				echo '<p class="description" style="color:#ed1515">' . esc_html__( 'Insufficient Permissions:', 'wp-analytify' ) . ' ' . esc_html( $exception[0]['message'] ) . ' <br>' . esc_html__( 'Check out', 'wp-analytify' ) . ' <a href="https://analytify.io/setup-account-google-analytics/" target="_blank">' . esc_html__( 'this guide here', 'wp-analytify' ) . '</a> ' . esc_html__( 'to setup it properly.', 'wp-analytify' ) . '</p>';
				return;
			} else {
				echo '<p class="description" style="color:#ed1515">' . esc_html( $exception[0]['reason'] ) . ' : ' . esc_html( $exception[0]['message'] ) . ' </p>';
			}
		}
		$exception = $GLOBALS['WP_ANALYTIFY']->get_ga4_exception();
		if ( $exception ) {
			if ( isset( $exception[0]['reason'] ) && 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' === $exception[0]['reason'] ) {
				echo '<p class="description" style="color:#ed1515">' . esc_html__( 'Insufficient Permissions:', 'wp-analytify' ) . ' ' . esc_html( $exception[0]['message'] ) . ' <br>' . esc_html__( 'Check out', 'wp-analytify' ) . ' <a href="https://analytify.io/setup-account-google-analytics/" target="_blank">' . esc_html__( 'this guide here', 'wp-analytify' ) . '</a> ' . esc_html__( 'to setup it properly.', 'wp-analytify' ) . '</p>';
				return;
			} elseif ( isset( $exception[0]['reason'] ) ) {
				echo '<p class="description" style="color:#ed1515">' . esc_html( $exception[0]['reason'] ) . ' : ' . esc_html( $exception[0]['message'] ) . ' </p>';
			}
		}
		$value              = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size               = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html               = sprintf( '<select class="%1$s analytify-chosen" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
		$_analytify_setting = get_option( 'wp-analytify-profile' );
		if ( isset( $_analytify_setting['hide_profiles_list'] ) && 'on' === $_analytify_setting['hide_profiles_list'] ) {
			$html .= '<option value="' . esc_attr( $value ) . '" selected>' . esc_html( WP_ANALYTIFY_FUNCTIONS::search_profile_info( $value, 'websiteUrl' ) ) . ' (' . esc_html( WP_ANALYTIFY_FUNCTIONS::search_profile_info( $value, 'name' ) ) . ')' . '</option>'; // phpcs:ignore Generic.Strings.UnnecessaryStringConcat.Found -- Dynamic HTML construction requires concatenation
		} else {
			$html       .= '<option value="">' . esc_html( $args['std'] ) . '</option>';
			$is_ga4_mode = 'ga4' === WPANALYTIFY_Utils::get_ga_mode() ? true : false;
			if ( $is_ga4_mode && isset( $args['options']['GA4'] ) && ! empty( $args['options']['GA4'] ) ) {
				foreach ( $args['options']['GA4'] as $account => $properties ) {
					$html .= '<optgroup label=" ' . esc_attr( $account ) . ' ">';
					foreach ( $properties as $property_id => $property ) {
						$html .= sprintf( '<option value="%1$s" %2$s>%3$s (%4$s)</option>', esc_attr( $property_id ), selected( $value, $property_id, false ), esc_html( $property['name'] ), esc_html( $property['code'] ) );
					}
					$html .= '</optgroup>';
				}
			}
		}
		$html .= '</select>';
		if ( ! $args['options'] ) {
			// No options available - leave as-is per original.
			$html .= $this->get_field_description( $args );
		} elseif ( isset( $args['options']->totalResults ) && ( $args['options']->totalResults < 1 ) ) {
			$html .= $this->get_field_description( $args );
			$html .= '<p class="description" style="color:#ed1515">Google Analytics account ' . ( isset( $args['options']->username ) ? esc_html( $args['options']->username ) : '' ) . ' doesn\'t have any UA property.<br /> See <a href="https://analytify.io/how-to-setup-your-account-at-google-analytics/" target="_blank">why &&how to fix it</a>.</p>';
		} else {
			$html .= $this->get_field_description( $args );
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_textarea( $args ) {
		$value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value );
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_html( $args ) {
		echo $this->get_field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method handles escaping
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_wysiwyg( $args ) {
		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';
		echo '<div style="max-width: ' . esc_attr( $size ) . ';">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Size is escaped
		$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
			'textarea_rows' => 10,
		);
		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $args['options'] );
		}
		wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );
		echo '</div>';
		echo $this->get_field_description( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method handles escaping
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_image( $args ) {
		$value   = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size    = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$id      = $args['section'] . '[' . $args['id'] . ']';
		$label   = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose Image', 'wp-analytify' );
		$img     = wp_get_attachment_image_src( (int) $value );
		$img_url = $img ? $img[0] : '';
		$html    = sprintf( '<input type="hidden" class="%1$s-text wpsa-image-id" id="%2$s" name="%2$s" value="%3$s"/>', $size, $id, $value );
		$html   .= '<p class="wpsa-image-preview"><img src="' . esc_url( $img_url ) . '" /></p>';
		$html   .= '<input type="button" class="button wpsa-image-browse" value="' . esc_attr( $label ) . '" />';
		$html   .= '<input type="button" class="button analytify_email_clear" value="' . esc_attr__( 'Remove Logo', 'wp-analytify' ) . '" />';
		$html   .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_file( $args ) {
		$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$id    = $args['section'] . '[' . $args['id'] . ']';
		$label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File', 'wp-analytify' );
		$html  = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s" name="%2$s" value="%3$s"/>', $size, $id, $value );
		$html .= '<input type="button" class="button wpsa-browse" value="' . esc_attr( $label ) . '" />';
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_password( $args ) {
		$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	/**
	 * Field callback.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	public function callback_color( $args ) {
		$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$html  = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
		$html .= $this->get_field_description( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML is properly escaped above
	}

	// UI renderers.

	/**
	 * Help tab content.
	 *
	 * @return void
	 */
	public function callback_authentication() {
		$output = '';
		ob_start();
		echo '<div class="inside">' . esc_html__( 'Set up a liaison between Analytify & your Google Analytics account.', 'wp-analytify' ) . '</div>';
		if ( get_option( 'pa_google_token' ) ) {
			$analytify_google_email = '';
			if ( isset( $GLOBALS['WP_ANALYTIFY'] ) && is_object( $GLOBALS['WP_ANALYTIFY'] ) ) {
				$analytify_google_email = $GLOBALS['WP_ANALYTIFY']->analytify_get_connected_google_account_email();
			}
			?>
			<div class="inside"></div>
			<table class="form-table"><form action="" method="post"><tbody><tr>
			<?php wp_nonce_field( 'analytify_analytics_logout', 'analytify_analytics_logout_nonce' ); ?>
			<th scope="row"><label class="pt-20">Google Authentication</label></th>
			<td><input type="submit" class="button-primary" value="Logout" name="wp_analytify_log_out" />
			<?php if ( is_string( $analytify_google_email ) && is_email( $analytify_google_email ) ) : ?>
			<p class="description analytify-google-account-email">
				<?php
				printf(
					/* translators: %s: Google account email address used for OAuth. */
					esc_html__( 'Logged in with %s', 'wp-analytify' ),
					'<strong>' . esc_html( $analytify_google_email ) . '</strong>'
				);
				?>
			</p>
			<?php else : ?>
			<p class="description analytify-google-account-email analytify-google-account-email--unavailable">
				<?php esc_html_e( 'Google account is connected; the sign-in email could not be shown.', 'wp-analytify' ); ?>
			</p>
			<?php endif; ?>
			<p class="description">You have allowed your site to access the data from your Google Analytics account. Click on logout button to disconnect or re-authenticate.</p></td>
			</tr></tbody></form></table>
			<?php
		} else {
			?>
			<table class="form-table"><tbody><tr>
			<th scope="row"><label class="pt-20">Google Authentication</label></th>
			<td>
				<a target="_self" title="Log in with your Google Analytics Account" class="button-primary authentication_btn" href="<?php echo esc_url( WP_ANALYTIFY_FUNCTIONS::analytify_create_auth_url() ); ?>"><?php esc_html_e( 'Log in with your Google Analytics Account', 'wp-analytify' ); ?></a>
				<p class="description">
				<?php
				// translators: %1$s is setup account link opening tag, %2$s is setup account link closing tag, %3$s is Google Analytics link opening tag, %4$s is Google Analytics link closing tag.
				printf( esc_html__( 'It is required to %1$sSet up your account%2$s and a website profile at %3$sGoogle Analytics%4$s to see Analytify Dashboard reports.', 'wp-analytify' ), '<a href="https://analytify.io/setup-account-google-analytics/" target="blank">', '</a>', '<a href="https://analytics.google.com/" target="blank">', '</a>' );
				?>
				</p>
			</td></tr></tbody></table>
			<?php
		}
		$output .= ob_get_contents();
		ob_end_clean();
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is properly escaped
	}

	/**
	 * Help tab content.
	 *
	 * @since 2.0
	 * @return void
	 */
	public function callback_help() {
		?>

	<div class="wrap">

		<?php
		if ( has_action( 'anlytify_pro_support_tab' ) ) {
			do_action( 'anlytify_pro_support_tab' );
		} else {
			?>

					<h3><?php esc_html_e( 'Support', 'wp-analytify' ); ?></h3>

					<p>
					<?php
					printf( // translators: Support.
						esc_html__( 'As this is a free plugin, Post all of your questions to the %1$s WordPress.org support forum %2$s. Response time can range from a couple of days to a week as this is a free support.', 'wp-analytify' ),
						'<a href="' . esc_url( 'https://wordpress.org/support/plugin/wp-analytify/' ) . '" target="_blank">',
						'</a>'
					);
					?>
						</p>

					<p class="upgrade-to-pro">
					<?php
					printf( // translators: Upgrade to Pro.
						esc_html__( 'If you want a %1$s timely response via email from a developer %2$s who works on this plugin, %3$s upgrade to Analytify Pro %4$s & send us an email.', 'wp-analytify' ),
						'<strong>',
						'</strong>',
						'<a href="' . esc_url( analytify_get_update_link( 'https://analytify.io/', '?utm_source=analytify-lite&amp;utm_medium=help-tab&amp;utm_content=support-upgrade&amp;utm_campaign=pro-upgrade' ) ) . '" target="_blank">',
						'</a>'
					);
					?>
						</p>

					<p>
					<?php
					printf( // translators: Notify the bug.
						esc_html__( 'If you\'ve found a bug, please %1$s submit an issue at Github %2$s.', 'wp-analytify' ),
						'<a href="' . esc_url( 'https://github.com/hiddenpearls/wp-analytify/issues' ) . '" target="_blank">',
						'</a>'
					);
					?>
						</p>

		<?php } ?>

		</div>

		<div class="wp-analytify-debug">
			<h3><?php esc_html_e( 'Diagnostic Information', 'wp-analytify' ); ?></h3>
			<textarea class="debug-log-textarea" autocomplete="off" readonly="" id="debug-log-textarea"></textarea>

			<div class="wp-analytify-view-error-log">
			</div>
			<div class="wp-analytify-view-error-log">
				<button id="analytify-copy-diagnostic" class="button"><?php esc_html_e( 'Copy to Clipboard', 'wp-analytify' ); ?></button>
				<button id="analytify-download-diagnostic" class="button"><?php esc_html_e( 'Download Diagnostic Log', 'wp-analytify' ); ?></button>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=analytify-logs' ) ); ?>"><?php esc_html_e( 'View Error Logs', 'wp-analytify' ); ?></a>
			</div>
		</div>

		<div class="wp-analytify-debug">
			<h3><?php esc_html_e( 'Tools', 'wp-analytify' ); ?></h3>
			
			<div class="wp-analytify-import-export">
				<div class="analytify-export-container">
					<h4><?php esc_html_e( 'Export Settings', 'wp-analytify' ); ?></h4>
					<p><?php esc_html_e( 'Export the Analytify settings file in JSON format.', 'wp-analytify' ); ?></p>
					
					<a id="analytify-export-settings" class="button" href="javascript:void(0)" data-nonce="<?php echo esc_attr( wp_create_nonce( 'import-export' ) ); ?>">
					<?php esc_html_e( 'Download Export File', 'wp-analytify' ); ?>
					</a>
				</div>
				<div class="analytify-import-container">
					<h4><?php esc_html_e( 'Import Settings', 'wp-analytify' ); ?></h4>
					<p><?php esc_html_e( 'Select the export file and click on Upload Import File button.', 'wp-analytify' ); ?></p>
					<p><?php esc_html_e( 'This will not include your Google authentication, you might need to authenticate again after import.', 'wp-analytify' ); ?></p>
					<form id="analytify-import-form">
						<input id="analytify-import-settings" type="file" accept=".json,application/json" title="Upload Import File">
						<button id="analytify-import-submit" type="submit" class="button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'import-export' ) ); ?>">
						<?php esc_html_e( 'Upload Import File', 'wp-analytify' ); ?>
						</button>
					</form>
					<p class="analytify-import-error" role="alert"></p>
					<p class="analytify-import-notice"><?php esc_html_e( 'Import completed successfully!', 'wp-analytify' ); ?></p>
					<p class="analytify-import-notice"><?php esc_html_e( 'Reloading settings page to reflect settings..', 'wp-analytify' ); ?></p>
				</div>
			</div>

			<div class="wp-analytify-tools-actions" style="margin-top: 15px;">
				<div class="analytify-refresh-container" style="margin-bottom: 10px;">
					<h4 style="margin-bottom: 5px;"><?php esc_html_e( 'Refresh Statistics', 'wp-analytify' ); ?></h4>
					<p style="margin-bottom: 8px;"><?php esc_html_e( 'Clear cached data to update your analytics with the latest stats.', 'wp-analytify' ); ?></p>
					<form class="" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="margin: 0;">
						<input type="hidden" name="action" value="analytify_delete_cache">
						<?php
						wp_nonce_field( 'analytify_delete_cache', 'analytify_delete_cache_nonce' );
						?>
						<button type="submit" class="wp-analytify-delete-cache-button button"><?php esc_html_e( 'Refresh All Stats', 'wp-analytify' ); ?></button>
					</form>
				</div>

				<div class="analytify-factory-reset-container">
					<h4 style="margin-bottom: 5px;"><?php esc_html_e( 'Factory Reset', 'wp-analytify' ); ?></h4>
					<p style="margin-bottom: 8px;"><?php esc_html_e( 'Restore all settings to their defaults. This will log you out and remove any saved configurations.', 'wp-analytify' ); ?></p>
					<div style="display: flex; gap: 10px; align-items: center;">
						<button type="button" id="btn-analytify-factory-reset" class="button analytify-button-danger">
							<?php esc_html_e( 'Reset Plugin Settings', 'wp-analytify' ); ?>
						</button>
						<span class="spinner analytify-factory-reset-spinner" style="float: none; margin: 0;"></span>
					</div>
				</div>
			</div>
		</div>
		<script>
			(function ($) {
				$(document).ready(function () {
					$('#btn-analytify-factory-reset').on('click', function () {
						var confirmMessage = '<?php echo esc_js( __( 'Are you sure you want to reset the plugin? This will delete all settings and data. This cannot be undone.', 'wp-analytify' ) ); ?>';
						if (!confirm(confirmMessage)) {
							return;
						}

						var $btn = $(this);
						var $spinner = $btn.siblings('.analytify-factory-reset-spinner');

						$btn.prop('disabled', true);
						$spinner.addClass('is-active');

						$.post(ajaxurl, {
							action: 'analytify_factory_reset',
							nonce: '<?php echo esc_js( wp_create_nonce( 'analytify_factory_reset_nonce' ) ); ?>'
						})
							.done(function (response) {
								$spinner.removeClass('is-active');
								if (response && response.success) {
									alert('<?php echo esc_js( __( 'Plugin reset successfully. Reloading...', 'wp-analytify' ) ); ?>');
									location.reload();
								} else {
									var errorMsg = (response && response.data) ? response.data : '<?php echo esc_js( __( 'Error resetting plugin.', 'wp-analytify' ) ); ?>';
									alert(errorMsg);
									$btn.prop('disabled', false);
								}
							})
							.fail(function () {
								$spinner.removeClass('is-active');
								alert('<?php echo esc_js( __( 'Error resetting plugin. Please try again.', 'wp-analytify' ) ); ?>');
								$btn.prop('disabled', false);
							});
					});
				});
			}(jQuery));
		</script>

		<div class="wp-analytify-video-container">
			<h3>
				<?php
				printf( // translators: Video.
					esc_html__( 'Videos %1$s (Subscribe To Our Youtube Channel) %2$s', 'wp-analytify' ),
					'<a href="https://www.youtube.com/c/Wp-analytify/videos" target="_blank">',
					'</a>'
				);
				?>
			</h3>
			
			<ul>
				<li>
					<h4>Generate Custom API Keys</h4>
					<p>It is highly recommended by Google to use your own API keys. Check out a <a href="https://analytify.io/google-api-tutorial" target="_blank">comprehensive tutorial</a> & a video guide to get your own ClientID, Client Secret &&Redirect URL to use in Advanced Tab.</p>
					<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/QJYzXsPJeTo" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</li>
				<li>
					<h4>How to Install Analytify Pro</h4>
					<p>Analytify Core version from wordpress.org is the base (required) to use all the addons (Free & Paid) &&Analytify Pro version.<br />Check out the Analytify Pro <a href="https://analytify.io/features/" target="_blank">features</a>.</p>
					<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/D02R6eP3olM" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</li>
				<li>
					<h4>View Google Analytics within WordPress</h4>
					<p>This video explains how to check the stats of each page in WordPress.<br />Check out the Analytify Pro <a href="https://analytify.io/features/" target="_blank">features</a></p>
					<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/6BnJiTOgCrE" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</li>
			</ul>

		</div>

		<?php
	}
	/**
	 * Help tab content.
	 *
	 * @return void
	 */
	public function callback_email_promo() {
		if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
			$url      = 'https://analytify.io/add-ons/email-notifications/?utm_source=analytify-pro&utm_medium=plugin-settings&utm_content=cta&utm_campaign=addons-upgrade';
			$url_text = __( 'Explore Email Notifications addon', 'wp-analytify' );
		} else {
			$url      = 'https://analytify.io/add-ons/email-notifications/?utm_source=analytify-lite&utm_medium=plugin-settings&utm_content=cta&utm_campaign=bundle-upgrade';
			$url_text = sprintf( '%1$s + %2$s', __( 'Explore Analytify Pro', 'wp-analytify' ), __( 'Email Notifications bundle', 'wp-analytify' ) );
		}
		?>
		<div class="analytify-email-promo-contianer">
			<img src="<?php echo esc_url( ( defined( 'ANALYTIFY_PLUGIN_URL' ) ? ANALYTIFY_PLUGIN_URL : '' ) . 'assets/img/email-promo.png' ); ?>" alt="promo">
			<div class="analytify-email-premium-overlay">
				<div class="analytify-email-premium-popup">
					<h3 class="analytify-promo-popup-heading"><?php esc_html_e( 'Unlock weekly and monthly reports', 'wp-analytify' ); ?></h3>
					<p class="analytify-promo-popup-paragraph"><?php esc_html_e( 'Email notifications add-on extends the Analytify Pro, and enables more control on customizing Analytics Email reports for your websites, delivers Analytics summaries straight in your inbox weekly and monthly.', 'wp-analytify' ); ?></p>
					<ul class="analytify-promo-popup-list">
						<li><?php esc_html_e( 'Add your logo', 'wp-analytify' ); ?></li>
						<li><?php esc_html_e( 'Choose your own metrics to display in reports', 'wp-analytify' ); ?></li>
						<li><?php esc_html_e( 'Edit Email Subject', 'wp-analytify' ); ?></li>
						<li><?php esc_html_e( 'Add personal note', 'wp-analytify' ); ?></li>
						<li><?php esc_html_e( 'Schedule weekly reports', 'wp-analytify' ); ?></li>
						<li><?php esc_html_e( 'Schedule monthly reports', 'wp-analytify' ); ?></li>
					</ul>
					<a href="<?php echo esc_url( $url ); ?>" class="analytify-promo-popup-btn" target="_blank"><?php echo esc_html( $url_text ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Help tab content.
	 *
	 * @return void
	 */
	public function callback_email_form() {
		?>
		<form class="analyitfy-email-test" action="" method="post">
			<input type="submit" name="test_email" class="analytify_test_email_btn" value="<?php esc_attr_e( 'Test Email', 'wp-analytify' ); ?>" />
			<span class="analytify_setting_note"><?php esc_html_e( 'Note: Please save changes before sending a test email.', 'wp-analytify' ); ?></span>
		</form>
		<?php
	}
}