/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', {
	buttons : {},

	_bindButtonEvents: function() {
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
 				self.showSettings('visibility');
			})
			.on('click', '.b-button-preview', function() {
				$.boom.editor.state($(this).attr('data-preview'));
			})
			.on('click', '#b-page-template', function() {
				self.showSettings('template');
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
				self.settingsAreOpen() ? self.closePageSettings() : self.showSettings();
			})
			.on('click', '#b-page-version-status', function() {
				self.settingsAreOpen() ? self.showSettings('drafts') : self.showSettingsAndCloseOnSave('drafts');
			})
			.on('mouseup', '#b-menu a', function() {
				// Clicking a link in the menu but opening in a new tab causes the menu to close.
				// But without this the iframe doesn't minimise and the window can't scroll.

				$(top.window).trigger('boom:dialog:close');
			});

		this.buttonBar = this.element.contents().find('#b-topbar');
	},

	closePageSettings: function() {
		var toolbar = this;

		this.$settingsContainer.removeClass('open');

		setTimeout(function() {
			toolbar.minimise();
			$(top.window).trigger('boom:dialog:close');
		}, 1000);
	},

	_create: function() {
		var toolbar = this;

		this.findButtons();
		this._toggle_view_live_button();

		this.status = $('#b-page-version-status')
			.pageStatus({
				page : this.options.page,
				publishable : this.options.publishable
			})
			.data('boom-pageStatus');
	
		this.$settingsContainer = this.element.contents().find('#b-page-settings-toolbar');

		this.$settings = this.$settingsContainer
			.find('.b-page-settings')
			.pageSettings({
				page: toolbar.options.page,
				close: function() {
					toolbar.closePageSettings();
				},
				draftsSave: function(event, data) {
					toolbar.draftsSaved(event, data);
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
							top.location.reload();
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
						'/' + ((primaryUrl === '/') ? '' : primaryUrl)
					);
				},
				deleteSave: function(event, response) {
					new boomNotification('Page deleted, redirecting to parent').show();

					setTimeout(function() {
						top.location = response;
					}, 500);
				}
			});

		this._bindButtonEvents();
	},

	draftsSaved: function(event, data) {
		if (data.action === 'revert') {
			top.location.reload();
		} else {
			this.status.set(data.status);
		}
	},

	findButtons: function() {
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
	maximise: function() {
		this.element.css({
			width : '100%',
			'z-index' : 100002
		});
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise: function() {
		this.element.css({
			width : '60px',
			'z-index' : 10000
		});
	},

	openPageSettings: function() {
		this.closeSettingsOnPublish = false;
		this.maximise();

		this.$settingsContainer.addClass('open');

		$(top.window).trigger('boom:dialog:open');
	},

	/**
	@function
	*/
	hide: function() {
		this.buttonBar.css('z-index', 1);
	},

	settingsAreOpen: function() {
		return this.$settingsContainer.hasClass('open');
	},

	/**
	@function
	*/
	show: function() {
		this.buttonBar.css('z-index', 10000);
	},

	showSettings: function(section) {
		if (!this.settingsAreOpen()) {
			this.openPageSettings();
		}

		if (section) {
			this.$settings.pageSettings('show', section);
		}
	},

	showSettingsAndCloseOnSave: function(section) {
		var toolbar = this;

		this.$settings
			.pageSettings({
				draftsSave: function(event, data) {
					toolbar.draftsSaved(event, data);
					toolbar.closePageSettings();
				}
			});

		this.showSettings(section);
	},

	_toggle_view_live_button: function() {
		if (this.buttons.visible.css('display') === 'none') {
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
