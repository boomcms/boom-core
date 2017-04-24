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

        bind: function() {
            var assetManager = this;

            this.selection
                .on('add', function(asset) {
                    setTimeout(function() {
                        assetManager.getThumb(asset).addClass(assetManager.selectedClass);
                    }, 0);
                })
                .on('remove', function(asset) {
                    setTimeout(function() {
                        assetManager.getThumb(asset).removeClass(assetManager.selectedClass);
                    }, 0);
                })
                .on('reset', function() {
                    assetManager.$('.b-assets-thumbnail').removeClass(assetManager.selectedClass);
                });


            this.$el
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
                .on('click', '#b-assets-selection-tag', function() {
                    assetManager.viewSelection('tags');
                })
                .on('click', '#b-assets-upload', function() {
                    assetManager.router.navigate('upload', {trigger: true});
                })
                .on('click', '#b-assets-search', function() {
                    $('#b-assets-filters').toggleClass('visible');
                    $(this).toggleClass('open');
                })
                .on('click', '#b-assets-view-assets', function() {
                    this.router.navigate('', {trigger: true});
                })
                .on('click', '#b-assets-view-albums', function() {
                    this.router.navigate('albums', {trigger: true});

                    assetManager.toggleSearch();
                })                        
                .on('keydown', '.thumb', function(e) {
                    if (e.which === $.ui.keyCode.DELETE || e.which === $.ui.keyCode.BACKSPACE) {
                        e.preventDefault();
                        e.stopPropagation();

                        $(this).parent().data('model').destroy();
                    }
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
                })
                .on('click', '.b-assets-upload-close', function(e) {
                    e.preventDefault();

                    assetManager.router.navigate('', {trigger: true});
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
                .on('route', function(section) {
                    assetManager.setView(section);
                });

            Backbone.history.start();
        },

        getAssets: function() {
            var assetManager = this,
                selection = this.selection,
                deferred = $.Deferred();

            this.$el
                .assetSearch('getAssets')
                .done(function() {
                    var remove = [];

                    // Ensure that any assets in the selection are marked as selected.
                    // If the asset thumbnail isn't in view then remove it from the selection.
                    selection.each(function(asset) {
                        var $thumb = assetManager.getThumb(asset);

                        if ($thumb.length && !$thumb.hasClass(assetManager.selectedClass)) {
                            $thumb.addClass(assetManager.selectedClass);
                        } else if ($thumb.length === 0) {
                            remove.push(asset);
                        }
                    });

                    for (var i = 0; i < remove.length; i++) {
                        selection.remove(remove[i]);
                    }

                    deferred.resolve();
                });

            return deferred;
        },

        getThumb: function(asset) {
            return this.$el.find('.b-assets-thumbnail[data-asset="' + asset.getId() + '"]').addClass('hello');
        },

        initialize: function() {
            var assetManager = this;

            this.$content = this.$('#b-assets-content');
            this.$viewAssetContainer = this.$('#b-assets-view-asset-container');
            this.$viewSelectionContainer = this.$('#b-assets-view-selection-container');
            this.uploader = this.$content.find('> .b-assets-upload .b-assets-upload-form').eq(0);

            this.$el.assetSearch({
                assets: this.assets
            });

            this.router = new BoomCMS.AssetManager.Router({assets: this.assets}); 

            this.getAssets()
                .done(function() {
                    assetManager.filmroll = new BoomCMS.AssetManager.Filmroll({
                        assets: assetManager.assets
                    }).render();

                    assetManager.bindRoutes();
                });

            this.listenTo(this.assets, 'select', this.select);
            this.listenTo(this.assets, 'view', this.viewAsset);

            this.listenTo(this.assets, 'reset', this.assetsChanged);

            this.listenTo(this.assets, 'destroy', function() {
                assetManager.getAssets();
                assetManager.selection.reset();
            });

            this.listenTo(this.selection, 'reset update', this.toggleButtons);
            
            this.bind();
        },

        showAlbums: function() {
            
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


        select: function(asset) {
            var selection = this.selection,
                method = selection.findWhere({id: asset.getId()}) ? 'remove' : 'add';

            selection[method](asset);
        },

        setView: function(section) {
            this.$el.attr('data-view', section);

            if (section !== 'asset') {
                this.activeAsset = null;
            }
        },

        toggleButtons: function() {
            var $buttons = this.$('.b-assets-multi');

            $buttons.prop('disabled', this.selection.length ? false : true);
        },

        toggleSearch: function() {
            this.$el.find('#b-assets-filters').toggleClass('visible');
            this.$el.find('#b-assets-search').toggleClass('open');
        },

        updateTagFilters: function(tags) {
            this.addFilter('tag', tags);
            this.getAssets();
        },

        viewAsset: function(asset, section) {
            this.activeAsset = asset;

            if (section === undefined) {
                section = 'info';
            }

            var view = new BoomCMS.AssetManager.ViewAsset({
                model: asset,
                assets: this.assets,
                router: this.router
            });

            this.router.navigate('asset/' + asset.getId() + '/' + section, {trigger: true});

            this.filmroll.select(asset);

            this.$viewAssetContainer.html(view.render(section).$el);
        },

        viewSelection: function(section) {
            var view = new BoomCMS.AssetManager.ViewSelection({
                selection: this.selection,
                assets: this.assets,
                router: this.router
            });

            this.filmroll.select(this.selection.models[0]);

            this.$viewSelectionContainer.html(view.render(section).$el);
        }
    });
}(Backbone, BoomCMS));
