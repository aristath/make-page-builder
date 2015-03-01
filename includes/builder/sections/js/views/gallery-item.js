/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.GalleryItemView = Backbone.View.extend({
		template: '',
		className: 'maera_pb-gallery-item',

		events: {
			'click .maera_pb-gallery-item-remove': 'removeItem'
		},

		initialize: function (options) {
			this.model = options.model;
			this.idAttr = 'maera_pb-gallery-item-' + this.model.get('id');
			this.serverRendered = ( options.serverRendered ) ? options.serverRendered : false;
			this.template = _.template($('#tmpl-maera_pb-gallery-item').html());
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()))
				.attr('id', this.idAttr)
				.attr('data-id', this.model.get('id'));
			return this;
		},

		removeItem: function (evt) {
			evt.preventDefault();

			var $stage = this.$el.parents('.maera_pb-gallery-items'),
				$orderInput = $('.maera_pb-gallery-item-order', $stage);

			oneApp.removeOrderValue(this.model.get('id'), $orderInput);

			// Fade and slide out the section, then cleanup view
			this.$el.animate({
				opacity: 'toggle',
				height: 'toggle'
			}, oneApp.options.closeSpeed, function() {
				this.remove();
			}.bind(this));
		}
	});
})(window, Backbone, jQuery, _, oneApp);