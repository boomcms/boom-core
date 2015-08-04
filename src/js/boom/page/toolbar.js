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
				self.options.page.delete()
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
			.on('click', '.b-page-visibility', function() {
				self.$settings
					.pageSettings('show', 'visibility')
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
					
				self.openPageSettings();
			})
			.on('click', '.b-button-preview', function() {
				$.boom.editor.state($(this).attr('data-preview'));
			})
			.on('click', '#b-page-template', function() {
				self.$settings.pageSettings('show', 'template');
				self.openPageSettings();
			})
			.on('click', '#b-menu-button', function() {
				var $body = $('body');

				if ($body.hasClass('pushy-active')) {
					self.maximise();
				} else {
					self.minimise();
				}
			})
			.on('click', '#b-page-settings', function() {
				var $settings = self.element
					.contents()
					.find('#b-page-settings-toolbar');
		
				if ($settings.hasClass('open')) {
					$settings.removeClass('open');

					setTimeout(function() {
						self.minimise();
					}, 1000);
				} else {
					self.maximise();
					$settings.addClass('open');
				}
			})
			.on('click', '#b-page-version-status', function() {
				self.$settings.pageSettings('show', 'drafts');
				self.openPageSettings();
			});

		this.buttonBar = this.element.contents().find('#b-topbar');
	},

	_create : function() {
		var toolbar = this;

		$.boom.log('init CMS toolbar');

		this.findButtons();

		this._toggle_view_live_button();
		this.status = $('#b-page-version-status')
			.pageStatus({
				page : this.options.page,
				publishable : this.options.publishable
			})
			.data('boom-pageStatus');
	
		this.$settings = this.element
			.contents()
			.find('.b-page-settings')
			.pageSettings({
				page: toolbar.options.page
			});

		this._bindButtonEvents();
	},

	findButtons : function() {
		this.buttons = {
			visible : this.element.contents().find('#b-page-visible'),
			invisible : this.element.contents().find('#b-page-invisible'),
			viewLive : this.element.contents().find('#b-page-viewlive')
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
			width : '60px',
			'z-index' : 10000
		});
	},
	
	openPageSettings: function() {
		this.maximise();

		this.element
			.contents()
			.find('#b-page-settings-toolbar')
			.addClass('open');
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
				.prop('disabled', true);
		} else {
			this.buttons.viewLive
				.attr('title', 'View the page as it appears on the live site')
				.prop('disabled', false);
		}
	}
});
