/* global jQuery, _, make_pbFormatBuilder */
var make_pbFormatBuilder = make_pbFormatBuilder || {};

( function( $ ) {
	var formatWindow, formatInsert, formatRemove;

	/**
	 * The Format Builder object
	 *
	 * This holds all the functionality of the format builder except the bits that
	 * explicitly hook into TinyMCE. Those are found in plugin.js.
	 *
	 * @since 1.4.1.
	 */
	make_pbFormatBuilder = {
		/**
		 * Stores the activeEditor instance for usage within the class.
		 *
		 * @since 1.4.1.
		 */
		editor: {},

		/**
		 * Stores the parameters for each format to be registered with the TinyMCE Formatter.
		 *
		 * @since 1.4.1.
		 */
		definitions: {},

		/**
		 * Stores the selectors that identify the HTML wrappers for each format
		 * and associates them with format models.
		 *
		 * @since 1.4.1.
		 */
		nodes: {},

		/**
		 * Stores the items that appear in the Format Builder listbox.
		 *
		 * @since 1.4.1.
		 */
		choices: {},

		/**
		 * Stores the models for each available format.
		 *
		 * @since 1.4.1.
		 */
		formats: {},

		/**
		 * The current format model when the Format Builder window is open.
		 *
		 * @since 1.4.1.
		 */
		currentFormat: {},

		/**
		 * Data associated with the current position/selection of the cursor
		 * in the TinyMCE editor.
		 *
		 * @since 1.4.1.
		 */
		currentSelection: {},

		/**
		 * Opens the TinyMCE modal window, and initializes all of the Format Builder
		 * functionality.
		 *
		 * @since 1.4.1.
		 *
		 * @param editor
		 */
		open: function( editor ) {
			this.editor = editor;
			this.currentSelection = editor.selection;

			var format = this.parseNode( this.currentSelection.getNode() ),
				items = [],
				args;

			if ('' == format) {
				// No existing format. Show listbox to choose a new format.
				items = [
					{
						type: 'form',
						name: 'listboxForm',
						items: make_pbFormatBuilder.getFormatListBox()
					}
				];
			} else if ('undefined' !== typeof make_pbFormatBuilder.formats[format]) {
				// Cursor is on an existing format. Only show the option form for that particular format.
				make_pbFormatBuilder.currentFormat = new make_pbFormatBuilder.formats[format]({ update: true });
				items = [
					{
						type: 'form',
						name: 'optionsForm',
						items: make_pbFormatBuilder.currentFormat.getOptionFields()
					}
				];
			}

			// Define the window args.
			args = {
				title: 'Format Builder',
				id: 'make_pb-format-builder',
				autoScroll: true,
				maxHeight: 500,
				items: {
					type: 'container',
					name: 'formatContainer',
					layout: 'flex',
					align: 'stretch',
					direction: 'column',
					items: items
				},
				buttons: [
					make_pbFormatBuilder.getRemoveButton(),
					make_pbFormatBuilder.getInsertButton()
				],
				onclose: function() {
					// Reset the dynamic stylesheet.
					make_pbFormatBuilder.editor.execCommand('Make_Reset_Dynamic_Stylesheet');

					// Clear the current* objects so there are no collisions when the Format Builder
					// is opened again.
					make_pbFormatBuilder.editor = {};
					make_pbFormatBuilder.currentFormat = {};
					make_pbFormatBuilder.currentSelection = {};
				}
			};

			// Open the window.
			formatWindow = editor.windowManager.open(args);
		},

		/**
		 * Check to see if the cursor is currently on an existing format.
		 *
		 * @since 1.4.1.
		 *
		 * @param editor
		 * @param node
		 * @returns string
		 */
		parseNode: function( node ) {
			var format = '';

			$.each(this.nodes, function( fmt, selector ) {
				var match = make_pbFormatBuilder.editor.dom.getParents( node, selector );
				if ( match.length > 0 ) {
					format = fmt;
					return false;
				}
			});

			return format;
		},

		/**
		 * Search through the parents of the current node (plus the current node itself)
		 * for one that matches the given selector.
		 *
		 * @since 1.4.1.
		 *
		 * @param selector
		 * @param node
		 * @param editor
		 * @returns {*}
		 */
		getParentNode: function(selector, node, editor) {
			var Node = node || make_pbFormatBuilder.currentSelection.getNode(),
				Editor = editor || make_pbFormatBuilder.editor,
				matchedParents = Editor.dom.getParents(Node, selector);

			if (matchedParents.length > 0) {
				return matchedParents.shift();
			} else {
				return false;
			}
		},

		/**
		 * Get the JSON definition for the format chooser listbox.
		 *
		 * @since 1.4.1.
		 *
		 * @returns object
		 */
		getFormatListBox: function() {
			var listbox = {
				type: 'listbox',
				name: 'format',
				id: 'make_pb-format-builder-picker',
				minWidth: 300,
				values: this.getFormatChoices(),
				onselect: function() {
					var choice = this.value(),
						fields = {
							type: 'form',
							name: 'optionsForm'
						},
						maxHeight = 500,
						winWidth, winHeight, viewWidth, viewHeight, deltaW, deltaH;

					// Only proceed if the chosen format has a model.
					if ('undefined' !== typeof make_pbFormatBuilder.formats[choice]) {
						make_pbFormatBuilder.currentFormat = new make_pbFormatBuilder.formats[choice];

						// Generate the options fields
						fields.items = make_pbFormatBuilder.currentFormat.getOptionFields();

						// Remove previous option forms.
						formatWindow.find('#optionsForm').remove();

						// Add the new option form.
						formatWindow.find('#formatContainer')[0].append(fields).reflow();

						// Show the Insert button.
						// (The Remove button is unnecessary for new formats)
						formatInsert.visible( true );

						// Resize the window (automatically repaints as well)
						formatWindow.resizeToContent();
						winWidth = formatWindow.layoutRect().w;
						winHeight = formatWindow.layoutRect().h;
						viewWidth = make_pbFormatBuilder.editor.dom.getViewPort().w;
						viewHeight = make_pbFormatBuilder.editor.dom.getViewPort().h;
						if (winHeight > maxHeight) {
							formatWindow.resizeTo(winWidth, maxHeight);
							winHeight = formatWindow.layoutRect().h;
						}
						deltaW = (viewWidth - winWidth) / 2;
						deltaH = (viewHeight - winHeight) / 2;
						formatWindow.moveTo(deltaW, deltaH);

						// Repaint
						//formatWindow.repaint();
					}
				}
			};

			return listbox;
		},

		/**
		 * Get the list of available formats for use in the format chooser listbox.
		 *
		 * @since 1.4.1.
		 *
		 * @returns array
		 */
		getFormatChoices: function() {
			var choices = [
				{
					value: '',
					text: 'Choose a format',
					disabled: true,
					classes: 'listbox-placeholder'
				}
			];

			$.each( this.choices, function( fmt, f ) {
				choices.push( f() );
			} );

			return choices;
		},

		/**
		 * Get the Insert button for the modal window.
		 *
		 * @since 1.4.1.
		 *
		 * @returns object
		 */
		getInsertButton: function() {
			var button = {
				text: ( 'undefined' === typeof make_pbFormatBuilder.currentFormat.get ) ? 'Insert' : 'Update',
				id: 'make_pb-format-builder-insert',
				name: 'formatSubmit',
				classes: 'button-primary',
				hidden: ( 'undefined' === typeof make_pbFormatBuilder.currentFormat.get ),
				onPostRender: function() {
					// Store this control so it can be accessed later.
					formatInsert = this;
				},
				onclick: function() {
					// Get the current data from the options form.
					var data = formatWindow.find( '#optionsForm' )[0].toJSON(),
						html;

					// Feed the current data into the model and sanitize it.
					make_pbFormatBuilder.currentFormat.sanitizeOptions( data );

					// Insert the HTML into the editor and close the modal.
					make_pbFormatBuilder.currentFormat.insert();
					formatWindow.fire( 'submit' );
				}
			};

			return button;
		},

		/**
		 * Get the Remove button for the modal window.
		 *
		 * @since 1.4.1.
		 *
		 * @returns object
		 */
		getRemoveButton: function() {
			var button = {
				text: 'Remove',
				id: 'make_pb-format-builder-remove',
				name: 'formatRemove',
				classes: 'button-secondary',
				hidden: ( 'undefined' === typeof make_pbFormatBuilder.currentFormat.get || true !== make_pbFormatBuilder.currentFormat.get( 'update' ) ),
				onPostRender: function() {
					// Store this control so it can be accessed later.
					formatRemove = this;
				},
				onclick: function() {
					make_pbFormatBuilder.currentFormat.remove();
					formatWindow.fire( 'submit' );
				}
			};

			return button;
		},

		/**
		 * Generate the definitions for a control group that picks and sets a color.
		 *
		 * @since 1.4.1.
		 *
		 * @param name
		 * @param label
		 * @returns object
		 */
		getColorButton: function( name, label ) {
			var model = make_pbFormatBuilder.currentFormat,
				button = {
					type: 'container',
					label: label,
					items: [
						{
							type: 'button',
							name: name + 'Button',
							border: '1 1 1 1',
							style: 'background-color: ' + model.escape( name ) + '; border-color: #e5e5e5; box-shadow: none; width: 28px;',
							onclick: function() {
								var self = this, // Store the button for later access.
									ctrl = this.next(); // Get the hidden text field with the hex code.

								// Open the TinyMCE color picker plugin
								make_pbFormatBuilder.editor.settings.color_picker_callback( function( value ) {
									value = _.escape(value);
									self.getEl().style.backgroundColor = value;
									ctrl.value( value );
								}, ctrl.value() );
							}
						},
						{
							type: 'textbox',
							name: name,
							hidden: true,
							value: model.escape( name )
						}
					]
				};

			return button;
		},

		/**
		 * Generate the definitions for a control group that picks and sets an icon.
		 *
		 * @since 1.4.1.
		 *
		 * @param name
		 * @param label
		 * @returns object
		 */
		getIconButton: function( name, label ) {
			var model = make_pbFormatBuilder.currentFormat,
				noIcon = '<span class="mce-add-icon">Add icon</span>',
				yesIcon = '<div class="mce-icon-choice"><i class="fa"></i></div>',
				button = {
					type: 'container',
					label: label,
					items: [
						{
							type: 'container',
							minWidth: 36,
							minHeight: 36,
							html: '',
							onPostRender: function() {
								var ctrl = this.next(); // Get the hidden text field with the icon code.

								if ( ctrl.value() ) {
									// Show the existing icon, if one is set.
									$(this.getEl()).html(yesIcon).find('i').addClass(ctrl.value());
								} else {
									// Show the "Add icon" pseudo link.
									$(this.getEl()).html(noIcon);
								}
							},
							onclick: function() {
								var self = this, // Store the button for later access.
									ctrl = this.next(); // Get the hidden text field with the icon code.

								window.make_pbIconPicker.open(make_pbFormatBuilder.editor, function(value) {
									if ('' !== value) {
										// Show the chosen icon.
										$(self.getEl()).html(yesIcon).find('i').addClass(value);
									} else {
										// Show the "Add icon" pseudo link.
										$(self.getEl()).html(noIcon);
									}

									// Set the value of the hidden text field.
									ctrl.value(value);
								}, ctrl.value());
							}
						},
						{
							type: 'textbox',
							name: name,
							hidden: true,
							value: model.escape( name )
						}
					]
				};

			return button;
		}
	};
})( jQuery );