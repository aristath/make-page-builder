/*global jQuery, tinyMCE, switchEditors */
var oneApp = oneApp || {}, ttfMaeraFrames = ttfMaeraFrames || [];

(function ($, oneApp, ttfMaeraFrames) {
	'use strict';

	// Kickoff Backbone App
	new oneApp.MenuView();

	oneApp.options = {
		openSpeed : 400,
		closeSpeed: 250
	};

	oneApp.cache = {
		$sectionOrder: $('#maera_pb-section-order'),
		$scrollHandle: $('html, body'),
		$maeraEditor: $('#wp-maera-wrap'),
		$maeraTextArea: $('#maera')
	};

	oneApp.initSortables = function () {
		$('.maera_pb-stage').sortable({
			handle: '.maera_pb-section-header',
			placeholder: 'sortable-placeholder',
			forcePlaceholderSizeType: true,
			distance: 2,
			tolerance: 'pointer',
			start: function (event, ui) {
				// Set the height of the placeholder to that of the sorted item
				var $item = $(ui.item.get(0)),
					$stage = $item.parents('.maera_pb-stage');

				$item.css('-webkit-transform', 'translateZ(0)');
				$('.sortable-placeholder', $stage).height(parseInt($item.height(), 10) - 2);
			},
			stop: function (event, ui) {
				var $item = $(ui.item.get(0)),
					$frames = $('iframe', $item);

				$item.css('-webkit-transform', '');
				oneApp.setOrder( $(this).sortable('toArray', {attribute: 'data-id'}), oneApp.cache.$sectionOrder );

				$.each($frames, function() {
					var id = $(this).attr('id').replace('maera_pb-iframe-', '');
					setTimeout(function() {
						oneApp.initFrame(id);
					}, 100);
				});
			}
		});
	};

	oneApp.setOrder = function (order, $input) {
		// Use a comma separated list
		order = order.join();

		// Set the val of the input
		$input.val(order);
	};

	oneApp.addOrderValue = function (id, $input) {
		var currentOrder = $input.val(),
			currentOrderArray;

		if ('' === currentOrder) {
			currentOrderArray = [id];
		} else {
			currentOrderArray = currentOrder.split(',');
			currentOrderArray.push(id);
		}

		oneApp.setOrder(currentOrderArray, $input);
	};

	oneApp.removeOrderValue = function (id, $input) {
		var currentOrder = $input.val(),
			currentOrderArray;

		if ('' === currentOrder) {
			currentOrderArray = [];
		} else {
			currentOrderArray = currentOrder.split(',');
			currentOrderArray = _.reject(currentOrderArray, function (item) {
				return id.toString() === item.toString();
			});
		}

		oneApp.setOrder(currentOrderArray, $input);
	};

	oneApp.initViews = function () {
		$('.maera_pb-section').each(function () {
			var $section = $(this),
				idAttr = $section.attr('id'),
				id = $section.attr('data-id'),
				sectionType = $section.attr('data-section-type'),
				sectionModel, modelViewName, view, viewName;

			// Build the model
			sectionModel = new oneApp.SectionModel({
				sectionType: sectionType,
				id: id
			});

			// Ensure that a view exists for the section, otherwise use the base view
			modelViewName = sectionModel.get('viewName') + 'View';
			viewName      = (true === oneApp.hasOwnProperty(modelViewName)) ? modelViewName : 'SectionView';

			// Create view
			view = new oneApp[viewName]({
				model: sectionModel,
				el: $('#' + idAttr),
				serverRendered: true
			});
		});
	};

	oneApp.scrollToAddedView = function (view) {
		// Scroll to the new section
		oneApp.cache.$scrollHandle.animate({
			scrollTop: parseInt($('#' + view.idAttr).offset().top, 10) - 32 - 9 // Offset + admin bar height + margin
		}, 800, 'easeOutQuad', function() {
			oneApp.focusFirstInput(view);
		});
	};

	oneApp.focusFirstInput = function (view) {
		$('input[type="text"]', view.$el).not('.wp-color-picker').first().focus();
	};

	oneApp.filliframe = function (iframeID) {
		var iframe = document.getElementById(iframeID),
			iframeContent = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document,
			iframeBody = $('body', iframeContent),
			content;

		content = oneApp.getMaeraContent();

		// Since content is being displayed in the iframe, run it through autop
		content = switchEditors.wpautop(oneApp.wrapShortcodes(content));

		iframeBody.html(content);
	};

	oneApp.setTextArea = function (textAreaID) {
		$('#' + textAreaID).val(oneApp.getMaeraContent());
	};

	oneApp.getMaeraContent = function () {
		var content = '';

		if (oneApp.isVisualActive()) {
			content = tinyMCE.get('maera').getContent();
		} else {
			content = oneApp.cache.$maeraTextArea.val();
		}

		return content;
	};

	oneApp.setMaeraContent = function (content) {
		if (oneApp.isVisualActive()) {
			tinyMCE.get('maera').setContent(content);
		} else {
			oneApp.cache.$maeraTextArea.val(switchEditors.pre_wpautop(content));
		}
	};

	oneApp.setMaeraContentFromTextArea = function (iframeID, textAreaID) {
		var textAreaContent = $('#' + textAreaID).val();

		oneApp.setActiveiframeID(iframeID);
		oneApp.setActiveTextAreaID(textAreaID);
		oneApp.setMaeraContent(textAreaContent);
	};

	oneApp.setActiveiframeID = function(iframeID) {
		oneApp.activeiframeID = iframeID;
	};

	oneApp.setActiveTextAreaID = function(textAreaID) {
		oneApp.activeTextAreaID = textAreaID;
	};

	oneApp.getActiveiframeID = function() {
		if (oneApp.hasOwnProperty('activeiframeID')) {
			return oneApp.activeiframeID;
		} else {
			return '';
		}
	};

	oneApp.getActiveTextAreaID = function() {
		if (oneApp.hasOwnProperty('activeTextAreaID')) {
			return oneApp.activeTextAreaID;
		} else {
			return '';
		}
	};

	oneApp.isTextActive = function() {
		return oneApp.cache.$maeraEditor.hasClass('html-active');
	};

	oneApp.isVisualActive = function() {
		return oneApp.cache.$maeraEditor.hasClass('tmce-active');
	};

	oneApp.initFrames = function() {
		if (ttfMaeraFrames.length > 0) {
			var link = oneApp.getFrameHeadLinks();

			// Add content and CSS
			_.each(ttfMaeraFrames, function(id) {
				oneApp.initFrame(id, link);
			});
		}
	};

	oneApp.initFrame = function(id, link) {
		var content = $('#maera_pb-content-' + id).val(),
			iframe = document.getElementById('maera_pb-iframe-' + id),
			iframeContent = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document,
			iframeHead = $('head', iframeContent),
			iframeBody = $('body', iframeContent);

		link = link || oneApp.getFrameHeadLinks();

		iframeHead.html(link);
		iframeBody.html(switchEditors.wpautop(oneApp.wrapShortcodes(content)));
	};

	oneApp.getFrameHeadLinks = function() {
		var scripts = tinyMCEPreInit.mceInit.maera.content_css.split(','),
			link = '';

		// Create the CSS links for the head
		_.each(scripts, function(e) {
			link += '<link type="text/css" rel="stylesheet" href="' + e + '" />';
		});

		return link;
	};

	oneApp.wrapShortcodes = function(content) {
		return content.replace(/^(<p>)?(\[.*\])(<\/p>)?$/gm, '<div class="shortcode-wrapper">$2</div>');
	};

	oneApp.triggerInitFrames = function() {
		$(document).ready(function(){
			oneApp.initFrames();
		});
	};

	$('body').on('click', '.maera_pb-remove-image-from-modal', function(evt){
		evt.preventDefault();

		var $parent = oneApp.$currentPlaceholder.parents('.maera_pb-uploader'),
			$input = $('.maera_pb-media-uploader-value', $parent);

		// Remove the image
		oneApp.$currentPlaceholder.css('background-image', '');
		$parent.removeClass('maera_pb-has-image-set');

		// Remove the value from the input
		$input.removeAttr('value');

		wp.media.frames.frame.close();
	});

	wp.media.view.Sidebar = wp.media.view.Sidebar.extend({
		render: function() {
			this.$el.html( wp.media.template( 'maera_pb-remove-image' ) );
			return this;
		}
	});

	// Leaving function to avoid errors if 3rd party code uses it. Deprecated in 1.4.0.
	oneApp.initAllEditors = function(id, model) {};

	oneApp.initSortables();
	oneApp.initViews();
	oneApp.triggerInitFrames();
})(jQuery, oneApp, ttfMaeraFrames);