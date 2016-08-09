(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Thumbnail = Backbone.View.extend({
		tagName: 'div',

		initialize: function() {
			this.template = _.template($('#b-asset-thumb').html());

			this.listenTo(this.model, 'change', this.render);
		},

		render: function() {
			this.$el
				.html(this.template({
					asset: this.model
				}));

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));