$.widget( 'boom.pageEditor', {
	elements : {},

	openDialogs : 0,

	options : {
		editable : false
	},

	_create: function() {
		var self = this;

		this.page = new BoomCMS.Page({id: this.options.page_id});
		window.BoomCMS.page = this.page;

		this.document = $(top.document);

		this.page.toolbar = this.toolbar = this.document
			.find('#b-page-topbar')
			.pageToolbar({ // This should probably be called editorIframe as we're calling this on the iframe. Then we need another widget which is specifically for the toolbar.
				page : this.page,
				publishable : this.options.publishable
			})
			.data('boom-pageToolbar');

		this.watchForDialogs();

		if (this.options.editable) {
			this.createChunks();
		}
	},

	createChunks: function() {
		var self = this;

		this.document.contents()
			.find('[data-boom-chunk]')
			.each(function() {
				var $this = $(this),
					type = $this.data('boom-chunk'),
					widgetName = 'chunk' + type.ucfirst();

				$this[widgetName]({
					type : type,
					name : $this.data('boom-slot-name'),
					template : $this.data('boom-slot-template'),
					id : $this.data('boom-target')? parseInt($this.data('boom-target'), 10) : 0,
					page : $this.data('boom-page'),
					currentPage : self.page,
					chunkId : $this.data('boom-chunk-id')
				});
			})
			.end()
			.find('#b-page-title')
			.pageTitle({
				currentPage : self.page
			})
			.end()
			.find('.b-chunk-pagetags')
			.each(function() {
				$(this).chunkPageTags({
					currentPage : self.page
				});
			})
			.end()
			.find('.b-chunk-pagevisibility')
			.each(function() {
				$(this).chunkPageVisibility({
					currentPage : self.page
				});
			})
			.end()
			.find('.b-page-featureimage')
			.each(function() {
				var $this = $(this);

				$this
					.addClass(BoomCMS.editableClass)
					.on('click', function(e) {
						e.preventDefault();

						window.BoomCMS.page.toolbar.showSettingsAndCloseOnSave('feature');
					});
			});
	},

	watchForDialogs: function() {
		var editor = this,
			toolbar = this.page.toolbar;

		$(top.window)
			.on('boom:dialog:open', function() {
				editor.openDialogs++;

				if (editor.openDialogs === 1) {
					toolbar.maximise();
					toolbar.hide();
					editor.document.find('body').css('overflow', 'hidden');
				}
			})
			.on('boom:dialog:close', function() {
				editor.openDialogs--;

				if (editor.openDialogs === 0) {
					toolbar.minimise();
					toolbar.show();
					editor.document.find('body').css('overflow', 'auto');
				}
			});
	}
});