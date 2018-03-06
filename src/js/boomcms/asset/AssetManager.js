(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager = Backbone.View.extend({
        el: '#b-assets-manager',

        albumList: null,
        albumViews: [],
        assetViews: [],
        assets: new BoomCMS.Collections.Assets(),

        // Map of asset collections which have had event listeners bound.
        assetCollections: {},

        selection: new BoomCMS.Collections.Assets(),
        uploaded: new BoomCMS.Collections.Assets().setOrderBy('created_at', 'desc'),
        selectedClass: 'selected',

        assetsUploaded: function() {
            new BoomCMS.AssetManager.ThumbnailGrid({
                assets: this.uploaded,
                selection: this.selection,
                el: this.$('#b-assets-upload .b-assets-view-thumbs'),
                $container: this.$el
            }).render();
        },

        bindAssetEvents: function(assets) {
            var assetManager = this;

            if (typeof this.assetCollections[assets._listenId] === 'undefined') {
                this.listenTo(assets, 'select', function(asset) {
                    assetManager.selection.add(asset);
                });

                this.listenTo(assets, 'unselect', function(asset) {
                    assetManager.selection.remove(asset);
                });

                this.listenTo(assets, 'view', function(asset) {
                    assetManager.router.goToAsset(asset);
                });

                this.listenTo(assets, 'destroy', function(asset) {
                    assetManager.removeFromAllCollections(asset.getId());
                });

                this.assetCollections[assets._listenId] = assets;
            }
        },

        bind: function() {
            var assetManager = this;

            this.$el
                .on('submit', '#b-assets-search form', function(e) {
                    e.preventDefault();

                    var search = $(this).serializeArray(),
                        active = {};

                    for (var i = 0; i < search.length; i++) {
                        if (search[i].value !== '0' && search[i].value !== '') {
                            active[search[i].name] = search[i].value;
                        }
                    }

                    assetManager.router.goToSearchResults(active);
                })
                .on('click', '#b-assets-all-albums a', function(e) {
                    e.preventDefault();

                    assetManager.router.goTo($(this).attr('href').replace('#', ''));
                })
                .on('click', '#selection-controls button', function() {
                    var action = $(this).attr('data-selection');

                    assetManager.router.updateSelection(assetManager.selection, action, {trigger: true});
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
                .on('click', '[data-view]', function(e) {
                    e.preventDefault();

                    assetManager.router.goTo($(this).attr('data-view'));

                    $('#common-upload-form').fileupload('enable');
                })
                .parents('body')
                .on('keydown', function(e) {
                    if ($(e.target).is('input')) {
                        return;
                    }

                    if (
                        (e.which === $.ui.keyCode.DELETE || e.which === $.ui.keyCode.BACKSPACE)
                        && assetManager.selection.models.length > 0
                    ) {
                        assetManager.router.viewSelection(assetManager.selection.getIdString(), 'delete');
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
                                assetManager.router.navigate('search', {trigger: true});
                                break;
                        }
                    }
                });

            this.$('#b-assets-search input[name=text]').assetNameAutocomplete();

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
                .on('route', function(section) {
                    assetManager.setView(section);
                    assetManager.scrollToTop();
                })
                .on('route:viewAsset', function(assetId, section) {
                    assetManager.viewAsset(assetId, section);
                })
                .on('route:viewAssetInUpload', function(assetId, section) {
                    assetManager.assets = assetManager.uploaded;
                    assetManager.viewAsset(assetId, section);
                })
                .on('route:viewSelectionInUpload', function(selection, section) {
                    assetManager.assets = assetManager.uploaded;
                    assetManager.viewSelection(selection.split(','), section);
                })
                .on('route:viewAssetInAlbum', function(album, assetId, section) {
                    album = assetManager.albums.findBySlug(album);

                    assetManager.loadAlbum(album, function() {
                        assetManager.viewAsset(assetId, section);
                    });
                })
                .on('route:viewSelectionInAlbum', function(album, selection, section) {
                    album = assetManager.albums.findBySlug(album);

                    assetManager.loadAlbum(album, function() {
                        assetManager.viewSelection(selection.split(','), section);
                    });
                })
                .on('route:viewAssetInSearch', function(queryString, assetId, section) {
                    var asset = assetManager.assets.findWhere({id: parseInt(assetId)});

                    if (asset === undefined) {
                        assetManager.viewSearchResults(queryString.toQueryParams());

                        assetManager.assets.on('sync', function() {
                            assetManager.viewAsset(assetId, section);
                        });
                    } else {
                        assetManager.viewAsset(assetId, section);
                    }
                })
                .on('route:viewSelectionInSearch', function(queryString, selection, section) {
                    if (assetManager.assets.length === 0) {
                        assetManager.viewSearchResults(queryString.toQueryParams());

                        assetManager.assets.on('sync', function() {
                            assetManager.viewSelection(selection.split(','), section);
                        });
                    } else {
                        assetManager.viewSelection(selection.split(','), section);
                    }
                })
                .on('route:viewAlbum', function(slug) {
                    assetManager.viewAlbum(slug);
                })
                .on('route:home', function() {
                    assetManager.selectNone();
                    assetManager.showAlbums();
                })
                .on('route:createAlbum', function() {
                    assetManager.$('#b-assets-view-album-container h1').text('Untitled');
                })
                .on('viewSearchResults', function(params) {
                    assetManager.viewSearchResults(params);
                });

            Backbone.history.start({
                pushState: true,
                root: '/boomcms/asset-manager'
            });
        },

        initialize: function(options) {
            var assetManager = this;

            this.albums = options.albums;

            this.uploader = this.$('#common-upload-form');
            this.router = new BoomCMS.AssetManager.Router(); 

            this.listenTo(this.albums, 'add remove reset', this.showAlbums);
            this.listenTo(this.selection, 'reset update', this.toggleButtons);

            this.listenTo(this.selection, 'destroy-all', function(assetIds) {
                for (var i = 0; i < assetIds.length; i++) {
                    assetManager.removeFromAllCollections(assetIds[i]);
                }
            });

            this.bindAssetEvents(this.uploaded);
            this.listenTo(this.uploaded, 'add', this.assetsUploaded);

            this.bind();
            this.bindRoutes();
        },

        loadAlbum: function(album, success) {
            if (album) {
                this.selectNone();

                if (!album.isNew()) {
                    this.assets = album.getAssets();
                    this.bindAssetEvents(this.assets);

                    if (album.getAssetCount() > 0) {
                        this.assets.fetchOnce(success);
                    } else if (typeof success === 'function') {
                        success();
                    }
                }
            }
        },

        /**
         * Remove an asset from all album collections and search.
         *
         * An asset can appear in collections for multiple albums
         * This ensures that when an asset is deleted all other albums are kept in sync 
         * And their thumbnail lists updated
         */
        removeFromAllCollections: function(assetId) {
            this.albums.each(function(album) {
                album.getAssets().removeIfExists(assetId);
            });

            if (this.searchResultsView !== undefined) {
                this.searchResultsView.assets.removeIfExists(assetId);
            }

            this.selection.removeIfExists(assetId);
            this.uploaded.removeIfExists(assetId);
        },

        scrollToTop: function() {
            $('html,body').scrollTop(0);
        },

        showAlbums: function() {
            if (this.albumList === null) {
                this.albumList = new BoomCMS.AssetManager.AlbumList({
                    albums: this.albums,
                    $container: $(this.$el[0].ownerDocument)
                });

                this.$('#b-assets-all-albums > .content').html(this.albumList.render().el);
            }
        },

        selectAll: function() {
            this.assets.each(function(asset) {
                asset.trigger('select', asset);
            });
        },

        selectNone: function() {
            var selectedIds = this.selection.getAssetIds();

            if (selectedIds.length === 0) {
                return;
            }

            // Ensure that the unselect event is triggered for equivalent models in all collections.
            for (var key in this.assetCollections) {
                var assets = this.assetCollections[key];

                for (var i = 0; i < selectedIds.length; i++) {
                    var asset = assets.findById(selectedIds[i]);

                    if (asset) {
                        asset.trigger('unselect', asset);
                    }
                };
            }

            this.selection.reset();
        },

        setView: function(section) {
            if (section === 'viewAssetInAlbum' || section === 'viewAssetInSearch' || section === 'viewAssetInUpload') {
                section = 'viewAsset';
            }

            if (section === 'viewSelectionInAlbum' || section === 'viewSelectionInSearch' || section === 'viewSelectionInUpload') {
                section = 'viewSelection';
            }

            this.$el.attr('data-view', section);

            this.$('button[data-view]')
                .removeClass('active')
                .filter('[data-active="' + section + '"]')
                .addClass('active');
        },

        showUploadErrors: function(errors) {
            var $errors = this.$('#b-assets-upload .errors');

            for (var i = 0; i < errors.length; i++) {
                var $li = $('<li></li>').text(errors[i]);

                $errors.find('ul').append($li);
            } 

            $errors.show();
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
            for (var i = 0; i < assets.length; i++) {
                this.uploaded.add(new BoomCMS.Asset(assets[i]));
            }

            // Force any existing, cached search to be re-done.
            // Otherwise if the user searched for most recent assets before uploading
            // the new asset won't appear in the search results if they go back.
            if (this.searchResultsView !== undefined) {
                this.searchResultsView.forceUpdate();
            }

            if (errors.length > 0) {
                this.showUploadErrors(errors);
            }

            this.assets = this.uploaded;
            this.$el.addClass('has-uploads');

            // Reload the album after uploads
            if(this.searchResultsView !== undefined) {
                this.searchResultsView.getAssets();
            }
        },

        viewAlbum: function(slug) {
            var albums = this.albums,
                album = slug ? albums.findBySlug(slug) : new BoomCMS.Album();

            this.loadAlbum(album);

            if (album) {
                if (this.albumViews[slug] === undefined) {
                    this.albumViews[slug] = new BoomCMS.AssetManager.ViewAlbum({
                        model: album,
                        albums: this.albums,
                        router: this.router,
                        selection: this.selection,
                        $container: $(this.$el[0].ownerDocument)
                    }).render();
                }

                this.$('#b-assets-view-album-container')
                    .children()
                    .detach()
                    .end()
                    .html(this.albumViews[slug].el)
                    .parents('#b-assets-content')
                    .scrollTop();
            }
        },

        viewAsset: function(assetId, section) {
            var assetManager = this,
                asset = this.assets.getOrFetch(assetId);

            if (this.assetViews[assetId] === undefined) {
                this.assetViews[assetId] = new BoomCMS.AssetManager.ViewAsset({
                    model: asset,
                    router: this.router,
                    albums: this.albums,
                    section: section
                }).render();
            }

            this.$('#b-assets-view-asset-container')
                .children()
                .detach()
                .end()
                .html(this.assetViews[assetId].el);

            this.assetViews[assetId].viewSection(section);

            setTimeout(function() {
                var $el = assetManager.assetViews[assetId].$el.find('.b-assets-view');

                if (assetManager.assets.length > 1) {
                    assetManager.viewNavigation(asset);
                    $el.removeClass('no-navigation');
                } else {
                    $el.addClass('no-navigation');
                }
            }, 0);
        },

        viewNavigation: function(active) {
            var position = this.assets.position(active),
                assets = this.assets,
                navStart = position > 10 ? position - 10 : 0,
                navEnd = (position < assets.length - 11) ? position + 11 : assets.length,
                navigationAssets = new BoomCMS.Collections.Assets(assets.slice(navStart, navEnd));

            if (this.navigation === undefined) {
                this.navigation = new BoomCMS.AssetManager.Navigation({
                    selection: this.selection
                });
            }

            this.navigation.render(navigationAssets, active);
        },

        viewSearchResults: function(params) {
            var assetManager = this;

            this.selectNone();

            if (this.searchResultsView === undefined) {
                var router = this.router;

                this.assets = new BoomCMS.Collections.Assets();
                this.bindAssetEvents(this.assets);

                this.searchResultsView = new BoomCMS.AssetManager.SearchResults({
                    el: this.$('#b-assets-search-results'),
                    pagination: this.$('#b-assets-pagination'),
                    assets: this.assets,
                    params: params,
                    selection: this.selection,
                    $container: $(this.$el[0].ownerDocument)
                });

                this.searchResultsView.on('filtered', function(params) {
                    router.goToSearchResults(params);
                });

                this.searchResultsView.getAssets();
            } else {
                this.assets = this.searchResultsView.assets;

                if (this.searchResultsView.setParams(params) === true) {
                    this.searchResultsView.getAssets();
                } else {
                    // If a user navigates directly to an asset within a search results list
                    // The thumbnail grid won't have been visible to be justified,
                    // so if have to do it manually when the search results page is viewed (i.e. the asset view is closed)
                    // The setTimeout ensures the thumbnails are visible before justificiation
                    setTimeout(function() {
                        assetManager.searchResultsView.justifyThumbnails();
                    }, 0);
                }
            }
        },

        viewSelection: function(assetIds, section) {
            this.selection.reset();

            for (var i = 0; i < assetIds.length; i++) {
                var asset = this.assets.getOrFetch(assetIds[i]);
                this.selection.add(asset);
            }

            var view = new BoomCMS.AssetManager.ViewSelection({
                selection: this.selection,
                router: this.router,
                albums: this.albums,
                section: section
            });

            this.$('#b-assets-view-selection-container').html(view.render().el);
        }
    });
}(Backbone, BoomCMS));
