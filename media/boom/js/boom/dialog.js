$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom dialog widget.
	@class
	@static
	*/
	dialog : {

		/**
		@property dialogs
		@type array
		@default []
		*/
		dialogs : [],

		options : {
			/**
			@type string
			@default 'auto'
			*/
			width: 'auto',
			/**
			@type number
			@default 100
			*/
			maxWidth: 100,
			/**
			@type function
			@default null
			*/
			show: null,
			/**
			@type function
			@default null
			*/
			hide: null,
			/**
			@type boolean
			@default true
			*/
			autoOpen: true,
			/**
			@type boolean
			@default true
			*/
			modal: true,
			/**
			@type boolean
			@default false
			*/
			resizable: false,
			/**
			@type array
			@default ['center', 'center']
			*/
			position: ['center', 'center'],
			/**
			@type boolean
			@default true
			*/
			draggable: true,
			/**
			@type boolean
			@default true
			*/
			closeOnEscape: true
		},

		/**
		@function
		@param opts configuration options from $.boom.config.dialog
		*/
		open : function(opts){

			var self = this;

			this.options = $.extend({
				type: 'modal',
				dialogClass : 'b-dialog',
				selector: '',
				msg: '',
				iframe: false,
				cache: false,
				url: '',
				id: false,
				resizable: false,
				data: {},
				treeConfig: {},
				tabsConfig: {},
				selectedTab: 0,
				deferred_args: {}
			}, opts);

			var dialogConfig = $.extend({}, $.boom.config.dialog, this.options);

			dialogConfig = $.extend({}, dialogConfig, {
				maxWidth: this.options.maxWidth ||  600,
				title: opts.title,
				position: { my: 'top', at: 'top+120', of: window },
				modal: (this.options.type.toString() == 'modal'),
				close: function(event, ui){

					( dialogConfig.deferred ) && dialogConfig.deferred.reject( dialogConfig.deferred_args );

					$.boom.dialog.destroy( this, self.options.destroy );
				},
				buttons: this.options.buttons || [
					{
						text : 'Cancel',
						icons : { primary : 'ui-icon-boom-cancel' },
						click : function() {

							( dialogConfig.deferred ) && dialogConfig.deferred.reject( dialogConfig.deferred_args );

							$.boom.dialog.destroy( this, dialogConfig.destroy );
						}
					},
					{
						text : 'Okay',
						icons : { primary : 'ui-icon-boom-accept' },
						click : function() {

							(opts.callback) && opts.callback.call(this);
							( dialogConfig.deferred ) && dialogConfig.deferred.resolve( dialogConfig.deferred_args );

							$.boom.dialog.destroy( this, dialogConfig.destroy );
						}
					}
				]
			});

			var tabsConfig = $.extend({}, $.boom.config.tabs, {

				show: function(event, ui){

					$.boom.dialog.resize(dialog);

					if (self.options.tabShow) return self.options.tabShow.apply(this, arguments);
				},
				selected: this.options.selectedTab
			});

			var tooltipConfig = $.extend({}, $.boom.config.tooltip, {
				//delay: 240,
				//position: 'left'
			});

			var treeConfig = $.extend({}, $.boom.config.tree, this.options.treeConfig); //careful

			var	dialog,
				id =	this.options.id ? this.options.id : $.boom.util.dom.uniqueId( 'boom-dialog-' );
			dialog = $('#' + id).length ?
					$('#' + id) :
					$('<div />').attr('id', id).hide().appendTo( $( document ).contents().find( 'body' ) );


			function initDialog(dialog, ui){

				ui = ui === undefined;

				$.boom.page && $.boom.page.toolbar && $.boom.page.toolbar.maximise();


				dialog
				.dialog(dialogConfig);

				if (ui) {
					dialog.ui({
						tabs: tabsConfig,
						tooltip: tooltipConfig,
						tree: treeConfig
					});
				}

				this.resize(dialog);

				this.dialogs.push(dialog);

				$.boom.log('Dialog open');
			}

			if (this.options.iframe) {

				var load = 0;

				var iframe = $('<iframe />', { id: self.options.iframeId || id }).height(this.options.height - 30)
				.load(function(){
					if ($.isFunction(self.options.onIframeLoad)) {
						self.options.onIframeLoad.call(this);
					}

				}).appendTo(dialog);

				iframe.attr('src', this.options.url);

				initDialog.call(self, dialog);

				if ($.isFunction(self.options.onLoad)) {

					self.options.onLoad.apply(dialog, [ iframe[0].contentWindow ]);

					self.resize(dialog);
				}

			} else if (this.options.url.length) {

				if ( dialog.hasClass('ui-dialog-content') ){

					dialog.dialog('open');
				} else {

					setTimeout(function(){

						dialog.load(self.options.url, function(response, status){

							if (status == 'error') {

								if ( $.boom.page && $( '.ui-dialog:visible' ).length == 0 ) {

									$.boom.page.toolbar && $.boom.page.toolbar.minimise();
								}

								return;
							}

							initDialog.call(self, dialog);

							if ($.isFunction(self.options.onLoad)) {

								self.options.onLoad.apply(dialog);

							}

							self.resize(dialog);
						});
					}, 100);
				}

			} else if (this.options.msg.length) {

				dialog.html( this.options.msg );

				initDialog.call( this, dialog );

			} else if (this.options.selector.length && $(this.options.selector).length) {

				dialog
				.attr('title', $(this.options.selector).attr('title'))
				.html( $(this.options.selector).html() );

				initDialog.call(this, dialog);
			}

			return dialog;
		},

		/**
		@function
		@param opts configuration options
		*/
		bind : function(opts){
			var self = this;

			// FIXME: opts.image is expected to be an anchor selector

			if (opts.image) {

				opts.image.each(function(){

					$( this )
					.data('title', opts.image[0].title)
					.removeAttr('title')
					.click(function(event){

						event.preventDefault();

						$('<img />')
						.load(function(){

							self.alert(
								opts.image.data('title'),
								'<img src="' + this.src +'" />',
								function(){},
								this.width + 30
							);
						})
						.error(function(){

							$.boom.dialog.alert('Error', 'There was an error loading the image.');
						})
						.attr('src', this.href);

					});
				});
			}


			return false;
		},

		/**
		@function
		*/
		resize : function(dialog){

			dialog.dialog('option', 'position', dialog.dialog('option', 'position'));
		},

		/**
		@function
		*/
		resizeIframe : function(dialog){

			if (!dialog || !dialog.length) return;

			var iframe = dialog.find('iframe');

			dialog.css({ height: 'auto'}).find('.ui-dialog-content').css({height:'auto'});

			setTimeout(function(){

				iframe.height(iframe.contents().find('.ui-dialog').height());

				dialog.find('.ui-dialog-content').dialog('option', 'position', dialog.dialog('option', 'position'));
			});
		},

		/**
		@function
		*/
		destroy : function(dialog, callback){

			var self = this;
			var cache = $(dialog).dialog( 'option', 'cache' );


			if ( cache ) {
				//alert('cached');

				$(dialog).dialog('close');
			} else {

				//alert('not cached');

				$(dialog).dialog('destroy').remove();
			}

			(callback) && callback.apply();

			if ( $.boom.page && $( '.ui-dialog:visible' ).length == 0 ) {

				$.boom.page.toolbar && $.boom.page.toolbar.minimise();
			}
		},

		/**
		@function
		*/
		destroyAll : function(){

			var self = this;

			$.each(this.dialogs, function(i){

				this.dialog('destroy').remove();
			});
		},

		/**
		@function
		*/
		alert : function(type, msg, callback, width){

			var self = this;

			var div = $('#boom-dialog-alerts').length ? $('#boom-dialog-alerts') : $('<div id="boom-dialog-alerts" />').appendTo('body');

			div.html(msg.replace(/\n/g, '<br />'));

			if (type.toLowerCase().trim() == 'error') {

				var container = $('<div class="boom-error-report" />');

				var icon = $('<span class="ui-icon ui-icon-comment ui-helper-left" />');

				container.append(icon).appendTo(div);
			}

			this.open({
				selector: '#boom-dialog-alerts',
				title: type,
				width: width || 300,
				buttons: [
					{
						text : 'Okay',
						icons : { primary : 'ui-icon-boom-accept' },
						click : function() {

							self.destroy( $(this) );

							(callback) && callback.apply();
						}
					}
				]
			});
		},

		/**
		@function
		*/
		confirm : function(title, msg, callback){

			var self = this;
			var confirmed = new $.Deferred();

			this.open({
				msg: msg.replace(/\n/g, '<br />'),
				title: title,
				width: 300,
				deferred: confirmed,
				callback: callback
			});

			return confirmed;
		}
	}
});