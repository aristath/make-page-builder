<?php
global $maera_pb_overlay_class, $maera_pb_section_data, $maera_pb_is_js_template, $maera_pb_overlay_title;
$maera_pb_overlay_class = 'maera_pb-configuration-overlay';
$maera_pb_overlay_title = __( 'Configure section', 'maera' );
$section_name          = Maera_PB()->sections->get_section_name( $maera_pb_section_data, $maera_pb_is_js_template );

// Include the header
Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'header' );

// Sort the config in case 3rd party code added another input
ksort( $maera_pb_section_data['section']['config'], SORT_NUMERIC );

// Print the inputs
$output = '';

foreach ( $maera_pb_section_data['section']['config'] as $input ) {
	if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
		$output .= Maera_PB_Config::create_input( $section_name, $input, $maera_pb_section_data['data'] );
	}
}

echo $output;

Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'footer' );
