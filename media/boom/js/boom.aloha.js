$.extend($.boom, {
	
	/**
	* @class
	* @name $.boom.editor
	*/
	editor : {
		/** @lends $.boom.editor */
		
		/** @property 
		@type string
		@default '/boom/js/tiny_mce'
		*/
		base_url: '/media/boom/js/aloha',
		/** @property 
		@type string
		@default 'tiny_mce_src.js'
		*/
		path: '/lib/aloha-full.js',
		
		/** 
		@property
		@type object
		*/
		options : {
		},
		
		/**
		* @function
		*/
		load : function() {
			
			var self = this;

			var editor_loaded = new $.Deferred();

			if (!window.Aloha) {

				$(document)
					.ready(function(){


						$.boom.log('aloha loading');
						
						$.getScript( self.base_url + self.path )
						.done( function(response, textStatus){

							editor_loaded.resolve();

							$.boom.log('aloha loaded');
						});


					});
			}

			return editor_loaded;
		},
		
		/**
		* @function
		*/
		ready : function() {
			
			if (!window.Aloha) {


				return false;
			}
			
			return true;
		},
		
		/**
		* @function
		*/
		edit : function ( element ){
			
			var self = this;

			Aloha.jQuery(element).aloha();
			
		},

		/**
		* @function
		*/
		get_content : function(){

			return '';
		},

		/**
		* @function
		*/
		remove : function( element ){

			element.mahalo();
		},
		
		/**
		* @function
		*/
		apply : function( element ){
			
			var self = this;

			self.remove( element );

		}
	}
});