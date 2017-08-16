(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.SearchResults = BoomCMS.AssetManager.ViewSelection.extend({
        page: 1,
        params: {},

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
        },

        initialize: function(options) {
            this.assets = options.assets;
            this.$pagination = options.pagination;
            this.router = options.router;
            this.selection = options.selection;
            this.$container = options.$container;

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

        getAssets: function() {
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

        reset: function() {
            this.params = {};
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

            // Return true if the parameters have changed, false if they haven't
            if (oldParams.length !== params.length) {
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
