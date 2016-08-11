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

		var $time = this.element.find('time');

		if ($time.length) {
			var tz = BoomCMS.getTimezone();

			this.element.find('time')
				.each(function() {
					var $this = $(this),
						time = moment($this.attr('datetime')).tz(tz).format('Do MMMM YYYY HH:mm');
console.log($this.attr('datetime'), moment($this.attr('datetime')));
					$this.text(time);
				});
		}
	}
});