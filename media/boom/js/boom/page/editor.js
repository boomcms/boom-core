$.widget( 'boom.pageEditor', {
	elements : {},

	options : {
		editable : false
	},

	_create : function() {
		var self = this;

		this.page = new boomPage(this.options.page_id);
		$.boom.page = this.page;

		$.boom.util.cacheImages($.boom.config.cachePageImages);

		this.document = $(top.document);

		this.page.toolbar = this.toolbar = this.document
			.find('#b-page-topbar')
			.pageToolbar({
				page : this.page
			})
			.data('boomPageToolbar');

		$.boom.log('Page init');

		top.$(function(){

			$.boom.loader.show();

			self.editors = [];

			$.boom.log('Page registered for editing: ' + self.options.page_id);

			self.loadPageEditor()
				.done( function(){
					self.init();
				});
		});
	},

	/** @function */
	init : function(){

		var self = this;

		this.config = $.boom.config.editor;

		this.config.pageScripts = [ this.config.stylesheetURL, '/media/boom/css/boom.page.css' ];

		this.elements.page_body = this.document;

		this.load()
			.done( function(){
				if (self.options.editable) {
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

	/** @function */
	loadPageEditor : function(){

		$.boom.loader.hide();

		var self = this;

		return $( 'body' ).editor().editor( 'load' );
	},

	createChunks : function() {
		var self = this;

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
					page : $this.attr( 'data-boom-page' ),
					currentPage : self.page,
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
});