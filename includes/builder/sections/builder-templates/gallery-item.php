<?php
/**
 * @package Maera
 */

global $maera_pb_section_data, $maera_pb_is_js_template, $maera_pb_gallery_id;
$section_name = 'maera_pb-section';
if ( true === $maera_pb_is_js_template ) {
	$section_name .= '[{{{ parentID }}}][gallery-items][{{{ id }}}]';
} else {
	$section_name .= '[' . $maera_pb_section_data['data']['id'] . '][gallery-items][' . $maera_pb_gallery_id . ']';
}

$title       = ( isset( $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['title'] ) ) ? $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['title'] : '';
$link        = ( isset( $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['link'] ) ) ? $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['link'] : '';
$image_id    = ( isset( $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['image-id'] ) ) ? $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['image-id'] : 0;
$description = ( isset( $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['description'] ) ) ? $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ]['description'] : '';

// Set up the combined section + slide ID
$section_id  = ( isset( $maera_pb_section_data['data']['id'] ) ) ? $maera_pb_section_data['data']['id'] : '';
$combined_id = ( true === $maera_pb_is_js_template ) ? '{{{ parentID }}}-{{{ id }}}' : $section_id . '-' . $maera_pb_gallery_id;
$overlay_id  = 'maera_pb-overlay-' . $combined_id;
?>

<?php if ( true !== $maera_pb_is_js_template ) : ?>
<div class="maera_pb-gallery-item" id="maera_pb-gallery-item-<?php echo esc_attr( $maera_pb_gallery_id ); ?>" data-id="<?php echo esc_attr( $maera_pb_gallery_id ); ?>" data-section-type="gallery-item">
<?php endif; ?>

	<div title="<?php esc_attr_e( 'Drag-and-drop this item into place', 'maera' ); ?>" class="maera_pb-sortable-handle">
		<div class="sortable-background"></div>
	</div>

	<?php echo maera_pb_get_builder_base()->add_uploader( $section_name, maera_pb_sanitize_image_id( $image_id ), __( 'Set gallery image', 'maera' ) ); ?>

	<a href="#" class="configure-gallery-item-link maera_pb-overlay-open" title="<?php esc_attr_e( 'Configure item', 'maera' ); ?>" data-overlay="#<?php echo $overlay_id; ?>">
		<span>
			<?php _e( 'Configure item', 'maera' ); ?>
		</span>
	</a>
	<a href="#" class="edit-content-link edit-gallery-item-link<?php if ( ! empty( $description ) ) : ?> item-has-content<?php endif; ?>" data-textarea="maera_pb-content-<?php echo $combined_id; ?>" title="<?php esc_attr_e( 'Edit content', 'maera' ); ?>">
		<span>
			<?php _e( 'Edit content', 'maera' ); ?>
		</span>
	</a>
	<a href="#" class="remove-gallery-item-link maera_pb-gallery-item-remove" title="<?php esc_attr_e( 'Delete item', 'maera' ); ?>">
		<span>
			<?php _e( 'Delete item', 'maera' ); ?>
		</span>
	</a>

	<?php maera_pb_get_builder_base()->add_frame( $combined_id, $section_name . '[description]', $description, false ); ?>

	<?php
	global $maera_pb_overlay_class, $maera_pb_overlay_id, $maera_pb_overlay_title;
	$maera_pb_overlay_class = 'maera_pb-configuration-overlay';
	$maera_pb_overlay_id    = $overlay_id;
	$maera_pb_overlay_title = __( 'Configure item', 'maera' );

	Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'header' );

	/**
	 * Filter the definitions of the Gallery item configuration inputs.
	 *
	 * @since 1.4.0.
	 *
	 * @param array    $inputs    The input definition array.
	 */
	$inputs = apply_filters( 'maera_gallery_item_configuration', array(
		100 => array(
			'type'    => 'section_title',
			'name'    => 'title',
			'label'   => __( 'Title', 'maera' ),
			'default' => '',
			'class'   => 'maera_pb-configuration-title',
		),
		200 => array(
			'type'    => 'text',
			'name'    => 'link',
			'label'   => __( 'Item link URL', 'maera' ),
			'default' => '',
		),
	) );

	// Sort the config in case 3rd party code added another input
	ksort( $inputs, SORT_NUMERIC );

	// Print the inputs
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$section_data  = ( isset( $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ] ) ) ? $maera_pb_section_data['data']['gallery-items'][ $maera_pb_gallery_id ] : array();
			$output       .= Maera_PB_Config::create_input( $section_name, $input, $section_data );
		}
	}

	echo $output;

	Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'footer' );
	?>

<?php if ( true !== $maera_pb_is_js_template ) : ?>
</div>
<?php endif; ?>
