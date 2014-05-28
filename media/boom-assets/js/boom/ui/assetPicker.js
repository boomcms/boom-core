function boomAssetPicker(currentAssetId) {
	this.currentAssetId = currentAssetId? currentAssetId : 0;
	this.deferred = new $.Deferred();

	boomAssetPicker.prototype.url = '/cms/assets/picker?currentAssetId=' + this.currentAssetId;

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
			.find('#b-assets-view-thumbs')
			.justifyAssets();
	};

	boomAssetPicker.prototype.cancel = function() {
		this.deferred.reject();
		this.close();
	}

	boomAssetPicker.prototype.close = function() {
		$(top.document).find('body').css('overflow', 'auto');
		this.picker.remove();
	};

	boomAssetPicker.prototype.loadPicker = function() {
		var assetPicker = this;

		this.picker = $("<div id='b-assets-picker'></div>");
		this.picker.load(this.url, function() {
			assetPicker.bind();
		});

		$(top.document).find('body')
			.css('overflow', 'hidden')
			.append(this.picker);
	};

	boomAssetPicker.prototype.open = function() {
		this.loadPicker();

		return this.deferred;
	};

	boomAssetPicker.prototype.pick = function(asset_id) {
		this.deferred.resolve(asset_id);

		this.close();
	};

	return this.open();
};