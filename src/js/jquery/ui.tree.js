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
		iconRemove: 'ui-icon-remove-small',
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
			if ($ul.children().length) {
				var children = $ul.children( 'li' ).toArray();
				var i;

				for ( i in children ) {
					$this = $( children[i] );
					self._add_item( $this );
					render_children( $this.children( 'ul' ) );
				}
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
	set_toggle : function( $item ) {

		var self = this;

		$('<span />')
			.addClass('b-tree-toggle boom-tree-hitarea')
			.bind('boom-tree.toggle', function(){
				self.toggle( $item );
			})
			.click(function(event){
				self._trigger('toggle', event, { hitarea: this });

				$(this).toggleClass('expanded').trigger('boom-tree.toggle');
			})
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
			});

		return self;
	},

	/**
	@function
	*/
	_add_item : function( $item ) {

		var self = this;

		self._set_icon( $item );

		if ( !$item.find( '.boom-tree-hitarea' ).length) {

			self.set_toggle( $item );

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
		var id = $item.find('> a').attr('data-page-id');
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