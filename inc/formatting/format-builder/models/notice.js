/* global Backbone, jQuery, _, make_pbFormatBuilder, make_pbFormatBuilderVars */
var make_pbFormatBuilder = make_pbFormatBuilder || {};

( function ( window, Backbone, $, _, make_pbFormatBuilder, make_pbFormatBuilderVars ) {
	'use strict';

	/**
	 * Defines the format parameters to register with the TinyMCE Formatter.
	 *
	 * @since 1.4.1.
	 */
	make_pbFormatBuilder.definitions.notice = {
		block: 'div',
		classes: 'make_pb-notice',
		wrapper: true
	};

	/**
	 * Define the selector for detecting this format in existing content.
	 *
	 * @since 1.4.1.
	 */
	make_pbFormatBuilder.nodes.notice = 'div.make_pb-notice';

	/**
	 * Defines the listbox item in the 'Choose a format' dropdown.
	 *
	 * @since 1.4.1.
	 *
	 * @returns object
	 */
	make_pbFormatBuilder.choices.notice = function() {
		var content = make_pbFormatBuilder.currentSelection.getContent(),
			parent = make_pbFormatBuilder.getParentNode('p'),
			choice, isP;

		// Determine if the current node or a parent is a <p> tag.
		isP = ($(parent).is('p'));

		choice = {
			value: 'notice',
			text: 'Notice',
			disabled: (false === isP && '' == content)
		};

		return choice;
	};

	/**
	 * The Button format model.
	 *
	 * @since 1.4.1.
	 */
	make_pbFormatBuilder.formats = make_pbFormatBuilder.formats || {};
	make_pbFormatBuilder.formats.notice = make_pbFormatBuilder.FormatModel.extend({
		/**
		 * Default format option values.
		 *
		 * @since 1.4.1.
		 */
		defaults: {
			update: false,
			id: 0,
			text: '',
			fontSize: make_pbFormatBuilderVars.userSettings.fontSizeBody,
			icon: '',
			iconSize: (parseInt(make_pbFormatBuilderVars.userSettings.fontSizeBody) * 2) + '',
			colorIcon: '#808080',
			iconPosition: 'left',
			paddingHorz: '20',
			paddingVert: '10',
			borderWidth: '2',
			borderStyle: 'solid',
			colorBorder: '#808080',
			colorBackground: '#e5e5e5',
			colorText: '#808080'
		},

		/**
		 * Populate the options with any existing values.
		 *
		 * @since 1.4.1.
		 */
		initialize: function() {
			var node = make_pbFormatBuilder.getParentNode(make_pbFormatBuilder.nodes.notice);

			// Create a new element ID.
			this.set('id', this.createID());

			// Check to see if we're updating an existing format.
			if (true === this.get('update')) {
				this.parseAttributes(node);
			}
		},

		/**
		 * Defines the fields in the options form.
		 *
		 * @since 1.4.1.
		 *
		 * @returns array
		 */
		getOptionFields: function() {
			var items = [
				make_pbFormatBuilder.getColorButton( 'colorBackground', 'Background Color' ),
				make_pbFormatBuilder.getColorButton( 'colorText', 'Text Color' ),
				{
					type: 'textbox',
					name: 'fontSize',
					label: 'Font Size (px)',
					size: 3,
					classes: 'monospace',
					value: this.escape('fontSize')
				},
				make_pbFormatBuilder.getIconButton( 'icon', 'Icon' ),
				{
					type: 'textbox',
					name: 'iconSize',
					label: 'Icon Size (px)',
					size: 3,
					classes: 'monospace',
					value: this.escape('iconSize')
				},
				make_pbFormatBuilder.getColorButton( 'colorIcon', 'Icon Color' ),
				{
					type: 'listbox',
					name: 'iconPosition',
					label: 'Icon Position',
					value: this.escape('iconPosition'),
					values: [
						{
							text: 'left',
							value: 'left'
						},
						{
							text: 'right',
							value: 'right'
						}
					]
				},
				{
					type: 'textbox',
					name: 'paddingHorz',
					label: 'Horizontal Padding (px)',
					size: 3,
					classes: 'monospace',
					value: this.escape('paddingHorz')
				},
				{
					type: 'textbox',
					name: 'paddingVert',
					label: 'Vertical Padding (px)',
					size: 3,
					classes: 'monospace',
					value: this.escape('paddingVert')
				},
				{
					type: 'listbox',
					name: 'borderStyle',
					label: 'Border Style',
					value: this.escape('borderStyle'),
					values: [
						{
							text: 'none',
							value: 'none'
						},
						{
							text: 'solid',
							value: 'solid'
						},
						{
							text: 'dotted',
							value: 'dotted'
						},
						{
							text: 'dashed',
							value: 'dashed'
						},
						{
							text: 'double',
							value: 'double'
						}
					]
				},
				{
					type: 'textbox',
					name: 'borderWidth',
					label: 'Border Width (px)',
					size: 3,
					classes: 'monospace',
					value: this.escape('borderWidth')
				},
				make_pbFormatBuilder.getColorButton( 'colorBorder', 'Border Color' )
			];

			return this.wrapOptionFields(items);
		},

		/**
		 * Parse an existing format node and extract its format options.
		 *
		 * @since 1.4.1.
		 *
		 * @param node
		 */
		parseAttributes: function(node) {
			var self = this,
				$node = $(node),
				icon, iconClasses, iconSize, iconColor, fontSize, paddingHorz, paddingVert, borderWidth;

			// Get an existing ID.
			if ($node.attr('id')) this.set('id', $node.attr('id'));

			// Parse the icon.
			icon = $node.find('i.make_pb-notice-icon');
			if ( icon.length > 0 ) {
				iconClasses = icon.attr('class').split(/\s+/);
				// Look for relevant classes on the <i> element.
				$.each(iconClasses, function(index, iconClass) {
					if (iconClass.match(/^fa-/)) {
						// Icon
						self.set('icon', iconClass);
					} else if (iconClass.match(/^pull-/)) {
						// Icon position
						self.set('iconPosition', iconClass.replace('pull-', ''));
					}
				});
				// Icon font size
				if (icon.css('fontSize')) {
					iconSize = parseInt( icon.css('fontSize') );
					this.set('iconSize', iconSize + ''); // Convert integer to string for TinyMCE
				}
				// Icon color
				if (icon.css('color')) {
					iconColor = icon.css('color');
					this.set('colorIcon', iconColor);
				}
			}

			// Font size
			if ( $node.css('fontSize') ) {
				fontSize = parseInt( $node.css('fontSize') );
				this.set('fontSize', fontSize + ''); // Convert integer to string for TinyMCE
			}
			// Horizontal padding
			if ( $node.css('paddingLeft') ) {
				paddingHorz = parseInt( $node.css('paddingLeft') );
				this.set('paddingHorz', paddingHorz + ''); // Convert integer to string for TinyMCE
			}
			// Vertical padding
			if ( $node.css('paddingTop') ) {
				paddingVert = parseInt( $node.css('paddingTop') );
				this.set('paddingVert', paddingVert + ''); // Convert integer to string for TinyMCE
			}
			// Border style
			if ( $node.css('borderTopStyle') ) this.set('borderStyle', $node.css('borderTopStyle'));
			// Border width
			if ( $node.css('borderTopWidth') ) {
				borderWidth = parseInt( $node.css('borderTopWidth') );
				this.set('borderWidth', borderWidth + ''); // Convert integer to string for TinyMCE
			}
			// Border color
			if ( $node.css('borderTopColor') ) this.set('colorBorder', $node.css('borderTopColor'));
			// Background color
			if ( $node.css('backgroundColor') ) this.set('colorBackground', $node.css('backgroundColor'));
			// Text color
			if ( $node.css('color') ) this.set('colorText', $node.css('color'));
		},

		/**
		 * Insert the format markup into the editor.
		 *
		 * @since 1.4.1.
		 */
		insert: function() {
			var $node, $icon;

			// If not updating an existing format, apply to the current selection using the Formatter.
			if (true !== this.get('update')) {
				make_pbFormatBuilder.editor.formatter.apply('notice');
			}

			// Make sure the right node is selected.
			$node = $(make_pbFormatBuilder.getParentNode(make_pbFormatBuilder.nodes.notice));

			// Set the element ID, if it doesn't have one yet.
			if (! $node.attr('id')) {
				$node.attr('id', this.escape('id'));
			}

			// Add inline styles.
			$node.css({
				backgroundColor: this.escape('colorBackground'),
				color: this.escape('colorText'),
				padding: this.escape('paddingVert') + 'px ' + this.escape('paddingHorz') + 'px',
				borderStyle: this.escape('borderStyle'),
				borderWidth: this.escape('borderWidth') + 'px',
				borderColor: this.escape('colorBorder')
			});

			// Add a font-size style if it's different than the user setting for the body font.
			if (this.escape('fontSize') != make_pbFormatBuilderVars.userSettings.fontSizeBody) {
				$node.css('fontSize', this.escape('fontSize') + 'px');
			}

			// Remove any existing icons.
			$node.find('i.make_pb-notice-icon').remove();

			// Add the current icon, if one is set.
			if ('' !== this.get('icon')) {
				// Build the icon.
				$icon = $('<i>');
				$icon.attr('class', 'make_pb-notice-icon fa ' + this.escape('icon') + ' pull-' + this.escape('iconPosition'));
				$icon.css({
					fontSize: this.escape('iconSize') + 'px',
					color: this.escape('colorIcon')
				});

				// Add the new icon.
				$node.prepend($icon);
			}

			// Remove TinyMCE attribute that breaks things when trying to update an existing format.
			$node.removeAttr('data-mce-style');
		},

		/**
		 * Remove the existing format node.
		 *
		 * @since 1.4.1.
		 */
		remove: function() {
			var node = make_pbFormatBuilder.getParentNode(make_pbFormatBuilder.nodes.notice),
				content;

			// Remove the icon if it exists.
			$(node).find('i.make_pb-notice-icon').remove();

			// Get inner content.
			content = $(node).html().trim();

			// Set the selection to the whole node.
			make_pbFormatBuilder.currentSelection.select(node);

			// Replace the current selection with the inner content.
			make_pbFormatBuilder.currentSelection.setContent(content);
		}
	});
})( window, Backbone, jQuery, _, make_pbFormatBuilder, make_pbFormatBuilderVars );