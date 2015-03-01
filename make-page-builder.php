<?php
/*
Plugin Name: Make Page Builder
*/

class Make_PB {

	private static $instance;

	public $scripts;
	public $sections;
	public $check;
	public $is_builder_active;

	public function __construct() {

		self::include_file( self::path() . '/includes/class-make-pb-check.php', 'Make_PB_Check' );
		self::include_file( self::path() . '/includes/class-make-pb-helper.php', 'Make_PB_Helper' );
		self::include_file( self::path() . '/includes/class-make-pb-config.php', 'Make_PB_Config' );
		self::include_file( self::path() . '/includes/class-make-pb-image.php', 'Make_PB_Image' );

		$this->check = Make_PB_Check::instance();
		$this->is_builder_active = make_pb_is_builder_active();

		if ( is_admin() ) {
			self::include_file( self::path() . '/includes/class-make-pb-scripts.php', 'Make_PB_Scripts' );
			self::include_file( self::path() . '/includes/class-make-pb-sections.php', 'Make_PB_Sections' );
			self::include_file( self::path() . '/includes/class-make-pb-section-text.php', 'Make_PB_Section_Text' );
			self::include_file( self::path() . '/includes/class-make-pb-section-banner.php', 'Make_PB_Section_Banner' );
			self::include_file( self::path() . '/includes/class-make-pb-section-gallery.php', 'Make_PB_Section_Gallery' );
			self::include_file( self::path() . '/includes/base.php', 'Make_PB_Base' );

			$this->scripts  = new Make_PB_Scripts();
			$this->sections = Make_PB_Sections::instance();

		}

	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function include_file( $path, $class ) {
		if ( ! class_exists( $class ) ) {
			require $path;
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

	public static function locate_template($template_names, $load = false, $require_once = true ) {
		$located = '';
		foreach ( (array) $template_names as $template_name ) {
			if ( !$template_name )
				continue;
			if ( file_exists(self::path() . '/' . $template_name)) {
				$located = self::path() . '/' . $template_name;
				break;
			}
		}

		if ( $load && '' != $located )
			load_template( $located, $require_once );

		return $located;
	}

}

function Make_PB() {
	return Make_PB::get_instance();
}

global $Make_PB;
$Make_PB = Make_PB();
