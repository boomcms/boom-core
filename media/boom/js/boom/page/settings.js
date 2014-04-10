boomPage.prototype.saveSettings = function(url, data, success_message) {
	return $.boom.post(url, data)
		.done(function() {
			if (success_message) {
				$.boom.growl.show(success_message);
			}
		});
};

boomPage.prototype.navigation = function() {
	var page = this,
		url = '/cms/page/settings/navigation/' + page.id;

	$.boom.dialog.open({
		url: url,
		title: 'Navigation',
		width: 570,
		onLoad : function() {
			$(this).find('.boom-tree').pageTree({
				onPageSelect : function(page) {
					$( 'input[name=parent_id]' ).val( page.page_rid );
				}
			});
		},
		callback: function() {
			page.saveSettings(url, $(this).find('form').serialize(), 'Page navigation settings saved');
		},
		open: function() {
			var parent_id = $( 'input[name=parent_id]' ).val();
			$( '#page_' + parent_id ).addClass( 'ui-state-active' );
		}
	});
};

boomPage.prototype.search = function() {
	var page = this,
		url = '/cms/page/settings/search/' + page.id;

	$.boom.dialog.open({
		url : url,
		title : 'Search Settings',
		width : 500,
		callback : function() {
			page.saveSettings(url, $(this).find("form").serialize(), 'Page search settings saved');
		}
	});
};

boomPage.prototype.tags = function() {
	var page = this,
		dialog;

	dialog = $.boom.dialog.open({
		url: '/cms/tags/page/list/' + page.id,
		title: 'Page tags',
		width: 440,
		open: function() {
			$('#b-tags').tagger({
				type: 'page',
				id: page.id
			});
		},
		buttons: [
			{
				text: 'Close',
				class : 'b-button',
				icons: { primary : 'b-button-icon b-button-icon-accept' },
				click: function(event) {
					$.boom.dialog.destroy(dialog);
				}
			}
		]
	});
};

boomPage.prototype.urls = function() {
	var urlEditor = new boomUrlEditor(this);
	urlEditor.open();
};

boomPage.prototype.featureimage = function() {
	var	page = this,
		url = '/cms/page/version/feature/' + page.id;

	$.boom.dialog.open({
		url: url,
		title: 'Page feature image',
		width: 300,
		buttons: [
			{
				text: 'Add',
				id: 'boom-feature-add',
				class : 'b-button',
				icons: { primary: 'b-button-icon b-button-icon-add' },
				click: function(){
					$.boom.assets
						.picker({
							asset_rid : $('#boom-featureimage-input').val()
						})
						.done( function( rid ){
							$('#boom-featureimage-img').attr( 'src', '/asset/view/' + rid + '/250/80').show();
							$('#boom-featureimage-input').val( rid );
							$( '#b-feature-remove' ).button( 'enable' );
							$( '#boom-featureimage-none' ).hide();
						});
				}
			},
			{
				text: 'Remove',
				class : 'b-button',
				id: 'b-feature-remove',
				icons: { primary: 'b-button-icon b-button-icon-delete' },
				click: function(){
					var dialog = $(this);
					$.boom.dialog.confirm(
						'Please confirm',
						"Are you sure you want to do delete this page's feature image?"
					)
					.done( function(){

						$('#boom-featureimage-img').attr( 'src', '').hide();
						$('#boom-featureimage-input').val( 0 );
						$( '#b-feature-remove' ).button( 'disable' );
						$( '#boom-featureimage-none' ).show();
					});
				}
			},
			{
				text: 'Cancel',
				class : 'b-button',
				icons: { primary: 'b-button-icon b-button-icon-cancel' },
				click: function(){

					$.boom.dialog.destroy( this );
				}
			},
			{
				text: 'Okay',
				class : 'b-button',
				icons: { primary: 'b-button-icon b-button-icon-accept' },
				click: function() {
					page.saveSettings(url, $("#boom-form-pagesettings-featureimage").serialize(), '"Page feature image saved')
						.done(function(response) {
							page.toolbar.status.set(response);
						});

					$.boom.dialog.destroy(this);
				}
			}
		],
		open: function(){
			$( '#b-feature-remove' ).button( 'disable' );
		},
		onLoad: function(){
			var asset_id = $('#boom-featureimage-input').val();

			if ( asset_id > 0 ) {
				$( '#boom-featureimage-none' ).hide();
				$( '#b-feature-remove' ).button( 'enable' );
			}
		}
	});
};

boomPage.prototype.template = function() {
	var	page = this,
		url = '/cms/page/version/template/' + page.id;

	$.boom.dialog.open({
		url: url,
		title: 'Page template',
		width: 500,
		callback: function() {
			page.saveSettings(url, $("#b-page-version-template").serialize(), 'Page template saved, reloading page')
				.done(function() {
					// Reload the page to show the template change.
					top.location.reload();
				});

		},
		open: function() {
			page.template._show_details();

			$(this).find('select').on('change', function() {
				page.template._show_details();
			});
		}
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
	var	page = this,
		url = '/cms/page/settings/visibility/' + page.id,
		deferred = new $.Deferred();

	$.boom.dialog.open({
		url: url,
		title: 'Page visibility',
		width: 440,
		callback: function(){
			page.saveSettings(url, $(this).find("form").serialize(), 'Page visibility settings saved')
				.done(function(response) {
					deferred.resolve(response);
				});
		},
		open: function(){

			$('#toggle-visible:checkbox').unbind('change').change(function(){

				if (this.checked) {

					$('#visible-to, #visible-to-time').removeAttr('disabled');

					if ($('#visible-to').val().toLowerCase().trim() == 'forever') {

						$('#visible-to').val('');
					}

					$('#visible-to').focus();

				} else {

					$('#visible-to, #visible-to-time').attr('disabled', 'disabled');

					if (!$('#visible-to').val().trim().length) {

						$('#visible-to').val('forever');
					}

					$('#visible-to').blur();
				}
			});

			$('#visible').on('change', function() {
				switch( $( this ).val() ) {
					case '1':
						$( '#visible-from' ).removeAttr( 'disabled' );
					break;
					case '0':
						$( '#visible-from' ).attr( 'disabled', 'disabled' );
						$( '#visible-to' ).attr( 'disabled', 'disabled' );
					break;
				}
			});

			if ($('#visible').val() == '0') {
				$( '#visible-from' ).attr( 'disabled', 'disabled' );
				$( '#visible-to' ).attr( 'disabled', 'disabled' );
			}
		}
	});

	return deferred;
};

boomPage.prototype.childsettings = function() {
	var page = this,
		url = '/cms/page/settings/children/' + page.id;

	$.boom.dialog.open({
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
				var sort_url = '/cms/page/settings/sort_children/' + page.id;
				$.boom.dialog.open({
					url:  sort_url,
					title: 'Reorder child pages',
					width: 'auto',
					open: function() {
						$('#b-page-settings-children-sort').sortable();
					},
					callback: function(){
						var sequences = $('#b-page-settings-children-sort li').map(function(){
							return $(this).attr('data-id');
						}).get();

						page.saveSettings(url, $("form.b-form-settings").serialize())
							.done(function() {
								page.saveSettings('/cms/page/settings/sort_children/' + page.id, {sequences : sequences}, 'Child page ordering saved');
							});
					}
				});
			});
		},
		callback: function(){
			page.saveSettings(url, $(this).find("form").serialize(), 'Child page settings saved');
		}
	});
};

boomPage.prototype.adminsettings = function() {
	var page = this,
		url = '/cms/page/settings/admin/' + page.id;

	$.boom.dialog.open({
		url: url,
		title: 'Admin settings',
		width: 'auto',
		callback: function(){
			page.saveSettings(url, $(this).find("form").serialize(), 'Page admin settings saved');
		}
	});
};