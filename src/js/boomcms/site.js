function boomSite(siteId) {
	this.id = siteId;
	this.baseUrl = '/boomcms/sites/';

	boomSite.prototype.edit = function() {
		return new boomDialog({
			url: this.baseUrl + this.id + '/edit'
		});
	};

	boomSite.prototype.update = function(data) {
		return $.ajax({
			type: 'put',
			url: this.baseUrl + this.id,
			data: data
		});
	};
}