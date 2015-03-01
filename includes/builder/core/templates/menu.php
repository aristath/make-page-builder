<?php
/**
 * @package Maera
 */

$class = ( 'c' === get_user_setting( 'maera_pbmt' . get_the_ID() ) ) ? 'closed' : 'opened';
?>

<div class="maera_pb-menu maera_pb-menu-<?php echo esc_attr( $class ); ?>" id="maera_pb-menu">
	<div class="maera_pb-menu-pane">
		<ul class="maera_pb-menu-list">
			<?php
			/**
			 * Execute code before the builder menu is displayed.
			 *
			 * @since 1.2.3.
			 */
			do_action( 'maera_before_builder_menu' );
			?>
			<?php foreach ( maera_pb_get_sections_by_order() as $key => $item ) : ?>
			<a href="#" title="<?php echo esc_html( $item['description'] ); ?>" class="maera_pb-menu-list-item-link" id="maera_pb-menu-list-item-link-<?php echo esc_attr( $item['id'] ); ?>" data-section="<?php echo esc_attr( $item['id'] ); ?>">

				<li class="maera_pb-menu-list-item">
						<div class="maera_pb-menu-list-item-link-icon-wrapper clear">
							<span class="maera_pb-menu-list-item-link-icon"></span>
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
			do_action( 'maera_after_builder_menu' );
			?>
		</ul>
	</div>
</div>
