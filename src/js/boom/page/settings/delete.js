$.widget('boom.pageSettingsDelete', {
	bind: function() {
		var settingsEditor = this,
			page = this.page;

		this.deleteOptions = {
			redirectTo: 0,
			reparentChildrenTo: 0
		};

		this.element
			.on('change', 'input[type=radio][name=urls]', function() {
				var $this = $(this);

				if ($this.val() == 1 && settingsEditor.deleteOptions.redirectTo === 0) {
					new boomLinkPicker(null, {external: false, asset: false})
						.done(function(link) {
							settingsEditor.deleteOptions.redirectTo = link.getPageId();
							
							$this.closest('label')
								.find('.target')
								.show()
								.find('span')
								.text(link.getTitle());
						})
						.fail(function() {
								settingsEditor.element
									.find('input[type=radio][name=urls][value=0]')
									.prop('checked', true);
						});
				}
			})
			.on('change', 'input[type=radio][name=children]', function() {
				var $this = $(this);

				if ($this.val() == 1 && settingsEditor.deleteOptions.reparentChildrenTo === 0) {
					new boomLinkPicker(null, {external: false, asset: false})
						.done(function(link) {
							settingsEditor.deleteOptions.reparentChildrenTo = link.getPageId();
							
							$this.closest('label')
								.find('.target')
								.show()
								.find('span')
								.text(link.getTitle());
						})
						.fail(function() {
							settingsEditor.element
								.find('input[type=radio][name=children][value=0]')
								.prop('checked', true);
						});
				}
			})
			.on('click', 'a.edit', function() {
				var $this = $(this),
					option = $this.attr('data-option'),
					link = new boomLink(null, settingsEditor.deleteOptions[option], $this.siblings('span').text());

					new boomLinkPicker(link, {external: false, asset: false})
						.done(function(link) {
							settingsEditor.deleteOptions[option] = link.getPageId();
							
							$this.closest('label')
								.find('.target')
								.show()
								.find('span')
								.text(link.getTitle());
						});
			})
			.on('click', '#b-page-delete-confirm', function(e) {
				e.preventDefault();

				page.delete(settingsEditor.deleteOptions)
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