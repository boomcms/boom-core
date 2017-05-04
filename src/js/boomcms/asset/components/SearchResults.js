(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.SearchResults = BoomCMS.AssetManager.ViewSelection.extend({
        initialFilters: {},
        page: 1,

        bind: function() {
            var assetSearch = this;

            this.listenTo(this.assets, 'reset sync add remove', this.render);

            this.$el
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
            this.params = options.params;
            this.router = options.router;

            for (var key in this.postData) {
                this.initialFilters[key] = this.postData[key];
            }

            this.bind();
            this.setAssetsPerPage();
            this.getAssets();
        },

        getAssets: function() {
            var data = {
                limit: this.perpage,
                page: this.page
            };

            for (var key in this.params) {
                data[key] = this.params[key];
            }
            
            this.assets.fetch({
                data: data,
                reset: true
            });
        },

        getPage: function(page) {
            this.params['page'] = page;

            this.router.goToSearchResults(this.params);
            this.getAssets();
        },

        initPagination: function() {
            var view = this;

            this.lastPage = Math.ceil(this.assets.total / this.perpage);

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
            }
        },

        previousPage: function() {
            var page = this.page;

            if (page > 1) {
                this.getPage(page - 1);
            }
        },

        render: function() {
            new BoomCMS.AssetManager.ThumbnailGrid({
                el: this.$('.b-assets-view-thumbs'),
                assets: this.assets
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
        }
    });
}(jQuery, Backbone, BoomCMS));
