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
		return get_template_directory();
	}

	public static function uri() {
		return get_template_directory_uri();
	}

	public static function version() {
		return;
	}

}

$Make_PB = new Make_PB();
