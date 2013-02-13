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
		Intialise the tagging interface.
		@param {Object} opts Options array.
		*/
		init : function( opts ) {
			
			var default_options = {
				selector : '#b-tags',
				type : 'asset',
				id : null
			};
			
			this.opts = $.extend( default_options, opts );
			
			this.container = $( this.opts.selector );
			
			this.bind();
		},

		/**
		Bind UI events for adding and listing tags.
		*/
		bind : function() {
			var self = this, type = this.opts.type, id = this.opts.id;

			// The add tag input box is hidden when the modal window opens.
			// Show it and give it focus when the add button is clicked.
			this.container
			.on('click', '#b-tags-add', function(){
				$('#b-tags-add-name').show().focus();
			})
			// Hide the add tag input box when it loses focus.
			.on('blur', '#b-tags-add-name', function(){
				$('#b-tags-add-name').val('').hide();
			})
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

				tag = $(this).attr('href');
				$.post(
					$.boom.tags.base_url + type + '/remove/' + id,
					{tag : tag}
					)
					.done(function(){
						$(event.target).closest('li').remove();
						$.boom.loader.hide();
					});
			});

			self.picker( $('#b-tags-add-name'), type )
				.progress( function ( tag ) {
					self.add( tag.label );
				});
		},

		/**
		Add a tag to an item.
		@param {String} tag Tag name
		*/
		add : function( tag ) {
			var self = this, type = this.opts.type, id = this.opts.id;

			$.boom.loader.show();

			$.post(
				$.boom.tags.base_url + type + '/add/' + id,
				{
					tag : tag,
					ids : id.split( '-' )
				}
				)
				.done(function(){
					
					$.get( $.boom.tags.base_url + type + '/list/' + id )
					.done( function( response ){
						var list = $( response ).find( 'ul' ).ui();
						$( '#b-tags' )
						.find( 'ul' )
						.replaceWith( list );
						
					});
				});

			$.boom.loader.hide();
			$('#b-tags-add-name').val('').hide();
		},
		
		/**
		Autocomplete UI for finding and picking tags.
		@param {jQuery element} add_input HTML text input for the tag name.
		@param {String} type one of asset/page.
		@returns {Deferred} Promise which resolves with the chosen tag as {label : {tag_name}, value : {tag_id} }
		*/
		picker : function( add_input, type ){

			var complete = new $.Deferred();

			add_input
				.autocomplete({
					delay: 200, // Time to wait after keypress before making the AJAX call.
					source: function( request, response ){
						$.ajax({
							url: '/cms/autocomplete/tags',
							dataType: 'json',
							data: {
								text : add_input.val(),
								type : (type == 'asset')? 1 : 2
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
						add_input.val( ui.item.label );
						complete.notify( ui.item );
					}
				})
				.on( 'keypress', function( e ){
					// Add a tag when the enter key is pressed.
					// This allows us to add a tag which doesn't already exist.
					if (e.which == 13) {
						complete.resolve( { label: add_input.val(), value: -1 } );
					}
				});
				
			return complete;
		}
	}
});