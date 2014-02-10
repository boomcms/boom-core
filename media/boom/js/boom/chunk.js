function boomChunk(page_id, type, slotname) {
	this.page_id = page_id;
	this.slotname = slotname;
	this.type = type;
	this.urlPrefix = '/cms/chunk/' + this.type + '/';

	boomChunk.prototype.delete = function() {
		return $.boom.post(this.urlPrefix + 'delete/' + this.page_id, {slotname : this.slotname});
	};

	boomChunk.prototype.save = function(data) {
		data.slotname = this.slotname;

		return $.boom.post(this.urlPrefix + 'save/' + this.page_id, data);
	};
}