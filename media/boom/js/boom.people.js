/**
@class
*/
$.boom.people = {};

/**
@class
@extends $.boom.filter
*/
$.boom.people.group = {};

/**
@class
@extends $.boom.item
*/
$.boom.person = {};

$.extend($.boom.person, $.boom.item, {
	/** 
	@lends $.boom.person 
	*/
	
	base_url: '/cms/people/view/',
	
	type: 'person',

	/** @function */
	bind: function( context){

		var self = this;

		$.boom.dialog.bind({
			image: $('.boom-asset-preview', context )
		});

		$('.b-people-groups-add', context ).click(function(){
			var rid = $( this ).attr( 'rel' );

			var dialog = $.boom.dialog.open({
				url: '/cms/people/add_group/' + rid,
				title: 'Add group',
				callback: function(){

					var dialog = this;
					var data = $( dialog ).find('form').serialize();
					$.boom.loader.show();

					$.post('/cms/people/add_group/' + rid, data )
					.done( function(){

						$.boom.loader.hide();
					});
				}
			});
		});

		$('.b-people-group-delete', context ).click(function(){
			var elem = $( this );
			var group_id = elem.attr( 'rel' );
			var person_id = elem.closest( 'div' ).attr( 'rel' );

			$.boom.loader.show();

			$.post( '/cms/people/remove_group/' + person_id, {groups: group_id} )
			.done( function(){
				elem.closest( 'li' ).remove();
				$.boom.loader.hide();
			});
		});

		$('.b-people-save', context ).bind('save', function( event ){

			var rid = $( this ).attr( 'rel' );
			var data = $( this ).closest( 'form' ).serialize();

			$.boom.loader.show();

			$.post('/cms/people/save/' + rid, data)
			.done( function(){

				$.boom.loader.hide();
				$.boom.growl.show( "Person saved." );
			});

		}).click(function(){

			$( this ).trigger( 'save' );
		});

		$('#b-delete-person', context ).click(function( event ){

			var rid = $( this ).attr( 'rel' );
			var deleted = new $.Deferred();

			$.boom.dialog.open({
				width: 350,
				msg: 'Are you sure you want to delete this person?',
				title: 'Please confirm',
				deferred: deleted
			});

			deleted
			.pipe( function( event ){

				$.boom.loader.show();

				return $.post('/cms/people/delete', { people: rid });
			})
			.done( function(){

				var items = $.boom.history.getHash().split('/')[1].split(',');

				$.boom.loader.hide();

				if ( items.length > 1 ){

					var segments =
						$.boom.history.getHash().split('/')[0] +
						'/' +
						$.grep(items, function(val){

							return val != rid;

						}).join(',');

					$.boom.history.load( segments );

				} else {

					//self.browser.defaultRoute();
				}
			});
		});
	}
});

$.extend($.boom.people.group, $.boom.filter,  {
	/** 
	@lends $.boom.people.group 
	*/


	/** @function */
	build_url : function(){

		var self = this;

		params =
			'tag=' + self.rid + '&' +
			'perpage=' + self.options.perpage + '&' +
			'sortby=' + self.options.sortby + '&' +
			'order='  + self.options.order;

		var url =
			'/cms/' + self.options.type + '/list'
			+ '?' + params;
			
		$.boom.log('Group items get' + self.rid );
		
		return url;
	},

	/** @function */
	bind : function( context ){

		

		$('.b-items-thumbs .thumb', context ).captions($.boom.config.captions);

	},

	/** @function */
	edit : function(event, browser){
		
		event.preventDefault();

		var self = this;
		var item = $( event.target ).closest( 'li' );
		var rid = item.find('a').attr( 'rel' );
		
		return browser.sidebar.group_editor( 'edit', browser, rid );

	},

	/** @function */
	remove : function(event){

		browser.sidebar.group_editor( 'remove', event );
	}
});

/**
* User interface for browsing and managing people.
* @class
* @name $.boom.browser_people
* @extends $.boom.browser
*/
$.widget( 'boom.browser_people', $.boom.browser, {
	/** @lends $.boom.browser_people */
	
	/**
	map url fragments to objects
	@property
	*/
	url_map : {
		person: $.boom.person,
		group: $.boom.people.group
	},
	
	options: {
		sortby: 'name',
		order: 'asc',
		defaultRoute: 'group/0', 
		type: 'people',
		treeConfig : {
			showEdit: true,
			showRemove: true
		}
	},
	
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
		self.sidebar.group_editor();

		$('#boom-tagmanager-create-person').click(function(){
			var dialog = $.boom.dialog.open({
				url: '/cms/people/add',
				title: 'Create new person',
				onLoad: function(){

					$('#boom-tagmanager-create-person-form input[name="name"]').focus();
				},
				callback: function(){
					self
						.savePerson('/cms/people/add')
						.done( function(){
							window.location.reload();
						});
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
	},
	
	/** @function */
	savePerson: function(url){

		// TODO: validation

		var data = $('#boom-tagmanager-create-person-form').serialize();

		$.boom.loader.show();

		return $.post(url, data)
		.done( function(id){

			$.boom.loader.hide();

		});
	}
});
