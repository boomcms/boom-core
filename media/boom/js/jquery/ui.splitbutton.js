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

	isOpen : false,

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

			self.isOpen? self.close() : self.open();
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
					self.elements.button.offset().left + self.elements.button.outerWidth() :
					( offset.left + hitarea.outerWidth() ) - menu.outerWidth();

			menu
			.width( width )
			.css({
				left: left,
				top: offset.top
			});

			$.boom.page && $.boom.page.toolbar && $.boom.page.toolbar.maximise();
		})
		.bind('hide', function(){

			$(this).css({ left: -9999 });

			$.boom.page && $.boom.page.toolbar && $.boom.page.toolbar.minimise();
		});
	},

	/**
	@function
	*/
	open : function(){
		this.isOpen = true;
		this.elements.menu.trigger('show');
		this._trigger('open');
	},

	/**
	@function
	*/
	close : function(){
		this.isOpen = false;
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