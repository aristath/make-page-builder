<?php
/**
 * @package Maera
 */
Maera_PB()->sections->load_header();

global $maera_pb_section_data, $maera_pb_is_js_template;
$section_name  = Maera_PB()->sections->get_section_name( $maera_pb_section_data, $maera_pb_is_js_template );
$title         = ( isset( $maera_pb_section_data['data']['title'] ) ) ? $maera_pb_section_data['data']['title'] : '';
$hide_arrows   = ( isset( $maera_pb_section_data['data']['hide-arrows'] ) ) ? $maera_pb_section_data['data']['hide-arrows'] : 0;
$hide_dots     = ( isset( $maera_pb_section_data['data']['hide-dots'] ) ) ? $maera_pb_section_data['data']['hide-dots'] : 0;
$autoplay      = ( isset( $maera_pb_section_data['data']['autoplay'] ) ) ? $maera_pb_section_data['data']['autoplay'] : 1;
$transition    = ( isset( $maera_pb_section_data['data']['transition'] ) ) ? $maera_pb_section_data['data']['transition'] : 'scrollHorz';
$delay         = ( isset( $maera_pb_section_data['data']['delay'] ) ) ? $maera_pb_section_data['data']['delay'] : 6000;
$height        = ( isset( $maera_pb_section_data['data']['height'] ) ) ? $maera_pb_section_data['data']['height'] : 600;
$responsive    = ( isset( $maera_pb_section_data['data']['responsive'] ) ) ? $maera_pb_section_data['data']['responsive'] : 'balanced';
$section_order = ( ! empty( $maera_pb_section_data['data']['banner-slide-order'] ) ) ? $maera_pb_section_data['data']['banner-slide-order'] : array();
?>

<div class="maera_pb-banner-slides">
	<div class="maera_pb-banner-slides-stage">
		<?php foreach ( $section_order as $key => $section_id  ) : ?>
			<?php if ( isset( $maera_pb_section_data['data']['banner-slides'][ $section_id ] ) ) : ?>
				<?php global $maera_pb_slide_id; $maera_pb_slide_id = $section_id; ?>
				<?php Maera_PB::get_template_part( '/includes/builder/sections/builder-templates/banner', 'slide' ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<a href="#" class="maera_pb-add-slide maera_pb-banner-add-item-link" title="<?php esc_attr_e( 'Add new slide', 'maera' ); ?>">
		<div class="maera_pb-banner-add-item">
			<span>
				<?php _e( 'Add Item', 'maera' ); ?>
			</span>
		</div>
	</a>

	<input type="hidden" value="<?php echo esc_attr( implode( ',', $section_order ) ); ?>" name="<?php echo $section_name; ?>[banner-slide-order]" class="maera_pb-banner-slide-order" />
</div>

<input type="hidden" class="maera_pb-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $maera_pb_section_data['data']['state'] ) ) echo esc_attr( $maera_pb_section_data['data']['state'] ); else echo 'open'; ?>" />
<?php Maera_PB()->sections->load_footer();
