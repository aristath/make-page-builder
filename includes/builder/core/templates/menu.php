<?php
/**
 * @package Make
 */

$class = ( 'c' === get_user_setting( 'make_pbmt' . get_the_ID() ) ) ? 'closed' : 'opened';
?>

<div class="make_pb-menu make_pb-menu-<?php echo esc_attr( $class ); ?>" id="make_pb-menu">
	<div class="make_pb-menu-pane">
		<ul class="make_pb-menu-list">
			<?php
			/**
			 * Execute code before the builder menu is displayed.
			 *
			 * @since 1.2.3.
			 */
			do_action( 'make_before_builder_menu' );
			?>
			<?php foreach ( make_pb_get_sections_by_order() as $key => $item ) : ?>
			<a href="#" title="<?php echo esc_html( $item['description'] ); ?>" class="make_pb-menu-list-item-link" id="make_pb-menu-list-item-link-<?php echo esc_attr( $item['id'] ); ?>" data-section="<?php echo esc_attr( $item['id'] ); ?>">

				<li class="make_pb-menu-list-item">
						<div class="make_pb-menu-list-item-link-icon-wrapper clear">
							<span class="make_pb-menu-list-item-link-icon"></span>
							<div class="section-type-description">
								<h4>
									<?php echo esc_html( $item['label'] ); ?>
								</h4>
							</div>
						</div>

				</li>
				</a>
			<?php endforeach; ?>
			<?php
			/**
			 * Execute code after the builder menu is displayed.
			 *
			 * @since 1.2.3.
			 */
			do_action( 'make_after_builder_menu' );
			?>
		</ul>
	</div>
</div>
