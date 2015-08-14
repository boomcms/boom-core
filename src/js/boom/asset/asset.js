function boomAsset(assetId) {
	this.id = assetId;
	
	boomAsset.prototype.getId = function() {
		return this.id;
	};

	boomAsset.prototype.getEmbedCode = function() {
		return $.get(this.getUrl('embed'));
	};
	
	boomAsset.prototype.getUrl = function(action, width) {
		var url = '/asset/' + this.getId();

		if (!action && width) {
			action = 'view';
		}

		if (action) {
			url = url + '/' + action;
		}

		if (width) {
			url = url + '/' + width;
		}

		return url;
	};
};
