(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAlbum = Backbone.View.extend({
        events: {
            'blur h1': 'save',
            'blur .description': 'save'
        },

        initialize: function(options) {
            this.options = options;

            this.template = _.template($('#b-assets-view-album-template').html());
            this.assets = this.model.getAssets();
        },

        render: function() {
            this.$el.html($(this.template({
                album: this.model
            })));

            this.$name = this.$('h1').boomcmsEditableHeading();
            this.$description = this.$('.description').boomcmsEditableHeading();

            new BoomCMS.AssetManager.ThumbnailGrid({
                assets: this.assets,
                selection: this.options.selection,
                el: this.$el.find('.b-assets-view-thumbs > div:nth-of-type(2)')
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
