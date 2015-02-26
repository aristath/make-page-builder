<?php
/**
 * Generate valid PHP code that defines an array of Google Font
 * options and their properties.
 */

// Define directories
$base_dir = dirname( dirname( __FILE__ ) );
$temp_dir = $base_dir . '/assets/temp/';
$dest_dir = $temp_dir;

// Check for JSON file
if ( ! is_file( $temp_dir . 'googlefonts.json' ) ) {
	die( 'File ' . $temp_dir . 'googlefonts.json not found.' );
}

// Get JSON data
$d = file_get_contents( $temp_dir . 'googlefonts.json' );

// Convert to multi-dimensional PHP array
$d2 = (array) json_decode( $d );
$d2 = array_map( function( $a ) { return (array) $a; }, $d2 );

// Convert to valid PHP code and clean up
$d3 = var_export( $d2, true );
$d3 = preg_replace( "/\n +array/", 'array', $d3 );
$d3 = preg_replace( '/ /', "\t", $d3 );
$d3 = preg_replace( "/\t+=>\t+/", ' => ', $d3 );
$d3 = preg_replace( "/array\t+\(/", 'array(', $d3 );
$d3 = preg_replace( "/(\w+)\t/", '\1 ', $d3 );
$d3 = preg_replace( "/(\d+) => '/", "'", $d3 );
$d3 = str_replace( "\t\t\t\t", "\t\t\t", $d3 );
$d3 = str_replace( "\t\t\t\t\t", "\t\t\t\t", $d3 );

// Get timestamp
$date = date( 'c', time() );

// File contents
$file = <<<EOD
<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_get_google_fonts' ) ) :
/**
 * Return an array of all available Google Fonts.
 *
 * Updated: {$date}
 *
 * @since  1.0.0.
 *
 * @return array    All Google Fonts.
 */
function ttfmake_get_google_fonts() {
	/**
	 * Allow developers to modify the allowed Google fonts.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    \$fonts    The list of Google fonts with variants and subsets.
	 */
	return apply_filters( 'make_get_google_fonts', {$d3} );
}
endif;
EOD;

// Check for destination
if ( ! file_exists( $dest_dir ) ) {
	die( 'Destination directory ' . $dest_dir . ' does not exist.' );
}

// Create/overwrite the file
file_put_contents( $dest_dir . 'google-fonts.php', $file );

// Done.
exit();