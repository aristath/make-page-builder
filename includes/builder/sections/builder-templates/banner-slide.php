<?php
/**
 * @package Maera
 */

global $maera_pb_section_data, $maera_pb_is_js_template, $maera_pb_slide_id;
$section_name = 'maera_pb-section';
if ( true === $maera_pb_is_js_template ) {
	$section_name .= '[{{{ parentID }}}][banner-slides][{{{ id }}}]';
} else {
	$section_name .= '[' . $maera_pb_section_data[ 'data' ][ 'id' ] . '][banner-slides][' . $maera_pb_slide_id . ']';
}

$content          = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['content'] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['content'] : '';
$background_color = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['background-color'] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['background-color'] : '';
$darken           = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['darken'] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['darken'] : 0;
$image_id         = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['image-id'] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['image-id'] : 0;
$alignment        = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['alignment'] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['alignment'] : 'none';
$state            = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['state'] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ]['state'] : 'open';

// Set up the combined section + slide ID
$section_id  = ( isset( $maera_pb_section_data['data']['id'] ) ) ? $maera_pb_section_data['data']['id'] : '';
$combined_id = ( true === $maera_pb_is_js_template ) ? '{{{ parentID }}}-{{{ id }}}' : $section_id . '-' . $maera_pb_slide_id;
$overlay_id  = 'maera_pb-overlay-' . $combined_id;
?>

<?php if ( true !== $maera_pb_is_js_template ) : ?>
<div class="maera_pb-banner-slide" id="maera_pb-banner-slide-<?php echo esc_attr( $maera_pb_slide_id ); ?>" data-id="<?php echo esc_attr( $maera_pb_slide_id ); ?>" data-section-type="banner-slide">
<?php endif; ?>

	<div title="<?php esc_attr_e( 'Drag-and-drop this slide into place', 'maera' ); ?>" class="maera_pb-sortable-handle">
		<div class="sortable-background"></div>
	</div>

	<?php echo maera_pb_get_builder_base()->add_uploader( $section_name, maera_pb_sanitize_image_id( $image_id ), __( 'Set banner image', 'maera' ) ); ?>

	<a href="#" class="configure-banner-slide-link maera_pb-banner-slide-configure maera_pb-overlay-open" title="<?php esc_attr_e( 'Configure slide', 'maera' ); ?>" data-overlay="#<?php echo $overlay_id; ?>">
		<span>
			<?php _e( 'Configure slide', 'maera' ); ?>
		</span>
	</a>
	<a href="#" class="edit-content-link edit-banner-slide-link<?php if ( ! empty( $content ) ) : ?> item-has-content<?php endif; ?>" title="<?php esc_attr_e( 'Edit content', 'maera' ); ?>" data-textarea="maera_pb-content-<?php echo $combined_id; ?>">
		<span>
			<?php _e( 'Edit content', 'maera' ); ?>
		</span>
	</a>
	<a href="#" class="remove-banner-slide-link maera_pb-banner-slide-remove" title="<?php esc_attr_e( 'Delete slide', 'maera' ); ?>">
		<span>
			<?php _e( 'Delete slide', 'maera' ); ?>
		</span>
	</a>

	<?php maera_pb_get_builder_base()->add_frame( $combined_id, $section_name . '[content]', $content, false ); ?>

	<?php
	global $maera_pb_overlay_class, $maera_pb_overlay_id, $maera_pb_overlay_title;
	$maera_pb_overlay_class = 'maera_pb-configuration-overlay';
	$maera_pb_overlay_id    = $overlay_id;
	$maera_pb_overlay_title = __( 'Configure slide', 'maera' );

	Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'header' );

	/**
	 * Filter the definitions of the Banner slide configuration inputs.
	 *
	 * @since 1.4.0.
	 *
	 * @param array    $inputs    The input definition array.
	 */
	$inputs = apply_filters( 'maera_banner_slide_configuration', array(
		100 => array(
			'type'    => 'select',
			'name'    => 'alignment',
			'label'   => __( 'Content position', 'maera' ),
			'default' => 'none',
			'options' => array(
				'none'  => __( 'None', 'maera' ),
				'left'  => __( 'Left', 'maera' ),
				'right' => __( 'Right', 'maera' ),
			),
		),
		200 => array(
			'type'    => 'checkbox',
			'label'   => __( 'Darken background to improve readability', 'maera' ),
			'name'    => 'darken',
			'default' => 0
		),
		300 => array(
			'type'    => 'color',
			'label'   => __( 'Background color', 'maera' ),
			'name'    => 'background-color',
			'class'   => 'maera_pb-gallery-background-color maera_pb-configuration-color-picker',
			'default' => '',
		),
	) );

	// Sort the config in case 3rd party code added another input
	ksort( $inputs, SORT_NUMERIC );

	// Print the inputs
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$section_data  = ( isset( $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ] ) ) ? $maera_pb_section_data['data']['banner-slides'][ $maera_pb_slide_id ] : array();
			$output       .= Maera_PB_Config::create_input( $section_name, $input, $section_data );
		}
	}

	echo $output;

	Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'footer' );
	?>

	<input type="hidden" class="maera_pb-banner-slide-state" name="<?php echo $section_name; ?>[state]" value="<?php echo esc_attr( $state ); ?>" />

	<?php if ( true !== $maera_pb_is_js_template ) : ?>
</div>
<?php endif; ?>
