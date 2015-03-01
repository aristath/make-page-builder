<?php

class Maera_PB_Image {

	/**
	 * Get an image to display in page builder backend or front end template.
	 *
	 * This function allows image IDs defined with a negative number to surface placeholder images. This allows templates to
	 * approximate real content without needing to add images to the user's media library.
	 *
	 * @param  int       $image_id    The attachment ID. Dimension value IDs represent placeholders (100x150).
	 * @param  string    $size        The image size.
	 * @return string                 HTML for the image. Empty string if image cannot be produced.
	 */
	function get_image( $image_id, $size ) {
		$return = '';

		if ( false === strpos( $image_id, 'x' ) ) {

			$return = wp_get_attachment_image( $image_id, $size );

		} else {

			$image = self::get_placeholder_image( $image_id );
			if ( ! empty( $image ) && isset( $image['src'] ) && isset( $image['alt'] ) && isset( $image['class'] ) && isset( $image['height'] ) && isset( $image['width'] ) ) {
				$return = '<img src="' . $image['src'] . '" alt="' . $image['alt'] . '" class="' . $image['class'] . '" height="' . $image['height'] . '" width="' . $image['width'] . '" />';
			}

		}

		return apply_filters( 'maera_get_image', $return, $image_id, $size );

	}

	/**
	 * Get an image's src.
	 *
	 * @param  int       $image_id    The attachment ID. Dimension value IDs represent placeholders (100x150).
	 * @param  string    $size        The image size.
	 * @return string                 URL for the image.
	 */
	function get_image_src( $image_id, $size ) {
		$src = '';

		if ( false === strpos( $image_id, 'x' ) ) {
			$image = wp_get_attachment_image_src( $image_id, $size );

			if ( false !== $image && isset( $image[0] ) ) {
				$src = $image;
			}
		} else {
			$image = self::get_placeholder_image( $image_id );

			if ( isset( $image['src'] ) ) {
				$wp_src = array(
					0 => $image['src'],
					1 => $image['width'],
					2 => $image['height'],
				);
				$src = array_merge( $image, $wp_src );
			}
		}

		return apply_filters( 'maera_get_image_src', $src, $image_id, $size );

	}

	/**
	 * Gets the specified placeholder image.
	 *
	 * @param  int      $image_id    Image ID. Should be a dimension value (100x150).
	 * @return array                 The image data, including 'src', 'alt', 'class', 'height', and 'width'.
	 */
	function get_placeholder_image( $image_id ) {
		global $maera_pb_placeholder_images;
		$return = array();

		if ( isset( $maera_pb_placeholder_images[ $image_id ] ) ) {
			$return = $maera_pb_placeholder_images[ $image_id ];
		}

		return apply_filters( 'maera_get_placeholder_image', $return, $image_id, $maera_pb_placeholder_images );

	}

	/**
	 * Add a new placeholder image.
	 *
	 * @param  int      $id      The ID for the image. Should be a dimension value (100x150).
	 * @param  array    $data    The image data, including 'src', 'alt', 'class', 'height', and 'width'.
	 * @return void
	 */
	function register_placeholder_image( $id, $data ) {
		global $maera_pb_placeholder_images;
		$maera_pb_placeholder_images[ $id ] = $data;
	}

}
