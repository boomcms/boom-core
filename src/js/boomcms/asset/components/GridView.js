(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.GridView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-assets-view-thumbs').html()),

		initialize: function(options) {
			this.assets = options.assets;

			this.listenTo(this.assets, 'sync', this.render);
		},

		render: function() {
			
		}
	});
}(jQuery, Backbone, BoomCMS));