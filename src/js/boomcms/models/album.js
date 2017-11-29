(function(BoomCMS) {
    'use strict';

    BoomCMS.Album = BoomCMS.Model.extend({
        defaults: {
            asset_count: 0
        },

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
            return this.assets;
        },

        getAssetCount: function() {
            return this.get('asset_count');
        },

        getDescription: function() {
            return this.get('description');
        },

        getFeatureImage: function() {
            var featureImageId = this.get('feature_image_id');

            return featureImageId ? new BoomCMS.Asset({id: featureImageId}) : null;
        },

        getName: function() {
            return this.get('name');
        },

        getSlug: function() {
            return this.get('slug');
        },

        initialize: function() {
            var album = this,
                destroyTimeout = null;

            this.assets = new BoomCMS.Collections.Assets();
            this.assets.mostRecentUploadsFirst();

            this.setAssetsUrl();

            this.assets.on('change:image', function(asset) {
                if (asset.getId() === album.get('feature_image_id')) {
                    album.trigger('change:thumbnail', album);
                }
            });

            this.assets.on('destroy remove', function() {
                // Get the album details from the server again to update asset count and feature image ID
                if (!destroyTimeout) {
                    destroyTimeout = setTimeout(function() {
                        album.fetch();
                        clearTimeout(destroyTimeout);
                    }, 0);
                }
            });

            this.on('change:id', function() {
                this.setAssetsUrl();
            });
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
        },

        setAssetsUrl: function() {
            this.assets.url = BoomCMS.urlRoot + 'album/' + this.getId() + '/assets';
        }
    });
}(BoomCMS));
