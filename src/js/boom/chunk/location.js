$.widget('ui.chunkLocation', $.ui.chunk, {
	edit: function() {
		var chunk = this,
			editor = new boomChunkLocationEditor(this.options.page, this.options.name);

		editor
			.done(function(data) {
				chunk._save(data);
			})
			.always(function() {
				chunk.bind();
			});
	}
});