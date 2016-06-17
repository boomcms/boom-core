$.widget('boom.pageSettingsNavigation', $.boom.pageSettingsDefault, {
	bindReparent: function() {
		var settingsEditor = this;

		this.element
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
		this.bindReparent();

		$el.find('.boom-tree').pageTree({
			active: $el.find('input[name=parent_id]').val(),
			onPageSelect: function(page) {
				$el.find('input[name=parent_id]').val(page.pageId);
			}
		});
	}
});