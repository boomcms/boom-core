/**
@fileOverview jQuery UI widgets for editable slots.
*/
/**
@namespace
@name $.ui
*/

/**
Common functionality for all editable slots
@class
@name $.ui.chunk
*/
$.widget('ui.chunk',

	/**
	@lends $.ui.chunk
	*/
	{

	edited : false,

	/**
	default config
	@property options
	*/
	options: {
		/** URL prefix for ajax calls */
		urlPrefix:  '/cms/chunk'
	},

	bind : function() {
		var self = this;

		this.element
			.addClass('b-editable')
			.unbind('click')
			.on('click', function(event) {
				event.preventDefault();
				event.stopPropagation();

				self.unbind();

				self.edit();

				return false;
			})
			.on( 'keydown', function(event) {
				switch(event.which) {
					case 13:
						self.edit()
					break;
				}
			});
	},

	_create : function(){
		$.boom.log( 'CHUNK CREATE' );

		this.bind();
	},

	destroy : function() {
		this.bind();
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_update_html : function(html) {
		this.element.html($(html).html());

		this.bind();
	},

	remove : function() {
		var self = this;

		return $.post(this._url('remove'), this._slot_data({}))
			.done(function(response) {
				var data = $.parseJSON(response);

				self._update_html(data.html);
				$.boom.page.toolbar.status.set(data.status);
				$.boom.growl.show("Page content saved");
			});
	},

	/**
	Get the base ajax URL for saving / removing the chunk data
	@function
	@returns {String} URL for this chunk's HTML
	*/
	_url : function(action) {
		return this.options.urlPrefix +
		'/' + this.options.type +
		 '/' + action + '/' + this.options.currentPage.id;
	},

	/**
	Get the POST data for a preview request
	@function
	@returns {Object} slot data including slotname and template
	*/
	_slot_data : function(data) {
		return $.extend(data,
			{
				slotname : this.options.name,
				template : this.options.template
			});
	},

	_save : function() {
		var self = this;

		return $.post(this._url('save'), this._slot_data(this.getData()))
			.done(function(response) {
				var data = $.parseJSON(response);

				self._update_html(data.html);
				$.boom.page.toolbar.status.set(data.status);
				$.boom.growl.show("Page content saved");
			});
	},

	/**
	Bring the slot UI forward, above all other page elements.
	@function
	*/
	_bring_forward : function() {

		this.element.css( {
			'z-index' : 1000,
			'position' : 'relative'
		});
		top.$( 'body' ).prepend( '<div class="overlay"></div>' );

	},
	/**
	Drop the slot UI back into its natural place in the page z-index stack.
	@function
	*/
	_send_back : function() {
		this.element.removeAttr( 'style' );
		top.$( 'div.overlay' ).remove();
	},

	unbind : function() {
		this.element
			.unbind('click')
			.removeClass('b-editable');
	}
});