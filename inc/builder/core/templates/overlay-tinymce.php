<?php
global $make_pb_overlay_id, $make_pb_overlay_title;
$make_pb_overlay_id    = 'make_pb-tinymce-overlay';
$make_pb_overlay_title = __( 'Edit content', 'make' );

Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'header' );

wp_editor( '', 'make', array(
	'tinymce'       => array(
		'wp_autoresize_on' => false,
		'resize'           => false,
	),
	'editor_height' => 320
) );

Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'footer' );
