(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager = Backbone.View.extend({
        el: '#b-assets-manager',

        activeAsset: null,
        assets: new BoomCMS.Collections.Assets(),
        selection: new BoomCMS.Collections.Assets(),
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
            this.router.navigate('', {trigger: true});
            this.uploader.assetUploader('reset');
            this.getAssets();
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
                        assetManager.assetsUploaded(data.result);
                    },
                    uploadFailed: function() {
                        // Update asset list even though an error occurred
                        // For situations where multiple files were uploaded but one caused an error.
                        assetManager.getAssets();
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
                        var asset = assets.get(assetIds[i]);

                        if (asset === undefined) {
                            asset = new BoomCMS.Asset({id: assetIds[i]});
                            asset.fetch({
                                success: function() {
                                    assetManager.selection.add(asset);
                                }
                            });
                        } else {
                            assetManager.selection.add(asset);
                        }
                    }

                    assetManager.viewSelection(section);
                })
                .on('route:viewAsset', function(assetId, section) {
                    assetManager.viewAsset(assetId, section);
                })
                .on('route:viewAlbum', function(slug) {
                    assetManager.viewAlbum(slug);
                })
                .on('route:home', function() {
                    assetManager.showAlbums();
                })
                .on('viewSearchResults', function(params) {
                    assetManager.viewSearchResults(params);
                })
                .on('route', function(section) {
                    assetManager.setView(section);
                });

            Backbone.history.start();
        },

        initialize: function(options) {
            this.albums = options.albums;
            this.uploader = this.$('#b-assets-upload .b-assets-upload-form');

            this.router = new BoomCMS.AssetManager.Router({assets: this.assets}); 

            this.listenTo(this.albums, 'add remove reset', this.showAlbums);

            this.listenTo(this.selection, 'reset update', this.toggleButtons);

            this.bind();
            this.bindRoutes();
        },

        showAlbums: function() {
            var $el = this.$('#b-assets-all-albums-container');

            if (!$el.html()) {
                new BoomCMS.AssetManager.AllAlbums({
                    albums: this.albums,
                    el: $el
                }).render();
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
            var $buttons = this.$('.b-assets-multi');

            $buttons.prop('disabled', this.selection.length ? false : true);
        },

        viewAlbum: function(slug) {
            var album = this.albums.findBySlug(slug);

            if (album) {
                this.selection.reset();

                this.assets = album.getAssets();
                this.bindAssetEvents(album.getAssets());

                new BoomCMS.AssetManager.ViewAlbum({
                    model: album,
                    el: this.$('#b-assets-view-album-container'),
                    router: this.router
                }).render();

                this.viewFilmroll();
            }
        },

        viewAsset: function(assetId, section) {
            var asset = this.assets.get(assetId);

            if (asset === undefined) {
                asset = new BoomCMS.Asset({id: assetId});
                asset.fetch();

                this.assets.add(asset);
            }

            this.activeAsset = asset;

            if (section === undefined) {
                section = 'info';
            }

            new BoomCMS.AssetManager.ViewAsset({
                model: asset,
                assets: this.assets,
                router: this.router,
                el: this.$('#b-assets-view-asset-container')
            }).render();

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

            this.viewFilmroll();
        },

        viewSelection: function(section) {
            new BoomCMS.AssetManager.ViewSelection({
                selection: this.selection,
                assets: this.assets,
                router: this.router,
                el: this.$('#b-assets-view-selection-container')
            }).render();

            this.filmroll.select(this.selection.models[0]);
        }
    });
}(Backbone, BoomCMS));
