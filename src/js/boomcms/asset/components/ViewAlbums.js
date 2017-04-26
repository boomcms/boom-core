(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAlbum = Backbone.View.extend({
        tagName: 'div',

        events: {
            'blur h1': 'save',
            'blur .description': 'save'
        },

        initialize: function() {
            this.template = _.template($('#b-assets-view-album-template').html());
        },

        render: function() {
            this.$el.html($(this.template({
                album: this.model
            })));

            this.$name = this.$('h1').boomcmsEditableHeading();
            this.$description = this.$('.description').boomcmsEditableHeading();

            new BoomCMS.AssetManager.ThumbnailGrid({
                assets: new BoomCMS.Collections.Albums(),
                el: this.$el.find('.b-assets-view-thumbs')
            }).render();

            return this;
        },

        save: function(e) {
            this.model
                .set({
                    name: this.$name.text(),
                    description: this.$description.text()
                })
                .save();
        }
    });
}(jQuery, Backbone, BoomCMS));
