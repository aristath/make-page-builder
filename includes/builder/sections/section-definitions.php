<?php

class Make_PB_Section_Definitions {

	private static $instance;

	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function __construct() {

		add_action( 'admin_footer', array( $this, 'print_templates' ) );

	}

	/**
	 * Print out the JS section templates
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function print_templates() {
		global $hook_suffix, $typenow, $make_pb_is_js_template;
		$make_pb_is_js_template = true;

		// Define the templates to print
		$templates = array(
			array(
				'id' => 'gallery-item',
				'builder_template' => 'sections/builder-templates/gallery-item',
				'path' => 'includes/builder/',
			),
			array(
				'id' => 'banner-slide',
				'builder_template' => 'sections/builder-templates/banner-slide',
				'path' => 'includes/builder/',
			),
		);

		// Print the templates
		foreach ( $templates as $template ) : ?>
		<script type="text/html" id="tmpl-make_pb-<?php echo $template['id']; ?>">
			<?php
			ob_start();
			make_pb_get_builder_base()->load_section( $template, array() );
			$html = ob_get_clean();
			$html = str_replace(
				array(
					'temp',
				),
				array(
					'{{{ id }}}',
				),
				$html
			);
			echo $html;
			?>
		</script>
		<?php endforeach;
		unset( $GLOBALS['make_pb_is_js_template'] );
	}

}


if ( ! function_exists( 'make_pb_get_section_default' ) ) :
/**
 * Return the default value for a particular section setting.
 *
 * @since 1.0.4.
 *
 * @param  string    $key             The key for the section setting.
 * @param  string    $section_type    The section type.
 * @return mixed                      Default value if found; false if not found.
 */
function make_pb_get_section_default( $key, $section_type ) {
	$defaults = Make_PB()->sections->get_section_defaults();
	$id       = "$section_type-$key";
	$value    = ( isset( $defaults[ $id ] ) ) ? $defaults[ $id ] : false;

	/**
	 * Filter the default section data that is received.
	 *
	 * @since 1.2.3.
	 *
	 * @param mixed     $value           The section value.
	 * @param string    $key             The key to get data for.
	 * @param string    $section_type    The type of section the data is for.
	 */
	return apply_filters( 'make_get_section_default', $value, $key, $section_type );
}
endif;

function make_pb_get_section_choices( $key, $section_type ) {
	return Make_PB()->sections->get_choices( $key, $section_type );
}


if ( ! function_exists( 'make_pb_sanitize_section_choice' ) ) :
/**
 * Sanitize a value from a list of allowed values.
 *
 * @since 1.0.4.
 *
 * @param  string|int $value The current value of the section setting.
 * @param  string        $key             The key for the section setting.
 * @param  string        $section_type    The section type.
 * @return mixed                          The sanitized value.
 */
function make_pb_sanitize_section_choice( $value, $key, $section_type ) {
	$choices         = make_pb_get_section_choices( $key, $section_type );
	$allowed_choices = array_keys( $choices );

	if ( ! in_array( $value, $allowed_choices ) ) {
		$value = make_pb_get_section_default( $key, $section_type );
	}

	/**
	 * Allow developers to alter a section choice during the sanitization process.
	 *
	 * @since 1.2.3.
	 *
	 * @param mixed     $value           The value for the section choice.
	 * @param string    $key             The key for the section choice.
	 * @param string    $section_type    The section type.
	 */
	return apply_filters( 'make_sanitize_section_choice', $value, $key, $section_type );
}
endif;

/**
 * Instantiate or return the one Make_PB_Section_Definitions instance.
 *
 * @since  1.0.0.
 *
 * @return Make_PB_Section_Definitions
 */
function make_pb_get_section_definitions() {
	return Make_PB_Section_Definitions::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	add_action( 'after_setup_theme', 'make_pb_get_section_definitions', 11 );
}
