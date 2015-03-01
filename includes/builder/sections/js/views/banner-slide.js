/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.BannerSlideView = Backbone.View.extend({
		template: '',
		className: 'make_pb-banner-slide make_pb-banner-slide-open',

		events: {
			'click .make_pb-banner-slide-remove': 'removeItem',
			'click .make_pb-banner-slide-toggle': 'toggleSection'
		},

		initialize: function (options) {
			this.model = options.model;
			this.idAttr = 'make_pb-banner-slide-' + this.model.get('id');
			this.serverRendered = ( options.serverRendered ) ? options.serverRendered : false;
			this.template = _.template($('#tmpl-make_pb-banner-slide').html());
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()))
				.attr('id', this.idAttr)
				.attr('data-id', this.model.get('id'));
			return this;
		},

		removeItem: function (evt) {
			evt.preventDefault();

			var $stage = this.$el.parents('.make_pb-banner-slides'),
				$orderInput = $('.make_pb-banner-slide-order', $stage);

			oneApp.removeOrderValue(this.model.get('id'), $orderInput);

			// Fade and slide out the section, then cleanup view
			this.$el.animate({
				opacity: 'toggle',
				height: 'toggle'
			}, oneApp.options.closeSpeed, function() {
				this.remove();
			}.bind(this));
		},

		toggleSection: function (evt) {
			evt.preventDefault();

			var $this = $(evt.target),
				$section = $this.parents('.make_pb-banner-slide'),
				$sectionBody = $('.make_pb-banner-slide-body', $section),
				$input = $('.make_pb-banner-slide-state', this.$el);

			if ($section.hasClass('make_pb-banner-slide-open')) {
				$sectionBody.slideUp(oneApp.options.closeSpeed, function() {
					$section.removeClass('make_pb-banner-slide-open');
					$input.val('closed');
				});
			} else {
				$sectionBody.slideDown(oneApp.options.openSpeed, function() {
					$section.addClass('make_pb-banner-slide-open');
					$input.val('open');
				});
			}
		}
	});
})(window, Backbone, jQuery, _, oneApp);