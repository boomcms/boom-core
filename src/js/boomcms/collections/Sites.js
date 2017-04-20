(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.Sites = Backbone.Collection.extend({
        model: BoomCMS.Site,
        url: BoomCMS.urlRoot + 'site',
        comparator: 'name'
    });
}(Backbone, window.BoomCMS));
