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
			})
			.on('click', '.b-navigation-reparent', function(e) {
				var current = settingsEditor.element.find('input[name=parent_id]').val();

				e.preventDefault();

				new boomLinkPicker(new boomLink(null, current), {
						external: false,
						asset: false
					})
					.done(function(link) {
						settingsEditor.element.find('input[name=parent_id]').val(link.getPageId());
				
						settingsEditor.element
							.find('.title')
							.text(link.getTitle())
							.end()
							.find('.uri')
							.text(link.getUrl());
					});
			});
	},

	_create: function() {
		var $el = this.element;
		this.page = this.options.page;
		this.bind();

		$el.find('.boom-tree').pageTree({
			active: $el.find('input[name=parent_id]').val(),
			onPageSelect: function(page) {
				$el.find('input[name=parent_id]').val(page.pageId);
			}
		});
	}
});