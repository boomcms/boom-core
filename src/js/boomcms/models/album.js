(function(BoomCMS) {
    'use strict';

    BoomCMS.Album = BoomCMS.Model.extend({
        urlRoot: BoomCMS.urlRoot + 'album',

        getAssetCount: function() {
            return this.get('asset_count');
        },

        getName: function() {
            return this.get('name');
        }
    });
}(BoomCMS));
