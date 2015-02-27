/*!
 * Script for adding functionality to the Edit Page screen.
 *
 * @since 1.0.0
 */
/* global jQuery, make_pbEditPageData */
(function($) {
	'use strict';

	var make_pbEditPage = {
		cache: {
			$document: $(document)
		},

		init: function() {
			this.cacheElements();
			this.bindEvents();
		},

		cacheElements: function() {
			this.cache.$pageTemplate = $('#page_template');
			this.cache.$builderToggle = $('#use-builder');
			this.cache.$mainEditor = $('#postdivrich');
			this.cache.$builder = $('#make_pb-builder');
			this.cache.$duplicator = $('.make_pb-duplicator');
			this.cache.$builderHide = $('#make_pb-builder-hide');
			this.cache.$featuredImage = $('#postimagediv');
			this.cache.$commentstatus = $('#comment_status');
			this.cache.$pingstatus = $('#ping_status');
			this.cache.$body = $('body');
		},

		bindEvents: function() {
			var self = this;

			// Setup the event for toggling the Page Builder when the page template input changes
			self.cache.$pageTemplate.on('change', self.templateToggle);
			self.cache.$builderToggle.on('click', self.templateToggle);

			// Change default settings for new pages
			if ( typeof make_pbEditPageData !== 'undefined' && 'post-new.php' === make_pbEditPageData.pageNow && 'page' === pagenow ) {
				// Builder template is selected by default
				self.cache.$pageTemplate.val('template-builder.php');

				// Comments and pings turned off by default
				self.cache.$commentstatus.prop('checked', '');
				self.cache.$pingstatus.prop('checked', '');
			}

			// Make sure screen is correctly toggled on load
			self.cache.$document.on('ready', function() {
				self.cache.$pageTemplate.trigger('change');
			});
		},

		templateToggle: function(e) {
			var self = make_pbEditPage,
				$target = $(e.target),
				val = $target.val();

			if ('template-builder.php' === val || $target.is(':checked')) {
				self.cache.$mainEditor.hide();
				self.cache.$builder.show();
				self.cache.$duplicator.show();
				self.cache.$builderHide.prop('checked', true).parent().show();
				self.featuredImageToggle('hide');
				self.cache.$body.addClass('make_pb-builder-active').removeClass('make_pb-default-active');
			} else {
				self.cache.$mainEditor.show();
				self.cache.$builder.hide();
				self.cache.$duplicator.hide();
				self.cache.$builderHide.prop('checked', false).parent().hide();
				self.featuredImageToggle('show');
				self.cache.$body.removeClass('make_pb-builder-active').addClass('make_pb-default-active');
			}
		},

		featuredImageToggle: function(state) {
			var self = make_pbEditPage,
				unavailable;

			self.cache.$featuredImage.find('.make_pb-message').remove();

			if ('undefined' !== typeof make_pbEditPageData) {
				unavailable = make_pbEditPageData.featuredImage;
			} else {
				unavailable = 'Featured images are not available for this page while using the current page template.';
			}

			unavailable = '<div class="make_pb-message inside"><p class="hide-if-no-js">'+unavailable+'</p></div>';

			if ('show' === state) {
				self.cache.$featuredImage.find('.inside').show();
			} else {
				self.cache.$featuredImage.find('.inside').before(unavailable).hide();
			}
		}
	};

	make_pbEditPage.init();
})(jQuery);