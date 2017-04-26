(function($, BoomCMS) {
    'use strict';

    $.widget('boom.assetSearch', {
        listUrl: BoomCMS.urlRoot + 'assets/get',

        initialFilters: {},

        postData: {
            page: 1,
            order: 'created_at desc'
        },

        addFilter: function(type, value) {
            this.postData.page = 1;
            this.postData[type] = value;
        },

        bind: function() {
            var assetSearch = this;

            this.element
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

        _create: function() {
            var assetSearch = this;

            this.assets = this.options.assets;

            if (typeof(this.options.filters) !== 'undefined') {
                for (var filter in this.options.filters) {
                    this.postData[filter] = this.options.filters[filter];
                }
            }

            this.assets.on('change:image', function() {
                assetSearch.justify();
            });

            for (var key in this.postData) {
                this.initialFilters[key] = this.postData[key];
            }

            this.bind();
            this.setAssetsPerPage();
        },

        getAssets: function() {
            var assetSearch = this,
                deferred = $.Deferred();

            if (this.paginateResults() === true) {
                this.postData.limit = this.perpage;
            }

            var $el = this.element
                .find('#b-assets-view-thumbs')
                .addClass('loading');

            $el.find('> div:nth-of-type(2)').html('');
            
            this.assets.fetch({
                data: this.postData,
                reset: true,
                success: function(collection, response) {
                    assetSearch.initPagination(response.total);
                    assetSearch.renderGrid();

                    deferred.resolve(collection);
                }
            });

            return deferred;
        },

        getPage: function(page) {
            if (this.postData.page !== page) {
                this.postData.page = page;
                this.getAssets();
            }
        },

        initPagination: function(total) {
            var assetManager = this,
                $el = assetManager.element.find('.b-pagination');

            if (this.paginateResults()) {
                this.lastPage = Math.ceil(total / this.postData.limit);

                // Max page isn't set correctly when re-initialising
                if ($el.data('jqPagination')) {
                    $el.jqPagination('destroy');
                }

                $el.jqPagination({
                    paged: function(page) {
                        assetManager.getPage(page);
                    },
                    max_page: this.lastPage,
                    current_page: total > 0 ? this.postData.page : 0
                });

                $el.show();
            }
        },

        nextPage: function() {
            var page = this.postData.page;

            if (page < this.lastPage) {
                this.getPage(page + 1);
            }
        },

        paginateResults: function() {
            return this.postData['page'] !== null;
        },

        previousPage: function() {
            var page = this.postData.page;

            if (page > 1) {
                this.getPage(page - 1);
            }
        },

        removeFilters: function() {
            this.element.find('#b-assets-types, #b-assets-extensions, select[name=uploadedby]').val(0);

            this.setFilters(this.initialFilters);
        },

        renderGrid: function() {
            var $el = this.element.find('.b-assets-view-thumbs > div:nth-of-type(2)');

            
        },

        setAssetsPerPage: function() {
            var $thumbs = this.element.find('#b-assets-view-thumbs > div:nth-of-type(2)'),
                rowHeight = 200,
                avgAspectRatio = 1.5,
                height = $thumbs.height(),
                rows = Math.ceil(height / rowHeight),
                perrow = Math.ceil($thumbs.width() / (rowHeight * avgAspectRatio)),
                perpage = Math.ceil(rows * perrow);

            if (perpage < 30) {
                perpage = 30;
            }

            this.perpage = perpage;
        },

        setFilters: function(filters) {
            this.postData = {};

            for (var key in filters) {
                this.postData[key] = filters[key];
            }
        },

        sortBy: function(sort) {
            this.postData['order'] = sort;
            this.getAssets();
        }
    });
}(jQuery, BoomCMS));
