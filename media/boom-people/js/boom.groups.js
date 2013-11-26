/**
@fileOverview Group editor widget. Used to manage groups in the people manager.
*/
/**
* User interface for managing groups.
* @class
* @name $.boom.group_editor
*/
$.widget( 'boom.group_editor',
	/** @lends $.boom.group_editor */
	{
	/**
	default config
	@property options
	*/
	options: {
		/** URL prefix for ajax calls */
		base_url : '/cms/groups/',
		/** ID of the group being edited */
		id : null,
		/** base tree config */
		tree_config: $.boom.config.tree,
		/** people browser */
		browser: {}
	},

	_init: function() {
		$.boom.log( 'init group editor' );

		this._bind();
	},

	_bind: function() {

		$.boom.log( 'binding group editor' );

		var self = this;

		var editableTreeConfig = $.extend({}, self.options.tree_config, {
			showRemove: true,
			showEdit: true,
			onEditClick: function(event){

				self.edit(event);
			},
			onRemoveClick: function(event){

				self.remove(event);
			}
		});

		this.element
			.find('.b-people-group-add')
			.click(function( event ){
				self.add();
			})
			.end()
			.find('.b-tags-tree')
			.tree( editableTreeConfig );
	},

	/**
	Add a new group
	@function
	*/
	add: function(){

		var self = this;

		var url = self.options.base_url + 'add';

		var dialog = $.boom.dialog.open({
			url: url,
			title: 'Add group',
			callback: function(){
				$.post(url, {name: $('#b-people-group-name').val()} )
				.done( function(response){

					$.boom.growl.show('Group successfully saved, reloading.');

					window.setTimeout(function() {
						top.location.reload();
					}, 300);
				});
			}
		});
	},

	/** @function */
	save: function(group_id, data){

		return $.post( '/cms/groups/save/' + group_id, data );
	},

	/** @function */
	remove : function(event){

		var self = this;

		event.preventDefault();

		var item = $( event.target ).closest( 'li' );
		var rid = item.find('a')[0].rel;

		$.boom.dialog.confirm(
			'Please confirm',
			'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!'
		)
		.done( function(){

			$.post( self.options.base_url + 'delete/' + rid )
			.done( function(){
				$.boom.growl.show( 'Group successfully removed.' );
				item.remove();
			});
		});
	},

	/**
	Load group details in the main panel for editing
	@function
	*/
	edit : function( event ){

		var self = this;
		var item = $( event.target ).closest( 'li' );
		self.options.id = item.find('a').attr( 'rel' );

		event.preventDefault();

		return $.get(self.options.base_url + 'edit/' + self.options.id)
			.done(function(data) {
				self.options.browser.main_panel
					.find('.b-items-content')
					.html(data);

				$('#b-people-group-save').on('click', function() {
					$.post('/cms/groups/save/' + self.options.id, {name : $('#b-people-group-name').val()})
						.done(function() {
							$.boom.growl.show('Group successfully saved, reloading.');

							window.setTimeout(function() {
								top.location.reload();
							}, 300);
						})
						.fail(function() {
							$.boom.growl.show('Sorry, an error occurred.');
						});
				});

				self.options.browser.main_panel.group_permissions({
					base_url: self.options.base_url,
					id: self.options.id
				});
			});
	}
});

/**
* User interface for managing group permissions.
* @class
* @name $.boom.group_permissions
*/
$.widget( 'boom.group_permissions',
	/** @lends $.boom.group_permissions */
	{
	/**
	default config
	@property options
	*/
	options: {
		/** URL prefix for ajax calls */
		base_url : '/cms/groups/',
		/** ID of the group being edited */
		id : null
	},

	_init: function() {
		$.boom.log( 'init group permissions editor' );

		this._bind();
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

	_change: function( role_id, allowed, page_id ) {

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
			$.boom.growl.show('Permissions updated');
		});

	},

	_bind: function() {

		var self = this;
		var selected_page = null;
		var options = this.options;

		this.element
			.ui()
			.on( 'change', '#b-group-roles-general input[type=radio]', function( event ){

				var role_id = this.name;
				var allowed = parseInt( this.value, 10 );

				self._change( role_id, allowed, 0 );

			})
			.on( 'change', '#b-group-roles-pages input[type=radio]', function( event ){

				var role_id = this.name;
				var allowed = parseInt( this.value, 10 );
				var page_id = selected_page;

				self._change( role_id, allowed, page_id );

			});

			self._check_inputs( $( '#b-group-roles-general input[type=radio]'), -1 );

			$.get( options.base_url + 'list_roles/' + options.id + '?page_id=0' )
			.done( function( data ){
				for ( role in data ) {

					self._check_inputs( $( 'input[name=' + role + ']' ), data[ role ] );
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

		var page_tree = this.element.find( '#b-group-roles-pages .boom-tree' );

		page_tree.pageTree({
			onPageSelect : function(page) {
				selected_page = page.page_id;

				self._check_inputs( $( '#b-group-roles-pages input[type=radio]'), -1 );

				page_tree
					.find( 'a[rel=' + page.page_id + ']' )
					.parents( '.boom-tree' )
					.find( 'a.ui-state-active' )
					.removeClass( 'ui-state-active' )
					.end()
					.end()
					.addClass( 'ui-state-active' );

				$.get( options.base_url + 'list_roles/' + options.id + '?page_id=' + selected_page )
				.done( function( data ){
					for ( role in data ) {

						self._check_inputs( $( 'input[name=' + role + ']' ), data[ role ] );
					}
				});
			}
		});
	}
} );