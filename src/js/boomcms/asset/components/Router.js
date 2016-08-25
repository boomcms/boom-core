(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Router = Backbone.Router.extend({
		routes: {
			'': 'home',
			'upload': 'upload',
			'asset/:asset': 'viewAsset',
			'asset/:asset/:section': 'viewAsset'
		},

		initialize: function(options) {
			this.assets = options.assets;
		},

		viewAsset: function(id, section) {
			var asset = this.assets.get(id);

			if (asset === undefined) {
				asset = new BoomCMS.Asset({id: id});
				asset.fetch();

				this.assets.add(asset);
			}

			asset.trigger('view', asset, section);
		},

		home: function() {
			this.trigger('home');
		},

		upload: function() {
			this.trigger('upload');
		}
	});
}(Backbone, BoomCMS));
