(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager = Backbone.View.extend({
        el: 'body',

        activeAsset: null,
        assets: new BoomCMS.Collections.Assets(),
        selection: new BoomCMS.Collections.Assets(),

        types: {},

        selectedClass: 'selected',
        hideThumbsClass: 'hide-thumbs',

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
            this.uploader.assetUploader('close');
            this.uploader.assetUploader('reset');

            this.getAssets();
        },

        bind: function() {
            var assetManager = this;

            this.selection
                .on('add', function(asset) {
                    assetManager.getThumb(asset).addClass(assetManager.selectedClass);
                })
                .on('remove', function(asset) {
                    assetManager.getThumb(asset).removeClass(assetManager.selectedClass);
                });


            this.$el
                .on('click', '#b-assets-selection-delete', function() {
                    assetManager.viewSelection(assetManager.selection, 'delete');
                })
                .on('click', '#b-assets-selection-download', function() {
                    assetManager.viewSelection(assetManager.selection, 'download');
                })
                .on('click', '#b-assets-select-all', function(e) {
                    e.preventDefault();

                    assetManager.selectAll();

                    $(this).blur();
                })
                .on('click', '#b-assets-select-none', function(e) {
                    e.preventDefault();

                    assetManager.clearSelection();

                    $(this).blur();
                })
                .on('click', '#b-assets-selection-tag', function() {
                    assetManager.viewSelection(assetManager.selection, 'tags');
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
                                    assetManager.clearSelection()
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

                    assetManager.uploader.assetUploader('close');
                    assetManager.router.navigate('', {trigger: true});
                });
        },

        bindRoutes: function() {
            var assetManager = this;

            this.router
                .on('selection', function(assetIds, section) {
                    assetManager.selection.reset();

                    for (var i = 0; i < assetIds.length; i++) {
                        var asset = new BoomCMS.Asset({id: assetIds[i]});
                        asset.fetch();

                        assetManager.selection.add(asset);
                    }

                    assetManager.viewSelection(assetManager.selection, section);
                })
                .on('upload', function() {
                    assetManager.uploader.show();
                    assetManager.hideThumbs();
                })
                .on('home', function() {
                    if (assetManager.assets.length <= 1) {
                        assetManager.getAssets();
                    }

                    assetManager.showThumbs();
                })
                .on('albums', function() {
                    assetManager.showAlbums();
                });

            Backbone.history.start();
        },

        clearSelection: function() {
            this.selection.reset();

            This.$('.b-assets-thumbnail').removeClass(this.selectedClass);
        },

        getAssets: function() {
            var assetManager = this,
                selection = this.selection;

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
                });
        },

        getThumb: function(asset) {
            return this.$el.find('.b-assets-thumbnail[data-asset="' + asset.getId() + '"]');
        },

        hideThumbs: function() {
            this.$content.addClass(this.hideThumbsClass);
        },

        initialize: function() {
            var assetManager = this;

            this.$content = this.$('#b-assets-content');
            this.uploader = this.$content.find('> .b-assets-upload .b-assets-upload-form').eq(0);

            this.filmroll = new BoomCMS.AssetManager.Filmroll({
                assets: this.assets
            });

            this.$content.append(this.filmroll.$el);

            this.$el.assetSearch({
                assets: this.assets
            });

            this.router = new BoomCMS.AssetManager.Router({assets: this.assets}); 
            this.bindRoutes();

            this.getAssets();

            this.listenTo(this.assets, 'select', this.select);
            this.listenTo(this.assets, 'view', this.viewAsset);

            this.listenTo(this.assets, 'reset', function() {
                assetManager.$content.find('#b-assets-filmroll').remove();
                assetManager.$content.append(assetManager.filmroll.render().$el);
                assetManager.filmroll.initFilmroll();
            });

            this.listenTo(this.assets, 'reset', this.assetsChanged);

            this.listenTo(this.assets, 'destroy', function() {
                assetManager.getAssets();
                assetManager.clearSelection();
            });

            this.listenTo(this.selection, 'reset update', this.toggleButtons);

            this.bind();
        },

        showAlbums: function() {
            
        },

        showThumbs: function() {
            this.activeAsset = null;

            this.$content.removeClass(this.hideThumbsClass);
            this.uploader.hide();
            this.filmroll.hide();
        },

        selectAll: function() {
            var assetManager = this;

            this.assets.each(function(asset) {
                assetManager.selection.add(asset);
            });

            this.$('#b-assets-view-thumbs .thumb').addClass(this.selectedClass);
        },


        select: function(asset) {
            var selection = this.selection,
                method = selection.findWhere({id: asset.getId()}) ? 'remove' : 'add';

            selection[method](asset);
        },

        toggleButtons: function() {
            var $buttons = this.$('.b-assets-multi');

            $buttons.prop('disabled', this.selection.length ? false : true);
        },

        toggleSearch: function() {
            this.$el.find('#b-assets-filters').toggleClass('visible');
            this.$el.find('#b-assets-search').toggleClass('open');
        },

        view: function(view, section) {
            view.render(section);
            this.$content.find('.b-assets-view').parent().remove();
            this.$content.prepend(view.$el);
            this.hideThumbs();
        },

        viewAsset: function(asset, section) {
            this.activeAsset = asset;

            if (section === undefined) {
                section = 'info';
            }

            var filmroll = this.filmroll,
                view = new BoomCMS.AssetManager.ViewAsset({
                    model: asset,
                    assets: this.assets,
                    router: this.router
                });

            this.router
                .navigate('asset/' + asset.getId() + '/' + section)
                .once('home', function() {
                    view.close();
                });

            setTimeout(function() {
                filmroll.select(asset).show();
            }, 0);

            this.view(view, section);
        },

        viewSelection: function(selection, section) {
            this.activeAsset = null;

            var view = new BoomCMS.AssetManager.ViewSelection({
                selection: selection,
                assets: this.assets,
                router: this.router
            });

            this.router
                .navigate('selection/' + selection.getIdString() + '/' + section)
                .once('home', function() {
                    view.close();
                });

            this.view(view, section);
        }
    });
}(Backbone, BoomCMS));
