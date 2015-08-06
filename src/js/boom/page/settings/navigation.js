$.widget('boom.pageSettingsNavigation', $.boom.pageSettingsDefault, {
	bind: function() {
		var settingsEditor = this,
			section = settingsEditor.options.section;

		this.element
			.on('click', '.b-button-cancel', function(e) {
				e.preventDefault();

				settingsEditor.options.settings.show(section);
			})
			.on('click', '.b-button-save', function(e) {
				e.preventDefault();

				settingsEditor.page.saveSettings(section, settingsEditor.element.find('form').serialize())
					.done(function() {
						new boomNotification('Page settings saved');
					});
			});
	},

	_create: function() {
		var $el = this.element;
		this.page = this.options.page;
		this.bind();

		$el.find('.boom-tree').pageTree({
			active: $el.find('input[name=parent_id]').val(),
			onPageSelect : function(page) {
				$el.find('input[name=parent_id]').val(page.pageId);
			}
		});
	}
});