boomPage.prototype.saveSettings = function(url, data, success_message) {
	return $.post(url, data)
		.done(function() {
			if (success_message) {
				new boomNotification(success_message);
			}
		});
};

boomPage.prototype.navigation = function() {
	var page = this,
		url = '/cms/page/settings/navigation/' + page.id,
		dialog;

	dialog = new boomDialog({
		url: url,
		title: 'Navigation',
		width: 570,
		onLoad : function() {
			dialog.contents.find('.boom-tree').pageTree({
				onPageSelect : function(page) {
					$( 'input[name=parent_id]' ).val(page.pageId);
				}
			});
		},
		open: function() {
			var parent_id = $( 'input[name=parent_id]' ).val();
			$( '#page_' + parent_id ).addClass( 'ui-state-active' );
		}
	});

	dialog.done(function() {
		page.saveSettings(url, dialog.contents.find('form').serialize(), 'Page navigation settings saved');
	});
};

boomPage.prototype.search = function() {
	var page = this,
		url = '/cms/page/settings/search/' + page.id,
		dialog;

	dialog = new boomDialog({
		url : url,
		title : 'Search Settings',
		width : 'auto'
	}).done(function() {
		page.saveSettings(url, dialog.contents.find("form").serialize(), 'Page search settings saved');
	});
};

boomPage.prototype.tags = function() {
	new boomPageTagEditor(this);
};

boomPage.prototype.urls = function() {
	var urlEditor = new boomUrlEditor(this);
	urlEditor.open();
};

boomPage.prototype.featureimage = function() {
	new boomPageFeatureEditor(this);
};

boomPage.prototype.template = function() {
	var	page = this,
		url = '/cms/page/version/template/' + page.id,
		dialog;

	dialog = new boomDialog({
		url: url,
		title: 'Page template',
		width: 500,
		open: function() {
			page.template._show_details();

			dialog.contents.on('change', 'select', function() {
				page.template._show_details();
			});
		}
	});

	dialog.done(function() {
		page.saveSettings(url, $("#b-page-version-template").serialize(), 'Page template saved, reloading page')
			.done(function() {
				// Reload the page to show the template change.
				top.location.reload();
			});
	});

	boomPage.prototype.template._show_details = function() {
		var $template = $('#template'),
			$description = $('#description'),
			$count = $('#count'),
			$selected = $template.find('option:selected'),
			description_text = $selected.data('description');

		if (description_text) {
			$description.show().find('p').html($selected.data('description'));
		} else {
			$description.hide();
		}
		$count.find('p').html($selected.data('count'));
	};
};

boomPage.prototype.visibility = function() {
	return new boomPageVisibilityEditor(this);
};

boomPage.prototype.childsettings = function() {
	var page = this,
		url = '/cms/page/settings/children/' + page.id,
		dialog;

	dialog = new boomDialog({
		url: url,
		title: 'Child page settings',
		width: 'auto',
		open: function() {
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

	dialog.done(function() {
		page.saveSettings(url, dialog.contents.find("form").serialize(), 'Child page settings saved');
	});
};

boomPage.prototype.adminsettings = function() {
	var page = this,
		url = '/cms/page/settings/admin/' + page.id,
		dialog;

	dialog = new boomDialog({
		url: url,
		title: 'Admin settings',
		width: '500px'
	})
	.done(function() {
		page.saveSettings(url, dialog.contents.find("form").serialize(), 'Page admin settings saved');
	});
};
