<?php
/**
 * @package Make
 */

if ( ! function_exists( 'make_pb_maybe_show_site_region' ) ) :
/**
 * Output the site region (header or footer) markup if the current view calls for it.
 *
 * @since  1.0.0.
 *
 * @param  string    $region    Region to maybe show.
 * @return void
 */
function make_pb_maybe_show_site_region( $region ) {
	if ( ! in_array( $region, array( 'header', 'footer' ) ) ) {
		return;
	}

	// Get the view
	$view = make_pb_get_view();

	// Get the relevant option
	$hide_region = (bool) get_theme_mod( 'layout-' . $view . '-hide-' . $region, make_pb_get_default( 'layout-' . $view . '-hide-' . $region ) );

	if ( true !== $hide_region ) {
		get_template_part(
			'partials/' . $region . '-layout',
			get_theme_mod( $region . '-layout', make_pb_get_default( $region . '-layout' ) )
		);
	}
}
endif;
