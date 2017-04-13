(function(BoomCMS) {
    'use strict';

    BoomCMS.AssetVersion = BoomCMS.Model.extend({
        getEditedAt: function() {
            return this.get('created_at');
        },

        getEditedBy: function() {
            return new BoomCMS.Person(this.get('edited_by'));
        },

        getThumbnail: function() {
            return '/asset/' + this.get('asset_id') + '/view/200/0?version=' + this.getId();
        }
    });
}(BoomCMS));
