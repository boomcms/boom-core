function boomAssetPicker(currentAssetId) {
	this.currentAssetId = currentAssetId? currentAssetId : 0;
	this.deferred = new $.Deferred();
	this.document = $(document);
	this.filters = {
		page : 1
	};

	boomAssetPicker.prototype.url = '/cms/assets/picker';
	boomAssetPicker.prototype.listUrl = '/cms/assets/list';

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
			.tagger_search({
				update : function(tagIds) {
					assetPicker.addFilter('tag', tagIds);
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
		this.close();
	};

	boomAssetPicker.prototype.clearFilters = function() {
		this.filters = {
			page : 1
		};

		this.titleFilter.val('');
		this.typeFilter.val('');
	};

	boomAssetPicker.prototype.close = function() {
		this.picker.remove();
		$(top.window).trigger('boom:dialog:close');
	};

	boomAssetPicker.prototype.getAssets = function() {
		var assetPicker = this;

		$.post(this.listUrl, this.filters)
			.done(function(response) {
				var $response = $(response);

				assetPicker.picker.find('#b-assets-view-thumbs').replaceWith($response.find('#b-assets-view-thumbs'));
				assetPicker.justifyAssets();

				assetPicker.picker.find('.pagination').replaceWith($response[2]);
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

		assetPicker.picker.find('.pagination')
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

		this.picker = $("<div id='b-assets-picker'></div>");
		this.picker.load(this.url, function() {
			assetPicker.titleFilter = assetPicker.picker.find('#b-assets-filter-title');
			assetPicker.tagFilter = assetPicker.picker.find('#b-tags-search');
			assetPicker.typeFilter = assetPicker.picker.find('#b-assets-types');

			assetPicker.bind();
			assetPicker.justifyAssets();
			assetPicker.initPagination();

			if (assetPicker.currentAssetId) {
				assetPicker.picker
					.find('#b-assets-picker-current img')
					.attr('src', '/asset/view/' + assetPicker.currentAssetId);
			} else {
				assetPicker.hideCurrentAsset();
			}
		});

		this.document
			.find('body')
			.append(this.picker);
	};

	boomAssetPicker.prototype.open = function() {
		this.loadPicker();
		$(top.window).trigger('boom:dialog:open');

		return this.deferred;
	};

	boomAssetPicker.prototype.pick = function(asset_id) {
		this.deferred.resolve(asset_id);

		this.close();
	};

	return this.open();
};