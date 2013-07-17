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
	update slot HTML and push changes to the stack of page edits.
	@function
	*/
	_apply: function(replacedata){

		this._update_html( replacedata );

		$.boom.log( 'push changes onto the stack' );
		this._save_slot();

	},

	/**
	Remove slot from the page
	@function
	*/
	_remove: function( data ){
		var self = this;

		return this._preview( data )
			.done( function( response ){
				self._update_html( response );
				self._save_slot( { "delete" : true } );
			});
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_update_html : function( html ) {
		// TODO: rewrite this mess.

		var $el = top.$( this.element );

		var replacedata =
			top.$( html )
			.insertAfter( $el );

		// this line implicitly calls _destroy(), by removing the element.
		this.element.remove();

		this.element = replacedata;

	},

	/**
	Push the current state of the slot on to the edits stack
	@function
	*/

	_save_slot : function( data ) {

		data = ( data ) ? data : this.getData();

		$.boom.page.slot_edits.push( {
			slot: this.options.slot,
			data: data
		} );

		$.boom.page.save_button.button( 'enable' ).attr( 'title', 'Save page' );
		$.boom.page.cancel_button.button( 'enable' ).attr( 'title', 'Cancel' );
	},

	/**
	Get the base ajax URL for previewing the slot HTML
	@function
	@returns {String} URL for this slot's HTML
	*/
	_preview_url : function() {
		return this.options.urlPrefix +
		'/' + this.options.slot.type +
		 '/preview/' + $.boom.page.options.id;
	},

	/**
	Get the POST data for a preview request
	@function
	@returns {Object} slot data including slotname and template
	*/
	_slot_data : function( data ) {
		return $.extend(
				{ data : data },
				{
					slotname: this.options.slot.name,
					template: this.options.slot.template
				 });
	},

	_preview : function( data ) {

		return $.post( this._preview_url(), this._slot_data( data ) );
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