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

	_create : function(){
		$.boom.log( 'CHUNK CREATE' );
	},

	_init : function(){
		$.boom.log( 'CHUNK INIT' );

		this.edit();
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_update_html : function(html) {
		top.$( this.element ).replaceWith(html);

		$.boom.page.editor.bind();
	},

	remove : function() {
		var self = this;

		$.boom.loader.show();

		return $.post(this._url('remove'), this._slot_data({}))
			.done(function(data) {
				$.boom.loader.hide();
				self._update_html(data);
			});
	},

	/**
	Get the base ajax URL for saving / removing the chunk data
	@function
	@returns {String} URL for this chunk's HTML
	*/
	_url : function(action) {
		return this.options.urlPrefix +
		'/' + this.options.slot.type +
		 '/' + action + '/' + $.boom.page.options.id;
	},

	/**
	Get the POST data for a preview request
	@function
	@returns {Object} slot data including slotname and template
	*/
	_slot_data : function(data) {
		return $.extend(data,
			{
				slotname : this.options.slot.name,
				template : this.options.slot.template
			});
	},

	_save : function() {
		var self = this;

		$.boom.loader.show();

		return $.post(this._url('save'), this._slot_data(this.getData()))
			.done(function(data) {
				self._update_html(data);
				$.boom.loader.hide();
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

	/**
	Remove widget from the DOM and clean up.
	@function
	*/
	_destroy : function(){
		$.boom.log( 'rebinding page chunks' );

		$.boom.page.editor.bind();
	}
});