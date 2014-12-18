$.widget('ui.chunkLinkset', $.ui.chunk, {
	edit : function() {
		var chunkLinkset = this;

		new boomChunkLinksetEditor(this.options.currentPage.id, this.options.name, {
				title : chunkLinkset.element.find('.linkset-title').length > 0
			})
			.done(function(data) {
				chunkLinkset.insert(data);
			})
			.fail(function() {
				chunkLinkset.destroy();
			});
	},

	insert : function(links) {
		if (links.length === 0) {
			this.remove();
		} else {
			this._save(links);
		}
	}
});