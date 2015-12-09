$.widget('boom.pageSettingsDrafts', {
	bind: function() {
		var draftSettings = this,
			page = this.options.page;

		this.element
			.on('click', '.b-page-publish', function() {
				page.publish()
					.done(function(status) {
						draftSettings.update({
							action: 'publish',
							status: status
						});
					});
			})
			.on('click', '.b-page-embargo', function() {
				page.embargo()
					.done(function(status) {
						draftSettings.update({
							action: 'embargo',
							status: status
						});
					});
			})
			.on('click', '.b-page-revert', function() {
				page.revertToPublished()
					.done(function(status) {
						draftSettings.update({
							action: 'revert',
							status: status
						});
					});
			})
			.on('click', '.b-page-request-approval', function() {
				page.requestApproval()
					.done(function(status) {
						draftSettings.update({
							action: 'request approval',
							status: status
						});
					});
			})
			.on('click', '.b-page-preview', function() {
				$.boom.editor.state('preview', $(this).attr('data-url'));
			});
	},

	_create: function() {
		this.bind();
	},
	
	update: function(status) {
		this._trigger('done', null, status);
		this.options.settings.show('drafts');
	}
});