<?php
/**
 * @package Make
 */
Make_PB()->sections->load_header();

global $make_pb_section_data, $make_pb_is_js_template;
$section_name  = Make_PB()->sections->get_section_name( $make_pb_section_data, $make_pb_is_js_template );
$title         = ( isset( $make_pb_section_data['data']['title'] ) ) ? $make_pb_section_data['data']['title'] : '';
$hide_arrows   = ( isset( $make_pb_section_data['data']['hide-arrows'] ) ) ? $make_pb_section_data['data']['hide-arrows'] : 0;
$hide_dots     = ( isset( $make_pb_section_data['data']['hide-dots'] ) ) ? $make_pb_section_data['data']['hide-dots'] : 0;
$autoplay      = ( isset( $make_pb_section_data['data']['autoplay'] ) ) ? $make_pb_section_data['data']['autoplay'] : 1;
$transition    = ( isset( $make_pb_section_data['data']['transition'] ) ) ? $make_pb_section_data['data']['transition'] : 'scrollHorz';
$delay         = ( isset( $make_pb_section_data['data']['delay'] ) ) ? $make_pb_section_data['data']['delay'] : 6000;
$height        = ( isset( $make_pb_section_data['data']['height'] ) ) ? $make_pb_section_data['data']['height'] : 600;
$responsive    = ( isset( $make_pb_section_data['data']['responsive'] ) ) ? $make_pb_section_data['data']['responsive'] : 'balanced';
$section_order = ( ! empty( $make_pb_section_data['data']['banner-slide-order'] ) ) ? $make_pb_section_data['data']['banner-slide-order'] : array();
?>

<div class="make_pb-banner-slides">
	<div class="make_pb-banner-slides-stage">
		<?php foreach ( $section_order as $key => $section_id  ) : ?>
			<?php if ( isset( $make_pb_section_data['data']['banner-slides'][ $section_id ] ) ) : ?>
				<?php global $make_pb_slide_id; $make_pb_slide_id = $section_id; ?>
				<?php Make_PB::get_template_part( '/inc/builder/sections/builder-templates/banner', 'slide' ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<a href="#" class="make_pb-add-slide make_pb-banner-add-item-link" title="<?php esc_attr_e( 'Add new slide', 'make' ); ?>">
		<div class="make_pb-banner-add-item">
			<span>
				<?php _e( 'Add Item', 'make' ); ?>
			</span>
		</div>
	</a>

	<input type="hidden" value="<?php echo esc_attr( implode( ',', $section_order ) ); ?>" name="<?php echo $section_name; ?>[banner-slide-order]" class="make_pb-banner-slide-order" />
</div>

<input type="hidden" class="make_pb-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $make_pb_section_data['data']['state'] ) ) echo esc_attr( $make_pb_section_data['data']['state'] ); else echo 'open'; ?>" />
<?php Make_PB()->sections->load_footer();
