(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.Templates = Backbone.Collection.extend({
        model: BoomCMS.Template,
        url: BoomCMS.urlRoot + 'template'
    });
}(Backbone, BoomCMS));
