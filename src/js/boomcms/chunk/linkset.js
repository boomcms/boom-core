$.widget('ui.chunkLinkset', $.ui.chunk, {
	edit: function() {
		var chunkLinkset = this;

		new boomChunkLinksetEditor(this.options.currentPage.id, this.options.name, this.getOptions())
			.done(function(data) {
				chunkLinkset.insert(data);
			})
			.fail(function() {
				chunkLinkset.destroy();
			});
	},

	getOptions: function() {
		var $el = this.element,
			options = {
				title: 'linkset-title',
				linkAssets: 'link-asset',
				linkText: 'link-text'
			};

		for (var i in options) {
			options[i] = $el.hasClass(options[i]) || $el.find('.' + options[i]).length > 0;
		}

		return options;
	},

	insert: function(links) {
		if (typeof(links) === 'undefined' || links.length === 0) {
			this.remove();
		} else {
			this._save(links);
		}
	}
});