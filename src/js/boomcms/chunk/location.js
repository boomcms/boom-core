$.widget('ui.chunkLocation', $.ui.chunk, {
	edit: function() {
		var chunk = this,
			editor = new boomChunkLocationEditor(this.options.page, this.options.name);

		editor
			.done(function(data) {
				if (data.lat != 0 && data.lng != 0) {
					chunk._save(data);
				} else {
					chunk.remove();
				}
			})
			.always(function() {
				chunk.bind();
			});
	}
});
