$.widget('boom.pageSettingsDelete', {
	bind: function() {
		var settingsEditor = this,
			page = this.page;

		this.element
			.on('click', '#b-page-delete-confirm', function(e) {
				e.preventDefault();

				page.delete()
					.done(function(response) {
						settingsEditor._trigger('done', null, response);
					});
			});
	},

	_create: function() {
		this.page = this.options.page;
		this.bind();
	}
});