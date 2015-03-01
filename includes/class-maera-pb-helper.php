<?php

class Maera_PB_Helper {

	/**
	 * Convert an array with array keys that map to a multidimensional array to the array.
	 *
	 * @param  array    $arr    The array to convert.
	 * @return array            The converted array.
	 */
	public static function create_array_from_meta_keys( $arr ) {
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
				$step = $pieces[$i];
				if ( ! isset( $current[ $step ] ) ) {
					$current[$step] = array();
				}
				$current = & $current[$step];
			}

			// Add the current value into the final nested sub-array
			$current[$pieces[$i]] = $value;
		}

		// Return the result array
		return $result;
	}

	/**
	 * Retrieve all of the data for the sections.
	 *
	 * @param  string    $post_id    The post to retrieve the data from.
	 * @return array                 The combined data.
	 */
	function get_section_data( $post_id ) {
		$ordered_data = array();
		$ids          = get_post_meta( $post_id, '_maera_pb-section-ids', true );
		$ids          = ( ! empty( $ids ) && is_array( $ids ) ) ? array_map( 'strval', $ids ) : $ids;
		$post_meta    = get_post_meta( $post_id );

		// Temp array of hashed keys
		$temp_data = array();

		// Any meta containing the old keys should be deleted
		if ( is_array( $post_meta ) ) {
			foreach ( $post_meta as $key => $value ) {
				// Only consider builder values
				if ( 0 === strpos( $key, '_maera_pb:' ) ) {
					// Get the individual pieces
					$temp_data[ str_replace( '_maera_pb:', '', $key ) ] = $value[0];
				}
			}
		}

		// Create multidimensional array from postmeta
		$data = self::create_array_from_meta_keys( $temp_data );

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
		return apply_filters( 'maera_get_section_data', $ordered_data, $post_id );
	}

}
