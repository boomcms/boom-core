function boomLink(url, pageId, title) {
	this.url = url? url : "";
	this.pageId = pageId? pageId : 0;
	this.title = title? title : "";

	boomLink.prototype.isExternal = function() {
		return this.getUrl() !== "";
	};

	boomLink.prototype.isInternal = function() {
		return this.pageId > 0;
	};

	boomLink.prototype.getUrl = function() {
		return this.url.replace(window.location.protocol + '//' + window.location.hostname, '');
	};

	boomLink.prototype.getPageId = function() {
		return this.pageId;
	};

	boomLink.prototype.getTitle = function() {
		return this.title;
	};
};