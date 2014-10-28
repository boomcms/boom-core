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
		this.filters[type] = value;
	};

	boomAssetPicker.prototype.bind = function() {
		var assetPicker = this;

		this.titleFilter = this.picker.find('#b-assets-filter-title');
		this.titleFilter.assetTitleFilter({
			search : function(event, ui) {
				assetPicker.addFilter('title', $(this).val());
				assetPicker.getAssets();
			},
			select : function(event, ui) {
				assetPicker.addFilter('title', ui.item.value);
				assetPicker.getAssets();
			}
		});
var selected_tag_ids = [];
		this.tagFilter = this.picker.find('#b-tags-search');
		this.tagFilter
			.tagger_search()
			.find('input')
			.tagAutocompleter({
				type : 1,
				complete : function(event, data) {
					selected_tag_ids.push(data.id);
					$(this).tagAutocompleter('setSelectedTags', selected_tag_ids);
					$('#b-tags-search').tagger_search('add', data.name, data.id);
				}
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
			.assetUploader()
			.end()
			.on('click', '#b-assets-picker-all', function() {
				assetPicker.clearFilters();
				assetPicker.getAssets();
			});

		this.picker
			.find('.pagination')
			.jqPagination({
				paged: function(page) {
					assetPicker.getPage(page);
				}
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
	};

	boomAssetPicker.prototype.close = function() {
		this.picker.remove();
		$(top.window).trigger('boom:dialog:close');
	};

	boomAssetPicker.prototype.getAssets = function() {
		var assetPicker = this;

		$.get(this.listUrl, this.filters)
			.done(function(response) {
				assetPicker.picker.find('#b-assets-view-thumbs').replaceWith($(response).find('#b-assets-view-thumbs'));
				assetPicker.justifyAssets();
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

	boomAssetPicker.prototype.justifyAssets = function() {
		this.picker
			.find('#b-assets-view-thumbs')
			.justifyAssets();
	};

	boomAssetPicker.prototype.loadPicker = function() {
		var assetPicker = this;

		this.picker = $("<div id='b-assets-picker'></div>");
		this.picker.load(this.url, function() {
			assetPicker.bind();
			assetPicker.justifyAssets();

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