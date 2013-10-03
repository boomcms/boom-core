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

		$.boom.log('Page init');
	},

	/** @function */
	register : function( options ){

		var self = this;

		top.$(function(){

			$.boom.loader.show();

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
					if ($.boom.page.options.writable) {
						self.createChunks();
					}
				});

			return this;
		},

		/** @function */
		load : function(){
			var promise = new $.Deferred();

			$.boom.loader.show();

			this.loadScripts(this.config.pageScripts)
				.done(function(){

					$.boom.loader.hide();

					$.boom.log('Scripts loaded into iFrame');

					promise.resolve();
				});

			return promise;
		},

		createChunks : function() {
			 this.elements.page_body.contents()
				.find('[data-boom-chunk]')
				.each(function(){
					var $this = $(this);

					var type = $this.attr('data-boom-chunk');
					var widgetName = 'chunk' + type.ucfirst();

					$this[widgetName]({
						type : type,
						name : $this.attr('data-boom-slot-name'),
						template : $this.attr('data-boom-template'),
						id : $this.attr('data-boom-target')? parseInt($this.attr('data-boom-target'), 10) : 0,
						page : $this.attr( 'data-boom-page' )
					});
				})
				.end()
				.find('#b-page-title')
				.pageTitle({});
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