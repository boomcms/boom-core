$.widget('boom.pageStatus', {

	menu : $('#b-page-publish-menu'),

	_buildMenu : function(status) {
		var self = this, options;

		options = this.options.publishable? this._get_publish_menu(status) : this._get_approvals_menu(status);

		options = $.extend({
				"Preview": function() {
					$.boom.editor.state('preview');
				}
			}, options, {
			"Revert to published version" : function() {
				// The call to setTimout fixes a bug in IE9 where the toolbar call is minimised (because the splitbutton menu has close) after the dialog is opened.
				// Therefore preventing the dialog from being seen.
				setTimeout(function() {
					self.options.page.revertToPublished()
						.done(function() {
							top.location.reload();
						});
					}, 0);
			}
		});

		if (this.element.hasClass('ui-splitbutton-hitarea')) {
			this.menu.splitbutton('destroy');
		}

		this.menu
			.splitbutton({
				items: options,
				width: 'auto',
				menuPosition: 'right',
				split: false
			});
	},

	_create : function() {
		this.set(this.element.text().trim());
	},

	_get_abbreviated_status : function(status) {
		switch(status) {
			case 'published':
				return "pub'd";
			case 'draft':
				return 'draft';
			case 'embargoed':
				return "emb'd";
			case 'pending approval':
				return "pen'd";
		}
	},

	_get_approvals_menu : function(status) {
		var self = this, options = {};

		(status == 'draft') && (options = {
			"Request approval" : function(){
				self.options.page.requestApproval()
					.done(function(response) {
						new boomNotification('This version of the page is awaiting approval.');
						self.set(response);
					});
			}
		});

		return options;
	},

	_get_publish_menu : function(status) {
		var self = this, options;

		options = {
			"Publish now" : function(){
				self.options.page.publish()
					.done(function(response) {
						new boomNotification('This version of the page is now published.');
						self.set(response);
					});
			}
		};

		if (status == 'embargoed') {
			options = $.extend(options, {
				'View or edit embargo time' : function() {
					self.options.page.embargo()
						.done(function(response) {
							self.set(response);
						});
				}
			});
		} else {
			options = $.extend(options, {
				'Publish later' : function() {
					self.options.page.embargo()
						.done(function(response) {
							self.set(response);
						});
				}
			});
		}

		return options;
	},

	set : function(status) {
		var abbreviated_status = this._get_abbreviated_status(status);

		this.element
			.find('span')
			.text(abbreviated_status)
			.end()
			.attr('data-status', status)
			.attr('title', status.ucfirst());

		this._buildMenu(status);

		if (status == 'published') {
			this.element.prop('disabled', true);
		} else {
			this.element.prop('disabled', false);
		}
	}
});