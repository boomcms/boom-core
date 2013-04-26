$.boom.people = {};

/**
@class
*/
$.boom.people.group = {};

/**
@class
*/
$.boom.person = {};

$.extend($.boom.person, $.boom.item, {
	/** @lends $.boom.person */
	
	base_url: '/cms/people/view/',

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
	/** @lends $.boom.people.group */


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

		var self = this;

		$('.b-items-select-checkbox', context ).change(function(){

			var view =
				this.id.replace(/^[a-z]+-([a-z]+)-[0-9]+$/, "$1") == 'list' ? 'thumb' : 'list',
				type =	this.id.replace(/^([a-z]+)-.*$/, "$1"),
				selector = $( '#' + type + '-' + view + '-' + this.id.replace(/[a-z]+-[a-z]+-/, ''));

			if ( $( this ).is(':checked')) {

				selector.attr('checked', 'checked');

			} else {

				selector.removeAttr('checked');
			}

			var amount = $('.b-items-select-checkbox:checked').length;

			var buttons = $('#b-button-multiaction-edit, #b-button-multiaction-delete, #b-button-multiaction-download');

			buttons.button( amount > 0 ? 'enable' : 'disable' );

			$('#boom-tagmanager-amount-checked').html( amount === 0 ? '' : amount / 2 );
		});

		$('.b-items-list tbody tr, .b-items-thumbs .thumb', context ).hover(
			function(){
				$( this ).addClass( 'ui-state-hover' );
			},
			function(){
				$( this ).removeClass( 'ui-state-hover' );
			}
		);

		$('.b-items-thumbs .thumb', context ).captions($.boom.config.captions);

	},

	/** @function */
	edit : function(event, browser){
		event.preventDefault();

		var self = this;
		var item = $( event.target ).closest( 'li' );
		var rid = item.find('a').attr( 'rel' );
		var selected_page = null;

		return browser.main_panel
		.find('.b-items-content')
		.sload( '/cms/groups/edit/' + rid, function(){

			$.boom.loader.hide();
			

			this
			.ui()
			.on( 'change', '#b-group-roles-general input[type=radio]', function( event ){

				var role_id = this.name;
				var allowed = this.value;

				$.post(
					'/cms/groups/remove_role/' + rid,
					{
						role_id : role_id
					}
				)
				.pipe( function( response ){
					return $.post(
						'/cms/groups/add_role/' + rid,
						{
							role_id : role_id,
							allowed : allowed,
							page_id: 0
						}
					);
				})
				.done( function( response ){
				});
			})
			.on( 'change', '#b-group-roles-pages input[type=radio]', function( event ){

				var role_id = this.name;
				var allowed = parseInt( this.value, 10 );
				var page_id = selected_page;

				$.post(
					'/cms/groups/remove_role/' + rid,
					{
						role_id : role_id,
						page_id : page_id
					}
				)
				.pipe( function( response ){
					if ( allowed == -1 ){
						return this.resolve();
					}
					return $.post(
						'/cms/groups/add_role/' + rid,
						{
							role_id : role_id,
							allowed : allowed,
							page_id: page_id
						}
					);
				})
				.done( function( response ){
				});
			});

			/**
			 * Clicking on a page in the tree.
			 * Should make a GET call to /cms/groups/list_roles/<group ID>?page_id=<page ID>
			 *
			 * This will return a json encoded array of role ID => <value>
			 * Possible values are 1 if the role is allowed and 0 if the role is disallowed.
			 * If nothing is set for a role then that role ID won't be in the returned array.
			 *
			 * The role checkboxes should then be updated if the correct values.
			 */
			
			$.boom.util.page_tree( this.find( '#b-group-roles-pages .boom-tree' ) )
				.progress( function( page ) {
					
					selected_page = page.page_id;
					
					$( '#b-group-roles-pages input[type=radio]')
						.filter( ':checked' )
						.prop( 'checked', false )
						.removeAttr( 'checked' )
						.end()
						.filter( '[value="-1"]' )
						.prop( 'checked', true )
						.attr( 'checked', 'checked' );

					$( '#b-group-roles-pages .boom-tree a[rel=' + page.page_id + ']' )
						.parents( '.boom-tree' )
						.find( 'a.ui-state-active' )
						.removeClass( 'ui-state-active' )
						.end()
						.end()
						.addClass( 'ui-state-active' );

					$.get( '/cms/groups/list_roles/' + rid + '?page_id=' + page.page_id )
					.done( function( data ){
						for ( role in data ) {
							$( 'input[name=' + role + ']' )
								.filter( ':checked' )
								.prop( 'checked', false )
								.removeAttr( 'checked' )
								.end()
								.filter( '[value=' + data[ role ] + ']' )
								.prop( 'checked', true )
								.attr( 'checked', 'checked' );
						}
					});
					
				});
		} );
	},

	/** @function */
	add: function(event){

		var self = this;

		var url = '/cms/groups/add';

		var dialog = $.boom.dialog.open({
			url: url,
			title: 'Add group',
			callback: function(){
				$.post(url, {name: $('#b-people-group-name').val()} )
				.done( function(response){

					$.boom.growl.show('Group successfully saved.');

					top.location.reload();
				});
			}
		});
	},

	/** @function */
	save: function(group_id, data){
		$.boom.loader.show();

		return $.post( '/cms/groups/save/' + group_id, data )
		.done( function(response){

			$.boom.loader.hide();

		});
	},

	/** @function */
	remove : function(event){

		event.preventDefault();

		var item = $( event.target ).closest( 'li' );
		var rid = item.find('a')[0].rel;

		$.boom.dialog.confirm(
			'Please confirm',
			'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!'
		)
		.done( function(){

			$.boom.loader.show();

			$.post( '/cms/groups/delete/' + rid )
			.done( function(){

				$.boom.loader.hide();

				$.boom.growl.show( 'Group successfully removed.' );
				item.remove();
			});
		});
	},

	/** @function */
	update_tree : function( selector, permission ) {
		$.boom.log( 'updating permissions ' + selector );

		var new_item = '<li class=\'boom-group-permission\' data-permission=\'' + permission.value + '\'>' + permission.label + '</li>';

		$( selector )
		.tree( 'add_item', new_item );

	}
});

/**
* User interface for browsing and managing people.
* @class
* @name $.boom.browser_people
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

		$('.b-people-group-add').click(function(event){
			$.boom.people.group.add( this );
		});

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
