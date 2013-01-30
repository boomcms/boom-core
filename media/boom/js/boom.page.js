$.extend($.boom, {

	/**
	* @class
	* @name $.boom.page
	*/
	page : {

		/** @lends $.boom.page */

		/** @property */
		save_button: $('#b-page-save'),

		/** @property */
		cancel_button: $('#b-page-cancel'),



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

		/**
		Create a tree widget for selecting pages.
		@function
		@returns {Promise} promise which notifies a page ID when a page is selected.
		*/
		picker : function( $element ){

			var self = this;
			var complete = new $.Deferred();

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

					var link = {};
					var $node = $(this);
					var uri = $node.attr('href');
					var page_rid = $node.attr('rel');

					link.title = $node.text();
					link.page_id = page_rid;
					link.url = uri;

					item_selected( $node );

					complete.notify( link );
				},
				onToggle: function( event ){

					var list_ready = $.Deferred();
					var children = $.ajax( {
						type: 'POST',
						url: '/page/children.json',
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

			$element.tree('destroy').tree( parent_treeConfig );

			return complete;
		},

		/** @function */
		register : function(config){

			var self = this;

			top.$(function(){

				$.boom.loader.show();

				// Bind UI events to the iframe contents.
			 	// Adds functionality to boom-sortable, boom-tree, etc. elements in the iframe contents.
			 	$('body').contents().ui();
				$( self.document ).contents().ui();

				$.extend(self.config, config);

				self.editors = [];

				$.boom.log('Page registered for editing: ' + self.config.rid);

				$.getScript( $.boom.config.editor.path, function(){
					self.loadPageEditor()
						.done( function(){
							self.editor.init();
						});
				});

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

			var save_menu = {
				"Save" : function(){
					self.save();
				},
				"Save and preview" : function(){
					self.save();
					saveEditorState( 'preview' );
				},
				"Save and publish" : function(){
					self.save();
					$.post( '/cms/page/version/embargo/' + self.config.id );
				},
				"Save and request approval" : function(){
					self.save();
				}
			};

			this.cancel_button.on( 'click', function(){
				top.location.reload();
			})
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
					onLoad : function() {

						self.picker( $( this ).find( '.boom-tree' ) )
							.progress( function( page ){
								$( 'input[name=parent_id]' ).val( page.page_id );
							});

					},
					callback: function(){

						$.boom.loader.show('modal');

						console.log( $('#b-page-add-form') );

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
			$('#boom-page-save-menu')
				.splitbutton({
					items: save_menu,
					width: 'auto',
					menuPosition: 'right',
					split: false
				});
			$('#b-page-version-status').click(function(){

				$.boom.dialog.confirm(
					'Publish',
					'Make this version of the page live?',
					function(){

						$.boom.loader.show();

						$.post( '/cms/page/version/embargo/' + self.config.id )
						.done( function(response){

							$.boom.loader.hide();

						});
					}
				);

			});
			$( '#boom-page-editlive' ).on( 'click', function( event ){
				$.boom.dialog.confirm(
					'Edit live',
					'Stash changes and edit the live page?',
					function(){

						$.boom.log( 'stashing page edits' );

						$.post( '/cms/page/stash/' + $.boom.page.config.id )
						.done( function( response ){
							$.boom.history.refresh();
						});
					}
				);
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

			var data = pagedata || {};
			var self = this;

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
			});


			$.boom.hooks.register('page.saveError', 'sites', function(vars){

				if (window.console) {

					console.debug('POST data debug:', data);
				}

			}, true);


			requestdata = $.extend({
				data: JSON.stringify(data)
			}, requestdata);

			$.post( '/cms/page/version/content/' + this.config.id, requestdata )
			.done(
				function(response){
					$.boom.growl.show( "Page successfully saved." );
					$.boom.page.slot_edits = [];

					if (response == '')
					{
						$('#b-page-publish').show();
						$.boom.page.save_button.button( 'disable' ).attr( 'title', 'You have no unsaved changes' );
						$.boom.page.cancel_button.button( 'disable' ).attr( 'title', 'You have no unsaved changes' );
					}
					else
					{
						top.location = response;
					}
				})
			.fail( function(){
				$.boom.growl.show( "Unable to save page." );
			})
			.always( function(){
				$.boom.loader.hide();
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
		height: '90px',

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

			self.iframe.css( {
				'margin-top' : '-' + this.height,
				'height' : this.height
			});
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

			this.config.pageScripts = [ this.config.stylesheetURL, '/media/boom/css/boom.page.css' ];

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

				var $this = $( this );
				var chunk = this;

				if ( $this.is( 'div' ) && $this.text() == 'Default text.' ) {
					$this.html( '<p>Default text.</p>');
				}

				$this
				.unbind('click mouseenter mouseleave')
				.one( 'click', function(event){

					//event.target = this;
					var $this = $( this );

					var slot = {
						type : $this.attr( 'data-boom-chunk' ),
						name : $this.attr( 'data-boom-slot-name' ),
						rid : parseInt( $this.attr( 'data-boom-target' ), 10 ),
						template: $this.attr( 'data-boom-slot-template' ),
						page : $this.attr( 'data-boom-page' )
					};

					if (!slot.name) {
						slot.name = '';
					}
					if (!slot.rid) {
						slot.rid = 0;
					}
					if (!slot.type) {
						slot.type = 'text';
					}
					if (slot.type == 'text') {
						config.toolbar = $.boom.page.config.editorOptions[ slot.name ] || [];
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
			};

			self.elements.page_body.contents()
			.find('[data-boom-chunk]')
			.each(function(){

				var config = {};
				var $this = $( this );

				var slotName = $( this ).attr( 'data-boom-slot-name' );

				console.log( slotName );

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

					var head = self.elements.page_body
						.contents()
						.find('head');

					if (/js$/.test(this.url)) {
						head.append('<script type="text/javascript">'+data+'</script>');
					}

					 if (/css$/.test(this.url)) {
						head.append('<style type="text/css">'+data+'</style>');
					}

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
				type = 'chunk' + slot.type.ucfirst(),
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
				'urls',
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

				$.boom.dialog.open({
					url: '/cms/page/settings/navigation/' + $.boom.page.config.id + '?vid=' + $.boom.page.config.vid,
					event: event,
					// cache: true,
					title: 'Navigation',
					width: 570,
					onLoad : function() {

						$.boom.page.picker( $( this ).find( '.boom-tree' ) )
							.progress( function( pag ){
								$( 'input[name=parent_id]' ).val( page.page_id );
							});

					},
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
					url: '/cms/page/tags/list/' + $.boom.page.config.id,
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
						self.bind();
					}
				});
			},
			bind: function(){
				var self = this;

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
 					// Adding the active class changes the margin-left of the text so that it doesn't shift to the right when then delete url becomes visible.
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
						'/cms/page/tags/remove/' + $.boom.page.config.id,
						{tag : tag}
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

			},

			add: function(tag) {
				var self = this;

				$.boom.loader.show();

				$.post(
					'/cms/page/tags/add/' + $.boom.page.config.id,
					{tag : tag}
					)
					.done(function(){
						$('#b-pagesettings-tags')
						.parent()
						.load( '/cms/page/tags/list/' + $.boom.page.config.id, function(){

							$( this ).ui();
							self.bind();
						});
					});

				$.boom.loader.hide();
				$('#b-pagesettings-tags-add-name').val('').hide();
			}
		},

		/**
		* @class
		* @name $.boom.page.settings.urls
		*/
		urls: {
			/** @lends $.boom.page.settings.urls */

			/**
			Menu label
			@property
			*/
			label: 'URLs',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-urls' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ) {

				var self = this;

				$.boom.dialog.open({
					url: '/cms/page/urls/list/' + $.boom.page.config.id,
					event: event,
					// cache: true,
					title: 'URLs',
					width: 440,
					buttons: {
						Add: function( event ){
							$.boom.dialog.open({
								url: '/cms/page/urls/add/' + $.boom.page.config.id,
								event: event,
								title: 'Add URL',
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

					$.post('/cms/page/urls/save/' + $.boom.page.config.id, {
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
				var form = $('#boom-form-addurl');
				var new_url = $( 'input[name=url]' ).val();
				new_url = new_url.split( ' ' ).join( '-' );
				$( 'input[name=url]' ).val( new_url );

				$.boom.loader.show();

				$
					.post('/cms/page/urls/add/' + $.boom.page.config.id, form.serialize())
					.done( function(response){

						$.boom.loader.hide();

						if (response == 'url in use')
						{
							// URL is being used on another page.
							// Ask if they want to move it.
							$.boom.dialog.confirm("URL in use", "The specified url is already in use on another page. Would you like to move it?", function(){
								$.boom.dialog.open({
									url: '/cms/page/urls/move/' + $.boom.page.config.id + '?url=' + new_url,
									title: 'Move url',
									buttons: {
										Cancel: function(){
											$.boom.dialog.destroy(this);
										},
										Okay: function(){
											$.boom.loader.show();
											var move_dialog = this;

											$.post('/cms/page/urls/move/' + $.boom.page.config.id + '?url=' + new_url)
												.done(function(response){
													$.boom.growl.show('URL added.');
													$( '#b-pagesettings-urls .boom-tree' )
														.data( 'tree' )
														.add_item( '<li>' + new_url + '</li>' );

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
							$.boom.growl.show('Url added.');
							$( '#b-pagesettings-urls' )
								.parent()
								.load( '/cms/page/urls/list/' + $.boom.page.config.id, function(){
									$(this).ui();
									self.bind();
								});
						}
					});
			},

			/** @function */
			remove: function( item ) {

				$.boom.dialog.confirm('Please confirm', 'Are you sure you want to remove this URL? <br /><br /> This will delete the URL from the database and cannot be undone!', function(){

					$.boom.loader.show();

					$
						.post(
							'/cms/page/urls/delete/' + $.boom.page.config.id,
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

									$.boom.dialog.destroy( dialog );
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

							$.boom.assets
								.picker({
									asset_rid : $('#boom-featureimage-input').val()
								})
								.done( function( rid ){

									$('#boom-featureimage-img').attr( 'src', '/asset/view/' + rid + '/250/80').show();
									$('#boom-featureimage-input').val( rid );
									$('#boom-featureimage-none').hide();
									$('#boom-featureimage-edit boom-button').hide();

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
					url: url,
					event: event,
					title: 'Page embargo',
					width: 300,
					// cache: true,
					buttons: {
						Save: function(){

							$.boom.page.settings.save(
								url,
								$("#b-form-pageversion-embargo").serialize(),
								"Page embargo saved."
							);

							$.boom.dialog.destroy( this );
						}
					},
					open: function(){
						$( '#page-visible' ).on( 'change', function(){
							switch( $( this ).val() ) {
								case '1':
									$( '#page-embargo' ).removeAttr( 'disabled' );
								break;
								case '0':
									$( '#page-embargo' ).attr( 'disabled', 'disabled' );
								break;
							}
						});
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

							$.boom.page.settings.save(
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
