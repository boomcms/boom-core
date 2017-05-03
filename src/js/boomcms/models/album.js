(function(BoomCMS) {
    'use strict';

    BoomCMS.Album = BoomCMS.Model.extend({
        assetsFetched: false,
        urlRoot: BoomCMS.urlRoot + 'album',

        addAssets: function(assets) {
            var model = this;

            $.post(this.assets.url, {
                'assets[]': assets.getAssetIds()
            })
            .done(function(response) {
                model.set(response);
            });

            assets.each(function(asset) {
                model.assets.add(asset);
            });
        },

        getAssets: function() {
            if (this.assetsFetched === false) {
                this.assets.fetch({
                    reset: true
                });

                this.assetsFetched = true;
            }

            return this.assets;
        },

        getAssetCount: function() {
            return this.get('asset_count');
        },

        getDescription: function() {
            return this.get('description');
        },

        getName: function() {
            return this.get('name');
        },

        getSlug: function() {
            return this.get('slug');
        },

        initialize: function() {
            this.assets = new BoomCMS.Collections.Assets();
            this.assets.url = BoomCMS.urlRoot + 'album/' + this.getId() + '/assets';
        },

        removeAssets: function(assets) {
            var model = this;

            $.ajax({
                url: this.assets.url,
                method: 'delete',
                data: {
                    'assets[]': assets.getAssetIds()
                }
            })
            .done(function(response) {
                model.set(response);
            });

            assets.each(function(asset) {
                model.assets.remove(asset);
            });
        }
    });
}(BoomCMS));
