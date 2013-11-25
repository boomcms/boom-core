$.widget( 'boom.pageEditor', {
	elements : {},

	options : {
		editable : false
	},

	_create : function() {
		var self = this;

		this.page = new boomPage(this.options.page_id);
		$.boom.page = this.page;

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

		setTimeout(function() {self.load()}, 0);
		this.options.editable && this.createChunks();

		return this;
	},

	/** @function */
	load : function(){
		$.boom.loader.show();

		this.loadScripts(this.config.pageScripts)
			.done(function(){
				$.boom.loader.hide();

				$.boom.log('Scripts loaded into iFrame');
			});
	},

	/** @function */
	loadPageEditor : function(){
		$.boom.loader.hide();

		return $('body').editor().editor('load');
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
					template : $this.attr('data-boom-slot-template'),
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
		var	promise = new $.Deferred(),
			$head = this.elements.page_body.contents().find('head');

		for (var i in scripts) {
			var script = scripts[i];
			
			$.ajax({
				url : script,
				cache : true
			})
			.done(function(response) {
				if (/css$/.test(script)) {
					$head.append($("<style/>", {type: "text/css", async : ''}).text(response));
				}
				else if (/js$/.test(script)) {
					$head.append($("<script/>", {type: "text/javascript", async : ''}).text(response));
				}
			});
		};

		return promise.resolve();
	}
});