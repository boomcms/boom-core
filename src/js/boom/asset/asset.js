function boomAsset(assetId) {
	this.id = assetId;
	
	boomAsset.prototype.getId = function() {
		return this.id;
	};

	boomAsset.prototype.getEmbedCode = function() {
		return $.get(this.getUrl('embed'));
	};
	
	boomAsset.prototype.getUrl = function(action, width) {
		var url;

		action = action ? action : 'view';

		url = '/asset/' + action + '/' + this.getId();
		
		if (width) {
			url = url + '/' + width;
		}
		
		return url;
	};
};
