/**
* User interface for tagging content.
* @class
* @name $.boom.tagger
*/
$.widget( 'boom.tagger', {
/** @lends $.boom.tagger */

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
	
	/**
	@property
	*/
	items : {},

	_create : function(){
		
		$.boom.log( 'tag widget created' );
		
		var 
			self = this, 
			type = this.options.type, 
			id = this.options.id;

		self._bind();
				
		self.tag_list()
			.progress( function( $link ){
				
				self.remove( $link.attr( 'href' ) );
				
				$link.closest('li').remove();
					
			});

		self.picker()
			.progress( function ( tag ) {
				self.add( tag );
			});
	},
	
	_init : function(){
		
	},
	
	_destroy : function(){
		
	},
	
	_bind : function(){
		var self = this;
		
		// The add tag input box is hidden when the modal window opens.
		// Show it and give it focus when the add button is clicked.
		this.element
			.on('click', '#b-tags-add', function(){
				self.element.find( '.b-filter-input' ).show().focus();
			})
			// Hide the add tag input box when it loses focus.
			.on('blur', '.b-filter-input', function(){
				$( this ).val('').hide();
			});
		
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
				tag : tag.label
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
	Remove a tag from an item.
	@param {String} tag Tag name
	*/
	remove : function( tag ) {
		var 
			self = this, 
			type = this.options.type, 
			id = this.options.id;

			return $.post(
				self.options.base_url + type + '/remove/' + id,
				{tag : tag}
				);
	},
	
	/**
	Set up the tag list
	@param {String} selector jQuery selector for the list container
	@returns {Deferred} sends progress notfications to handle remove callbacks.
	*/
	tag_list : function() {
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
				
				remove.notify( $(this) );
			});
			
			remove.progress( function(){
				$.boom.loader.hide();
			});
			
			return remove;
	},
	
	/**
	Autocomplete UI for finding and picking tags.
	@param {Array} tags optional array of tags to filter the search by
	@returns {Deferred} Promise which resolves with the chosen tag as {label : {tag_name}, value : {tag_id} }
	*/
	picker : function( tags ){
		
		var 
			type = this.options.type,
			add_input = this.element.find( '.b-filter-input' );

		var complete = new $.Deferred();
		tags = ( tags ) ? tags : [];
		
		console.log( type );

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

/**
* User interface for searching by tag.
* @class
* @name $.boom.tagger_search
* @extends $.boom.tagger
*/
$.widget( 'boom.tagger_search', $.boom.tagger, {
	/** @lends $.boom.tagger_search */
	
	/**
	default config
	@property
	*/
	options: {
		/** selected tags for this search */
		selected_tag_ids : [],
		/** item type for URL fragments */
		tag : 'tag'
	},
	
	_bind : function(){
		
	},
	
	/**
	Add a tag to the search.
	@param {String} tag Tag name
	*/
	add : function( tag ) {
		var 
			self = this,
			selected_tag_ids = this.options.selected_tag_ids,
			tag_filter_list = this.element.find( 'ul.b-tags-list' );

		selected_tag_ids.push( tag.value );
		var link = $( '<a>', {
			href : '#',
			"class" : 'b-tags-remove',
			"data-tag_id" : tag.value 
		});
		var label = $( '<span>').text( tag.label );
		
		$( '<li>' )
			.append( link )
			.append( label )
			.appendTo( tag_filter_list );
			
		self.do_search();
	},
	
	/**
	Remove a tag from the search.
	@param {String} tag Tag name
	*/
	remove : function( tag ) {
		var 
			self = this,
			selected_tag_ids = this.options.selected_tag_ids;

			selected_tag_ids.splice( selected_tag_ids.indexOf( tag.value ), 1);
			self.do_search();
	},
	
	/**
	Autocomplete, filtering by all selected tags
	*/
	picker : function() {
		var 
			self = this,
			selected_tag_ids = this.options.selected_tag_ids;
			
		return $.boom.tagger.prototype.picker.call( this, selected_tag_ids );
	},
	
	/** Perform a search based on currently selected tags */
	do_search : function() {
		var 
			self = this,
			selected_tag_ids = this.options.selected_tag_ids;
			
		return $.boom.history.load( self.options.tag + '/' + selected_tag_ids.join( '-' ) );
	}
	
	
});

/**
* Deferred tagger for file uploads.
* @class
* @name $.boom.tagger_deferred
* @extends $.boom.tagger
*/
$.widget( 'boom.tagger_deferred', $.boom.tagger, {
	/** @lends $.boom.tagger_deferred */
	
	/**
	default config
	@property
	*/
	options: {
		/** selected tags */
		tags : [],
		/** URL prefix for ajax calls */
		base_url : '/cms/tags/',
		/** Type. One of asset|page */
		type : 'asset'
	},
	
	_bind : function(){
		
		// Build the UI for any existing tags.
		for ( var i in this.options.tags ) {
			this._render_tag( this.options.tags[ i ] );
		}
	},
	
	/**
	Add a tag.
	@param {Object} tag Tag
	*/
	add : function( tag ) {

			this.options.tags.push( tag );
			
			this._render_tag( tag );
	},
	
	/**
	Remove a tag.
	@param {Object} tag Tag
	*/
	remove : function( tag ) {
		var 
			self = this,
			tags = this.options.tags;

			tags.splice( tags.indexOf( tag ), 1);
	},
	
	/** Show a tag in the UI
	@param {Object} tag Tag
	*/
	_render_tag: function( tag ) {
		
		var tag_list = this.element.find( 'ul.b-tags-list' );
		
		var link = $( '<a>', {
			href : '#',
			"class" : 'b-tags-remove',
			"data-tag_id" : tag.value 
		});
		var label = $( '<span>').text( tag.label );

		$( '<li>' )
			.append( link )
			.append( label )
			.appendTo( tag_list );
	}
	
});