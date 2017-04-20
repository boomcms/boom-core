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
                .on('click', '#b-assets-all', function() {
                    assetSearch.removeFilters();
                })
                .on('change', 'select[name=type], select[name=extension], select[name=uploadedby]', function() {
                    var $this = $(this);

                    assetSearch.addFilter($this.attr('name'), $this.val());
                    assetSearch.getAssets();
                })
                .on('change', '#b-assets-sortby', function() {
                    assetSearch.sortBy(this.value);
                })
                .find('#b-assets-filter-title')
                .assetTitleFilter({
                    search: function() {
                        assetSearch.addFilter('title', $(this).val());
                        assetSearch.getAssets();
                    },
                    select: function(event, ui) {
                        assetSearch.addFilter('title', ui.item.value);
                        assetSearch.getAssets();
                    }
                })
                .end()
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

            this.element.find('#b-tags-search')
                .assetTagSearch({
                    update: function(e, data) {
                        assetSearch.updateTagFilters(data.tags);
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

            this.postData.limit = this.perpage;
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
        },

        justify: function() {
            this.element.find('#b-assets-view-thumbs').justifyAssets();
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

        removeFilters: function() {
            this.postData = {};

            for (var key in this.initialFilters) {
                this.postData[key] = this.initialFilters[key];
            }

            this.element.find('#b-assets-types, #b-assets-extensions, select[name=uploadedby]').val(0);
            this.element.find('#b-tags-search li').remove();

            this.getAssets();
        },

        renderGrid: function() {
            var $el = this.element.find('#b-assets-view-thumbs');

            $el.html('');

            if (!this.assets.length) {
                return $el.html(this.element.find('#b-assets-none-template').html());
            }

            this.assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                });

                $el.append(thumbnail.render().el);
            });

            this.justify();
        },

        setAssetsPerPage: function() {
            var $thumbs = this.element.find('#b-assets-view-thumbs'),
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

        sortBy: function(sort) {
            this.postData['order'] = sort;
            this.getAssets();
        },

        updateTagFilters: function(tags) {
            this.addFilter('tag', tags);
            this.getAssets();
        }
    });
}(jQuery, BoomCMS));
