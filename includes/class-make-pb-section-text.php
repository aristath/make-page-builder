<?php

class Make_PB_Section_Text {

	private static $instance;

	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function __construct() {

		$this->register_text_section();

	}

	/**
	 * Register the text section.
	 *
	 * Note that in 1.4.0, the "text" section was renamed to "columns". In order to provide good back compatibility,
	 * only the section label is changed to "Columns". All other internal references for this section will remain as
	 * "text".
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function register_text_section() {
		Make_PB()->sections->add_section(
			'text',
			_x( 'Columns', 'section name', 'make' ),
			Make_PB::uri() . '/includes/builder/sections/css/images/text.png',
			__( 'Create rearrangeable columns of content and images.', 'make' ),
			array( $this, 'save_text' ),
			'sections/builder-templates/text',
			'sections/front-end-templates/text',
			100,
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
					'name'    => 'columns-number',
					'class'   => 'make_pb-text-columns',
					'label'   => __( 'Columns', 'make' ),
					'default' => 3,
					'options' => array(
						1 => 1,
						2 => 2,
						3 => 3,
						4 => 4,
					),
				),
			)
		);
	}

	/**
	 * Save the data for the text section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save_text( $data ) {
		$clean_data = array();

		if ( isset( $data['columns-number'] ) ) {
			if ( in_array( $data['columns-number'], range( 1, 4 ) ) ) {
				$clean_data['columns-number'] = $data['columns-number'];
			}
		}

		$clean_data['title'] = $clean_data['label'] = ( isset( $data['title'] ) ) ? apply_filters( 'title_save_pre', $data['title'] ) : '';

		if ( isset( $data['columns-order'] ) ) {
			$clean_data['columns-order'] = array_map( array( 'Make_PB_Save', 'clean_section_id' ), explode( ',', $data['columns-order'] ) );
		}

		if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
			foreach ( $data['columns'] as $id => $item ) {
				if ( isset( $item['title'] ) ) {
					$clean_data['columns'][ $id ]['title'] = apply_filters( 'title_save_pre', $item['title'] );
				}

				if ( isset( $item['image-link'] ) ) {
					$clean_data['columns'][ $id ]['image-link'] = esc_url_raw( $item['image-link'] );
				}

				if ( isset( $item['image-id'] ) ) {
					$clean_data['columns'][ $id ]['image-id'] = make_pb_sanitize_image_id( $item['image-id'] );
				}

				if ( isset( $item['content'] ) ) {
					$clean_data['columns'][ $id ]['content'] = sanitize_post_field( 'post_content', $item['content'], ( get_post() ) ? get_the_ID() : 0, 'db' );
				}
			}
		}

		return $clean_data;
	}

}

function make_pb_get_section_definition_text() {
	return Make_PB_Section_Text::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	add_action( 'after_setup_theme', 'make_pb_get_section_definition_text', 11 );
}
