function boomChunkAssetEditor(pageId, slotname) {
	this.pageId = pageId;
	this.slotname = slotname;

	boomChunkAssetEditor.prototype.open = function() {
		var chunkAssetEditor = this;
		this.deferred = new $.Deferred();

		this.dialog = new boomDialog({
			url : '/cms/chunk/asset/edit/' + this.pageId + '?slotname=' + this.slotname,
			id : 'b-chunk-asset-editor',
			open : function() {
				chunkAssetEditor.open();
			}
		});

		return this.deferred;
	};

	return this.open();
};