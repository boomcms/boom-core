/**
* User interface for managing groups.
* @class
* @name $.boom.group_editor
*/
$.widget( 'boom.group_editor', {
	/** @lends $.boom.group_editor */
	
	/**
	default config
	@property
	*/
	options: {
		/** URL prefix for ajax calls */
		base_url : '/cms/groups/',
		/** ID of the group being edited */
		id : null
	},

	_init: function() {
		$.boom.log( 'init group editor' );
		
		this._bind();
	},
	
	_bind: function() {
		
		$.boom.log( 'binding group editor' );
		
		var self = this;
		
		this.element
			.find('.b-people-group-add')
			.click(function( event ){
				self.add();
			});
		
		self.permissions._bind( this );
	},
	
	_check_inputs: function( radio_buttons, value ) {
		
		radio_buttons
			.filter( ':checked' )
			.prop( 'checked', false )
			.removeAttr( 'checked' )
			.end()
			.filter( '[value=' + value + ']' )
			.prop( 'checked', true )
			.attr( 'checked', 'checked' );
		
	},
	
	_change_permissions: function( role_id, allowed, page_id ) {
		
		var self = this;
		
		return $.post(
			self.options.base_url + 'remove_role/' + self.options.id,
			{
				role_id : role_id,
				page_id : page_id
			}
		)
		.pipe( function( response ){
			if ( allowed == -1 ){
				console.log( this );
				return this;
			}
			return $.post(
				self.options. base_url + 'add_role/' + self.options.id,
				{
					role_id : role_id,
					allowed : allowed,
					page_id: page_id
				}
			);
		})
		.done( function( response ){
		});
		
	},
	
	/**
	Add a new group 
	@function 
	*/
	add: function(){

		var self = this;

		var url = self.options.base_url + 'add/';

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
	
	/**
	Load group details in the main panel for editing 
	@function 
	*/
	edit : function( browser, group_id){
		
		$.boom.loader.show();

		return browser.main_panel
		.find('.b-items-content')
		.sload( '/cms/groups/edit/' + rid, function(){

			$.boom.loader.hide();
			

			this.options.id = group_id;
			this.permissions._bind( this );
		} );
	},
	
	/**
	@property
	*/
	permissions: {
		
		_bind: function( editor ) {

			var self = this;
			var selected_page = null;

			$.boom.loader.hide();


			editor.element
			.ui()
			.on( 'change', '#b-group-roles-general input[type=radio]', function( event ){

				var role_id = this.name;
				var allowed = parseInt( this.value, 10 );

				editor._change_permissions( role_id, allowed, 0 );

			})
			.on( 'change', '#b-group-roles-pages input[type=radio]', function( event ){

				var role_id = this.name;
				var allowed = parseInt( this.value, 10 );
				var page_id = selected_page;

				editor._change_permissions( role_id, allowed, page_id );

			});

			editor._check_inputs( $( '#b-group-roles-general input[type=radio]'), -1 );

			$.get( editor.options.base_url + 'list_roles/' + editor.options.id + '?page_id=0' )
			.done( function( data ){
				for ( role in data ) {

					editor._check_inputs( $( 'input[name=' + role + ']' ), data[ role ] );
				}
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

			var page_tree = editor.element.find( '#b-group-roles-pages .boom-tree' );

			$.boom.util.page_tree(  page_tree )
				.progress( function( page ) {

					selected_page = page.page_id;

					editor._check_inputs( $( '#b-group-roles-pages input[type=radio]'), -1 );

					page_tree
						.find( 'a[rel=' + page.page_id + ']' )
						.parents( '.boom-tree' )
						.find( 'a.ui-state-active' )
						.removeClass( 'ui-state-active' )
						.end()
						.end()
						.addClass( 'ui-state-active' );

					$.get( editor.options.base_url + 'list_roles/' + editor.options.id + '?page_id=' + selected_page )
					.done( function( data ){
						for ( role in data ) {

							editor._check_inputs( $( 'input[name=' + role + ']' ), data[ role ] );
						}
					});

				});

		}
	}
});