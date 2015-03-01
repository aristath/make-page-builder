<?php

class Maera_PB_Section_Banner {

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
		Maera_PB()->sections->add_section(
			'banner',
			_x( 'Banner', 'section name', 'maera' ),
			Maera_PB::uri() . '/includes/builder/sections/css/images/banner.png',
			__( 'Display multiple types of content in a banner or a slider.', 'maera' ),
			array( $this, 'save_banner' ),
			'sections/builder-templates/banner',
			'sections/front-end-templates/banner',
			300,
			'includes/builder/',
			array(
				100 => array(
					'type'  => 'section_title',
					'name'  => 'title',
					'label' => __( 'Enter section title', 'maera' ),
					'class' => 'maera_pb-configuration-title maera_pb-section-header-title-input',
				),
				200 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Hide navigation arrows', 'maera' ),
					'name'    => 'hide-arrows',
					'default' => 0
				),
				300 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Hide navigation dots', 'maera' ),
					'name'    => 'hide-dots',
					'default' => 0
				),
				400 => array(
					'type'    => 'checkbox',
					'label'   => __( 'Autoplay slideshow', 'maera' ),
					'name'    => 'autoplay',
					'default' => 1
				),
				500 => array(
					'type'    => 'text',
					'label'   => __( 'Time between slides (ms)', 'maera' ),
					'name'    => 'delay',
					'default' => 6000
				),
				600 => array(
					'type'    => 'select',
					'label'   => __( 'Transition effect', 'maera' ),
					'name'    => 'transition',
					'default' => 'scrollHorz',
					'options' => array(
						'scrollHorz' => __( 'Slide horizontal', 'maera' ),
						'fade'       => __( 'Fade', 'maera' ),
						'none'       => __( 'None', 'maera' ),
					)
				),
				700 => array(
					'type'    => 'text',
					'label'   => __( 'Section height (px)', 'maera' ),
					'name'    => 'height',
					'default' => 600
				),
				800 => array(
					'type'        => 'select',
					'label'       => __( 'Responsive behavior', 'maera' ),
					'name'        => 'responsive',
					'default'     => 'balanced',
					'description' => __( 'Choose how the Banner will respond to varying screen widths. Default is ideal for large amounts of written content, while Aspect is better for showing your images.', 'maera' ),
					'options'     => array(
						'balanced' => __( 'Default', 'maera' ),
						'aspect'   => __( 'Aspect', 'maera' ),
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
			$clean_data['banner-slide-order'] = array_map( array( 'Maera_PB_Save', 'clean_section_id' ), explode( ',', $data['banner-slide-order'] ) );
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
					$clean_data['banner-slides'][ $id ]['image-id'] = maera_pb_sanitize_image_id( $slide['image-id'] );
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

function maera_pb_get_section_definition_banner() {
	return Maera_PB_Section_Banner::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	add_action( 'after_setup_theme', 'maera_pb_get_section_definition_banner', 11 );
}
