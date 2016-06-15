$.widget('boom.pageSettingsChildren', {
	bind: function() {
		var settingsEditor = this,
			page = this.options.page,
			pages = new BoomCMS.Collections.Pages();

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

				pages.findByParent(page);
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
						new boomNotification('Child page settings saved').show();
					});
			});

		pages.on('sync', function(pages) {
			settingsEditor.sortPages(pages);
		});
	},

	_create: function() {
		this.$reorderButton = this.element.find('#b-page-settings-children-reorder');
		this.sortUrl = '/boomcms/page/' + this.options.page.id + '/settings/sort-children';

		this.bind();
	},

	sortPages: function(pages) {
		var url = this.sortUrl;

		var sortDialog = new boomDialog({
			msg: "<div></div>",
			title: 'Reorder child pages',
			width: 'auto',
			open: function() {
				var $ul = $('<ul>')
					.attr('id', 'b-page-settings-children-sort')
					.appendTo(sortDialog.contents);

				for (var i = 0; i < pages.length; i++) {
					var page = pages.models[i];

					$('<li>')
						.attr('data-id', page.getId())
						.append(
							$('<span>')
								.addClass('title fa fa-bars')
								.text(page.getTitle())
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

			$.post(url, {sequences: sequences})
				.done(function() {
					new boomNotification('Child page ordering saved').show();
				});
		});
	}
});