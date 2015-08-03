$.widget('boom.pageSettingsChildren', {
	_create: function() {
		$('select[name="children_ordering_policy"]').on('change', function(){
			var reorder_link = $('#b-page-settings-children-reorder');

			if ($(this).val() == 'sequence') {
				reorder_link.removeClass('ui-helper-hidden');
			} else {
				reorder_link.addClass('ui-helper-hidden');
			}
		});

		$('#b-page-settings-children-reorder').on('click', function(e) {
			e.preventDefault();

			var sort_url = '/cms/page/settings/sort_children/' + page.id,
				sortDialog;

			sortDialog = new boomDialog({
				url:  sort_url,
				title: 'Reorder child pages',
				width: 'auto',
				open: function() {
					sortDialog.contents.find('#b-page-settings-children-sort').sortable();
				}
			});

			sortDialog.done(function() {
				var sequences = sortDialog.contents.find('li').map(function() {
					return $(this).attr('data-id');
				}).get();

				page.saveSettings(sort_url, {sequences: sequences}, 'Child page ordering saved, reloading page');
			});
		});
	}
});