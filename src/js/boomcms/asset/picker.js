function boomAssetPicker(currentAsset, filters) {
	this.currentAsset = typeof(currentAsset) === 'object' ? 
		currentAsset : new boomAsset();

	this.deferred = new $.Deferred();
	this.document = $(document);
	this.filters = filters? filters : {};

	boomAssetPicker.prototype.url = '/boomcms/assets/picker';
	boomAssetPicker.prototype.listUrl = '/boomcms/assets/get';

	boomAssetPicker.prototype.addFilter = function(type, value) {
		this.filters.page = 1;
		this.filters[type] = value;
	};

	boomAssetPicker.prototype.assetsUploaded = function(assetIds) {
		if (assetIds.length === 1) {
			this.pick(new boomAsset(assetIds[0]));
		} else {
			this.clearFilters();
			this.getAssets();
		}
	};

	boomAssetPicker.prototype.bind = function() {
		var assetPicker = this;

		this.titleFilter.assetTitleFilter({
			search : function(event, ui) {
				assetPicker.addFilter('title', assetPicker.titleFilter.val());
				assetPicker.getAssets();
			},
			select : function(event, ui) {
				assetPicker.addFilter('title', ui.item.value);
				assetPicker.getAssets();
			}
		});

		this.tagFilter
			.assetTagSearch({
				update : function(e, data) {
					assetPicker.addFilter('tag', data.tags);
					assetPicker.getAssets();
				}
			});

		this.typeFilter.on('change', function() {
			assetPicker.addFilter('type', $(this).val());
			assetPicker.getAssets();
		});

		this.picker
			.on('click', '.thumb', function(e) {
				e.preventDefault();

				var assetId = $(this).attr('data-asset');

				assetPicker.pick(new boomAsset(assetId));
			})
			.on('click', '#b-assets-picker-close', function() {
				assetPicker.cancel();
			})
			.on('click', '#b-assets-picker-current-remove', function() {
				assetPicker.pick(new boomAsset());
			})
			.find('#b-assets-upload-form')
			.assetUploader({
				uploadFinished: function(e, data) {
					assetPicker.assetsUploaded(data.result);
				}
			})
			.end()
			.on('click', '#b-assets-picker-all', function() {
				assetPicker.clearFilters();
				assetPicker.getAssets();
			});
	};

	boomAssetPicker.prototype.cancel = function() {
		this.deferred.reject();
		this.dialog.cancel();
	};

	boomAssetPicker.prototype.clearFilters = function() {
		this.filters = {
			page: 1,
			limit: 30,
			order: 'last_modified desc'
		};

		this.titleFilter.val('');
		this.typeFilter.val('');
	};

	boomAssetPicker.prototype.close = function() {
		this.dialog.cancel();
	};

	boomAssetPicker.prototype.getAssets = function() {
		var assetPicker = this;

		$.post(this.listUrl, this.filters)
			.done(function(response) {
				assetPicker.picker.find('#b-assets-view-thumbs').replaceWith(response.html);
				assetPicker.justifyAssets();

				assetPicker.initPagination(response.total);
			});
	};

	boomAssetPicker.prototype.getPage = function(page) {
		if (this.filters.page !== page) {
			this.filters.page = page;
			this.getAssets();
		}
	};

	boomAssetPicker.prototype.hideCurrentAsset = function() {
		this.picker
			.find('#b-assets-picker-current')
			.hide();
	};

	boomAssetPicker.prototype.initPagination = function(total) {
		var assetPicker = this,
			$el = this.picker.find('.b-pagination');

		// Max page isn't set correctly when re-initialising
		if ($el.data('jqPagination')) {
			$el.jqPagination('destroy');
		}

		$el.jqPagination({
			paged: function(page) {
				assetPicker.getPage(page);
			},
			max_page: Math.ceil(total / this.filters.limit),
			current_page: total > 0 ? this.filters.page : 0
		});
	};

	boomAssetPicker.prototype.justifyAssets = function() {
		this.picker
			.find('#b-assets-view-thumbs')
			.justifyAssets()
			.find('[data-asset]')
			.assetManagerImages();
	};

	boomAssetPicker.prototype.loadPicker = function() {
		var assetPicker = this;

		this.filters.limit = 30;
		this.filters.order = 'last_modified desc';

		this.dialog = new boomDialog({
			url : this.url,
			onLoad : function() {
				assetPicker.dialog.contents.parent().css({
					position: 'fixed',
					height: '100vh',
					width: '100vw',
					transform: 'none'
				});

				assetPicker.picker = assetPicker.dialog.contents.find('#b-assets-picker');
				assetPicker.titleFilter = assetPicker.picker.find('#b-assets-filter-title');
				assetPicker.tagFilter = assetPicker.picker.find('#b-tags-search');
				assetPicker.typeFilter = assetPicker.picker.find('#b-assets-types');

				if (typeof(assetPicker.filters.type) !== 'undefined') {
					assetPicker.showActiveTypeFilter(assetPicker.filters.type);
				}

				assetPicker.bind();
				assetPicker.getAssets();

				if (assetPicker.currentAsset.getId() > 0) {
					assetPicker.picker
						.find('#b-assets-picker-current img')
						.attr('src', assetPicker.currentAsset.getUrl());
				} else {
					assetPicker.hideCurrentAsset();
				}
			}
		});
	};

	boomAssetPicker.prototype.open = function() {
		this.loadPicker();

		return this.deferred;
	};

	boomAssetPicker.prototype.pick = function(asset) {
		this.deferred.resolve(asset);

		this.close();
	};

	/**
	 * Selects an option in the type filter select box to show that the type filter is active.
	 * Used when the asset picker is opened with an active type filter.
	 *
	 * @param {string} type
	 * @returns {boomAssetPicker.prototype}
	 */
	boomAssetPicker.prototype.showActiveTypeFilter = function(type) {
		var assetPicker = this;

		this.typeFilter.find('option').each(function() {
			var $this = $(this);

			if ($this.text().toLowerCase() === type.toLowerCase()) {
				assetPicker.typeFilter.val($this.val());
			}
		});

		return this;
	};

	return this.open();
};