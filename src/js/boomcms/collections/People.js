(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.People = Backbone.Collection.extend({
        model: BoomCMS.Person,
        url: BoomCMS.urlRoot + 'person',
        comparator: 'name',

        findByEmail: function(email) {
            return this.findWhere({email: email});
        }
    });
}(Backbone, BoomCMS));
