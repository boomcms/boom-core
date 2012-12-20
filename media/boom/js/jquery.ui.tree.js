/*
 * jQuery UI Tree
 *
 * @Author Richard Willis
 *
 * @Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *
 */

(function( $ ) {

	$.widget('ui.tree', {

		options : {
			width: 320,
			theme: 'default',
			nodeDataURL: '',
			sortable: false,
			animateSpeed: 300,
			checkbox: false,
			icons: true,
			parentAsFolder: true
		},
		
		_create : function(){

			var self = this;
			
			this.theme = this.themes[ this.options.theme ];

			this.element
				.width( this.options.width )
				.addClass( this.theme.list );

			this._bind( this.element );
		},

		_bind : function( element ){

			var self = this, theme = this.theme;
			
			element
				.find( 'li' )
				.addClass( 'ui-helper-clearfix' )
				.find( 'a' ).each(function(){

					var list = $( this ).next();
					
					list =  ( list.length && list[0].nodeName == 'UL' ) ? list : false;
					
					var hitarea = self._buildHitarea( this, list );

					var icon = self._buildIcon( this, list, hitarea );

					var checkbox = self._buildCheckbox( this, list, hitarea, icon );
			
					if ( list ) {

						( !list.children().length ) && list.hide();

						//  bind custom even to list
						self._bindList( list, hitarea );
					}
					
					self._bindAnchor( this, list, hitarea );
				});
		},
		
		_buildHitarea : function( anchor, list ){

			var self = this, theme = this.theme, icon = 'ui-tree-icon-transparent';

			if ( list ){

				icon = ( list.is(':visible') && list.children().length ) ? 
					theme.icons.listopen : 
					theme.icons.listclosed;
			}

			return $('<span />')
				.addClass( theme.hitarea + ' ui-icon ' + icon )
				.data('list', list)
				.prependTo( anchor );
		},

		_buildIcon : function( anchor, list, hitarea ) {

			if ( !this.options.icons ) return;

			var icon = this.theme.icons['default'];

			if ( list && this.options.parentAsFolder ){
				icon = this.theme.folderCollapsed;
			}
			
			var element = 
				$('<span />')
				.addClass( 'ui-icon ' + icon );

			hitarea.after( element );

			return element;
		},

		_buildCheckbox : function( anchor, list, hitarea, icon ){

			if ( !this.options.checkbox ) return false;

			var element = 
				$('<input type="checkbox" />')
				.addClass( 'ui-helper-reset' + ' ' + this.theme.checkbox )
				.attr('id', $( anchor ).attr('rel') );

			var el = icon || hitarea;

			el.after( element );

			return element;
		},

		_bindList : function( list, hitarea ){

			var self = this;

			$( list )
			.data('hitarea', hitarea)
			.bind('toggle', function( event ){

				self._toggle( this, event );
			})
			.bind('open', function( event ){

				self._open( this, event );
			})
			.bind('close', function( event ){

				self._close( this, event );
			});
			
			if ( this.options.sortable && $.isFunction( $.fn.sortable ) ) {

				$([ this.element, this.element.find('ul') ]).sortable({
					placeholder: 'ui-state-highlight ui-tree-placeholder',
					connectWith: self.element.find('ul').not( this )
				}).disableSelection();
			}
		},

		_bindAnchor : function( anchor, list ){

			var self = this, theme = this.theme;

			$( anchor )
			.click(function( event ){

				// hitarea
				if ( new RegExp( theme.hitarea ).test( event.target.className ) ) {

					self._toggle( list, event );

					return false;
				}

				// checkbox
				else if ( new RegExp( theme.checkbox ).test( event.target.className ) ) {

					$( this ).toggleClass( theme.itemselected, $(event.target).is(':checked') );

					return;
				}

				else {

					$( this ).toggleClass( self.theme.itemselected );

					if ( self.options.checkbox ) {
				
						var checkbox = $( this ).find( '.' + theme.checkbox );
				
						if ( $( this ).hasClass( theme.itemselected ) ) {
							checkbox.attr('checked', 'checked');
						} else {
							checkbox.removeAttr('checked');
						}
					}
					
					self._trigger( 'click', event, this );

					return false;
				}
			})
			.hover(
				function(){

					$( this ).addClass( 'ui-state-hover ui-corner-all' );
				},
				function(){

					$( this ).removeClass( 'ui-state-hover' );
				}
			)
			.bind('toggle', function( event, tree ){

			});
		},

		_open : function( list, event ){

			list = $( list );

			var self = this, theme = this.theme, hitarea = list.data('hitarea');

			function open( hitarea ){

				hitarea.addClass( theme.icons.listopen );

				list.slideDown( self.options.animateSpeed );

				self._trigger('open', event, { list: list });
			}

			if ( !list.children().length && self.options.nodeDataURL ) {

				function complete( response, status, xhr ){

					if ( status == 'error' ) {

						alert('Error loading the request.');

						return;
					}

					hitarea.removeClass( 'ui-tree-load' );

					self._bind( list );

					open( hitarea );
				}

				hitarea.addClass( 'ui-tree-load' );

				list.hide().load( self.options.nodeDataURL, { page: hitarea.parent().attr('rel') || 0 }, complete );

			} else open( hitarea );
		},

		_close : function( list, event ){

			list = $( list );

			list
				.slideUp( this.options.animateSpeed )
				.data('hitarea')
					.removeClass( this.theme.icons.listopen )
					.addClass( this.theme.icons.listclosed );

			this._trigger('close', event, { list: list });
		},

		_toggle : function( list, event ){

			list = $( list );

			if ( list.length ){

				list.is(':visible') ? this._close( list, event ) : this._open( list, event );
			}
		},

		selected : function(){

			return this.element.find( '.' + this.theme.itemselected );
		},

		expand : function(){
	
			this.element.find( 'ul' ).trigger( 'open' );
		},

		collapse : function(){

			this.element.find( 'ul' ).trigger( 'close' );
		},

		destroy : function(){

			this.element
				.removeClass( this.theme.list )
				.find( 'li' )
					.removeClass( 'ui-helper-clearfix' )
				.find( 'a' )
					.removeClass( this.theme.itemselected + ' ui-corner-all' )
					.unbind()
				.find( '.ui-icon' )
					.remove()
					.end()
				.find( '.' + this.theme.checkbox )
					.remove();

			$.Widget.prototype.destroy.apply(this, arguments);
		}
	});

	$.extend($.ui.tree.prototype, { 

		themes: {
			'default': { 
				list: 'ui-helper-reset ui-tree ui-widget ui-widget-content ui-corner ui-corner-all',
				hitarea: 'ui-tree-hitarea',
				folderCollapsed: 'ui-icon-folder-collapsed',
				folderOpen: 'ui-icon-folder-open',
				itemselected: 'ui-tree-item-active',
				checkbox: 'ui-tree-checkbox',
				icons: {
					'default' : 'ui-icon-document',
					'listopen': 'ui-icon-triangle-1-s',
					'listclosed': 'ui-icon-triangle-1-e'
				}
			},
			minimal: {
				list: 'ui-helper-reset ui-tree ui-widget ui-widget-content ui-corner ui-corner-all',
				hitarea: 'ui-tree-hitarea',
				folderCollapsed: 'ui-icon-folder-collapsed',
				folderOpen: 'ui-icon-folder-open',
				itemselected: 'ui-tree-item-active',
				checkbox: 'ui-tree-checkbox',
				icons: {
					'default' : 'ui-icon-document',
					'listopen': 'ui-icon-minus',
					'listclosed': 'ui-icon-plus'
				}
			}
		}
	});

})( jQuery );
