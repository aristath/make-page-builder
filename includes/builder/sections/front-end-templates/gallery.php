<?php
/**
 * @package Maera
 */

global $maera_pb_section_data, $maera_pb_sections;
$gallery  = maera_pb_builder_get_gallery_array( $maera_pb_section_data );
$darken   = ( isset( $maera_pb_section_data[ 'darken' ] ) ) ? absint( $maera_pb_section_data[ 'darken' ] ) : 0;
$captions = ( isset( $maera_pb_section_data[ 'captions' ] ) ) ? esc_attr( $maera_pb_section_data[ 'captions' ] ) : 'reveal';
$aspect   = ( isset( $maera_pb_section_data[ 'aspect' ] ) ) ? esc_attr( $maera_pb_section_data[ 'aspect' ] ) : 'square';
?>

<section id="builder-section-<?php echo esc_attr( $maera_pb_section_data['id'] ); ?>" class="builder-section<?php echo esc_attr( maera_pb_builder_get_gallery_class( $maera_pb_section_data, $maera_pb_sections ) ); ?>" style="<?php echo esc_attr( maera_pb_builder_get_gallery_style( $maera_pb_section_data ) ); ?>">
	<?php if ( '' !== $maera_pb_section_data['title'] ) : ?>
	<h3 class="builder-gallery-section-title">
		<?php echo apply_filters( 'the_title', $maera_pb_section_data['title'] ); ?>
	</h3>
	<?php endif; ?>
	<div class="builder-section-content">
		<?php if ( ! empty( $gallery ) ) : $i = 0; foreach ( $gallery as $item ) :
			$onclick = ' onclick="return false;"';
			if ( '' !== $item['link'] ) :
				$onclick = ' onclick="window.location.href = \'' . esc_js( esc_url( $item['link'] ) ) . '\';"';
			endif;
			$i++;
		?>
		<div class="builder-gallery-item<?php echo esc_attr( maera_pb_builder_get_gallery_item_class( $item, $maera_pb_section_data, $i ) ); ?>"<?php echo $onclick; ?>>
			<?php $image = maera_pb_builder_get_gallery_item_image( $item, $aspect ); ?>
			<?php if ( '' !== $image ) : ?>
				<?php echo $image; ?>
			<?php endif; ?>
			<?php if ( 'none' !== $captions && ( '' !== $item['title'] || '' !== $item['description'] || has_excerpt( $item['image-id'] ) ) ) : ?>
			<div class="builder-gallery-content">
				<div class="builder-gallery-content-inner">
					<?php if ( '' !== $item['title'] ) : ?>
					<h4 class="builder-gallery-title">
						<?php echo apply_filters( 'the_title', $item['title'] ); ?>
					</h4>
					<?php endif; ?>
					<?php if ( '' !== $item['description'] ) : ?>
					<div class="builder-gallery-description">
						<?php maera_pb_get_builder_save()->the_builder_content( $item['description'] ); ?>
					</div>
					<?php elseif ( has_excerpt( $item['image-id'] ) ) : ?>
					<div class="builder-gallery-description">
						<?php echo maera_pb_sanitize_text( get_post( $item['image-id'] )->post_excerpt ); ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endforeach; endif; ?>
	</div>
	<?php if ( 0 !== $darken ) : ?>
	<div class="builder-section-overlay"></div>
	<?php endif; ?>
</section>
