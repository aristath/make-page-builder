<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_edit_page_script' ) ) :
/**
 * Enqueue scripts that run on the Edit Page screen
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_edit_page_script() {
	global $pagenow;

	wp_enqueue_script(
		'ttfmake-admin-edit-page',
		get_template_directory_uri() . '/js/admin/edit-page.js',
		array( 'jquery' ),
		TTFMAKE_VERSION,
		true
	);

	wp_localize_script(
		'ttfmake-admin-edit-page',
		'ttfmakeEditPageData',
		array(
			'featuredImage' => __( 'Featured images are not available for this page while using the current page template.', 'make' ),
			'pageNow'       => esc_js( $pagenow ),
		)
	);
}
endif;

add_action( 'admin_enqueue_scripts', 'ttfmake_edit_page_script' );

/**
 * Add a Make Plus metabox to each qualified post type edit screen
 *
 * @since  1.0.6.
 *
 * @return void
 */
function ttfmake_add_plus_metabox() {
	// Post types
	$post_types = get_post_types(
		array(
			'public' => true,
			'_builtin' => false
		)
	);
	$post_types[] = 'post';
	$post_types[] = 'page';

	// Add the metabox for each type
	foreach ( $post_types as $type ) {
		add_meta_box(
			'ttfmake-plus-metabox',
			__( 'Layout Settings', 'make' ),
			'ttfmake_render_plus_metabox',
			$type,
			'side',
			'default'
		);
	}
}

add_action( 'add_meta_boxes', 'ttfmake_add_plus_metabox' );

