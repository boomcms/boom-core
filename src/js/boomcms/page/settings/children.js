$.widget('boom.pageSettingsChildren', {
	bind: function() {
		var settingsEditor = this,
			page = this.options.page;

		this.element
			.on('change', 'select[name="children_ordering_policy"]', function() {
				if ($(this).find('option:selected').val() === 'sequence') {
					settingsEditor.$reorderButton.show();
				} else {
					settingsEditor.$reorderButton.hide();
				}
			})
			.on('click', '#b-page-settings-children-reorder', function(e) {
				e.preventDefault();
		
				$.get('/boomcms/search/pages', {parent: page.id})
					.done(function(pages) {
						var sortDialog = new boomDialog({
							msg: "<div></div>",
							title: 'Reorder child pages',
							width: 'auto',
							open: function() {
								var $ul = $('<ul>')
									.attr('id', 'b-page-settings-children-sort')
									.appendTo(sortDialog.contents);

								for (var i = 0; i < pages.length; i++) {
									$('<li>')
										.attr('data-id', pages[i].id)
										.append(
											$('<span>')
												.addClass('title fa fa-bars')
												.text(pages[i].title)
										)
										.appendTo($ul);
								}
										
								$ul.sortable();
							}
						});

						sortDialog.done(function() {
							var sequences = sortDialog.contents.find('li').map(function() {
								return $(this).attr('data-id');
							}).get();

							$.post(settingsEditor.sortUrl, {sequences: sequences})
								.done(function() {
									new boomNotification('Child page ordering saved');
								});
						});
					});
			})
			.on('click', '.b-button-cancel', function(e) {
				e.preventDefault();

				settingsEditor.options.settings.show('children');
			})
			.on('click', '.b-button-save', function(e) {
				e.preventDefault();

				page
					.saveSettings('children', settingsEditor.element.find('form').serialize())
					.done(function() {
						new boomNotification('Child page settings saved');
					});
			});;
	},

	_create: function() {
		this.$reorderButton = this.element.find('#b-page-settings-children-reorder');
		this.sortUrl = '/boomcms/page/settings/sort_children/' + this.options.page.id;

		this.bind();
	}
});