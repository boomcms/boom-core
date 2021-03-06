(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.SearchResults = BoomCMS.AssetManager.ViewSelection.extend({
        updateForced: false,
        page: 1,
        params: {},
        requestTimeout: null,

        bind: function() {
            var assetSearch = this;

            this.$el
                .parents('body')
                .on('keydown', function(e) {
                    switch (e.which) {
                        case $.ui.keyCode.LEFT:
                            assetSearch.previousPage();
                            break;
                        case $.ui.keyCode.RIGHT:
                            assetSearch.nextPage();
                            break;
                    }
                });

            this.listenTo(this.assets, 'destroy remove', function() {
                assetSearch.forceUpdate().getAssets();
            });
        },

        initialize: function(options) {
            this.assets = options.assets;
            this.$pagination = options.pagination;
            this.router = options.router;
            this.selection = options.selection;
            this.$container = options.$container;

            this.assets.comparator = null;

            this.thumbnails = new BoomCMS.AssetManager.ThumbnailGrid({
                el: this.$('.b-assets-view-thumbs'),
                assets: this.assets,
                selection: this.selection,
                $container: this.$container
            });

            this.bind();
            this.setAssetsPerPage();
            this.setParams(options.params);
        },

        /**
         * Force search results to be updated even if the parameters haven't changed.
         */
        forceUpdate: function() {
            this.updateForced = true;

            return this;
        },

        getAssets: function() {
            var searchResults = this;

            if (this.requestTimeout !== null) {
                clearTimeout(this.requestTimeout);
            }

            // Wrap the request in a timeout to avoid stale HTTP requests when user rapidly moves through pages.
            this.requestTimeout = setTimeout(function() {
                searchResults.sendGetAssetsRequest();
            }, 300);
        },

        getPage: function(page) {
            var params = {};

            for (var key in this.params) {
                params[key] = this.params[key];
            }

            params.page = page;

            this.trigger('filtered', params);
        },

        initPagination: function() {
            var view = this;

            this.lastPage = Math.ceil(this.assets.total / this.perpage);

            if (this.$pagination.data('jqPagination') !== undefined) {
                this.$pagination.jqPagination('destroy');
            }

            this.$pagination.jqPagination({
                paged: function(page) {
                    view.getPage(page);
                },
                max_page: this.lastPage,
                current_page: this.assets.total > 0 ? this.page : 0
            });

            this.$pagination.data('jqPagination').updateInput(true);
        },

        justifyThumbnails: function() {
            this.thumbnails.justify();
        },

        nextPage: function() {
            var page = this.page;

            if (page < this.lastPage) {
                this.getPage(page + 1);
                this.initPagination();
            }
        },

        previousPage: function() {
            var page = this.page;

            if (page > 1) {
                this.getPage(page - 1);
                this.initPagination();
            }
        },

        render: function() {},

        sendGetAssetsRequest: function() {
            var data = {
                limit: this.perpage,
                page: this.page
            }, search = this;

            for (var key in this.params) {
                data[key] = this.params[key];
            }

            // Reset before fetching to clear the thumbnail grid
            // and ensure there's no on screen 'flash' of the old search results
            this.assets.reset();

            this.assets.fetch({
                data: data,
                success: function() {
                    search.initPagination();
                }
            });
        },

        setAssetsPerPage: function() {
            var rowHeight = 200,
                avgAspectRatio = 1.5,
                height = this.$el.height(),
                rows = Math.ceil(height / rowHeight),
                perrow = Math.ceil(this.$el.width() / (rowHeight * avgAspectRatio)),
                perpage = Math.ceil(rows * perrow);

            if (perpage === NaN || perpage < 20) {
                perpage = 20;
            }

            this.perpage = perpage;
        },

        setParams: function(params) {
            var oldParams = this.params;

            params.page = (typeof params.page !== 'undefined') ? parseInt(params.page) : 1;
            this.params = params;
            this.page = params.page;

            if (this.updateForced === true) {
                this.updateForced = false;

                return true;
            }

            // Return true if the parameters have changed, false if they haven't
            if (Object.keys(oldParams).length !== Object.keys(params).length) {
                return true;
            }

            for (var key in params) {
                if (params[key] !== oldParams[key]) {
                    return true;
                }
            }

            return false;
        }
    });
}(jQuery, Backbone, BoomCMS));
