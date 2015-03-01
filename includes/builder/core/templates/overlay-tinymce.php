<?php
global $maera_pb_overlay_id, $maera_pb_overlay_title;
$maera_pb_overlay_id    = 'maera_pb-tinymce-overlay';
$maera_pb_overlay_title = __( 'Edit content', 'maera' );

Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'header' );

wp_editor( '', 'maera', array(
	'tinymce'       => array(
		'wp_autoresize_on' => false,
		'resize'           => false,
	),
	'editor_height' => 320
) );

Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'footer' );
