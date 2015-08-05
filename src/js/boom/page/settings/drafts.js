$.widget('boom.pageSettingsDrafts', {
	bind: function() {
		var draftSettings = this,
			page = this.options.page;

		this.element
			.on('click', '.b-page-publish', function() {
				page.publish()
					.done(function(response) {
						draftSettings._trigger('done', response);
						draftSettings.options.settings.show('drafts');
					});
			});
	},

	_create: function() {
		this.bind();
	}
});