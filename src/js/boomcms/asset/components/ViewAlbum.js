(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAlbum = Backbone.View.extend({
        events: {
            'blur h1': 'save',
            'blur .description': 'save',
            'click .delete': 'delete'
        },

        delete: function() {
            var album = this.model;

            BoomCMS.Confirmation('Please confirm', 'Are you sure you want to delete this album?')
                .done(function() {
                    album.destroy();    
                });
        },

        initialize: function(options) {
            var album = this.model,
                router = options.router;

            this.options = options;

            this.router = options.router;
            this.template = _.template($('#b-assets-view-album-template').html());
            this.assets = this.model.getAssets();

            this.model.on('change:slug', function() {
                router.navigate('albums/' + album.getSlug(), {replace: true});
            });

            this.model.on('destroy', function() {
                router.goTo('');
            });
        },

        render: function() {
            this.$el.html($(this.template({
                album: this.model
            })));

            this.$('h1, .description').boomcmsEditableHeading();

            new BoomCMS.AssetManager.ThumbnailGrid({
                assets: this.assets,
                selection: this.options.selection,
                el: this.$('.b-assets-view-thumbs')
            }).render();

            return this;
        },

        save: function(e) {
            this.model
                .set({
                    name: this.$('h1').text(),
                    description: this.$('.description').text()
                })
                .save();
        }
    });
}(jQuery, Backbone, BoomCMS));
