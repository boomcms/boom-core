(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Thumbnail = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-asset-thumb').html()),

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
		},

		render: function() {
			this.$el
				.html(this.template({
					asset: this.asset
				}));
		}
	});
}(jQuery, Backbone, BoomCMS));