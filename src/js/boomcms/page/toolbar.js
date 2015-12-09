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
				self.$settings.pageSettings('show', 'delete');
				self.openPageSettings();
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
			.on('click', '.b-page-visibility', function() {
				self.$settings.pageSettings('show', 'visibility');
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
					$(top.window).trigger('boom:dialog:open');
				} else {
					$(top.window).trigger('boom:dialog:close');
				}
			})
			.on('click', '#b-page-settings', function() {
					self.openPageSettings();
			})
			.on('click', '#b-page-version-status', function() {
				self.showSettingsAndCloseOnSave('drafts');
			});

		this.buttonBar = this.element.contents().find('#b-topbar');
	},
	
	closePageSettings: function() {
		var toolbar = this;

		this.element
			.contents()
			.find('#b-page-settings-toolbar')
			.removeClass('open');

		setTimeout(function() {
			toolbar.minimise();
			$(top.window).trigger('boom:dialog:close');
		}, 1000);
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
				page: toolbar.options.page,
				close: function() {
					toolbar.closePageSettings();
				},
				draftsSave: function(event, data) {
					if (data.action === 'revert') {
						$.boom.reload();
					} else {
						toolbar.status.set(data.status);
					}
				},
				featureSave: function(event, asset) {
					top.$('.b-page-featureimage').each(function() {
						var $el = $(this);

						if (asset.getId() > 0) {
							if ($el.is('img')) {
								var src = $el
									.attr('src')
									.replace(/\/asset\/view\/\d+/, asset.getUrl());

								$el.attr('src', src);
							} else {
								var attrs = { };

								$.each($el[0].attributes, function(i, attr) {
									attrs[attr.nodeName] = attr.nodeValue;
								});

								$el.replaceWith(function () {
									return $("<img />", attrs).attr('src', asset.getUrl());
								});
							}
						} else {
							$el.remove();
						}
					});
				},
				templateSave: function() {
					toolbar.status.set('draft');

					new boomConfirmation('Reload page?', "Do you want to reload the page to view the new template?")
						.done(function() {
							$.boom.reload();
						});
				},
				visibilitySave: function(event, response) {
					if (response == 1) {
						toolbar.buttons.visible.show();
						toolbar.buttons.invisible.hide();
					} else {
						toolbar.buttons.visible.hide();
						toolbar.buttons.invisible.show();
					}

					toolbar._toggle_view_live_button();
				},
				urlsSave: function(event, primaryUrl) {
					var history = new boomHistory();
					history.replaceState({},
						top.window.document.title,
						'/' + primaryUrl
					);
				},
				deleteSave: function(event, response) {
					new boomNotification('Page deleted, redirecting to parent');

					setTimeout(function() {
						top.location = response;
					}, 500);
				}
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
		this.closeSettingsOnPublish = false;
		this.maximise();

		this.element
			.contents()
			.find('#b-page-settings-toolbar')
			.addClass('open');

		$(top.window).trigger('boom:dialog:open');
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
	
	showSettingsAndCloseOnSave: function(settingsGroup) {
		var toolbar = this;

		this.$settings
			.pageSettings({
				save: function() {
					toolbar.$settings.pageSettings({
						save: function() {}
					});
					
					toolbar.closePageSettings();
				}	
			})
			.pageSettings('show', settingsGroup);

		this.openPageSettings();
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
