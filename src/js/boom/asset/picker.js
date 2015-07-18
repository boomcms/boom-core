function boomAssetPicker(currentAssetId, filters) {
	this.currentAssetId = currentAssetId? currentAssetId : 0;
	this.deferred = new $.Deferred();
	this.document = $(document);
	this.filters = filters? filters : {};

	boomAssetPicker.prototype.url = '/cms/assets/picker';
	boomAssetPicker.prototype.listUrl = '/cms/assets/get';

	boomAssetPicker.prototype.addFilter = function(type, value) {
		this.filters.page = 1;
		this.filters[type] = value;
	};

	boomAssetPicker.prototype.assetsUploaded = function(assetIds) {
		if (assetIds.length === 1) {
			this.pick(assetIds[0]);
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
			.on('click', '.thumb a', function(e) {
				e.preventDefault();

				var asset_id = $(this).attr('href').replace('#asset/', '');

				assetPicker.pick(asset_id);

				return false;
			})
			.on('click', '#b-assets-picker-close', function() {
				assetPicker.cancel();
			})
			.on('click', '#b-assets-picker-current-remove', function() {
				assetPicker.pick(0);
			})
			.find('#b-assets-upload-form')
			.assetUploader({
				done : function(e, data) {
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
			page : 1
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
				var $response = $(response);

				assetPicker.picker.find('#b-assets-view-thumbs').replaceWith($response.find('#b-assets-view-thumbs'));
				assetPicker.justifyAssets();

				assetPicker.picker.find('.b-pagination').replaceWith($response[2]);
				assetPicker.initPagination();
			});
	};

	boomAssetPicker.prototype.getPage = function(page) {
		if (this.filters.page !== page) {
			this.addFilter('page', page);
			this.getAssets();
		}
	};

	boomAssetPicker.prototype.hideCurrentAsset = function() {
		this.picker
			.find('#b-assets-picker-current')
			.hide();
	};

	boomAssetPicker.prototype.initPagination = function() {
		var assetPicker = this;

		assetPicker.picker.find('.b-pagination')
			.jqPagination({
				paged: function(page) {
					assetPicker.getPage(page);
				}
			});
	};

	boomAssetPicker.prototype.justifyAssets = function() {
		this.picker
			.find('#b-assets-view-thumbs')
			.justifyAssets();
	};

	boomAssetPicker.prototype.loadPicker = function() {
		var assetPicker = this;

		this.dialog = new boomDialog({
			url : this.url,
			onLoad : function() {
				assetPicker.picker = assetPicker.dialog.contents.find('#b-assets-picker');
				assetPicker.titleFilter = assetPicker.picker.find('#b-assets-filter-title');
				assetPicker.tagFilter = assetPicker.picker.find('#b-tags-search');
				assetPicker.typeFilter = assetPicker.picker.find('#b-assets-types');

				if (typeof(assetPicker.filters.type) !== 'undefined') {
					assetPicker.showActiveTypeFilter(assetPicker.filters.type);
				}

				assetPicker.bind();
				assetPicker.getAssets();

				if (assetPicker.currentAssetId > 0) {
					assetPicker.picker
						.find('#b-assets-picker-current img')
						.attr('src', '/asset/view/' + assetPicker.currentAssetId);
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

	boomAssetPicker.prototype.pick = function(asset_id) {
		this.deferred.resolve(asset_id);

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