/**
@class
@extends CMSTagmanager
*/
$.boom.people = new CMSTagmanager('people');

$.extend($.boom.people, {
	/** @lends $.boom.people */

	/** @function */
	init: function(config){

		$.boom.log( 'people init' );

		var self = this;

		this._init( config );

		$('.b-people-group-add').click(function(event){
			$.boom.items.group.add( this );
		});

		$('#boom-tagmanager-create-person').click(function(){
			var dialog = $.boom.dialog.open({
				url: '/cms/people/add',
				title: 'Create new person',
				onLoad: function(){

					$('#boom-tagmanager-create-person-form input[name="name"]').focus();
				},
				buttons: {
					Cancel: function(){

						$.boom.dialog.destroy( dialog );
					},
					Save: function(){
						self
							.savePerson('/cms/people/add')
							.done( function(){
								window.location.reload();
							});

						$.boom.dialog.destroy( dialog );

					}
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

				$.boom.dialog.confirm('Confirm deletion', msg, function(){

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

/**
@class
*/
$.boom.items.person = $.extend(true, {}, $.boom.tagmanager.base.item);

$.extend($.boom.items.person, {
	/** @lends $.boom.items.person */

	/** @function */
	get: function( rid ){

		var self = this;
		var options = this.tagmanager.options;

		this.rid = rid;

		$.boom.loader.show();

		$.boom.events.register('person.clickBefore', 'tagmanager', { rid: rid });

		var segments = [
			rid
		].join('/'),
		url = '/cms/people/view/' + segments;

		console.debug('before person load');

		self.tagmanager.elements.rightpane
		.find('.b-items-content')
		.sload(url, function(){

			$.boom.tagmanager.base.item.prototype.get.apply( self );

			$( this ).scrollTop( 0 );

			$.boom.events.register('person.clickAfter', 'tagmanager', { rid: rid });

			$.boom.loader.hide();

			self.bind( this );
		});
	},

	/** @function */
	bind: function( elem ){

		var self = this;
		var options = this.tagmanager.options,

		treeConfig = $.extend({}, options.treeConfig, {
			toggleSelected: true,
			onClick: function(e) {

				var $tags = $('input[name=groups]');
				var tags = $tags.val().split(',');

				tags = (function(tags) {

					for (var i in tags) {
						if (tags[i] == e.data.tag) {
							tags.splice(i,1);
							return tags;
						}
					}

					tags.push(e.data.tag);
					return tags;

				})(tags);

				$tags.val(tags.join(','));

			}
		});

		$( elem ).ui({
			tabs: $.boom.config.tabs
		});

		$.boom.dialog.bind({
			image: $('.boom-asset-preview')
		});

		$('.b-people-groups-add').click(function(){
			var rid = $( this ).attr( 'rel' );

			var dialog = $.boom.dialog.open({
				url: '/cms/people/add_group/' + rid,
				title: 'Add group',
				buttons: {
					Cancel: function(){

						$.boom.dialog.destroy( dialog );
					},
					Save: function(){

						var dialog = this;
						var data = $( dialog ).find('form').serialize();
						$.boom.loader.show();

						$.post('/cms/people/add_group/' + rid, data )
						.done( function(){

							$.boom.loader.hide();

							$.boom.dialog.destroy(dialog);
						});
					}
				}
			});
		});

		$('.b-people-group-delete').click(function(){
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

		$('.b-people-save').bind('save', function( event ){

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

		$('#b-delete-person').click(function( event ){

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

					self.tagmanager.defaultRoute();
				}
			});
		});
	}
});

/**
@class
*/
$.boom.items.group = $.extend(true, {}, $.boom.tagmanager.base.item);

$.extend($.boom.items.group,  {
	/** @lends $.boom.items.group */

	/**
	@property
	*/
	buttonManager: {},

	/** @function */
	get : function(rid){

		var self = this, options = this.tagmanager.options;

		this.rid = rid;

		$.boom.loader.show();

		params =
			'tag=' + rid + '&' +
			'perpage=' + options.perpage + '&' +
			'sortby=' + options.sortby + '&' +
			'order='  + options.order;

		var url =
			'/cms/' + options.type + '/list'
			+ '?' + params;

		self.tagmanager.options.url = url;

		$('.b-items-content')
			.sload( url, function(){

				$.boom.tagmanager.base.item.prototype.get.apply( self );

				$.boom.loader.hide();

				self.bind();
			});

		$.boom.log('Group items get');
	},

	/** @function */
	bind : function(){

		var self = this;

		var tabsConfig = $.extend({}, $.boom.config.tabs, {

			show: function(event, ui){

				$.boom.hooks.register('rightpane.afterResize', 'tagmanager', function(){

					$( ui.panel ).infiniteScroll( 'loadInitialData' );

				}, true);

				self.tagmanager.elements.rightpane.trigger( 'resize.boom' );
			}
		});

		this.tagmanager.elements.rightpane.ui({
			tabs: tabsConfig
		});

		self.tagmanager.elements.rightpane.trigger( 'resize.boom' );

		$.boom.events.register('tag.clickAfter', 'tagmanager');

		$('.b-items-select-checkbox').change(function(){

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

		$('.b-items-list tbody tr, .b-items-thumbs .thumb').hover(
			function(){
				$( this ).addClass( 'ui-state-hover' );
			},
			function(){
				$( this ).removeClass( 'ui-state-hover' );
			}
		);

		$('.b-items-thumbs .thumb').captions($.boom.config.captions);

	},

	/** @function */
	edit : function(event){
		event.preventDefault();

		var self = this;
		var item = $( event.target ).closest( 'li' );
		var rid = item.find('a').attr( 'rel' );
		var selected_page = null;

		var permissions_treeConfig = {
			onClick: function(event){
				console.log( 'CLICK' );
				var $this = $( this );
				event.preventDefault();

				var item = $this.closest( 'li' );
				var page_id = $this.attr( 'rel' );
				selected_page = page_id;
				console.log( rid );

				$this
					.parents( '.boom-tree' )
					.find( 'a.ui-state-active' )
					.removeClass( 'ui-state-active' )
					.end()
					.end()
					.addClass( 'ui-state-active' );

				$.get( '/cms/groups/list_roles/' + rid + '?page_id=' + page_id )
				.done( function( data ){
					for ( role in data ) {
						$( 'input[name=' + role + '][value=' + data[ role ] + ']' ).attr( 'checked', 'checked' );
					}
				});
			}
		};

		self.tagmanager.elements.rightpane
		.find('.b-items-content')
		.sload( '/cms/groups/edit/' + rid, function(){

			$.boom.tagmanager.base.item.prototype.get.apply( self );

			$.boom.loader.hide();

			self.tagmanager.elements.rightpane
			.ui({
				tree: permissions_treeConfig
			})
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
							allowed : allowed
						}
					);
				})
				.done( function( response ){
					console.log( response );
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
					console.log( response );
				});
			});

			/**
			 * Save button has ID 'b-people-group-save'
			 * Needs to POST to /cms/groups/save/<group ID>
			 * With the value of 'b-people-group-name' in $_POST['name']
			 */

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
		} );

		// var dialog = $.boom.dialog.open({
		// 			url: '/cms/groups/edit/' + rid,
		// 			title: 'Edit group',
		// 			buttons: {
		// 				Cancel: function(){
		// 					$.boom.dialog.destroy( dialog );
		// 				},
		// 				Save: function(){
		// 					var dialog = this;
		// 					var data = {};
		// 					data.name = $('#boom-tagmanager-group-edit-name').val();
		// 					data.permissions = [];
		//
		// 					$.each($('.boom-group-permission'), function(index, value){
		// 						data.permissions.push( $(value).data( 'permission' ) );
		// 					});
		//
		// 					item.find('a').text( $( '#boom-tagmanager-group-edit-name' ).val() );
		//
		// 					self.save( rid, data)
		// 					.done( function(){
		//
		// 						$.boom.dialog.destroy(dialog);
		//
		// 						$.boom.growl.show('Group successfully saved.');
		// 					});
		// 				}
		// 			},
		// 			treeConfig: permissions_treeConfig,
		// 			open: function(){
		// 				$('#edit-group-permissions-general button')
		// 					.on( 'click', function(){
		// 						self.permissions.general_picker({
		// 							rid : rid
		// 						})
		// 						.done( function( permission ){
		// 							self.update_tree( '#boom-group-permissions-general', permission );
		//
		// 						});
		// 					});
		// 			}
		// 		});
	},

	/** @function */
	add: function(event){

		var self = this;

		var url = '/cms/groups/add';

		var dialog = $.boom.dialog.open({
			url: url,
			title: 'Add group',
			buttons: {
				Cancel: function(){
					$.boom.dialog.destroy( dialog );
				},
				Save: function(){
					$.post(url, {name: $('#b-people-group-name').val()}, function(response){
						$.boom.dialog.destroy(dialog);

						$.boom.growl.show('Group successfully saved.');

						top.location.reload();
					});
				}
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
		console.log( item );

		$.boom.dialog.confirm(
			'Please confirm',
			'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!',
			function(){

				$.boom.loader.show();

				$.post( '/cms/groups/delete/' + rid )
				.done( function(){

					$.boom.loader.hide();

					$.boom.growl.show( 'Group successfully removed.' );
					item.remove();
				});
			}
		);
	},

	/** @function */
	update_tree : function( selector, permission ) {
		$.boom.log( 'updating permissions ' + selector );

		var new_item = '<li class=\'boom-group-permission\' data-permission=\'' + permission.value + '\'>' + permission.label + '</li>';

		$( selector )
		.tree( 'add_item', new_item );

	},

	/** @class */
	permissions : {

		/** @function */
		general_picker : function( opts ) {
			var picked = new $.Deferred();

			var dialog = $.boom.dialog.open({
				url: '/cms/groups/add_permission/' + opts.rid + '?component=boom/main',
				title: 'Add general permission',
				buttons: {
					Close: function(){
						$.boom.dialog.destroy( dialog );
					},
					Add: function(){
						permission = $('#b-permissions-add-action option:selected');
						value = $('#b-permissions-add-value option:selected' ).val();

						picked.resolve( {
							label : permission.text(),
							value : permission.val() + ' 1 0 ' + value
						});

						$.boom.dialog.destroy( dialog );
					}
				}
			});

			return picked;
		},

		/** @function */
		page_picker : function( opts ) {

			$.boom.log( 'opening permissions editor' );
			console.log( opts );

			var self = this;
			var group = $.boom.items.group;
			var rid = opts.item_rid;
			var page_id = opts.page_rid;

			return $.boom.dialog.open({
				url: '/cms/groups/page_permissions/' + rid + '?page=' + page_id,
				title: 'Page permissions',
				buttons: {
					Cancel: function(){
						var dialog = this;
						$.boom.dialog.destroy( dialog );
					},
					Save: function(){
						var dialog = this;

						$('#boom-group-permissions-page-add .boom-group-permissions-page-permission').each( function(){
							console.log( this );
							$('#boom-group-permissions-page').append( '<li class=\'boom-group-permission\' data-permission=\'' + $(this).data( 'permission' ) + '\'></li>' );
						});

						$.boom.dialog.destroy( dialog );
					}
				},
				treeConfig: {
					showRemove: true,
					onRemoveClick: function(event){
						var item = $( event.target ).closest( 'li' ), permission = item.data( 'permission' );
						item.remove();

						$('.boom-group-permission').each( function(){
							if ($( this ).data( 'permission' ) == permission)
							{
								$( this ).remove();
							}
						});
					}
				},
				open: function(){
					$('#edit-group-permissions-page button')
						.on( 'click', function(){
							self.page_settings({
								rid : rid,
								page_id : page_id
							})
							.done( function( permissions ){
								$( permissions ).each( function(){
									group.update_tree( '#boom-group-permissions-page-list', this );
								});
							});
						});
				}
			});
		},

		/** @function */
		page_settings: function( opts ) {
			$.boom.log( 'page permissions editor opened' );

			var picked = new $.Deferred();
			var permissions = [];

			var dialog = $.boom.dialog.open({
				url: '/cms/groups/add_permission/' + opts.rid + '?page=' + opts.page_id,
				title: 'Add page permission',
				buttons: {
					Close: function(){
						$.boom.dialog.destroy( dialog );
					},
					Add: function(){
						var value = $( '#b-permissions-add-value option:selected' ).val();
						var permission = $( '#b-permissions-add-action option:selected');
						permission.each( function(){
							permissions.push( {
								label : $( this ).text(),
								value : $(this).val() + opts.page_id + ' ' + value
							});

						});

						$.boom.dialog.destroy( dialog );
						picked.resolve( permissions );
					}
				}
			});

			return picked;
		}
	}
});
