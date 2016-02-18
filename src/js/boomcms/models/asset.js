function boomAsset(assetId) {
	this.id = assetId;
	
	boomAsset.prototype.getId = function() {
		return this.id;
	};

	boomAsset.prototype.getEmbedCode = function() {
		return $.get(this.getUrl('embed'));
	};
	
	boomAsset.prototype.getUrl = function(action, width, height) {
		var url = '/asset/' + this.getId();

		if ((!action || action === 'view') && !(width || height)) {
			return url;
		}

		if (!action && (width || height)) {
			action = 'view';
		}

		url = url + '/' + action;

		if (width || height) {
			url = url + '/' + width + '/' + height;
		}

		return url;
	};
}
