(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplateView = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-template-pages').html()),

        initialize: function() {
            this.listenTo(this.model.pages, 'sync', this.addPages);
        },

        addPages: function(pages) {
            var table = this;

            pages.each(function(page) {
                var view = new BoomCMS.TemplateManager.TemplatePageItem({
                    model: page
                });

                table.$('tbody').append(view.render().el);
            });

            this.$('table').tablesorter();
        },

        render: function() {
            this.$el
                .html(this.template({
                    template: this.model
                }));
            
            this.model.getPages();

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));