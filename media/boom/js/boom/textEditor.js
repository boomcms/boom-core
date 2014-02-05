/**
Base class for the text editor
@class
@name $.boom.editor
*/
$.widget( 'boom.textEditor',
	/** @lends $.boom.editor */
	{
	_create : function() {

	},

	_init : function() {

	},

	_destroy : function() {

	},

	/**
	Load the wysiwyg javascript files
	@returns {Deferred} Promise which resolves whenm the editor has loaded.
	*/
	load : function() {

		$.boom.log( 'editor loading ');
		var loaded = new $.Deferred();

		return loaded;
	},

	/**
	Apply changes and exit
	*/
	apply : function() {

	},

	/**
	Cancel changes and exit
	*/
	cancel : function() {

	},

	/**
	Edit a slot
	@param {Object} element DOM element to edit
	*/
	edit : function( element ) {

	},

	/**
	Remove the  wysiwyg instance from the DOM
	*/
	remove : function() {

	}
});