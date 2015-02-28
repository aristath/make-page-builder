<?php
global $make_pb_overlay_class, $make_pb_section_data, $make_pb_is_js_template, $make_pb_overlay_title;
$make_pb_overlay_class = 'make_pb-configuration-overlay';
$make_pb_overlay_title = __( 'Configure section', 'make' );
$section_name          = make_pb_get_section_name( $make_pb_section_data, $make_pb_is_js_template );

// Include the header
Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'header' );

// Sort the config in case 3rd party code added another input
ksort( $make_pb_section_data['section']['config'], SORT_NUMERIC );

// Print the inputs
$output = '';

foreach ( $make_pb_section_data['section']['config'] as $input ) {
	if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
		$output .= Make_PB_Config::create_input( $section_name, $input, $make_pb_section_data['data'] );
	}
}

echo $output;

Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'footer' );
