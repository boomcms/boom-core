(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Router = Backbone.Router.extend({
        routes: {
            '': 'home',
            'upload': 'upload',
            'asset/:asset/:section': 'viewAsset',
            'selection/:selection/:section': 'viewSelection'
        },

        initialize: function(options) {
            this.assets = options.assets;
        },

        viewAsset: function(id, section) {
            var asset = this.assets.get(id);

            if (asset === undefined) {
                asset = new BoomCMS.Asset({id: id});
                asset.fetch({
                    success: function() {
                        asset.trigger('view', asset, section);
                    }
                });

                this.assets.add(asset);
            } else {
                asset.trigger('view', asset, section);
            }
        },

        viewSelection: function(selection, section) {
            var assetIds = selection.split(',');

            this.trigger('selection', assetIds, section);
        },

        home: function() {
            this.trigger('home');
        },

        upload: function() {
            this.trigger('upload');
        }
    });
}(Backbone, BoomCMS));
