(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.Assets = Backbone.Collection.extend({
        albums: null,
        fetched: false,
        model: BoomCMS.Asset,
        url: BoomCMS.urlRoot + 'asset',
        comparator: 'name',

		destroy: function() {
			var assets = this,
                assetIds = this.getAssetIds();

            return $.ajax({
                url: this.url,
                type: 'delete',
                data: {
                    'assets[]': this.getAssetIds()
                }
            })
            .done(function() {
                assets.trigger('destroy-all', assetIds);
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

        findById: function(id) {
            return this.findWhere({id: parseInt(id)});
        },

        fetchOnce: function(success) {
            if (this.fetched === false) {
                this.fetched = true;

                this.fetch({
                    reset: true,
                    success: success
                });
            } else if (typeof success === 'function') {
                success();
            }
        },

        getAlbums: function() {
            var assets = this;

            if (this.albums === null) {
                this.albums = $.Deferred();

                var albums = new BoomCMS.Collections.Albums();

                albums.fetch({
                    data: {
                        'assets': this.getAssetIds()
                    },
                    complete: function() {
                        assets.albums.resolve(albums);
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
            var assetId = parseInt(assetId),
                asset = this.findWhere({id: assetId});

            if (asset === undefined) {
                asset = new BoomCMS.Asset({id: assetId});
                asset.fetch();

                this.add(asset);
            }

            return asset;
        },

        initialize: function() {
            this.on('reset', function() {
                this.albums = null;
            });
        },

        mostRecentUploadsFirst: function() {
            this.setOrderBy('created_at', 'desc');
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

        removeIfExists: function(assetId) {
            var matched = this.findWhere({id: assetId});

            if (matched !== undefined) {
                this.remove(matched);
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

            return this;
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
