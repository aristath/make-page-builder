<?php
/**
 * @package Make
 */

/**
 * Defines the functionality for the HTML Builder.
 *
 * @since 1.0.0.
 */
class Make_PB_Base {
	/**
	 * The one instance of Make_PB_Base.
	 *
	 * @since 1.0.0.
	 *
	 * @var   Make_PB_Base
	 */
	private static $instance;

	/**
	 * Instantiate or return the one Make_PB_Base instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return Make_PB_Base
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initiate actions.
	 *
	 * @since  1.0.0.
	 *
	 * @return Make_PB_Base
	 */
	public function __construct() {

		// Add the core sections
		require Make_PB::path() . '/inc/builder/sections/section-definitions.php';

		// Include the save routines
		require Make_PB::path() . '/includes/save.php';

		// Include the front-end helpers
		require Make_PB::path() . '/inc/builder/sections/section-front-end-helpers.php';

		// Set up actions
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 1 ); // Bias toward top of stack

		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_footer', array( $this, 'print_templates' ) );


	}

	/**
	 * Add the meta box.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		$post_types = get_post_types( '', 'names' );
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'make_pb-builder',
				__( 'Page Builder', 'make' ),
				array( $this, 'display_builder' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Display the meta box.
	 *
	 * @since  1.0.0.
	 *
	 * @param  WP_Post    $post_local    The current post object.
	 * @return void
	 */
	public function display_builder( $post_local ) {
		wp_nonce_field( 'save', 'make_pb-builder-nonce' );

		// Get the current sections
		global $make_pb_sections;
		$make_pb_sections = get_post_meta( $post_local->ID, '_make_pb-sections', true );
		$make_pb_sections = ( is_array( $make_pb_sections ) ) ? $make_pb_sections : array();

		// Load the boilerplate templates
		Make_PB::get_template_part( 'inc/builder/core/templates/menu' );
		Make_PB::get_template_part( 'inc/builder/core/templates/stage', 'header' );

		$section_data        = Make_PB_Helper::get_section_data( $post_local->ID );
		$registered_sections = Make_PB()->sections->get_sections();

		// Print the current sections
		foreach ( $section_data as $section ) {
			/**
			 * In Make 1.4.0, the blank section was deprecated. Any existing blank sections are converted to 1 column,
			 * text sections.
			 */
			if ( isset( $section['section-type'] ) && 'blank' === $section['section-type'] && isset( $registered_sections['text'] ) ) {
				// Convert the data for the section
				$content = ( ! empty( $section['content'] ) ) ? $section['content'] : '';
				$title   = ( ! empty( $section['title'] ) ) ? $section['title'] : '';
				$label   = ( ! empty( $section['label'] ) ) ? $section['label'] : '';
				$state   = ( ! empty( $section['state'] ) ) ? $section['state'] : 'open';
				$id      = ( ! empty( $section['id'] ) ) ? $section['id'] : time();

				// Set the data
				$section = array(
					'id'             => $id,
					'state'          => $state,
					'section-type'   => 'text',
					'title'          => $title,
					'label'          => $label,
					'columns-number' => 1,
					'columns-order'  => array(
						0 => 1,
						1 => 2,
						2 => 3,
						3 => 4,
					),
					'columns'        => array(
						1 => array(
							'title'    => '',
							'image-id' => 0,
							'content'  => $content,
							''
						),
						2 => array(
							'title'    => '',
							'image-id' => 0,
							'content'  => '',
							''
						),
						3 => array(
							'title'    => '',
							'image-id' => 0,
							'content'  => '',
							''
						),
						4 => array(
							'title'    => '',
							'image-id' => 0,
							'content'  => '',
							''
						),
					)
				);
			}

			if ( isset( $registered_sections[ $section['section-type'] ]['display_template'] ) ) {
				// Print the saved section
				$this->load_section( $registered_sections[ $section['section-type'] ], $section );
			}
		}

		Make_PB::get_template_part( 'inc/builder/core/templates/stage', 'footer' );

		// Add the sort input
		$section_order = get_post_meta( $post_local->ID, '_make_pb-section-ids', true );
		$section_order = ( ! empty( $section_order ) ) ? implode( ',', $section_order ) : '';
		echo '<input type="hidden" value="' . esc_attr( $section_order ) . '" name="make_pb-section-order" id="make_pb-section-order" />';
	}

	/**
	 * Add a class to indicate the current template being used.
	 *
	 * @since  1.0.4.
	 *
	 * @param  array    $classes    The current classes.
	 * @return array                The modified classes.
	 */
	function admin_body_class( $classes ) {
		global $pagenow;

		if ( 'post-new.php' === $pagenow || ( 'post.php' === $pagenow && make_pb_is_builder_active() ) ) {
			$classes .= ' make_pb-builder-active';
			$classes .= ' make-plus-disabled';
		} else {
			$classes .= ' make_pb-default-active';
		}

		return $classes;
	}

	/**
	 * Reusable component for adding an image uploader.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $section_name    Name of the current section.
	 * @param  int       $image_id        ID of the current image.
	 * @param  string    $title           Title for the media modal.
	 * @return string                     Either return the string or echo it.
	 */
	public function add_uploader( $section_name, $image_id = 0, $title = '' ) {
		$image = Make_PB_Image::get_image_src( $image_id, 'large' );
		$title = ( ! empty( $title ) ) ? $title : __( 'Set image', 'make' );
		ob_start();
		?>
		<div class="make_pb-uploader<?php if ( ! empty( $image[0] ) ) : ?> make_pb-has-image-set<?php endif; ?>">
			<div data-title="<?php echo esc_attr( $title ); ?>" class="make_pb-media-uploader-placeholder make_pb-media-uploader-add"<?php if ( ! empty( $image[0] ) ) : ?> style="background-image: url(<?php echo addcslashes( esc_url_raw( $image[0] ), '"' ); ?>);"<?php endif; ?>></div>
			<input type="hidden" name="<?php echo esc_attr( $section_name ); ?>[image-id]" value="<?php echo make_pb_sanitize_image_id( $image_id ); ?>" class="make_pb-media-uploader-value" />
		</div>
	<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Create an iframe preview area that is connected to a TinyMCE modal window.
	 *
	 * @since  1.4.0.
	 *
	 * @param  string    $id               The unique ID to identify the different areas.
	 * @param  string    $textarea_name    The name of the textarea.
	 * @param  string    $content          The content for the text area.
	 * @param  bool      $iframe           Whether or not to add an iframe to preview content.
	 * @return void
	 */
	public function add_frame( $id, $textarea_name, $content = '', $iframe = true ) {
		global $make_pb_is_js_template;
		$iframe_id   = 'make_pb-iframe-' . $id;
		$textarea_id = 'make_pb-content-' . $id;
	?>
		<?php if ( true === $iframe ) : ?>
		<div class="make_pb-iframe-wrapper">
			<div class="make_pb-iframe-overlay">
				<a href="#" class="edit-content-link" data-textarea="<?php echo esc_attr( $textarea_id ); ?>" data-iframe="<?php echo esc_attr( $iframe_id ); ?>">
					<span class="screen-reader-text">
						<?php _e( 'Edit content', 'make' ); ?>
					</span>
				</a>
			</div>
			<iframe width="100%" height="300" id="<?php echo esc_attr( $iframe_id ); ?>" scrolling="no"></iframe>
		</div>
		<?php endif; ?>

		<textarea id="<?php echo esc_attr( $textarea_id ); ?>" name="<?php echo esc_attr( $textarea_name ); ?>" style="display:none;"><?php echo esc_textarea( $content ); ?></textarea>

		<?php if ( true !== $make_pb_is_js_template && true === $iframe ) : ?>
		<script type="text/javascript">
			var ttfMakeFrames = ttfMakeFrames || [];
			ttfMakeFrames.push('<?php echo esc_js( $id ); ?>');
		</script>
		<?php endif;
	}

	/**
	 * Load a section template with an available data payload for use in the template.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $section     The section data.
	 * @param  array     $data        The data payload to inject into the section.
	 * @return void
	 */
	public function load_section( $section, $data = array() ) {
		if ( ! isset( $section['id'] ) ) {
			return;
		}

		// Globalize the data to provide access within the template
		global $make_pb_section_data;
		$make_pb_section_data = array(
			'data'    => $data,
			'section' => $section,
		);

		// Include the template
		Make_PB()->sections->load_template(
			$make_pb_section_data['section']['builder_template'],
			$make_pb_section_data['section']['path']
		);

		// Destroy the variable as a good citizen does
		unset( $GLOBALS['make_pb_section_data'] );
	}

	/**
	 * Print out the JS section templates
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function print_templates() {
		global $hook_suffix, $typenow, $make_pb_is_js_template;
		$make_pb_is_js_template = true;

		// Print the templates
		foreach ( Make_PB()->sections->get_sections() as $section ) : ?>
			<script type="text/html" id="tmpl-make_pb-<?php echo esc_attr( $section['id'] ); ?>">
			<?php
			ob_start();
			$this->load_section( $section, array() );
			$html = ob_get_clean();
			echo $html;
			?>
		</script>
		<?php endforeach;

		unset( $GLOBALS['make_pb_is_js_template'] );

		// Load the overlay for TinyMCE
		Make_PB::get_template_part( '/inc/builder/core/templates/overlay', 'tinymce' );

		// Print the template for removing images
		?>
			<script type="text/html" id="tmpl-make_pb-remove-image">
				<div class="make_pb-remove-current-image">
					<h3><?php _e( 'Current image', 'make' ); ?></h3>
					<a href="#" class="make_pb-remove-image-from-modal">
						<?php _e( 'Remove Current Image', 'make' ); ?>
					</a>
				</div>
			</script>
		<?php
	}

	/**
	 * Wrapper function to produce a WP Editor with special defaults.
	 *
	 * @since  1.0.0.
	 * @deprecated  1.4.0.
	 *
	 * @param  string    $content     The content to display in the editor.
	 * @param  string    $name        Name of the editor.
	 * @param  array     $settings    Setting to send to the editor.
	 * @return void
	 */
	public function wp_editor( $content, $name, $settings = array() ) {
		_deprecated_function( __FUNCTION__, '1.4.0', 'wp_editor' );
		wp_editor( $content, $name, $settings );
	}

	/**
	 * Add the media buttons to the text editor.
	 *
	 * This is a copy and modification of the core "media_buttons" function. In order to make the media editor work
	 * better for smaller width screens, we need to wrap the button text in a span tag. By doing so, we can hide the
	 * text in some situations.
	 *
	 * @since  1.0.0.
	 * @deprecated  1.4.0.
	 *
	 * @param  string    $editor_id    The value of the current editor ID.
	 * @return void
	 */
	public function media_buttons( $editor_id = 'content' ) {
		_deprecated_function( __FUNCTION__, '1.4.0', 'media_buttons' );
		media_buttons( $editor_id );
	}

	/**
	 * Append the editor styles to the section editors.
	 *
	 * Unfortunately, the `wp_editor()` function does not support a "content_css" argument. As a result, the stylesheet
	 * for the "content_css" parameter needs to be added via a filter.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $mce_init     The array of tinyMCE settings.
	 * @param  string    $editor_id    The ID for the current editor.
	 * @return array                   The modified settings.
	 */
	function tiny_mce_before_init( $mce_init, $editor_id ) {
		_deprecated_function( __FUNCTION__, '1.4.0' );
		return $mce_init;
	}

	/**
	 * Retrieve all of the data for the sections.
	 *
	 * Note that in 1.2.0, this function was changed to call the global function. This global function was added to
	 * provide easier reuse of the function. In order to maintain backwards compatibility, this function is left in
	 * place.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $post_id    The post to retrieve the data from.
	 * @return array                 The combined data.
	 */
	public function get_section_data( $post_id ) {
		return Make_PB_Helper::get_section_data( $post_id );
	}

	/**
	 * Convert an array with array keys that map to a multidimensional array to the array.
	 *
	 * Note that in 1.2.0, this function was changed to call the global function. This global function was added to
	 * provide easier reuse of the function. In order to maintain backwards compatibility, this function is left in
	 * place.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $arr    The array to convert.
	 * @return array            The converted array.
	 */
	function create_array_from_meta_keys( $arr ) {
		return Make_PB_Helper::create_array_from_meta_keys( $arr );
	}

}

/**
 * Instantiate or return the one Make_PB_Base instance.
 *
 * @since  1.0.0.
 *
 * @return Make_PB_Base
 */
function make_pb_get_builder_base() {
	return Make_PB_Base::instance();
}


// Add the base immediately
if ( is_admin() ) {
	make_pb_get_builder_base();
}
