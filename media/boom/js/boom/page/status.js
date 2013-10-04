$.widget('boom.pageStatus', {

	menu : $('#b-page-publish-menu'),

	_buildMenu : function(status) {
		var self = this;

		var options = {
			"Publish now" : function(){
				self.options.page.publish()
					.done(function(response) {
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

		options = $.extend(options, {
			"Revert to published version" : function() {
				self.options.page.revertToPublished()
					.done(function() {
						top.location.reload();
					});
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

	set : function(status) {
		this.element.text(status);
		this.element.attr('data-status', status);

		this._buildMenu(status);

		if (status == 'published') {
			this.element.button('disable');
		} else {
			this.element.button('enable');
		}
	}
});