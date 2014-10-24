boomPage.prototype.saveSettings = function(url, data, success_message) {
	return $.boom.post(url, data)
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
					$( 'input[name=parent_id]' ).val(page.page_id);
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
		url = '/cms/page/settings/search/' + page.id;

	new boomDialog({
		url : url,
		title : 'Search Settings',
		width : 500
	}).done(function() {
		page.saveSettings(url, $(this).find("form").serialize(), 'Page search settings saved');
	});
};

boomPage.prototype.tags = function() {
	var page = this;

	new boomDialog({
		url: '/cms/tags/page/list/' + page.id,
		title: 'Page tags',
		width: 440,
		cancelButton : false,
		open: function() {
			$('#b-tags').tagger({
				type: 'page',
				id: page.id
			});
		}
	});
};

boomPage.prototype.urls = function() {
	var urlEditor = new boomUrlEditor(this);
	urlEditor.open();
};

boomPage.prototype.featureimage = function() {
	var page = this;

	new boomPageFeatureEditor(this)
		.done(function(response) {
			page.toolbar.status.set(response);
		});
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

			$(this).find('select').on('change', function() {
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

//	dialog = new boomDialog({
//		url: url,
//		title: 'Page visibility',
//		width: 440,
//		open: function() {
//			$('#toggle-visible:checkbox').unbind('change').change(function(){
//
//				if (this.checked) {
//
//					$('#visible-to, #visible-to-time').removeAttr('disabled');
//
//					if ($('#visible-to').val().toLowerCase().trim() == 'forever') {
//
//						$('#visible-to').val('');
//					}
//
//					$('#visible-to').focus();
//
//				} else {
//
//					$('#visible-to, #visible-to-time').attr('disabled', 'disabled');
//
//					if (!$('#visible-to').val().trim().length) {
//
//						$('#visible-to').val('forever');
//					}
//
//					$('#visible-to').blur();
//				}
//			});
//
//			$('#visible').on('change', function() {
//				switch( $( this ).val() ) {
//					case '1':
//						$( '#visible-from' ).removeAttr( 'disabled' );
//					break;
//					case '0':
//						$( '#visible-from' ).attr( 'disabled', 'disabled' );
//						$( '#visible-to' ).attr( 'disabled', 'disabled' );
//					break;
//				}
//			});
//
//			if ($('#visible').val() == '0') {
//				$( '#visible-from' ).attr( 'disabled', 'disabled' );
//				$( '#visible-to' ).attr( 'disabled', 'disabled' );
//			}
//		}
//	});
//
//	dialog.done(function() {
//		page.saveSettings(url, $(this).find("form").serialize(), 'Page visibility settings saved')
//			.done(function(response) {
//				deferred.resolve(response);
//			});
//	});

//	return deferred;
};

boomPage.prototype.childsettings = function() {
	var page = this,
		url = '/cms/page/settings/children/' + page.id,
		dialog;

	dialog = new boomDialog({
		url: url,
		title: 'Child page settings',
		width: '510px',
		open: function() {
			$('select[name="children_ordering_policy"]').on('change', function(){
				var reorder_link = $('#b-page-settings-children-reorder');

				if ($(this).val() == 'sequence')
				{
					reorder_link.removeClass('ui-helper-hidden');
				}
				else
				{
					reorder_link.addClass('ui-helper-hidden');
				}
			});

			$('#b-page-settings-children-reorder').on('click', function() {
				var sort_url = '/cms/page/settings/sort_children/' + page.id,
					dialog;

				dialog = new boomDialog({
					url:  sort_url,
					title: 'Reorder child pages',
					width: 'auto',
					open: function() {
						$('#b-page-settings-children-sort').sortable();
					}
				});

				dialog.done(function() {
					var sequences = $('#b-page-settings-children-sort li').map(function(){
						return $(this).attr('data-id');
					}).get();

					page.saveSettings(url, $("form.b-form-settings").serialize())
						.done(function() {
							page.saveSettings('/cms/page/settings/sort_children/' + page.id, {sequences : sequences}, 'Child page ordering saved, reloading page');
						});
				});
			});
		}
	});

	dialog.done(function() {
		page.saveSettings(url, $(this).find("form").serialize(), 'Child page settings saved');
	});
};

boomPage.prototype.adminsettings = function() {
	var page = this,
		url = '/cms/page/settings/admin/' + page.id,
		dialog;

	dialog = new boomDialog({
		url: url,
		title: 'Admin settings',
		width: 'auto'
	});

	dialog.done(function() {
		page.saveSettings(url, $(this).find("form").serialize(), 'Page admin settings saved');
	});
};
