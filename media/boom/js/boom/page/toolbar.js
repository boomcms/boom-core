/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', {
	buttons : {},

	_bindButtonEvents : function() {
		var self = this;

		this.element.contents()
			.on('click', '#b-page-delete', function() {
				self.options.page.remove()
					.done(function(response) {
						new boomNotification("Page deleted, redirecting to parent.");
						top.location = response;
					});
			})
			.on('click', '#b-page-addpage', function() {
				self.options.page.add()
					.done(function(response) {
						top.location = response.url;
					})
					.fail(function(response) {
						new boomAlert(response);
					});
			})
			.on('click', '#boom-page-editlive', function() {
				self.options.page.stash();
			})
			.on('click', '#b-page-readability', function() {
				new boomDialog({
					url : '/media/boom/html/help/readability.html',
					width : 600,
					title: 'Readability measure',
					cancelButton : false
				});
			})
			.on('click', '.b-page-visibility', function() {
				self.options.page.visibility()
					.done(function(response) {
						if (response == 1) {
							self.buttons.visible.show();
							self.buttons.invisible.hide();
						} else {
							self.buttons.visible.hide();
							self.buttons.invisible.show();
						}

						self._toggle_view_live_button();
					});
			})
			.on('click', '.b-button-preview', function() {
				$.boom.editor.state($(this).attr('data-preview'));
			})
			.on('click', '#b-page-template', function() {
				self.options.page.template();
			})
			.find('#b-page-settings-menu')
			.splitbutton({
				items: self._buildSettingsMenu(),
				width: 'auto',
				menuPosition: 'right',
				split: false
			});

		this.buttonBar = this.element.contents().find('#b-topbar');
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

	_create : function() {
		$.boom.log( 'init CMS toolbar' );

		this.findButtons();

		this._toggle_view_live_button();
		this.status = $('#b-page-version-status')
			.pageStatus({
				page : this.options.page,
				publishable : this.options.publishable
			})
			.data('boomPageStatus');

		this._bindButtonEvents();
	},

	findButtons : function() {
		this.buttons = {
			visible : this.element.contents().find('#b-page-visible'),
			invisible : this.element.contents().find('#b-page-invisible'),
			viewLive : this.element.contents().find('#boom-page-viewlive')
		};
	},

	/**
	* extend the toolbar to cover the entire window
	* @function
	*/
	maximise : function() {
		$.boom.log('maximise iframe');

		this.element.css({
			width : '100%',
			'z-index' : 100002
		});
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise : function() {
		$.boom.log('minimise iframe');

		this.element.css({
			width : 'auto',
			'z-index' : 10000
		});
	},

	/**
	@function
	*/
	hide : function() {
		this.buttonBar.css('z-index', 1);
	},

	/**
	@function
	*/
	show : function() {
		this.buttonBar.css('z-index', 10000);
	},

	_toggle_view_live_button : function() {
		if (this.buttons.visible.css('display') == 'none') {
			this.buttons.viewLive
				.attr('title', 'You cannot view a live version of this page as it is currently hidden from the live site')
				.button('disable');
		} else {
			this.buttons.viewLive
				.attr('title', 'View the page as it appears on the live site')
				.button('enable');
		}
	}
});
