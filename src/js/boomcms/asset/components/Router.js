(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Router = Backbone.Router.extend({
		routes: {
			'': 'home',
			'asset/:asset': 'viewAsset'
		},

		initialize: function(options) {
			this.assets = options.assets;
		},

		viewAsset: function(id) {
			var asset = this.assets.get(id);

			asset.trigger('view', asset);
		},

		home: function() {}
	});
}(Backbone, BoomCMS));
