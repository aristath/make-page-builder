<?php
/**
 * @package Make
 */

global $make_pb_sections;
?>

<div class="make_pb-stage<?php if ( empty( $make_pb_sections ) ) echo ' make_pb-stage-closed'?>" id="make_pb-stage">
	<?php
	/**
	 * Execute code before the builder stage is displayed.
	 *
	 * @since 1.2.3.
	 */
	do_action( 'make_before_builder_stage' );