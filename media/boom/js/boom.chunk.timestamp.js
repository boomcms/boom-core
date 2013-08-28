/**
Editable timestamps
@class
@name chunkTimestamp
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkTimestamp', $.ui.chunk,

	/**
	@lends $.ui.chunkTimestamp
	*/
	{

	format : '',

	timestamp : '',

	/**
	Make the element editable by invokeing boom.editor.edit() on it.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Timestamp chunk slot edit');

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/timestamp/edit/' + $.boom.page.options.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			// cache: true,
			title: 'Edit date / time',
			onLoad : function() {

			},
			destroy: function(){
				self.destroy();
			},
			open: function(){

			}
		});
	},

	/**
	Get the chunk HTML, escaped and cleaned.
	*/
	getData : function(){
		var $content = this.element.find( '.slot-content');

		if ( $content.length ) {
			this.content = $content.html();
			this.title = this.element.find( '.slot-title').text();
		} else {
			this.title = null;
			this.content = this.element.html();
		}

		return { title : this.title, text : this.content.cleanup() };
	},

	/**
	Update the page with edited HTML from the editor, then remove TinyMCE.
	@param {String} replacedata HTML to insert into the page.
	*/
	_apply: function(replacedata){

		//replacedata = $( replacedata );

		if( replacedata ) {
			this.element
				.html( replacedata )
				.show();
		}

		this.element
			.find( '[contenteditable]' )
			.removeAttr( 'contenteditable' )
			.off( 'click' );

		this._save_slot();

		this.destroy();
	},

	/**
	Remove the slot from the page.
	*/
	_remove: function(){

		if( this.element.is( 'div' ) ) {
			this.element
				.html( '<p>Default text.</p>' )
				.show();
		} else {
			this.element
				.text( 'Default text.' )
				.show();
		}

		this.element
			.find( '[contenteditable]' )
			.removeAttr( 'contenteditable' )
			.off( 'click' );

		this._save_slot( { "delete" : true } );

		this.destroy();
	}

});