function boomLink(url, pageId, title) {
	this.url = url? url : "";
	this.pageId = pageId? pageId : 0;
	this.title = title? title : "";

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

	boomLink.prototype.getUrl = function() {
		return (this.url == 'http://') ? '' : this.makeUrlRelative();
	};

	boomLink.prototype.getPageId = function() {
		return this.pageId;
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
