<?php
/*
Plugin Name: Maera Page Builder
*/

class Maera_PB {

	private static $instance;

	public $scripts;
	public $sections;
	public $check;
	public $is_builder_active;

	public function __construct() {

		self::include_file( self::path() . '/includes/class-maera-pb-check.php', 'Maera_PB_Check' );
		self::include_file( self::path() . '/includes/class-maera-pb-helper.php', 'Maera_PB_Helper' );
		self::include_file( self::path() . '/includes/class-maera-pb-config.php', 'Maera_PB_Config' );
		self::include_file( self::path() . '/includes/class-maera-pb-image.php', 'Maera_PB_Image' );

		$this->check = Maera_PB_Check::instance();
		$this->is_builder_active = maera_pb_is_builder_active();

		if ( is_admin() ) {
			self::include_file( self::path() . '/includes/class-maera-pb-scripts.php', 'Maera_PB_Scripts' );
			self::include_file( self::path() . '/includes/class-maera-pb-sections.php', 'Maera_PB_Sections' );
			self::include_file( self::path() . '/includes/class-maera-pb-section-banner.php', 'Maera_PB_Section_Banner' );
			self::include_file( self::path() . '/includes/class-maera-pb-section-gallery.php', 'Maera_PB_Section_Gallery' );
			self::include_file( self::path() . '/includes/base.php', 'Maera_PB_Base' );

			$this->scripts  = new Maera_PB_Scripts();
			$this->sections = Maera_PB_Sections::instance();

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

function Maera_PB() {
	return Maera_PB::get_instance();
}

global $Maera_PB;
$Maera_PB = Maera_PB();
