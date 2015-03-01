<?php
/**
 * @package Maera
 */

global $maera_pb_section_data, $maera_pb_sections;
$banner_slides = maera_pb_builder_get_banner_array( $maera_pb_section_data );
$is_slider = ( count( $banner_slides ) > 1 ) ? true : false;
?>

<section id="builder-section-<?php echo esc_attr( $maera_pb_section_data['id'] ); ?>" class="builder-section <?php echo esc_attr( maera_pb_builder_get_banner_class( $maera_pb_section_data, $maera_pb_sections ) ); ?>">
	<?php if ( '' !== $maera_pb_section_data['title'] ) : ?>
	<h3 class="builder-banner-section-title">
		<?php echo apply_filters( 'the_title', $maera_pb_section_data['title'] ); ?>
	</h3>
	<?php endif; ?>
	<div class="builder-section-content<?php echo ( $is_slider ) ? ' cycle-slideshow' : ''; ?>"<?php echo ( $is_slider ) ? maera_pb_builder_get_banner_slider_atts( $maera_pb_section_data ) : ''; ?>>
		<?php if ( ! empty( $banner_slides ) ) : foreach ( $banner_slides as $slide ) : ?>
		<div class="builder-banner-slide<?php echo maera_pb_builder_banner_slide_class( $slide ); ?>" style="<?php echo maera_pb_builder_banner_slide_style( $slide, $maera_pb_section_data ); ?>">
			<div class="builder-banner-content">
				<div class="builder-banner-inner-content">
					<?php maera_pb_get_builder_save()->the_builder_content( $slide['content'] ); ?>
				</div>
			</div>
			<?php if ( 0 !== absint( $slide['darken'] ) ) : ?>
			<div class="builder-banner-overlay"></div>
			<?php endif; ?>
		</div>
		<?php endforeach; endif; ?>
		<?php if ( $is_slider && false === (bool) $maera_pb_section_data['hide-arrows'] ) : ?>
		<div class="cycle-prev"></div>
		<div class="cycle-next"></div>
		<?php endif; ?>
		<?php if ( $is_slider && false === (bool) $maera_pb_section_data['hide-dots'] ) : ?>
		<div class="cycle-pager"></div>
		<?php endif; ?>
	</div>
</section>
