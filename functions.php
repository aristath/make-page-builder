<?php
/*
Plugin Name: Make Page Builder
*/

class Make_PB {

	public function __construct() {

		require self::path() . '/inc/extras.php';
		if ( is_admin() ) {
			require self::path() . '/inc/edit-page.php';
			require self::path() . '/inc/builder/core/base.php';
		}

	}

	public static function path() {
		// return get_template_directory();
		return dirname( __FILE__ );
	}

	public static function uri() {
		// return get_template_directory_uri();
		return plugin_dir_url( __FILE__ );
	}

	public static function version() {
		return;
	}

	public static function get_template_part( $slug, $name = null ) {

		$suffix = ! is_null( $name ) ? '-' . $name : null;
		$full_filename = self::path() . '/' . $slug . $suffix . '.php';
		$fallback_file = self::path() . '/' . $slug . '.php';
		$file = file_exists( $full_filename ) ? $full_filename : $fallback_file;

		require( $file );

	}

}

function make_page_builder_init() {
	$Make_PB = new Make_PB();
}
add_action( 'after_setup_theme', 'make_page_builder_init' );
