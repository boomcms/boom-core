/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', {
	_bindButtonEvents : function() {
		var self = this;

		this.element.contents()
			.on('click', '#b-page-delete', function() {
				self.options.page.delete();
			})
			.on('click', '#b-page-addpage', function() {
				self.options.page.add();
			})
			.on('click', '#boom-page-editlive', function() {
				self.options.page.stash();
			})
			.on('click', '#b-page-readability', function() {
				$.boom.dialog.open({
					url: '/media/boom/html/readability.html'
				});
			})
			.on('click', '#b-page-visibility', function() {
				self.options.page.visibility();
			})
			.on('click', '.b-button-preview', function() {
				$.boom.editor.state($(this).attr('data-preview'));
			})
			.find('#b-page-settings-menu')
				.splitbutton({
					items: self._buildSettingsMenu(),
					width: 'auto',
					menuPosition: 'right',
					split: false
				})
			.end()
			.find('#b-page-version-menu')
				.splitbutton({
					items: self._buildVersionMenu(),
					width: 'auto',
					menuPosition: 'right',
					split: false
				})
			.end();
	},

	_buildSettingsMenu : function() {
		var menu_items = {};
		var self = this;

		this.element.contents().find('button.b-page-settings').each(function(index, value) {
			var $this = $(this);
			var setting = $this.attr('data-b-page-setting');
			var item = [];

			item[$this.text()] =  function() {
				self.options.page[setting]();
			}
			menu_items = $.extend(menu_items, item);
		});

		return menu_items;
	},

	_buildVersionMenu : function() {
		var self = this;

		return {
			'Feature image' : function() {
				self.options.page.featureimage();
			},
			'Template' : function() {
				self.options.page.template();
			}
		};
	},

	_create : function() {
		$.boom.log( 'init CMS toolbar' );

		this.status = $('#b-page-version-status')
			.pageStatus({
				page : this.options.page
			})
			.data('boomPageStatus');

		this._bindButtonEvents();
	},

	/**
	* extend the toolbar to cover the entire window
	* @function
	*/
	maximise : function() {
		$.boom.log('maximise iframe');

		this.element
			.css('width', '120%');
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise : function() {
		$.boom.log('minimise iframe');

		this.element.css && this.element.css('width', '60px');
	},

	/**
	@function
	*/
	hide : function() {
		this.element.hide();
	},

	/**
	@function
	*/
	show : function() {
		this.element.show();
	}
});