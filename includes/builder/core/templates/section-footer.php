<?php
/**
 * @package Maera
 */

global $maera_pb_section_data, $maera_pb_is_js_template;
?>

	<?php if ( ! empty( $maera_pb_section_data['section']['config'] ) ) : ?>
		<?php global $maera_pb_overlay_id; $id = ( true === $maera_pb_is_js_template ) ? '{{{ id }}}' : esc_attr( $maera_pb_section_data['data']['id'] ); $maera_pb_overlay_id = 'maera_pb-overlay-' . $id; ?>
		<?php Maera_PB::get_template_part( '/includes/builder/core/templates/overlay', 'configuration' ); ?>
	<?php endif; ?>

	</div>
<?php if ( ! isset( $maera_pb_is_js_template ) || true !== $maera_pb_is_js_template ) : ?>
</div>
<?php endif; ?>
