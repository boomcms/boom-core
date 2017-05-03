(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager = Backbone.View.extend({
        el: '#b-assets-manager',

        activeAsset: null,
        assets: new BoomCMS.Collections.Assets(),
        selection: new BoomCMS.Collections.Assets(),
        uploaded: new BoomCMS.Collections.Assets(),
        selectedClass: 'selected',

        /**
         * When the assets in the collection change (e.g. the page or filters are changed)
         *
         * If an asset is being viewed which isn't in the collection
         * Then view the first asset in the collection instead
         *
         * @returns {undefined}
         */
        assetsChanged: function() {
            if (this.activeAsset !== null && this.assets.get(this.activeAsset) === undefined) {
                var first = this.assets.at(0);

                if (first) {
                    first.trigger('view', first);
                }
            }
        },

        assetsUploaded: function() {
            new BoomCMS.AssetManager.ThumbnailGrid({
                assets: this.uploaded,
                selection: this.selection,
                el: this.$('#b-assets-upload .b-assets-view-thumbs')
            }).render();
        },

        bindAssetEvents: function(assets) {
            var assetManager = this;

            this.stopListening(assets);

            this.listenTo(assets, 'select', function(asset) {
                assetManager.selection.add(asset);
            });

            this.listenTo(assets, 'unselect', function(asset) {
                assetManager.selection.remove(asset);
            });

            this.listenTo(assets, 'reset', this.assetsChanged);

            this.listenTo(assets, 'view', function(asset) {
                assetManager.router.goToAsset(asset);
            });

            this.listenTo(assets, 'destroy', function() {
                assetManager.router.navigate('', {trigger: true});
                assetManager.selection.reset();
            });  
        },

        bind: function() {
            var assetManager = this;

            this.$el
                .on('submit', '#b-assets-search form', function(e) {
                    e.preventDefault();

                    var search = $(this).serializeArray();

                    assetManager.router.goToSearchResults(search);
                })
                .on('click', '#b-assets-selection-delete', function() {
                    assetManager.router.updateSelection(assetManager.selection, 'delete', {trigger: true});
                })
                .on('click', '#b-assets-selection-download', function() {
                    assetManager.router.updateSelection(assetManager.selection, 'download', {trigger: true});
                })
                .on('click', '#b-assets-selection-albums', function() {
                    assetManager.router.updateSelection(assetManager.selection, 'albums', {trigger: true});
                })
                .on('click', '#b-assets-select-all', function(e) {
                    e.preventDefault();

                    assetManager.selectAll();

                    $(this).blur();
                })
                .on('click', '#b-assets-select-none', function(e) {
                    e.preventDefault();

                    assetManager.selectNone();

                    $(this).blur();
                })
                .on('click', '#b-assets-selection-albums', function() {
                    assetManager.viewSelection('albums');
                })
                .on('click', '#b-assets-upload', function() {
                    assetManager.router.navigate('upload', {trigger: true});
                })
                .on('click', '[data-view]', function(e) {
                    e.preventDefault();

                    assetManager.router.goTo($(this).attr('data-view'));
                })
                .on('keydown', function(e) {
                    if ($(e.target).is('input')) {
                        return;
                    }

                    if (
                        (e.which === $.ui.keyCode.DELETE || e.which === $.ui.keyCode.BACKSPACE)
                        && assetManager.selection.models.length > 0
                    ) {
                        assetManager.viewSelection(assetManager.selection, 'delete');
                    }

                    if (e.metaKey || e.ctrlKey) {
                        switch (e.which) {
                            case 65:
                                e.preventDefault();

                                (assetManager.selection.length === assetManager.assets.length) ?
                                    assetManager.selectNone()
                                    : assetManager.selectAll();

                                break;
                            case 70:
                                e.preventDefault();
                                assetManager.toggleSearch();
                                break;
                        }
                    }
                });

            this.uploader
                .assetUploader({
                    uploadFinished: function(e, data) {
                        assetManager.uploadCompleted(data.result.assets, data.result.errors);
                    }
                });
        },

        bindRoutes: function() {
            var assetManager = this,
                assets = this.assets;

            this.router
                .on('selection', function(assetIds, section) {
                    assetManager.selection.reset();

                    for (var i = 0; i < assetIds.length; i++) {
                        var asset = assets.getOrFetch(assetIds[i]);
                        assetManager.selection.add(asset);
                    }

                    assetManager.viewSelection(section);
                })
                .on('route', function(section) {
                    assetManager.setView(section);
                })
                .on('route:viewAsset', function(assetId, section) {
                    assetManager.viewAsset(assetId, section);
                })
                .on('route:viewAssetInAlbum', function(album, assetId, section) {
                    album = assetManager.albums.findBySlug(album);

                    if (album) {
                        assetManager.assets = album.getAssets();
                        assetManager.bindAssetEvents(album.getAssets());

                        if (assetManager.assets.length > 0) {
                            assetManager.viewFilmroll();
                            assetManager.viewAsset(assetId, section);
                        } else {
                            assetManager.assets.once('sync', function() {
                                assetManager.viewFilmroll();
                                assetManager.viewAsset(assetId, section);
                            });
                        }
                    }
                })
                .on('route:viewAlbum', function(slug) {
                    assetManager.viewAlbum(slug);
                })
                .on('route:home', function() {
                    assetManager.showAlbums();
                })
                .on('viewSearchResults', function(params) {
                    assetManager.viewSearchResults(params);
                });

            Backbone.history.start();
        },

        initialize: function(options) {
            this.albums = options.albums;

            this.uploader = this.$('#b-assets-upload form');
            this.router = new BoomCMS.AssetManager.Router({assets: this.assets}); 

            this.listenTo(this.albums, 'add remove reset', this.showAlbums);
            this.listenTo(this.selection, 'reset update', this.toggleButtons);

            this.bindAssetEvents(this.uploaded);
            this.listenTo(this.uploaded, 'add', this.assetsUploaded);

            this.bind();
            this.bindRoutes();
        },

        showAlbums: function() {
            var $el = this.$('#b-assets-all-albums > div');

            if (!$el.html()) {
                var view = new BoomCMS.AssetManager.AlbumList({
                    albums: this.albums
                });

                $el.html(view.render().el);
            }
        },

        selectAll: function() {
            var assetManager = this;

            this.assets.each(function(asset) {
                assetManager.selection.add(asset);
            });
        },

        selectNone: function() {
            this.selection.reset();
        },

        setView: function(section) {
            if (section === 'home') {
                section = '';
            }

            if (section === 'viewAssetInAlbum') {
                section = 'viewAsset';
            }

            this.$el.attr('data-view', section);

            this.$('button[data-view]')
                .removeClass('active')
                .filter('[data-view="' + section + '"]')
                .addClass('active');

            if (section !== 'asset') {
                this.activeAsset = null;
            }
        },

        toggleButtons: function() {
            var $controls = this.$('#selection-controls'),
                $buttons = $controls.find('button');

            if (this.selection.length > 0) {
                $controls.addClass('visible');
                $buttons.prop('disabled', false);
            } else {
                $controls.removeClass('visible');
                $buttons.prop('disabled', true);
            }
        },

        uploadCompleted: function(assets, errors) {
            var $errors = this.$('#b-assets-upload .errors');

            for (var i = 0; i < assets.length; i++) {
                this.uploaded.add(new BoomCMS.Asset(assets[i]));
            }

            for (var i = 0; i < errors.length; i++) {
                var $li = $('<li></li>').text(errors[i]);

                $errors.find('ul').append($li);
            } 

            $errors.show();
        },

        viewAlbum: function(slug) {
            var album = this.albums.findBySlug(slug);

            if (album) {
                this.selection.reset();

                this.assets = album.getAssets();
                this.bindAssetEvents(this.assets);

                var view = new BoomCMS.AssetManager.ViewAlbum({
                    model: album,
                    router: this.router
                });

                this.$('#b-assets-view-album-container').html(view.render().el);
                this.viewFilmroll();
            }
        },

        viewAsset: function(assetId, section) {
            var asset = this.assets.getOrFetch(assetId);

            this.activeAsset = asset;

            if (section === undefined) {
                section = 'info';
            }

            var view = new BoomCMS.AssetManager.ViewAsset({
                model: asset,
                router: this.router,
                albums: this.albums,
                section: section
            });

            this.$('#b-assets-view-asset-container').html(view.render().el);

            if (this.filmroll) {
                this.filmroll.select(asset);
            }
        },

        viewFilmroll: function() {
            this.filmroll = new BoomCMS.AssetManager.Filmroll({
                assets: this.assets
            }).render();
        },

        viewSearchResults: function(params) {
            new BoomCMS.AssetManager.SearchResults({
                el: this.$('#b-assets-search-results'),
                pagination: this.$('#b-assets-pagination'),
                assets: this.assets,
                params: params
            });

            this.bindAssetEvents(this.assets);
            this.viewFilmroll();
        },

        viewSelection: function(section) {
            var view = new BoomCMS.AssetManager.ViewSelection({
                selection: this.selection,
                router: this.router,
                albums: this.albums,
                section: section
            });

            this.$('#b-assets-view-selection-container').html(view.render().el);
            this.viewFilmroll();

            if (this.filmroll) {
                this.filmroll.select(this.selection.models[0]);
            }
        }
    });
}(Backbone, BoomCMS));
