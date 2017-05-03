(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewSelection = Backbone.View.extend({
        eventsBound: false,
        selected: 'selected',
        tagsDisplayed: false,
        tagName: 'div',
        templateSelector: '#b-assets-selection-template',

        addAlbum: function(album) {
            album.addAssets(this.selection);

            this.relatedAlbums.add(album);
        },

        bind: function() {
            var view = this,
                selection = this.selection;

            this.eventsBound = true;

            this.$el
                .on('click', '.b-settings-close', function(e) {
                    e.preventDefault();

                    view.router.goToPreviousOrHome();
                })
                .on('click', '.b-assets-delete', function() {
                    selection.destroy();
                })
                .on('click', 'a[data-section]', function(e) {
                    e.preventDefault();

                    view.viewSection($(this).attr('data-section'));
                })
                .on('submit', '#b-assets-download-filename', function(e) {
                    e.preventDefault();

                    var filename = $(this).find('input[name=filename]').val();

                    selection.download(filename);
                })
                .on('click', '#b-asset-albums a', function(e) {
                    e.preventDefault();

                    var albumId = $(this).parent().attr('data-album'),
                        album = view.albums.get(albumId);

                    view.toggleAlbum(album);
                });

            this.$el.ui();
        },

        getSection: function() {
            return this.$('a[data-section].selected').attr('data-section');
        },

        init: function(options) {
            var view = this;

            this.section = options.section;
            this.router = options.router;
            this.albums = options.albums;
            this.relatedAlbums = this.selection.getAlbums();

            this.template = _.template($(this.templateSelector).html());

            this.listenTo(this.selection, 'sync add remove', function() {
                this.render(view.getSection());
            });

            this.listenTo(this.selection, 'destroy', function() {
                view.close();
            });
        },

        initialize: function(options) {
            this.selection = options.selection;

            this.init(options);
        },

        removeAlbum: function(album) {
            album.removeAssets(this.selection);

            this.relatedAlbums.remove(album);
        },

        render: function() {
            this.$el.html($(this.template({
                selection: this.selection,
                section: this.section
            })));
            
            var $about = this.$('.about');
            $about.text($about.text().replace(':count', this.selection.length));

            if (this.eventsBound === false) {
                this.bind();
            }

            this.viewAlbums();

            this.listenTo(this.assets, 'select', function() {
                this.router.updateSelection(this.selection, this.getSection());
            });

            return this;
        },

        toggleAlbum: function(album) {
            var method = this.relatedAlbums.get(album.getId()) === undefined ? 
                'addAlbum' : 'removeAlbum';

            this[method](album);
        },

        viewAlbums: function() {
            var view = new BoomCMS.AssetManager.AlbumList({
                albums: this.albums,
                selected: this.relatedAlbums
            });

            this.$('#b-asset-albums > div').html(view.render().el);
        },

        viewSection: function(section) {
            this.$('.b-settings-menu a, .b-settings-content > div')
                .removeClass('selected')
                .filter('[data-section=' + section + ']')
                .addClass('selected');

            this.router.updateSection(section);
        }
    });
}(jQuery, Backbone, BoomCMS));
