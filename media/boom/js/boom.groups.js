/**
* User interface for managing groups.
* @class
* @name $.boom.group_editor
*/
$.widget( 'boom.group_editor', {
	
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

	_create: function() {
		
		this._bind();
	},
	
	_bind: function() {
		
		var self = this;
		var selected_page = null;
		
		$.boom.loader.hide();
		

		this.element
		.ui()
		.on( 'change', '#b-group-roles-general input[type=radio]', function( event ){

			var role_id = this.name;
			var allowed = this.value;

			this._change_permissions( role_id, allowed, 0 );
			
		})
		.on( 'change', '#b-group-roles-pages input[type=radio]', function( event ){

			var role_id = this.name;
			var allowed = parseInt( this.value, 10 );
			var page_id = selected_page;

			this._change_permissions( role_id, allowed, page_id );
			
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
		
		$.boom.util.page_tree( this.element.find( '#b-group-roles-pages .boom-tree' ) )
			.progress( function( page ) {
				
				selected_page = page.page_id;
				
				self._check_inputs( $( '#b-group-roles-pages input[type=radio]') )
					.filter( '[value="-1"]' );

				$( '#b-group-roles-pages .boom-tree a[rel=' + page.page_id + ']' )
					.parents( '.boom-tree' )
					.find( 'a.ui-state-active' )
					.removeClass( 'ui-state-active' )
					.end()
					.end()
					.addClass( 'ui-state-active' );

				$.get( self.options.base_url + 'list_roles/' + self.options.id + '?page_id=' + selected_page )
				.done( function( data ){
					for ( role in data ) {
						
						self._check_inputs( $( 'input[name=' + role + ']' ) )
							.filter( '[value=' + data[ role ] + ']' );
					}
				});
				
			});
		
	},
	
	_check_inputs: function( radio_buttons ) {
		
		return radio_buttons
			.filter( ':checked' )
			.prop( 'checked', false )
			.removeAttr( 'checked' )
			.end()
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
				return this.resolve();
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
		
	}
});