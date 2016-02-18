$.widget('boom.pageStatus', {

	menu : $('#b-page-publish-menu'),

	_create: function() {
		this.set(this.element.text().trim());
	},

	_get_abbreviated_status: function(status) {
		switch(status) {
			case 'published':
				return "pub'd";
			case 'draft':
				return 'draft';
			case 'embargoed':
				return "emb'd";
			case 'pending approval':
				return "pen'd";
		}
	},

	set: function(status) {
		this.element
			.text(this._get_abbreviated_status(status))
			.attr('data-status', status)
			.attr('title', status.ucfirst());
	}
});