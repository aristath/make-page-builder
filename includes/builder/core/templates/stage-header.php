<?php
/**
 * @package Maera
 */

global $maera_pb_sections;
?>

<div class="maera_pb-stage<?php if ( empty( $maera_pb_sections ) ) echo ' maera_pb-stage-closed'?>" id="maera_pb-stage">
	<?php
	/**
	 * Execute code before the builder stage is displayed.
	 *
	 * @since 1.2.3.
	 */
	do_action( 'maera_before_builder_stage' );