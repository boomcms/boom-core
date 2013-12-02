function savePageSettings( url, data, message) {
	return $.post(
		url, data
	)
	.done( function(response){
		// success
		if (message)
		{
			$.boom.growl.show( message );
		}
	})
	.fail( function( response ){
		$.boom.dialog.alert('error', response);
	});
};

boomPage.prototype.navigation = function() {
	var page = this;

	$.boom.log( 'opening navigation settings' );

	$.boom.dialog.open({
		url: '/cms/page/settings/navigation/' + page.id,
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
			savePageSettings(
				'/cms/page/settings/navigation/' + page.id,
				$(this).find('form').serialize(),
				"Page navigation settings saved."
			);
		},
		open: function() {
			var parent_id = $( 'input[name=parent_id]' ).val();
			$( '#page_' + parent_id ).addClass( 'ui-state-active' );
		}
	});
};

boomPage.prototype.search = function() {
	var page = this;

	$.boom.dialog.open({
		url: '/cms/page/settings/search/' + page.id,
		title: 'Search Settings',
		width: 500,
		callback : function(){
			var $this = $(this);

			savePageSettings(
				'/cms/page/settings/search/' + page.id,
				$this.find("form").serialize(),
				"Page search settings saved."
			);
		}
	});
};

boomPage.prototype.tags = function() {
	var page = this;

	$.boom.dialog.open({
		url: '/cms/tags/page/list/' + page.id,
		title: 'Page tags',
		width: 440,
		callback: function(){
		},
		open: function() {
			$('#b-tags').tagger({
				type: 'page',
				id: page.id
			});
		},
		buttons: [
			{
				text: 'Close',
				icons: { primary : 'ui-icon-boom-cancel' },
				click: function( event ){
					$.boom.dialog.destroy( this );
				}
			}
		]
	});
};

boomPage.prototype.urls = function() {
	var page = this;

	$.boom.dialog.open({
		url: '/cms/page/urls/list/' + page.id,
		title: 'URLs',
		width: 440,
		buttons: [
			{
				text: 'Add URL',
				title: 'Add URL',
				id: 'b-page-settings-url-add',
				icons: { primary : 'ui-icon-boom-add' },
				click: function( event ){
					$.boom.dialog.open({
						url: '/cms/page/urls/add/' + page.id,
						event: event,
						title: 'Add URL',
						width: 500,
						callback: function(){

							self.add();

							$.boom.dialog.destroy( this );

						}
					});
				}
			},
			{
				text: 'Close',
				title: 'Close',
				icons: { primary : 'ui-icon-boom-cancel' },
				click: function( event ){
					$.boom.dialog.destroy( this );
				}
			}
		],
		open: function(){
			self.bind();
		}
	});


	/** @function */
	boomPage.prototype.urls.bind = function() {

		var self = this;

		//  Each url in the list has a radio button whic toggles whether the url is a primary url
		// and a checkbox to toggle whether a secondary url redirects to the primary url.
		$('.b-urls-primary, .b-urls-redirect').change(function(){
			var $url = $(this).closest('li');
			var redirect = $url.find('.b-urls-redirect').is(':checked')? 1: 0;
			var primary = $url.find('.b-urls-primary').is(':checked')? 1 : 0;

			$.post('/cms/page/urls/save/' + self.id, {
				url_id :  $url.attr('data-id'),
				redirect : redirect,
				primary : primary
			})
			.done(function(){
				$url
					.parent()
					.find( '.ui-state-active' )
					.removeClass( 'ui-state-active' )
					.end()
					.find( '.b-urls-primary:checked' )
					.parent()
					.addClass( 'ui-state-active' );
				$.boom.growl.show("URL saved.");
			});
		});

		$( '.b-urls-remove' ).on( 'click', function( event ){

			event.preventDefault();

			var item = $( event.target ).closest( 'li' );

			self.remove( item );
		});
	};

	/** @function */
	boomPage.prototype.urls.add = function() {

		var self = this;
		var form = $('#b-form-addurl');
		var new_url = $( 'input[name=url]' ).val();
		new_url = new_url.split( ' ' ).join( '-' );
		$( 'input[name=url]' ).val( new_url );

		$.post('/cms/page/urls/add/' + page.id, form.serialize())
			.done( function(response) {

				var add_url = new $.Deferred();

				add_url.done( function(){
					// show a notifcation and refresh the URL list.
					$.boom.growl.show('Url added.');
					$( '#b-page-settings-urls' )
						.parent()
						.load( '/cms/page/urls/list/' + self.id, function(){
							$(this).ui();
							self.bind();
						});
				});

				if (response == 'url in use')
				{
					self.move( new_url ).done( function(){
						add_url.resolve();
					});
				}
				else
				{

					add_url.resolve();
				}
			});
	};

	/** @function */
	boomPage.prototype.urls.move = function( new_url ) {

		var move_url = new $.Deferred(),
			page = this,
			move_dialog,
			form_url = '/cms/page/urls/move/' + page.id + '?url=' + new_url;

		// URL is being used on another page.
		// Ask if they want to move it.
		$.boom.dialog.confirm(
			"URL in use",
			"The specified url is already in use on another page. Would you like to move it?"
		)
		.done( function(){
			move_dialog = $.boom.dialog.open({
				url: form_url,
				title: 'Move url',
				deferred: move_url
			});
		});

		return move_url.pipe( function(){

			return $.post( form_url );
		});

	};

	/** @function */
	boomPage.prototype.urls.remove = function( item ) {
		var page = this;

		$.boom.dialog.confirm(
			'Please confirm',
			'Are you sure you want to remove this URL? <br /><br /> This will delete the URL from the database and cannot be undone!'
		)
		.done( function(){

			$.post('/cms/page/urls/delete/' + page.id, {
					location: $.trim( item.attr( 'data-url' ) )
			})
			.done( function() {
				item.remove();
			});
		});
	};
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
				icons: { primary: 'ui-icon-boom-add' },
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
				id: 'b-feature-remove',
				icons: { primary: 'ui-icon-boom-delete' },
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
				icons: { primary: 'ui-icon-boom-cancel' },
				click: function(){

					$.boom.dialog.destroy( this );
				}
			},
			{
				text: 'Okay',
				icons: { primary: 'ui-icon-boom-accept' },
				click: function(){
					savePageSettings(
						url,
						$("#boom-form-pagesettings-featureimage").serialize(),
						"Page feature image saved."
					)
					.done(function(response) {
						page.status.set(response);
					});

					$.boom.dialog.destroy( this );
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
		width: 300,
		callback: function(){

			savePageSettings(
				url,
				$("#b-form-pageversion-template").serialize(),
				"Page template saved, reloading page."
			)
			.done( function(){
				// Reload the page to show the template change.
				top.location.reload();
			});

		},
		open: function(){
		}
	});
};

boomPage.prototype.visibility = function() {
	var	page = this,
		url = '/cms/page/settings/visibility/' + page.id;

	$.boom.dialog.open({
		url: url,
		title: 'Page visibility',
		width: 440,
		callback: function(){

			savePageSettings(
				url,
				$(this).find("form").serialize(),
				"Page visibility settings saved."
			)
			.done(function(response) {
				var icons = {
					1 : 'ui-icon-boom-visible',
					0 : 'ui-icon-boom-invisible'
				};

				$('#b-page-visibility')
					.attr('data-icon', icons[response])
					.find('span.ui-icon')
					.removeClass(icons[0])
					.removeClass(icons[1])
					.addClass(icons[response]);
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
};

boomPage.prototype.childsettings = function() {
	var page = this;

	$.boom.dialog.open({
		url: '/cms/page/settings/children/' + page.id,
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
				var url = '/cms/page/settings/sort_children/' + page.id;
				$.boom.dialog.open({
					url:  url,
					title: 'Reorder child pages',
					width: 'auto',
					open: function() {
						$('#b-page-settings-children-sort').sortable();
					},
					callback: function(){
						var sequences = $('#b-page-settings-children-sort li').map(function(){
							return $(this).attr('data-id');
						}).get();

						savePageSettings(
							'/cms/page/settings/children/' + page.id,
							$("form.b-form-settings").serialize()
						).done(function() {
							savePageSettings(
								url,
								{csrf: $("form.b-form-settings").find('input[name=csrf]').val(), sequences: sequences},
								"Child page ordering saved, reloading page."
							).done(function(){
								setTimeout(function() {
									top.location.reload();
								}, 1000);
							});
						});
					}
				});
			});
		},
		callback: function(){

			savePageSettings(
				'/cms/page/settings/children/' + page.id,
				$(this).find("form").serialize(),
				"Child page settings saved."
			);
		}
	});
};

boomPage.prototype.adminsettings = function() {
	var page = this;

	$.boom.dialog.open({
		url: '/cms/page/settings/admin/' + page.id,
		title: 'Admin settings',
		width: 'auto',
		callback: function(){

			savePageSettings(
				'/cms/page/settings/admin/' + page.id,
				$(this).find("form").serialize(),
				"Page admin settings saved."
			);
		}
	});
};