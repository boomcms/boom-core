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

	boom : $.boom,

	/**
	default config
	@property options
	*/
	options : {},

	/**
	 @property status_button
	 */
	status: $('#b-page-version-status').pageStatus({}).data('boomPageStatus'),

	_init : function() {

	},

	_destroy : function() {

	},

	/** @function */
	_create : function() {
		$.boom.page = this;

		$.boom.util.cacheImages($.boom.config.cachePageImages);

		this.document = $(top.document);

		this.toolbar = this.document
			.find('#b-page-topbar')
			.pageToolbar({
				page : this
			})
			.data('boomPageToolbar');

		this.document.find('body')
			.contents()
			.ui();

		this.bind();

		$.boom.log('Page init');
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

		return this;
	},

	/** @function */
	loadPageEditor : function(){

		$.boom.loader.hide();

		var self = this;

		return $( 'body' ).editor().editor( 'load' );
	},

	add : function() {
		var self = this;

		self.boom.loader.show();

		$.post('/cms/page/add/' + self.options.id, {csrf : $('#b-csrf').val()}, function(response){
			if (new RegExp('^' + "\/").test( response)) {
				top.location = response;
			} else {
				self.boom.dialog.alert('Error', response);
				self.boom.loader.hide();
			}
		});
	},

	delete : function() {
		var self = this;

		self.boom.dialog.open({
			width: 350,
			url: '/cms/page/delete/' + self.options.id,
			title: 'Please confirm',
			callback: function(){

				$.post('/cms/page/delete/' + self.options.id, $(this).find('form').serialize(), function(response){
					self.boom.growl.show("Page deleted, redirecting to parent.");
					top.location = response;
				});
			}
		});
	},

	stash : function() {
		var self = this;

		self.boom.dialog.confirm(
			'Edit live',
			'Discard changes and edit the live page?'
		)
		.done(function(){
			self.boom.log('stashing page edits');

			$.post('/cms/page/stash/' + self.options.id)
				.done(function(response) {
					self.boom.history.refresh();
				});
		});
	}
} );

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

			function slotBind( config ){

				config = config || {};

				var $this = $( this );
				var chunk = this;

				if ( $this.is( 'div' ) && $this.text() == 'Default text.' ) {
					$this.html( '<p>Default text.</p>');
				}

				$this
				.attr( 'tabindex', '0' )
				.unbind('click')
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
				});
			};

			self.elements.page_body.contents()
			.find('[data-boom-chunk]')
			.each(function(){

				var config = {};
				var $this = $( this );

				var slotName = $( this ).attr( 'data-boom-slot-name' );


				$( this ).addClass( 'b-editable' );

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
			.end();

			self.elements.page_body.contents().find('#b-page-title').pageTitle({});

			$.boom.log('Page editor bind');
		},

		/** @function */
		unbind : function(){
			if ( ! this.elements.page_body) {
				return;
			}

			var $contents = this.elements.page_body.contents();

			$contents.find('.chunk-slot').each(function() {
				$(this)
					.unbind('click')
					.removeClass('b-editable');
			});

			$contents.find('#b-page-title').pageTitle('destroy');

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

			$(elem).removeClass('b-editable');

			$.boom.log('Chunk slot edit');

			$.boom.page.editor.unbind();

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
	}
});