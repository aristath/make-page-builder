<?php
/**
 * The suffix to use for scripts.
 */
if ( ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ) {
	define( 'TTFMAKE_SUFFIX', '' );
} else {
	define( 'TTFMAKE_SUFFIX', '.min' );
}

// Custom functions that act independently of the theme templates
require get_template_directory() . '/inc/extras.php';
/**
 * Admin includes.
 */
if ( is_admin() ) {
	// Page customizations
	require get_template_directory() . '/inc/edit-page.php';

	// Page Builder
	require get_template_directory() . '/inc/builder/core/base.php';
}
