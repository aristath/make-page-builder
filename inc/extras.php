<?php
/**
 * @package Make
 */

if ( ! function_exists( 'make_pb_get_view' ) ) :
/**
 * Determine the current view.
 *
 * For use with view-related theme options.
 *
 * @since  1.0.0.
 *
 * @return string    The string representing the current view.
 */
function make_pb_get_view() {
	// Post types
	$post_types = get_post_types(
		array(
			'public' => true,
			'_builtin' => false
		)
	);
	$post_types[] = 'post';

	// Post parent
	$parent_post_type = '';
	if ( is_attachment() ) {
		$post_parent      = get_post()->post_parent;
		$parent_post_type = get_post_type( $post_parent );
	}

	$view = 'post';

	// Blog
	if ( is_home() ) {
		$view = 'blog';
	}
	// Archives
	else if ( is_archive() ) {
		$view = 'archive';
	}
	// Search results
	else if ( is_search() ) {
		$view = 'search';
	}
	// Posts and public custom post types
	else if ( is_singular( $post_types ) || ( is_attachment() && in_array( $parent_post_type, $post_types ) ) ) {
		$view = 'post';
	}
	// Pages
	else if ( is_page() || ( is_attachment() && 'page' === $parent_post_type ) ) {
		$view = 'page';
	}

	/**
	 * Allow developers to dynamically change the view.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $view                The view name.
	 * @param string    $parent_post_type    The post type for the parent post of the current post.
	 */
	return apply_filters( 'make_get_view', $view, $parent_post_type );
}
endif;

if ( ! function_exists( 'make_pb_get_section_data' ) ) :
/**
 * Retrieve all of the data for the sections.
 *
 * @since  1.2.0.
 *
 * @param  string    $post_id    The post to retrieve the data from.
 * @return array                 The combined data.
 */
function make_pb_get_section_data( $post_id ) {
	$ordered_data = array();
	$ids          = get_post_meta( $post_id, '_make_pb-section-ids', true );
	$ids          = ( ! empty( $ids ) && is_array( $ids ) ) ? array_map( 'strval', $ids ) : $ids;
	$post_meta    = get_post_meta( $post_id );

	// Temp array of hashed keys
	$temp_data = array();

	// Any meta containing the old keys should be deleted
	if ( is_array( $post_meta ) ) {
		foreach ( $post_meta as $key => $value ) {
			// Only consider builder values
			if ( 0 === strpos( $key, '_make_pb:' ) ) {
				// Get the individual pieces
				$temp_data[ str_replace( '_make_pb:', '', $key ) ] = $value[0];
			}
		}
	}

	// Create multidimensional array from postmeta
	$data = make_pb_create_array_from_meta_keys( $temp_data );

	// Reorder the data in the order specified by the section IDs
	if ( is_array( $ids ) ) {
		foreach ( $ids as $id ) {
			if ( isset( $data[ $id ] ) ) {
				$ordered_data[ $id ] = $data[ $id ];
			}
		}
	}

	/**
	 * Filter the section data for a post.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $ordered_data    The array of section data.
	 * @param int      $post_id         The post ID for the retrieved data.
	 */
	return apply_filters( 'make_get_section_data', $ordered_data, $post_id );
}
endif;

if ( ! function_exists( 'make_pb_create_array_from_meta_keys' ) ) :
/**
 * Convert an array with array keys that map to a multidimensional array to the array.
 *
 * @since  1.2.0.
 *
 * @param  array    $arr    The array to convert.
 * @return array            The converted array.
 */
function make_pb_create_array_from_meta_keys( $arr ) {
	// The new multidimensional array we will return
	$result = array();

	// Process each item of the input array
	foreach ( $arr as $key => $value ) {
		// Store a reference to the root of the array
		$current = & $result;

		// Split up the current item's key into its pieces
		$pieces = explode( ':', $key );

		/**
		 * For all but the last piece of the key, create a new sub-array (if necessary), and update the $current
		 * variable to a reference of that sub-array.
		 */
		for ( $i = 0; $i < count( $pieces ) - 1; $i++ ) {
			$step = $pieces[ $i ];
			if ( ! isset( $current[ $step ] ) ) {
				$current[ $step ] = array();
			}
			$current = & $current[ $step ];
		}

		// Add the current value into the final nested sub-array
		$current[ $pieces[ $i ] ] = $value;
	}

	// Return the result array
	return $result;
}
endif;

if ( ! function_exists( 'make_pb_is_builder_page' ) ) :
/**
 * Determine if the post uses the builder or not.
 *
 * @since  1.2.0.
 *
 * @param  int     $post_id    The post to inspect.
 * @return bool                True if builder is used for post; false if it is not.
 */
function make_pb_is_builder_page( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	// Pages will use the template-builder.php template to denote that it is a builder page
	$has_builder_template = ( 'template-builder.php' === get_page_template_slug( $post_id ) );

	// Other post types will use meta data to support builder pages
	$has_builder_meta = ( 1 === (int) get_post_meta( $post_id, '_make_pb-use-builder', true ) );

	$is_builder_page = $has_builder_template || $has_builder_meta;

	/**
	 * Allow a developer to dynamically change whether the post uses the builder or not.
	 *
	 * @since 1.2.3
	 *
	 * @param bool    $is_builder_page    Whether or not the post uses the builder.
	 * @param int     $post_id            The ID of post being evaluated.
	 */
	return apply_filters( 'make_is_builder_page', $is_builder_page, $post_id );
}
endif;

if ( ! function_exists( 'make_pb_builder_css' ) ) :
/**
 * Trigger an action hook for each section on a Builder page for the purpose
 * of adding section-specific CSS rules to the document head.
 *
 * @since 1.4.5
 *
 * @return void
 */
function make_pb_builder_css() {
	if ( make_pb_is_builder_page() ) {
		$sections = make_pb_get_section_data( get_the_ID() );

		if ( ! empty( $sections ) ) {
			foreach ( $sections as $id => $data ) {
				if ( isset( $data['section-type'] ) ) {
					/**
					 * Allow section-specific CSS rules to be added to the document head of a Builder page.
					 *
					 * @since 1.4.5
					 *
					 * @param array    $data    The Builder section's data.
					 * @param int      $id      The ID of the Builder section.
					 */
					do_action( 'make_builder_' . $data['section-type'] . '_css', $data, $id );
				}
			}
		}
	}
}
endif;

add_action( 'make_css', 'make_pb_builder_css' );

if ( ! function_exists( 'make_pb_builder_banner_css' ) ) :
/**
 * Add frontend CSS rules for Banner sections based on certain section options.
 *
 * @since 1.4.5
 *
 * @param array    $data    The banner's section data.
 * @param int      $id      The banner's section ID.
 *
 * @return void
 */
function make_pb_builder_banner_css( $data, $id ) {
	$responsive = ( isset( $data['responsive'] ) ) ? $data['responsive'] : 'balanced';
	$slider_height = absint( $data['height'] );
	if ( 0 === $slider_height ) {
		$slider_height = 600;
	}
	$slider_ratio = ( $slider_height / 960 ) * 100;

	if ( 'aspect' === $responsive ) {
		make_pb_get_css()->add( array(
			'selectors'    => array( '#builder-section-' . esc_attr( $id ) . ' .builder-banner-slide' ),
			'declarations' => array(
				'padding-bottom' => $slider_ratio . '%'
			),
		) );
	} else {
		make_pb_get_css()->add( array(
			'selectors'    => array( '#builder-section-' . esc_attr( $id ) . ' .builder-banner-slide' ),
			'declarations' => array(
				'padding-bottom' => $slider_height . 'px'
			),
		) );
		make_pb_get_css()->add( array(
			'selectors'    => array( '#builder-section-' . esc_attr( $id ) . ' .builder-banner-slide' ),
			'declarations' => array(
				'padding-bottom' => $slider_ratio . '%'
			),
			'media'        => 'screen and (min-width: 600px) and (max-width: 960px)'
		) );
	}
}
endif;

add_action( 'make_builder_banner_css', 'make_pb_builder_banner_css', 10, 2 );
