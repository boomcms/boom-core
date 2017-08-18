(function(BoomCMS) {
    'use strict';

    BoomCMS.AssetPicker = function(currentAsset, filters) {
        this.currentAsset = typeof(currentAsset) === 'object' ? 
            currentAsset : new BoomCMS.Asset();

        this.deferred = new $.Deferred();
        this.document = $(document);

        this.filters = filters ? filters : {};

        this.url = BoomCMS.urlRoot + 'asset-picker';

        this.assetsUploaded = function(assets) {
            var assetPicker = this;

            if (this.activeAlbum) {
                this.activeAlbum.addAssets(assets);
            }

            if (assets.length === 1) {
                this.pick(assets.at(0));
            } else {
                assetPicker.assets.reset();

                assets.each(function(asset) {
                    assetPicker.assets.add(asset);
                });

                this.showAssets();
            }
        };

        this.bind = function() {
            var assetPicker = this;

            this.assets
                .on('select', function(asset) {
                    assetPicker.pick(asset);
                })
                .on('view', function(asset) {
                    window.open('/boomcms/asset-manager#asset/' + asset.getId() + '/info');
                });

            this.picker
                .on('submit', '#b-assets-search form', function(e) {
                    e.preventDefault();

                    var search = $(this).serializeArray(),
                        active = {};

                    for (var i = 0; i < search.length; i++) {
                        if (search[i].value !== '0' && search[i].value !== '') {
                            active[search[i].name] = search[i].value;
                        }
                    }

                    assetPicker.search(active);
                })
                .on('click', '#b-assets-picker-close', function() {
                    assetPicker.cancel();
                })
                .on('click', '#b-assets-picker-current-remove', function() {
                    assetPicker.pick(new BoomCMS.Asset());
                })
                .on('click', '.b-assets-album-list [data-album] a', function(e) {
                    e.preventDefault();

                    assetPicker.viewAlbum($(this).attr('href').replace('#albums/', ''));
                })
                .on('click', '.b-assets-album-list .search a', function(e) {
                    e.preventDefault();

                    var params = $(this).attr('href').replace('#search/', '');

                    assetPicker.search(params.toQueryParams());
                })
                .on('click', '#b-assets-picker-albums', function(e) {
                    e.preventDefault();

                    assetPicker.viewAlbumsList();
                })
                .find('.b-assets-upload')
                .assetUploader({
                    dropArea: this.picker,
                    uploadFinished: function(e, data) {
                        assetPicker.assetsUploaded(new BoomCMS.Collections.Assets(data.result.assets));
                    }
                });
        };

        this.cancel = function() {
            this.deferred.reject();
            this.dialog.cancel();
        };

        this.close = function() {
            this.dialog.cancel();
        };

        this.hideCurrentAsset = function() {
            this.picker
                .find('#b-assets-picker-current')
                .hide();
        };

        this.loadPicker = function() {
            var assetPicker = this;

            this.assets = new BoomCMS.Collections.Assets();
            this.albums = new BoomCMS.Collections.Albums();

            this.albums.fetch();

            this.dialog = new BoomCMS.Dialog({
                url: this.url,
                onLoad: function() {
                    assetPicker.dialog.contents.parent().css({
                        position: 'fixed',
                        height: '100vh',
                        width: '100vw',
                        transform: 'none',
                        overflow: 'visible'
                    });

                    new BoomCMS.AssetManager.AlbumList({
                        albums: assetPicker.albums,
                        el: assetPicker.dialog.contents.find('.b-assets-album-list'),
                        $container: assetPicker.dialog.contents.find('#b-assets-picker-content')
                    }).render();

                    assetPicker.picker = assetPicker.dialog.contents.find('#b-assets-picker');

                    if (typeof(assetPicker.filters.type) !== 'undefined') {
                        assetPicker.showActiveTypeFilter(assetPicker.filters.type);
                    }

                    assetPicker.bind();

                    if (assetPicker.currentAsset && assetPicker.currentAsset.getId() > 0) {
                        assetPicker.picker
                            .find('#b-assets-picker-current img')
                            .attr('src', assetPicker.currentAsset.getUrl('thumb'));
                    } else {
                        assetPicker.hideCurrentAsset();
                    }
                }
            });
        };

        this.open = function() {
            this.loadPicker();

            return this.deferred;
        };

        this.pick = function(asset) {
            this.deferred.resolve(asset);

            this.close();
        };

        this.search = function(params) {
            var assetPicker = this,
                $pagination = this.picker.find('#b-assets-pagination');

            this.activeAlbum = null;

            if (this.searchResultsView === undefined) {
                this.searchResultsView = new BoomCMS.AssetManager.SearchResults({
                    el: this.picker,
                    pagination: $pagination,
                    assets: this.assets,
                    params: params,
                    selection: new BoomCMS.Collections.Assets(),
                    $container: this.dialog.contents.find('#b-assets-picker-content')
                });

                this.searchResultsView.on('filtered', function(params) {
                    assetPicker.search(params);
                });
            } else {
                this.searchResultsView.setParams(params);
            }

            this.searchResultsView.getAssets();

            $pagination.show();

            this.picker.attr('data-view', 'assets');
        },

        this.showAssets = function() {
            new BoomCMS.AssetManager.ThumbnailGrid({
                assets: this.assets,
                selection: new BoomCMS.Collections.Assets(),
                el: this.picker.find('.b-assets-view-thumbs'),
                $container: this.dialog.contents.find('#b-assets-picker-content')
            }).render();

            this.picker.attr('data-view', 'assets');
        };

        /**
         * Selects an option in the type filter select box to show that the type filter is active.
         * Used when the asset picker is opened with an active type filter.
         *
         * @param {string} type
         * @returns {BoomCMS.AssetPicker.prototype}
         */
        this.showActiveTypeFilter = function(type) {
            var $types = this.dialog.contents.find('#b-assets-types');

            $types.find('option').each(function() {
                var $this = $(this);

                if ($this.val().toLowerCase() === type.toLowerCase()) {
                    $types.val($this.val());
                }
            });

            return this;
        };

        this.viewAlbumsList = function() {
            this.picker.removeAttr('data-view');
            this.picker.find('#b-assets-pagination').hide();
        };

        this.viewAlbum = function(slug) {
            var view = this,
                album = this.albums.findBySlug(slug);

            this.activeAlbum = album;

            this.assets.fetch({
                data: {
                    album: album.getId()
                },
                reset: true,
                complete: function() {
                    view.showAssets();
                }
            });
            
            this.picker.find('#b-assets-pagination').hide();
        };

        return this.open();
    };
}(BoomCMS));
