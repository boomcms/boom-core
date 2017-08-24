(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAlbum = Backbone.View.extend({
        tagName: 'div',

        events: {
            'blur h1': 'save',
            'blur .description': 'save',
            'click .delete': 'delete'
        },

        delete: function(e) {
            e.preventDefault();

            var album = this.model;

            BoomCMS.Confirmation('Please confirm', 'Are you sure you want to delete this album?')
                .done(function() {
                    album.destroy();    
                });
        },

        initialize: function(options) {
            var album = this.model,
                albums = options.albums,
                router = options.router,
                routerParams = this.model.isNew() ? {trigger: true} : {replace: true};

            this.options = options;

            this.router = options.router;

            this.template = _.template($('#b-assets-view-album-template').html());

            if (!this.model.isNew()) {
                this.assets = this.model.getAssets();
            }

            this.model.on('change:slug', function() {
                albums.add(album);
                router.navigate('albums/' + album.getSlug(), routerParams);
            });

            this.model.on('destroy', function() {
                router.goTo('');
            });
        },

        render: function() {
            var album = this.model,
                view = this,
                sortbyId = 'b-assets-sortby' + this.model.getId();

            this.$el.html($(this.template({
                album: this.model
            })));

            this.$el
                .find('#b-assets-sortby')
                .attr('id', sortbyId)
                .end()
                .find('label[for="#b-assets-sortby"]')
                .attr('for', sortbyId);

            this.$el.on('change', '#' + sortbyId, function(e) {
                view.reorder(e);
            });

            this.$('h1, .description').boomcmsEditableHeading();

            if (!this.model.isNew()) {
                new BoomCMS.AssetManager.ThumbnailGrid({
                    assets: this.assets,
                    selection: this.options.selection,
                    el: this.$('.b-assets-view-thumbs'),
                    $container: this.options.$container
                }).render();
            }

            this.$('#b-assets-album-upload-' + album.getId())
                .assetUploader({
                    asset: undefined,
                    dropArea: this.$el,
                    uploadFinished: function(e, data) {
                        var assets = new BoomCMS.Collections.Assets(data.result.assets);

                        album.addAssets(assets);
                    }
                });

            return this;
        },

        reorder: function(e) {
            var value = $(e.target).val(),
                parts = value.split(' '),
                assets = this.model.getAssets();

            assets.setOrderBy(parts[0], parts[1]);
            assets.sort();
        },

        save: function() {
            this.model
                .set({
                    name: this.$('h1').text(),
                    description: this.$('.description').text()
                })
                .save();
        }
    });
}(jQuery, Backbone, BoomCMS));
