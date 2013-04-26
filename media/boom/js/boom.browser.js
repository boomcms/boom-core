/**
@class
*/
$.boom.item = {};

$.extend($.boom.item, {
	/** @lends $.boom.item */

	/** @function */
	get: function( rid ){

		var self = this;

		this.rid = rid;

		var url = this.base_url + rid;
		
		return $.get( url );

	},
	
	/** @function */
	select : function( rid, selected ){

		var thumb = '#' + this.type + '-thumb-' + rid;
		var list = '#' + this.type + '-list-' + rid;

		var checkbox = $( thumb );
		checkbox.prop( 'checked', selected );

		if ( selected ) {

			checkbox.attr('checked', 'checked');

			checkbox.parents( 'div.thumb' ).addClass( 'ui-state-active' );
			$( list ).parents( 'tr' ).addClass( 'ui-state-active' );

		} else {

			checkbox.removeAttr('checked');

			checkbox.parents( 'div.thumb' ).removeClass( 'ui-state-active' );
			$( list ).parents( 'tr' ).removeClass( 'ui-state-active' );
		}
		
	}

});

/**
@class
*/
$.boom.filter = {};

$.extend($.boom.filter, {
	/** @lends $.boom.filter */
	
	/** @function */
	get : function( rid ){

		var self = this;
		var options = this.options;

		this.rid = rid;

		options.url = this.build_url();
		
		return $.get( options.url );
	}
});

/**
* User interface for browsing and managing content items, eg. assets and people.
* @class
* @name $.boom.browser
*/
$.widget( 'boom.browser', {
	/** @lends $.boom.browser */
	
	/**
	default config
	@property
	*/
	options : {
		sortby: 'audit_time',
		order: 'desc',
		defaultRoute: 'tag/0', 
		selected: [],
		types: [],
		page: 1,
		perpage: 30,
		excludeSmartTags: 0,
		template: 'list',
		treeConfig: {
			border: true,
			height: 'auto',
			overflow: 'hidden',
			toggleSelected: false,
			width: 278
		}
	},
	
	_create : function(){
		
		$.boom.log( 'content browser init' );
		
		this._set_tag_options({
			perpage: this.options.perpage,
			sortby : this.options.sortby,
			order : this.options.order,
			type: this.options.type
		});

		this.main_panel = $('.b-items-rightpane');
		this.sidebar = $('.b-items-leftpane');

		
		this._bind();
		
		this.route();
	},
	
	_init : function(){
		
	},
	
	_destroy : function(){
		
	},
	
	_bind: function(){
		var self = this;
		
		$('.b-items-sidebar h3').click(function(){

			$(this).parent().next().toggle();
		});
		
		$( '#tag_all' )
			.on( 'click', function( event ){
				self.tag.filters = {};
				$.boom.history.load( self.options.defaultRoute );
				
				$(this)
					.parent()
					.siblings( '.boom-tree-container' )
					.find( 'a.ui-state-active' )
					.removeClass( 'ui-state-active' );
				
				return false;
			});
			
		var tag_name  = self.options.defaultRoute.split( '/' )[ 0 ];
		
		var item_selected = function( $item ){
			$( '#tag_all' ).removeClass( 'ui-state-active' );
			
			$.boom.log( 'adding active class' );
			$item
				.parents( '.b-tags-tree' )
				.find( 'a.ui-state-active' )
				.removeClass( 'ui-state-active' )
				.end()
				.end()
				.addClass( 'ui-state-active' );
		};
		
		var multi_select = function( $item ){
			var tags = [];
			
			$item.toggleClass( 'ui-state-active' );
			$item
				.parents( 'ul:first' )
				.find( 'a.ui-state-active' )
				.not( $item )
				.removeClass( 'ui-state-active' );
			
			$item
				.parents( '.boom-tree' )
				.find( 'a.ui-state-active' )
				.each( function(){
					var tag = $(this)
						.attr( 'href' )
						.split( '/' );
					tags.push( tag );
				});
			
			return tags;
		};
		
		var treeConfig = $.extend({}, $.boom.config.tree, {
			toggleSelected: false,
			click: false,
			onClick: function(event){
				$this = $(this);

				var tags = multi_select( $this );

				self.tag.filters = {};
				
				for ( t in tags ) {
					var tag = tags[ t ];
					
					switch( tag[ 0 ] ) {
						
						case '#tag': case '#group':
							self.tag.rid = tag[ 1 ];
							break;
						default:
							var name = tag[ 0 ].replace( '#', '' );
							self.tag.filters[ name ] = tag[ 1 ];
					}
				}

				$.boom.history.load( tag_name + '/' + self.tag.rid );
				return false;
			}
			
		});
		
		$( '.boom-filter-tree' )
			.tree( treeConfig );
			
		var editableTreeConfig = $.extend({}, treeConfig, {
			maxSelected: 1,
			toggleSelected: false,
			preventDefault: true,
			onClick: function(event){
				$this = $(this);
				item_selected( $this );
				
				self.tag.rid = 
					$this
						.attr( 'href' )
						.split('/')
						[1];

				$.boom.history.load( tag_name + '/' + self.tag.rid );
				return false;
			}
		});
		
		editableTreeConfig = $.extend({}, editableTreeConfig, {
			showRemove: true,
			showEdit: true,
			onEditClick: function(event){
				
				self.tag.edit(event, self);
			},
			onRemoveClick: function(event){

				self.tag.remove(event);
			}
		});
		
		$('.b-tags-tree')
			.tree(editableTreeConfig);
			
		self.main_panel
			.on( 'change', '#boom-tagmanager-sortby-select', function( event ){
				self.tag.options.sortby = this.value;
				$.boom.history.refresh();
			})
			.on( 'change', '.b-items-select-checkbox', function( event ){
				// checkbox IDs are of the form type-view-id.
				var item = this.id.split( '-' );
				var view = item[ 1 ];
				var type = item[ 0 ];
				var item_id = item[ 2 ];

				self.select( item_id, $( this ).is(':checked') );



				var amount = $('.b-items-select-checkbox:checked').length;

				var buttons = $( '[id|=b-button-multiaction]' ).not( '#b-button-multiaction-edit' );

				$( '#b-button-multiaction-edit' ).button( (  amount && amount < 3) ? 'enable' : 'disable' );

				buttons.button( amount > 0 ? 'enable' : 'disable' );
			})
			.on( 'mouseenter focus', '#b-items-view-list tbody tr, #b-items-view-thumbs a', function( event ){
				$( this ).addClass( 'ui-state-hover' );
			})
			.on( 'mouseleave blur', '#b-items-view-list tbody tr, #b-items-view-thumbs a', function( event ){
				$( this ).removeClass( 'ui-state-hover' );
			})
			.on( 'click', '.boom-pagination a', function( e ){
				e.preventDefault();

				//$.boom.history.load( '/cms/assets/list?' + $( this ).attr( 'href' ).split( '?' )[ 1 ] );
				$.get( '/cms/assets/list?' + $( this ).attr( 'href' ).split( '?' )[ 1 ])
				.done( function( data ){
					var $data = $( data );
					var pagination = $data.find( '.boom-pagination' ).html();
					var list = $data.find( '#b-items-view-list' ).html();
					var thumbs = $data.find( '#b-items-view-thumbs' ).html();
					$( self.main_panel )
						.find( '.boom-pagination' )
						.html( pagination )
						.end()
						.find( '#b-items-view-list' )
						.html( list )
						.end()
						.find( '#b-items-view-thumbs' )
						.html( thumbs );
				});

				return false;
			});
		
	},
	
	/** set navigation options. */
	_set_tag_options : function( options ){
		this.tag.options = options;
	},
	/** Default history routing. */
	defaultRoute: function(){

		$.boom.history.load( this.options.defaultRoute);
	},

	/** Map URL fragment #{item}/{id} to get method call $.boom.{item}.get( id ); */
	route : function(){

		var self = this;
	
		$.boom.history.route(
			function(segments){
				segments = segments.split('/');

				var 
					item = segments[0], 
					rid = segments[1];
				
				if ( item.length && self.url_map[ item ] ) {
					
					$.boom.loader.show();
					
					return self.url_map[ item ]
						.get( rid )
						.done( function( response ){
							
							$.boom.loader.hide();
							
							self.main_panel
								.find( '.b-items-content' )
								.html( response )
								.ui();
							self.url_map[ item ].bind( self.main_panel );
						});
				}
			}, 
			function(){

				self.defaultRoute();
			}
		);
	}
});
