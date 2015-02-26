<?php
/**
 * The suffix to use for scripts.
 */
if ( ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ) {
	define( 'TTFMAKE_SUFFIX', '' );
} else {
	define( 'TTFMAKE_SUFFIX', '.min' );
}

// Custom functions that act independently of the theme templates
require get_template_directory() . '/inc/extras.php';
// Gallery slider
require get_template_directory() . '/inc/gallery-slider/gallery-slider.php';

// Formatting
require get_template_directory() . '/inc/formatting/formatting.php';

/**
 * Admin includes.
 */
if ( is_admin() ) {
	// Page customizations
	require get_template_directory() . '/inc/edit-page.php';

	// Page Builder
	require get_template_directory() . '/inc/builder/core/base.php';

	// Admin notices
	require get_template_directory() . '/inc/admin-notice/admin-notice.php';
}

/**
 * 3rd party compatibility includes.
 */
// Jetpack
// There are several plugins that duplicate the functionality of various Jetpack modules,
// so rather than conditionally loading our Jetpack compatibility file based on the presence
// of the main Jetpack class, we attempt to detect individual classes/functions related to
// their particular modules.
require get_template_directory() . '/inc/jetpack.php';

// WooCommerce
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

if ( ! function_exists( 'ttfmake_setup' ) ) :
/**
 * Sets up text domain, theme support, menus, and editor styles
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_setup() {
	// Attempt to load text domain from child theme first
	if ( ! load_theme_textdomain( 'make', get_stylesheet_directory() . '/languages' ) ) {
		load_theme_textdomain( 'make', get_template_directory() . '/languages' );
	}

	// Feed links
	add_theme_support( 'automatic-feed-links' );

	// Featured images
	add_theme_support( 'post-thumbnails' );

	// Title tag
	add_theme_support( 'title-tag' );

	// Menu locations
	register_nav_menus( array(
		'primary'    => __( 'Primary Menu', 'make' ),
		'social'     => __( 'Social Profile Links', 'make' ),
		'header-bar' => __( 'Header Bar Menu', 'make' ),
	) );

	// Editor styles
	$editor_styles = array();
	$editor_styles[] = 'css/font-awesome.css';
	$editor_styles[] = 'css/editor-style.css';

	// Another editor stylesheet is added via ttfmake_mce_css() in inc/customizer/bootstrap.php
	add_editor_style( $editor_styles );
}
endif;

add_action( 'after_setup_theme', 'ttfmake_setup' );

if ( ! function_exists( 'ttfmake_scripts' ) ) :
/**
 * Enqueue styles and scripts.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_scripts() {
	// Styles
	$style_dependencies = array();

	// Google fonts
	if ( '' !== $google_request = ttfmake_get_google_font_uri() ) {
		// Enqueue the fonts
		wp_enqueue_style(
			'ttfmake-google-fonts',
			$google_request,
			$style_dependencies,
			TTFMAKE_VERSION
		);
		$style_dependencies[] = 'ttfmake-google-fonts';
	}

	// Font Awesome
	wp_enqueue_style(
		'ttfmake-font-awesome',
		get_template_directory_uri() . '/css/font-awesome' . TTFMAKE_SUFFIX . '.css',
		$style_dependencies,
		'4.2.0'
	);
	$style_dependencies[] = 'ttfmake-font-awesome';

	// Main stylesheet
	wp_enqueue_style(
		'ttfmake-main-style',
		get_stylesheet_uri(),
		$style_dependencies,
		TTFMAKE_VERSION
	);
	$style_dependencies[] = 'ttfmake-main-style';

	// Print stylesheet
	wp_enqueue_style(
		'ttfmake-print-style',
		get_template_directory_uri() . '/css/print.css',
		$style_dependencies,
		TTFMAKE_VERSION,
		'print'
	);

	// Scripts
	$script_dependencies = array();

	// jQuery
	$script_dependencies[] = 'jquery';

	// Cycle2
	ttfmake_cycle2_script_setup( $script_dependencies );
	$script_dependencies[] = 'ttfmake-cycle2';

	// FitVids
	wp_enqueue_script(
		'ttfmake-fitvids',
		get_template_directory_uri() . '/js/libs/fitvids/jquery.fitvids' . TTFMAKE_SUFFIX . '.js',
		$script_dependencies,
		'1.1',
		true
	);

	// Default selectors
	$selector_array = array(
		"iframe[src*='www.viddler.com']",
		"iframe[src*='money.cnn.com']",
		"iframe[src*='www.educreations.com']",
		"iframe[src*='//blip.tv']",
		"iframe[src*='//embed.ted.com']",
		"iframe[src*='//www.hulu.com']",
	);

	/**
	 * Allow for changing of the selectors that are used to apply FitVids.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $selector_array    The selectors used by FitVids.
	 */
	$selector_array = apply_filters( 'make_fitvids_custom_selectors', $selector_array );

	// Compile selectors
	$fitvids_custom_selectors = array(
		'selectors' => implode( ',', $selector_array )
	);

	// Send to the script
	wp_localize_script(
		'ttfmake-fitvids',
		'ttfmakeFitVids',
		$fitvids_custom_selectors
	);

	$script_dependencies[] = 'ttfmake-fitvids';

	// Global script
	wp_enqueue_script(
		'ttfmake-global',
		get_template_directory_uri() . '/js/global' . TTFMAKE_SUFFIX . '.js',
		$script_dependencies,
		TTFMAKE_VERSION,
		true
	);

	// Comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;

add_action( 'wp_enqueue_scripts', 'ttfmake_scripts' );

if ( ! function_exists( 'ttfmake_cycle2_script_setup' ) ) :
/**
 * Enqueue Cycle2 scripts
 *
 * If the environment is set up for minified scripts, load one concatenated, minified
 * Cycle 2 script. Otherwise, load each module separately.
 *
 * @since  1.0.0.
 *
 * @param  array    $script_dependencies    Scripts that Cycle2 depends on.
 *
 * @return void
 */
function ttfmake_cycle2_script_setup( $script_dependencies ) {
	if ( defined( 'TTFMAKE_SUFFIX' ) && '.min' === TTFMAKE_SUFFIX ) {
		wp_enqueue_script(
			'ttfmake-cycle2',
			get_template_directory_uri() . '/js/libs/cycle2/jquery.cycle2' . TTFMAKE_SUFFIX . '.js',
			$script_dependencies,
			TTFMAKE_VERSION,
			true
		);
	} else {
		// Core script
		wp_enqueue_script(
			'ttfmake-cycle2',
			get_template_directory_uri() . '/js/libs/cycle2/jquery.cycle2.js',
			$script_dependencies,
			'2.1.6',
			true
		);

		// Vertical centering
		wp_enqueue_script(
			'ttfmake-cycle2-center',
			get_template_directory_uri() . '/js/libs/cycle2/jquery.cycle2.center.js',
			'ttfmake-cycle2',
			'20140121',
			true
		);

		// Swipe support
		wp_enqueue_script(
			'ttfmake-cycle2-swipe',
			get_template_directory_uri() . '/js/libs/cycle2/jquery.cycle2.swipe.js',
			'ttfmake-cycle2',
			'20121120',
			true
		);
	}
}
endif;
