<?php
/**
 * @package Make
 */

global $make_pb_section_data, $make_pb_is_js_template, $make_pb_slide_id;
$section_name = 'make_pb-section';
if ( true === $make_pb_is_js_template ) {
	$section_name .= '[{{{ parentID }}}][banner-slides][{{{ id }}}]';
} else {
	$section_name .= '[' . $make_pb_section_data[ 'data' ][ 'id' ] . '][banner-slides][' . $make_pb_slide_id . ']';
}

$content          = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['content'] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['content'] : '';
$background_color = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['background-color'] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['background-color'] : '';
$darken           = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['darken'] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['darken'] : 0;
$image_id         = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['image-id'] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['image-id'] : 0;
$alignment        = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['alignment'] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['alignment'] : 'none';
$state            = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['state'] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ]['state'] : 'open';

// Set up the combined section + slide ID
$section_id  = ( isset( $make_pb_section_data['data']['id'] ) ) ? $make_pb_section_data['data']['id'] : '';
$combined_id = ( true === $make_pb_is_js_template ) ? '{{{ parentID }}}-{{{ id }}}' : $section_id . '-' . $make_pb_slide_id;
$overlay_id  = 'make_pb-overlay-' . $combined_id;
?>

<?php if ( true !== $make_pb_is_js_template ) : ?>
<div class="make_pb-banner-slide" id="make_pb-banner-slide-<?php echo esc_attr( $make_pb_slide_id ); ?>" data-id="<?php echo esc_attr( $make_pb_slide_id ); ?>" data-section-type="banner-slide">
<?php endif; ?>

	<div title="<?php esc_attr_e( 'Drag-and-drop this slide into place', 'make' ); ?>" class="make_pb-sortable-handle">
		<div class="sortable-background"></div>
	</div>

	<?php echo make_pb_get_builder_base()->add_uploader( $section_name, make_pb_sanitize_image_id( $image_id ), __( 'Set banner image', 'make' ) ); ?>

	<a href="#" class="configure-banner-slide-link make_pb-banner-slide-configure make_pb-overlay-open" title="<?php esc_attr_e( 'Configure slide', 'make' ); ?>" data-overlay="#<?php echo $overlay_id; ?>">
		<span>
			<?php _e( 'Configure slide', 'make' ); ?>
		</span>
	</a>
	<a href="#" class="edit-content-link edit-banner-slide-link<?php if ( ! empty( $content ) ) : ?> item-has-content<?php endif; ?>" title="<?php esc_attr_e( 'Edit content', 'make' ); ?>" data-textarea="make_pb-content-<?php echo $combined_id; ?>">
		<span>
			<?php _e( 'Edit content', 'make' ); ?>
		</span>
	</a>
	<a href="#" class="remove-banner-slide-link make_pb-banner-slide-remove" title="<?php esc_attr_e( 'Delete slide', 'make' ); ?>">
		<span>
			<?php _e( 'Delete slide', 'make' ); ?>
		</span>
	</a>

	<?php make_pb_get_builder_base()->add_frame( $combined_id, $section_name . '[content]', $content, false ); ?>

	<?php
	global $make_pb_overlay_class, $make_pb_overlay_id, $make_pb_overlay_title;
	$make_pb_overlay_class = 'make_pb-configuration-overlay';
	$make_pb_overlay_id    = $overlay_id;
	$make_pb_overlay_title = __( 'Configure slide', 'make' );

	Make_PB::get_template_part( '/includes/builder/core/templates/overlay', 'header' );

	/**
	 * Filter the definitions of the Banner slide configuration inputs.
	 *
	 * @since 1.4.0.
	 *
	 * @param array    $inputs    The input definition array.
	 */
	$inputs = apply_filters( 'make_banner_slide_configuration', array(
		100 => array(
			'type'    => 'select',
			'name'    => 'alignment',
			'label'   => __( 'Content position', 'make' ),
			'default' => 'none',
			'options' => array(
				'none'  => __( 'None', 'make' ),
				'left'  => __( 'Left', 'make' ),
				'right' => __( 'Right', 'make' ),
			),
		),
		200 => array(
			'type'    => 'checkbox',
			'label'   => __( 'Darken background to improve readability', 'make' ),
			'name'    => 'darken',
			'default' => 0
		),
		300 => array(
			'type'    => 'color',
			'label'   => __( 'Background color', 'make' ),
			'name'    => 'background-color',
			'class'   => 'make_pb-gallery-background-color make_pb-configuration-color-picker',
			'default' => '',
		),
	) );

	// Sort the config in case 3rd party code added another input
	ksort( $inputs, SORT_NUMERIC );

	// Print the inputs
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$section_data  = ( isset( $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ] ) ) ? $make_pb_section_data['data']['banner-slides'][ $make_pb_slide_id ] : array();
			$output       .= Make_PB_Config::create_input( $section_name, $input, $section_data );
		}
	}

	echo $output;

	Make_PB::get_template_part( '/includes/builder/core/templates/overlay', 'footer' );
	?>

	<input type="hidden" class="make_pb-banner-slide-state" name="<?php echo $section_name; ?>[state]" value="<?php echo esc_attr( $state ); ?>" />

	<?php if ( true !== $make_pb_is_js_template ) : ?>
</div>
<?php endif; ?>
