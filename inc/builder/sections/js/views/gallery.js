/* global jQuery, _ */
var oneApp = oneApp || {}, $oneApp = $oneApp || jQuery(oneApp);

(function (window, $, _, oneApp, $oneApp) {
	'use strict';

	oneApp.GalleryView = oneApp.SectionView.extend({
		events: function() {
			return _.extend({}, oneApp.SectionView.prototype.events, {
				'click .make_pb-gallery-add-item' : 'addGalleryItem',
				'change .make_pb-gallery-columns' : 'handleColumns'
			});
		},

		addGalleryItem : function (evt, params) {
			evt.preventDefault();

			var view, html;

			// Create view
			view = new oneApp.GalleryItemView({
				model: new oneApp.GalleryItemModel({
					id: new Date().getTime(),
					parentID: this.getParentID()
				})
			});

			// Append view
			html = view.render().el;
			$('.make_pb-gallery-items-stage', this.$el).append(html);

			// Only scroll and focus if not triggered by the pseudo event
			if ( ! params ) {
				// Scroll to added view and focus first input
				oneApp.scrollToAddedView(view);
			}

			// Add the section value to the sortable order
			oneApp.addOrderValue(view.model.get('id'), $('.make_pb-gallery-item-order', $(view.$el).parents('.make_pb-gallery-items')));
		},

		getParentID: function() {
			var idAttr = this.$el.attr('id'),
				id = idAttr.replace('make_pb-section-', '');

			return parseInt(id, 10);
		},

		handleColumns : function (evt) {
			evt.preventDefault();

			var columns = $(evt.target).val(),
				$stage = $('.make_pb-gallery-items-stage', this.$el);

			$stage.removeClass('make_pb-gallery-columns-1 make_pb-gallery-columns-2 make_pb-gallery-columns-3 make_pb-gallery-columns-4');
			$stage.addClass('make_pb-gallery-columns-' + parseInt(columns, 10));
		}
	});

	// Makes gallery items sortable
	oneApp.initializeGalleryItemSortables = function(view) {
		$('.make_pb-gallery-items-stage', view).sortable({
			handle: '.make_pb-sortable-handle',
			placeholder: 'sortable-placeholder',
			distance: 2,
			tolerance: 'pointer',
			start: function (event, ui) {
				// Set the height of the placeholder to that of the sorted item
				var $item = $(ui.item.get(0)),
					$stage = $item.parents('.make_pb-gallery-items-stage');

				$('.sortable-placeholder', $stage)
					.height(parseInt($item.height(), 10) - 2); // -2 to account for placeholder border
			},
			stop: function (event, ui) {
				var $item = $(ui.item.get(0)),
					$stage = $item.parents('.make_pb-gallery-items'),
					$orderInput = $('.make_pb-gallery-item-order', $stage);

				oneApp.setOrder($(this).sortable('toArray', {attribute: 'data-id'}), $orderInput);
			}
		});
	};

	// Initialize the color picker
	oneApp.initializeGalleryItemColorPicker = function (view) {
		var $selector;
		view = view || '';

		if (view.$el) {
			$selector = $('.make_pb-gallery-background-color', view.$el);
		} else {
			$selector = $('.make_pb-gallery-background-color');
		}

		$selector.wpColorPicker();
	};

	// Initialize the sortables
	$oneApp.on('afterSectionViewAdded', function(evt, view) {
		if ('gallery' === view.model.get('sectionType')) {
			// Add 3 initial gallery item
			var $addButton = $('.make_pb-gallery-add-item', view.$el);
			$addButton.trigger('click', {type: 'pseudo'});
			$addButton.trigger('click', {type: 'pseudo'});
			$addButton.trigger('click', {type: 'pseudo'});

			// Initialize the sortables and picker
			oneApp.initializeGalleryItemSortables();
			oneApp.initializeGalleryItemColorPicker(view);
		}
	});

	// Initialize available gallery items
	oneApp.initGalleryItemViews = function ($el) {
		$el = $el || '';
		var $items = ('' === $el) ? $('.make_pb-gallery-item') : $('.make_pb-gallery-item', $el);

		$items.each(function () {
			var $item = $(this),
				idAttr = $item.attr('id'),
				id = $item.attr('data-id'),
				$section = $item.parents('.make_pb-section'),
				parentID = $section.attr('data-id'),
				model;

			// Build the model
			model = new oneApp.GalleryItemModel({
				id: id,
				parentID: parentID
			});

			// Build the view
			new oneApp.GalleryItemView({
				model: model,
				el: $('#' + idAttr),
				serverRendered: true
			});
		});

		oneApp.initializeGalleryItemSortables();
		oneApp.initializeGalleryItemColorPicker();
	};

	// Set the classes for the elements
	oneApp.setClearClasses = function ($el) {
		var columns = $('.make_pb-gallery-columns', $el).val(),
			$items = $('.make_pb-gallery-item', $el);

		$items.each(function(index, item){
			var $item = $(item);
			if (0 !== index && 0 === index % columns) {
				$item.addClass('clear');
			} else {
				$item.removeClass('clear');
			}
		});
	};

	// Initialize the views when the app starts up
	oneApp.initGalleryItemViews();
})(window, jQuery, _, oneApp, $oneApp);