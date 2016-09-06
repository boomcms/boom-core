$.widget('boom.pageSettingsDefault', {
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
						new boomNotification('Page settings saved').show();
					});
			});
	},

	_create: function() {
		this.page = this.options.page;
		this.bind();

		this.element.find('time').localTime();
	}
});