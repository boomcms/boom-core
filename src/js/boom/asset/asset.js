function boomAsset(assetId) {
	this.id = assetId;
	
	boomAsset.prototype.getId = function() {
		return this.id;
	};
	
	boomAsset.prototype.getUrl = function() {
		return '/asset/view/' + this.id;
	};
};
