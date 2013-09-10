/**
@fileOverview People manager UI, including groups and person view.
*/

/**
@class
@extends $.boom.item
*/
$.boom.person = {};

$.extend($.boom.person, $.boom.item,
	/**
	@lends $.boom.person
	*/
	{

	base_url: '/cms/people/',

	type: 'person',

	/** @function */
	bind: function( context){

		var self = this;

		this.groups.person_id = this.rid;

		$.boom.dialog.bind({
			image: $('.boom-asset-preview', context )
		});

		$('.b-people-groups-add', context ).click(function(){

			self.groups.add();
		});

		$('.b-people-group-delete', context ).click(function(){
			var $this = $( this );
			var $li = $this.closest( 'li' );
			var group_id = $this.attr( 'rel' );

			self.groups.remove( group_id )
			.done( function(){
				$li.remove();
			});


		});

		$('.b-people-save', context ).bind('save', function( event ){

			var data = $( '#boom-person-view > form' ).serialize();

			self
				.save( data )
				.done( function(){
					$.boom.growl.show( "Person saved." );
				});

		}).click(function(){

			$( this ).trigger( 'save' );
		});

		$('#b-delete-person', context ).click(function( event ){

			self.remove()
				.done( function(){
					$.boom.history.load( 'group/0' );
				});

		});
	},

	/**
	Handle UI for adding and removing groups from a person
	@static
	@class
	*/
	groups: {

		/**
		@property person_id
		*/
		person_id: null,

		/** @function */
		add: function(){

			var self = this;

			var dialog = $.boom.dialog.open({
				url: '/cms/people/add_group/' + self.person_id,
				title: 'Add group',
				callback: function(){

					var dialog = this;
					var data = $( dialog ).find('form').serialize();
					$.boom.loader.show();

					return $.post('/cms/people/add_group/' + self.person_id, data )
					.done( function(){

						$.boom.loader.hide();
						$.boom.history
							.load( 'person/' + self.person_id )
							.done( function(){
								$( '#boom-person-view ul[role=tablist] a:eq(2)' ).click();
							});
					});
				}
			});
		},

		/** @function */
		remove: function( group_id ){

			var self = this;

			$.boom.loader.show();

			return $.post( '/cms/people/remove_group/' + self.person_id, {group_id: group_id} )
				.done( function(){
					$.boom.loader.hide();
				});
		}
	}
});

/**
Filter lists of people by group.
@class
@extends $.boom.filter
*/
$.boom.filter_people = $.extend( {}, $.boom.filter, {

	base_url: '/cms/people/',

	type: 'group'
});

/**
* User interface for browsing and managing people.
* @class
* @name $.boom.browser_people
* @extends $.boom.browser
*/
$.widget( 'boom.browser_people', $.boom.browser,
	/** @lends $.boom.browser_people */
	{

	/**
	@see $.boom.config.browser_people
	*/
	options: $.boom.config.browser_people,

	_create : function(){

		$.boom.log( 'people browser init' );

		var self = this;

		self.item = $.boom.person;
		self.tag = $.boom.filter_people;

		$.boom.browser.prototype._create.call( this );

	},

	_bind: function(){
		$.boom.log( 'people browser bind' );

		$.boom.browser.prototype._bind.call( this );

		var self = this;

		self.treeConfig = $.extend({}, $.boom.config.tree, {
			toggleSelected: false,
			click: false,
			onClick: function(event){
				$this = $(this);

				var tags = multi_select( $this );

				self.tag.set_filters( tags );

				$.boom.history.load( tag_name + '/' + self.tag.rid );
				return false;
			}

		});

		$( '.boom-filter-tree' )
			.tree( self.treeConfig );

		self.editableTreeConfig = $.extend({}, self.treeConfig, {
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

		var tag_name  = self.options.defaultRoute.split( '/' )[ 0 ];

		self.sidebar.group_editor({
			tree_config: self.editableTreeConfig,
			browser: self
		});

		$('#boom-tagmanager-create-person').click(function(){
			var dialog = $.boom.dialog.open({
				url: '/cms/people/add',
				title: 'Create new person',
				callback: function(){

					var data = $('#boom-tagmanager-create-person-form').serialize();

					$.boom.person.add( data )
						.done(function() {
							$.boom.growl.show('Success');
							$.boom.history.refresh();
						})
						.fail(function() {
							$.boom.growl.show('Failure');
						});
				}
			});
		});

		$('#b-topbar')
			.on('click', '#b-button-multiaction-edit', function(){

				var ids = [];

				$('.b-items-select-checkbox:checked').each(function(){

					var id = this.id.replace(/person-(thumb|list)-/, '');

					if ( $.inArray(id, ids) === -1 ) {

						ids.push( id );
					}
				});

				$.boom.history.load('person/' + ids.join('-'));
			})
			.on('click', '#b-button-multiaction-delete', function(){

				var msg = 'Are you sure you want to send the selected people to the rubbish bin?';

				$.boom.dialog
					.confirm('Confirm deletion', msg)
					.done( function(){

						var people = [];

						$('.b-items-select-checkbox:checked').each(function(i){

							people.push( $( this ).attr( 'id' ).replace(/person-(thumb|list)-/, '') );
						});

						$.boom.loader.show();

						$.post('/cms/people/delete', {people: people}, function(){

							$.boom.loader.hide();

							$.boom.history.refresh();
						});
					});
			});

		self.main_panel
			.on( 'change', '#boom-tagmanager-sortby-select', function( event ){
				self.tag.options.sortby = this.value;
				$.boom.history.refresh();
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
						.html( thumbs )
						.end()
						.trigger('justify');
				});

				return false;
			});
	}
});
