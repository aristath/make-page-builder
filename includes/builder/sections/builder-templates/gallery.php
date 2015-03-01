<?php
/**
 * @package Maera
 */

Maera_PB()->sections->load_header();

global $maera_pb_section_data, $maera_pb_is_js_template;
$section_name     = Maera_PB()->sections->get_section_name( $maera_pb_section_data, $maera_pb_is_js_template );
$columns          = ( isset( $maera_pb_section_data['data']['columns'] ) ) ? $maera_pb_section_data['data']['columns'] : 3;
$caption_color    = ( isset( $maera_pb_section_data['data']['caption-color'] ) ) ? $maera_pb_section_data['data']['caption-color'] : 'light';
$captions         = ( isset( $maera_pb_section_data['data']['captions'] ) ) ? $maera_pb_section_data['data']['captions'] : 'reveal';
$aspect           = ( isset( $maera_pb_section_data['data']['aspect'] ) ) ? $maera_pb_section_data['data']['aspect'] : 'square';
$title            = ( isset( $maera_pb_section_data['data']['title'] ) ) ? $maera_pb_section_data['data']['title'] : '';
$background_image = ( isset( $maera_pb_section_data['data']['background-image'] ) ) ? $maera_pb_section_data['data']['background-image'] : 0;
$background_color = ( isset( $maera_pb_section_data['data']['background-color'] ) ) ? $maera_pb_section_data['data']['background-color'] : '';
$background_style = ( isset( $maera_pb_section_data['data']['background-style'] ) ) ? $maera_pb_section_data['data']['background-style'] : 'tile';
$darken           = ( isset( $maera_pb_section_data['data']['darken'] ) ) ? $maera_pb_section_data['data']['darken'] : 0;
$section_order    = ( ! empty( $maera_pb_section_data['data']['gallery-item-order'] ) ) ? $maera_pb_section_data['data']['gallery-item-order'] : array();
?>

<div class="maera_pb-gallery-items">
	<div class="maera_pb-gallery-items-stage maera_pb-gallery-columns-<?php echo absint( $columns ); ?>">
		<?php foreach ( $section_order as $key => $section_id  ) : ?>
			<?php if ( isset( $maera_pb_section_data['data']['gallery-items'][ $section_id ] ) ) : ?>
				<?php global $maera_pb_gallery_id; $maera_pb_gallery_id = $section_id; ?>
				<?php Maera_PB::get_template_part( '/includes/builder/sections/builder-templates/gallery', 'item' ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<a href="#" class="maera_pb-add-item maera_pb-gallery-add-item-link" title="<?php esc_attr_e( 'Add new item', 'maera' ); ?>">
		<div class="maera_pb-gallery-add-item">
			<span>
				<?php _e( 'Add Item', 'maera' ); ?>
			</span>
		</div>
	</a>

	<input type="hidden" value="<?php echo esc_attr( implode( ',', $section_order ) ); ?>" name="<?php echo $section_name; ?>[gallery-item-order]" class="maera_pb-gallery-item-order" />
</div>

<input type="hidden" class="maera_pb-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $maera_pb_section_data['data']['state'] ) ) echo esc_attr( $maera_pb_section_data['data']['state'] ); else echo 'open'; ?>" />
<?php Maera_PB()->sections->load_footer();
