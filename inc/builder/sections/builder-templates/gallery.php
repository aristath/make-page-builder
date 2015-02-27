<?php
/**
 * @package Make
 */

make_pb_load_section_header();

global $make_pb_section_data, $make_pb_is_js_template;
$section_name     = make_pb_get_section_name( $make_pb_section_data, $make_pb_is_js_template );
$columns          = ( isset( $make_pb_section_data['data']['columns'] ) ) ? $make_pb_section_data['data']['columns'] : 3;
$caption_color    = ( isset( $make_pb_section_data['data']['caption-color'] ) ) ? $make_pb_section_data['data']['caption-color'] : 'light';
$captions         = ( isset( $make_pb_section_data['data']['captions'] ) ) ? $make_pb_section_data['data']['captions'] : 'reveal';
$aspect           = ( isset( $make_pb_section_data['data']['aspect'] ) ) ? $make_pb_section_data['data']['aspect'] : 'square';
$title            = ( isset( $make_pb_section_data['data']['title'] ) ) ? $make_pb_section_data['data']['title'] : '';
$background_image = ( isset( $make_pb_section_data['data']['background-image'] ) ) ? $make_pb_section_data['data']['background-image'] : 0;
$background_color = ( isset( $make_pb_section_data['data']['background-color'] ) ) ? $make_pb_section_data['data']['background-color'] : '';
$background_style = ( isset( $make_pb_section_data['data']['background-style'] ) ) ? $make_pb_section_data['data']['background-style'] : 'tile';
$darken           = ( isset( $make_pb_section_data['data']['darken'] ) ) ? $make_pb_section_data['data']['darken'] : 0;
$section_order    = ( ! empty( $make_pb_section_data['data']['gallery-item-order'] ) ) ? $make_pb_section_data['data']['gallery-item-order'] : array();
?>

<div class="make_pb-gallery-items">
	<div class="make_pb-gallery-items-stage make_pb-gallery-columns-<?php echo absint( $columns ); ?>">
		<?php foreach ( $section_order as $key => $section_id  ) : ?>
			<?php if ( isset( $make_pb_section_data['data']['gallery-items'][ $section_id ] ) ) : ?>
				<?php global $make_pb_gallery_id; $make_pb_gallery_id = $section_id; ?>
				<?php Make_PB::get_template_part( '/inc/builder/sections/builder-templates/gallery', 'item' ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<a href="#" class="make_pb-add-item make_pb-gallery-add-item-link" title="<?php esc_attr_e( 'Add new item', 'make' ); ?>">
		<div class="make_pb-gallery-add-item">
			<span>
				<?php _e( 'Add Item', 'make' ); ?>
			</span>
		</div>
	</a>

	<input type="hidden" value="<?php echo esc_attr( implode( ',', $section_order ) ); ?>" name="<?php echo $section_name; ?>[gallery-item-order]" class="make_pb-gallery-item-order" />
</div>

<input type="hidden" class="make_pb-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $make_pb_section_data['data']['state'] ) ) echo esc_attr( $make_pb_section_data['data']['state'] ); else echo 'open'; ?>" />
<?php make_pb_load_section_footer();
