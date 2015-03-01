<?php
/**
 * @package Make
 */

global $make_pb_section_data, $make_pb_is_js_template;

$links = array(
	100 => array(
	'href'  => '#',
	'class' => 'make_pb-section-remove',
	'label' => __( 'Delete section', 'make' ),
	'title' => __( 'Delete section', 'make' ),
) );

if ( ! empty( $make_pb_section_data['section']['config'] ) ) {
	$id = ( true === $make_pb_is_js_template ) ? '{{{ id }}}' : esc_attr( $make_pb_section_data['data']['id'] );
	$links[25] = array(
		'href'  => '#',
		'class' => 'make_pb-section-configure make_pb-overlay-open',
		'label' => __( 'Configure section', 'make' ),
		'title' => __( 'Configure section', 'make' ),
		'other' => 'data-overlay="#make_pb-overlay-' . $id . '"'
	);
}

/**
 * Deprecated: Filter the definitions for the links that appear in each Builder section's footer.
 *
 * This filter is deprecated. Use make_builder_section_links instead.
 *
 * @since 1.0.7.
 *
 * @param array    $links    The link definition array.
 */
$links = apply_filters( 'make_pb_builder_section_footer_links', $links );
/**
 * Filter the definitions for the buttons that appear in each Builder section's header.
 *
 * @since 1.4.0.
 *
 * @param array    $links    The button definition array.
 */
$links = apply_filters( 'make_builder_section_links', $links );
ksort( $links );
?>

<?php if ( ! isset( $make_pb_is_js_template ) || true !== $make_pb_is_js_template ) : ?>
<div class="make_pb-section <?php if ( isset( $make_pb_section_data['data']['state'] ) && 'open' === $make_pb_section_data['data']['state'] ) echo 'make_pb-section-open'; ?> make_pb-section-<?php echo esc_attr( $make_pb_section_data['section']['id'] ); ?>" id="<?php echo 'make_pb-section-' . esc_attr( $make_pb_section_data['data']['id'] ); ?>" data-id="<?php echo esc_attr( $make_pb_section_data['data']['id'] ); ?>" data-section-type="<?php echo esc_attr( $make_pb_section_data['section']['id'] ); ?>">
<?php endif; ?>
	<?php
	/**
	 * Execute code before the section header is displayed.
	 *
	 * @since 1.2.3.
	 */
	do_action( 'make_before_section_header' );
	?>
	<div class="make_pb-section-header">
		<?php $header_title = ( isset( $make_pb_section_data['data']['label'] ) ) ? $make_pb_section_data['data']['label'] : ''; ?>
		<h3>
			<span class="make_pb-section-header-title"><?php echo esc_html( $header_title ); ?></span><em><?php echo ( esc_html( $make_pb_section_data['section']['label'] ) ); ?></em>
		</h3>
		<div class="ttf-make-section-header-button-wrapper">
			<?php foreach ( $links as $link ) : ?>
				<?php
				$href  = ( isset( $link['href'] ) ) ? ' href="' . esc_url( $link['href'] ) . '"' : '';
				$id    = ( isset( $link['id'] ) ) ? ' id="' . esc_attr( $link['id'] ) . '"' : '';
				$label = ( isset( $link['label'] ) ) ? esc_html( $link['label'] ) : '';
				$title = ( isset( $link['title'] ) ) ? ' title="' . esc_html( $link['title'] ) . '"' : '';
				$other = ( isset( $link['other'] ) ) ? ' ' . $link['other'] : '';

				// Set up the class value with a base class
				$class_base = ' class="make_pb-builder-section-link';
				$class      = ( isset( $link['class'] ) ) ? $class_base . ' ' . esc_attr( $link['class'] ) . '"' : '"';
				?>
				<a<?php echo $href . $id . $class . $title . $other; ?>>
					<span>
						<?php echo $label; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
		<a href="#" class="make_pb-section-toggle" title="<?php esc_attr_e( 'Click to toggle', 'make' ); ?>">
			<div class="handlediv"></div>
		</a>
	</div>
	<div class="clear"></div>
	<div class="make_pb-section-body">
		<input type="hidden" value="<?php echo $make_pb_section_data['section']['id']; ?>" name="<?php echo Make_PB()->sections->get_section_name( $make_pb_section_data, $make_pb_is_js_template ); ?>[section-type]" />
