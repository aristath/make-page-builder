<?php
/**
 * @package Maera
 */

global $maera_pb_section_data, $maera_pb_is_js_template;

$links = array(
	100 => array(
	'href'  => '#',
	'class' => 'maera_pb-section-remove',
	'label' => __( 'Delete section', 'maera' ),
	'title' => __( 'Delete section', 'maera' ),
) );

if ( ! empty( $maera_pb_section_data['section']['config'] ) ) {
	$id = ( true === $maera_pb_is_js_template ) ? '{{{ id }}}' : esc_attr( $maera_pb_section_data['data']['id'] );
	$links[25] = array(
		'href'  => '#',
		'class' => 'maera_pb-section-configure maera_pb-overlay-open',
		'label' => __( 'Configure section', 'maera' ),
		'title' => __( 'Configure section', 'maera' ),
		'other' => 'data-overlay="#maera_pb-overlay-' . $id . '"'
	);
}

/**
 * Deprecated: Filter the definitions for the links that appear in each Builder section's footer.
 *
 * This filter is deprecated. Use maera_builder_section_links instead.
 *
 * @since 1.0.7.
 *
 * @param array    $links    The link definition array.
 */
$links = apply_filters( 'maera_pb_builder_section_footer_links', $links );
/**
 * Filter the definitions for the buttons that appear in each Builder section's header.
 *
 * @since 1.4.0.
 *
 * @param array    $links    The button definition array.
 */
$links = apply_filters( 'maera_builder_section_links', $links );
ksort( $links );
?>

<?php if ( ! isset( $maera_pb_is_js_template ) || true !== $maera_pb_is_js_template ) : ?>
<div class="maera_pb-section <?php if ( isset( $maera_pb_section_data['data']['state'] ) && 'open' === $maera_pb_section_data['data']['state'] ) echo 'maera_pb-section-open'; ?> maera_pb-section-<?php echo esc_attr( $maera_pb_section_data['section']['id'] ); ?>" id="<?php echo 'maera_pb-section-' . esc_attr( $maera_pb_section_data['data']['id'] ); ?>" data-id="<?php echo esc_attr( $maera_pb_section_data['data']['id'] ); ?>" data-section-type="<?php echo esc_attr( $maera_pb_section_data['section']['id'] ); ?>">
<?php endif; ?>
	<?php
	/**
	 * Execute code before the section header is displayed.
	 *
	 * @since 1.2.3.
	 */
	do_action( 'maera_before_section_header' );
	?>
	<div class="maera_pb-section-header">
		<?php $header_title = ( isset( $maera_pb_section_data['data']['label'] ) ) ? $maera_pb_section_data['data']['label'] : ''; ?>
		<h3>
			<span class="maera_pb-section-header-title"><?php echo esc_html( $header_title ); ?></span><em><?php echo ( esc_html( $maera_pb_section_data['section']['label'] ) ); ?></em>
		</h3>
		<div class="ttf-maera-section-header-button-wrapper">
			<?php foreach ( $links as $link ) : ?>
				<?php
				$href  = ( isset( $link['href'] ) ) ? ' href="' . esc_url( $link['href'] ) . '"' : '';
				$id    = ( isset( $link['id'] ) ) ? ' id="' . esc_attr( $link['id'] ) . '"' : '';
				$label = ( isset( $link['label'] ) ) ? esc_html( $link['label'] ) : '';
				$title = ( isset( $link['title'] ) ) ? ' title="' . esc_html( $link['title'] ) . '"' : '';
				$other = ( isset( $link['other'] ) ) ? ' ' . $link['other'] : '';

				// Set up the class value with a base class
				$class_base = ' class="maera_pb-builder-section-link';
				$class      = ( isset( $link['class'] ) ) ? $class_base . ' ' . esc_attr( $link['class'] ) . '"' : '"';
				?>
				<a<?php echo $href . $id . $class . $title . $other; ?>>
					<span>
						<?php echo $label; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
		<a href="#" class="maera_pb-section-toggle" title="<?php esc_attr_e( 'Click to toggle', 'maera' ); ?>">
			<div class="handlediv"></div>
		</a>
	</div>
	<div class="clear"></div>
	<div class="maera_pb-section-body">
		<input type="hidden" value="<?php echo $maera_pb_section_data['section']['id']; ?>" name="<?php echo Maera_PB()->sections->get_section_name( $maera_pb_section_data, $maera_pb_is_js_template ); ?>[section-type]" />
