<?php

class Maera_PB_Scripts extends Maera_PB {

	function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'sections_admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'maera_pb_edit_page_script' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_print_styles' ) );

	}

	public function admin_enqueue_scripts() {

		$version = Maera_PB::version();

		// Enqueue the CSS
		wp_enqueue_style( 'maera_pb-builder', Maera_PB::uri() . '/includes/builder/core/css/builder.css', array(), $version );
		wp_enqueue_style( 'wp-color-picker' );

		wp_register_script( 'maera_pb-builder/js/models/section.js', Maera_PB::uri() . '/includes/builder/core/js/models/section.js', array(), $version, true );
		wp_register_script( 'maera_pb-builder/js/collections/sections.js', Maera_PB::uri() . '/includes/builder/core/js/collections/sections.js', array(), $version, true );
		wp_register_script( 'maera_pb-builder/js/views/menu.js', Maera_PB::uri() . '/includes/builder/core/js/views/menu.js', array(), $version, true );
		wp_register_script( 'maera_pb-builder/js/views/section.js', Maera_PB::uri() . '/includes/builder/core/js/views/section.js', array(), $version, true );
		wp_register_script( 'maera_pb-builder/js/views/overlay.js', Maera_PB::uri() . '/includes/builder/core/js/views/overlay.js', array(), $version, true );

		$dependencies = array( 'wplink', 'utils', 'wp-color-picker', 'jquery-effects-core', 'jquery-ui-sortable', 'backbone' );
		$dependencies = apply_filters( 'maera_pb_builder_js_dependencies', array_merge( $dependencies, array(
			'maera_pb-builder/js/models/section.js',
			'maera_pb-builder/js/collections/sections.js',
			'maera_pb-builder/js/views/menu.js',
			'maera_pb-builder/js/views/section.js',
			'maera_pb-builder/js/views/overlay.js',
		) ) );

		wp_enqueue_script( 'maera_pb-builder', Maera_PB::uri() . '/includes/builder/core/js/app.js', $dependencies, $version, true );

		wp_localize_script( 'maera_pb-builder', 'maera_pbBuilderData', array(
			'pageID'        => get_the_ID(),
			'postRefresh'   => true,
			'confirmString' => __( 'Delete the section?', 'maera' )
		) );
	}

	public function sections_admin_enqueue_scripts( $hook_suffix ) {

		wp_register_script( 'maera_pb-sections/js/models/gallery-item.js', Maera_PB::uri() . '/includes/builder/sections/js/models/gallery-item.js', array(), $version, true );
		wp_register_script( 'maera_pb-sections/js/views/gallery-item.js', Maera_PB::uri() . '/includes/builder/sections/js/views/gallery-item.js', array(), $version, true );
		wp_register_script( 'maera_pb-sections/js/views/gallery.js', Maera_PB::uri() . '/includes/builder/sections/js/views/gallery.js', array(), $version, true );
		wp_register_script( 'maera_pb-sections/js/models/banner-slide.js', Maera_PB::uri() . '/includes/builder/sections/js/models/banner-slide.js', array(), $version, true );
		wp_register_script( 'maera_pb-sections/js/views/banner-slide.js', Maera_PB::uri() . '/includes/builder/sections/js/views/banner-slide.js', array(), $version, true );
		wp_register_script( 'maera_pb-sections/js/views/banner.js', Maera_PB::uri() . '/includes/builder/sections/js/views/banner.js', array(), $version, true );
		wp_enqueue_script( 'maera_pb-sections/js/quick-start.js', Maera_PB::uri() . '/includes/builder/sections/js/quick-start.js', array( 'maera_pb-builder' ), $version, true );

		add_filter( 'maera_pb_builder_js_dependencies', array( $this, 'add_js_dependencies' ) );

		wp_enqueue_style( 'maera_pb-sections/css/sections.css', Maera_PB::uri() . '/includes/builder/sections/css/sections.css', array(), $version, 'all' );
	}

	function maera_pb_edit_page_script() {
		global $pagenow;

		wp_enqueue_script( 'maera_pb-admin-edit-page', Maera_PB::uri() . '/js/admin/edit-page.js', array( 'jquery' ), $version, true );
		wp_localize_script( 'maera_pb-admin-edit-page', 'maera_pbEditPageData', array(
			'featuredImage' => __( 'Featured images are not available for this page while using the current page template.', 'maera' ),
			'pageNow'       => esc_js( $pagenow )
		) );
	}

	public function add_js_dependencies( $deps ) {

		if ( ! is_array( $deps ) ) {
			$deps = array();
		}

		return array_merge( $deps, array(
			'maera_pb-sections/js/models/gallery-item.js',
			'maera_pb-sections/js/models/banner-slide.js',
			'maera_pb-sections/js/views/gallery-item.js',
			'maera_pb-sections/js/views/gallery.js',
			'maera_pb-sections/js/views/banner-slide.js',
			'maera_pb-sections/js/views/banner.js',
		) );

	}

	public function admin_print_styles() {
		global $pagenow;

		echo '<style type="text/css">';
		if ( 'post-new.php' === $pagenow || ( 'post.php' === $pagenow && maera_pb_is_builder_active() ) ) {
			echo '#postdivrich { display: none; }';
		} else {
			echo '#maera_pb-builder, .maera_pb-duplicator { display: none; }';
		}

		foreach ( Maera_PB()->sections->get_sections() as $key => $section ) {
			echo '#maera_pb-menu-list-item-link-' . esc_attr( $section['id'] ) . ' .maera_pb-menu-list-item-link-icon-wrapper {';
			echo 'background-image: url(' . addcslashes( esc_url_raw( $section['icon'] ), '"' ) . ');';
			echo '}';
		}
		echo '</style>';

	}

}
