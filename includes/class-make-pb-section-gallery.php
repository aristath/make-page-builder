<?php

class Make_PB_Section_Gallery {

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
		Make_PB()->sections->add_section(
			'gallery',
			_x( 'Gallery', 'section name', 'make' ),
			Make_PB::uri() . '/includes/builder/sections/css/images/gallery.png',
			__( 'Display your images in various grid combinations.', 'make' ),
			array( $this, 'save_gallery' ),
			'sections/builder-templates/gallery',
			'sections/front-end-templates/gallery',
			400,
			'includes/builder/',
			array(
				100 => array(
					'type'  => 'section_title',
					'name'  => 'title',
					'label' => __( 'Enter section title', 'make' ),
					'class' => 'make_pb-configuration-title make_pb-section-header-title-input',
				),
				200 => array(
					'type'    => 'select',
					'name'    => 'columns',
					'label'   => __( 'Columns', 'make' ),
					'class'   => 'make_pb-gallery-columns',
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
					'label'   => __( 'Aspect ratio', 'make' ),
					'default' => 'square',
					'options' => array(
						'square'    => __( 'Square', 'make' ),
						'landscape' => __( 'Landscape', 'make' ),
						'portrait'  => __( 'Portrait', 'make' ),
						'none'      => __( 'None', 'make' ),
					)
				),
				400 => array(
					'type'    => 'select',
					'name'    => 'captions',
					'label'   => __( 'Caption style', 'make' ),
					'default' => 'reveal',
					'options' => array(
						'reveal'  => __( 'Reveal', 'make' ),
						'overlay' => __( 'Overlay', 'make' ),
						'none'    => __( 'None', 'make' ),
					)
				),
				500 => array(
					'type'    => 'select',
					'name'    => 'caption-color',
					'label'   => __( 'Caption color', 'make' ),
					'default' => 'light',
					'options' => array(
						'light'  => __( 'Light', 'make' ),
						'dark' => __( 'Dark', 'make' ),
					)
				),
				600 => array(
					'type'  => 'image',
					'name'  => 'background-image',
					'label' => __( 'Background image', 'make' ),
					'class' => 'make_pb-configuration-media'
				),
				700 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Darken background to improve readability', 'make' ),
					'name'    => 'darken',
					'default' => 0,
				),
				800 => array(
					'type'    => 'select',
					'name'    => 'background-style',
					'label'   => __( 'Background style', 'make' ),
					'default' => 'tile',
					'options' => array(
						'tile'  => __( 'Tile', 'make' ),
						'cover' => __( 'Cover', 'make' ),
					),
				),
				900 => array(
					'type'    => 'color',
					'label'   => __( 'Background color', 'make' ),
					'name'    => 'background-color',
					'class'   => 'make_pb-gallery-background-color make_pb-configuration-color-picker',
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
			$clean_data['background-image'] = make_pb_sanitize_image_id( $data['background-image']['image-id'] );
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
			$clean_data['gallery-item-order'] = array_map( array( 'Make_PB_Save', 'clean_section_id' ), explode( ',', $data['gallery-item-order'] ) );
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
					$clean_data['gallery-items'][ $id ]['image-id'] = make_pb_sanitize_image_id( $item['image-id'] );
				}
			}
		}

		return $clean_data;
	}

}

function make_pb_get_section_definition_gallery() {
	return Make_PB_Section_Gallery::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	add_action( 'after_setup_theme', 'make_pb_get_section_definition_gallery', 11 );
}
