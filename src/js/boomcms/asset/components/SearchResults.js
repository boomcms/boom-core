(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.SearchResults = BoomCMS.AssetManager.ViewSelection.extend({
        initialFilters: {},

        bind: function() {
            var assetSearch = this;

            this.listenTo(this.assets, 'reset sync add remove', this.render);

            this.$pagination
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
                    page: 1
                };

            for (var key in this.params) {
                data[key] = this.params[key];
            }
            
            this.assets.fetch({
                data: data,
                reset: true
            });
        },

        initPagination: function(total) {
            var view = this,
                $el = this.$pagination;

            this.lastPage = Math.ceil(total / this.postData.limit);

            // Max page isn't set correctly when re-initialising
            if ($el.data('jqPagination')) {
                $el.jqPagination('destroy');
            }

            $el.jqPagination({
                paged: function(page) {
                    view.getPage(page);
                },
                max_page: this.lastPage,
                current_page: total > 0 ? this.postData.page : 0
            });
        },

        nextPage: function() {
            var page = this.postData.page;

            if (page < this.lastPage) {
                this.getPage(page + 1);
            }
        },

        previousPage: function() {
            var page = this.postData.page;

            if (page > 1) {
                this.getPage(page - 1);
            }
        },

        render: function() {
            new BoomCMS.AssetManager.ThumbnailGrid({
                el: this.$('.b-assets-view-thumbs'),
                assets: this.assets
            }).render();
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
