function boomAsset(asset_id) {
	this.id = asset_id;

	boomAsset.prototype.base_url = '/cms/assets/';

	boomAsset.prototype.delete = function() {
		var asset = this,
			deleted = new $.Deferred(),
			confirmation;

		confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to delete this asset?');
		confirmation.done(function() {
			$.boom.post(asset.base_url + 'delete/' + asset.id)
				.done(function() {
					deleted.resolve();
				});
		});

		return deleted;
	};
};