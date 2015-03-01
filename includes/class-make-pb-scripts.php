<?php

class Make_PB_Scripts extends Make_PB {

	function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'sections_admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'make_pb_edit_page_script' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_print_styles' ) );

	}

	public function admin_enqueue_scripts() {

		$version = Make_PB::version();

		// Enqueue the CSS
		wp_enqueue_style( 'make_pb-builder', Make_PB::uri() . '/includes/builder/core/css/builder.css', array(), $version );
		wp_enqueue_style( 'wp-color-picker' );

		wp_register_script( 'make_pb-builder/js/models/section.js', Make_PB::uri() . '/includes/builder/core/js/models/section.js', array(), $version, true );
		wp_register_script( 'make_pb-builder/js/collections/sections.js', Make_PB::uri() . '/includes/builder/core/js/collections/sections.js', array(), $version, true );
		wp_register_script( 'make_pb-builder/js/views/menu.js', Make_PB::uri() . '/includes/builder/core/js/views/menu.js', array(), $version, true );
		wp_register_script( 'make_pb-builder/js/views/section.js', Make_PB::uri() . '/includes/builder/core/js/views/section.js', array(), $version, true );
		wp_register_script( 'make_pb-builder/js/views/overlay.js', Make_PB::uri() . '/includes/builder/core/js/views/overlay.js', array(), $version, true );

		$dependencies = array( 'wplink', 'utils', 'wp-color-picker', 'jquery-effects-core', 'jquery-ui-sortable', 'backbone' );
		$dependencies = apply_filters( 'make_pb_builder_js_dependencies', array_merge( $dependencies, array(
			'make_pb-builder/js/models/section.js',
			'make_pb-builder/js/collections/sections.js',
			'make_pb-builder/js/views/menu.js',
			'make_pb-builder/js/views/section.js',
			'make_pb-builder/js/views/overlay.js',
		) ) );

		wp_enqueue_script( 'make_pb-builder', Make_PB::uri() . '/includes/builder/core/js/app.js', $dependencies, $version, true );

		wp_localize_script( 'make_pb-builder', 'make_pbBuilderData', array(
			'pageID'        => get_the_ID(),
			'postRefresh'   => true,
			'confirmString' => __( 'Delete the section?', 'make' )
		) );
	}

	public function sections_admin_enqueue_scripts( $hook_suffix ) {

		wp_register_script( 'make_pb-sections/js/models/gallery-item.js', Make_PB::uri() . '/includes/builder/sections/js/models/gallery-item.js', array(), $version, true );
		wp_register_script( 'make_pb-sections/js/views/gallery-item.js', Make_PB::uri() . '/includes/builder/sections/js/views/gallery-item.js', array(), $version, true );
		wp_register_script( 'make_pb-sections/js/views/gallery.js', Make_PB::uri() . '/includes/builder/sections/js/views/gallery.js', array(), $version, true );
		wp_register_script( 'make_pb-sections/js/models/banner-slide.js', Make_PB::uri() . '/includes/builder/sections/js/models/banner-slide.js', array(), $version, true );
		wp_register_script( 'make_pb-sections/js/views/banner-slide.js', Make_PB::uri() . '/includes/builder/sections/js/views/banner-slide.js', array(), $version, true );
		wp_register_script( 'make_pb-sections/js/views/banner.js', Make_PB::uri() . '/includes/builder/sections/js/views/banner.js', array(), $version, true );
		wp_enqueue_script( 'make_pb-sections/js/quick-start.js', Make_PB::uri() . '/includes/builder/sections/js/quick-start.js', array( 'make_pb-builder' ), $version, true );

		add_filter( 'make_pb_builder_js_dependencies', array( $this, 'add_js_dependencies' ) );

		wp_enqueue_style( 'make_pb-sections/css/sections.css', Make_PB::uri() . '/includes/builder/sections/css/sections.css', array(), $version, 'all' );
	}

	function make_pb_edit_page_script() {
		global $pagenow;

		wp_enqueue_script( 'make_pb-admin-edit-page', Make_PB::uri() . '/js/admin/edit-page.js', array( 'jquery' ), $version, true );
		wp_localize_script( 'make_pb-admin-edit-page', 'make_pbEditPageData', array(
			'featuredImage' => __( 'Featured images are not available for this page while using the current page template.', 'make' ),
			'pageNow'       => esc_js( $pagenow )
		) );
	}

	public function add_js_dependencies( $deps ) {

		if ( ! is_array( $deps ) ) {
			$deps = array();
		}

		return array_merge( $deps, array(
			'make_pb-sections/js/models/gallery-item.js',
			'make_pb-sections/js/models/banner-slide.js',
			'make_pb-sections/js/views/gallery-item.js',
			'make_pb-sections/js/views/gallery.js',
			'make_pb-sections/js/views/banner-slide.js',
			'make_pb-sections/js/views/banner.js',
		) );

	}

	public function admin_print_styles() {
		global $pagenow;

		echo '<style type="text/css">';
		if ( 'post-new.php' === $pagenow || ( 'post.php' === $pagenow && make_pb_is_builder_active() ) ) {
			echo '#postdivrich { display: none; }';
		} else {
			echo '#make_pb-builder, .make_pb-duplicator { display: none; }';
		}

		foreach ( Make_PB()->sections->get_sections() as $key => $section ) {
			echo '#make_pb-menu-list-item-link-' . esc_attr( $section['id'] ) . ' .make_pb-menu-list-item-link-icon-wrapper {';
			echo 'background-image: url(' . addcslashes( esc_url_raw( $section['icon'] ), '"' ) . ');';
			echo '}';
		}
		echo '</style>';

	}

}
