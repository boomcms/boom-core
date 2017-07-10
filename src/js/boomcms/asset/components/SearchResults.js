(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.SearchResults = BoomCMS.AssetManager.ViewSelection.extend({
        page: 1,

        bind: function() {
            var assetSearch = this;

            this.listenTo(this.assets, 'reset sync add remove', this.render);

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
            
            this.assets.fetch({
                data: data,
                reset: true,
                success: function() {
                    search.trigger('fetched', search.assets);
                }
            });
        },

        getPage: function(page) {
            this.params['page'] = page;
            this.page = page;

            this.trigger('filtered', this.params);
        },

        initPagination: function() {
            var view = this;

            this.lastPage = Math.ceil(this.assets.total / this.perpage);

            if (this.$pagination.data('jqPagination')) {
                this.$pagination.jqPagination('destroy');
            }

            this.$pagination.jqPagination({
                paged: function(page) {
                    view.getPage(page);
                },
                max_page: this.lastPage,
                current_page: this.assets.total > 0 ? this.page : 0
            });
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

        render: function() {
            new BoomCMS.AssetManager.ThumbnailGrid({
                el: this.$('.b-assets-view-thumbs'),
                assets: this.assets,
                selection: this.selection,
                $container: this.$container
            }).render();

            this.initPagination();
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
            this.params = params;

            if (typeof this.params.page !== 'undefined') {
                this.page = parseInt(this.params.page);
            }
        }
    });
}(jQuery, Backbone, BoomCMS));
