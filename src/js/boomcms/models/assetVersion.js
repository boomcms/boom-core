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
            return '/asset/version/' + this.getId() + '/200/0';
        }
    });
}(BoomCMS));
