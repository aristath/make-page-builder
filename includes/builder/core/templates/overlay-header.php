<?php global $make_pb_overlay_id, $make_pb_overlay_class, $make_pb_overlay_title; ?>
<div class="make_pb-overlay <?php if ( ! empty( $make_pb_overlay_class ) ) echo $make_pb_overlay_class; ?>"<?php if ( ! empty( $make_pb_overlay_id ) ) echo ' id="' . $make_pb_overlay_id . '"'; ?>>
	<div class="make_pb-overlay-wrapper">
		<div class="make_pb-overlay-header">
			<div class="make_pb-overlay-window-head">
				<div class="make_pb-overlay-title"><?php if ( ! empty( $make_pb_overlay_title ) ) : echo $make_pb_overlay_title; else : _e( 'Configuration', 'make' ); endif; ?></div>
				<span class="make_pb-overlay-close make_pb-overlay-close-action" aria-hidden="true"><?php _e( 'Done', 'make' ); ?></span>
			</div>
		</div>
		<div class="make_pb-overlay-body">