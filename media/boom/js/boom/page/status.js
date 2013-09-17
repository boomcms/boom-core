$.widget('boom.pageStatus', {

	cancel_button : $('#b-page-cancel').button({}),

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
				'Publish at a future time' : function() {
					self.embargo();
				}
			});
		}
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

		var self = this;
		this.cancel_button.on('click', function() {
			self.discardChanges();
		});
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
			width: 300,
			// cache: true,
			callback: function(){
				$.boom.page.settings.save(
					url,
					$(this).find("form").serialize(),
					"Page embargo saved."
				)
				.done(function(response) {
					self.set(response);
				});
			},
			open: function(){
				$( '#page-visible' ).on( 'change', function(){
					switch( $( this ).val() ) {
						case '1':
							$( '#page-embargo' ).removeAttr( 'disabled' );
						break;
						case '0':
							$( '#page-embargo' ).attr( 'disabled', 'disabled' );
						break;
					}
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

		if (status == 'live') {
			this.element.button('disable');
			this.cancel_button.button('disable');
		} else {
			this.element.button('enable');
			this.cancel_button.button('enable');
		}
	}
});