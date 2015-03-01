<?php global $maera_pb_overlay_id, $maera_pb_overlay_class, $maera_pb_overlay_title; ?>
<div class="maera_pb-overlay <?php if ( ! empty( $maera_pb_overlay_class ) ) echo $maera_pb_overlay_class; ?>"<?php if ( ! empty( $maera_pb_overlay_id ) ) echo ' id="' . $maera_pb_overlay_id . '"'; ?>>
	<div class="maera_pb-overlay-wrapper">
		<div class="maera_pb-overlay-header">
			<div class="maera_pb-overlay-window-head">
				<div class="maera_pb-overlay-title"><?php if ( ! empty( $maera_pb_overlay_title ) ) : echo $maera_pb_overlay_title; else : _e( 'Configuration', 'maera' ); endif; ?></div>
				<span class="maera_pb-overlay-close maera_pb-overlay-close-action" aria-hidden="true"><?php _e( 'Done', 'maera' ); ?></span>
			</div>
		</div>
		<div class="maera_pb-overlay-body">