/**
@class
@name $.boom.tagmanager.base.item
*/
$.extend($.boom.tagmanager.base.item.prototype, {
	/** @lends $.boom.tagmanager.base.item */

	/**
	Get???
	*/
	get: function(){

		//this.tagmanager.elements.rightpane.trigger( 'resize.boom' );

		if ( this.buttonManager ) {

			if ( this.buttonManager.show ) { 

				$( this.buttonManager.show.join(',') ).show();
			}
		
			if ( this.buttonManager.hide ) {

				$( this.buttonManager.hide.join(',') ).hide();
			}
		}
	}
});

/**
@class
*/
$.boom.items = $.extend( true, {}, $.boom.tagmanager.items );

/**
@class
*/
$.boom.items.tag = $.extend(true, {}, $.boom.tagmanager.base.item);

$.extend($.boom.items.tag,  {
	/** @lends $.boom.items.tag */

	/** 
	@property
	*/
	buttonManager: {
		
		show: [ '#b-assets-upload' ],
		hide: [ '#boom-tagmanager-save-all' ]
	},
	
	rid: 0,
	
	filters: {},

	/** @function */
	get : function( rid ){
		
		$.boom.log( 'get tag ' + rid );
		
		var self = this;
		var options = this.tagmanager.options;

		this.rid = rid;
	
		$.boom.loader.show();
	
		params = 
			'tag=' + rid + '&' +
			'perpage=' + options.perpage + '&' +
			'sortby=' + options.sortby + '&' +
			'order='  + options.order;
			
		for ( filter in self.filters ) {
			params += '&' + filter + '=' + self.filters[ filter ];
		}
 
		var url = 
			'/cms/' + options.type + '/list' 
			+ '?' + params;

		self.tagmanager.options.url = url;
		
			self.tagmanager.elements.rightpane
			.find('.b-items-content')
			.sload( url, function(){

				$.boom.tagmanager.base.item.prototype.get.apply( self );

				$.boom.loader.hide();
	
				self.bind();
			});

		$.boom.log('Tag items get');
	},
	
	/** @function */
	bind : function(){
		$.boom.log( 'items.tag.bind()' );

		var self = this;

		var tabsConfig = $.extend({}, $.boom.config.tabs, {
		
			show: function(event, ui){
			}
		});	
			
		this.tagmanager.elements.rightpane.ui({
			tabs: tabsConfig
		});

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

			var buttons = $( '[id|=s-button-multiaction]' );

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

		$('#b-items-view-thumbs').on( 'click', 'a', function(event){
			event.preventDefault();

			var container = $(this).parent('div');
			var checkbox = container.find('.b-items-select-checkbox');

			container.toggleClass('ui-state-active');

			checkbox.attr('checked', ! checkbox.attr('checked')).change();
		});

		$('.b-items-thumbs .thumb').captions($.boom.config.captions);
		
	},

	/** @function */
	edit : function(event){
		event.preventDefault();

		var item = $( event.target ).closest( 'li' ), rid = item.find('a')[0].rel;
		var self = this;

		$.boom.dialog.open({
			url: '/cms/tag/edit/' + rid + '?type=1',
			title: 'Edit tag',
			buttons: {
				Cancel: function(){
					$.boom.dialog.destroy(this);
				},
				Save: function(){
					var dialog = this
					var data = $( dialog ).find('form').serialize();
					
					item.find('> a').text( $( '#boom-tagmanager-tag-edit-name' ).val() );
					
					self.save( rid, data)
					.done( function(){

						$.boom.dialog.destroy(dialog);
			
						$.boom.growl.show('Tag successfully saved.');
					});
				}
			}
		});
	},
	
	/** @function */
	add: function(event){

		var self = this;

		$.boom.dialog.open({
			url: '/cms/tag/edit/0?type=1',
			title: 'Add tag',
			buttons: {
				Cancel: function(){
	
					$.boom.dialog.destroy(this);
				},
				Save: function(){
					
					var selected = $( '#boom-tag-tree a.ui-state-active').attr( 'id');
					
					var treeConfig = $.extend({}, $.boom.config.tree, {
						maxSelected: 1,
						toggleSelected: false,
						showRemove: true,
						showEdit: true,
						click: false,
						onClick: function(event){
							$this = $(this);
							self.item_selected( $this );

							self.get( 
								$this
									.attr( 'href' )
									.split('/')
									[1]
							);
						},
						onEditClick: function(event){

							self.edit(event);
						},
						onRemoveClick: function(event){

							self.remove(event);
						}
					});

					var dialog = this, data = $( dialog ).find('form').serialize();
					
					var tag_saved = self.save( 0, data );
					console.log( tag_saved );
					
					var tree_refresh = $.Deferred();
					
					tree_refresh.done( 	function(){
						$(this)
							.find( '.b-tags-tree' )
							.tree( treeConfig );
						if ( selected ) {
							$( '#' + selected )
							.addClass( 'ui-state-active' );
						}
					});
					
					tag_saved.done( function(){
						var name = $('#boom-tagmanager-tag-edit-name').val();
						var parent = $('#boom-tagmanager-tag-edit-parent').val();
						
						$.boom.dialog.destroy(dialog);
						
						$('#boom-tag-tree')
							.load( 
								'/cms/tag/tree', 
								{ type : 1 },
								tree_refresh.resolve 
							);
							
						$.boom.growl.show('Tag successfully saved.');
					});

				}
			}
		});
	},

	/** @function */
	save: function( tag_id, data ){
		$.boom.loader.show();

		return $.post('/cms/tag/save/' + tag_id, data)
		.done( function(response){

			$.boom.loader.hide();
			
		});
	},

	/** @function */
	remove : function(event){

		event.preventDefault();

		var item = $( event.target ).closest( 'li' ), rid = item.find('a')[0].rel;
		console.log( item );

		$.boom.dialog.confirm('Please confirm', 'Are you sure you want to remove this tag? <br /><br /> This will delete the tag from the database and cannot be undone!', function(){

			$.boom.loader.show();
		
			$.post('/cms/tag/delete/' + rid)
			.done( function(){

				$.boom.loader.hide();
				
				$.boom.growl.show('Tag successfully removed.');
				item.remove();
			});
		});
	},
	
	/** @function */
	picker: function( options ) {
		
		var options = ( options ) ? optionss : {};
		
		return tag_tree = $.post(
			'/cms/tag/tree',
			{ type: 1 }
		)
		.pipe( function( response ){
			
			var treeConfig = $.extend({}, options.treeConfig, {
				toggleSelected: true,
				onClick: function(e) {
					var $tags = $('input[name=tags]');
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

					$tags.val(tags.join(','))

				}
			});
			
			var tags_edited = new $.Deferred();
			
			$.boom.dialog.open({
				msg: response,
				// cache: true,
				title: 'Asset tags',
				width: 440,
				buttons: {
					Okay: function(){

						var tag_tree = $(this).find( '.boom-tree' );
						var tags = [];
						
						$.each( tag_tree.find( '.ui-state-active'), function(){
							var rid = parseInt( $(this).attr( 'rel' ) );
							tags.push( rid );
						});
						
						tags_edited.resolve( tags );
						$( this ).dialog( 'destroy' );
					}
				},
				treeConfig: {
					showEdit: false,
					width: 'auto',
					showRemove: false,
					toggleSelected: true
				}
			});
			
			return tags_edited;
		});
	}
});