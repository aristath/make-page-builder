<?php
/**
 * @package Make
 */

global $make_pb_section_data, $make_pb_is_js_template, $make_pb_gallery_id;
$section_name = 'make_pb-section';
if ( true === $make_pb_is_js_template ) {
	$section_name .= '[{{{ parentID }}}][gallery-items][{{{ id }}}]';
} else {
	$section_name .= '[' . $make_pb_section_data['data']['id'] . '][gallery-items][' . $make_pb_gallery_id . ']';
}

$title       = ( isset( $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['title'] ) ) ? $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['title'] : '';
$link        = ( isset( $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['link'] ) ) ? $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['link'] : '';
$image_id    = ( isset( $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['image-id'] ) ) ? $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['image-id'] : 0;
$description = ( isset( $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['description'] ) ) ? $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ]['description'] : '';

// Set up the combined section + slide ID
$section_id  = ( isset( $make_pb_section_data['data']['id'] ) ) ? $make_pb_section_data['data']['id'] : '';
$combined_id = ( true === $make_pb_is_js_template ) ? '{{{ parentID }}}-{{{ id }}}' : $section_id . '-' . $make_pb_gallery_id;
$overlay_id  = 'make_pb-overlay-' . $combined_id;
?>

<?php if ( true !== $make_pb_is_js_template ) : ?>
<div class="make_pb-gallery-item" id="make_pb-gallery-item-<?php echo esc_attr( $make_pb_gallery_id ); ?>" data-id="<?php echo esc_attr( $make_pb_gallery_id ); ?>" data-section-type="gallery-item">
<?php endif; ?>

	<div title="<?php esc_attr_e( 'Drag-and-drop this item into place', 'make' ); ?>" class="make_pb-sortable-handle">
		<div class="sortable-background"></div>
	</div>

	<?php echo make_pb_get_builder_base()->add_uploader( $section_name, make_pb_sanitize_image_id( $image_id ), __( 'Set gallery image', 'make' ) ); ?>

	<a href="#" class="configure-gallery-item-link make_pb-overlay-open" title="<?php esc_attr_e( 'Configure item', 'make' ); ?>" data-overlay="#<?php echo $overlay_id; ?>">
		<span>
			<?php _e( 'Configure item', 'make' ); ?>
		</span>
	</a>
	<a href="#" class="edit-content-link edit-gallery-item-link<?php if ( ! empty( $description ) ) : ?> item-has-content<?php endif; ?>" data-textarea="make_pb-content-<?php echo $combined_id; ?>" title="<?php esc_attr_e( 'Edit content', 'make' ); ?>">
		<span>
			<?php _e( 'Edit content', 'make' ); ?>
		</span>
	</a>
	<a href="#" class="remove-gallery-item-link make_pb-gallery-item-remove" title="<?php esc_attr_e( 'Delete item', 'make' ); ?>">
		<span>
			<?php _e( 'Delete item', 'make' ); ?>
		</span>
	</a>

	<?php make_pb_get_builder_base()->add_frame( $combined_id, $section_name . '[description]', $description, false ); ?>

	<?php
	global $make_pb_overlay_class, $make_pb_overlay_id, $make_pb_overlay_title;
	$make_pb_overlay_class = 'make_pb-configuration-overlay';
	$make_pb_overlay_id    = $overlay_id;
	$make_pb_overlay_title = __( 'Configure item', 'make' );

	Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'header' );

	/**
	 * Filter the definitions of the Gallery item configuration inputs.
	 *
	 * @since 1.4.0.
	 *
	 * @param array    $inputs    The input definition array.
	 */
	$inputs = apply_filters( 'make_gallery_item_configuration', array(
		100 => array(
			'type'    => 'section_title',
			'name'    => 'title',
			'label'   => __( 'Title', 'make' ),
			'default' => '',
			'class'   => 'make_pb-configuration-title',
		),
		200 => array(
			'type'    => 'text',
			'name'    => 'link',
			'label'   => __( 'Item link URL', 'make' ),
			'default' => '',
		),
	) );

	// Sort the config in case 3rd party code added another input
	ksort( $inputs, SORT_NUMERIC );

	// Print the inputs
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$section_data  = ( isset( $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ] ) ) ? $make_pb_section_data['data']['gallery-items'][ $make_pb_gallery_id ] : array();
			$output       .= Make_PB_Config::create_input( $section_name, $input, $section_data );
		}
	}

	echo $output;

	Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'footer' );
	?>

<?php if ( true !== $make_pb_is_js_template ) : ?>
</div>
<?php endif; ?>
