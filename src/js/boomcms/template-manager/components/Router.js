(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.Router = Backbone.Router.extend({
        routes: {
            '': 'home',
            'template/:template/pages': 'viewTemplatePages'
        },

        initialize: function(options) {
            this.templates = options.templates;

            this.listenTo(this.templates, 'change:id', this.editGroupOnCreate);
            this.listenTo(this.people, 'destroy', this.goBackOnPersonDelete);
        },

        home: function() {
            this.trigger('home');
        },

        viewTemplatePages: function(id) {
            var template = this.templates.get(id);

            template.trigger('view', template);
        }
    });
}(Backbone, BoomCMS));
