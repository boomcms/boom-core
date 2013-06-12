/**
@fileOverview Core CMS functionality.
*/
/**
@namespace
@name $
*/
$.extend({
	/**
	Namespace for core boom classes and functions.
	@static
	@class
	@name $.boom
	*/
	boom :
		/** @lends $.boom */
		{

		/**
		Boom bootstrap/setup. Initialises boom.config and does some browser detection.
		*/
		setup: function(){

			$.extend(this, { config: window.boomConfig });

			$.boom.isMobile = $.boom.util.isMobileDevice();

			// reference boom from the site window too.
			top.$.boom = $.boom;
		},

		/**
		Initialise boom classes. Create top bar and UI.
		@param type FIXME: who knows?
		@param {Object} options Boom options. Extends and overrides defaults in boom.config.
		*/
		init: function( type, options ){

			this.type = type;

			( options ) && $.extend( this.config, options );

			var classes = $.boom.data.boomInit;

			if ( $.isArray(classes) ) {
				$.each(classes, function(){
					$.boom[this].init();
				});
			}

			$.boom.loader.init();

			$('#boom-topbar').exists(function(){

				this.find('.ui-tabs-nav li').bind('mouseenter mouseleave', function(){

					$( this ).toggleClass( 'ui-state-hover' );
				});

				if ( $.boom.cookie.contains( 'navmenu' ) ) {
					$( '#boom-nav' ).hide();
				}
				$( '#boom-page-menu' ).on( 'click', function(){
					$( '#boom-nav' ).toggle();
					$.boom.cookie.toggle( 'navmenu');
				});

				var user_menu = {
					"Profile" : function(){
						var url = '/cms/profile';

						$.boom.dialog.open({
							'url': url,
							'title': 'User profile',
							callback: function() {
								data = $('#b-people-profile').serialize();
								data = data + '&avatar_id=' + $('.b-people-edit-avatar img').attr('data-asset-id');

								$.post(url, data)
									.done(function() {
										$.boom.growl.show('Profile updated');
									});
							},
							open: function() {
								$('.b-people-edit-avatar').on('click', function() {
									var avatar = $(this).find('img');

									$.boom.assets
										.picker({
											asset_rid : avatar.attr('data-asset-id'),
										})
										.done( function( rid ){
											avatar
												.attr('data-asset-id', rid)
												.attr('src', '/asset/view/'+rid+'/80/80');
										});
								});
							}
						});
					},
					"Logout" : function(){
						top.location = '/cms/logout';
					}
				};

				$('#boom-page-user-menu')
					.splitbutton({
						items: user_menu,
						width: 'auto',
						menuPosition: 'left',
						split: false
					});
			});

			$(window).bind('scroll', function(event){

				//$(this).scrollTop(0);

				return false;

			}).trigger('scroll');

			if ( options === undefined || options.ui === undefined || options.ui ) {

				$('body').ui();
			}
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom data storage.
	@class
	*/
	data: {
		benchmarks: {
			boom_setup_before_classes_load: (new Date).getTime()
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom cookie management.
	@class
	*/
	cookie : {

		/**
		@property ids
		*/
		ids: {},

		/** @function */
		init : function(){

			$.boom.log('Init cookie');

			this.config = $.boom.config.cookie;
			this.ids[ this.config.name ] = this.get( this.config.name ).split( this.config.delimiter );
		},

		/** @function */
		_set : function(name, val, expiredays){

			expiredays = expiredays || this.config.expiredays;

			var expiredate = new Date();

			expiredate.setDate(expiredate.getDate() + expiredays);

			document.cookie = name + '=' + escape(val) + ((expiredays == null) ? '' : ';expires=' + expiredate.toGMTString()) + ';path=' + this.config.path;

			$.boom.log('Set cookie var: ' + name + ' => ' + val);
		},

		/** @function */
		get : function(name){

			if (document.cookie.length){

				var start = document.cookie.indexOf(name + '=');

				if (start === -1) return '';

				start = start + name.length + 1;

				var end = document.cookie.indexOf(';', start);

				if (end === -1) end = document.cookie.length;

				return unescape(document.cookie.substring(start, end));
			}
			return '';
		},

		/** @function */
		add : function(id, name){

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			if ( $.inArray( id, ids ) !== -1 ) return;

			ids.push(id);

			this.ids[ name ] = ids;
			this._set( name || this.config.name, ids.join( this.config.delimiter ) );
		},

		/** @function */
		remove : function(id, name){

			if (!id) return;

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			for(var i in ids) ( ids[i] == id ) && ids.splice( i, 1 );

			this.ids[ name ] = ids;
			this._set( name || this.config.name, ids.join( this.config.delimiter ) );
		},

		/** @function */
		toggle : function(id, name){

			if (!id) return;

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			if ( $.inArray( id, ids ) !== -1 ) {

				this.remove( id, name );
			} else {
				this.add( id, name );
			};
		},

		/** @function */
		contains : function(id, name){

			if (!id) return;

			var name = name ? name : this.config.name;

			var ids = ( this.ids[ name ] ) ? this.ids[ name ] : this.get( name ).split( this.config.delimiter );

			if ( $.inArray( id, ids ) !== -1 ) {

				return true;
			} else {
				return false;
			};
		}
	}
});

(!window.console) && function(){
	window.console = /** @ignore */ { log: function(){}, debug: function(){}, error: function(){}, warning: function(){}, info: function(){} };
}();

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom logging. Extends console logging.
	*/
	log : function(type, msg){

		if (!$.boom.config.logs.show) return;

		if (msg === undefined) {
			msg = type;
			type = 'info';
		}

		if ($.boom.config.logs.showTimes){

			$.boom.config.logs.times.push((new Date).getTime());
			var time = $.boom.config.logs.times[$.boom.config.logs.times.length - 1] - $.boom.config.logs.times[$.boom.config.logs.times.length - 2];

			if (time) $.boom.config.logs.totalTime += parseInt( time, 10 );
			else time = 0;

			msg += ' : ' + time + 'ms : ' + ($.boom.config.logs.totalTime) + 'ms';
		}

		var log;

		switch(type.toLowerCase()) {
			case 'debug': log = window.console.debug; break;
			case 'error': log = window.console.error; break;
			case 'warning': log = window.console.warning; break;
			case 'info': log = window.console.info; break;
			default: log = window.console.log;
		}

		// FIXME for webkit
		//log.apply(this, [msg]);
		console.log(msg);
	}

});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom history manager. Mostly used by the tag managers.
	* @class
	@static
	*/
	history : {

		_interval: 0,

		hashCallback : function( hash ) {

		},

		/**
		Initialise from boom.config.history
		*/
		init : function(){

			this.options = $.boom.config.history;
		},

		/**
		Route hash URLs to functions
		@param {Function} hashCallback Callback to invoke with current fragment identifier
		@param {Function} nohashCallback Callback to invoke if no current fragment identifier
		*/
		route : function(hashCallback, nohashCallback){

			this.hashCallback = hashCallback || function(){};
			this.nohashCallback = nohashCallback || function(){};

			this.current_hash = this._getHash();

			if (this.current_hash) {

				this.hashCallback(this.current_hash);
			}

			if (!this.current_hash) {
				this.nohashCallback();
			}

			this._checkHistory();
		},

		/**
		Invoke the hashCallback for the current fragment identifier.
		@param {String} hash fragment identifier from page URL
		@returns {Object} returns the hstory callback return value, to allow callback chaining.
		*/
		load : function(hash){

			this.current_hash = decodeURIComponent(hash.replace(/\?.*$/, ''));

			this._setHash(this.current_hash);

			var promise = this.hashCallback(this.current_hash);

			this._checkHistory();

			return promise;
		},

		/**
		Reload without changing the current fragment identifier.
		*/
		refresh : function(){

			this.load( this.current_hash );
		},

		/**
		Get the current fragment identifier.
		*/
		getHash : function(){
			return this._getHash();
		},

		/**
		Set the current fragment identifier.
		*/
		setHash : function(val){
			this._setHash(val);
		},


		/**
		Get the current fragment identifier from the window URL.
		*/
		_getHash : function(){
			return $.trim( top.location.hash.replace(/^.*#/, '') );
		},

		/**
		Set the current fragment identifier.
		@param {String} val A fragment identifier, without the # character.
		*/
		_setHash : function(val){

			top.location.hash = ( val == '' ) ? '' : '#' + val;
			$.boom.log( 'setting hash ' + top.location.hash );
		},

		/**
		FIXME: No idea what this does.
		*/
		_checkHistory : function(){

			var self = this;

			clearInterval(this.interval);

			this.interval = setInterval(function(){

				var current_hash = self._getHash();

				if(current_hash != self.current_hash) {
					self.current_hash = current_hash;
					self.hashCallback(current_hash.replace(/^#/, ''));
				}

			}, this.options.checkInterval);
		}
	}
});

/**
@class
@name $.ajax
@static
*/
$.extend($.ajax,
	/** @lends $.ajax */
	{
	requestStack : [],

	/** @function */
	abortRequests : function(){

		$.each(this.requestStack, function(){

			if (this.readyState === 1) {

				this.requestStack[i].abort();

				delete this.requestStack[i];

				this.requestStack.splice(i, 1);
			}
		});
	}
});

$.fn.sload = function(url, successCallback) {

	if (!this.length) return this;

	var self = this;

	return $.ajax({
		type: 'GET',
		url: url,
		success: function(data, status, xhr){

			self.html( data );

			successCallback && successCallback.apply(self, [ xhr, status ]);
		}
	});
};

$.ajaxSetup({

	/**
	Default AJAX error handler.
	@function
	*/
	error: function(xhr, textStatus, error, callback) {

		// data is sent as a serialized string

		var showError = $.boom.config.errors.report, queryvar = /([^&=]+)=([^&]+)/g;

		while (match = queryvar.exec( decodeURIComponent( this.data ) )) {

			if ( match[1] == 'showAjaxError' && match[2] == 0 ) {

				showError = 0;
			}
		}

		setTimeout(function(){

			$.boom.loader.hide().hide('dialog');

			if ( showError ) {
				var errString;

				try
				{
					var error = $.parseJSON( xhr.responseText );
					var errString;

					if (error.type) {
						errString = error.type + ' : ';
					}
					errString = errorString + error.message;
				}
				catch (e) {}

				if (errString) {
					$.boom.dialog.alert('Error', 'Sorry, an unexpected error occured. Please try again.\n\n' + errString );
				}
			}

			(callback) && callback.apply();
		});
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	* @class
	@static
	*/
	loader : {

		/** initialise loader */
		init : function(){
			this.loaders = 0;
			this.elements = {
				loader: $('#boom-loader'),
				loaderDialogOverlay: $('#boom-loader-dialog-overlay')
			};
		},
		/** show loader */
		show : function(type){

			if ( !this.elements ) {
				this.init();
			}

			type = type || '';

			this.loaders ++;

			if ( type == 'dialog' ) {

				this.elements.loaderDialogOverlay.show();
			}

			this.elements.loader.show();

			return this.loaders;
		},
		/** hide loader */
		hide : function(force){

			force = (force == undefined) ? false : true;

			if (force) this.loaders = 0;

			if (this.loaders > 0) this.loaders --;

			if (this.loaders === 0) {
				$.each(this.elements, function(){
					$(this).hide();
				});
			}
			return this;
		},
		/** FIXME: This doesn't seem to do anything. */
		hideOverlay : function(){
			//this.elements.loaderOverlay.hide();
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom utils.
	@class
	@static
	*/
	util :
		/** @lends $.boom.util */
		{
		/** @function */
		cacheImages : function(images){

			if (!images) return;

			$.each(images, function(i){
				images[i] = new Image();
				images[i].src = this;
			});

			$.boom.log('Cache images : (' + images.length + ' total)');

			return this;
		},

		/** @function */
		isMobileDevice : function(){

			var mobile = false, uagent = window.navigator.userAgent.toLowerCase();
			var uagentStrings = [ 'iphone', 'ipod', 'android', 'symbian', 'series60', 'series70', 'series80', 'series90', 'windows ce', 'iemobile', 'wm5 pie', 'blackberry', 'vnd.rim', 'palm', 'webos', 'blazer', 'xiino', 'mobile', 'pda' ];

			$.each(uagentStrings, function(){
				if (new RegExp(this, 'i').test(uagent)){
					mobile = true;
					return false; // break
				}
			});

			return mobile;
		},

		/** @function */
		isLoaded: function(namespace, classes, callback){

			if (!namespace) return;

			var timers = [];

			$.each(classes, function(i, val){

				if ( !namespace[val] ) {

					timers[i] = setInterval(function(){

						if ( namespace[val] ) {

							clearInterval( timers[i] );

							timers.splice(i, 1);
						}
					}, 100);
				}
			});

			if (timers.length) {

				var check = setInterval(function(){

					if ( !timers.length ){

						clearInterval( check );

						callback();
					}

				}, 100);

			} else callback();
		},

		/**
		Create a tree widget for selecting pages.
		@function
		@returns {Promise} promise which notifies a page ID when a page is selected.
		*/
		page_tree : function( $element ){

			var self = this;
			var complete = new $.Deferred();

			var item_selected = function( $item ){

				$item
					.addClass( 'ui-state-active' )
					.parents( '.boom-tree' )
					.find( 'a.ui-state-active' )
					.not( $item )
					.removeClass( 'ui-state-active' );

			};

			var parent_treeConfig = $.extend({}, $.boom.config.tree, {
				toggleSelected: false,
				onClick: function( event ){

					event.preventDefault();

					var link = {};
					var $node = $(this);
					var uri = $node.attr('href');
					var page_rid = $node.attr('rel');

					link.title = $node.text();
					link.page_id = page_rid;
					link.url = uri;

					item_selected( $node );

					complete.notify( link );
				},
				onToggle: function( page_id ){

					var list_ready = $.Deferred();
					var children = $.ajax( {
						type: 'POST',
						url: '/page/children.json',
						data: {parent : page_id},
						dataType: 'json'
					} );
					children.done( function( data ) {

						var children = $('<ul></ul>');

						$( data ).each( function( i, item ){
							var li = $('<li></li>')
								.data( 'children', parseInt(item.has_children, 10) )
								.appendTo( children );
							$('<a></a>')
								.attr( 'id', 'page_' + item.id )
								.attr( 'href', item.url )
								.attr( 'rel', item.id )
								.text( item.title )
								.appendTo( li );
						});

						var parent_id = $( 'input[name=parent_id]' ).val();
						children.find( '#page_' + parent_id ).addClass( 'ui-state-active' );


						list_ready.resolve( { childList: children } );
					});

					return list_ready;
				}
			});

			$element.tree('destroy').tree( parent_treeConfig );

			return complete;
		},

		/**
		@class
		@static
		@name $.boom.util.dom
		*/
		dom :
			/** @lends $.boom.util.dom */
			{
			/**
			Generate a unique ID for a DOM element
			@param {String} prefix Optional prefix. Defaults to 'boom-'
			*/
			uniqueId : function(prefix){

				prefix = prefix || 'boom-';
				var id;

				do {
					id = Math.floor(Math.random()*1000);
				} while($('#' + prefix + id).length);

				return (prefix + id);
			},

			/** @function */
			getDocHeight : function(window){
				window = window || window;
				return Math.max(
					$(window.document).height(),
					$(window).height(),
					/* For opera: */
					window.document.documentElement.clientHeight
				);
			}
		},

		/**
		@class
		@static
		@name $.boom.util.obj
		*/
		obj :
			/** @lends $.boom.util.obj */
			{
			/**
			searches for a value in a given object
			*/
			search : function(obj, val, exactMatch, p, r) {

				exactMatch = exactMatch === undefined ? true : exactMatch;

				var results = r || [];
				var parents = p || '';
				this.recursion_count = this.recursion_count || 0;
				this.recursion_count++;

				if (this.recursion_count > 20) {
					$.boom.log('error', 'too much recursion in $.boom.util.obj.search');
					return this.results;
				}

				for (var i in obj) {

					if (exactMatch) (obj[i] === val) && results.push(parents + i);
					else (new RegExp(val).test(obj[i])) && results.push(parents + i);

					// recursion
					if (typeof obj[i] == 'object') {
						results = $.boom.util.obj.search(obj[i], val, exactMatch, i + '.', results);
					}
				}

				return results;
			}
		},

		/**
		@class
		@static
		@name $.boom.util.url
		*/
		url :
			/** @lends $.boom.util.url */
			{
			/** @function */
			addQueryStringParams: function(data, returnURL){

				returnURL = returnURL || true;

				var match, params = {}, querystring = window.location.search.substring(1), queryparam = /([^&=]+)=([^&]+)/g;

				while (match = queryparam.exec(querystring)) {
					params[ decodeURIComponent( match[1] ) ] = match[2];
				}

				$.each(data, function(key, val){

					params[ key ] = val;
				});

				return !returnURL ? $.param( params ) : top.location.protocol + '//' + top.location.host + top.location.pathname + '?' + $.param( params );
			}
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom growl notifications.
	@class
	@static
	*/
	growl : {

		/** @function */
		show : function(msg, sticky){

			$.jGrowl(msg, $.extend({}, $.boom.config.growl, {
				sticky: sticky,
				closer: false,
				open: function(elem, message){
					$(this).removeClass('ui-state-highlight').addClass('ui-state-default').find('.message').prepend('<span class="ui-icon ui-icon-check ui-helper-left" />');
				}
			}));
		},

		/** @function */
		hide : function(id){
			$('.jGrowl-notification').trigger('jGrowl.close');
		}

	}

});

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

		/**
		@function
		@param opts configuration options from $.boom.config.dialog
		*/
		open : function(opts){

			var self = this;

			this.options = $.extend({
				type: 'modal',
				selector: '',
				msg: '',
				iframe: false,
				cache: false,
				url: '',
				id: false,
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

				$.boom.page && $.boom.page.toolbar.maximise();


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

				$.boom.loader.show();

				var load = 0;

				var iframe = $('<iframe />', { id: self.options.iframeId || id }).height(this.options.height - 30)
				.load(function(){

					$.boom.loader.hide();

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

					$.boom.loader.show('dialog');

					setTimeout(function(){

						dialog.load(self.options.url, function(response, status){

							if (status == 'error') {

								if ( $.boom.page && $( '.ui-dialog:visible' ).length == 0 ) {

									$.boom.page.toolbar.minimise();
								}

								return;
							}

							$.boom.loader.hide('dialog');

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

						$.boom.loader.show('dialog');

						$('<img />')
						.load(function(){

							$.boom.loader.hide('dialog');

							self.alert(
								opts.image.data('title'),
								'<img src="' + this.src +'" />',
								function(){},
								this.width + 30
							);
						})
						.error(function(){

							$.boom.loader.hide('dialog');

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

				$.boom.page.toolbar.minimise();
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

				var anchor = $('<a />', {
					href: '#',
					title: 'Send a detailed error report to Hoop'
				})
				.addClass('boom-tooltip')
				.click(function(){

					$.boom.errors.report();

					return false;

				}).html('Report this error');

				container.append(icon).append(anchor).appendTo(div);
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

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom error reporting.
	@class
	@static
	*/
	errors : {

		/** @function */
		report : function(msg){

			var data = {
				location: window.location,
				browser: navigator.appName + ' ' + navigator.appVersion + ' ' + navigator.platform,
				useragent: navigator.userAgent,
				type: '404'
			};

			$.boom.dialog.open({
				url: '/get-error-report.php',
				title: 'Report an error',
				data: data,
				callback: function() {
					alert('clicked send!');
				}
			});
		}
	}
});

$.boom.setup();

$.extend($.boom.data, {
	boomInit: ['cookie', 'history']
});
