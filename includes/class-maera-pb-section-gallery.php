<?php

class Maera_PB_Section_Gallery {

	private static $instance;

	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function __construct() {

		$this->register_gallery_section();

	}

	/**
	 * Register the gallery section.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function register_gallery_section() {
		Maera_PB()->sections->add_section(
			'gallery',
			_x( 'Gallery', 'section name', 'maera' ),
			Maera_PB::uri() . '/includes/builder/sections/css/images/gallery.png',
			__( 'Display your images in various grid combinations.', 'maera' ),
			array( $this, 'save_gallery' ),
			'sections/builder-templates/gallery',
			'sections/front-end-templates/gallery',
			400,
			'includes/builder/',
			array(
				100 => array(
					'type'  => 'section_title',
					'name'  => 'title',
					'label' => __( 'Enter section title', 'maera' ),
					'class' => 'maera_pb-configuration-title maera_pb-section-header-title-input',
				),
				200 => array(
					'type'    => 'select',
					'name'    => 'columns',
					'label'   => __( 'Columns', 'maera' ),
					'class'   => 'maera_pb-gallery-columns',
					'default' => 3,
					'options' => array(
						1 => 1,
						2 => 2,
						3 => 3,
						4 => 4,
					)
				),
				300 => array(
					'type'    => 'select',
					'name'    => 'aspect',
					'label'   => __( 'Aspect ratio', 'maera' ),
					'default' => 'square',
					'options' => array(
						'square'    => __( 'Square', 'maera' ),
						'landscape' => __( 'Landscape', 'maera' ),
						'portrait'  => __( 'Portrait', 'maera' ),
						'none'      => __( 'None', 'maera' ),
					)
				),
				400 => array(
					'type'    => 'select',
					'name'    => 'captions',
					'label'   => __( 'Caption style', 'maera' ),
					'default' => 'reveal',
					'options' => array(
						'reveal'  => __( 'Reveal', 'maera' ),
						'overlay' => __( 'Overlay', 'maera' ),
						'none'    => __( 'None', 'maera' ),
					)
				),
				500 => array(
					'type'    => 'select',
					'name'    => 'caption-color',
					'label'   => __( 'Caption color', 'maera' ),
					'default' => 'light',
					'options' => array(
						'light'  => __( 'Light', 'maera' ),
						'dark' => __( 'Dark', 'maera' ),
					)
				),
				600 => array(
					'type'  => 'image',
					'name'  => 'background-image',
					'label' => __( 'Background image', 'maera' ),
					'class' => 'maera_pb-configuration-media'
				),
				700 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Darken background to improve readability', 'maera' ),
					'name'    => 'darken',
					'default' => 0,
				),
				800 => array(
					'type'    => 'select',
					'name'    => 'background-style',
					'label'   => __( 'Background style', 'maera' ),
					'default' => 'tile',
					'options' => array(
						'tile'  => __( 'Tile', 'maera' ),
						'cover' => __( 'Cover', 'maera' ),
					),
				),
				900 => array(
					'type'    => 'color',
					'label'   => __( 'Background color', 'maera' ),
					'name'    => 'background-color',
					'class'   => 'maera_pb-gallery-background-color maera_pb-configuration-color-picker',
					'default' => '',
				),
			)
		);
	}


	/**
	 * Save the data for the gallery section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save_gallery( $data ) {
		$clean_data = array();

		if ( isset( $data['columns'] ) ) {
			if ( in_array( $data['columns'], range( 1, 4 ) ) ) {
				$clean_data['columns'] = $data['columns'];
			}
		}

		if ( isset( $data['caption-color'] ) ) {
			if ( in_array( $data['caption-color'], array( 'light', 'dark' ) ) ) {
				$clean_data['caption-color'] = $data['caption-color'];
			}
		}

		if ( isset( $data['captions'] ) ) {
			if ( in_array( $data['captions'], array( 'none', 'overlay', 'reveal' ) ) ) {
				$clean_data['captions'] = $data['captions'];
			}
		}

		if ( isset( $data['aspect'] ) ) {
			if ( in_array( $data['aspect'], array( 'none', 'landscape', 'portrait', 'square' ) ) ) {
				$clean_data['aspect'] = $data['aspect'];
			}
		}

		if ( isset( $data['background-image']['image-id'] ) ) {
			$clean_data['background-image'] = maera_pb_sanitize_image_id( $data['background-image']['image-id'] );
		}

		if ( isset( $data['title'] ) ) {
			$clean_data['title'] = $clean_data['label'] = apply_filters( 'title_save_pre', $data['title'] );
		}

		if ( isset( $data['darken'] ) ) {
			$clean_data['darken'] = 1;
		} else {
			$clean_data['darken'] = 0;
		}

		if ( isset( $data['background-color'] ) ) {
			$clean_data['background-color'] = $data['background-color'];
		}

		if ( isset( $data['background-style'] ) ) {
			if ( in_array( $data['background-style'], array( 'tile', 'cover' ) ) ) {
				$clean_data['background-style'] = $data['background-style'];
			}
		}

		if ( isset( $data['gallery-item-order'] ) ) {
			$clean_data['gallery-item-order'] = array_map( array( 'Maera_PB_Save', 'clean_section_id' ), explode( ',', $data['gallery-item-order'] ) );
		}

		if ( isset( $data['gallery-items'] ) && is_array( $data['gallery-items'] ) ) {
			foreach ( $data['gallery-items'] as $id => $item ) {
				if ( isset( $item['title'] ) ) {
					$clean_data['gallery-items'][ $id ]['title'] = apply_filters( 'title_save_pre', $item['title'] );
				}

				if ( isset( $item['link'] ) ) {
					$clean_data['gallery-items'][ $id ]['link'] = esc_url_raw( $item['link'] );
				}

				if ( isset( $item['description'] ) ) {
					$clean_data['gallery-items'][ $id ]['description'] = sanitize_post_field( 'post_content', $item['description'], ( get_post() ) ? get_the_ID() : 0, 'db' );
				}

				if ( isset( $item['image-id'] ) ) {
					$clean_data['gallery-items'][ $id ]['image-id'] = maera_pb_sanitize_image_id( $item['image-id'] );
				}
			}
		}

		return $clean_data;
	}

}

function maera_pb_get_section_definition_gallery() {
	return Maera_PB_Section_Gallery::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	add_action( 'after_setup_theme', 'maera_pb_get_section_definition_gallery', 11 );
}
