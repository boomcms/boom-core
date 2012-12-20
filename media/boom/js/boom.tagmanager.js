/**
@class
*/
$.boom.tagmanager = {
	/** @lends $.boom.tagmanager */

	/**
	Common properties and methods for site and CMS tagmanagers and items.
	@class
	*/
	base: {
		/** @lends $.boom.tagmanager.base */
		
		/**
		Common config, properties and methods for site and CMS tagmanagers.
		@class
		*/
		tagmanager: {
			options : {
				sortby: 'audit_time',
				order: 'desc',
				withinModal: false,
				edition: 'cms', 
				type: 'tag_manager',
				basetagRid: 0,
				defaultTagRid: 0, 
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

			elements: {}, 
			items: {},

			/** Default history routing. */
			defaultRoute: function(){

				$.boom.history.load('tag/' + this.options.defaultTagRid);
			},

			/** Route hashes to objects and methods. Typical tagmanager hash: asset/2 would execute $.boom.tagmanager.asset.get(2); */
			route : function(){

				var self = this;
			
				$.boom.history.route(
					function(segments){
						segments = segments.split('/');

						var 
							item = segments[0], 
							rid = segments[1], 
							method = 'get';
						
						if ( item.length && self.items[ item ] && self.items[ item ][ method ] ) {
							self.items[ item ][ method ]( rid );
						}
					}, 
					function(){

						self.defaultRoute();
					}
				);
			}
		},
		/**
		Abstract constructor for tagmanager item.
		*/
		item: function(){}
	}
};

/**
Site view tag manager
@class
@constructor
@name SiteTagmanager
@extends $.boom.tagmanager.base.tagmanager
@param type FIXME:?????
*/
function SiteTagmanager(type){

	var self = this;

	this.type = type;

	/**
	Init
	@function
	@name _init
	*/
	this._init = function(config){
		
		$.extend(config.items, {
			search: $.boom.items.search
		});

		$.extend(true, this, config);	

		$.each(this.items, function(){
	
			// create an attribute reference to the tagmanager
			this.tagmanager = self;
		});

		this.route();
		
	};
}

/**
CMS view tag manager
@class
@constructor
@name CMSTagmanager
@extends $.boom.tagmanager.base.tagmanager
@param type FIXME:?????
*/
function CMSTagmanager(type){
	
	var self = this;
	
	this.type = type;

	if (this.options.withinModal && top.$.boom.events){

		$.extend($.boom.events, top.$.boom.events);
	}
	
	/** 
	Init
	@function
	@name _init
	*/
	this._init = function(config){
		
		$.boom.log( 'CMS tag manager init' );
		
		$.extend(config.items, {
			search: $.boom.items.search
		});

		$.extend(true, this, config);
		
		$.each(this.items, function(){
		
			this.tagmanager = self;
		});

		this.elements.rightpane = $('.s-items-rightpane');
		this.elements.leftpane = $('.s-items-leftpane');

		$('.s-items-sidebar h3').click(function(){

			$(this).parent().next().toggle();
		});
		
		self.items.tag.item_selected = function( $item ){
			$( '#tag_all' ).removeClass( 'ui-state-active' );
			
			$item
				.parents( '.s-tags-tree' )
				.find( 'a.ui-state-active' )
				.removeClass( 'ui-state-active' )
				.end()
				.end()
				.addClass( 'ui-state-active' );
		};
		
		self.items.tag.multi_select = function( $item ){
			var tags = [];
			
			//$item.toggleClass( 'ui-state-active' );
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

				var tags = self.items.tag.multi_select( $this );

				self.items.tag.filters = {};
				
				for ( t in tags ) {
					var tag = tags[ t ];
					
					switch( tag[ 0 ] ) {
						
						case '#tag':
							self.items.tag.rid = tag[ 1 ];
							break;
						default:
							var name = tag[ 0 ].replace( '#', '' );
							self.items.tag.filters[ name ] = tag[ 1 ];
					}
				}

				self.items.tag.get( self.items.tag.rid );
			}
			
		});
		
		$( '.boom-tree' )
			.not( '.s-tags-tree' )
			.tree( 'destroy' )
			.tree( treeConfig );
			
		var editableTreeConfig = $.extend({}, treeConfig, {
			maxSelected: 1,
			toggleSelected: false,
			onClick: function(event){
				console.log('CLICK');
				$this = $(this);
				self.items.tag.item_selected( $this );
				
				self.items.tag.rid = 
					$this
						.attr( 'href' )
						.split('/')
						[1];

				self.items.tag.get( self.items.tag.rid );
			}
		});
		
		if (config.options.edition == 'cms')
		{
			editableTreeConfig = $.extend({}, editableTreeConfig, {
				showRemove: true,
				showEdit: true,
				onEditClick: function(event){
					
					self.items.tag.edit(event);
				},
				onRemoveClick: function(event){

					self.items.tag.remove(event);
				}
			});
		}
		
		$('.s-tags-tree')
			//.tree( 'destroy' )
			.tree(editableTreeConfig);

		$.boom.hooks.register('tag.clickAfter', 'tagmanager', function(){

			$( '#boom-tagmanager-sortby-select' ).change(function(event){

				self.options.sortby = this.value; 
						
				$.boom.history.refresh();
			});

			$( '#boom-tagmanager-order-select' ).change(function(event){

				self.options.order = this.value;
						
				$.boom.history.refresh();
			});
		});

		this.route();
	}
};

$.extend(SiteTagmanager.prototype, $.boom.tagmanager.base.tagmanager);

$.extend(CMSTagmanager.prototype, $.boom.tagmanager.base.tagmanager);
