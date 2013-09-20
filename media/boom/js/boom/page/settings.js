$.widget('boom.page', $.boom.page, {

	/**
	* @class
	* @name $.boom.page.settings
	* @static
	*/
	settings:
		/** @lends $.boom.page.settings */
		{
		/** @function */
		save: function( url, data, message) {
			$.boom.loader.show();

			return $.post(
				url, data
			)
			.always( function( response ) {
				$.boom.loader.hide();
			})
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
		},

		/**
		* @class
		* @name $.boom.page.settings.navigation
		* @static
		*/
		navigation:
			/** @lends $.boom.page.settings.navigation */
			{

			/** @function */
			edit: function() {
				$.boom.log( 'opening navigation settings' );

				$.boom.dialog.open({
					url: '/cms/page/settings/navigation/' + $.boom.page.options.id + '?vid=' + $.boom.page.options.vid,
					// cache: true,
					title: 'Navigation',
					width: 570,
					onLoad : function() {

						$.boom.util.page_tree( $( this ).find( '.boom-tree' ) )
							.progress( function( page ){
								$( 'input[name=parent_id]' ).val( page.page_id );
							});

					},
					callback: function() {
						$.boom.page.settings.save(
							'/cms/page/settings/navigation/' + $.boom.page.options.id,
							$(this).find('form').serialize(),
							"Page navigation settings saved."
						);
					},
					open: function() {

						var parent_id = $( 'input[name=parent_id]' ).val();
						$( '#page_' + parent_id ).addClass( 'ui-state-active' );
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.search
		* @static
		*/
		search:
			/** @lends $.boom.page.settings.search */
			{

			/** @function */
			edit: function( event ) {

				$.boom.dialog.open({
					url: '/cms/page/settings/search/' + $.boom.page.options.id + '?vid=' + $.boom.page.options.vid,
					// cache: true,
					title: 'Search Settings',
					width: 500,
					callback : function(){

						$.boom.page.settings.save(
							'/cms/page/settings/search/' + $.boom.page.options.id,
							$this.find("form").serialize(),
							"Page search settings saved."
						);
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.tags
		* @static
		*/
		tags:
			/** @lends $.boom.page.settings.tags */
			{

			/** @function */
			edit: function( event ) {

				var self = this;

				$.boom.dialog.open({
					url: '/cms/tags/page/list/' + $.boom.page.options.id,
					// cache: true,
					title: 'Page tags',
					width: 440,
					callback: function(){
					},
					open: function() {
						$('#b-tags').tagger({
							type: 'page',
							id: $.boom.page.options.id
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
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.urls
		* @static
		*/
		urls:
			/** @lends $.boom.page.settings.urls */
			{

			/** @function */
			edit: function( event ) {

				var self = this;

				$.boom.dialog.open({
					url: '/cms/page/urls/list/' + $.boom.page.options.id,
					// cache: true,
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
									url: '/cms/page/urls/add/' + $.boom.page.options.id,
									event: event,
									title: 'Add URL',
									width: 500,
									// cache: true,
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
			},

			/** @function */
			bind: function() {

				var self = this;

				//  Each url in the list has a radio button whic toggles whether the url is a primary url
				// and a checkbox to toggle whether a secondary url redirects to the primary url.
				$('.b-urls-primary, .b-urls-redirect').change(function(){
					$url = $(this).closest('li');
					redirect = $url.find('.b-urls-redirect').is(':checked')? 1: 0;
					primary = $url.find('.b-urls-primary').is(':checked')? 1 : 0;

					$.post('/cms/page/urls/save/' + $.boom.page.options.id, {
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
			},

			/** @function */
			add: function() {

				var self = this;
				var form = $('#b-form-addurl');
				var new_url = $( 'input[name=url]' ).val();
				new_url = new_url.split( ' ' ).join( '-' );
				$( 'input[name=url]' ).val( new_url );

				$.boom.loader.show();

				$
					.post('/cms/page/urls/add/' + $.boom.page.options.id, form.serialize())
					.done( function(response){

						$.boom.loader.hide();

						var add_url = new $.Deferred();

						add_url.done( function(){
							// show a notifcation and refresh the URL list.
							$.boom.growl.show('Url added.');
							$( '#b-page-settings-urls' )
								.parent()
								.load( '/cms/page/urls/list/' + $.boom.page.options.id, function(){
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
			},

			/** @function */
			move: function( new_url ) {

				var move_url = new $.Deferred();
				var move_dialog;
				var form_url = '/cms/page/urls/move/' + $.boom.page.options.id + '?url=' + new_url;

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

					return $.post( form_url )
					.done( function( response ) {
						//$.boom.dialog.destroy( move_dialog );

						$.boom.loader.hide();
					});
				});

			},

			/** @function */
			remove: function( item ) {

				$.boom.dialog.confirm(
					'Please confirm',
					'Are you sure you want to remove this URL? <br /><br /> This will delete the URL from the database and cannot be undone!'
				)
				.done( function(){

					$.boom.loader.show();

					$
						.post(
							'/cms/page/urls/delete/' + $.boom.page.options.id,
						 	{
								location: $.trim( item.attr( 'data-url' ) )
							}
						)
						.done( function(){

							$.boom.loader.hide();
							item.remove();
						});
				});

			}
		},

		/**
		* @class
		* @name $.boom.page.settings.featureimage
		* @static
		*/
		featureimage:
			/** @lends $.boom.page.settings.featureimage */
			{

			/** @function */
			edit: function( event ){
				var url = '/cms/page/version/feature/' + $.boom.page.options.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.options.vid,
					title: 'Page feature image',
					width: 300,
					// cache: true,
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
										$( '#boom-feature-remove' ).button( 'enable' );
										$( '#boom-featureimage-none' ).hide();
									});
							}
						},
						{
							text: 'Remove',
							id: 'boom-feature-remove',
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
									$( '#boom-feature-remove' ).button( 'disable' );
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
								$.boom.page.settings.save(
									url,
									$("#boom-form-pagesettings-featureimage").serialize(),
									"Page feature image saved."
								)
								.done(function(response) {
									$.boom.page.setStatus(response);
								});

								$.boom.dialog.destroy( this );
							}
						}
					],
					open: function(){
						$( '#boom-feature-remove' ).button( 'disable' );
					},
					onLoad: function(){
						var asset_id = $('#boom-featureimage-input').val();

						if ( asset_id > 0 ) {
							$( '#boom-featureimage-none' ).hide();
							$( '#boom-feature-remove' ).button( 'enable' );
						}
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.template
		* @static
		*/
		template:
			/** @lends $.boom.page.settings.template */
			{

			/** @function */
			edit: function( event ){

				var url = '/cms/page/version/template/' + $.boom.page.options.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.options.vid,
					title: 'Page template',
					width: 300,
					// cache: true,
					callback: function(){

						$.boom.page.settings.save(
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
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.visibility
		* @static
		*/
		visibility:
			/** @lends $.boom.page.settings.visibility */
			{

			/** @function */
			edit: function( event ){

				var url = '/cms/page/settings/visibility/' + $.boom.page.options.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.options.vid,
					// cache: true,
					title: 'Page visibility',
					width: 440,
					callback: function(){

						$.boom.page.settings.save(
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
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.childsettings
		* @static
		*/
		childsettings:
			/** @lends $.boom.page.settings.childsettings */
			{

			/** @function */
			edit: function( event ){

				$.boom.dialog.open({
					url: '/cms/page/settings/children/' + $.boom.page.options.id + '?vid=' + $.boom.page.options.vid,
					// cache: true,
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
							var url = '/cms/page/settings/sort_children/' + $.boom.page.options.id;
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

									$.boom.page.settings.save(
										'/cms/page/settings/children/' + $.boom.page.options.id,
										$(this).find('form').serialize()
									).done(function() {
										$.boom.page.settings.save(
											url,
											{csrf: $("this").find('input[name=csrf]').val(), sequences: sequences},
											"Child page ordering saved, reloading page."
										).done(function(){
											setTimeout(function() {
												top.location.reload();
											}, 1000);
										});
									});
								}
							})
						});
					},
					callback: function(){

						$.boom.page.settings.save(
							'/cms/page/settings/children/' + $.boom.page.options.id,
							$(this).find("form").serialize(),
							"Child page settings saved."
						);
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.adminsettings
		* @static
		*/
		adminsettings:
			/** @lends $.boom.page.settings.adminsettings */
			{

			/** @function */
			edit: function( event ){

				$.boom.dialog.open({
					url: '/cms/page/settings/admin/' + $.boom.page.options.id + '?vid=' + $.boom.page.options.vid,
					// cache: true,
					title: 'Admin settings',
					width: 'auto',
					callback: function(){

						$.boom.page.settings.save(
							'/cms/page/settings/admin/' + $.boom.page.options.id,
							$(this).find("form").serialize(),
							"Page admin settings saved."
						);

					}
				});
			}
		}
	}
});