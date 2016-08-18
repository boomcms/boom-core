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
				.on('click', '#b-assets-all', function(event) {
					assetSearch.removeFilters();
					assetSearch.getAssets();
				})
				.on('change', 'select[name=type], select[name=extension]', function() {
					var $this = $(this);

					assetSearch.addFilter($this.attr('name'), $this.val());
					assetSearch.getAssets();
				});

			this.$titleFilter = this.element
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

			this.$tagFilter
				.assetTagSearch({
					update: function(e, data) {
						assetSearch.updateTagFilters(data.tags);
					}
				});
		},

		_create: function() {
			this.$titleFilter = this.element.find('#b-assets-filter-title');
			this.$tagFilter = this.element.find('#b-tags-search');
			this.$typeFilter = this.element.find('#b-assets-types');

			if (typeof(this.options.filters) !== 'undefined') {
				for (var filter in this.options.filters) {
					this.postData[filter] = this.options.filters[filter];
				}
			}

			this.initialFilters = this.postData;

			this.bind();
			this.setAssetsPerPage();
			this.getAssets();
		},

		getAssets: function() {
			var assetSearch = this;

			this.postData.limit = this.perpage;

			return $.post(this.listUrl, this.postData)
				.done(function(response) {
					assetSearch.element
						.find('#b-assets-view-thumbs')
						.replaceWith(response.html);

					assetSearch.element
						.find('#b-assets-view-thumbs')
						.justifyAssets()
						.find('[data-asset]')
						.each(function() {
							var $this = $(this),
								asset = new BoomCMS.Asset({id: $this.attr('data-asset')}),
								url  = asset.getUrl('thumb', $this.width(), $this.height()) + '?' + Math.floor(Date.now() / 1000);

							$this.find('img')
								.attr('src', url)
								.on('load', function() {
									$(this).removeClass('loading');
								});
						});

					assetSearch.initPagination(response.total);
					assetSearch._trigger('fetched');
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

		removeFilters: function() {
			this.postData = this.initialFilters;

			this.element.find('#b-assets-types').val(0);

			this.getAssets();
		},

		setAssetsPerPage: function() {
			var rowHeight = 200,
				avgAspectRatio = 1.5,
				height = this.element.find('#b-assets-view-thumbs').height(),
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
		}
	});
}(jQuery, BoomCMS));
