<?php
/**
 * @package Make
 */

if ( ! function_exists( 'make_pb_edit_page_script' ) ) :
/**
 * Enqueue scripts that run on the Edit Page screen
 *
 * @since  1.0.0.
 *
 * @return void
 */
function make_pb_edit_page_script() {
	global $pagenow;

	wp_enqueue_script(
		'make_pb-admin-edit-page',
		Make_PB::uri() . '/js/admin/edit-page.js',
		array( 'jquery' ),
		Make_PB::version(),
		true
	);

	wp_localize_script(
		'make_pb-admin-edit-page',
		'make_pbEditPageData',
		array(
			'featuredImage' => __( 'Featured images are not available for this page while using the current page template.', 'make' ),
			'pageNow'       => esc_js( $pagenow ),
		)
	);
}
endif;

add_action( 'admin_enqueue_scripts', 'make_pb_edit_page_script' );
