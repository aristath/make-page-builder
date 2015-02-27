/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	var Sections = Backbone.Collection.extend({
		model: oneApp.SectionModel,

		$stage: $('#make_pb-stage'),

		toggleStageClass: function() {
			var sections = $('.make_pb-section', this.$stage).length;

			if (sections > 0) {
				this.$stage.removeClass('make_pb-stage-closed');
			} else {
				this.$stage.addClass('make_pb-stage-closed');
				$('html, body').animate({
					scrollTop: $('#make_pb-menu').offset().top
				}, oneApp.options.closeSpeed);
			}
		}
	});

	oneApp.sections = new Sections();
})(window, Backbone, jQuery, _, oneApp);