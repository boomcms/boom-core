$.widget('boom.pageStatus', {

	menu : $('#b-page-publish-menu'),

	_buildMenu : function(status) {
		var self = this;

		var options = {
			"Publish now" : function(){
				self.publish();
			}
		};

		if (status == 'embargoed') {
			options = $.extend(options, {
				'View or edit embargo time' : function() {
					self.embargo();
				}
			});
		} else {
			options = $.extend(options, {
				'Publish later' : function() {
					self.embargo();
				}
			});
		}

		options = $.extend(options, {
			"Revert to published version" : function() {
				self.discardChanges();
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

	discardChanges : function() {
		$.boom.dialog.confirm(
			'Discard changes',
			'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?'
		)
		.done( function(){
			$.boom.loader.show();

			$.post('/cms/page/discard/' + $.boom.page.options.id, {csrf : $.boom.options.csrf})
				.always(function() {
					$.boom.loader.hide();
				})
				.done(function() {
					top.location.reload();
				});
		});
	},

	embargo : function() {
		var url = '/cms/page/version/embargo/' + $.boom.page.options.id;
		var self = this;

		$.boom.dialog.open({
			url: url,
			title: 'Page embargo',
			width: 440,
			callback: function(){
				$.boom.page.settings.save(
					url,
					$(this).find("form").serialize(),
					"Page embargo saved."
				)
				.done(function(response) {
					self.set(response);
				});
			}
		});
	},

	publish : function() {
		var self = this;

		$.boom.loader.show();

		$.post('/cms/page/version/embargo/' + $.boom.page.options.id, {csrf : $.boom.options.csrf})
			.done(function(response) {
				self.set(response);
			})
			.always(function() {
				$.boom.loader.hide();
			});
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