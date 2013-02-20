/**
* User interface for tagging content.
* @class
* @name boom.tagger
*/
$.widget( 'boom.tagger', {
/** @lends boom.tagger */

	/**
	default config
	@property
	*/
	options: {
		/** URL prefix for ajax calls */
		base_url : '/cms/tags/',
		/** Type. One of asset|page */
		type : 'asset',
		/** ID of the item being tagged */
		id : null
	},

	_create : function(){
		
		$.boom.log( 'tag widget created' );
		
		var 
			self = this, 
			type = this.options.type, 
			id = this.options.id;

		// The add tag input box is hidden when the modal window opens.
		// Show it and give it focus when the add button is clicked.
		this.element
			.on('click', '#b-tags-add', function(){
				$('#b-tags-add-name').show().focus();
			})
			// Hide the add tag input box when it loses focus.
			.on('blur', '#b-tags-add-name', function(){
				$('#b-tags-add-name').val('').hide();
			});
		
		self.bind_tree()
			.progress( function( $link ){
				
				$.post(
					self.options.base_url + type + '/remove/' + id,
					{tag : $link.attr( 'href' )}
					)
					.done(function(){
						$link.closest('li').remove();
						$.boom.loader.hide();
					});
					
			});

		self.picker( $('#b-tags-add-name'), type )
			.progress( function ( tag ) {
				self.add( tag.label );
			});
	},
	
	_init : function(){
		
	},
	
	_destroy : function(){
		
	},
	
	/**
	Add a tag to an item.
	@param {String} tag Tag name
	*/
	add : function( tag ) {
		var 
			self = this, 
			type = this.options.type, 
			id = this.options.id;

		$.boom.loader.show();

		$.post(
			self.options.base_url + type + '/add/' + id,
			{
				tag : tag
			}
			)
			.done(function(){
				
				$.get( self.options.base_url + type + '/list/' + id )
				.done( function( response ){
					var list = $( response ).find( 'ul' ).ui();
					self.element
					.find( 'ul' )
					.replaceWith( list );
					
				});
			});

		$.boom.loader.hide();
		$('#b-tags-add-name').val('').hide();
	},
	
	/**
	Bind events to the tag list
	@param {String} selector jQuery selector for the list container
	@returns {Deferred} sends progress notfications to handle remove callbacks.
	*/
	bind_tree : function() {
		var remove = new $.Deferred();
		
		this.element
			// When hovering over an existing tag show a button to remove the tag from the page.
			// Then hide the button again when the mouse moves away.
			.on('mouseenter', '.b-tags-list li', function(){
				// If the ui-icon and ui-icon-close clases are added in the HTML then the crosses aren't hidden when the modal opens.
				// So we only add these classes when we need to show them.
				$(this)
					.find('a')
					.addClass('ui-icon ui-icon-close')
					.show()
					.end()
					.find('span')
					.addClass('active');
				// Adding the active class changes the margin-left of the text so that it doesn't shift to the right when then delete url becomes visible.
			})
			.on('mouseleave', '.b-tags-list li', function(){
				$(this)
					.find('a')
					.hide()
					.end()
					.find('span')
					.removeClass('active');
			})
			// Remove a tag from the page.
			.on('click', '.b-tags-remove', function(event){
				event.preventDefault();

				$.boom.loader.show();
				
				remove.notify( $(this) );
			});
			
			remove.progress( function(){
				$.boom.loader.hide();
			});
			
			return remove;
	},
	
	/**
	Autocomplete UI for finding and picking tags.
	@param {jQuery element} add_input HTML text input for the tag name.
	@param {String} type one of asset/page.
	@param {Array} tags optional array of tags to filter the search by
	@returns {Deferred} Promise which resolves with the chosen tag as {label : {tag_name}, value : {tag_id} }
	*/
	picker : function( add_input, type, tags ){

		var complete = new $.Deferred();
		tags = ( tags ) ? tags : [];

		add_input
			.autocomplete({
				delay: 200, // Time to wait after keypress before making the AJAX call.
				source: function( request, response ){
					$.ajax({
						url: '/cms/autocomplete/tags',
						dataType: 'json',
						data: {
							text : add_input.val(),
							type : (type == 'asset')? 1 : 2,
							tags : tags
						}
					})
					.done(function(data) {
						var suggestions = [];
						for ( name in data ) {
							suggestions.push({
								label : name,
								value : data[ name ]
							});
						}
						response( suggestions );
					});
				},
				select: function( event, ui ){
					event.preventDefault();
					add_input.val( '' );
					complete.notify( ui.item );
				}
			})
			.on( 'keypress', function( e ){
				// Add a tag when the enter key is pressed.
				// This allows us to add a tag which doesn't already exist.
				if (e.which == 13) {
					complete.notify( { label: add_input.val(), value: -1 } );
				}
			});
			
		return complete;
	}
	
});
$.extend($.boom, {
	/**
	@lends $.boom
	*/
	/**
	* User interface for tagging content and searching by tag.
	* @class
	*/
	tags : {

		/**
		Base URL for the tags controller
		@property 
		@type String
		@default '/cms/tags'
		*/
		base_url: '/cms/tags/',
		
		/**
		Container element for the tag UI.
		@property 
		@type jQuery element
		@default null
		*/
		container : null,
		
		/**
		Bind events to the tag list
		@param {String} selector jQuery selector for the list container
		@returns {Deferred} sends progress notfications to handle remove callbacks.
		*/
		bind_tree : function( selector ) {
			var remove = new $.Deferred();
			
			$( selector )
				// When hovering over an existing tag show a button to remove the tag from the page.
				// Then hide the button again when the mouse moves away.
				.on('mouseenter', '.b-tags-list li', function(){
					// If the ui-icon and ui-icon-close clases are added in the HTML then the crosses aren't hidden when the modal opens.
					// So we only add these classes when we need to show them.
					$(this)
						.find('a')
						.addClass('ui-icon ui-icon-close')
						.show()
						.end()
						.find('span')
						.addClass('active');
					// Adding the active class changes the margin-left of the text so that it doesn't shift to the right when then delete url becomes visible.
				})
				.on('mouseleave', '.b-tags-list li', function(){
					$(this)
						.find('a')
						.hide()
						.end()
						.find('span')
						.removeClass('active');
				})
				// Remove a tag from the page.
				.on('click', '.b-tags-remove', function(event){
					event.preventDefault();

					$.boom.loader.show();
					
					remove.notify( $(this) );
				});
				
				remove.progress( function(){
					$.boom.loader.hide();
				});
				
				return remove;
		},
		
		/**
		Autocomplete UI for finding and picking tags.
		@param {jQuery element} add_input HTML text input for the tag name.
		@param {String} type one of asset/page.
		@param {Array} tags optional array of tags to filter the search by
		@returns {Deferred} Promise which resolves with the chosen tag as {label : {tag_name}, value : {tag_id} }
		*/
		picker : function( add_input, type, tags ){

			var complete = new $.Deferred();
			tags = ( tags ) ? tags : [];

			add_input
				.autocomplete({
					delay: 200, // Time to wait after keypress before making the AJAX call.
					source: function( request, response ){
						$.ajax({
							url: '/cms/autocomplete/tags',
							dataType: 'json',
							data: {
								text : add_input.val(),
								type : (type == 'asset')? 1 : 2,
								tags : tags
							}
						})
						.done(function(data) {
							var suggestions = [];
							for ( name in data ) {
								suggestions.push({
									label : name,
									value : data[ name ]
								});
							}
							response( suggestions );
						});
					},
					select: function( event, ui ){
						event.preventDefault();
						add_input.val( '' );
						complete.notify( ui.item );
					}
				})
				.on( 'keypress', function( e ){
					// Add a tag when the enter key is pressed.
					// This allows us to add a tag which doesn't already exist.
					if (e.which == 13) {
						complete.notify( { label: add_input.val(), value: -1 } );
					}
				});
				
			return complete;
		}
	}
});