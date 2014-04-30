function boomAssetPicker(currentAssetId) {
	this.currentAssetId = currentAssetId;
	this.deferred = new $.Deferred();

	boomAssetPicker.prototype.url = '/cms/assets/list';

	boomAssetPicker.prototype.bind = function() {
		var assetPicker = this;

		this.picker
			.on('click', '.thumb a', function(e) {
				e.preventDefault();

				var asset_id = $(this).attr('href').replace('#asset/', '');

				assetPicker.pick(asset_id);

				return false;
			});
	};

	boomAssetPicker.prototype.loadPicker = function() {
		var assetPicker = this;

		this.picker = $("<div id='b-assets-picker'></div>");
		this.picker.load(this.url, function() {
			assetPicker.bind();
		});

		$(top.document).find('body').append(this.picker);
	};

	boomAssetPicker.prototype.open = function() {
		this.loadPicker();

		return this.deferred;
	};

	boomAssetPicker.prototype.pick = function(asset_id) {
		this.deferred.resolve(asset_id);
		//this.dialog.close();
	};

	return this.open();
};