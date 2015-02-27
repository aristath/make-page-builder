<?php
/**
 * @package Make
 */

global $make_pb_section_data, $make_pb_is_js_template;
?>

	<?php if ( ! empty( $make_pb_section_data['section']['config'] ) ) : ?>
		<?php global $make_pb_overlay_id; $id = ( true === $make_pb_is_js_template ) ? '{{{ id }}}' : esc_attr( $make_pb_section_data['data']['id'] ); $make_pb_overlay_id = 'make_pb-overlay-' . $id; ?>
		<?php Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'configuration' ); ?>
	<?php endif; ?>

	</div>
<?php if ( ! isset( $make_pb_is_js_template ) || true !== $make_pb_is_js_template ) : ?>
</div>
<?php endif; ?>
