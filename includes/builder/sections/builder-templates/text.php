<?php
/**
 * @package Make
 */

Make_PB()->sections->load_header();

global $make_pb_section_data, $make_pb_is_js_template;
$section_id     = ( isset( $make_pb_section_data['data']['id'] ) ) ? $make_pb_section_data['data']['id'] : '{{{ id }}}';
$section_name   = Make_PB()->sections->get_section_name( $make_pb_section_data, $make_pb_is_js_template );
$columns_number = ( isset( $make_pb_section_data['data']['columns-number'] ) ) ? $make_pb_section_data['data']['columns-number'] : 3;
$section_order  = ( ! empty( $make_pb_section_data['data']['columns-order'] ) ) ? $make_pb_section_data['data']['columns-order'] : range(1, 4);
$columns_class  = ( in_array( $columns_number, range( 1, 4 ) ) && true !== $make_pb_is_js_template ) ? $columns_number : 3;

/**
 * Execute code before the columns select input is displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $make_pb_section_data    The data for the section.
 */
do_action( 'make_section_text_before_columns_select', $make_pb_section_data );

/**
 * Execute code after the columns select input is displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $make_pb_section_data    The data for the section.
 */
do_action( 'make_section_text_after_columns_select', $make_pb_section_data );

/**
 * Execute code after the section title is displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $make_pb_section_data    The data for the section.
 */
do_action( 'make_section_text_after_title', $make_pb_section_data ); ?>

<div class="make_pb-text-columns-stage make_pb-text-columns-<?php echo $columns_class; ?>">
	<?php $j = 1; foreach ( $section_order as $key => $i ) : ?>
	<?php
		$column_name = $section_name . '[columns][' . $i . ']';
		$iframe_id   = 'make_pb-iframe-' . $section_id . '-' . $i;
		$textarea_id = 'make_pb-content-' . $section_id . '-' . $i;
		$overlay_id  = 'make_pb-overlay-' . $section_id . '-' . $i;
		$link        = ( isset( $make_pb_section_data['data']['columns'][ $i ]['image-link'] ) ) ? $make_pb_section_data['data']['columns'][ $i ]['image-link'] : '';
		$image_id    = ( isset( $make_pb_section_data['data']['columns'][ $i ]['image-id'] ) ) ? $make_pb_section_data['data']['columns'][ $i ]['image-id'] : 0;
		$title       = ( isset( $make_pb_section_data['data']['columns'][ $i ]['title'] ) ) ? $make_pb_section_data['data']['columns'][ $i ]['title'] : '';
		$content     = ( isset( $make_pb_section_data['data']['columns'][ $i ]['content'] ) ) ? $make_pb_section_data['data']['columns'][ $i ]['content'] : '';

		$item_has_content = ( ! empty( $content ) ) ? ' item-has-content' : '';

		$column_buttons = array(
			100 => array(
				'label'              => __( 'Configure column', 'make' ),
				'href'               => '#',
				'class'              => 'configure-column-link make_pb-overlay-open',
				'title'              => __( 'Configure column', 'make' ),
				'other-a-attributes' => ' data-overlay="#' . $overlay_id .'"',
			),
			200 => array(
				'label'              => __( 'Edit text column', 'make' ),
				'href'               => '#',
				'class'              => 'edit-content-link edit-text-column-link' . $item_has_content,
				'title'              => __( 'Edit content', 'make' ),
				'other-a-attributes' => 'data-textarea="' . esc_attr( $textarea_id ) . '" data-iframe="' . esc_attr( $iframe_id ) . '"',
			),
		);

		/**
		 * Filter the buttons added to a text column.
		 *
		 * @since 1.4.0.
		 *
		 * @param array    $column_buttons          The current list of buttons.
		 * @param array    $make_pb_section_data    All data for the section.
		 */
		$column_buttons = apply_filters( 'make_column_buttons', $column_buttons, $make_pb_section_data );
		ksort( $column_buttons );

		/**
		 * Filter the classes applied to each column in a Columns section.
		 *
		 * @since 1.2.0.
		 *
		 * @param string    $column_classes          The classes for the column.
		 * @param int       $i                       The column number.
		 * @param array     $make_pb_section_data    The array of data for the section.
		 */
		$column_classes = apply_filters( 'make_pb-text-column-classes', 'make_pb-text-column make_pb-text-column-position-' . $j, $i, $make_pb_section_data );
	?>
	<div class="<?php echo esc_attr( $column_classes ); ?>" data-id="<?php echo $i; ?>">
		<div title="<?php esc_attr_e( 'Drag-and-drop this column into place', 'make' ); ?>" class="make_pb-sortable-handle">
			<div class="sortable-background column-sortable-background"></div>
		</div>

		<?php
		/**
		 * Execute code before an individual text column is displayed.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $make_pb_section_data    The data for the section.
		 */
		do_action( 'make_section_text_before_column', $make_pb_section_data, $i );
		?>

		<?php foreach ( $column_buttons as $button ) : ?>
		<a href="<?php echo esc_url( $button['href'] ); ?>" class="column-buttons <?php echo esc_attr( $button['class'] ); ?>" title="<?php echo esc_attr( $button['title'] ); ?>" <?php if ( ! empty( $button['other-a-attributes'] ) ) echo $button['other-a-attributes']; ?>>
			<span>
				<?php echo esc_html( $button['label'] ); ?>
			</span>
		</a>
		<?php endforeach; ?>

		<?php echo make_pb_get_builder_base()->add_uploader( $column_name, make_pb_sanitize_image_id( $image_id ), __( 'Set image', 'make' ) ); ?>
		<?php make_pb_get_builder_base()->add_frame( $section_id . '-' . $i, $column_name . '[content]', $content ); ?>

		<?php
		/**
		 * Execute code after an individual text column is displayed.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $make_pb_section_data    The data for the section.
		 */
		do_action( 'make_section_text_after_column', $make_pb_section_data, $i );
		?>

		<?php
		global $make_pb_overlay_class, $make_pb_overlay_id, $make_pb_overlay_title;
		$make_pb_overlay_class = 'make_pb-configuration-overlay';
		$make_pb_overlay_id    = $overlay_id;
		$make_pb_overlay_title = __( 'Configure column', 'make' );

		Make_PB::get_template_part( '/includes/builder/core/templates/overlay', 'header' );

		/**
		 * Filter the definitions of the Columns section's column configuration inputs.
		 *
		 * @since 1.4.0.
		 *
		 * @param array    $inputs    The input definition array.
		 */
		$inputs = apply_filters( 'make_column_configuration', array(
			100 => array(
				'type'    => 'section_title',
				'name'    => 'title',
				'label'   => __( 'Enter column title', 'make' ),
				'default' => '',
				'class'   => 'make_pb-configuration-title',
			),
			200 => array(
				'type'    => 'text',
				'name'    => 'image-link',
				'label'   => __( 'Image link URL', 'make' ),
				'default' => '',
			),
		) );

		// Sort the config in case 3rd party code added another input
		ksort( $inputs, SORT_NUMERIC );

		// Print the inputs
		$output = '';

		foreach ( $inputs as $input ) {
			if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
				$section_data  = ( isset( $make_pb_section_data['data']['columns'][ $i ] ) ) ? $make_pb_section_data['data']['columns'][ $i ] : array();
				$output       .= Make_PB_Config::create_input( $column_name, $input, $section_data );
			}
		}

		echo $output;

		Make_PB::get_template_part( '/includes/builder/core/templates/overlay', 'footer' );
		?>
	</div>
	<?php $j++; endforeach; ?>
</div>

<?php
/**
 * Execute code after all columns are displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $make_pb_section_data    The data for the section.
 */
do_action( 'make_section_text_after_columns', $make_pb_section_data );
?>

<div class="clear"></div>

<input type="hidden" value="<?php echo esc_attr( implode( ',', $section_order ) ); ?>" name="<?php echo $section_name; ?>[columns-order]" class="make_pb-text-columns-order" />
<input type="hidden" class="make_pb-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $make_pb_section_data['data']['state'] ) ) echo esc_attr( $make_pb_section_data['data']['state'] ); else echo 'open'; ?>" />
<?php Make_PB()->sections->load_footer();
