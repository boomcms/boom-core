(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Router = Backbone.Router.extend({
        routes: {
            '': 'home',
            'upload': 'upload',
            'asset/:asset/:section': 'viewAsset',
            'selection/:selection/:section': 'viewSelection',
            'albums/:album': 'viewAlbum',
            'search': 'search'
        },

        goTo: function(route) {
            this.navigate(route, {trigger: true});
        },

        initialize: function(options) {
            this.assets = options.assets;
        },

        updateSelection: function(assets, section, options) {
            this.navigate('selection/' + assets.getIdString() + '/' + section, options);
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
        }
    });
}(Backbone, BoomCMS));
