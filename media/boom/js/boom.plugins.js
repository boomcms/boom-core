/**
boom.plugins.js
@author Hoop Associates
*/

/**
@namespace
@name $.fn
*/

(function( $ ){

	/**
	@function
	*/
	$.fn.exists = function( func ){

		return 	( this.length && func && func.apply( this ) ) ? 
			this : 
			( !func ? this.length : this );
	};
	
	/**
	@function
	*/
	$.fn.selectRange = function(start, end) {
	  return this.each(function() {
	    if(this.setSelectionRange) {
	      this.focus();
	      this.setSelectionRange(start, end);
	    } else if(this.createTextRange) {
	      var range = this.createTextRange();
	      range.collapse(true);
	      range.moveEnd('character', end);
	      range.moveStart('character', start);
	      range.select();
	    }
	  });
	};

	/**
	@function
	*/
	$.size = function(obj){
		
		if ( typeof obj === 'object' ) {

			var size = 0, key;

			for (key in obj) {

				if ( obj.hasOwnProperty( key ) ) {

					size++;
				}
			}

			return size;

		} else {

			return $( obj ).size();
		}
	};

	/**
	@class
	@name $.ui.infiniteScroll
	@extends $.ui
	*/
	$.widget('ui.infiniteScroll', {
		/** @lends $.ui.infiniteScroll */

		/**
		@property
		*/
		options: {
			url: '/get_data/$page',
			amount: 30,
			selector: '',
			pxFromBottom: 50,
			// callbacks
			beforeLoad: function( event, data ){},
			onLoad: function( event, data ){}
		},

		/**
		@property
		*/
		_scrollTop: 0, 
		/**
		@property
		*/
		_isLoading: false, 
		/**
		@property
		*/
		_stopLoading: false, 
		/**
		@property
		*/
		_curPage: 2,
		
		/**
		@function
		*/
		_create : function(){

			var self = this;

			this._getDimensions();

			this.element.bind('scroll.boom', function( event ){

				var scrollTop = $( this ).scrollTop();

				if ( 
					! self._isLoading && // are we currently retrieving a new data set?
					scrollTop > self._scrollTop && // are we scrolling down?
					( self.dimensions.maxHeight - self.dimensions.minHeight - self.options.pxFromBottom ) < scrollTop  // are we at the bottom?
				) {

					self._load( event );
				}
					
				self._scrollTop = scrollTop;

			}).trigger('scroll.boom');
		},

		/**
		@function
		*/
		_load: function( event, dataCallback ){

			var self = this;
					
			this._isLoading = true;
					
			this._trigger( 'beforeLoad' );

			$( '<div></div>' ).load( this.options.url.replace(/\$page/, this._curPage) + ' ' + this.options.selector, function(){

				self._trigger( 'onLoad' , event, {
					html: $( this ).children() 
				});

				self._curPage++;

				self._getDimensions();

				self._isLoading = false;
				
				if ( ! $( this ).children().size() ) {

					this._stopLoading = true;

				} else if ( $( this ).children().size() && dataCallback ) {
				
					dataCallback.apply( this );
				}

				$( this ).remove();
			});
		},

		/**
		@function
		*/
		_getDimensions: function(){
			
			this.dimensions = {
				minHeight: this.element.height()
			};

			this.element.height( 'auto' );

			this.dimensions.maxHeight = this.element.height();

			this.element.height( this.dimensions.minHeight );

			this.element.scrollTop( this._scrollTop );
		},

		/**
		@function
		*/
		loadInitialData: function(){

			var self = this;

			this._getDimensions();

			if ( !this._stopLoading && this.dimensions.maxHeight === this.dimensions.minHeight ) {

				this._load(null, function(){

					// recursion!
					self.loadInitialData();
				});
			}
		},

		/**
		@function
		*/
		getDimensions: function(){

			this._getDimensions();
		},

		/**
		@function
		*/
		destroy: function(){

			$.Widget.prototype.destroy.apply(this, arguments);

			this.element.unbind('scroll.boom');
		}
	});

	/**
	@class
	@name $.ui.splitbutton
	@extends ui
	*/
	$.widget('ui.splitbutton', {
		/** @lends $.ui.splitbutton */
		
		options: {
			width: 'auto',
			split: true,
			menuPosition: 'right',
			menuStack: 'bottom'
		},
		
		/**
		@function
		*/
		_create : function(){

			var self = this;

			this.elements = {
				button: this.element.find( 'button:eq(0)' ).addClass('ui-button-text-icon').button(),
				hitarea: !this.options.split ? 
					this.element.find( 'button:eq(0)' ) : 
					this.element.find( 'button:eq(1)' ).button({
						text: false,
						icons: {
							primary: 'ui-icon-triangle-1-s'
						}
					}).addClass('boom-button'),
				menu: $('<ul />').appendTo('body')
			};

			this.element
				.addClass( this.widgetBaseClass )
				.buttonset();
			
			this.elements.hitarea
				.attr('role', 'hitarea')
				.addClass( this.widgetFullName + '-hitarea');

			this.elements.menu
				.addClass( this.widgetFullName + '-menu ui-widget ui-widget-content ui-corner-all ui-helper-reset')
				.attr('role', 'menu');

			this._build();

			this._bind();
		},

		/**
		@function
		*/
		_build : function(){

			var self = this;

			if ( !this.options.items) return;

			$.each(this.options.items, function(label){

				self._addItem( label, {}, this, self.options.menuStack );

			});

		},

		/**
		@function
		*/
		_addItem: function( label, data, callback, menustack ) {

			var self = this, 

			anchor = 
				$( '<a />')
				.attr('href', '#')
				.bind('itemclick', function(event){
	
					self.elements.hitarea.removeClass('ui-state-hover').blur();
					
					self.elements.button.removeClass('ui-state-hover').blur();
					
					( callback ) && callback.apply( this, [ event, data ] );
	
					self._trigger('itemclick', event);
				})
				.bind( 'mousedown click', function(){
	
					$(this).trigger('itemclick');
				})
				.bind('mouseenter mouseleave', function(){
	
					$(this).toggleClass('ui-state-hover');
				})
				.addClass('ui-corner-all')
				.html( label );

			$( '<li />' )
				.attr('role', 'menuitem')
				.append( anchor )
				.addClass( 'ui-helper-reset' )
				[ menustack == 'bottom' ? 'appendTo' : 'prependTo' ]( this.elements.menu );
		},
		
		/**
		@function
		*/
		_bind : function(){

			var self = this;

			this.elements.button
			.bind('click', function(event){

				if ( self.options.split && !self.options.buttonclick ) {

					self.elements.menu.find('a:first').trigger('itemclick');
				} else {
				
					self._trigger('buttonclick', event);
				}
			});
			
			this.elements.hitarea
			.bind( 'click', function(){

				$(this).focus();
				
				self.open();
			})
			.bind( 'blur', function(){

				self.close();
			});

			this.elements.menu
			.bind('show', function(){

				var hitarea = self.elements.hitarea, menu = self.elements.menu, 
					offset = hitarea.offset(), width = self.options.width, position = self.options.menuPosition;

				if ( width == 'inherit' ) {

					menu.width( 'auto ');

					var padding = ( menu.outerWidth() - menu.width() ) / 2;

					width = self.element.width() - padding;
				}

				var left = ( position == 'right' ) ?
						self.elements.button.offset().left : 
						( offset.left + hitarea.outerWidth() ) - menu.outerWidth();

				menu
				.width( width )
				.css({
					left: left,
					top: offset.top + hitarea.outerHeight()
				});
				
				$.boom.page && $.boom.page.toolbar.maximise();
			})
			.bind('hide', function(){

				$(this).css({ left: -9999 });
				
				$.boom.page && $.boom.page.toolbar.minimise();
			});
		},
		
		/**
		@function
		*/
		open : function(){
			
			this.elements.menu.trigger('show');

			this._trigger('open');
		},

		/**
		@function
		*/
		close : function(){
			
			this.elements.menu.trigger('hide');

			this._trigger('close');
		},

		/**
		@function
		*/
		add : function(label, data, callback, menuStack){

			this._addItem( label, data, callback, menuStack || this.options.menuStack );

			return this;
		},

		/**
		@function
		*/
		items: function(){
			
			return this.elements.menu.children();
		},

		/**
		@function
		*/
		destroy : function(){

			this.elements.menu.remove();

			this.element.buttonset('destroy');

			$.Widget.prototype.destroy.apply(this, arguments);
		}
	});

	/**
	@function
	*/
	$.fn.ui = function(opts){
		
		opts = $.extend({
			tabs: $.boom.config.tabs,
			button: $.boom.config.button,
			selectmenu: $.boom.config.selectmenu,
			tooltip: $.boom.config.tooltip,
			tree: $.boom.config.tree,
			sortable: $.boom.config.sortable,
			datepicker: $.boom.config.datepicker	
		}, opts);

		var elems = '.boom-button, .boom-buttonset, .boom-tabs, .boom-selectmenu, .boom-tooltip, .boom-tree, .boom-sortable, .boom-datepicker, .boom-toggleinput, .boom-splitbutton, .boom-help';

		$.boom.log('Start bind UI events');

		this.find(elems).each(function(){

			var elem = $(this), types = [];

			$.each(this.className.split(' '), function(){

				if (/boom/.test(this)) {
					
					types.push(this.replace(/boom-/, ''));
				}
			});

			$.each(types, function(){
				switch(this.toString()) {
					case 'button' :
						var primary = elem.attr( 'data-icon' );
						var secondary = elem.attr( 'data-icon-secondary' );
						opts.button = $.extend( opts.button, { 
							icons: { primary: primary, secondary: secondary },
							text: false,
							label: $.trim( elem.text() ) 
						});
						elem.button(opts.button);
					break;
					case 'splitbutton' :
						elem.splitbutton(opts.splitbutton);
					break;
					case 'buttonset' :
						elem.buttonset(opts.button);
					break;
					case 'tabs' :
						elem.tabs(opts.tabs);
					break;
					case 'selectmenu' :
						elem.selectmenu(opts.selectmenu);
					break;
					case 'tooltip' : //FIXME: Disalbed because it was breaking the JS. (Rob 2012/03/27).
						elem.tooltip(opts.tooltip);
					break;
					case 'tree' :
						elem.tree($.extend({}, opts.tree, {
							border: !(/tree-noborder/).test(elem[0].className)
						}));
					break;
					case 'toggleinput' :
						elem.toggleField();
					break;
					case 'sortable' : 
						elem.sortable($.extend({}, opts.sortable, {
							axis: (/sortable-y/.test(elem[0].className) ? 'y' : 'x')
						}));
					break;
					case 'datepicker' :
						elem.datetimepicker(opts.datepicker);
					break;
					case 'help' :
						elem.on( 'click', function( event ){
							$.boom.dialog.alert(
								'',
								$( this ).attr( 'data-help')
							);
						});
					break;
				};
			});
		});

		$.boom.log('Stop bind UI events');

		return this;
	};

	/**
	@function
	*/
	$.fn.render = function(data){

		data = data || {};

		function walkTextNodes(textNodeCallback){

			(this.nodeType === 3 && textNodeCallback) && textNodeCallback.call(this);

			$.each(this.childNodes, function(){
				walkTextNodes.call(this, textNodeCallback);
			});
		}

		function replace(key){
			this.nodeValue = this.nodeValue.replace(new RegExp('\\$\{' + key + '\}', 'g'), data[key]);
		}

		return this.each(function(){

			walkTextNodes.call(this, function(){

				var nodeValue = this.nodeValue.replace(new RegExp('\\n', 'g'), '');

				while(match = /\{(.*?)\}/g.exec(nodeValue)) {

					(data[match[1]]) && replace.call(this, match[1]);
				}
			});
		});
	}; 

	/**
	@function
	*/
	$.fn.toggleField = function(){

		return this.each(function(){

			if (this.nodeName != 'INPUT') return true;

			var $field = 
				$(this)
				.data('val', this.value)
				.focus(function(){
					this.value = ($.trim(this.value) == $field.data('val') ? '' : this.value);
				})
				.blur(function(){
					this.value = ($.trim(this.value) == '' ? $field.data('val') : this.value);
				});
		});
	};

	/**
	@function
	*/
	$.fn.pageSortable = function(opts){
	
		return this.each(function(){

			var config = $.extend({}, $.boom.config.sortable, {
				axis: (/sortable-y/.test(this.className) ? 'y' : 'x')
			});
						
			$(this).sortable(config);
		});
	};

	/**
	@function
	*/
	$.fn.captions = function(options){

		options = $.extend({
			captionTitleSelector: '.caption',
			captionOverlaySelector: '.caption-overlay',
			showSpeed: 220,
			hideSpeed: 220,
			opacityOverlay: 0.5
		}, options);

		return this.each(function(){
			$(this)
			.find('img')
				.removeAttr('title')
				.removeAttr('alt')
			.end()
			.bind('mouseenter', function(){
				$(this)
				.find(options.captionTitleSelector)
					.stop()
					.css({ opacity: 0 })
					.animate({ 'opacity': 1 }, options.showSpeed)
				.end()
				.find(options.captionOverlaySelector)
					.stop()
					.css({ opacity: 0, height: $(this).find(options.captionTitleSelector).outerHeight() })
					.animate({ 'opacity': options.opacityOverlay }, options.showSpeed);
			})
			.bind('mouseleave', function(){
				$(this)
				.find(options.captionTitleSelector)
					.stop()
					.css({ opacity: 1 })
					.animate({ 'opacity': 0}, options.hideSpeed)
				.end()
				.find(options.captionOverlaySelector)
					.stop()
					.css({ opacity: options.opacityOverlay })
					.animate({ 'opacity': 0 }, options.hideSpeed);
			});
		});
	};

	/**
	@class
	@name ui.tree
	@extends ui
	*/
	$.widget('ui.tree', {
		/** @lends ui.tree */
		
		/**
		@property
		*/
		options: {
			maxSelected: -1,
			toggleSelected: false,
			click: true,
			height: 240,
			width: 320,
			useCookie: true,
			cookieName: 'boom-tree',
			showRemove: 'auto',
			showEdit: 'auto',
			iconHitareaClosed: 'ui-icon-triangle-1-e',
			iconHitareaOpen: 'ui-icon-triangle-1-s',
			iconHitareaPlaceholder: 'ui-icon-placeholder',
			iconHitareaHover: 'ui-icon-hover',
			iconDefault: 'ui-icon-document',
			iconToggleChecked: 'ui-icon-check',
			iconRemove: 'ui-icon-close',
			iconEdit: 'ui-icon-wrench',
			anchorActive: 'ui-state-active',
			onClick: function(event){},
			onEditClick: function(event){},
			onRemoveClick: function(event){},
			onToggle: false
		},

		/**
		@function
		*/
		_create : function(){
			
			$.boom.log( 'tree create' );

			if (this.element[0].nodeName != 'UL') return;
			
			var self = this;
			
			this.amountChecked = 0;

			this.elements = {};

			$.each(this.element[0].className.split(' '), function(){
				if (/boom-tree-icon/.test(this)) {
					iconDefault = this.replace(/boom-tree-icon-/, '');
				};
			});
			
			var $container = 
				$('<div />')
				.addClass('boom-tree-container ui-widget')
				.width(this.options.width)
				.height(this.options.height);

			if (this.options.height != 'auto') {
				$container.css({ overflow: 'auto' });
			}

			if (this.options.border) $container.addClass('ui-state-active ui-corner-all');
			
			this.element.wrap($container);

		},

		/**
		@function
		*/
		_init : function(){
			
			$.boom.log( 'tree init' );

			var self = this;
			
			console.log( this.element );

			this.cookieItems = !this.options.useCookie ? [] : $.boom.cookie.get(this.options.cookieName).split($.boom.config.cookie.delimiter);

			
			var render_children = function( $ul ){
				
				var children = $ul.children( 'li' ).toArray();
				var i;
				
				for ( i in children ) {
					$this = $( children[i] );
					self._add_item( $this );
					render_children( $this.children( 'ul' ) );
				}
			};
			
			if ( this.element.is( 'ul' ) ) {
				render_children( this.element );
			} else {
				render_children( this.element.children( 'ul' ) );
			}
		},
		
		/**
		@function
		*/
		_set_icon : function( $item ) {
			
			var self = this;
			var defaultIcon;
			
			return;
			
			if ( $item.find('> a:first').hasClass( self.options.anchorActive ) && self.options.toggleSelected ) {

				defaultIcon = self.options.iconToggleChecked;

				self.amountChecked++;

			} else {

				defaultIcon = self.options.iconDefault;
			}

			if ( !$item.find( '.' + defaultIcon ).length ) {
				$('<span />')
					.addClass('ui-icon ' + defaultIcon)
					.prependTo( $item );
			}
			
			return self;
		},
		
		/**
		@function
		*/
		_set_toggle : function( $item ) {
			
			var self = this;
			
			$('<span />')
				.addClass('boom-tree-hitarea ui-icon')
				.bind('boom-tree.toggle', function(){
					self.toggle( $item );
				})
				.click(function(event){
					self._trigger('toggle', event, { hitarea: this });
	
					$( this ).trigger( 'boom-tree.toggle' );
				})
				.hover(
					function(){
						$(this).addClass(self.options.iconHitareaHover);
					},
					function(){
						$(this).removeClass(self.options.iconHitareaHover);
					}
				)
				.prependTo( $item );
				
				return self;
		},
		
		/**
		@function
		*/
		_set_edit : function( $item ) {
			
			var self = this;
			var re = /tree-remove/;
			
			if (self.options.showEdit === true || (self.options.showEdit === 'auto' && re.test(self.element[0].className)))
			{
				$('<span />', {
					title: 'Edit'
				})
					.css({ margin: 0 })
					.bind('boom-tree.edit', function(event){

						if (self.options.onEditClick) {

							self.options.onEditClick(event);
						}
					})
					.addClass('ui-icon ' + self.options.iconEdit + ' ui-helper-right')
					.click(function(event){
						$( this ).trigger('boom-tree.edit', event);
					})
					.prependTo( $item );
			}
			
			return self;
		},
		
		/**
		@function
		*/
		_set_remove : function( $item ) {
			
			var self = this;
			var re = /tree-edit/;
			
			if (self.options.showRemove === true || (self.options.showRemove === 'auto' && re.test(self.element[0].className)))
			{
				$('<span />', {
					title: 'Remove'
				})
					.bind('boom-tree.remove', function(event){

						if (self.options.onRemoveClick) {
							if (!self.options.onRemoveClick(event)) return;
						}

						$( this ).parent().fadeOut(function(){
							$( this ).remove();
						});
					})
					.addClass('ui-icon ' + self.options.iconRemove + ' ui-helper-right')
					.click(function(event){
						$( this ).trigger('boom-tree.remove', event);
					})
					.prependTo( $item );
			}
			
			return self;
		}, 
		
		/**
		@function
		*/
		_bind_events : function( $item ) {
			
			var self = this;
			
			var anchor = 
			$item
				.addClass('ui-helper-clearfix ui-state-default')
				.find('> a')
				.off('click hover')
				.on( 'click', function(event){
				
					var tag = this.href.match(/\/([0-9]+)$/);

					event.data = {
						//tag: this.href.match(/\/([0-9]+)$/)[1]
						rid: this.id.replace(/^(tag|page)_/, ''),
						tag: (tag !== null) ? tag[1] : ''
					};

					if (self.options.preventDefault) {

						event.preventDefault(); 

					}

					if (self.options.onClick) {
						
						self.options.onClick.call(this, event);
					}

					var anchor = $(this);

					if (anchor.hasClass(self.options.anchorActive) === true){

						self.amountChecked--;

						if (self.options.toggleSelected) {
							
							$.boom.log( 'removing active class' );

							anchor
								.removeClass(self.options.anchorActive)
								.siblings('.' + self.options.iconToggleChecked)
								.removeClass(self.options.iconToggleChecked)
								.addClass(self.options.iconDefault);
						}
					} else {

						if (self.amountChecked === self.options.maxSelected) {

							return;
						}

						self.amountChecked++;

						if (self.options.toggleSelected) {
							anchor
							.addClass(self.options.anchorActive)
							.siblings('.' + self.options.iconDefault)
							.removeClass(self.options.iconDefault)
							.addClass(self.options.iconToggleChecked);
						}
					}
				})
				.removeClass('ui-state-hover')
				.hover(
					function(){
						$(this).addClass('ui-state-hover');
					},
					function(){
						$(this).removeClass('ui-state-hover');
					}
				);
			
			return self;
		},
		
		/**
		@function
		*/
		_add_item : function( $item ) {
			
			var self = this;

			self._set_icon( $item );

			if ( !$item.find( '.boom-tree-hitarea' ).length) {

				self._set_toggle( $item );
				
			}

			if (!$item.find('.' + self.options.iconRemove).length) {

				self._set_edit( $item ); 
				self._set_remove( $item );

			}

			self._bind_events( $item );

			self.toggle($item, false);
			
			return self;
		},
		
		/**
		@function
		*/
		toggle: function( $item, toggle ) {
			
			var self = this;
			var children_ready = $.Deferred();
			var id = $item.find('> a').attr('rel');
			var childList = $item.find( '> ul' );
			
			if ( childList.length == 0 && self.options.onToggle) {
				
				//FIXME: hack to pass page IDs around
				self.options.onToggle
					.call(this, id)
					.done( function( data ){
						
						if ( $item.find( '> ul' ).length == 0 ) {
							$item
								.append( data.childList )
								.find( '> ul > li' ).each( function( i, child ){

									var $child = $( child );
									self
										._set_edit( $child )
										._set_remove( $child )
										._set_icon( $child );
									if ( $child.data( 'children' ) ) {
										self._set_toggle( $child );
										$child
											.find( '.boom-tree-hitarea' )
											.addClass( self.options.iconHitareaClosed );
									}
									self._bind_events( $child );
								})
								.end()
								.find( '> .boom-tree-hitarea' )
								.trigger( 'boom-tree.toggle' );
						}
						self._toggle( $item, toggle, $item.find( '> ul' ));
					});
			} else {
				self._toggle( $item, toggle, childList);
			}
			
			
		},

		/**
		@function
		*/
		_toggle : function( $item, toggle, childList ){
			toggle = (toggle === undefined) ? true : false;

			var hitarea = $item.find('> .boom-tree-hitarea'); 
			var id = $item.find('> a').attr('rel');

			if (toggle) {

				childList.toggle();

			} else if (this.options.useCookie) {

				var rel = $item.find('> a').attr('rel');

				if (rel && $.inArray(rel, this.cookieItems) !== -1) {

					childList.show();
				}
			}

			if (!childList.length) {

				hitarea.addClass(this.options.iconHitareaPlaceholder);

			} else if (childList.is(':hidden')) {

				if (this.options.useCookie) {

					$.boom.cookie.remove(id, this.options.cookieName);
				}

				hitarea
					.addClass(this.options.iconHitareaClosed)
					.removeClass(this.options.iconHitareaOpen);
				
			} else {

				if (this.options.useCookie) {

					$.boom.cookie.add(id, this.options.cookieName);
				}

				hitarea
					.addClass(this.options.iconHitareaOpen)
					.removeClass(this.options.iconHitareaClosed);
			}
		},

		/**
		@function
		*/
		add_item : function( item, parent ){
			
			var self = this;
			
			parent = (parent === undefined) ? self.element : parent;
			
			self._add_item( $( item ).appendTo( parent ) );
			
		},
		
		/**
		@function
		*/
		destroy : function(){

			$.Widget.prototype.destroy.apply(this, arguments);

			this.element
				.find('li')
					.each(function(){
						$(this).removeClass('ui-helper-clearfix ui-state-default')
						.find('.ui-icon').remove();
					})
				.end()
				.unwrap();
		}
	});

})( jQuery );


// jGrowl v1.2.9
(function($){var $ie6=(function(){return false===$.support.boxModel&&$.support.objectAll&&$support.leadingWhitespace;})();$.jGrowl=function(m,o){if($('#jGrowl').size()==0)
$('<div id="jGrowl"></div>').addClass((o&&o.position)?o.position:$.jGrowl.defaults.position).appendTo('body');$('#jGrowl').jGrowl(m,o);};$.fn.jGrowl=function(m,o){if($.isFunction(this.each)){var args=arguments;return this.each(function(){var self=this;if($(this).data('jGrowl.instance')==undefined){$(this).data('jGrowl.instance',$.extend(new $.fn.jGrowl(),{notifications:[],element:null,interval:null}));$(this).data('jGrowl.instance').startup(this);}
if($.isFunction($(this).data('jGrowl.instance')[m])){$(this).data('jGrowl.instance')[m].apply($(this).data('jGrowl.instance'),$.makeArray(args).slice(1));}else{$(this).data('jGrowl.instance').create(m,o);}});};};$.extend($.fn.jGrowl.prototype,{defaults:{pool:0,header:'',group:'',sticky:false,position:'top-right',glue:'after',theme:'default',themeState:'highlight',corners:'10px',check:250,life:3000,closeDuration:'normal',openDuration:'normal',easing:'swing',closer:true,closeTemplate:'&times;',closerTemplate:'<div>[ close all ]</div>',log:function(e,m,o){},beforeOpen:function(e,m,o){},afterOpen:function(e,m,o){},open:function(e,m,o){},beforeClose:function(e,m,o){},close:function(e,m,o){},animateOpen:{opacity:'show'},animateClose:{opacity:'hide'}},notifications:[],element:null,interval:null,create:function(message,o){var o=$.extend({},this.defaults,o);if(typeof o.speed!=='undefined'){o.openDuration=o.speed;o.closeDuration=o.speed;}
this.notifications.push({message:message,options:o});o.log.apply(this.element,[this.element,message,o]);},render:function(notification){var self=this;var message=notification.message;var o=notification.options;o.themeState=(o.themeState=='')?'':'ui-state-'+o.themeState;var notification=$('<div class="jGrowl-notification '+o.themeState+' ui-corner-all'+
((o.group!=undefined&&o.group!='')?' '+o.group:'')+'">'+'<div class="jGrowl-close">'+o.closeTemplate+'</div>'+'<div class="jGrowl-header">'+o.header+'</div>'+'<div class="jGrowl-message">'+message+'</div></div>').data("jGrowl",o).addClass(o.theme).children('div.jGrowl-close').bind("click.jGrowl",function(){$(this).parent().trigger('jGrowl.close');}).parent();$(notification).bind("mouseover.jGrowl",function(){$('div.jGrowl-notification',self.element).data("jGrowl.pause",true);}).bind("mouseout.jGrowl",function(){$('div.jGrowl-notification',self.element).data("jGrowl.pause",false);}).bind('jGrowl.beforeOpen',function(){if(o.beforeOpen.apply(notification,[notification,message,o,self.element])!=false){$(this).trigger('jGrowl.open');}}).bind('jGrowl.open',function(){if(o.open.apply(notification,[notification,message,o,self.element])!=false){if(o.glue=='after'){$('div.jGrowl-notification:last',self.element).after(notification);}else{$('div.jGrowl-notification:first',self.element).before(notification);}
$(this).animate(o.animateOpen,o.openDuration,o.easing,function(){if($.support.opacity===false)
this.style.removeAttribute('filter');if($(this).data("jGrowl")!=null)
$(this).data("jGrowl").created=new Date();$(this).trigger('jGrowl.afterOpen');});}}).bind('jGrowl.afterOpen',function(){o.afterOpen.apply(notification,[notification,message,o,self.element]);}).bind('jGrowl.beforeClose',function(){if(o.beforeClose.apply(notification,[notification,message,o,self.element])!=false)
$(this).trigger('jGrowl.close');}).bind('jGrowl.close',function(){$(this).data('jGrowl.pause',true);$(this).animate(o.animateClose,o.closeDuration,o.easing,function(){if($.isFunction(o.close)){if(o.close.apply(notification,[notification,message,o,self.element])!==false)
$(this).remove();}else{$(this).remove();}});}).trigger('jGrowl.beforeOpen');if(o.corners!=''&&$.fn.corner!=undefined)$(notification).corner(o.corners);if($('div.jGrowl-notification:parent',self.element).size()>1&&$('div.jGrowl-closer',self.element).size()==0&&this.defaults.closer!=false){$(this.defaults.closerTemplate).addClass('jGrowl-closer '+this.defaults.themeState+' ui-corner-all').addClass(this.defaults.theme).appendTo(self.element).animate(this.defaults.animateOpen,this.defaults.speed,this.defaults.easing).bind("click.jGrowl",function(){$(this).siblings().trigger("jGrowl.beforeClose");if($.isFunction(self.defaults.closer)){self.defaults.closer.apply($(this).parent()[0],[$(this).parent()[0]]);}});};},update:function(){$(this.element).find('div.jGrowl-notification:parent').each(function(){if($(this).data("jGrowl")!=undefined&&$(this).data("jGrowl").created!=undefined&&($(this).data("jGrowl").created.getTime()+parseInt($(this).data("jGrowl").life))<(new Date()).getTime()&&$(this).data("jGrowl").sticky!=true&&($(this).data("jGrowl.pause")==undefined||$(this).data("jGrowl.pause")!=true)){$(this).trigger('jGrowl.beforeClose');}});if(this.notifications.length>0&&(this.defaults.pool==0||$(this.element).find('div.jGrowl-notification:parent').size()<this.defaults.pool))
this.render(this.notifications.shift());if($(this.element).find('div.jGrowl-notification:parent').size()<2){$(this.element).find('div.jGrowl-closer').animate(this.defaults.animateClose,this.defaults.speed,this.defaults.easing,function(){$(this).remove();});}},startup:function(e){this.element=$(e).addClass('jGrowl').append('<div class="jGrowl-notification"></div>');this.interval=setInterval(function(){$(e).data('jGrowl.instance').update();},parseInt(this.defaults.check));if($ie6){$(this.element).addClass('ie6');}},shutdown:function(){$(this.element).removeClass('jGrowl').find('div.jGrowl-notification').remove();clearInterval(this.interval);},close:function(){$(this.element).find('div.jGrowl-notification').each(function(){$(this).trigger('jGrowl.beforeClose');});}});$.jGrowl.defaults=$.fn.jGrowl.prototype.defaults;})(jQuery);

(function($){$.extend({tablesorter:new
function(){var parsers=[],widgets=[];this.defaults={cssHeader:"header",cssAsc:"headerSortUp",cssDesc:"headerSortDown",cssChildRow:"expand-child",sortInitialOrder:"asc",sortMultiSortKey:"shiftKey",sortForce:null,sortAppend:null,sortLocaleCompare:true,textExtraction:"simple",parsers:{},widgets:[],widgetZebra:{css:["even","odd"]},headers:{},widthFixed:false,cancelSelection:true,sortList:[],headerList:[],dateFormat:"us",decimal:'/\.|\,/g',onRenderHeader:null,selectorHeaders:'thead th',debug:false};function benchmark(s,d){log(s+","+(new Date().getTime()-d.getTime())+"ms");}this.benchmark=benchmark;function log(s){if(typeof console!="undefined"&&typeof console.debug!="undefined"){console.log(s);}else{alert(s);}}function buildParserCache(table,$headers){if(table.config.debug){var parsersDebug="";}if(table.tBodies.length==0)return;var rows=table.tBodies[0].rows;if(rows[0]){var list=[],cells=rows[0].cells,l=cells.length;for(var i=0;i<l;i++){var p=false;if($.metadata&&($($headers[i]).metadata()&&$($headers[i]).metadata().sorter)){p=getParserById($($headers[i]).metadata().sorter);}else if((table.config.headers[i]&&table.config.headers[i].sorter)){p=getParserById(table.config.headers[i].sorter);}if(!p){p=detectParserForColumn(table,rows,-1,i);}if(table.config.debug){parsersDebug+="column:"+i+" parser:"+p.id+"\n";}list.push(p);}}if(table.config.debug){log(parsersDebug);}return list;};function detectParserForColumn(table,rows,rowIndex,cellIndex){var l=parsers.length,node=false,nodeValue=false,keepLooking=true;while(nodeValue==''&&keepLooking){rowIndex++;if(rows[rowIndex]){node=getNodeFromRowAndCellIndex(rows,rowIndex,cellIndex);nodeValue=trimAndGetNodeText(table.config,node);if(table.config.debug){log('Checking if value was empty on row:'+rowIndex);}}else{keepLooking=false;}}for(var i=1;i<l;i++){if(parsers[i].is(nodeValue,table,node)){return parsers[i];}}return parsers[0];}function getNodeFromRowAndCellIndex(rows,rowIndex,cellIndex){return rows[rowIndex].cells[cellIndex];}function trimAndGetNodeText(config,node){return $.trim(getElementText(config,node));}function getParserById(name){var l=parsers.length;for(var i=0;i<l;i++){if(parsers[i].id.toLowerCase()==name.toLowerCase()){return parsers[i];}}return false;}function buildCache(table){if(table.config.debug){var cacheTime=new Date();}var totalRows=(table.tBodies[0]&&table.tBodies[0].rows.length)||0,totalCells=(table.tBodies[0].rows[0]&&table.tBodies[0].rows[0].cells.length)||0,parsers=table.config.parsers,cache={row:[],normalized:[]};for(var i=0;i<totalRows;++i){var c=$(table.tBodies[0].rows[i]),cols=[];if(c.hasClass(table.config.cssChildRow)){cache.row[cache.row.length-1]=cache.row[cache.row.length-1].add(c);continue;}cache.row.push(c);for(var j=0;j<totalCells;++j){cols.push(parsers[j].format(getElementText(table.config,c[0].cells[j]),table,c[0].cells[j]));}cols.push(cache.normalized.length);cache.normalized.push(cols);cols=null;};if(table.config.debug){benchmark("Building cache for "+totalRows+" rows:",cacheTime);}return cache;};function getElementText(config,node){var text="";if(!node)return"";if(!config.supportsTextContent)config.supportsTextContent=node.textContent||false;if(config.textExtraction=="simple"){if(config.supportsTextContent){text=node.textContent;}else{if(node.childNodes[0]&&node.childNodes[0].hasChildNodes()){text=node.childNodes[0].innerHTML;}else{text=node.innerHTML;}}}else{if(typeof(config.textExtraction)=="function"){text=config.textExtraction(node);}else{text=$(node).text();}}return text;}function appendToTable(table,cache){if(table.config.debug){var appendTime=new Date()}var c=cache,r=c.row,n=c.normalized,totalRows=n.length,checkCell=(n[0].length-1),tableBody=$(table.tBodies[0]),rows=[];for(var i=0;i<totalRows;i++){var pos=n[i][checkCell];rows.push(r[pos]);if(!table.config.appender){var l=r[pos].length;for(var j=0;j<l;j++){tableBody[0].appendChild(r[pos][j]);}}}if(table.config.appender){table.config.appender(table,rows);}rows=null;if(table.config.debug){benchmark("Rebuilt table:",appendTime);}applyWidget(table);setTimeout(function(){$(table).trigger("sortEnd");},0);};function buildHeaders(table){if(table.config.debug){var time=new Date();}var meta=($.metadata)?true:false;var header_index=computeTableHeaderCellIndexes(table);$tableHeaders=$(table.config.selectorHeaders,table).each(function(index){this.column=header_index[this.parentNode.rowIndex+"-"+this.cellIndex];this.order=formatSortingOrder(table.config.sortInitialOrder);this.count=this.order;if(checkHeaderMetadata(this)||checkHeaderOptions(table,index))this.sortDisabled=true;if(checkHeaderOptionsSortingLocked(table,index))this.order=this.lockedOrder=checkHeaderOptionsSortingLocked(table,index);if(!this.sortDisabled){var $th=$(this).addClass(table.config.cssHeader);if(table.config.onRenderHeader)table.config.onRenderHeader.apply($th);}table.config.headerList[index]=this;});if(table.config.debug){benchmark("Built headers:",time);log($tableHeaders);}return $tableHeaders;};function computeTableHeaderCellIndexes(t){var matrix=[];var lookup={};var thead=t.getElementsByTagName('THEAD')[0];var trs=thead.getElementsByTagName('TR');for(var i=0;i<trs.length;i++){var cells=trs[i].cells;for(var j=0;j<cells.length;j++){var c=cells[j];var rowIndex=c.parentNode.rowIndex;var cellId=rowIndex+"-"+c.cellIndex;var rowSpan=c.rowSpan||1;var colSpan=c.colSpan||1
var firstAvailCol;if(typeof(matrix[rowIndex])=="undefined"){matrix[rowIndex]=[];}for(var k=0;k<matrix[rowIndex].length+1;k++){if(typeof(matrix[rowIndex][k])=="undefined"){firstAvailCol=k;break;}}lookup[cellId]=firstAvailCol;for(var k=rowIndex;k<rowIndex+rowSpan;k++){if(typeof(matrix[k])=="undefined"){matrix[k]=[];}var matrixrow=matrix[k];for(var l=firstAvailCol;l<firstAvailCol+colSpan;l++){matrixrow[l]="x";}}}}return lookup;}function checkCellColSpan(table,rows,row){var arr=[],r=table.tHead.rows,c=r[row].cells;for(var i=0;i<c.length;i++){var cell=c[i];if(cell.colSpan>1){arr=arr.concat(checkCellColSpan(table,headerArr,row++));}else{if(table.tHead.length==1||(cell.rowSpan>1||!r[row+1])){arr.push(cell);}}}return arr;};function checkHeaderMetadata(cell){if(($.metadata)&&($(cell).metadata().sorter===false)){return true;};return false;}function checkHeaderOptions(table,i){if((table.config.headers[i])&&(table.config.headers[i].sorter===false)){return true;};return false;}function checkHeaderOptionsSortingLocked(table,i){if((table.config.headers[i])&&(table.config.headers[i].lockedOrder))return table.config.headers[i].lockedOrder;return false;}function applyWidget(table){var c=table.config.widgets;var l=c.length;for(var i=0;i<l;i++){getWidgetById(c[i]).format(table);}}function getWidgetById(name){var l=widgets.length;for(var i=0;i<l;i++){if(widgets[i].id.toLowerCase()==name.toLowerCase()){return widgets[i];}}};function formatSortingOrder(v){if(typeof(v)!="Number"){return(v.toLowerCase()=="desc")?1:0;}else{return(v==1)?1:0;}}function isValueInArray(v,a){var l=a.length;for(var i=0;i<l;i++){if(a[i][0]==v){return true;}}return false;}function setHeadersCss(table,$headers,list,css){$headers.removeClass(css[0]).removeClass(css[1]);var h=[];$headers.each(function(offset){if(!this.sortDisabled){h[this.column]=$(this);}});var l=list.length;for(var i=0;i<l;i++){h[list[i][0]].addClass(css[list[i][1]]);}}function fixColumnWidth(table,$headers){var c=table.config;if(c.widthFixed){var colgroup=$('<colgroup>');$("tr:first td",table.tBodies[0]).each(function(){colgroup.append($('<col>').css('width',$(this).width()));});$(table).prepend(colgroup);};}function updateHeaderSortCount(table,sortList){var c=table.config,l=sortList.length;for(var i=0;i<l;i++){var s=sortList[i],o=c.headerList[s[0]];o.count=s[1];o.count++;}}function multisort(table,sortList,cache){if(table.config.debug){var sortTime=new Date();}var dynamicExp="var sortWrapper = function(a,b) {",l=sortList.length;for(var i=0;i<l;i++){var c=sortList[i][0];var order=sortList[i][1];var s=(table.config.parsers[c].type=="text")?((order==0)?makeSortFunction("text","asc",c):makeSortFunction("text","desc",c)):((order==0)?makeSortFunction("numeric","asc",c):makeSortFunction("numeric","desc",c));var e="e"+i;dynamicExp+="var "+e+" = "+s;dynamicExp+="if("+e+") { return "+e+"; } ";dynamicExp+="else { ";}var orgOrderCol=cache.normalized[0].length-1;dynamicExp+="return a["+orgOrderCol+"]-b["+orgOrderCol+"];";for(var i=0;i<l;i++){dynamicExp+="}; ";}dynamicExp+="return 0; ";dynamicExp+="}; ";if(table.config.debug){benchmark("Evaling expression:"+dynamicExp,new Date());}eval(dynamicExp);cache.normalized.sort(sortWrapper);if(table.config.debug){benchmark("Sorting on "+sortList.toString()+" and dir "+order+" time:",sortTime);}return cache;};function makeSortFunction(type,direction,index){var a="a["+index+"]",b="b["+index+"]";if(type=='text'&&direction=='asc'){return"("+a+" == "+b+" ? 0 : ("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : ("+a+" < "+b+") ? -1 : 1 )));";}else if(type=='text'&&direction=='desc'){return"("+a+" == "+b+" ? 0 : ("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : ("+b+" < "+a+") ? -1 : 1 )));";}else if(type=='numeric'&&direction=='asc'){return"("+a+" === null && "+b+" === null) ? 0 :("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : "+a+" - "+b+"));";}else if(type=='numeric'&&direction=='desc'){return"("+a+" === null && "+b+" === null) ? 0 :("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : "+b+" - "+a+"));";}};function makeSortText(i){return"((a["+i+"] < b["+i+"]) ? -1 : ((a["+i+"] > b["+i+"]) ? 1 : 0));";};function makeSortTextDesc(i){return"((b["+i+"] < a["+i+"]) ? -1 : ((b["+i+"] > a["+i+"]) ? 1 : 0));";};function makeSortNumeric(i){return"a["+i+"]-b["+i+"];";};function makeSortNumericDesc(i){return"b["+i+"]-a["+i+"];";};function sortText(a,b){if(table.config.sortLocaleCompare)return a.localeCompare(b);return((a<b)?-1:((a>b)?1:0));};function sortTextDesc(a,b){if(table.config.sortLocaleCompare)return b.localeCompare(a);return((b<a)?-1:((b>a)?1:0));};function sortNumeric(a,b){return a-b;};function sortNumericDesc(a,b){return b-a;};function getCachedSortType(parsers,i){return parsers[i].type;};this.construct=function(settings){return this.each(function(){if(!this.tHead||!this.tBodies)return;var $this,$document,$headers,cache,config,shiftDown=0,sortOrder;this.config={};config=$.extend(this.config,$.tablesorter.defaults,settings);$this=$(this);$.data(this,"tablesorter",config);$headers=buildHeaders(this);this.config.parsers=buildParserCache(this,$headers);cache=buildCache(this);var sortCSS=[config.cssDesc,config.cssAsc];fixColumnWidth(this);$headers.click(function(e){var totalRows=($this[0].tBodies[0]&&$this[0].tBodies[0].rows.length)||0;if(!this.sortDisabled&&totalRows>0){$this.trigger("sortStart");var $cell=$(this);var i=this.column;this.order=this.count++%2;if(this.lockedOrder)this.order=this.lockedOrder;if(!e[config.sortMultiSortKey]){config.sortList=[];if(config.sortForce!=null){var a=config.sortForce;for(var j=0;j<a.length;j++){if(a[j][0]!=i){config.sortList.push(a[j]);}}}config.sortList.push([i,this.order]);}else{if(isValueInArray(i,config.sortList)){for(var j=0;j<config.sortList.length;j++){var s=config.sortList[j],o=config.headerList[s[0]];if(s[0]==i){o.count=s[1];o.count++;s[1]=o.count%2;}}}else{config.sortList.push([i,this.order]);}};setTimeout(function(){setHeadersCss($this[0],$headers,config.sortList,sortCSS);appendToTable($this[0],multisort($this[0],config.sortList,cache));},1);return false;}}).mousedown(function(){if(config.cancelSelection){this.onselectstart=function(){return false};return false;}});$this.bind("update",function(){var me=this;setTimeout(function(){me.config.parsers=buildParserCache(me,$headers);cache=buildCache(me);},1);}).bind("updateCell",function(e,cell){var config=this.config;var pos=[(cell.parentNode.rowIndex-1),cell.cellIndex];cache.normalized[pos[0]][pos[1]]=config.parsers[pos[1]].format(getElementText(config,cell),cell);}).bind("sorton",function(e,list){$(this).trigger("sortStart");config.sortList=list;var sortList=config.sortList;updateHeaderSortCount(this,sortList);setHeadersCss(this,$headers,sortList,sortCSS);appendToTable(this,multisort(this,sortList,cache));}).bind("appendCache",function(){appendToTable(this,cache);}).bind("applyWidgetId",function(e,id){getWidgetById(id).format(this);}).bind("applyWidgets",function(){applyWidget(this);});if($.metadata&&($(this).metadata()&&$(this).metadata().sortlist)){config.sortList=$(this).metadata().sortlist;}if(config.sortList.length>0){$this.trigger("sorton",[config.sortList]);}applyWidget(this);});};this.addParser=function(parser){var l=parsers.length,a=true;for(var i=0;i<l;i++){if(parsers[i].id.toLowerCase()==parser.id.toLowerCase()){a=false;}}if(a){parsers.push(parser);};};this.addWidget=function(widget){widgets.push(widget);};this.formatFloat=function(s){var i=parseFloat(s);return(isNaN(i))?0:i;};this.formatInt=function(s){var i=parseInt(s);return(isNaN(i))?0:i;};this.isDigit=function(s,config){return/^[-+]?\d*$/.test($.trim(s.replace(/[,.']/g,'')));};this.clearTableBody=function(table){if($.browser.msie){function empty(){while(this.firstChild)this.removeChild(this.firstChild);}empty.apply(table.tBodies[0]);}else{table.tBodies[0].innerHTML="";}};}});$.fn.extend({tablesorter:$.tablesorter.construct});var ts=$.tablesorter;ts.addParser({id:"text",is:function(s){return true;},format:function(s){return $.trim(s.toLocaleLowerCase());},type:"text"});ts.addParser({id:"digit",is:function(s,table){var c=table.config;return $.tablesorter.isDigit(s,c);},format:function(s){return $.tablesorter.formatFloat(s);},type:"numeric"});ts.addParser({id:"currency",is:function(s){return/^[£$€?.]/.test(s);},format:function(s){return $.tablesorter.formatFloat(s.replace(new RegExp(/[£$€]/g),""));},type:"numeric"});ts.addParser({id:"ipAddress",is:function(s){return/^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(s);},format:function(s){var a=s.split("."),r="",l=a.length;for(var i=0;i<l;i++){var item=a[i];if(item.length==2){r+="0"+item;}else{r+=item;}}return $.tablesorter.formatFloat(r);},type:"numeric"});ts.addParser({id:"url",is:function(s){return/^(https?|ftp|file):\/\/$/.test(s);},format:function(s){return jQuery.trim(s.replace(new RegExp(/(https?|ftp|file):\/\//),''));},type:"text"});ts.addParser({id:"isoDate",is:function(s){return/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(s);},format:function(s){return $.tablesorter.formatFloat((s!="")?new Date(s.replace(new RegExp(/-/g),"/")).getTime():"0");},type:"numeric"});ts.addParser({id:"percent",is:function(s){return/\%$/.test($.trim(s));},format:function(s){return $.tablesorter.formatFloat(s.replace(new RegExp(/%/g),""));},type:"numeric"});ts.addParser({id:"usLongDate",is:function(s){return s.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/));},format:function(s){return $.tablesorter.formatFloat(new Date(s).getTime());},type:"numeric"});ts.addParser({id:"shortDate",is:function(s){return/\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(s);},format:function(s,table){var c=table.config;s=s.replace(/\-/g,"/");if(c.dateFormat=="us"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/,"$3/$1/$2");}else if(c.dateFormat=="uk"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/,"$3/$2/$1");}else if(c.dateFormat=="dd/mm/yy"||c.dateFormat=="dd-mm-yy"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/,"$1/$2/$3");}return $.tablesorter.formatFloat(new Date(s).getTime());},type:"numeric"});ts.addParser({id:"time",is:function(s){return/^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(s);},format:function(s){return $.tablesorter.formatFloat(new Date("2000/01/01 "+s).getTime());},type:"numeric"});ts.addParser({id:"metadata",is:function(s){return false;},format:function(s,table,cell){var c=table.config,p=(!c.parserMetadataName)?'sortValue':c.parserMetadataName;return $(cell).metadata()[p];},type:"numeric"});ts.addWidget({id:"zebra",format:function(table){if(table.config.debug){var time=new Date();}var $tr,row=-1,odd;$("tr:visible",table.tBodies[0]).each(function(i){$tr=$(this);if(!$tr.hasClass(table.config.cssChildRow))row++;odd=(row%2==0);$tr.removeClass(table.config.widgetZebra.css[odd?0:1]).addClass(table.config.widgetZebra.css[odd?1:0])});if(table.config.debug){$.tablesorter.benchmark("Applying Zebra widget",time);}}});})(jQuery);

// jquery timepicker http://trentrichardson.com/examples/timepicker/
(function($){$.ui.timepicker=$.ui.timepicker||{};if($.ui.timepicker.version){return;}
$.extend($.ui,{timepicker:{version:"1.1.2"}});function Timepicker(){this.regional=[];this.regional['']={currentText:'Now',closeText:'Done',amNames:['AM','A'],pmNames:['PM','P'],timeFormat:'HH:mm',timeSuffix:'',timeOnlyTitle:'Choose Time',timeText:'Time',hourText:'Hour',minuteText:'Minute',secondText:'Second',millisecText:'Millisecond',timezoneText:'Time Zone',isRTL:false};this._defaults={showButtonPanel:true,timeOnly:false,showHour:true,showMinute:true,showSecond:false,showMillisec:false,showTimezone:false,showTime:true,stepHour:1,stepMinute:1,stepSecond:1,stepMillisec:1,hour:0,minute:0,second:0,millisec:0,timezone:null,useLocalTimezone:false,defaultTimezone:"+0000",hourMin:0,minuteMin:0,secondMin:0,millisecMin:0,hourMax:23,minuteMax:59,secondMax:59,millisecMax:999,minDateTime:null,maxDateTime:null,onSelect:null,hourGrid:0,minuteGrid:0,secondGrid:0,millisecGrid:0,alwaysSetTime:true,separator:' ',altFieldTimeOnly:true,altTimeFormat:null,altSeparator:null,altTimeSuffix:null,pickerTimeFormat:null,pickerTimeSuffix:null,showTimepicker:true,timezoneIso8601:false,timezoneList:null,addSliderAccess:false,sliderAccessArgs:null,controlType:'slider',defaultValue:null,parse:'strict'};$.extend(this._defaults,this.regional['']);}
$.extend(Timepicker.prototype,{$input:null,$altInput:null,$timeObj:null,inst:null,hour_slider:null,minute_slider:null,second_slider:null,millisec_slider:null,timezone_select:null,hour:0,minute:0,second:0,millisec:0,timezone:null,defaultTimezone:"+0000",hourMinOriginal:null,minuteMinOriginal:null,secondMinOriginal:null,millisecMinOriginal:null,hourMaxOriginal:null,minuteMaxOriginal:null,secondMaxOriginal:null,millisecMaxOriginal:null,ampm:'',formattedDate:'',formattedTime:'',formattedDateTime:'',timezoneList:null,units:['hour','minute','second','millisec'],control:null,setDefaults:function(settings){extendRemove(this._defaults,settings||{});return this;},_newInst:function($input,o){var tp_inst=new Timepicker(),inlineSettings={},fns={},overrides,i;for(var attrName in this._defaults){if(this._defaults.hasOwnProperty(attrName)){var attrValue=$input.attr('time:'+attrName);if(attrValue){try{inlineSettings[attrName]=eval(attrValue);}catch(err){inlineSettings[attrName]=attrValue;}}}}
overrides={beforeShow:function(input,dp_inst){if($.isFunction(tp_inst._defaults.evnts.beforeShow)){return tp_inst._defaults.evnts.beforeShow.call($input[0],input,dp_inst,tp_inst);}},onChangeMonthYear:function(year,month,dp_inst){tp_inst._updateDateTime(dp_inst);if($.isFunction(tp_inst._defaults.evnts.onChangeMonthYear)){tp_inst._defaults.evnts.onChangeMonthYear.call($input[0],year,month,dp_inst,tp_inst);}},onClose:function(dateText,dp_inst){if(tp_inst.timeDefined===true&&$input.val()!==''){tp_inst._updateDateTime(dp_inst);}
if($.isFunction(tp_inst._defaults.evnts.onClose)){tp_inst._defaults.evnts.onClose.call($input[0],dateText,dp_inst,tp_inst);}}};for(i in overrides){if(overrides.hasOwnProperty(i)){fns[i]=o[i]||null;}}
tp_inst._defaults=$.extend({},this._defaults,inlineSettings,o,overrides,{evnts:fns,timepicker:tp_inst});tp_inst.amNames=$.map(tp_inst._defaults.amNames,function(val){return val.toUpperCase();});tp_inst.pmNames=$.map(tp_inst._defaults.pmNames,function(val){return val.toUpperCase();});if(typeof(tp_inst._defaults.controlType)==='string'){if($.fn[tp_inst._defaults.controlType]===undefined){tp_inst._defaults.controlType='select';}
tp_inst.control=tp_inst._controls[tp_inst._defaults.controlType];}
else{tp_inst.control=tp_inst._defaults.controlType;}
if(tp_inst._defaults.timezoneList===null){var timezoneList=['-1200','-1100','-1000','-0930','-0900','-0800','-0700','-0600','-0500','-0430','-0400','-0330','-0300','-0200','-0100','+0000','+0100','+0200','+0300','+0330','+0400','+0430','+0500','+0530','+0545','+0600','+0630','+0700','+0800','+0845','+0900','+0930','+1000','+1030','+1100','+1130','+1200','+1245','+1300','+1400'];if(tp_inst._defaults.timezoneIso8601){timezoneList=$.map(timezoneList,function(val){return val=='+0000'?'Z':(val.substring(0,3)+':'+val.substring(3));});}
tp_inst._defaults.timezoneList=timezoneList;}
tp_inst.timezone=tp_inst._defaults.timezone;tp_inst.hour=tp_inst._defaults.hour<tp_inst._defaults.hourMin?tp_inst._defaults.hourMin:tp_inst._defaults.hour>tp_inst._defaults.hourMax?tp_inst._defaults.hourMax:tp_inst._defaults.hour;tp_inst.minute=tp_inst._defaults.minute<tp_inst._defaults.minuteMin?tp_inst._defaults.minuteMin:tp_inst._defaults.minute>tp_inst._defaults.minuteMax?tp_inst._defaults.minuteMax:tp_inst._defaults.minute;tp_inst.second=tp_inst._defaults.second<tp_inst._defaults.secondMin?tp_inst._defaults.secondMin:tp_inst._defaults.second>tp_inst._defaults.secondMax?tp_inst._defaults.secondMax:tp_inst._defaults.second;tp_inst.millisec=tp_inst._defaults.millisec<tp_inst._defaults.millisecMin?tp_inst._defaults.millisecMin:tp_inst._defaults.millisec>tp_inst._defaults.millisecMax?tp_inst._defaults.millisecMax:tp_inst._defaults.millisec;tp_inst.ampm='';tp_inst.$input=$input;if(o.altField){tp_inst.$altInput=$(o.altField).css({cursor:'pointer'}).focus(function(){$input.trigger("focus");});}
if(tp_inst._defaults.minDate===0||tp_inst._defaults.minDateTime===0){tp_inst._defaults.minDate=new Date();}
if(tp_inst._defaults.maxDate===0||tp_inst._defaults.maxDateTime===0){tp_inst._defaults.maxDate=new Date();}
if(tp_inst._defaults.minDate!==undefined&&tp_inst._defaults.minDate instanceof Date){tp_inst._defaults.minDateTime=new Date(tp_inst._defaults.minDate.getTime());}
if(tp_inst._defaults.minDateTime!==undefined&&tp_inst._defaults.minDateTime instanceof Date){tp_inst._defaults.minDate=new Date(tp_inst._defaults.minDateTime.getTime());}
if(tp_inst._defaults.maxDate!==undefined&&tp_inst._defaults.maxDate instanceof Date){tp_inst._defaults.maxDateTime=new Date(tp_inst._defaults.maxDate.getTime());}
if(tp_inst._defaults.maxDateTime!==undefined&&tp_inst._defaults.maxDateTime instanceof Date){tp_inst._defaults.maxDate=new Date(tp_inst._defaults.maxDateTime.getTime());}
tp_inst.$input.bind('focus',function(){tp_inst._onFocus();});return tp_inst;},_addTimePicker:function(dp_inst){var currDT=(this.$altInput&&this._defaults.altFieldTimeOnly)?this.$input.val()+' '+this.$altInput.val():this.$input.val();this.timeDefined=this._parseTime(currDT);this._limitMinMaxDateTime(dp_inst,false);this._injectTimePicker();},_parseTime:function(timeString,withDate){if(!this.inst){this.inst=$.datepicker._getInst(this.$input[0]);}
if(withDate||!this._defaults.timeOnly){var dp_dateFormat=$.datepicker._get(this.inst,'dateFormat');try{var parseRes=parseDateTimeInternal(dp_dateFormat,this._defaults.timeFormat,timeString,$.datepicker._getFormatConfig(this.inst),this._defaults);if(!parseRes.timeObj){return false;}
$.extend(this,parseRes.timeObj);}catch(err){$.datepicker.log("Error parsing the date/time string: "+err+"\ndate/time string = "+timeString+"\ntimeFormat = "+this._defaults.timeFormat+"\ndateFormat = "+dp_dateFormat);return false;}
return true;}else{var timeObj=$.datepicker.parseTime(this._defaults.timeFormat,timeString,this._defaults);if(!timeObj){return false;}
$.extend(this,timeObj);return true;}},_injectTimePicker:function(){var $dp=this.inst.dpDiv,o=this.inst.settings,tp_inst=this,litem='',uitem='',max={},gridSize={},size=null;if($dp.find("div.ui-timepicker-div").length===0&&o.showTimepicker){var noDisplay=' style="display:none;"',html='<div class="ui-timepicker-div'+(o.isRTL?' ui-timepicker-rtl':'')+'"><dl>'+'<dt class="ui_tpicker_time_label"'+((o.showTime)?'':noDisplay)+'>'+o.timeText+'</dt>'+'<dd class="ui_tpicker_time"'+((o.showTime)?'':noDisplay)+'></dd>';for(var i=0,l=this.units.length;i<l;i++){litem=this.units[i];uitem=litem.substr(0,1).toUpperCase()+litem.substr(1);max[litem]=parseInt((o[litem+'Max']-((o[litem+'Max']-o[litem+'Min'])%o['step'+uitem])),10);gridSize[litem]=0;html+='<dt class="ui_tpicker_'+litem+'_label"'+((o['show'+uitem])?'':noDisplay)+'>'+o[litem+'Text']+'</dt>'+'<dd class="ui_tpicker_'+litem+'"><div class="ui_tpicker_'+litem+'_slider"'+((o['show'+uitem])?'':noDisplay)+'></div>';if(o['show'+uitem]&&o[litem+'Grid']>0){html+='<div style="padding-left: 1px"><table class="ui-tpicker-grid-label"><tr>';if(litem=='hour'){for(var h=o[litem+'Min'];h<=max[litem];h+=parseInt(o[litem+'Grid'],10)){gridSize[litem]++;var tmph=$.datepicker.formatTime(useAmpm(o.pickerTimeFormat||o.timeFormat)?'hht':'HH',{hour:h},o);html+='<td data-for="'+litem+'">'+tmph+'</td>';}}
else{for(var m=o[litem+'Min'];m<=max[litem];m+=parseInt(o[litem+'Grid'],10)){gridSize[litem]++;html+='<td data-for="'+litem+'">'+((m<10)?'0':'')+m+'</td>';}}
html+='</tr></table></div>';}
html+='</dd>';}
html+='<dt class="ui_tpicker_timezone_label"'+((o.showTimezone)?'':noDisplay)+'>'+o.timezoneText+'</dt>';html+='<dd class="ui_tpicker_timezone" '+((o.showTimezone)?'':noDisplay)+'></dd>';html+='</dl></div>';var $tp=$(html);if(o.timeOnly===true){$tp.prepend('<div class="ui-widget-header ui-helper-clearfix ui-corner-all">'+'<div class="ui-datepicker-title">'+o.timeOnlyTitle+'</div>'+'</div>');$dp.find('.ui-datepicker-header, .ui-datepicker-calendar').hide();}
for(var i=0,l=tp_inst.units.length;i<l;i++){litem=tp_inst.units[i];uitem=litem.substr(0,1).toUpperCase()+litem.substr(1);tp_inst[litem+'_slider']=tp_inst.control.create(tp_inst,$tp.find('.ui_tpicker_'+litem+'_slider'),litem,tp_inst[litem],o[litem+'Min'],max[litem],o['step'+uitem]);if(o['show'+uitem]&&o[litem+'Grid']>0){size=100*gridSize[litem]*o[litem+'Grid']/(max[litem]-o[litem+'Min']);$tp.find('.ui_tpicker_'+litem+' table').css({width:size+"%",marginLeft:o.isRTL?'0':((size/(-2*gridSize[litem]))+"%"),marginRight:o.isRTL?((size/(-2*gridSize[litem]))+"%"):'0',borderCollapse:'collapse'}).find("td").click(function(e){var $t=$(this),h=$t.html(),n=parseInt(h.replace(/[^0-9]/g),10),ap=h.replace(/[^apm]/ig),f=$t.data('for');if(f=='hour'){if(ap.indexOf('p')!==-1&&n<12){n+=12;}
else{if(ap.indexOf('a')!==-1&&n===12){n=0;}}}
tp_inst.control.value(tp_inst,tp_inst[f+'_slider'],litem,n);tp_inst._onTimeChange();tp_inst._onSelectHandler();}).css({cursor:'pointer',width:(100/gridSize[litem])+'%',textAlign:'center',overflow:'hidden'});}}
this.timezone_select=$tp.find('.ui_tpicker_timezone').append('<select></select>').find("select");$.fn.append.apply(this.timezone_select,$.map(o.timezoneList,function(val,idx){return $("<option />").val(typeof val=="object"?val.value:val).text(typeof val=="object"?val.label:val);}));if(typeof(this.timezone)!="undefined"&&this.timezone!==null&&this.timezone!==""){var local_date=new Date(this.inst.selectedYear,this.inst.selectedMonth,this.inst.selectedDay,12);var local_timezone=$.timepicker.timeZoneOffsetString(local_date);if(local_timezone==this.timezone){selectLocalTimeZone(tp_inst);}else{this.timezone_select.val(this.timezone);}}else{if(typeof(this.hour)!="undefined"&&this.hour!==null&&this.hour!==""){this.timezone_select.val(o.defaultTimezone);}else{selectLocalTimeZone(tp_inst);}}
this.timezone_select.change(function(){tp_inst._defaults.useLocalTimezone=false;tp_inst._onTimeChange();tp_inst._onSelectHandler();});var $buttonPanel=$dp.find('.ui-datepicker-buttonpane');if($buttonPanel.length){$buttonPanel.before($tp);}else{$dp.append($tp);}
this.$timeObj=$tp.find('.ui_tpicker_time');if(this.inst!==null){var timeDefined=this.timeDefined;this._onTimeChange();this.timeDefined=timeDefined;}
if(this._defaults.addSliderAccess){var sliderAccessArgs=this._defaults.sliderAccessArgs,rtl=this._defaults.isRTL;sliderAccessArgs.isRTL=rtl;setTimeout(function(){if($tp.find('.ui-slider-access').length===0){$tp.find('.ui-slider:visible').sliderAccess(sliderAccessArgs);var sliderAccessWidth=$tp.find('.ui-slider-access:eq(0)').outerWidth(true);if(sliderAccessWidth){$tp.find('table:visible').each(function(){var $g=$(this),oldWidth=$g.outerWidth(),oldMarginLeft=$g.css(rtl?'marginRight':'marginLeft').toString().replace('%',''),newWidth=oldWidth-sliderAccessWidth,newMarginLeft=((oldMarginLeft*newWidth)/oldWidth)+'%',css={width:newWidth,marginRight:0,marginLeft:0};css[rtl?'marginRight':'marginLeft']=newMarginLeft;$g.css(css);});}}},10);}}},_limitMinMaxDateTime:function(dp_inst,adjustSliders){var o=this._defaults,dp_date=new Date(dp_inst.selectedYear,dp_inst.selectedMonth,dp_inst.selectedDay);if(!this._defaults.showTimepicker){return;}
if($.datepicker._get(dp_inst,'minDateTime')!==null&&$.datepicker._get(dp_inst,'minDateTime')!==undefined&&dp_date){var minDateTime=$.datepicker._get(dp_inst,'minDateTime'),minDateTimeDate=new Date(minDateTime.getFullYear(),minDateTime.getMonth(),minDateTime.getDate(),0,0,0,0);if(this.hourMinOriginal===null||this.minuteMinOriginal===null||this.secondMinOriginal===null||this.millisecMinOriginal===null){this.hourMinOriginal=o.hourMin;this.minuteMinOriginal=o.minuteMin;this.secondMinOriginal=o.secondMin;this.millisecMinOriginal=o.millisecMin;}
if(dp_inst.settings.timeOnly||minDateTimeDate.getTime()==dp_date.getTime()){this._defaults.hourMin=minDateTime.getHours();if(this.hour<=this._defaults.hourMin){this.hour=this._defaults.hourMin;this._defaults.minuteMin=minDateTime.getMinutes();if(this.minute<=this._defaults.minuteMin){this.minute=this._defaults.minuteMin;this._defaults.secondMin=minDateTime.getSeconds();if(this.second<=this._defaults.secondMin){this.second=this._defaults.secondMin;this._defaults.millisecMin=minDateTime.getMilliseconds();}else{if(this.millisec<this._defaults.millisecMin){this.millisec=this._defaults.millisecMin;}
this._defaults.millisecMin=this.millisecMinOriginal;}}else{this._defaults.secondMin=this.secondMinOriginal;this._defaults.millisecMin=this.millisecMinOriginal;}}else{this._defaults.minuteMin=this.minuteMinOriginal;this._defaults.secondMin=this.secondMinOriginal;this._defaults.millisecMin=this.millisecMinOriginal;}}else{this._defaults.hourMin=this.hourMinOriginal;this._defaults.minuteMin=this.minuteMinOriginal;this._defaults.secondMin=this.secondMinOriginal;this._defaults.millisecMin=this.millisecMinOriginal;}}
if($.datepicker._get(dp_inst,'maxDateTime')!==null&&$.datepicker._get(dp_inst,'maxDateTime')!==undefined&&dp_date){var maxDateTime=$.datepicker._get(dp_inst,'maxDateTime'),maxDateTimeDate=new Date(maxDateTime.getFullYear(),maxDateTime.getMonth(),maxDateTime.getDate(),0,0,0,0);if(this.hourMaxOriginal===null||this.minuteMaxOriginal===null||this.secondMaxOriginal===null){this.hourMaxOriginal=o.hourMax;this.minuteMaxOriginal=o.minuteMax;this.secondMaxOriginal=o.secondMax;this.millisecMaxOriginal=o.millisecMax;}
if(dp_inst.settings.timeOnly||maxDateTimeDate.getTime()==dp_date.getTime()){this._defaults.hourMax=maxDateTime.getHours();if(this.hour>=this._defaults.hourMax){this.hour=this._defaults.hourMax;this._defaults.minuteMax=maxDateTime.getMinutes();if(this.minute>=this._defaults.minuteMax){this.minute=this._defaults.minuteMax;this._defaults.secondMax=maxDateTime.getSeconds();if(this.second>=this._defaults.secondMax){this.second=this._defaults.secondMax;this._defaults.millisecMax=maxDateTime.getMilliseconds();}else{if(this.millisec>this._defaults.millisecMax){this.millisec=this._defaults.millisecMax;}
this._defaults.millisecMax=this.millisecMaxOriginal;}}else{this._defaults.secondMax=this.secondMaxOriginal;this._defaults.millisecMax=this.millisecMaxOriginal;}}else{this._defaults.minuteMax=this.minuteMaxOriginal;this._defaults.secondMax=this.secondMaxOriginal;this._defaults.millisecMax=this.millisecMaxOriginal;}}else{this._defaults.hourMax=this.hourMaxOriginal;this._defaults.minuteMax=this.minuteMaxOriginal;this._defaults.secondMax=this.secondMaxOriginal;this._defaults.millisecMax=this.millisecMaxOriginal;}}
if(adjustSliders!==undefined&&adjustSliders===true){var hourMax=parseInt((this._defaults.hourMax-((this._defaults.hourMax-this._defaults.hourMin)%this._defaults.stepHour)),10),minMax=parseInt((this._defaults.minuteMax-((this._defaults.minuteMax-this._defaults.minuteMin)%this._defaults.stepMinute)),10),secMax=parseInt((this._defaults.secondMax-((this._defaults.secondMax-this._defaults.secondMin)%this._defaults.stepSecond)),10),millisecMax=parseInt((this._defaults.millisecMax-((this._defaults.millisecMax-this._defaults.millisecMin)%this._defaults.stepMillisec)),10);if(this.hour_slider){this.control.options(this,this.hour_slider,'hour',{min:this._defaults.hourMin,max:hourMax});this.control.value(this,this.hour_slider,'hour',this.hour-(this.hour%this._defaults.stepHour));}
if(this.minute_slider){this.control.options(this,this.minute_slider,'minute',{min:this._defaults.minuteMin,max:minMax});this.control.value(this,this.minute_slider,'minute',this.minute-(this.minute%this._defaults.stepMinute));}
if(this.second_slider){this.control.options(this,this.second_slider,'second',{min:this._defaults.secondMin,max:secMax});this.control.value(this,this.second_slider,'second',this.second-(this.second%this._defaults.stepSecond));}
if(this.millisec_slider){this.control.options(this,this.millisec_slider,'millisec',{min:this._defaults.millisecMin,max:millisecMax});this.control.value(this,this.millisec_slider,'millisec',this.millisec-(this.millisec%this._defaults.stepMillisec));}}},_onTimeChange:function(){var hour=(this.hour_slider)?this.control.value(this,this.hour_slider,'hour'):false,minute=(this.minute_slider)?this.control.value(this,this.minute_slider,'minute'):false,second=(this.second_slider)?this.control.value(this,this.second_slider,'second'):false,millisec=(this.millisec_slider)?this.control.value(this,this.millisec_slider,'millisec'):false,timezone=(this.timezone_select)?this.timezone_select.val():false,o=this._defaults,pickerTimeFormat=o.pickerTimeFormat||o.timeFormat,pickerTimeSuffix=o.pickerTimeSuffix||o.timeSuffix;if(typeof(hour)=='object'){hour=false;}
if(typeof(minute)=='object'){minute=false;}
if(typeof(second)=='object'){second=false;}
if(typeof(millisec)=='object'){millisec=false;}
if(typeof(timezone)=='object'){timezone=false;}
if(hour!==false){hour=parseInt(hour,10);}
if(minute!==false){minute=parseInt(minute,10);}
if(second!==false){second=parseInt(second,10);}
if(millisec!==false){millisec=parseInt(millisec,10);}
var ampm=o[hour<12?'amNames':'pmNames'][0];var hasChanged=(hour!=this.hour||minute!=this.minute||second!=this.second||millisec!=this.millisec||(this.ampm.length>0&&(hour<12)!=($.inArray(this.ampm.toUpperCase(),this.amNames)!==-1))||((this.timezone===null&&timezone!=this.defaultTimezone)||(this.timezone!==null&&timezone!=this.timezone)));if(hasChanged){if(hour!==false){this.hour=hour;}
if(minute!==false){this.minute=minute;}
if(second!==false){this.second=second;}
if(millisec!==false){this.millisec=millisec;}
if(timezone!==false){this.timezone=timezone;}
if(!this.inst){this.inst=$.datepicker._getInst(this.$input[0]);}
this._limitMinMaxDateTime(this.inst,true);}
if(useAmpm(o.timeFormat)){this.ampm=ampm;}
this.formattedTime=$.datepicker.formatTime(o.timeFormat,this,o);if(this.$timeObj){if(pickerTimeFormat===o.timeFormat){this.$timeObj.text(this.formattedTime+pickerTimeSuffix);}
else{this.$timeObj.text($.datepicker.formatTime(pickerTimeFormat,this,o)+pickerTimeSuffix);}}
this.timeDefined=true;if(hasChanged){this._updateDateTime();}},_onSelectHandler:function(){var onSelect=this._defaults.onSelect||this.inst.settings.onSelect;var inputEl=this.$input?this.$input[0]:null;if(onSelect&&inputEl){onSelect.apply(inputEl,[this.formattedDateTime,this]);}},_updateDateTime:function(dp_inst){dp_inst=this.inst||dp_inst;var dt=$.datepicker._daylightSavingAdjust(new Date(dp_inst.selectedYear,dp_inst.selectedMonth,dp_inst.selectedDay)),dateFmt=$.datepicker._get(dp_inst,'dateFormat'),formatCfg=$.datepicker._getFormatConfig(dp_inst),timeAvailable=dt!==null&&this.timeDefined;this.formattedDate=$.datepicker.formatDate(dateFmt,(dt===null?new Date():dt),formatCfg);var formattedDateTime=this.formattedDate;if(this._defaults.timeOnly===true){formattedDateTime=this.formattedTime;}else if(this._defaults.timeOnly!==true&&(this._defaults.alwaysSetTime||timeAvailable)){formattedDateTime+=this._defaults.separator+this.formattedTime+this._defaults.timeSuffix;}
this.formattedDateTime=formattedDateTime;if(!this._defaults.showTimepicker){this.$input.val(this.formattedDate);}else if(this.$altInput&&this._defaults.altFieldTimeOnly===true){this.$altInput.val(this.formattedTime);this.$input.val(this.formattedDate);}else if(this.$altInput){this.$input.val(formattedDateTime);var altFormattedDateTime='',altSeparator=this._defaults.altSeparator?this._defaults.altSeparator:this._defaults.separator,altTimeSuffix=this._defaults.altTimeSuffix?this._defaults.altTimeSuffix:this._defaults.timeSuffix;if(this._defaults.altFormat)altFormattedDateTime=$.datepicker.formatDate(this._defaults.altFormat,(dt===null?new Date():dt),formatCfg);else altFormattedDateTime=this.formattedDate;if(altFormattedDateTime)altFormattedDateTime+=altSeparator;if(this._defaults.altTimeFormat)altFormattedDateTime+=$.datepicker.formatTime(this._defaults.altTimeFormat,this,this._defaults)+altTimeSuffix;else altFormattedDateTime+=this.formattedTime+altTimeSuffix;this.$altInput.val(altFormattedDateTime);}else{this.$input.val(formattedDateTime);}
this.$input.trigger("change");},_onFocus:function(){if(!this.$input.val()&&this._defaults.defaultValue){this.$input.val(this._defaults.defaultValue);var inst=$.datepicker._getInst(this.$input.get(0)),tp_inst=$.datepicker._get(inst,'timepicker');if(tp_inst){if(tp_inst._defaults.timeOnly&&(inst.input.val()!=inst.lastVal)){try{$.datepicker._updateDatepicker(inst);}catch(err){$.datepicker.log(err);}}}}},_controls:{slider:{create:function(tp_inst,obj,unit,val,min,max,step){var rtl=tp_inst._defaults.isRTL;return obj.prop('slide',null).slider({orientation:"horizontal",value:rtl?val*-1:val,min:rtl?max*-1:min,max:rtl?min*-1:max,step:step,slide:function(event,ui){tp_inst.control.value(tp_inst,$(this),unit,rtl?ui.value*-1:ui.value);tp_inst._onTimeChange();},stop:function(event,ui){tp_inst._onSelectHandler();}});},options:function(tp_inst,obj,unit,opts,val){if(tp_inst._defaults.isRTL){if(typeof(opts)=='string'){if(opts=='min'||opts=='max'){if(val!==undefined)
return obj.slider(opts,val*-1);return Math.abs(obj.slider(opts));}
return obj.slider(opts);}
var min=opts.min,max=opts.max;opts.min=opts.max=null;if(min!==undefined)
opts.max=min*-1;if(max!==undefined)
opts.min=max*-1;return obj.slider(opts);}
if(typeof(opts)=='string'&&val!==undefined)
return obj.slider(opts,val);return obj.slider(opts);},value:function(tp_inst,obj,unit,val){if(tp_inst._defaults.isRTL){if(val!==undefined)
return obj.slider('value',val*-1);return Math.abs(obj.slider('value'));}
if(val!==undefined)
return obj.slider('value',val);return obj.slider('value');}},select:{create:function(tp_inst,obj,unit,val,min,max,step){var sel='<select class="ui-timepicker-select" data-unit="'+unit+'" data-min="'+min+'" data-max="'+max+'" data-step="'+step+'">',ul=tp_inst._defaults.timeFormat.indexOf('t')!==-1?'toLowerCase':'toUpperCase',m=0;for(var i=min;i<=max;i+=step){sel+='<option value="'+i+'"'+(i==val?' selected':'')+'>';if(unit=='hour'&&useAmpm(tp_inst._defaults.pickerTimeFormat||tp_inst._defaults.timeFormat))
sel+=$.datepicker.formatTime("hh TT",{hour:i},tp_inst._defaults);else if(unit=='millisec'||i>=10)sel+=i;else sel+='0'+i.toString();sel+='</option>';}
sel+='</select>';obj.children('select').remove();$(sel).appendTo(obj).change(function(e){tp_inst._onTimeChange();tp_inst._onSelectHandler();});return obj;},options:function(tp_inst,obj,unit,opts,val){var o={},$t=obj.children('select');if(typeof(opts)=='string'){if(val===undefined)
return $t.data(opts);o[opts]=val;}
else o=opts;return tp_inst.control.create(tp_inst,obj,$t.data('unit'),$t.val(),o.min||$t.data('min'),o.max||$t.data('max'),o.step||$t.data('step'));},value:function(tp_inst,obj,unit,val){var $t=obj.children('select');if(val!==undefined)
return $t.val(val);return $t.val();}}}});$.fn.extend({timepicker:function(o){o=o||{};var tmp_args=Array.prototype.slice.call(arguments);if(typeof o=='object'){tmp_args[0]=$.extend(o,{timeOnly:true});}
return $(this).each(function(){$.fn.datetimepicker.apply($(this),tmp_args);});},datetimepicker:function(o){o=o||{};var tmp_args=arguments;if(typeof(o)=='string'){if(o=='getDate'){return $.fn.datepicker.apply($(this[0]),tmp_args);}else{return this.each(function(){var $t=$(this);$t.datepicker.apply($t,tmp_args);});}}else{return this.each(function(){var $t=$(this);$t.datepicker($.timepicker._newInst($t,o)._defaults);});}}});$.datepicker.parseDateTime=function(dateFormat,timeFormat,dateTimeString,dateSettings,timeSettings){var parseRes=parseDateTimeInternal(dateFormat,timeFormat,dateTimeString,dateSettings,timeSettings);if(parseRes.timeObj){var t=parseRes.timeObj;parseRes.date.setHours(t.hour,t.minute,t.second,t.millisec);}
return parseRes.date;};$.datepicker.parseTime=function(timeFormat,timeString,options){var o=extendRemove(extendRemove({},$.timepicker._defaults),options||{});var strictParse=function(f,s,o){var getPatternAmpm=function(amNames,pmNames){var markers=[];if(amNames){$.merge(markers,amNames);}
if(pmNames){$.merge(markers,pmNames);}
markers=$.map(markers,function(val){return val.replace(/[.*+?|()\[\]{}\\]/g,'\\$&');});return'('+markers.join('|')+')?';};var getFormatPositions=function(timeFormat){var finds=timeFormat.toLowerCase().match(/(h{1,2}|m{1,2}|s{1,2}|l{1}|t{1,2}|z|'.*?')/g),orders={h:-1,m:-1,s:-1,l:-1,t:-1,z:-1};if(finds){for(var i=0;i<finds.length;i++){if(orders[finds[i].toString().charAt(0)]==-1){orders[finds[i].toString().charAt(0)]=i+1;}}}
return orders;};var regstr='^'+f.toString().replace(/([hH]{1,2}|mm?|ss?|[tT]{1,2}|[lz]|'.*?')/g,function(match){var ml=match.length;switch(match.charAt(0).toLowerCase()){case'h':return ml===1?'(\\d?\\d)':'(\\d{'+ml+'})';case'm':return ml===1?'(\\d?\\d)':'(\\d{'+ml+'})';case's':return ml===1?'(\\d?\\d)':'(\\d{'+ml+'})';case'l':return'(\\d?\\d?\\d)';case'z':return'(z|[-+]\\d\\d:?\\d\\d|\\S+)?';case't':return getPatternAmpm(o.amNames,o.pmNames);default:return'('+match.replace(/\'/g,"").replace(/(\.|\$|\^|\\|\/|\(|\)|\[|\]|\?|\+|\*)/g,function(m){return"\\"+m;})+')?';}}).replace(/\s/g,'\\s?')+
o.timeSuffix+'$',order=getFormatPositions(f),ampm='',treg;treg=s.match(new RegExp(regstr,'i'));var resTime={hour:0,minute:0,second:0,millisec:0};if(treg){if(order.t!==-1){if(treg[order.t]===undefined||treg[order.t].length===0){ampm='';resTime.ampm='';}else{ampm=$.inArray(treg[order.t].toUpperCase(),o.amNames)!==-1?'AM':'PM';resTime.ampm=o[ampm=='AM'?'amNames':'pmNames'][0];}}
if(order.h!==-1){if(ampm=='AM'&&treg[order.h]=='12'){resTime.hour=0;}else{if(ampm=='PM'&&treg[order.h]!='12'){resTime.hour=parseInt(treg[order.h],10)+12;}else{resTime.hour=Number(treg[order.h]);}}}
if(order.m!==-1){resTime.minute=Number(treg[order.m]);}
if(order.s!==-1){resTime.second=Number(treg[order.s]);}
if(order.l!==-1){resTime.millisec=Number(treg[order.l]);}
if(order.z!==-1&&treg[order.z]!==undefined){var tz=treg[order.z].toUpperCase();switch(tz.length){case 1:tz=o.timezoneIso8601?'Z':'+0000';break;case 5:if(o.timezoneIso8601){tz=tz.substring(1)=='0000'?'Z':tz.substring(0,3)+':'+tz.substring(3);}
break;case 6:if(!o.timezoneIso8601){tz=tz=='Z'||tz.substring(1)=='00:00'?'+0000':tz.replace(/:/,'');}else{if(tz.substring(1)=='00:00'){tz='Z';}}
break;}
resTime.timezone=tz;}
return resTime;}
return false;};var looseParse=function(f,s,o){try{var d=new Date('2012-01-01 '+s);if(isNaN(d.getTime())){d=new Date('2012-01-01T'+s);if(isNaN(d.getTime())){d=new Date('01/01/2012 '+s);if(isNaN(d.getTime())){throw"Unable to parse time with native Date: "+s;}}}
return{hour:d.getHours(),minute:d.getMinutes(),second:d.getSeconds(),millisec:d.getMilliseconds(),timezone:$.timepicker.timeZoneOffsetString(d)};}
catch(err){try{return strictParse(f,s,o);}
catch(err2){$.datepicker.log("Unable to parse \ntimeString: "+s+"\ntimeFormat: "+f);}}
return false;};if(typeof o.parse==="function"){return o.parse(timeFormat,timeString,o)}
if(o.parse==='loose'){return looseParse(timeFormat,timeString,o);}
return strictParse(timeFormat,timeString,o);};$.datepicker.formatTime=function(format,time,options){options=options||{};options=$.extend({},$.timepicker._defaults,options);time=$.extend({hour:0,minute:0,second:0,millisec:0,timezone:'+0000'},time);var tmptime=format,ampmName=options.amNames[0],hour=parseInt(time.hour,10);if(hour>11){ampmName=options.pmNames[0];}
tmptime=tmptime.replace(/(?:HH?|hh?|mm?|ss?|[tT]{1,2}|[lz]|('.*?'|".*?"))/g,function(match){switch(match){case'HH':return('0'+hour).slice(-2);case'H':return hour;case'hh':return('0'+convert24to12(hour)).slice(-2);case'h':return convert24to12(hour);case'mm':return('0'+time.minute).slice(-2);case'm':return time.minute;case'ss':return('0'+time.second).slice(-2);case's':return time.second;case'l':return('00'+time.millisec).slice(-3);case'z':return time.timezone===null?options.defaultTimezone:time.timezone;case'T':return ampmName.charAt(0).toUpperCase();case'TT':return ampmName.toUpperCase();case't':return ampmName.charAt(0).toLowerCase();case'tt':return ampmName.toLowerCase();default:return match.replace(/\'/g,"")||"'";}});tmptime=$.trim(tmptime);return tmptime;};$.datepicker._base_selectDate=$.datepicker._selectDate;$.datepicker._selectDate=function(id,dateStr){var inst=this._getInst($(id)[0]),tp_inst=this._get(inst,'timepicker');if(tp_inst){tp_inst._limitMinMaxDateTime(inst,true);inst.inline=inst.stay_open=true;this._base_selectDate(id,dateStr);inst.inline=inst.stay_open=false;this._notifyChange(inst);this._updateDatepicker(inst);}else{this._base_selectDate(id,dateStr);}};$.datepicker._base_updateDatepicker=$.datepicker._updateDatepicker;$.datepicker._updateDatepicker=function(inst){var input=inst.input[0];if($.datepicker._curInst&&$.datepicker._curInst!=inst&&$.datepicker._datepickerShowing&&$.datepicker._lastInput!=input){return;}
if(typeof(inst.stay_open)!=='boolean'||inst.stay_open===false){this._base_updateDatepicker(inst);var tp_inst=this._get(inst,'timepicker');if(tp_inst){tp_inst._addTimePicker(inst);}}};$.datepicker._base_doKeyPress=$.datepicker._doKeyPress;$.datepicker._doKeyPress=function(event){var inst=$.datepicker._getInst(event.target),tp_inst=$.datepicker._get(inst,'timepicker');if(tp_inst){if($.datepicker._get(inst,'constrainInput')){var ampm=useAmpm(tp_inst._defaults.timeFormat),dateChars=$.datepicker._possibleChars($.datepicker._get(inst,'dateFormat')),datetimeChars=tp_inst._defaults.timeFormat.toString().replace(/[hms]/g,'').replace(/TT/g,ampm?'APM':'').replace(/Tt/g,ampm?'AaPpMm':'').replace(/tT/g,ampm?'AaPpMm':'').replace(/T/g,ampm?'AP':'').replace(/tt/g,ampm?'apm':'').replace(/t/g,ampm?'ap':'')+" "+tp_inst._defaults.separator+
tp_inst._defaults.timeSuffix+
(tp_inst._defaults.showTimezone?tp_inst._defaults.timezoneList.join(''):'')+
(tp_inst._defaults.amNames.join(''))+(tp_inst._defaults.pmNames.join(''))+
dateChars,chr=String.fromCharCode(event.charCode===undefined?event.keyCode:event.charCode);return event.ctrlKey||(chr<' '||!dateChars||datetimeChars.indexOf(chr)>-1);}}
return $.datepicker._base_doKeyPress(event);};$.datepicker._base_updateAlternate=$.datepicker._updateAlternate;$.datepicker._updateAlternate=function(inst){var tp_inst=this._get(inst,'timepicker');if(tp_inst){var altField=tp_inst._defaults.altField;if(altField){var altFormat=tp_inst._defaults.altFormat||tp_inst._defaults.dateFormat,date=this._getDate(inst),formatCfg=$.datepicker._getFormatConfig(inst),altFormattedDateTime='',altSeparator=tp_inst._defaults.altSeparator?tp_inst._defaults.altSeparator:tp_inst._defaults.separator,altTimeSuffix=tp_inst._defaults.altTimeSuffix?tp_inst._defaults.altTimeSuffix:tp_inst._defaults.timeSuffix,altTimeFormat=tp_inst._defaults.altTimeFormat!==null?tp_inst._defaults.altTimeFormat:tp_inst._defaults.timeFormat;altFormattedDateTime+=$.datepicker.formatTime(altTimeFormat,tp_inst,tp_inst._defaults)+altTimeSuffix;if(!tp_inst._defaults.timeOnly&&!tp_inst._defaults.altFieldTimeOnly&&date!==null){if(tp_inst._defaults.altFormat)
altFormattedDateTime=$.datepicker.formatDate(tp_inst._defaults.altFormat,date,formatCfg)+altSeparator+altFormattedDateTime;else altFormattedDateTime=tp_inst.formattedDate+altSeparator+altFormattedDateTime;}
$(altField).val(altFormattedDateTime);}}
else{$.datepicker._base_updateAlternate(inst);}};$.datepicker._base_doKeyUp=$.datepicker._doKeyUp;$.datepicker._doKeyUp=function(event){var inst=$.datepicker._getInst(event.target),tp_inst=$.datepicker._get(inst,'timepicker');if(tp_inst){if(tp_inst._defaults.timeOnly&&(inst.input.val()!=inst.lastVal)){try{$.datepicker._updateDatepicker(inst);}catch(err){$.datepicker.log(err);}}}
return $.datepicker._base_doKeyUp(event);};$.datepicker._base_gotoToday=$.datepicker._gotoToday;$.datepicker._gotoToday=function(id){var inst=this._getInst($(id)[0]),$dp=inst.dpDiv;this._base_gotoToday(id);var tp_inst=this._get(inst,'timepicker');selectLocalTimeZone(tp_inst);var now=new Date();this._setTime(inst,now);$('.ui-datepicker-today',$dp).click();};$.datepicker._disableTimepickerDatepicker=function(target){var inst=this._getInst(target);if(!inst){return;}
var tp_inst=this._get(inst,'timepicker');$(target).datepicker('getDate');if(tp_inst){tp_inst._defaults.showTimepicker=false;tp_inst._updateDateTime(inst);}};$.datepicker._enableTimepickerDatepicker=function(target){var inst=this._getInst(target);if(!inst){return;}
var tp_inst=this._get(inst,'timepicker');$(target).datepicker('getDate');if(tp_inst){tp_inst._defaults.showTimepicker=true;tp_inst._addTimePicker(inst);tp_inst._updateDateTime(inst);}};$.datepicker._setTime=function(inst,date){var tp_inst=this._get(inst,'timepicker');if(tp_inst){var defaults=tp_inst._defaults;tp_inst.hour=date?date.getHours():defaults.hour;tp_inst.minute=date?date.getMinutes():defaults.minute;tp_inst.second=date?date.getSeconds():defaults.second;tp_inst.millisec=date?date.getMilliseconds():defaults.millisec;tp_inst._limitMinMaxDateTime(inst,true);tp_inst._onTimeChange();tp_inst._updateDateTime(inst);}};$.datepicker._setTimeDatepicker=function(target,date,withDate){var inst=this._getInst(target);if(!inst){return;}
var tp_inst=this._get(inst,'timepicker');if(tp_inst){this._setDateFromField(inst);var tp_date;if(date){if(typeof date=="string"){tp_inst._parseTime(date,withDate);tp_date=new Date();tp_date.setHours(tp_inst.hour,tp_inst.minute,tp_inst.second,tp_inst.millisec);}else{tp_date=new Date(date.getTime());}
if(tp_date.toString()=='Invalid Date'){tp_date=undefined;}
this._setTime(inst,tp_date);}}};$.datepicker._base_setDateDatepicker=$.datepicker._setDateDatepicker;$.datepicker._setDateDatepicker=function(target,date){var inst=this._getInst(target);if(!inst){return;}
var tp_date=(date instanceof Date)?new Date(date.getTime()):date;this._updateDatepicker(inst);this._base_setDateDatepicker.apply(this,arguments);this._setTimeDatepicker(target,tp_date,true);};$.datepicker._base_getDateDatepicker=$.datepicker._getDateDatepicker;$.datepicker._getDateDatepicker=function(target,noDefault){var inst=this._getInst(target);if(!inst){return;}
var tp_inst=this._get(inst,'timepicker');if(tp_inst){if(inst.lastVal===undefined){this._setDateFromField(inst,noDefault);}
var date=this._getDate(inst);if(date&&tp_inst._parseTime($(target).val(),tp_inst.timeOnly)){date.setHours(tp_inst.hour,tp_inst.minute,tp_inst.second,tp_inst.millisec);}
return date;}
return this._base_getDateDatepicker(target,noDefault);};$.datepicker._base_parseDate=$.datepicker.parseDate;$.datepicker.parseDate=function(format,value,settings){var date;try{date=this._base_parseDate(format,value,settings);}catch(err){date=this._base_parseDate(format,value.substring(0,value.length-(err.length-err.indexOf(':')-2)),settings);$.datepicker.log("Error parsing the date string: "+err+"\ndate string = "+value+"\ndate format = "+format);}
return date;};$.datepicker._base_formatDate=$.datepicker._formatDate;$.datepicker._formatDate=function(inst,day,month,year){var tp_inst=this._get(inst,'timepicker');if(tp_inst){tp_inst._updateDateTime(inst);return tp_inst.$input.val();}
return this._base_formatDate(inst);};$.datepicker._base_optionDatepicker=$.datepicker._optionDatepicker;$.datepicker._optionDatepicker=function(target,name,value){var inst=this._getInst(target),name_clone;if(!inst){return null;}
var tp_inst=this._get(inst,'timepicker');if(tp_inst){var min=null,max=null,onselect=null,overrides=tp_inst._defaults.evnts,fns={},prop;if(typeof name=='string'){if(name==='minDate'||name==='minDateTime'){min=value;}else if(name==='maxDate'||name==='maxDateTime'){max=value;}else if(name==='onSelect'){onselect=value;}else if(overrides.hasOwnProperty(name)){if(typeof(value)==='undefined'){return overrides[name];}
fns[name]=value;name_clone={};}}else if(typeof name=='object'){if(name.minDate){min=name.minDate;}else if(name.minDateTime){min=name.minDateTime;}else if(name.maxDate){max=name.maxDate;}else if(name.maxDateTime){max=name.maxDateTime;}
for(prop in overrides){if(overrides.hasOwnProperty(prop)&&name[prop]){fns[prop]=name[prop];}}}
for(prop in fns){if(fns.hasOwnProperty(prop)){overrides[prop]=fns[prop];if(!name_clone){name_clone=$.extend({},name);}
delete name_clone[prop];}}
if(name_clone&&isEmptyObject(name_clone)){return;}
if(min){if(min===0){min=new Date();}else{min=new Date(min);}
tp_inst._defaults.minDate=min;tp_inst._defaults.minDateTime=min;}else if(max){if(max===0){max=new Date();}else{max=new Date(max);}
tp_inst._defaults.maxDate=max;tp_inst._defaults.maxDateTime=max;}else if(onselect){tp_inst._defaults.onSelect=onselect;}}
if(value===undefined){return this._base_optionDatepicker.call($.datepicker,target,name);}
return this._base_optionDatepicker.call($.datepicker,target,name_clone||name,value);};var isEmptyObject=function(obj){var prop;for(prop in obj){if(obj.hasOwnProperty(obj)){return false;}}
return true;};var extendRemove=function(target,props){$.extend(target,props);for(var name in props){if(props[name]===null||props[name]===undefined){target[name]=props[name];}}
return target;};var useAmpm=function(timeFormat){return(timeFormat.indexOf('t')!==-1&&timeFormat.indexOf('h')!==-1);};var convert24to12=function(hour){if(hour>12){hour=hour-12;}
if(hour==0){hour=12;}
return String(hour);};var splitDateTime=function(dateFormat,dateTimeString,dateSettings,timeSettings){try{var separator=timeSettings&&timeSettings.separator?timeSettings.separator:$.timepicker._defaults.separator,format=timeSettings&&timeSettings.timeFormat?timeSettings.timeFormat:$.timepicker._defaults.timeFormat,timeParts=format.split(separator),timePartsLen=timeParts.length,allParts=dateTimeString.split(separator),allPartsLen=allParts.length;if(allPartsLen>1){return[allParts.splice(0,allPartsLen-timePartsLen).join(separator),allParts.splice(0,timePartsLen).join(separator)];}}catch(err){$.datepicker.log('Could not split the date from the time. Please check the following datetimepicker options'+"\nthrown error: "+err+"\ndateTimeString"+dateTimeString+"\ndateFormat = "+dateFormat+"\nseparator = "+timeSettings.separator+"\ntimeFormat = "+timeSettings.timeFormat);if(err.indexOf(":")>=0){var dateStringLength=dateTimeString.length-(err.length-err.indexOf(':')-2),timeString=dateTimeString.substring(dateStringLength);return[$.trim(dateTimeString.substring(0,dateStringLength)),$.trim(dateTimeString.substring(dateStringLength))];}else{throw err;}}
return[dateTimeString,''];};var parseDateTimeInternal=function(dateFormat,timeFormat,dateTimeString,dateSettings,timeSettings){var date;var splitRes=splitDateTime(dateFormat,dateTimeString,dateSettings,timeSettings);date=$.datepicker._base_parseDate(dateFormat,splitRes[0],dateSettings);if(splitRes[1]!==''){var timeString=splitRes[1],parsedTime=$.datepicker.parseTime(timeFormat,timeString,timeSettings);if(parsedTime===null){throw'Wrong time format';}
return{date:date,timeObj:parsedTime};}else{return{date:date};}};var selectLocalTimeZone=function(tp_inst,date){if(tp_inst&&tp_inst.timezone_select){tp_inst._defaults.useLocalTimezone=true;var now=typeof date!=='undefined'?date:new Date();var tzoffset=$.timepicker.timeZoneOffsetString(now);if(tp_inst._defaults.timezoneIso8601){tzoffset=tzoffset.substring(0,3)+':'+tzoffset.substring(3);}
tp_inst.timezone_select.val(tzoffset);}};$.timepicker=new Timepicker();$.timepicker.timeZoneOffsetString=function(date){var off=date.getTimezoneOffset()*-1,minutes=off%60,hours=(off-minutes)/60;return(off>=0?'+':'-')+('0'+(hours*101).toString()).slice(-2)+('0'+(minutes*101).toString()).slice(-2);};$.timepicker.timeRange=function(startTime,endTime,options){return $.timepicker.handleRange('timepicker',startTime,endTime,options);};$.timepicker.dateTimeRange=function(startTime,endTime,options){$.timepicker.dateRange(startTime,endTime,options,'datetimepicker');};$.timepicker.dateRange=function(startTime,endTime,options,method){method=method||'datepicker';$.timepicker.handleRange(method,startTime,endTime,options);};$.timepicker.handleRange=function(method,startTime,endTime,options){$.fn[method].call(startTime,$.extend({onClose:function(dateText,inst){checkDates(this,endTime,dateText);},onSelect:function(selectedDateTime){selected(this,endTime,'minDate');}},options,options.start));$.fn[method].call(endTime,$.extend({onClose:function(dateText,inst){checkDates(this,startTime,dateText);},onSelect:function(selectedDateTime){selected(this,startTime,'maxDate');}},options,options.end));if(method!='timepicker'&&options.reformat){$([startTime,endTime]).each(function(){var format=$(this)[method].call($(this),'option','dateFormat'),date=new Date($(this).val());if($(this).val()&&date){$(this).val($.datepicker.formatDate(format,date));}});}
checkDates(startTime,endTime,startTime.val());function checkDates(changed,other,dateText){if(other.val()&&(new Date(startTime.val())>new Date(endTime.val()))){other.val(dateText);}}
selected(startTime,endTime,'minDate');selected(endTime,startTime,'maxDate');function selected(changed,other,option){if(!$(changed).val()){return;}
var date=$(changed)[method].call($(changed),'getDate');if(date.getTime){$(other)[method].call($(other),'option',option,date);}}
return $([startTime.get(0),endTime.get(0)]);};$.timepicker.version="1.1.2";})(jQuery);

(function(factory){'use strict';if(typeof define==='function'&&define.amd){define(['jquery','jquery.ui.widget'],factory);}else{factory(window.jQuery);}}(function($){'use strict';$.support.xhrFileUpload=!!(window.XMLHttpRequestUpload&&window.FileReader);$.support.xhrFormDataFileUpload=!!window.FormData;$.propHooks.elements={get:function(form){if($.nodeName(form,'form')){return $.grep(form.elements,function(elem){return!$.nodeName(elem,'input')||elem.type!=='file';});}
return null;}};$.widget('blueimp.fileupload',{options:{dropZone:$(document),pasteZone:$(document),fileInput:undefined,replaceFileInput:true,paramName:undefined,singleFileUploads:true,limitMultiFileUploads:undefined,sequentialUploads:false,limitConcurrentUploads:undefined,forceIframeTransport:false,redirect:undefined,redirectParamName:undefined,postMessage:undefined,multipart:true,maxChunkSize:undefined,uploadedBytes:undefined,recalculateProgress:true,progressInterval:100,bitrateInterval:500,formData:function(form){return form.serializeArray();},add:function(e,data){data.submit();},processData:false,contentType:false,cache:false},_refreshOptionsList:['fileInput','dropZone','pasteZone','multipart','forceIframeTransport'],_BitrateTimer:function(){this.timestamp=+(new Date());this.loaded=0;this.bitrate=0;this.getBitrate=function(now,loaded,interval){var timeDiff=now-this.timestamp;if(!this.bitrate||!interval||timeDiff>interval){this.bitrate=(loaded-this.loaded)*(1000/timeDiff)*8;this.loaded=loaded;this.timestamp=now;}
return this.bitrate;};},_isXHRUpload:function(options){return!options.forceIframeTransport&&((!options.multipart&&$.support.xhrFileUpload)||$.support.xhrFormDataFileUpload);},_getFormData:function(options){var formData;if(typeof options.formData==='function'){return options.formData(options.form);}
if($.isArray(options.formData)){return options.formData;}
if(options.formData){formData=[];$.each(options.formData,function(name,value){formData.push({name:name,value:value});});return formData;}
return[];},_getTotal:function(files){var total=0;$.each(files,function(index,file){total+=file.size||1;});return total;},_onProgress:function(e,data){if(e.lengthComputable){var now=+(new Date()),total,loaded;if(data._time&&data.progressInterval&&(now-data._time<data.progressInterval)&&e.loaded!==e.total){return;}
data._time=now;total=data.total||this._getTotal(data.files);loaded=parseInt(e.loaded/e.total*(data.chunkSize||total),10)+(data.uploadedBytes||0);this._loaded+=loaded-(data.loaded||data.uploadedBytes||0);data.lengthComputable=true;data.loaded=loaded;data.total=total;data.bitrate=data._bitrateTimer.getBitrate(now,loaded,data.bitrateInterval);this._trigger('progress',e,data);this._trigger('progressall',e,{lengthComputable:true,loaded:this._loaded,total:this._total,bitrate:this._bitrateTimer.getBitrate(now,this._loaded,data.bitrateInterval)});}},_initProgressListener:function(options){var that=this,xhr=options.xhr?options.xhr():$.ajaxSettings.xhr();if(xhr.upload){$(xhr.upload).bind('progress',function(e){var oe=e.originalEvent;e.lengthComputable=oe.lengthComputable;e.loaded=oe.loaded;e.total=oe.total;that._onProgress(e,options);});options.xhr=function(){return xhr;};}},_initXHRData:function(options){var formData,file=options.files[0],multipart=options.multipart||!$.support.xhrFileUpload,paramName=options.paramName[0];options.headers=options.headers||{};if(options.contentRange){options.headers['Content-Range']=options.contentRange;}
if(!multipart){options.headers['Content-Disposition']='attachment; filename="'+
encodeURI(file.name)+'"';options.contentType=file.type;options.data=options.blob||file;}else if($.support.xhrFormDataFileUpload){if(options.postMessage){formData=this._getFormData(options);if(options.blob){formData.push({name:paramName,value:options.blob});}else{$.each(options.files,function(index,file){formData.push({name:options.paramName[index]||paramName,value:file});});}}else{if(options.formData instanceof FormData){formData=options.formData;}else{formData=new FormData();$.each(this._getFormData(options),function(index,field){formData.append(field.name,field.value);});}
if(options.blob){options.headers['Content-Disposition']='attachment; filename="'+
encodeURI(file.name)+'"';formData.append(paramName,options.blob,file.name);}else{$.each(options.files,function(index,file){if((window.Blob&&file instanceof Blob)||(window.File&&file instanceof File)){formData.append(options.paramName[index]||paramName,file,file.name);}});}}
options.data=formData;}
options.blob=null;},_initIframeSettings:function(options){options.dataType='iframe '+(options.dataType||'');options.formData=this._getFormData(options);if(options.redirect&&$('<a></a>').prop('href',options.url).prop('host')!==location.host){options.formData.push({name:options.redirectParamName||'redirect',value:options.redirect});}},_initDataSettings:function(options){if(this._isXHRUpload(options)){if(!this._chunkedUpload(options,true)){if(!options.data){this._initXHRData(options);}
this._initProgressListener(options);}
if(options.postMessage){options.dataType='postmessage '+(options.dataType||'');}}else{this._initIframeSettings(options,'iframe');}},_getParamName:function(options){var fileInput=$(options.fileInput),paramName=options.paramName;if(!paramName){paramName=[];fileInput.each(function(){var input=$(this),name=input.prop('name')||'files[]',i=(input.prop('files')||[1]).length;while(i){paramName.push(name);i-=1;}});if(!paramName.length){paramName=[fileInput.prop('name')||'files[]'];}}else if(!$.isArray(paramName)){paramName=[paramName];}
return paramName;},_initFormSettings:function(options){if(!options.form||!options.form.length){options.form=$(options.fileInput.prop('form'));if(!options.form.length){options.form=$(this.options.fileInput.prop('form'));}}
options.paramName=this._getParamName(options);if(!options.url){options.url=options.form.prop('action')||location.href;}
options.type=(options.type||options.form.prop('method')||'').toUpperCase();if(options.type!=='POST'&&options.type!=='PUT'&&options.type!=='PATCH'){options.type='POST';}
if(!options.formAcceptCharset){options.formAcceptCharset=options.form.attr('accept-charset');}},_getAJAXSettings:function(data){var options=$.extend({},this.options,data);this._initFormSettings(options);this._initDataSettings(options);return options;},_enhancePromise:function(promise){promise.success=promise.done;promise.error=promise.fail;promise.complete=promise.always;return promise;},_getXHRPromise:function(resolveOrReject,context,args){var dfd=$.Deferred(),promise=dfd.promise();context=context||this.options.context||promise;if(resolveOrReject===true){dfd.resolveWith(context,args);}else if(resolveOrReject===false){dfd.rejectWith(context,args);}
promise.abort=dfd.promise;return this._enhancePromise(promise);},_getUploadedBytes:function(jqXHR){var range=jqXHR.getResponseHeader('Range'),parts=range&&range.split('-'),upperBytesPos=parts&&parts.length>1&&parseInt(parts[1],10);return upperBytesPos&&upperBytesPos+1;},_chunkedUpload:function(options,testOnly){var that=this,file=options.files[0],fs=file.size,ub=options.uploadedBytes=options.uploadedBytes||0,mcs=options.maxChunkSize||fs,slice=file.slice||file.webkitSlice||file.mozSlice,dfd=$.Deferred(),promise=dfd.promise(),jqXHR,upload;if(!(this._isXHRUpload(options)&&slice&&(ub||mcs<fs))||options.data){return false;}
if(testOnly){return true;}
if(ub>=fs){file.error='Uploaded bytes exceed file size';return this._getXHRPromise(false,options.context,[null,'error',file.error]);}
upload=function(){var o=$.extend({},options);o.blob=slice.call(file,ub,ub+mcs,file.type);o.chunkSize=o.blob.size;o.contentRange='bytes '+ub+'-'+
(ub+o.chunkSize-1)+'/'+fs;that._initXHRData(o);that._initProgressListener(o);jqXHR=((that._trigger('chunksend',null,o)!==false&&$.ajax(o))||that._getXHRPromise(false,o.context)).done(function(result,textStatus,jqXHR){ub=that._getUploadedBytes(jqXHR)||(ub+o.chunkSize);if(!o.loaded||o.loaded<o.total){that._onProgress($.Event('progress',{lengthComputable:true,loaded:ub-o.uploadedBytes,total:ub-o.uploadedBytes}),o);}
options.uploadedBytes=o.uploadedBytes=ub;o.result=result;o.textStatus=textStatus;o.jqXHR=jqXHR;that._trigger('chunkdone',null,o);that._trigger('chunkalways',null,o);if(ub<fs){upload();}else{dfd.resolveWith(o.context,[result,textStatus,jqXHR]);}}).fail(function(jqXHR,textStatus,errorThrown){o.jqXHR=jqXHR;o.textStatus=textStatus;o.errorThrown=errorThrown;that._trigger('chunkfail',null,o);that._trigger('chunkalways',null,o);dfd.rejectWith(o.context,[jqXHR,textStatus,errorThrown]);});};this._enhancePromise(promise);promise.abort=function(){return jqXHR.abort();};upload();return promise;},_beforeSend:function(e,data){if(this._active===0){this._trigger('start');this._bitrateTimer=new this._BitrateTimer();}
this._active+=1;this._loaded+=data.uploadedBytes||0;this._total+=this._getTotal(data.files);},_onDone:function(result,textStatus,jqXHR,options){if(!this._isXHRUpload(options)||!options.loaded||options.loaded<options.total){var total=this._getTotal(options.files)||1;this._onProgress($.Event('progress',{lengthComputable:true,loaded:total,total:total}),options);}
options.result=result;options.textStatus=textStatus;options.jqXHR=jqXHR;this._trigger('done',null,options);},_onFail:function(jqXHR,textStatus,errorThrown,options){options.jqXHR=jqXHR;options.textStatus=textStatus;options.errorThrown=errorThrown;this._trigger('fail',null,options);if(options.recalculateProgress){this._loaded-=options.loaded||options.uploadedBytes||0;this._total-=options.total||this._getTotal(options.files);}},_onAlways:function(jqXHRorResult,textStatus,jqXHRorError,options){this._active-=1;this._trigger('always',null,options);if(this._active===0){this._trigger('stop');this._loaded=this._total=0;this._bitrateTimer=null;}},_onSend:function(e,data){var that=this,jqXHR,aborted,slot,pipe,options=that._getAJAXSettings(data),send=function(){that._sending+=1;options._bitrateTimer=new that._BitrateTimer();jqXHR=jqXHR||(((aborted||that._trigger('send',e,options)===false)&&that._getXHRPromise(false,options.context,aborted))||that._chunkedUpload(options)||$.ajax(options)).done(function(result,textStatus,jqXHR){that._onDone(result,textStatus,jqXHR,options);}).fail(function(jqXHR,textStatus,errorThrown){that._onFail(jqXHR,textStatus,errorThrown,options);}).always(function(jqXHRorResult,textStatus,jqXHRorError){that._sending-=1;that._onAlways(jqXHRorResult,textStatus,jqXHRorError,options);if(options.limitConcurrentUploads&&options.limitConcurrentUploads>that._sending){var nextSlot=that._slots.shift(),isPending;while(nextSlot){isPending=nextSlot.state?nextSlot.state()==='pending':!nextSlot.isRejected();if(isPending){nextSlot.resolve();break;}
nextSlot=that._slots.shift();}}});return jqXHR;};this._beforeSend(e,options);if(this.options.sequentialUploads||(this.options.limitConcurrentUploads&&this.options.limitConcurrentUploads<=this._sending)){if(this.options.limitConcurrentUploads>1){slot=$.Deferred();this._slots.push(slot);pipe=slot.pipe(send);}else{pipe=(this._sequence=this._sequence.pipe(send,send));}
pipe.abort=function(){aborted=[undefined,'abort','abort'];if(!jqXHR){if(slot){slot.rejectWith(options.context,aborted);}
return send();}
return jqXHR.abort();};return this._enhancePromise(pipe);}
return send();},_onAdd:function(e,data){var that=this,result=true,options=$.extend({},this.options,data),limit=options.limitMultiFileUploads,paramName=this._getParamName(options),paramNameSet,paramNameSlice,fileSet,i;if(!(options.singleFileUploads||limit)||!this._isXHRUpload(options)){fileSet=[data.files];paramNameSet=[paramName];}else if(!options.singleFileUploads&&limit){fileSet=[];paramNameSet=[];for(i=0;i<data.files.length;i+=limit){fileSet.push(data.files.slice(i,i+limit));paramNameSlice=paramName.slice(i,i+limit);if(!paramNameSlice.length){paramNameSlice=paramName;}
paramNameSet.push(paramNameSlice);}}else{paramNameSet=paramName;}
data.originalFiles=data.files;$.each(fileSet||data.files,function(index,element){var newData=$.extend({},data);newData.files=fileSet?element:[element];newData.paramName=paramNameSet[index];newData.submit=function(){newData.jqXHR=this.jqXHR=(that._trigger('submit',e,this)!==false)&&that._onSend(e,this);return this.jqXHR;};result=that._trigger('add',e,newData);return result;});return result;},_replaceFileInput:function(input){var inputClone=input.clone(true);$('<form></form>').append(inputClone)[0].reset();input.after(inputClone).detach();$.cleanData(input.unbind('remove'));this.options.fileInput=this.options.fileInput.map(function(i,el){if(el===input[0]){return inputClone[0];}
return el;});if(input[0]===this.element[0]){this.element=inputClone;}},_handleFileTreeEntry:function(entry,path){var that=this,dfd=$.Deferred(),errorHandler=function(e){if(e&&!e.entry){e.entry=entry;}
dfd.resolve([e]);},dirReader;path=path||'';if(entry.isFile){if(entry._file){entry._file.relativePath=path;dfd.resolve(entry._file);}else{entry.file(function(file){file.relativePath=path;dfd.resolve(file);},errorHandler);}}else if(entry.isDirectory){dirReader=entry.createReader();dirReader.readEntries(function(entries){that._handleFileTreeEntries(entries,path+entry.name+'/').done(function(files){dfd.resolve(files);}).fail(errorHandler);},errorHandler);}else{dfd.resolve([]);}
return dfd.promise();},_handleFileTreeEntries:function(entries,path){var that=this;return $.when.apply($,$.map(entries,function(entry){return that._handleFileTreeEntry(entry,path);})).pipe(function(){return Array.prototype.concat.apply([],arguments);});},_getDroppedFiles:function(dataTransfer){dataTransfer=dataTransfer||{};var items=dataTransfer.items;if(items&&items.length&&(items[0].webkitGetAsEntry||items[0].getAsEntry)){return this._handleFileTreeEntries($.map(items,function(item){var entry;if(item.webkitGetAsEntry){entry=item.webkitGetAsEntry();if(entry){entry._file=item.getAsFile();}
return entry;}
return item.getAsEntry();}));}
return $.Deferred().resolve($.makeArray(dataTransfer.files)).promise();},_getSingleFileInputFiles:function(fileInput){fileInput=$(fileInput);var entries=fileInput.prop('webkitEntries')||fileInput.prop('entries'),files,value;if(entries&&entries.length){return this._handleFileTreeEntries(entries);}
files=$.makeArray(fileInput.prop('files'));if(!files.length){value=fileInput.prop('value');if(!value){return $.Deferred().resolve([]).promise();}
files=[{name:value.replace(/^.*\\/,'')}];}else if(files[0].name===undefined&&files[0].fileName){$.each(files,function(index,file){file.name=file.fileName;file.size=file.fileSize;});}
return $.Deferred().resolve(files).promise();},_getFileInputFiles:function(fileInput){if(!(fileInput instanceof $)||fileInput.length===1){return this._getSingleFileInputFiles(fileInput);}
return $.when.apply($,$.map(fileInput,this._getSingleFileInputFiles)).pipe(function(){return Array.prototype.concat.apply([],arguments);});},_onChange:function(e){var that=this,data={fileInput:$(e.target),form:$(e.target.form)};this._getFileInputFiles(data.fileInput).always(function(files){data.files=files;if(that.options.replaceFileInput){that._replaceFileInput(data.fileInput);}
if(that._trigger('change',e,data)!==false){that._onAdd(e,data);}});},_onPaste:function(e){var cbd=e.originalEvent.clipboardData,items=(cbd&&cbd.items)||[],data={files:[]};$.each(items,function(index,item){var file=item.getAsFile&&item.getAsFile();if(file){data.files.push(file);}});if(this._trigger('paste',e,data)===false||this._onAdd(e,data)===false){return false;}},_onDrop:function(e){var that=this,dataTransfer=e.dataTransfer=e.originalEvent.dataTransfer,data={};if(dataTransfer&&dataTransfer.files&&dataTransfer.files.length){e.preventDefault();}
this._getDroppedFiles(dataTransfer).always(function(files){data.files=files;if(that._trigger('drop',e,data)!==false){that._onAdd(e,data);}});},_onDragOver:function(e){var dataTransfer=e.dataTransfer=e.originalEvent.dataTransfer;if(this._trigger('dragover',e)===false){return false;}
if(dataTransfer&&$.inArray('Files',dataTransfer.types)!==-1){dataTransfer.dropEffect='copy';e.preventDefault();}},_initEventHandlers:function(){if(this._isXHRUpload(this.options)){this._on(this.options.dropZone,{dragover:this._onDragOver,drop:this._onDrop});this._on(this.options.pasteZone,{paste:this._onPaste});}
this._on(this.options.fileInput,{change:this._onChange});},_destroyEventHandlers:function(){this._off(this.options.dropZone,'dragover drop');this._off(this.options.pasteZone,'paste');this._off(this.options.fileInput,'change');},_setOption:function(key,value){var refresh=$.inArray(key,this._refreshOptionsList)!==-1;if(refresh){this._destroyEventHandlers();}
this._super(key,value);if(refresh){this._initSpecialOptions();this._initEventHandlers();}},_initSpecialOptions:function(){var options=this.options;if(options.fileInput===undefined){options.fileInput=this.element.is('input[type="file"]')?this.element:this.element.find('input[type="file"]');}else if(!(options.fileInput instanceof $)){options.fileInput=$(options.fileInput);}
if(!(options.dropZone instanceof $)){options.dropZone=$(options.dropZone);}
if(!(options.pasteZone instanceof $)){options.pasteZone=$(options.pasteZone);}},_create:function(){var options=this.options;$.extend(options,$(this.element[0].cloneNode(false)).data());this._initSpecialOptions();this._slots=[];this._sequence=this._getXHRPromise(true);this._sending=this._active=this._loaded=this._total=0;this._initEventHandlers();},_destroy:function(){this._destroyEventHandlers();},add:function(data){var that=this;if(!data||this.options.disabled){return;}
if(data.fileInput&&!data.files){this._getFileInputFiles(data.fileInput).always(function(files){data.files=files;that._onAdd(null,data);});}else{data.files=$.makeArray(data.files);this._onAdd(null,data);}},send:function(data){if(data&&!this.options.disabled){if(data.fileInput&&!data.files){var that=this,dfd=$.Deferred(),promise=dfd.promise(),jqXHR,aborted;promise.abort=function(){aborted=true;if(jqXHR){return jqXHR.abort();}
dfd.reject(null,'abort','abort');return promise;};this._getFileInputFiles(data.fileInput).always(function(files){if(aborted){return;}
data.files=files;jqXHR=that._onSend(null,data).then(function(result,textStatus,jqXHR){dfd.resolve(result,textStatus,jqXHR);},function(jqXHR,textStatus,errorThrown){dfd.reject(jqXHR,textStatus,errorThrown);});});return this._enhancePromise(promise);}
data.files=$.makeArray(data.files);if(data.files.length){return this._onSend(null,data);}}
return this._getXHRPromise(false,data&&data.context);}});}));

(function(factory){'use strict';if(typeof define==='function'&&define.amd){define(['jquery'],factory);}else{factory(window.jQuery);}}(function($){'use strict';var counter=0;$.ajaxTransport('iframe',function(options){if(options.async){var form,iframe,addParamChar;return{send:function(_,completeCallback){form=$('<form style="display:none;"></form>');form.attr('accept-charset',options.formAcceptCharset);addParamChar=/\?/.test(options.url)?'&':'?';if(options.type==='DELETE'){options.url=options.url+addParamChar+'_method=DELETE';options.type='POST';}else if(options.type==='PUT'){options.url=options.url+addParamChar+'_method=PUT';options.type='POST';}else if(options.type==='PATCH'){options.url=options.url+addParamChar+'_method=PATCH';options.type='POST';}
iframe=$('<iframe src="javascript:false;" name="iframe-transport-'+
(counter+=1)+'"></iframe>').bind('load',function(){var fileInputClones,paramNames=$.isArray(options.paramName)?options.paramName:[options.paramName];iframe.unbind('load').bind('load',function(){var response;try{response=iframe.contents();if(!response.length||!response[0].firstChild){throw new Error();}}catch(e){response=undefined;}
completeCallback(200,'success',{'iframe':response});$('<iframe src="javascript:false;"></iframe>').appendTo(form);form.remove();});form.prop('target',iframe.prop('name')).prop('action',options.url).prop('method',options.type);if(options.formData){$.each(options.formData,function(index,field){$('<input type="hidden"/>').prop('name',field.name).val(field.value).appendTo(form);});}
if(options.fileInput&&options.fileInput.length&&options.type==='POST'){fileInputClones=options.fileInput.clone();options.fileInput.after(function(index){return fileInputClones[index];});if(options.paramName){options.fileInput.each(function(index){$(this).prop('name',paramNames[index]||options.paramName);});}
form.append(options.fileInput).prop('enctype','multipart/form-data').prop('encoding','multipart/form-data');}
form.submit();if(fileInputClones&&fileInputClones.length){options.fileInput.each(function(index,input){var clone=$(fileInputClones[index]);$(input).prop('name',clone.prop('name'));clone.replaceWith(input);});}});form.append(iframe).appendTo(document.body);},abort:function(){if(iframe){iframe.unbind('load').prop('src','javascript'.concat(':false;'));}
if(form){form.remove();}}};}});$.ajaxSetup({converters:{'iframe text':function(iframe){return iframe&&$(iframe[0].body).text();},'iframe json':function(iframe){return iframe&&$.parseJSON($(iframe[0].body).text());},'iframe html':function(iframe){return iframe&&$(iframe[0].body).html();},'iframe script':function(iframe){return iframe&&$.globalEval($(iframe[0].body).text());}}});}));

