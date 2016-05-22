function boomLink(url, pageId, title) {
	this.url = url? url : "";
	this.pageId = pageId? pageId : 0;
	this.title = title? title : "";

	boomLink.prototype.isAsset = function() {
		return this.getUrl().indexOf('/asset/') === 0;
	};

	boomLink.prototype.isExternal = function() {
		return this.getUrl() !== "" && this.getUrl().substring(0,1) !== '/';
	};

	boomLink.prototype.isHttp = function() {
		return this.url.substring(0,7) === 'http://';
	};

	boomLink.prototype.isHttps = function() {
		return this.url.substring(0,8) === 'https://';
	};

	boomLink.prototype.isInternal = function() {
		return this.pageId > 0 || this.getUrl().substring(0,1) === '/';
	};

	boomLink.prototype.isMailto = function() {
		return this.url.substring(0,7) === 'mailto:';
	};

	boomLink.prototype.isTel = function() {
		return this.url.substring(0,4) === 'tel:';
	};

	boomLink.prototype.getAsset = function() {
		var assetId = this.getUrl().replace(/\/asset\/(\d+)([\/\d]*?)\/(view|download)/i, "$1");

		return new BoomCMS.Asset({id: assetId});
	};

	boomLink.prototype.getAssetAction = function() {
		if (this.isAsset()) {
			return this.getUrl().replace(/\/asset\/(\d+)([\/\d]*?)\/(view|download)/i, "$3");
		}
	};

	boomLink.prototype.getUrl = function() {
		if (this.isTel()) {
			return this.getTelUrl();
		}

		return (this.url === 'http://') ? '' : this.makeUrlRelative();
	};

	boomLink.prototype.getPageId = function() {
		return this.pageId;
	};

	boomLink.prototype.getTelUrl = function() {
		return 'tel:' + this.url.replace(/[^+\d]+/g, '');
	};

	boomLink.prototype.getTitle = function() {
		return this.title;
	};
	
	boomLink.prototype.makeUrlRelative = function() {
		return (this.url.indexOf(window.location.hostname) > -1) ?
			this.url.replace(/^https?:\/\//, '').replace(window.location.hostname, '') :
			this.url;
	};
};
