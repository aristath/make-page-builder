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

		if ( ! class_exists( 'Make_PB_Check' ) ) {
			require self::path() . '/includes/class-make-pb-check.php';
		}

		if ( ! class_exists( 'Make_PB_Helper' ) ) {
			require self::path() . '/includes/class-make-pb-helper.php';
		}

		if ( ! class_exists( 'Make_PB_Config' ) ) {
			require self::path() . '/includes/class-make-pb-config.php';
		}

		if ( ! class_exists( 'Make_PB_Image' ) ) {
			require self::path() . '/includes/class-make-pb-image.php';
		}


		$this->check = Make_PB_Check::instance();
		$this->is_builder_active = make_pb_is_builder_active();

		if ( is_admin() ) {

			require self::path() . '/includes/class-make-pb-scripts.php';
			require self::path() . '/includes/class-make-pb-sections.php';

			require self::path() . '/includes/base.php';

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
