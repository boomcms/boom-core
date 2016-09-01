(function($, BoomCMS) {
	'use strict';

	$.widget('boom.assetSearch', {
		listUrl: BoomCMS.urlRoot + 'assets/get',

		postData: {
			page: 1,
			order: 'last_modified desc'
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
					assetSearch.getAssets();
				})
				.on('change', 'select[name=type], select[name=extension]', function() {
					var $this = $(this);

					assetSearch.addFilter($this.attr('name'), $this.val());
					assetSearch.getAssets();
				})
				.on('change', '#b-assets-sortby', function(event) {
					assetSearch.sortBy(this.value);
				})
				.find('#b-assets-filter-title')
				.assetTitleFilter({
					search: function(event, ui) {
						assetSearch.addFilter('title', $(this).val());
						assetSearch.getAssets();
					},
					select: function(event, ui) {
						assetSearch.addFilter('title', ui.item.value);
						assetSearch.getAssets();
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

			this.initialFilters = this.postData;

			this.bind();
			this.setAssetsPerPage();
		},

		getAssets: function() {
			var assetSearch = this;

			this.postData.limit = this.perpage;
			this.assets.fetch({
				data: this.postData,
				reset: true,
				success: function(collection, response, options) {
					assetSearch.initPagination(response.total);
					assetSearch.renderGrid();
				}
			});
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

			// Max page isn't set correctly when re-initialising
			if ($el.data('jqPagination')) {
				$el.jqPagination('destroy');
			}

			$el.jqPagination({
				paged: function(page) {
					assetManager.getPage(page);
				},
				max_page: Math.ceil(total / this.postData.limit),
				current_page: total > 0 ? this.postData.page : 0
			});
		},

		justify: function() {
			this.element.find('#b-assets-view-thumbs').justifyAssets();
		},

		removeFilters: function() {
			this.postData = this.initialFilters;

			this.element.find('#b-assets-types').val(0);

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
			var rowHeight = 200,
				avgAspectRatio = 1.5,
				height = this.element.find('#b-assets-content').height(),
				rows = Math.ceil(height / rowHeight),
				perrow = Math.ceil(document.documentElement.clientWidth / (rowHeight * avgAspectRatio)),
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
