$.extend($.boom, {

	/**
	* @class
	* @name $.boom.page
	*/
	page : {

		/** @lends $.boom.page */

		/** @property */
		save_button: $('#b-page-save'),



		/** @function */
		init : function(config) {

			var self = this;
			this.slot_edits = [];

			this.config = $.extend({}, $.boom.config.page, config);

			$.boom.util.cacheImages($.boom.config.cachePageImages);

			this.build();

			this.bind();

			$.boom.log('Page init');

			// FIXME
			window.onbeforeunload = function(){
				if ( $.boom.page.slot_edits.length ){
					return 'You have unsaved changes.';
					$.boom.dialog.confirm(
						'Save changes',
						'You have unsaved changes to this page. Press OK to save these and continue.',
						function(){
							self.save();
						}
					);
				}
			};

		},

		/** @function */
		register : function(config){

			var self = this;

			$(function(){

				$.boom.loader.show();

				// Bind UI events to the iframe contents.
			 	// Adds functionality to boom-sortable, boom-tree, etc. elements in the iframe contents.
			 	$('body').contents().ui();
				$( self.document ).contents().ui();

				$.extend(self.config, config);

				self.editors = [];

				$.boom.log('Page registered for editing: ' + self.config.rid);

				new $.boom.orm('page', { id: self.config.id }).find(function(){

					//if (this.autosave == 't') {
						// TODO
					//}
				});

				$.getScript( $.boom.config.editor.path, function(){
					self.loadPageEditor()
						.done( function(){
							self.editor.init();
						});
				})

			});
		},

		/** @function */
		build : function(){

			var self = this;

			this.document = $( top.document )

			return this;
		},

		/** @function */
		bind : function(){

			var self = this;

			function saveEditorState( state ) {

				$.boom.loader.show();

				$.post('/cms/editor/state', { state: state }, function(){

					top.location.reload();

					$.boom.loader.hide();
				});
			}

			$('.b-button-preview').on('click', function(){
				saveEditorState( $(this).attr('data-preview') );
			});

			$('#b-page-delete').click(function(){

				$.boom.dialog.open({
					width: 350,
					url: '/cms/page/delete/' + self.config.id,
					title: 'Please confirm',
					callback: function(){

						$.post('/cms/page/delete/' + self.config.id, $('#b-page-delete-form').serialize(), function(response){
							$.boom.growl.show("Page deleted, redirecting to parent.");
							top.location = response;
						});
					}
				});
			});
			$('#b-page-addpage').click(function(){

				var button = this;

				$.boom.dialog.open({
					url: '/cms/page/add/' + self.config.id,
					title: $(this).text(),
					callback: function(){

						$.boom.loader.show('modal');

						$.post('/cms/page/add', $('#b-page-add-form').serialize(), function(response){

							$.boom.loader.hide('modal');

							if ( new RegExp('^' + "\/").test( response ) ) {

								top.location = response;
							} else {

								$.boom.dialog.alert('Error', response);
							}

						});
					}
				});
			});
			$('#b-page-save').click(function(){

				self.save();
			});
			$('#b-page-publish').click(function(){
				$.boom.loader.show();

				$.get(
					'/cms/page/publish/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid
				)
				.done( function(response){

					$.boom.loader.hide();

					$('#b-page-publish').hide();
					$("#boom-topbar-revisions").html(response);

				});
			});


			self.settings.init();

			self.settings.bind();

			$.boom.hooks.register('page.init');

			return this;
		},

		/** @function */
		loadPageEditor : function(){

			$.boom.loader.hide();

			var self = this;

			return $.boom.editor.load();

		},

		/** @function */
		save : function(callback, pagedata, requestdata, config) {

			data = pagedata || {};
			self = this;

			if ($.boom.page.editor.isOpen()) {

				$.boom.dialog.alert('Error', 'Please accept or cancel changes in the editor before saving the page.');

				return;
			}

			if (!config || (config.showloader != undefined && config.showloader)) {
				$.boom.loader.show();
			}

			var page =
				$.boom.page.config,
				title =
					this.document.contents().find('#b-page-title').length ?
					this.document.contents().find('#b-page-title').html().text().safeEscape() :
					$('input[name=alttitle]').val();

			data = $.extend(data, {
				title: title || 'Untitled',
				slots: {}
			});

			if (!data.vid) {
				data.vid = this.config.vid;
			}

			$( $.boom.page.slot_edits ).each(function(){

				if ( this.id == 'b-page-title' ) return;

				var
					slot = this.slot;

				// Text slots don't get previewed when changed from default, so has_content doesn't get changed.
				if (slot.has_content == "1" || slot.type == 'text')
				{
					// Don't submit data for chunks which have been inherited from another page.
					// slotobj.page will be 0 when the slot has been edited.
					if (slot.page == self.config.id || slot.page == 0)
					{
						if (!data.slots[slot.type]) {
							data.slots[slot.type] = {};
						}

						if (slot.type != 'text' || this.data != 'Default text.') {
							data.slots[slot.type][slot.name] = this.data;
						}
					}
				}
			});


			$.boom.hooks.register('page.saveError', 'sites', function(vars){

				if (window.console) {

					console.debug('POST data debug:', data);
				}

			}, true);

			console.debug('POST data debug:', data.slots);

			requestdata = $.extend({
				data: JSON.stringify(data)
			}, requestdata);

			console.debug('POST data debug:', requestdata.data);

			$.post( '/cms/page/save/' + this.config.id, requestdata )
			.done(
				function(response){

					$.boom.loader.hide();

					response = $.parseJSON(response);

					// success
					if (typeof response == 'object') {
						console.log( response );
						// Only reload the page if the URL returned is different to the browser URL.
						// This will happen when the page title has been changed and the primary link has been updated.
						if (response.reload && response.reload == true)
						{
							$.boom.growl.show( "Page successfully saved, reloading." );
							$.boom.page.slot_edits = [];
							$.boom.page.save_button.button( 'disable' ).attr( 'title', 'You have no unsaved changes' );

							setTimeout(function(){
								top.location = response.url;
							}, 100);
						}
						else
						{
							self.config.vid = response.vid;
							$('#b-page-publish').show();
							$.boom.growl.show( "Page successfully saved." );
							$.boom.page.slot_edits = [];
							$.boom.page.save_button.button( 'disable' ).attr( 'title', 'You have no unsaved changes' );
						}
					}
					else if (response.match(/<html/i)) {

						// FIXME
						// unexpected app error
						$.boom.dialog.alert('error', 'Sorry, there was an error trying to save the page.<br/>Please try again.');
					} else {
						// managable app error
						$.boom.dialog.alert('error', response);
					}
				});

			/*
			/// get the child page order sequences from the left nav
			sequence = 1;
			$("#navsort > li, .navsort-xy > li").each(function(){
				if (this.id) {
					dataString += "&sequence_" + this.id.replace(/^p/, '') + "=" + sequence;
					sequence++;
				}
			});

			// if callback function is present, then tell back-end not to save session message
			if (callback) dataString += "&nosession=1";
			*/
			/*
			// group tags and users (access control)
			$(".accesscontrol .current").each(function(i){
				var type = this.id.replace(/[0-9]+/, '');
				dataString += '&'+type+(i+1)+'='+this.id.replace(/^[^0-9]+/, '');
				//dataString += '&ac_'+type+(i+1)+'='+this.id.replace(/^[^0-9]+/, '');
			});
			*/
		}
	}
});

$.extend( $.boom.page, {

	/**
	* Common functionality for the embedded CMS toolbar
	* @class
	* @name $.boom.page.toolbar
	*/
	toolbar: {

		/**
		* Editable page DOM
		* @property
		* @type (jQuery Object)
		*/
		page_dom: {},

		/**
		* Editor iframe
		* @property
		* @type (jQuery Object)
		*/
		iframe: {},

		/** @property */
		height: '60px',

		/**
		* Adds some whitespace to prevent the toolbar sitting over links etc.
		* Initialises some useful properties
		* @function
		*/
		init : function() {
			$.boom.log( 'init CMS toolbar' );

			var self = this;

			self.page_dom = $.boom.page.document.contents();
			self.iframe = self.page_dom.find( '#b-page-topbar' );

			self.page_dom
				.find( 'body' )
				.css( 'margin-top', this.height );

			self.iframe.css( 'margin-top', '-' + this.height );
		},

		/**
		* extend the toolbar to cover the entire window
		* @function
		*/
		maximise : function() {
			$.boom.log( 'maximise iframe' );
			this.iframe.show();
			this.iframe.css && this.iframe.css( 'height', '120%' );
		},

		/**
		* minimise the toolbar to allow clicking on the underlying page
		* @function
		*/
		minimise : function() {
			$.boom.log( 'minimise iframe' );
			this.iframe.css && this.iframe.css( 'height', this.height );
		},

		/**
		@function
		*/
		hide : function() {
			this.iframe.slideUp().hide();
			this.page_dom
				.find( 'body' )
				.css( 'margin-top', 0 );
		},

		/**
		@function
		*/
		show : function() {
			this.iframe.show().slideDown();
			this.page_dom
				.find( 'body' )
				.css( 'margin-top', this.height );
		}
	}
});

$.extend($.boom.page, {

	/**
	* @class
	* @name $.boom.page.editor
	*/
	editor : {

		/** @lends $.boom.page.editor */

		elements : {},

		/** @function */
		init : function(){

			var self = this;

			this.config = $.boom.config.editor;

			this.config.pageScripts = [ this.config.stylesheetURL ];

			$.boom.page.toolbar.init();

			this.elements.page_body = $.boom.page.document;

			this.load()
				.done( function(){
					self.bind();
				});

			return this;
		},

		/** @function */
		load : function(){

			var self = this;
			var promise = new $.Deferred();

			$.boom.loader.show();

			this.loadScripts( this.config.pageScripts )
				.pipe( function(){

					return self.loadHTML();

				})
				.done(function(){

					$.boom.loader.hide();

					$.boom.log('Scripts loaded into iFrame');

					promise.resolve();
				});


			return promise;
		},

		/** @function */
		bind : function() {

			var self = this;

			// can we haz link click?
			self.elements.page_body.contents().find('body').unbind('click').click(function(event){

				function isAnchor(target){

					return ( target && target.nodeName == 'A' && !/#|javascript:/.test( target.href ) );
				}

				var target = isAnchor(event.target) ? event.target : $( event.target ).parents('a').get(0);

				if ( isAnchor(target) ) {

					event.preventDefault();

					if ( $.boom.page.slot_edits.length ){

						$.boom.dialog.open({
							msg: 'You have unsaved changes to this page. Save your changes, or discard them and continue.',
							title: 'Save changes',
							width: 300,
							buttons: {
								Discard: function(event){
									$.boom.dialog.destroy(this);
									$.boom.page.slot_edits = [];
									top.location = target.href;
								},
								Save: function(event){
									$.boom.dialog.destroy(this);
									$.boom.page.save();
								}
							}
						});

					} else {
						top.location = target.href;
					}

				}
			});

			function slotBind( config ){

				config = config || {};

				var chunk = this;

				$( this )
				.unbind('click mouseenter mouseleave')
				.one( 'click', function(event){

					//event.target = this;

					var slot =  /\{([^\}]+)\}/.test( this.className ) ?
						this.className.match(/\{([^\}]+)\}/)[1].split(' ') :
						[ 'text' ];

					if (!slot[1]) {
						slot[1] = '';
					}
					if (!slot[2]) {
						slot[2] = 0;
					}
					if (slot[0] == 'text') {
						config.toolbar = $.boom.page.config.editorOptions[slot[1]] || [];
					}

					$.boom.page.slots.edit(event, this, slot, config);

					return false;
				})
				.bind('mouseenter', function(){

					$.boom.page.slots.bindMouseEnter.call(this, self.elements.page_body);
				})
				.bind('mouseleave', function(){

					$.boom.page.slots.bindMouseLeave.call(this, self.elements.page_body);
				});

				// now bind other config events eg sortable

				$.each(config, function(key, val){

					if ( key === 'sortable' && val ) {

						// FIXME

						//$( chunk ).sortable($.extend({}, $.boom.config.sortable, {
						//	axis: 'y'
						//}));
					}
				});
			}

			self.elements.page_body.contents()
			.find('.chunk-slot')
			.each(function(){

				var chunkExp = /\{([^\}]+)\}/, config = {};

				if (chunkExp.test(this.className)) {

					var slotName = this.className.match(chunkExp)[1].split(' ')[1];

					$( this ).addClass( 'boom-tooltip boom-tooltip-follow' );

					if (!this.id) {

						this.id = $.boom.util.dom.uniqueId('boom-chunk-');
					}


					$.each( this.className.split(' '), function(i, val){

						if ( /boom-chunk-option-/.test(val) ) {

							config[ val.replace(/boom-chunk-option-/, '') ] = true;
						}
					});

					slotBind.call( this, config );
				}
			})
			.end()
			.find('#b-page-title').exists(function(){

				slotBind.call( this );
			});

			$.boom.log('Page editor bind');
		},

		/** @function */
		unbind : function(){

			var self = this;

			if (!self.elements.page_body) return;

			self.elements.page_body.contents().find('#b-page-title, .chunk-slot').each(function(){
				$(this).unbind('click mouseleave mouseenter')
				// FIXME
				.removeClass('boom-chunk-mouseenter');
			});

			self.elements.page_body.contents().find('.sortable').each(function(){
				$(this).sortable('destroy');
			});

			$.boom.log('Page editor unbind');
		},

		/** @function */
		loadHTML : function(){

			var html_loaded = new $.Deferred();

			var body = this.elements.page_body.contents().find('body');

			this.elements.hoverOverlay = {
				icon: $('<span class="b-page-chunk-edit-icon ui-icon ui-icon-wrench" />').appendTo(body)
			};

			html_loaded.resolve();

			return html_loaded;
		},

		/** @function */
		loadScripts : function( scripts ){

			var self = this;
			var promise = new $.Deferred();

			$.each(scripts, function(){

				$.get(this, function(data){

					(/js$/.test(this.url)) &&
						self.elements.page_body.contents().find('head').append('<script type="text/javascript">'+data+'</script>');

					(/css$/.test(this.url)) &&
						self.elements.page_body.contents().find('head').append('<style type="text/css">'+data+'</style>');

				});
			});

			return promise.resolve();
		},

		//
		/**
		TODO
		@function
		*/
		isOpen : function(){
			return false;
		}
	}
});

$.extend($.boom.page, {

	/**
	* @class
	* @name $.boom.page.slots
	*/
	slots : {
		/** @lends $.boom.page.slots */

		/** @function */
		edit : function(event, elem, slot, slotconfig){
			event.preventDefault();
			event.stopPropagation();

			this.hideEditOverlay();

			$.boom.log('Chunk slot edit');
			console.log( elem );

			$.boom.page.editor.unbind();

			$.boom.data.slotClicked = true;

			var
				node = elem.nodeName.toLowerCase(),
				config = $.extend({
					slot: slot
				}, slotconfig),
				type = 'chunk' + slot[0].ucfirst(),
				$elem = $( elem );

			$elem[ type ]( config );

			return false;
		},

		/** @function */
		bindMouseEnter : function(page_body){

			$(this).addClass('b-chunk-mouseenter ui-helper-clearfix');

			var data = {
				width: $(this).outerWidth(),
				height: $(this).outerHeight(),
				offset: $(this).offset()
			};

			$(this).removeClass('ui-helper-clearfix');

			$.boom.page.slots.showEditOverlay(data);
		},

		/** @function */
		bindMouseLeave : function(page_body){

			$(this).removeClass('b-chunk-mouseenter');

			$.boom.page.slots.hideEditOverlay();
		},

		/** @function */
		showEditOverlay : function(data) {

			$.boom.page.editor.elements.hoverOverlay.icon
				.css({
					top: data.offset.top - 10,
					left: data.offset.left - 10
				})
				.show();
		},

		/** @function */
		hideEditOverlay : function(){

			$.boom.page.editor.elements.hoverOverlay.icon.hide();
		}
	}
});

$.extend($.boom.page, {

	/**
	* @class
	* @name $.boom.page.undo
	*/
	undo : {
		/** @lends $.boom.page.undo */

		maxAmount: 5,

		/** @function */
		register: function(msg, data, callback) {

			var time = new Date(), hours = time.getHours().toString(), minutes = time.getMinutes().toString();

			if (hours.length === 1) hours = '0' + hours;
			if (minutes.length === 1) minutes = '0' + minutes;

			msg += ' <small>@ ' + hours + ':' + minutes + '</small>';

			$.boom.log('Added new event to undo');

			$('#b-page-undo-splitbutton')
			.data('splitbutton')
			.add(msg, data, function(event, data){

				callback(event, data);

				var menu = $(this).parents('ul:first');

				$(this).parent().remove();

				if ( !menu.find('li').length ) {

					$('#b-page-undo-splitbutton').find('button').button('disable');
				}

				$.boom.page.editor.bind();
			}, 'top');

			var menu = $('#b-page-undo-splitbutton').splitbutton('getMenu');

			if (menu.children().length > this.maxAmount ) {
				menu.find('li:last').remove();
			}

			$('#b-page-undo-splitbutton')
				.find('button')
				.button('enable');
		}
	},

	/**
	* @class
	* @name $.boom.page.settings
	*/
	settings: {
		/** @lends $.boom.page.settings */

		/** @function */
		init: function() {
			// some initialisation code should probably go here.

		},

		/** @function */
		bind: function() {

			var self = this;
			var i = 0;

			var settings = [
				'navigation',
				'links',
				'search',
				'tags',
				'childsettings',
				'adminsettings'
				];



			$('#boom-page-settings-menu').splitbutton({
				items: self._build_menu( settings ),
				itemclick : function(event){

					if (!$.boom.page.config.id) {

						$.boom.loader.hide('modal');

						$.boom.dialog.alert('Error', 'The page is still loading, please try again.');

						return false;
					}
				},
				width: 'auto',
				menuPosition: 'right',
				split: false
			});

			var template_settings = [
				'featureimage',
				'template',
				'embargo'
			];

			$('#boom-page-template-menu').splitbutton({
				items: self._build_menu( template_settings ),
				itemclick : function(event){

					if (!$.boom.page.config.id) {

						$.boom.loader.hide('modal');

						$.boom.dialog.alert('Error', 'The page is still loading, please try again.');

						return false;
					}
				},
				width: 'auto',
				menuPosition: 'right',
				split: false
			});

			var buttons = [
				'information',
				'visibility',
				'history'
			];

			for ( i in buttons ) {

				self.register( buttons[ i ] );

			}

		},

		/** @function */
		register: function( class_name ) {

			var self = this;

			$( '#boom-page-' + class_name )
				.bind( 'boomclick', function( event ) {

					self[ class_name ].edit( event );
				})
				.click( function(){

					$(this).trigger('boomclick');
				});

		},

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
				$.boom.growl.show( message );
			})
			.fail( function( response ){
				$.boom.dialog.alert('error', response);
			});
		},

		/** @function */
		_build_menu: function ( settings ) {

			var self = this;
			var menu_items = {};

			for ( i in settings ) {

				var class_name = settings[i];
				var menu_item = self[ class_name ].label;
				var menu_handler = self[ class_name ].menu_handler;

				self.register( class_name );


				menu_items[ menu_item ] = menu_handler;

				$.boom.log( 'initialising ' + class_name );

			};

			return menu_items;
		},
		
		/**
		* @class
		* @name $.boom.page.settings.navigation
		*/
		navigation: {
			/** @lends $.boom.page.settings.navigation */

			/**
			Menu label
			@property
			*/
			label: 'Navigation',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-navigation' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ) {

				$.boom.log( 'opening navigation settings' );

				var item_selected = function( $item ){

					$item
						.addClass( 'ui-state-active' )
						.parents( '.boom-tree' )
						.find( 'a.ui-state-active' )
						.not( $item )
						.removeClass( 'ui-state-active' );

				};

				var parent_treeConfig = $.extend({}, $.boom.config.tree, {
					toggleSelected: false,
					onClick: function( event ){

						event.preventDefault();

						item_selected( $(this) );

						$( 'input[name=parent_id]' ).val( event.data.rid );
					},
					onToggle: function( event ){

						var list_ready = $.Deferred();
						var children = $.ajax( {
							type: 'POST',
							url: '/plugin/page/children.json',
							data: {parent : event.data.rid, page: 0, perpage: 0},
							dataType: 'json'
						} );
						children.done( function( data ) {

							var children = $('<ul></ul>');

							$( data ).each( function( i, item ){
								var li = $('<li></li>')
									.data( 'children', parseInt(item.has_children) )
									.appendTo( children );
								$('<a></a>')
									.attr( 'id', 'page_' + item.id )
									.attr( 'href', item.url )
									.attr( 'rel', item.id )
									.text( item.title )
									.appendTo( li );
							});

							var parent_id = $( 'input[name=parent_id]' ).val();
							children.find( '#page_' + parent_id ).addClass( 'ui-state-active' );


							list_ready.resolve( { childList: children } );
						});

						return list_ready;
					}
				});

				$.boom.dialog.open({
					url: '/cms/page/settings/navigation/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid,
					event: event,
					// cache: true,
					title: 'Navigation',
					width: 570,
					treeConfig: parent_treeConfig,
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								'/cms/page/settings/navigation/' + $.boom.page.config.id,
								$("#boom-form-pagesettings-navigation").serialize(),
								"Page navigation settings saved."
							);

							$.boom.dialog.destroy( this );
						}
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
		*/
		search: {
			/** @lends $.boom.page.settings.search */

			/**
			Menu label
			@property
			*/
			label: 'Search',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-search' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ) {

				$.boom.dialog.open({
					url: '/cms/page/settings/search/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid,
					// cache: true,
					event: event,
					title: 'Search Settings',
					width: 500,
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								'/cms/page/settings/search/' + $.boom.page.config.id,
								$("#boom-form-pagesettings-search").serialize(),
								"Page search settings saved."
							);

							$.boom.dialog.destroy( this );
						}
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.tags
		*/
		tags: {
			/** @lends $.boom.page.settings.tags */

			/**
			Menu label
			@property
			*/
			label: 'Page tags',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-tags' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ) {

				var self = this;

				$.boom.dialog.open({
					url: '/cms/page/settings/tags/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid,
					event: event,
					// cache: true,
					title: 'Page tags',
					width: 440,
					buttons: {
						Close: function(){
							$.boom.dialog.destroy( this );
						}
					},
					open: function() {
						// The add tag input box is hidden when the modal window opens.
						// Show it and give it focus when the add button is clicked.
						$('#b-pagesettings-tags-add').click(function(){
							$('#b-pagesettings-tags-add-name').show().focus();
						});

						// Hide the add tag input box when it loses focus.
						$('#b-pagesettings-tags-add-name').blur(function(){
							$('#b-pagesettings-tags-add-name').val('').hide();
						});

						// When hovering over an existing tag show a button to remove the tag from the page.
						// Then hide the button again when the mouse moves away.
						$('.b-tags-list li').mouseenter(function(){
							// If the ui-icon and ui-icon-close clases are added in the HTML then the crosses aren't hidden when the modal opens.
							// So we only add these classes when we need to show them.
							$(this)
								.find('a')
								.addClass('ui-icon ui-icon-close')
								.show()
								.end()
								.find('span')
								.addClass('active');
		 					// Adding the active class changes the margin-left of the text so that it doesn't shift to the right when then delete link becomes visible.
						}).mouseleave(function(){
							$(this)
								.find('a')
								.hide()
								.end()
								.find('span')
								.removeClass('active');
						});

						// Remove a tag from the page.
						$('.b-tags-remove').click(function(event){
							event.preventDefault();

							$.boom.loader.show();

							tag = $(this).attr('href');
							$.post(
								'/cms/page/settings/tags/' + $.boom.page.config.id,
								{action: 'remove', tag : tag}
								)
								.done(function(){
									$(event.target).closest('li').remove();
									$.boom.loader.hide();
								});
						});

						// Add a tag to the tag.
						$('#b-pagesettings-tags-add-name').autocomplete({
							delay: 200, // Time to wait after keypress before making the AJAX call.
							source: function(request, response){
								$.ajax({
									url: '/cms/autocomplete/tags',
									dataType: 'json',
									data: {
										text : $('#b-pagesettings-tags-add-name').val(),
										type : 2 // Restricts the returned tags to page tags.
									}
								})
								.done(function(data) {
									response(data)
								});
							},
							select: function(event, ui){
								self.add(ui.item.value);
							}
						})
						.keypress(function(e){
							// Add a tag when the enter key is pressed.
							// This allows us to add a tag which doesn't already exist.
							if (e.which == 13) {
								self.add($('#b-pagesettings-tags-add-name').val());
							}
						});
					}
				});
			},
			add: function(tag) {
				$.boom.loader.show();

				$.post(
					'/cms/page/settings/tags/' + $.boom.page.config.id,
					{action: 'add', tag : tag}
					)
					.done(function(){
						$('#b-pagesettings-tags ul').append(
							'<li>' +
							'<a href="' + tag + '" title="Remove ' + tag +
							'" class="b-tags-remove"></a><span>' +
							tag + '</span></li>'
						);
					});

				$.boom.loader.hide();
				$('#b-pagesettings-tags-add-name').val('').hide();
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.links
		*/
		links: {
			/** @lends $.boom.page.settings.links */

			/**
			Menu label
			@property
			*/
			label: 'Links',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-links' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ) {

				var self = this;

				var treeConfig = $.extend( {}, $.boom.config.tree, {
					showRemove: true,
					onRemoveClick: function(event){

						event.preventDefault();

						var item = $( event.target ).closest( 'li' );

						self.remove( item );
					}
				});

				$.boom.dialog.open({
					url: '/cms/page/link/list/' + $.boom.page.config.id,
					event: event,
					// cache: true,
					title: 'Links',
					width: 440,
					treeConfig: treeConfig,
					buttons: {
						Add: function( event ){
							$.boom.dialog.open({
								url: '/cms/page/link/add/' + $.boom.page.config.id,
								event: event,
								title: 'Add Link',
								width: 300,
								// cache: true,
								buttons: {
									Okay: function(){

										self.add();

										$.boom.dialog.destroy( this );

									}
								}
							});
						},
						Close: function(){
							$.boom.dialog.destroy( this );
						}
					},
					open: function(){
						//  Each link in the list has a radio button whic toggles whether the link is a primary link
						// and a checkbox to toggle whether a secondary link redirects to the primary link.
						$('.b-links-primary, s-links-redirect').change(function(){
							link = $(this).closest('li');
							redirect = $(link).find('b-links-redirect').val();
							primary = $(link).find('b-links-primary').val();

							$.post('/cms/page/link/save/' + $.boom.page.config.id, {
								link_id :  link.attr('data-id'),
								redirect : redirect,
								primary : primary
							})
							.done(function(){
								$.boom.growl.show("Link saved.");
							});
						});
					}
				});
			},

			/** @function */
			add: function() {

				var form = $('#boom-form-addlink');
				var new_link = $( 'input[name=link]' ).val();

				$.boom.loader.show();

				$
					.post('/cms/page/link/add/' + $.boom.page.config.id, form.serialize())
					.done( function(response){

						$.boom.loader.hide();

						if (response == 'link in use')
						{
							// Link is being used on another page.
							// Ask if they want to move it.
							$.boom.dialog.confirm("Link in use", "The specified link is already in use on another page. Would you like to move it?", function(){
								$.boom.dialog.open({
									url: '/cms/page/link/move/' + $.boom.page.config.id + '?link=' + new_link,
									title: 'Move link',
									buttons: {
										Cancel: function(){
											$.boom.dialog.destroy(this);
										},
										Okay: function(){
											$.boom.loader.show();
											var move_dialog = this;

											$.post('/cms/page/link/move/' + $.boom.page.config.id + '?link=' + new_link)
												.done(function(response){
													$.boom.growl.show('Link added.');
													$( '#b-pagesettings-links .boom-tree' )
														.data( 'tree' )
														.add_item( '<li>' + new_link + '</li>' );

													$.boom.dialog.destroy(move_dialog);

													$.boom.loader.hide();
												});
										}
									}
								});
							});
						}
						else
						{
							// success
							$.boom.growl.show('Link added.');
							$( '#b-pagesettings-links .boom-tree' )
								.data( 'tree' )
								.add_item( '<li>' + new_link + '</li>' );
						}
					});
			},

			/** @function */
			remove: function( item ) {

				$.boom.dialog.confirm('Please confirm', 'Are you sure you want to remove this link? <br /><br /> This will delete the link from the database and cannot be undone!', function(){

					$.boom.loader.show();

					$
						.post(
							'/cms/page/link/delete/' + $.boom.page.config.id,
						 	{
								link: $.trim( item.text() )
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
		*/
		featureimage: {
			/** @lends $.boom.page.settings.featureimage */

			/**
			Menu label
			@property
			*/
			label: 'Feature image',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-featureimage' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){
				var url = '/cms/page/version/feature/' + $.boom.page.config.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.config.vid,
					event: event,
					title: 'Page feature image',
					width: 300,
					// cache: true,
					buttons: {
						Remove: function(){
							var dialog = $(this);
							$.boom.dialog.confirm(
								'Please confirm',
								"Are you sure you want to do delete this page's feature image?",
								function(){

									$.boom.page.settings.save(
										url,
										{feature_image_id : 0},
										"Page feature image removed."
									);

									dialog.dialog( 'destroy' );
								}
							);
						},
						Save: function(){

							$.boom.page.settings.save(
								url,
								$("#boom-form-pagesettings-featureimage").serialize(),
								"Page feature image saved."
							);

							$.boom.dialog.destroy( this );
						}
					},
					open: function(){

						$('.boom-featureimage-edit').click(function(){

							var asset = $.boom.items.asset;
							var tag = $.boom.items.tag;

							$.boom.dialog.open({
								url: '/cms/assets/manager/',
								iframe: false,
								width: 850,
								height: 500,
								title: 'Select an asset',
								destroy: function(){

									$.boom.dialog.destroy( this );
								},
								onLoad: function() {

									var asset_manager = $(this);

									$.boom.assets.init({
										items: {
											asset: asset,
											tag: tag
										},
										options: {
											sortby: 'audit_time',
											order: 'desc',
											edition: 'cms',
											type: 'assets',
											template: 'thumb',
											treeConfig: {
												onClick: function(event){
													tag.get(this.id);
												}
											}
										}
									});

									asset_manager
										.on( 'click', '.thumb a', function(event){
											var data = $(this).attr('href').split('/');

											$('#boom-featureimage-img').attr( 'src', '/asset/view/' + data[1] + '/250/80').show();
											$('#boom-featureimage-input').val( data[1] );
											$('#boom-featureimage-none').hide();
											$('#boom-featureimage-edit boom-button').hide();


											asset_manager.dialog( 'destroy' );

											return false;
										});

								}
							});
						});
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.template
		*/
		template: {
			/** @lends $.boom.page.settings.template */

			/**
			Menu label
			@property
			*/
			label: 'Template',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-template' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				var url = '/cms/page/version/template/' + $.boom.page.config.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.config.vid,
					event: event,
					title: 'Page template',
					width: 300,
					// cache: true,
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								url,
								$("#b-form-pageversion-template").serialize(),
								"Page template saved, reloading page."
							);

							$.boom.dialog.destroy( this );

							// Reload the page to show the template change.
							top.location.reload();
						}
					},
					open: function(){
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.embargo
		*/
		embargo: {
			/** @lends $.boom.page.settings.embargo */

			/**
			Menu label
			@property
			*/
			label: 'Embargo',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-embargo' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				var url = '/cms/page/version/embargo/' + $.boom.page.config.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.config.vid,
					event: event,
					title: 'Page template',
					width: 300,
					// cache: true,
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								url,
								$("#b-form-pageversion-template").serialize(),
								"Page template saved, reloading page."
							);

							$.boom.dialog.destroy( this );

							// Reload the page to show the template change.
							top.location.reload();
						}
					},
					open: function(){
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.visibility
		*/
		visibility: {
			/** @lends $.boom.page.settings.visibility */

			/**
			Menu label
			@property
			*/
			label: 'Visibility',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-visibility' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				var url = '/cms/page/settings/visibility/' + $.boom.page.config.id;

				$.boom.dialog.open({
					url: url + '?vid=' + $.boom.page.config.vid,
					event: event,
					// cache: true,
					title: 'Page visibility',
					width: 440,
					buttons: {
						Save: function(){

							self.save(
								url,
								$("#boom-form-pagesettings-visibility").serialize(),
								"Page visibility settings saved."
							);

							$.boom.dialog.destroy( this );
						}
					},
					open: function(){

						$('#b-page-toggle-visible:checkbox').unbind('change').change(function(){

							if (this.checked) {

								$('#page-visible-to, #page-visible-to-time').removeAttr('disabled');

								if ($('#page-visible-to').val().toLowerCase().trim() == 'forever') {

									$('#page-visible-to').val('');
								}

								$('#page-visible-to').focus();

							} else {

								$('#page-visible-to, #page-visible-to-time').attr('disabled', 'disabled');

								if (!$('#page-visible-to').val().trim().length) {

									$('#page-visible-to').val('forever');
								}

								$('#page-visible-to').blur();
							}
						});
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.history
		*/
		history: {
			/** @lends $.boom.page.settings.history */

			/**
			Menu label
			@property
			*/
			label: 'History',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-history' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){
				// TODO: fix this old page versions code.

				var url = '/cms/page/revisions/' + $.boom.page.config.id;
				$.boom.dialog.open({
					url:  url + '?vid=' + $.boom.page.config.vid,
					title: 'Page versions',
					width: 440,
					buttons: {
						Cancel : function(){
							$.boom.dialog.destroy( this );
						}
					},
					open: function(){

						var dialog = this;

						$('#b-page-revisions-list ul ul li').click(function(){
							$('#b-page-revisions-list ul ul li').removeClass('ui-state-active');
							$(this).addClass('ui-state-active');

							$('#b-page-revisions-selected').val($(this).attr('data-id'));

							$('#b-button-multiaction-edit').button('enable');
							$('#b-button-multiaction-publish').button('enable');
						});


						$('#b-button-multiaction-edit').click(function(){

							var vid = $('#b-page-revisions-selected').val();

							top.location = $.boom.util.url.addQueryStringParams({ version: vid }, true);
						});

						$('#b-button-multiaction-publish').bind('click',function(){
							var vid = $('#b-page-revisions-selected').val();

							$.boom.loader.show();

							$.get( '/cms/page/publish/' + $.boom.page.config.id, {vid: vid}, function(){
								$.boom.loader.hide();

								$.boom.dialog.destroy( dialog );
							});
						});
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.childsettings
		*/
		childsettings: {
			/** @lends $.boom.page.settings.childsettings */

			/**
			Menu label
			@property
			*/
			label: 'Child page settings',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-childsettings' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				$.boom.dialog.open({
					url: '/cms/page/settings/children/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid,
					event: event,
					// cache: true,
					title: 'Child page settings',
					width: 'auto',
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								'/cms/page/settings/children/' + $.boom.page.config.id,
								$("#boom-form-pagesettings-childsettings").serialize(),
								"Child page settings saved."
							);

							$.boom.dialog.destroy( this );
						}
					}
				});
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.adminsettings
		*/
		adminsettings: {
			/** @lends $.boom.page.settings.adminsettings */

			/**
			Menu label
			@property
			*/
			label: 'Admin settings',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-adminsettings' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				$.boom.dialog.open({
					url: '/cms/page/settings/admin/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid,
					event: event,
					// cache: true,
					title: 'Admin settings',
					width: 'auto',
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								'/cms/page/settings/admin/' + $.boom.page.config.id,
								$("#boom-form-pagesettings-adminsettings").serialize(),
								"Page admin settings saved."
							);

							$.boom.dialog.destroy( this );
						}
					}
				});
			}
		}

	}
});
