(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewSelection = Backbone.View.extend({
        albumsSelector: '#b-asset-albums',
        eventsBound: false,
        selected: 'selected',
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

                    view.router.goToContext();
                })
                .on('click', '.b-assets-delete', function() {
                    selection.destroy();
                    view.router.goToContext();
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
                .on('click', '.b-assets-create-album a', function(e) {
                    e.preventDefault();

                    view.createAlbumAndAddSelection();
                })
                .on('click', this.albumsSelector + ' [data-album] a', function(e) {
                    e.preventDefault();

                    var albumId = $(this).parent().attr('data-album'),
                        album = view.albums.get(albumId);

                    if (album !== undefined) {
                        view.toggleAlbum(album);
                    }
                })
                .on('click', '#b-selection-delete a', function(e) {
                    e.preventDefault();

                    view.removeFromSelection($(this).attr('data-asset'));
                });

            this.$el.ui();
        },

        createAlbumAndAddSelection: function() {
            var view = this,
                dialog;

            dialog = this.showCreateAlbumDialog()
                .done(function() {
                    var name = dialog.contents.find('input').val(),
                        album = new BoomCMS.Album();

                    if (name.trim() === '') {
                        return;
                    }

                    // Save the album and then add it to the albums collection
                    // Ensures that the album details are complete when it's added to the collection
                    album.save({name: name}, {
                        success: function() {
                            view.albums.add(album);
                        }
                    });

                    view.$el.one('rendered', function() {
                        var selector = view.albumsSelector + ' [data-album="' + album.getId() + '"]',
                            $el = view.$(selector);

                        $('html,body').animate({
                            scrollTop: $el.offset().top - $('#b-topbar').height()
                        }, 1000, function() {
                            view.addAlbum(album);
                        });
                    });
                });
        },

        getSection: function() {
            return this.$('a[data-section].selected').attr('data-section');
        },

        init: function(options) {
            this.section = options.section;
            this.router = options.router;
            this.albums = options.albums;
            this.relatedAlbums = this.selection.getAlbums();

            this.template = _.template($(this.templateSelector).html());
        },

        initialize: function(options) {
            var view = this;

            this.selection = options.selection;

            this.listenTo(this.selection, 'sync add remove', function() {
                this.render(view.getSection());
            });

            this.init(options);
        },

        removeAlbum: function(album) {
            album.removeAssets(this.selection);

            this.relatedAlbums.remove(album);
        },

        removeFromSelection: function(assetId) {
            var asset = this.selection.findById(assetId);

            if (asset !== undefined) {
                this.selection.remove(asset);
            }

            this.router.updateSelection(this.selection, this.getSection());
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

        showCreateAlbumDialog: function() {
            var dialog = new BoomCMS.Dialog({
                msg: $('#b-album-create-name-template').html(),
                width: 600,
                onLoad: function() {
                    dialog.contents.find('form').on('submit', function(e) {
                        e.preventDefault();

                        dialog.close();
                    });
                }
            });

            return dialog;
        },

        toggleAlbum: function(album) {
            var method = this.relatedAlbums.get(album.getId()) === undefined ? 
                'addAlbum' : 'removeAlbum';

            this[method](album);
        },

        viewAlbums: function() {
            var view = this;

            if (typeof this.albumList === 'undefined') {
                this.albumList = new BoomCMS.AssetManager.AlbumList({
                    albums: this.albums,
                    selected: this.relatedAlbums,
                    $container: $(this.$el[0].ownerDocument)
                });
            }

            this.albumList.render();
            view.$(view.albumsSelector + ' > div').html(this.albumList.el);
        },

        viewSection: function(section) {
            section = section ? section : 'info';

            this.$('.b-settings-menu a, .b-settings-content > div')
                .removeClass('selected')
                .filter('[data-section=' + section + ']')
                .addClass('selected');

            this.section = section;
            this.router.updateSection(section);
        }
    });
}(jQuery, Backbone, BoomCMS));
