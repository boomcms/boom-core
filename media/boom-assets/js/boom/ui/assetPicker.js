function boomAssetPicker(currentAssetId) {
	this.currentAssetId = currentAssetId? currentAssetId : 0;
	this.currentPage = 1;
	this.deferred = new $.Deferred();
	this.document = $(document);

	boomAssetPicker.prototype.url = '/cms/assets/picker';
	boomAssetPicker.prototype.listUrl = '/cms/assets/list';

	boomAssetPicker.prototype.bind = function() {
		var assetPicker = this;

		this.picker
			.on('click', '.thumb a', function(e) {
				e.preventDefault();

				var asset_id = $(this).attr('href').replace('#asset/', '');

				assetPicker.pick(asset_id);

				return false;
			})
			.on('click', '#b-asset-picker-close', function() {
				assetPicker.cancel();
			})
			.find('#b-assets-upload-form')
			.assetUploader()
			.end()
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

	boomAssetPicker.prototype.close = function() {
		this.picker.remove();
		$(top.window).trigger('boom:dialog:close');
	};

	boomAssetPicker.prototype.getPage = function(page) {
		var assetPicker = this;

		if (this.currentPage !== page) {
			$.get(this.listUrl, {page : page})
				.done(function(response) {
					assetPicker.picker.find('#b-assets-view-thumbs').replaceWith(response);
					assetPicker.justifyAssets();
				});
				
			this.currentPage = page;
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