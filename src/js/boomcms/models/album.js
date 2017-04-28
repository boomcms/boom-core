(function(BoomCMS) {
    'use strict';

    BoomCMS.Album = BoomCMS.Model.extend({
        assetsFetched: false,
        urlRoot: BoomCMS.urlRoot + 'album',

        getAssets: function() {
            if (this.assetsFetched === false) {
                this.assets.fetch({
                    data: {
                        album: this.getId()
                    },
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
        }
    });
}(BoomCMS));
