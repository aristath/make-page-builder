<?php

class Make_PB_Sections {

	private $_sections = array();
	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __constructor() {}

	/**
	 * Return the sections.
	 */
	public function get_sections() {
		return $this->_sections;
	}

	/**
	 * Add a section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id                  Unique ID for the section. Alphanumeric characters only.
	 * @param  string    $label               Name to display for the section.
	 * @param  string    $description         Section description.
	 * @param  string    $icon                URL to the icon for the display.
	 * @param  string    $save_callback       Function to save the content.
	 * @param  string    $builder_template    Path to the template used in the builder.
	 * @param  string    $display_template    Path to the template used for the frontend.
	 * @param  int       $order               The order in which to display the item.
	 * @param  string    $path                The path to the template files.
	 * @param  array     $config              Array of configuration options for the section.
	 * @return void
	 */
	public function add_section( $id, $label, $icon, $description, $save_callback, $builder_template, $display_template, $order, $path, $config = array() ) {

		$this->_sections[$id] = apply_filters( 'make_add_section', array(
			'id'               => $id,
			'label'            => $label,
			'icon'             => $icon,
			'description'      => $description,
			'save_callback'    => $save_callback,
			'builder_template' => $builder_template,
			'display_template' => $display_template,
			'order'            => $order,
			'path'             => $path,
			'config'           => $config,
		) );

	}

	/**
	 * Generate the name of a section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $data              The data for the section.
	 * @param  array     $is_js_template    Whether a JS template is being printed or not.
	 * @return string                       The name of the section.
	 */
	public function get_section_name( $data, $is_js_template ) {
		$name = 'make_pb-section';

		if ( $is_js_template ) {
			$name .= '[{{{ id }}}]';
		} else {
			$name .= '[' . $data['data']['id'] . ']';
		}

		return apply_filters( 'make_get_section_name', $name, $data, $is_js_template );

	}

	/**
	 * Load a section front- or back-end section template. Searches for child theme versions
	 * first, then parent themes, then plugins.
	 *
	 * @param  string    $slug    The relative path and filename (w/out suffix) required to substitute the template in a child theme.
	 * @param  string    $path    An optional path extension to point to the template in the parent theme or a plugin.
	 * @return string             The template filename if one is located.
	 */
	function load_template( $slug, $path ) {
		$templates = array(
			$slug . '.php',
			trailingslashit( $path ) . $slug . '.php'
		);

		$templates = apply_filters( 'make_load_section_template', $templates, $slug, $path );

		if ( '' === $located = Make_PB::locate_template( $templates, true, false ) ) {
			if ( isset( $templates[1] ) && file_exists( $templates[1] ) ) {
				require( $templates[1] );
				$located = $templates[1];
			}
		}

		return $located;
	}

}


function make_pb_get_sections_by_order() {
	$sections = Make_PB()->sections->get_sections();
	usort( $sections, 'make_pb_sorter' );
	return $sections;
}

function make_pb_sorter( $a, $b ) {
	return $a['order'] - $b['order'];
}
