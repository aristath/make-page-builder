<?php

class Make_PB_Config {

	/**
	 * Generate a select input for the configuration overlay.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_select( $section_name, $args, $section_data ) {
		$return        = '';
		$current_value = self::get_current_configuration_value( $section_data, $args );

		if ( isset( $args['default'] ) && isset( $args['options'] ) ) {
			$id     = $section_name . '[' . $args['name'] . ']';
			$label  = ( isset( $args['label'] ) ) ? '<label for="' . $id . '">' . esc_html( $args['label'] ) . '</label>' : '';
			$class  = ( isset( $args['class'] ) ) ? ' class="' . esc_attr( $args['class'] ) . '"' : '';
			$description = ( isset( $args['description'] ) ) ? '<div class="make_pb-configuration-description">' . esc_html( $args['description'] ) . '</div>': '';
			$select = '<select id="' . $id . '"' . $class .' name="' . $id . '">%s</select>';

			$options = '';

			foreach ( $args['options'] as $key => $value ) {
				$options .= '<option value="' . esc_attr( $key ) . '"' . selected( $key, $current_value, false ) . '>' . $value . '</option>';
			}

			$return = $label . sprintf( $select, $options ) . $description;
		}

		return $return;
	}

	/**
	 * Generate a checkbox input for the configuration overlay.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_checkbox( $section_name, $args, $section_data ) {
		$current_value = self::get_current_configuration_value( $section_data, $args );
		$id          = $section_name . '[' . $args['name'] . ']';
		$label       = ( isset( $args['label'] ) ) ? '<label for="' . $id . '">' . esc_html( $args['label'] ) . '</label>' : '';
		$description = ( isset( $args['description'] ) ) ? '<div class="make_pb-configuration-description">' . esc_html( $args['description'] ) . '</div>': '';
		$args        = '<input id="' . $id . '" type="checkbox" name="' . $id . '" value="1"' . checked( 1, $current_value, false ) . '>' . $description;

		return  $label . $args;
	}

	/**
	 * Generate a text input for the configuration overlay.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_text( $section_name, $args, $section_data ) {
		$current_value = self::get_current_configuration_value( $section_data, $args );
		$id          = $section_name . '[' . $args['name'] . ']';
		$label       = ( isset( $args['label'] ) ) ? '<label for="' . $id . '">' . esc_html( $args['label'] ) . '</label>' : '';

		return  $label . '<input type="text" id="' . $id . '" name="' . $id . '" value="' . $current_value . '" />';
	}

	/**
	 * Generate a image uploader input for the configuration overlay.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_image( $section_name, $args, $section_data ) {
		$current_value = self::get_current_configuration_value( $section_data, $args );
		$name        = $section_name . '[' . $args['name'] . ']';
		$label       = ( isset( $args['label'] ) ) ? '<label for="' . $name . '">' . esc_html( $args['label'] ) . '</label>' : '';

		return $label . make_pb_get_builder_base()->add_uploader( $name, $current_value, __( 'Set image', 'make' ) );
	}

	/**
	 * Generate a color picker input for the configuration overlay.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_color( $section_name, $args, $section_data ) {
		$current_value = self::get_current_configuration_value( $section_data, $args );
		$name        = $section_name . '[' . $args['name'] . ']';
		$label       = ( isset( $args['label'] ) ) ? '<label for="' . $name . '">' . esc_html( $args['label'] ) . '</label>' : '';
		$class       = ( isset( $args['class'] ) ) ? ' class="' . esc_attr( $args['class'] ) . '"' : '';

		return  $label . '<input id="' . $name . '" type="text" name="' . $name . '" ' . $class . ' value="' . $current_value . '" />';
	}

	/**
	 * Generate a section title input for the configuration overlay.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_section_title( $section_name, $args, $section_data ) {
		$current_value = self::get_current_configuration_value( $section_data, $args );
		$placeholder = ( isset( $args['label'] ) ) ? ' placeholder="' . esc_attr( $args['label'] ) . '"' : '';
		$name        = 'name="' . $section_name . '[' . esc_attr( $args['name'] ) . ']"';
		$class       = ( isset( $args['class'] ) ) ? ' ' . esc_attr( $args['class'] ) : '';

		return  '<input' . $placeholder . ' type="text" ' . $name . ' value="' . $current_value . '" class="make_pb-title' . $class . '" autocomplete="off">';
	}

	/**
	 * Get the current or default value for an input.
	 *
	 * @param  array     $section_data    The data for the current section.
	 * @param  array     $args            Arguments for creating the input.
	 * @return string                     The current value for the input.
	 */
	public static function get_current_configuration_value( $section_data, $args ) {
		$default_value = ( isset( $args['default'] ) ) ? $args['default'] : '';
		$current_value = ( isset( $section_data[ $args['name'] ] ) ) ? $section_data[ $args['name'] ] : $default_value;
		return $current_value;
	}

	/**
	 * Create an input with header and footer wrapper.
	 *
	 * @param  string    $section_name    The section prefix for the input name.
	 * @param  array     $args            Arguments for creating the input.
	 * @param  array     $section_data    The data for the current section.
	 * @return string                     The full input string.
	 */
	public static function create_input( $section_name, $args, $section_data ) {
		$final_output = '';

		if ( isset( $args['type'] ) ) {
			// Get the input HTML
			$function_name = 'create_' . $args['type'];

			$input_html = call_user_func( 'Make_PB_Config::' . $function_name, $section_name, $args, $section_data );

			/**
			 * Filter the wrapped used for the inputs.
			 *
			 * @param string    $wrapper         The HTML to wrap around the input.
			 * @param string    $args            The input data that is wrapped.
			 * @param string    $section_data    The data for the section.
			 */
			$wrap = apply_filters( 'make_configuration_overlay_input_wrap', '<div class="make_pb-configuration-overlay-input-wrap %1$s">%2$s</div>', $args, $section_data );

			/**
			 * Filter the HTML for the input.
			 *
			 * @param string    $this_output     The HTML for the input.
			 * @param string    $args            The input data.
			 * @param string    $section_data    The data for the section.
			 */
			$input_html = apply_filters( 'make_configuration_overlay_input', $input_html, $args, $section_data );

			if ( ! empty( $input_html ) ) {
				// Add "-wrap" to each class
				$class        = ( isset( $args['class'] ) ) ? esc_attr( str_replace( ' ', '-wrap ', $args['class'] ) . '-wrap' ) : '';
				$final_output = sprintf( $wrap, $class, $input_html );
			}

		}

		return $final_output;
	}

}
