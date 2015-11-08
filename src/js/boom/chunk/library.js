$.widget('ui.chunkLibrary', $.ui.chunk, {
	edit : function(){
		$.boom.log('Tag library edit');

		var library = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/' + this.options.currentPage.id + '/edit?type=library&slotname=' + this.options.name,
			width: 400,
			title: 'Select tag',
			onLoad : function() {
				library.tag = library.dialog.contents.find('#b-selected p').text();

				library.dialog.contents.find('#b-tags-add-name').assetTagAutocomplete({
					complete : function(e, data) {
						library.tag = data.tag;
						library.dialog.contents.find('#b-selected p').text(data.tag);
					}
				});
			}
		});
	},

	getData: function() {
		return {params : {tag: this.tag}};
	},

	insert : function(tag) {
		this.tag = tag;

		return this._save();
	}
});