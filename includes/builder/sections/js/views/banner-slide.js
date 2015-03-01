/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.BannerSlideView = Backbone.View.extend({
		template: '',
		className: 'maera_pb-banner-slide maera_pb-banner-slide-open',

		events: {
			'click .maera_pb-banner-slide-remove': 'removeItem',
			'click .maera_pb-banner-slide-toggle': 'toggleSection'
		},

		initialize: function (options) {
			this.model = options.model;
			this.idAttr = 'maera_pb-banner-slide-' + this.model.get('id');
			this.serverRendered = ( options.serverRendered ) ? options.serverRendered : false;
			this.template = _.template($('#tmpl-maera_pb-banner-slide').html());
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()))
				.attr('id', this.idAttr)
				.attr('data-id', this.model.get('id'));
			return this;
		},

		removeItem: function (evt) {
			evt.preventDefault();

			var $stage = this.$el.parents('.maera_pb-banner-slides'),
				$orderInput = $('.maera_pb-banner-slide-order', $stage);

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
				$section = $this.parents('.maera_pb-banner-slide'),
				$sectionBody = $('.maera_pb-banner-slide-body', $section),
				$input = $('.maera_pb-banner-slide-state', this.$el);

			if ($section.hasClass('maera_pb-banner-slide-open')) {
				$sectionBody.slideUp(oneApp.options.closeSpeed, function() {
					$section.removeClass('maera_pb-banner-slide-open');
					$input.val('closed');
				});
			} else {
				$sectionBody.slideDown(oneApp.options.openSpeed, function() {
					$section.addClass('maera_pb-banner-slide-open');
					$input.val('open');
				});
			}
		}
	});
})(window, Backbone, jQuery, _, oneApp);