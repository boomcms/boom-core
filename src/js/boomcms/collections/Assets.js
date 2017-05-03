(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.Assets = Backbone.Collection.extend({
        model: BoomCMS.Asset,
        url: BoomCMS.urlRoot + 'asset',
        comparator: 'name',

		destroy: function() {
			var assets = this;

            return $.ajax({
                url: this.url,
                type: 'delete',
                data: {
                    'assets[]': this.getAssetIds()
                }
            })
            .done(function() {
                assets.each(function(model) {
                    model.trigger('destroy');
                });
            });
        },
    
        download: function(filename) {
            var url = this.url + '/download?',
                assets = [];

            for (var i = 0; i < this.models.length; i++) {
                assets[i] = 'assets[]=' + this.models[i].getId();
            }

            url = url + assets.join('&');
            url = url + '&filename=' + filename;

            window.location = url;
        },

        getAlbums: function() {
            if (this.albums === undefined) {
                this.albums = new BoomCMS.Collections.Albums();

                this.albums.fetch({
                    data: {
                        'assets': this.getAssetIds()
                    }
                });
            }

            return this.albums;
        },

        getAssetIds: function() {
            return this.pluck('id');
        },

        getIdString: function() {
            return this.getAssetIds().join(',');
        },

        getOrFetch: function(assetId) {
            var asset = this.get(assetId);

            if (asset === undefined) {
                asset = new BoomCMS.Asset({id: assetId});
                asset.fetch();

                this.add(asset);
            }

            return asset;
        },

        parse: function(data) {
            return data.assets;
        },

        /**
         * Remove the asset if it exists in the collection, otherwise add it to
         *
         * @param BoomCMS.Models.Asset asset
         */
        toggle: function(asset) {
            var method = this.findWhere({id: asset.getId()}) ? 'remove' : 'add';

            this[method](asset);
        }
    });
}(Backbone, BoomCMS));
