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
				page : this.page,
				publishable : this.options.publishable
			})
			.data('boomPageToolbar');

		$.boom.log('Page registered for editing: ' + self.options.page_id);

		if (this.options.editable) {
			$('body').editor();
			this.createChunks();
		}
	},

	createChunks : function() {
		var self = this;

		this.document.contents()
			.find('[data-boom-chunk]')
			.each(function(){
				var $this = $(this);

				var type = $this.data('boom-chunk');
				var widgetName = 'chunk' + type.ucfirst();

				$this[widgetName]({
					type : type,
					name : $this.data('boom-slot-name'),
					template : $this.data('boom-slot-template'),
					id : $this.data('boom-target')? parseInt($this.data('boom-target'), 10) : 0,
					page : $this.data('boom-page'),
					currentPage : self.page,
				});
			})
			.end()
			.find('#b-page-title')
			.pageTitle({
				currentPage : self.page
			});
	}
});