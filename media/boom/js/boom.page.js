/**
@fileOverview Page editor, toolbar and page settings.
*/
/**
* Page editor UI.
* @class
* @name $.boom.page
*/
$.widget( 'boom.page', {

	/** @lends $.boom.page */

	/**
	default config
	@property options
	*/
	options : {},

	/**
	@property save_button
	*/
	save_button: $('#b-page-save'),

	/**
	@property cancel_button
	*/
	cancel_button: $('#b-page-cancel'),

	_init : function() {

	},

	_destroy : function() {

	},

	/** @function */
	_create : function(config) {

		var self = this;

		$.boom.page = self;

		this.slot_edits = [];

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
					'You have unsaved changes to this page. Press OK to save these and continue.'
				)
				.done( function(){
					self.save();
				});
			}
		};

	},

	/** @function */
	register : function( options ){

		var self = this;

		top.$(function(){

			$.boom.loader.show();

			// Bind UI events to the editable page.
		 	// Adds functionality to boom-sortable, boom-tree, etc. elements in the page.
			$( self.document ).contents().ui();

			$.extend(self.options, options);

			self.editors = [];

			$.boom.log('Page registered for editing: ' + self.options.rid);

			self.loadPageEditor()
				.done( function(){
					self.editor.init();
				});
		});
	},

	/** @function */
	build : function(){

		var self = this;

		this.document = $( top.document );

		this.toolbar.init();

		$('body').contents().ui();

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
				self.save(null, {'publish' : 1});
			},
		};

		this.cancel_button.on( 'click', function(){
			top.location.reload();
		});
		$('#b-page-delete').click(function(){

			$.boom.dialog.open({
				width: 350,
				url: '/cms/page/delete/' + self.options.id,
				title: 'Please confirm',
				callback: function(){

					$.post('/cms/page/delete/' + self.options.id, $('#b-page-delete-form').serialize(), function(response){
						$.boom.growl.show("Page deleted, redirecting to parent.");
						top.location = response;
					});
				}
			});
		});
		$('#b-page-addpage').click(function(){
			$.boom.loader.show();

			$.post('/cms/page/add/' + self.options.id, function(response){
				$.boom.loader.hide();

				if ( new RegExp('^' + "\/").test( response ) ) {

					top.location = response;
				} else {

					$.boom.dialog.alert('Error', response);
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
				'Make this version of the page live?'
			)
			.done( function(){

				$.boom.loader.show();

				$.post( '/cms/page/version/embargo/' + self.options.id )
				.done( function(response){
					$('#b-page-version-status span').text(response);
					$.boom.loader.hide();

				});
			});

		});
		$( '#boom-page-editlive' ).on( 'click', function( event ){
			$.boom.dialog.confirm(
				'Edit live',
				'Discard changes and edit the live page?'
			)
			.done( function(){

				$.boom.log( 'stashing page edits' );

				$.post( '/cms/page/stash/' + $.boom.page.options.id )
				.done( function( response ){
					$.boom.history.refresh();
				});
			});
		});
		$('#b-page-readability').on('click', function(event) {
			$.boom.dialog.open({
				url: '/media/boom/html/readability.html'
			});
		});

		self.settings.init();

		self.settings.bind();

		return this;
	},

	/** @function */
	loadPageEditor : function(){

		$.boom.loader.hide();

		var self = this;

		return $( 'body' ).editor().editor( 'load' );

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
			$.boom.page.options,
			title =
				this.document.contents().find('#b-page-title').length ?
				this.document.contents().find('#b-page-title').html().text() :
				$('input[name=alttitle]').val();

		data = $.extend(data, {
			title: title || 'Untitled',
			slots: {}
		});

		if (!data.vid) {
			data.vid = this.options.vid;
		}

		$( $.boom.page.slot_edits ).each(function(){

			if ( this.id == 'b-page-title' ) return;

			var
				slot = this.slot;

			// Don't submit data for chunks which have been inherited from another page.
			// slotobj.page will be 0 when the slot has been edited.
			if (slot.page == self.options.id || slot.page == 0)
			{
				if (!data.slots[slot.type]) {
					data.slots[slot.type] = {};
				}

				if (slot.type != 'text' || this.data != 'Default text.') {
					data.slots[slot.type][slot.name] = this.data;
				}
			}
		});



		requestdata = $.extend({
			data: JSON.stringify(data)
		}, requestdata);

		$.post( '/cms/page/version/content/' + this.options.id, requestdata )
		.done(
			function(response){
				$.boom.growl.show( "Page successfully saved." );
				$.boom.page.slot_edits = [];

				if (response.substring(0, 9) == 'Location:') {
					top.location = response.replace('Location:', '');
				} else {
					$('#b-page-publish').show();
					$.boom.page.save_button.button( 'disable' ).attr( 'title', 'You have no unsaved changes' );
					$.boom.page.cancel_button.button( 'disable' ).attr( 'title', 'You have no unsaved changes' );

					$('#b-page-version-status span').text(response);
				}
			})
		.fail( function(response){
			var message;

			try {
				error = JSON.parse(response.responseText);
			} catch (e) {
				message = "Unable to save page.";
			}

			if (message == null) {
				message = (error.message)? error.message : 'Unable to save page.';
			}

			$.boom.growl.show( message );
		})
		.always( function(){
			$.boom.loader.hide();
		});
	}
} );

$.widget( 'boom.page', $.boom.page, {

	/**
	* Common functionality for the embedded CMS toolbar
	* @class
	* @name $.boom.page.toolbar
	*/
	toolbar: {

		/**
		* Editable page DOM
		* @property page_dom
		* @type (Object)
		*/
		page_dom: {},

		/**
		* Editor iframe
		* @property iframe
		* @type (Object)
		*/
		iframe: {},

		/**
		@property height
		*/
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

			if ( $( '#boom-topbar' ).length ) {
				self.page_dom
					.find( 'body' )
					.css( {'margin-top' : this.height} );

				self.iframe
				.css( {
					'margin-top' : '-' + this.height
				})
				.animate( {
					'height' : this.height
				});
			} else {
				self.iframe.animate( {
					'height' : '30px'
				});
			}

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

$.widget( 'boom.page', $.boom.page, {

	/**
	* @class
	* @name $.boom.page.editor
	*/
	editor :

		/** @lends $.boom.page.editor */
		{
		elements : {},

		/** @function */
		init : function(){

			var self = this;

			this.config = $.boom.config.editor;

			this.config.pageScripts = [ this.config.stylesheetURL, '/media/boom/css/boom.page.css' ];

			this.elements.page_body = $.boom.page.document;

			this.load()
				.done( function(){
					if ( $.boom.page.options.writable ) self.bind();
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

					var internal_link = /#|javascript:/;

					return ( target && target.nodeName == 'A' && !internal_link.test( target.href ) );
				}

				var target = isAnchor(event.target) ? event.target : $( event.target ).parents('a').get(0);

				if ( isAnchor(target) ) {

					event.preventDefault();

					if ( $.boom.page.slot_edits.length ){

						$.boom.dialog.confirm(
							'Save changes',
							'You have unsaved changes to this page. Save your changes, or discard them and continue.'
						)
						.done( function(){
							$.boom.page.save();
						})
						.fail( function(){
							$.boom.page.slot_edits = [];
							top.location = target.href;
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
				.attr( 'tabindex', '0' )
				.unbind('click mouseenter mouseleave')
				.on( 'keydown', function( event ){
					switch( event.which ) {
						case 13:
						 $this.click();
						break;
					}
				})
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
						config.toolbar = $.boom.page.options.editorOptions[ slot.name ] || [];
					}

					$.boom.page.slots.edit(event, this, slot, config);

					return false;
				})
				.bind('mouseenter focus', function(){

					$.boom.page.slots.bindMouseEnter.call(this, self.elements.page_body);
				})
				.bind('mouseleave blur', function(){

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

				$.get( this )
				.done( function( response ){

					var head = self.elements.page_body
						.contents()
						.find('head');

					if (/js$/.test(this.url)) {
						$( '<script></script>', {
							type : "text/javascript"
						} )
						.text( response )
						.appendTo( head );
					}

					 if (/css$/.test(this.url)) {
						$( '<style></style>', {
							type: "text/css"
						})
						.text( response )
						.appendTo( head );
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

$.widget( 'boom.page', $.boom.page, {

	/**
	* @class
	* @name $.boom.page.slots
	*/
	slots :
		/** @lends $.boom.page.slots */
		{
		/** @function */
		edit : function(event, elem, slot, slotconfig){
			event.preventDefault();
			event.stopPropagation();

			this.hideEditOverlay();

			$.boom.log('Chunk slot edit');

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

$.widget( 'boom.page', $.boom.page, {

	/**
	* @class
	* @name $.boom.page.settings
	* @static
	*/
	settings:
		/** @lends $.boom.page.settings */
		{
		/** @function */
		init: function() {
			// some initialisation code should probably go here.

		},

		/** @function */
		bind: function() {

			var self = this;
			var i = 0;

			var settings = $('button.b-page-settings')
				.map(function() {
					return $(this).attr('data-b-page-setting');
				})
				.get();

			$('#boom-page-settings-menu').splitbutton({
				items: self._build_menu( settings ),
				itemclick : function(event){

					if (!$.boom.page.options.id) {

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

					if (!$.boom.page.options.id) {

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
				if (message)
				{
					$.boom.growl.show( message );
				}
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

				if ($('#boom-page-' + class_name).length) {
					var menu_item = self[ class_name ].label;
					var menu_handler = self[ class_name ].menu_handler;

					self.register( class_name );


					menu_items[ menu_item ] = menu_handler;

					$.boom.log( 'initialising ' + class_name );
				}
			};

			return menu_items;
		},

		/**
		* @class
		* @name $.boom.page.settings.navigation
		* @static
		*/
		navigation:
			/** @lends $.boom.page.settings.navigation */
			{

			/**
			Menu label
			@property label
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
					callback: function(){

						$.boom.page.settings.save(
							'/cms/page/settings/navigation/' + $.boom.page.options.id,
							$("#boom-form-pagesettings-navigation").serialize(),
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
			/**
			Menu label
			@property label
			*/
			label: 'Search',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-search' ).trigger('boomclick');
			},

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
							$("#boom-form-pagesettings-search").serialize(),
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
			/**
			Menu label
			@property label
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
			/**
			Menu label
			@property label
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
					url: '/cms/page/urls/list/' + $.boom.page.options.id,
					// cache: true,
					title: 'URLs',
					width: 440,
					buttons: [
						{
							text: 'Add URL',
							title: 'Add URL',
							id: 'boom-page-settings-url-add',
							icons: { primary : 'ui-icon-boom-add' },
							click: function( event ){
								$.boom.dialog.open({
									url: '/cms/page/urls/add/' + $.boom.page.options.id,
									event: event,
									title: 'Add URL',
									width: 300,
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
				var form = $('#boom-form-addurl');
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
			/**
			Menu label
			@property label
			*/
			label: 'Feature image',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-featureimage' ).trigger('boomclick');
			},

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
									$('#b-page-version-status span').text(response);
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
			/**
			Menu label
			@property label
			*/
			label: 'Template',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-template' ).trigger('boomclick');
			},

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
		* @name $.boom.page.settings.embargo
		* @static
		*/
		embargo:
			/** @lends $.boom.page.settings.embargo */
			{
			/**
			Menu label
			@property label
			*/
			label: 'Embargo',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-embargo' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				var url = '/cms/page/version/embargo/' + $.boom.page.options.id;

				$.boom.dialog.open({
					url: url,
					title: 'Page embargo',
					width: 300,
					// cache: true,
					callback: function(){

						$.boom.page.settings.save(
							url,
							$("#b-form-pageversion-embargo").serialize(),
							"Page embargo saved."
						)
						.done(function(response) {
							$('#b-page-version-status span').text(response);
						});
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
		* @static
		*/
		visibility:
			/** @lends $.boom.page.settings.visibility */
			{
			/**
			Menu label
			@property label
			*/
			label: 'Visibility',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-visibility' ).trigger('boomclick');
			},

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
							$("#boom-form-pagesettings-visibility").serialize(),
							"Page visibility settings saved."
						)
						.done(function(response) {
							var icons = {
								1 : 'ui-icon-boom-visible',
								0 : 'ui-icon-boom-invisible'
							};

							$('#boom-page-visibility')
								.attr('data-icon', icons[response])
								.find('span.ui-icon')
								.removeClass(icons[0])
								.removeClass(icons[1])
								.addClass(icons[response]);
						});
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

						$('#page-visible').on('change', function() {
							switch( $( this ).val() ) {
								case '1':
									$( '#page-visible-from' ).removeAttr( 'disabled' );
								break;
								case '0':
									$( '#page-visible-from' ).attr( 'disabled', 'disabled' );
									$( '#page-visible-to' ).attr( 'disabled', 'disabled' );
								break;
							}
						});

						if ($('#page-visible').val() == '0') {
							$( '#page-visible-from' ).attr( 'disabled', 'disabled' );
							$( '#page-visible-to' ).attr( 'disabled', 'disabled' );
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
			/**
			Menu label
			@property label
			*/
			label: 'Child page settings',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-childsettings' ).trigger('boomclick');
			},

			/** @function */
			edit: function( event ){

				$.boom.dialog.open({
					url: '/cms/page/settings/children/' + $.boom.page.options.id + '?vid=' + $.boom.page.options.vid,
					// cache: true,
					title: 'Child page settings',
					width: 'auto',
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
										$("#boom-form-pagesettings-childsettings").serialize()
									).done(function() {
										$.boom.page.settings.save(
											url,
											{sequences: sequences},
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
							$("#boom-form-pagesettings-childsettings").serialize(),
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
			/**
			Menu label
			@property label
			*/
			label: 'Admin settings',

			/** @function */
			menu_handler: function() {
				$( '#boom-page-adminsettings' ).trigger('boomclick');
			},

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
							$("#boom-form-pagesettings-adminsettings").serialize(),
							"Page admin settings saved."
						);

					}
				});
			}
		}

	}
});
/**
Base class for the text editor
@class
@name $.boom.editor
*/
$.widget( 'boom.editor',
	/** @lends $.boom.editor */
	{
	_create : function() {

	},

	_init : function() {

	},

	_destroy : function() {

	},

	/**
	Load the wysiwyg javascript files
	@returns {Deferred} Promise which resolves whenm the editor has loaded.
	*/
	load : function() {

		$.boom.log( 'editor loading ');
		var loaded = new $.Deferred();

		return loaded;
	},

	/**
	Apply changes and exit
	*/
	apply : function() {

	},

	/**
	Cancel changes and exit
	*/
	cancel : function() {

	},

	/**
	Edit a slot
	@param {Object} element DOM element to edit
	*/
	edit : function( element ) {

	},

	/**
	Remove the  wysiwyg instance from the DOM
	*/
	remove : function() {

	}
});
