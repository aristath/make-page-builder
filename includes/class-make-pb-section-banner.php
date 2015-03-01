<?php

class Make_PB_Section_Banner {

	private static $instance;

	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	public function __construct() {

		$this->register_banner_section();

	}

	/**
	 * Register the banner section.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function register_banner_section() {
		Make_PB()->sections->add_section(
			'banner',
			_x( 'Banner', 'section name', 'make' ),
			Make_PB::uri() . '/includes/builder/sections/css/images/banner.png',
			__( 'Display multiple types of content in a banner or a slider.', 'make' ),
			array( $this, 'save_banner' ),
			'sections/builder-templates/banner',
			'sections/front-end-templates/banner',
			300,
			'includes/builder/',
			array(
				100 => array(
					'type'  => 'section_title',
					'name'  => 'title',
					'label' => __( 'Enter section title', 'make' ),
					'class' => 'make_pb-configuration-title make_pb-section-header-title-input',
				),
				200 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Hide navigation arrows', 'make' ),
					'name'    => 'hide-arrows',
					'default' => 0
				),
				300 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Hide navigation dots', 'make' ),
					'name'    => 'hide-dots',
					'default' => 0
				),
				400 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Autoplay slideshow', 'make' ),
					'name'    => 'autoplay',
					'default' => 1
				),
				500 => array(
					'type'    => 'text',
					'label'   => __( 'Time between slides (ms)', 'make' ),
					'name'    => 'delay',
					'default' => 6000
				),
				600 => array(
					'type'    => 'select',
					'label'   => __( 'Transition effect', 'make' ),
					'name'    => 'transition',
					'default' => 'scrollHorz',
					'options' => array(
						'scrollHorz' => __( 'Slide horizontal', 'make' ),
						'fade'       => __( 'Fade', 'make' ),
						'none'       => __( 'None', 'make' ),
					)
				),
				700 => array(
					'type'    => 'text',
					'label'   => __( 'Section height (px)', 'make' ),
					'name'    => 'height',
					'default' => 600
				),
				800 => array(
					'type'        => 'select',
					'label'       => __( 'Responsive behavior', 'make' ),
					'name'        => 'responsive',
					'default'     => 'balanced',
					'description' => __( 'Choose how the Banner will respond to varying screen widths. Default is ideal for large amounts of written content, while Aspect is better for showing your images.', 'make' ),
					'options'     => array(
						'balanced' => __( 'Default', 'make' ),
						'aspect'   => __( 'Aspect', 'make' ),
					)
				)
			)
		);
	}

	/**
	 * Save the data for the banner section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save_banner( $data ) {
		$clean_data = array();

		$clean_data['title']       = $clean_data['label'] = ( isset( $data['title'] ) ) ? apply_filters( 'title_save_pre', $data['title'] ) : '';
		$clean_data['hide-arrows'] = ( isset( $data['hide-arrows'] ) && 1 === (int) $data['hide-arrows'] ) ? 1 : 0;
		$clean_data['hide-dots']   = ( isset( $data['hide-dots'] ) && 1 === (int) $data['hide-dots'] ) ? 1 : 0;
		$clean_data['autoplay']    = ( isset( $data['autoplay'] ) && 1 === (int) $data['autoplay'] ) ? 1 : 0;

		if ( isset( $data['transition'] ) && in_array( $data['transition'], array( 'fade', 'scrollHorz', 'none' ) ) ) {
			$clean_data['transition'] = $data['transition'];
		}

		if ( isset( $data['delay'] ) ) {
			$clean_data['delay'] = absint( $data['delay'] );
		}

		if ( isset( $data['height'] ) ) {
			$clean_data['height'] = absint( $data['height'] );
		}

		if ( isset( $data['responsive'] ) && in_array( $data['responsive'], array( 'aspect', 'balanced' ) ) ) {
			$clean_data['responsive'] = $data['responsive'];
		}

		if ( isset( $data['banner-slide-order'] ) ) {
			$clean_data['banner-slide-order'] = array_map( array( 'Make_PB_Save', 'clean_section_id' ), explode( ',', $data['banner-slide-order'] ) );
		}

		if ( isset( $data['banner-slides'] ) && is_array( $data['banner-slides'] ) ) {
			foreach ( $data['banner-slides'] as $id => $slide ) {

				if ( isset( $slide['content'] ) ) {
					$clean_data['banner-slides'][ $id ]['content'] = sanitize_post_field( 'post_content', $slide['content'], ( get_post() ) ? get_the_ID() : 0, 'db' );
				}

				if ( isset( $slide['background-color'] ) ) {
					$clean_data['banner-slides'][ $id ]['background-color'] = Kirki_Color::sanitize_hex( $slide['background-color'] );
				}

				$clean_data['banner-slides'][ $id ]['darken'] = ( isset( $slide['darken'] ) && 1 === (int) $slide['darken'] ) ? 1 : 0;

				if ( isset( $slide['image-id'] ) ) {
					$clean_data['banner-slides'][ $id ]['image-id'] = make_pb_sanitize_image_id( $slide['image-id'] );
				}

				$clean_data['banner-slides'][ $id ]['alignment'] = ( isset( $slide['alignment'] ) && in_array( $slide['alignment'], array( 'none', 'left', 'right' ) ) ) ? $slide['alignment'] : 'none';

				if ( isset( $slide['state'] ) ) {
					$clean_data['banner-slides'][ $id ]['state'] = ( in_array( $slide['state'], array( 'open', 'closed' ) ) ) ? $slide['state'] : 'open';
				}
			}
		}

		return $clean_data;
	}

}

function make_pb_get_section_definition_banner() {
	return Make_PB_Section_Banner::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	add_action( 'after_setup_theme', 'make_pb_get_section_definition_banner', 11 );
}
