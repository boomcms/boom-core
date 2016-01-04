$.widget('ui.chunkLibrary', $.ui.chunk, {
	clearFilters: function() {
		var $el = this.dialog.contents;

		$el.find('select').prop('selectedIndex', 0);
		$el.find('#b-tags-search li').remove();
		$el.find('input[type=text]').val('');
	},

	edit : function() {
		var library = this;

		this.dialog = new boomDialog({
			url: '/boomcms/chunk/' + this.options.currentPage.id + '/edit?type=library&slotname=' + this.options.name,
			width: 700,
			closeButton: false,
			saveButton: true,
			onLoad: function() {
				library.dialog.contents
					.on('click', '.b-button.clear', function() {
						library.clearFilters();
					})
					.find('#b-tags-search')
					.assetTagSearch();
			}
		})
		.always(function() {
			library.bind();
		})
		.done(function() {
			library._save(library.getData());
		});
	},

	getData: function() {
		var $el = this.dialog.contents;

		return {
			params: {
				type: $el.find('#b-assets-types :selected').val(),
				order: $el.find('#b-assets-sortby :selected').val(),
				limit: $el.find('input[name=limit]').val(),
				tag: $el.find('#b-tags-search [data-tag]').map(function() {
					return $(this).attr('data-tag');
				}).toArray()
			}
		};
	}
});