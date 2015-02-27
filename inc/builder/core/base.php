<?php
/**
 * @package Make
 */

if ( ! function_exists( 'Make_PB_Base' ) ) :
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
		// Include the API
		require Make_PB::path() . '/inc/builder/core/api.php';

		// Include the configuration helpers
		require Make_PB::path() . '/inc/builder/core/configuration-helpers.php';

		// Add the core sections
		require Make_PB::path() . '/inc/builder/sections/section-definitions.php';

		// Include the save routines
		require Make_PB::path() . '/inc/builder/core/save.php';

		// Include the front-end helpers
		require Make_PB::path() . '/inc/builder/sections/section-front-end-helpers.php';

		// Set up actions
		add_action( 'admin_init', array( $this, 'register_post_type_support_for_builder' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 1 ); // Bias toward top of stack
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 11 );
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_print_styles' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_footer', array( $this, 'print_templates' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'builder_toggle' ) );

	}

	/**
	 * Add support for post types to use the Make builder.
	 *
	 * @since  1.3.0.
	 *
	 * @return void
	 */
	public function register_post_type_support_for_builder() {
		add_post_type_support( 'page', 'make-builder' );
	}

	/**
	 * Add the meta box.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		foreach ( make_pb_get_post_types_supporting_builder() as $name ) {
			add_meta_box(
				'make_pb-builder',
				__( 'Page Builder', 'make' ),
				array( $this, 'display_builder' ),
				$name,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Display the checkbox to turn the builder on or off.
	 *
	 * @since  1.2.0.
	 *
	 * @return void
	 */
	public function builder_toggle() {
		// Do not show the toggle for pages as the builder is controlled by page templates
		if ( 'page' === get_post_type() ) {
			return;
		}

		$using_builder = get_post_meta( get_the_ID(), '_make_pb-use-builder', true );
	?>
		<div class="misc-pub-section">
			<input type="checkbox" value="1" name="use-builder" id="use-builder"<?php checked( $using_builder, 1 ); ?> />
			&nbsp;<label for="use-builder"><?php _e( 'Use Page Builder', 'make' ); ?></label>
		</div>
	<?php
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
		get_template_part( 'inc/builder/core/templates/menu' );
		get_template_part( 'inc/builder/core/templates/stage', 'header' );

		$section_data        = make_pb_get_section_data( $post_local->ID );
		$registered_sections = make_pb_get_sections();

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

		get_template_part( 'inc/builder/core/templates/stage', 'footer' );

		// Add the sort input
		$section_order = get_post_meta( $post_local->ID, '_make_pb-section-ids', true );
		$section_order = ( ! empty( $section_order ) ) ? implode( ',', $section_order ) : '';
		echo '<input type="hidden" value="' . esc_attr( $section_order ) . '" name="make_pb-section-order" id="make_pb-section-order" />';
	}

	/**
	 * Enqueue the JS and CSS for the admin.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $hook_suffix    The suffix for the screen.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		// Only load resources if they are needed on the current page
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		// Enqueue the CSS
		wp_enqueue_style(
			'make_pb-builder',
			Make_PB::uri() . '/inc/builder/core/css/builder.css',
			array(),
			Make_PB::version()
		);

		wp_enqueue_style( 'wp-color-picker' );

		// Dependencies regardless of min/full scripts
		$dependencies = array(
			'wplink',
			'utils',
			'wp-color-picker',
			'jquery-effects-core',
			'jquery-ui-sortable',
			'backbone',
		);

		wp_register_script(
			'make_pb-builder/js/models/section.js',
			Make_PB::uri() . '/inc/builder/core/js/models/section.js',
			array(),
			Make_PB::version(),
			true
		);

		wp_register_script(
			'make_pb-builder/js/collections/sections.js',
			Make_PB::uri() . '/inc/builder/core/js/collections/sections.js',
			array(),
			Make_PB::version(),
			true
		);

		wp_register_script(
			'make_pb-builder/js/views/menu.js',
			Make_PB::uri() . '/inc/builder/core/js/views/menu.js',
			array(),
			Make_PB::version(),
			true
		);

		wp_register_script(
			'make_pb-builder/js/views/section.js',
			Make_PB::uri() . '/inc/builder/core/js/views/section.js',
			array(),
			Make_PB::version(),
			true
		);

		wp_register_script(
			'make_pb-builder/js/views/overlay.js',
			Make_PB::uri() . '/inc/builder/core/js/views/overlay.js',
			array(),
			Make_PB::version(),
			true
		);

		/**
		 * Filter the dependencies for the Make builder JS.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $dependencies    The list of dependencies.
		 */
		$dependencies = apply_filters(
			'make_pb_builder_js_dependencies',
			array_merge(
				$dependencies,
				array(
					'make_pb-builder/js/models/section.js',
					'make_pb-builder/js/collections/sections.js',
					'make_pb-builder/js/views/menu.js',
					'make_pb-builder/js/views/section.js',
					'make_pb-builder/js/views/overlay.js',
				)
			)
		);

		wp_enqueue_script(
			'make_pb-builder',
			Make_PB::uri() . '/inc/builder/core/js/app.js',
			$dependencies,
			Make_PB::version(),
			true
		);

		// Add data needed for the JS
		$data = array(
			'pageID'        => get_the_ID(),
			'postRefresh'   => true,
			'confirmString' => __( 'Delete the section?', 'make' ),
		);

		wp_localize_script(
			'make_pb-builder',
			'make_pbBuilderData',
			$data
		);
	}

	/**
	 * Print additional, dynamic CSS for the builder interface.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function admin_print_styles() {
		global $pagenow;

	?>
		<style type="text/css">
			<?php if ( 'post-new.php' === $pagenow || ( 'post.php' === $pagenow && make_pb_is_builder_page() ) ) : ?>
			#postdivrich {
				display: none;
			}
			<?php else : ?>
			#make_pb-builder {
				display: none;
			}
			.make_pb-duplicator {
				display: none;
			}
			<?php endif; ?>

			<?php foreach ( make_pb_get_sections() as $key => $section ) : ?>
			#make_pb-menu-list-item-link-<?php echo esc_attr( $section['id'] ); ?> .make_pb-menu-list-item-link-icon-wrapper {
				background-image: url(<?php echo addcslashes( esc_url_raw( $section['icon'] ), '"' ); ?>);
			}
			<?php endforeach; ?>
		</style>
	<?php
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

		if ( 'post-new.php' === $pagenow || ( 'post.php' === $pagenow && make_pb_is_builder_page() ) ) {
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
		$image = make_pb_get_image_src( $image_id, 'large' );
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
		make_pb_load_section_template(
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
		foreach ( make_pb_get_sections() as $section ) : ?>
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
		get_template_part( '/inc/builder/core/templates/overlay', 'tinymce' );

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
		return make_pb_get_section_data( $post_id );
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
		return make_pb_create_array_from_meta_keys( $arr );
	}

}
endif;

if ( ! function_exists( 'make_pb_get_builder_base' ) ) :
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
endif;

// Add the base immediately
if ( is_admin() ) {
	make_pb_get_builder_base();
}

if ( ! function_exists( 'make_pb_get_post_types_supporting_builder' ) ) :
/**
 * Get all post types that support the Make builder.
 *
 * @since  1.2.0.
 *
 * @return array    Array of all post types that support the builder.
 */
function make_pb_get_post_types_supporting_builder() {
	$post_types_supporting_builder = array();

	// Inspect each post type for builder support
	foreach ( get_post_types() as $name => $data ) {
		if ( post_type_supports( $name, 'make-builder' ) ) {
			$post_types_supporting_builder[] = $name;
		}
	}

	return $post_types_supporting_builder;
}
endif;

if ( ! function_exists( 'make_pb_will_be_builder_page' ) ):
/**
 * Determines if a page in the process of being saved will use the builder template.
 *
 * @since  1.2.0.
 *
 * @return bool    True if the builder template will be used; false if it will not.
 */
function make_pb_will_be_builder_page() {
	$template    = isset( $_POST[ 'page_template' ] ) ? $_POST[ 'page_template' ] : '';
	$use_builder = isset( $_POST['use-builder'] ) ? (int) isset( $_POST['use-builder'] ) : 0;

	/**
	 * Allow developers to dynamically change the builder page status.
	 *
	 * @since 1.2.3.
	 *
	 * @param bool      $will_be_builder_page    Whether or not this page will be a builder page.
	 * @param string    $template                The template name.
	 * @param int       $use_builder             Value of the "use-builder" input. 1 === use builder. 0 === do not use builder.
	 */
	return apply_filters( 'make_will_be_builder_page', ( 'template-builder.php' === $template || 1 === $use_builder ), $template, $use_builder );
}
endif;

if ( ! function_exists( 'make_pb_load_section_header' ) ) :
/**
 * Load a consistent header for sections.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function make_pb_load_section_header() {
	global $make_pb_section_data;
	get_template_part( 'inc/builder/core/templates/section', 'header' );

	/**
	 * Allow for script execution in the header of a builder section.
	 *
	 * This action is a variable action that allows a developer to hook into specific section types (e.g., 'text'). Do
	 * not confuse "id" in this context as the individual section id (e.g., 14092814910).
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $make_pb_section_data    The array of data for the section.
	 */
	do_action( 'make_section_' . $make_pb_section_data['section']['id'] . '_before', $make_pb_section_data );

	// Backcompat
	do_action( 'make_pb_section_' . $make_pb_section_data['section']['id'] . '_before', $make_pb_section_data );
}
endif;

if ( ! function_exists( 'make_pb_load_section_footer' ) ) :
/**
 * Load a consistent footer for sections.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function make_pb_load_section_footer() {
	global $make_pb_section_data;
	get_template_part( 'inc/builder/core/templates/section', 'footer' );

	/**
	 * Allow for script execution in the footer of a builder section.
	 *
	 * This action is a variable action that allows a developer to hook into specific section types (e.g., 'text'). Do
	 * not confuse "id" in this context as the individual section id (e.g., 14092814910).
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $make_pb_section_data    The array of data for the section.
	 */
	do_action( 'make_section_' . $make_pb_section_data['section']['id'] . '_after', $make_pb_section_data );

	// Backcompat
	do_action( 'make_pb_section_' . $make_pb_section_data['section']['id'] . '_after', $make_pb_section_data );
}
endif;

if ( ! function_exists( 'make_pb_load_section_template' ) ) :
/**
 * Load a section front- or back-end section template. Searches for child theme versions
 * first, then parent themes, then plugins.
 *
 * @since  1.0.4.
 *
 * @param  string    $slug    The relative path and filename (w/out suffix) required to substitute the template in a child theme.
 * @param  string    $path    An optional path extension to point to the template in the parent theme or a plugin.
 * @return string             The template filename if one is located.
 */
function make_pb_load_section_template( $slug, $path ) {
	$templates = array(
		$slug . '.php',
		trailingslashit( $path ) . $slug . '.php'
	);

	/**
	 * Filter the templates to try and load.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $templates    The list of template to try and load.
	 * @param string   $slug         The template slug.
	 * @param string   $path         The path to the template.
	 */
	$templates = apply_filters( 'make_load_section_template', $templates, $slug, $path );

	if ( '' === $located = locate_template( $templates, true, false ) ) {
		if ( isset( $templates[1] ) && file_exists( $templates[1] ) ) {
			require( $templates[1] );
			$located = $templates[1];
		}
	}

	return $located;
}
endif;

if ( ! function_exists( 'make_pb_get_wp_editor_id' ) ) :
/**
 * Generate the ID for a WP editor based on an existing or future section number.
 *
 * @since  1.0.0.
 *
 * @param  array     $data              The data for the section.
 * @param  array     $is_js_template    Whether a JS template is being printed or not.
 * @return string                       The editor ID.
 */
function make_pb_get_wp_editor_id( $data, $is_js_template ) {
	$id_base = 'make_pbeditor' . $data['section']['id'];

	if ( $is_js_template ) {
		$id = $id_base . 'temp';
	} else {
		$id = $id_base . $data['data']['id'];
	}

	/**
	 * Alter the wp_editor ID.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $id                The ID for the editor.
	 * @param array     $data              The section data.
	 * @param bool      $is_js_template    Whether or not this is in the context of a JS template.
	 */
	return apply_filters( 'make_get_wp_editor_id', $id, $data, $is_js_template );
}
endif;

if ( ! function_exists( 'make_pb_get_section_name' ) ) :
/**
 * Generate the name of a section.
 *
 * @since  1.0.0.
 *
 * @param  array     $data              The data for the section.
 * @param  array     $is_js_template    Whether a JS template is being printed or not.
 * @return string                       The name of the section.
 */
function make_pb_get_section_name( $data, $is_js_template ) {
	$name = 'make_pb-section';

	if ( $is_js_template ) {
		$name .= '[{{{ id }}}]';
	} else {
		$name .= '[' . $data['data']['id'] . ']';
	}

	/**
	 * Alter section name.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $name              The name of the section.
	 * @param array     $data              The section data.
	 * @param bool      $is_js_template    Whether or not this is in the context of a JS template.
	 */
	return apply_filters( 'make_get_section_name', $name, $data, $is_js_template );
}
endif;

if ( ! function_exists( 'make_pb_get_image' ) ) :
/**
 * Get an image to display in page builder backend or front end template.
 *
 * This function allows image IDs defined with a negative number to surface placeholder images. This allows templates to
 * approximate real content without needing to add images to the user's media library.
 *
 * @since  1.0.4.
 *
 * @param  int       $image_id    The attachment ID. Dimension value IDs represent placeholders (100x150).
 * @param  string    $size        The image size.
 * @return string                 HTML for the image. Empty string if image cannot be produced.
 */
function make_pb_get_image( $image_id, $size ) {
	$return = '';

	if ( false === strpos( $image_id, 'x' ) ) {
		$return = wp_get_attachment_image( $image_id, $size );
	} else {
		$image = make_pb_get_placeholder_image( $image_id );

		if ( ! empty( $image ) && isset( $image['src'] ) && isset( $image['alt'] ) && isset( $image['class'] ) && isset( $image['height'] ) && isset( $image['width'] ) ) {
			$return = '<img src="' . $image['src'] . '" alt="' . $image['alt'] . '" class="' . $image['class'] . '" height="' . $image['height'] . '" width="' . $image['width'] . '" />';
		}
	}

	/**
	 * Filter the image HTML.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $return      The image HTML.
	 * @param int       $image_id    The ID for the image.
	 * @param bool      $size        The requested image size.
	 */
	return apply_filters( 'make_get_image', $return, $image_id, $size );
}
endif;

if ( ! function_exists( 'make_pb_get_image_src' ) ) :
/**
 * Get an image's src.
 *
 * @since  1.0.4.
 *
 * @param  int       $image_id    The attachment ID. Dimension value IDs represent placeholders (100x150).
 * @param  string    $size        The image size.
 * @return string                 URL for the image.
 */
function make_pb_get_image_src( $image_id, $size ) {
	$src = '';

	if ( false === strpos( $image_id, 'x' ) ) {
		$image = wp_get_attachment_image_src( $image_id, $size );

		if ( false !== $image && isset( $image[0] ) ) {
			$src = $image;
		}
	} else {
		$image = make_pb_get_placeholder_image( $image_id );

		if ( isset( $image['src'] ) ) {
			$wp_src = array(
				0 => $image['src'],
				1 => $image['width'],
				2 => $image['height'],
			);
			$src = array_merge( $image, $wp_src );
		}
	}

	/**
	 * Filter the image source attributes.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $src         The image source attributes.
	 * @param int       $image_id    The ID for the image.
	 * @param bool      $size        The requested image size.
	 */
	return apply_filters( 'make_get_image_src', $src, $image_id, $size );
}
endif;

global $make_pb_placeholder_images;

if ( ! function_exists( 'make_pb_get_placeholder_image' ) ) :
/**
 * Gets the specified placeholder image.
 *
 * @since  1.0.4.
 *
 * @param  int      $image_id    Image ID. Should be a dimension value (100x150).
 * @return array                 The image data, including 'src', 'alt', 'class', 'height', and 'width'.
 */
function make_pb_get_placeholder_image( $image_id ) {
	global $make_pb_placeholder_images;
	$return = array();

	if ( isset( $make_pb_placeholder_images[ $image_id ] ) ) {
		$return = $make_pb_placeholder_images[ $image_id ];
	}

	/**
	 * Filter the image source attributes.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $return                        The image source attributes.
	 * @param int       $image_id                      The ID for the image.
	 * @param bool      $make_pb_placeholder_images    The list of placeholder images.
	 */
	return apply_filters( 'make_get_placeholder_image', $return, $image_id, $make_pb_placeholder_images );
}
endif;

if ( ! function_exists( 'make_pb_register_placeholder_image' ) ) :
/**
 * Add a new placeholder image.
 *
 * @since  1.0.4.
 *
 * @param  int      $id      The ID for the image. Should be a dimension value (100x150).
 * @param  array    $data    The image data, including 'src', 'alt', 'class', 'height', and 'width'.
 * @return void
 */
function make_pb_register_placeholder_image( $id, $data ) {
	global $make_pb_placeholder_images;
	$make_pb_placeholder_images[ $id ] = $data;
}
endif;
