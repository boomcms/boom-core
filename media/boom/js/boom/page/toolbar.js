/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', $.boom.page, {
	options : {
		height : '90px'
	},

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
				self.boom.dialog.open({
					url: '/media/boom/html/readability.html'
				});
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
			.end()
			.on('click', '#b-page-visibility', function() {
				self.options.page.settings.visibility.edit();
			});
	},

	_buildSettingsMenu : function() {
		var menu_items = {};
		var self = this;

		this.element.contents().find('button.b-page-settings').each(function(index, value) {
			var $this = $(this);
			var setting = $this.attr('data-b-page-setting');
			var item = [];

			item[$this.text()] =  function() {
				self.options.page.settings[setting].edit();
			}
			menu_items = $.extend(menu_items, item);
		});

		return menu_items;
	},

	_buildVersionMenu : function() {
		var self = this;

		return {
			'Feature image' : function() {
				self.options.page.settings.featureimage.edit();
			},
			'Template' : function() {
				self.options.page.settings.template.edit();
			}
		};
	},

	_create : function() {
		this.boom.log( 'init CMS toolbar' );

		this.document
			.find('body')
			.css({'margin-top' : this.options.height});

		this.element
			.css({'margin-top' : '-' + this.options.height})
			.animate({'height' : this.options.height});

		this._bindButtonEvents();
	},

	/**
	* extend the toolbar to cover the entire window
	* @function
	*/
	maximise : function() {
		this.boom.log('maximise iframe');

		this.element
			.show()
			.css('height', '120%');
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise : function() {
		this.boom.log('minimise iframe');

		this.element.css && this.element.css('height', this.options.height);
	},

	/**
	@function
	*/
	hide : function() {
		this.element
			.slideUp()
			.hide();

		this.document
			.find('body')
			.css('margin-top', 0);
	},

	/**
	@function
	*/
	show : function() {
		this.element
			.show()
			.slideDown();

		this.document
			.find('body')
			.css('margin-top', this.options.height);
	}
});