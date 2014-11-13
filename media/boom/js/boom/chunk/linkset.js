$.widget('ui.chunkLinkset', $.ui.chunk, {
	edit : function() {
		var chunkLinkset = this;

		new boomChunkLinksetEditor(this.options.currentPage.id, this.options.name, {
				title : chunkLinkset.element.find('.linkset-title').length > 0
			})
			.done(function(data) {
				chunkLinkset.insert(data);
			});
	},

	insert : function(links) {
		if (links.length === 0) {
			return this.remove();
		} else {
			return this._save(links);
			this.destroy();
		}
	}
});