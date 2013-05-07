/**
@fileOverview People manager UI, including groups and person view.
*/
/**
@namespace
*/
$.boom.people = {};

/**
Filter lists of people by group
@class
@extends $.boom.filter
*/
$.boom.people.group = {};

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

			self.save( data );

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

$.extend($.boom.people.group, $.boom.filter,
	/** 
	@lends $.boom.people.group 
	*/
	{


	/** @function */
	bind : function( context ){

		

		$('.b-items-thumbs .thumb', context ).captions($.boom.config.captions);

	}
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
	map url fragments to objects
	@property url_map
	*/
	url_map : {
		person: $.boom.person,
		group: $.boom.people.group
	},
	
	/**
	@see $.boom.config.browser_people
	*/
	options: $.boom.config.browser_people,
	
	_create : function(){
		
		$.boom.log( 'people browser init' );
		
		var self = this;
		
		this.tag = this.url_map.group;
		
		$.boom.browser.prototype._create.call( this );
		
	},
	
	_bind: function(){
		$.boom.log( 'people browser bind' );
		
		$.boom.browser.prototype._bind.call( this );
		
		var self = this;
		
		console.log( self.sidebar );
		self.sidebar.group_editor({
			tree_config: self.editableTreeConfig,
			browser: self
		});

		$('#boom-tagmanager-create-person').click(function(){
			var dialog = $.boom.dialog.open({
				url: '/cms/people/add',
				title: 'Create new person',
				onLoad: function(){

					$('#boom-tagmanager-create-person-form input[name="name"]').focus();
				},
				callback: function(){
					
					var data = $('#boom-tagmanager-create-person-form').serialize();
					
					$.boom.person.add( data );
				}
			});
		});

		$('#boom-topbar')
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
		
	},
	
	select: function( rid, selected ){
		$.boom.person.select( rid, selected );
	}
});
