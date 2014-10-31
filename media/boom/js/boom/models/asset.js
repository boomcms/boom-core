function boomAsset(asset_id) {
	this.id = asset_id;

	boomAsset.prototype.base_url = '/cms/assets/';

	boomAsset.prototype.delete = function() {
		var asset = this,
			deleted = new $.Deferred(),
			confirmation,
			message = this.hasMultipleIds()? 'Are you sure you wanted to delete these assets?' : 'Are you sure you want to delete this asset?'

		confirmation = new boomConfirmation('Please confirm', message);
		confirmation.done(function() {
			$.boom.post(asset.base_url + 'delete', {
				assets : asset.id.split('-')
			})
			.done(function() {
				deleted.resolve();
			});
		});

		return deleted;
	};

	boomAsset.prototype.download = function() {
		var url = this.base_url + 'download?',
			assets = this.id.split('-');

		for (var i = 0; i < assets.length; i++) {
			assets[i] = 'asset[]=' + assets[i];
		}

		window.location = url + assets.join('&');
	};

	boomAsset.prototype.get = function() {
		return $.get(this.base_url + 'view/' + this.id);
	};

	boomAsset.prototype.hasMultipleIds = function() {
		return this.id.indexOf('-') !== -1;
	};

	boomAsset.prototype.save = function(data) {
		return $.boom.post(this.base_url + 'save/' + this.id, data);
	};
};