(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplatePageItem = Backbone.View.extend({
        tagName: 'tr',
        template: _.template($('#b-template-page').html()),

        render: function() {
            this.$el
                .html(this.template({
                    page: this.model
                }));

            this.$('time').text(moment.unix(this.$('time').attr('datetime')).fromNow());

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));