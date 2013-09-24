/**
@fileOverview Generic item browser. Base classes for asset and people managers.
*/
/**
@class
*/
$.boom.item = {};

$.extend($.boom.item,
	/** @lends $.boom.item */
	{

	/**
	@property rid
	*/
	rid: null,

	/** @function */
	get: function( rid ){

		this.rid = rid;

		var url = this.base_url + 'view/' + rid;

		return $.get( url );

	},

	/** @function */
	add: function( data ){
		$.boom.loader.show();

		return $.post( this.base_url + 'add', data)
		.done( function(id){

			$.boom.loader.hide();

		});
	},

	/** @function */
	save: function( data ){
		$.boom.loader.show();

		return $.post( this.base_url + 'save/' + this.rid, data)
		.done( function(){

			$.boom.loader.hide();
		});
	},

	/** @function */
	remove: function(){

		var self = this;
		var deleted = new $.Deferred();

		$.boom.dialog.open({
			width: 350,
			msg: 'Are you sure you want to delete this ' + self.type +'?',
			title: 'Please confirm',
			deferred: deleted
		});

		return deleted
		.pipe( function( event ){

			$.boom.loader.show();

			return $.post( self.base_url + 'delete/' + self.rid, {csrf : $.boom.options.csrf} );
		})
		.done( function(){

			$.boom.loader.hide();

		});
	},
});

/**
Generate filtered lists of items in the browser.
@class
*/
$.boom.filter = {};

$.extend($.boom.filter,
	/** @lends $.boom.filter */
	{
	/** tag ID */
	rid: 0,

	/** filters */
	filters: {},

	/**
	Set search options.
	@param {Object} options.
	*/
	set_options : function( options ){

		this.options = options;

	},

	/**
	Set search filters from an array of tags.
	@param {Array} tags. Array of tags.
	*/
	set_filters : function( tags ){

		this.filters = {};

		for ( t in tags ) {
			var tag = tags[ t ];

			switch( tag[ 0 ] ) {

				case '#tag': case '#group':
					this.rid = tag.id;
					break;
				default:
					var name = tag.type.replace( '#', '' );
					this.filters[ name ] = tag.id;
			}
		}
	},

	/**
	Build AJAX request URL to return a filtered list of items
	@function
	*/
	build_url : function(){

		$.boom.log( 'get tag ' + this.rid );

		var self = this;

		params =
			'tag=' + self.rid + '&' +
			'perpage=' + self.options.perpage + '&' +
			'sortby=' + self.options.sortby;

		for ( filter in self.filters ) {
			params += '&' + filter + '=' + self.filters[ filter ];
		}

		var url =
			self.base_url + '/list'
			+ '?' + params;

		return url;
	},

	/**
	@function
	@returns {Deferred} ajax request returning a set of items.
	*/
	get : function( rid ){

		var self = this;
		var options = this.options;

		this.rid = rid;

		return $.get( this.build_url() );
	}
});

/**
* User interface for browsing and managing content items, eg. assets and people.
* @class
* @name $.boom.browser
*/
$.widget( 'boom.browser',
	/** @lends $.boom.browser */
	{

	/**
	default config
	@property options
	@see $.boom.config.browser
	*/
	options : $.boom.config.browser,

	selected : [],

	_create : function(){

		$.boom.log( 'content browser init' );

		this.url_map = {};

		this.url_map[ this.item.type ] = this.item;
		this.url_map[ this.tag.type ] = this.tag;

		this.tag.set_options({
			perpage: this.options.perpage,
			sortby : this.options.sortby,
			order : this.options.order
		});

		this.main_panel = $('.b-items-rightpane');
		this.sidebar = $('.b-items-sidebar');

		this._bind();

		this.route();
	},

	_init : function(){

	},

	_destroy : function(){

	},

	_bind: function(){
		$.boom.log( 'content browser bind' );

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
					tags.push( {
						type :	tag[0],
						id :	tag[1]
					} );
				});

			return tags;
		};
	},

	/** Default history routing. */
	defaultRoute: function(){

		$.boom.history.load( this.options.defaultRoute);
	},

	/** Map URL fragment #{item}/{id} to get method call this.url_map.{item}.get( id ); */
	route : function(){

		var self = this;

		$.boom.history.route(
			function(segments){
				segments = segments.split('/');

				var
					item = segments[0],
					rid = segments[1],
					instance = self.url_map[ item ];

				if ( item.length && instance ) {

					$.boom.loader.show();

					return instance
						.get( rid )
						.done( function( response ){

							$.boom.loader.hide();

							self.showContent(response);
							instance.bind( self.main_panel );
						});
				}
			},
			function(){

				self.defaultRoute();
			}
		);
	},

	/** @function */
	select : function(id) {
		var index = this.selected.indexOf(id);

		if (index == -1) {
			this.selected.push(id);
		} else {
			this.selected.splice(index, 1);
		}

		this.toggleButtons();
	},

	showContent : function(content) {
		this.main_panel
			.find( '.b-items-content' )
			.html( content )
			.ui();
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').button(this.selected.length == 1 ? 'enable' : 'disable');
		buttons.button(this.selected.length > 0 ? 'enable' : 'disable');
	}
});
