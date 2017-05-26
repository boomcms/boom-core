(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.Assets = Backbone.Collection.extend({
        fetched: false,
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
                while (assets.length > 0) {
                    assets.models[0].trigger('destroy', assets.models[0]);
                }
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

        fetchOnce: function() {
            if (this.fetched === false) {
                this.fetched = true;

                this.fetch({reset: true});
            }
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
            var asset = this.findWhere({id: assetId});

            if (asset === undefined) {
                asset = new BoomCMS.Asset({id: assetId});
                asset.fetch();

                this.add(asset);
            }

            return asset;
        },

        parse: function(data) {
            this.total = data.total;

            return data.assets;
        },

        position: function(asset) {
            for (var i = 0; i < this.models.length; i++) {
                if (this.models[i].getId() === asset.getId()) {
                    return i;
                }
            }
        },

        setOrderBy: function(column, direction) {
            this.comparator = function(a, b) {
                var value1 = direction === 'asc' ? a.get(column) : b.get(column),
                    value2 = direction === 'asc' ? b.get(column) : a.get(column);

                return value1 > value2 ?  1
                    : value1 < value2 ? -1
                    :  0;
            };
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
