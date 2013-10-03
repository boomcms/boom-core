/**
@fileOverview jQuery plugins written specifically for Boom.
*/
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
	@name $.ui.splitbutton
	@extends $.ui
	*/
	$.widget('ui.splitbutton',
		/** @lends $.ui.splitbutton */
		{
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
			tooltip: $.boom.config.tooltip,
			tree: $.boom.config.tree,
			sortable: $.boom.config.sortable,
			datepicker: $.boom.config.datepicker
		}, opts);

		var elems = '.boom-button, .boom-buttonset, .boom-tabs, .boom-tooltip, .boom-tree, .boom-sortable, .boom-datepicker, .boom-toggleinput, .boom-splitbutton, .boom-help';

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
	@class
	@name $.ui.tree
	@extends $.ui
	*/
	$.widget('ui.tree',
		/** @lends $.ui.tree */
		{
		/**
		@property options
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
			}

			if (!childList.length) {

				hitarea.addClass(this.options.iconHitareaPlaceholder);

			} else if (childList.is(':hidden')) {
				hitarea
					.addClass(this.options.iconHitareaClosed)
					.removeClass(this.options.iconHitareaOpen);

			} else {
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

