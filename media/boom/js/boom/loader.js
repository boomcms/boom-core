$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	* @class
	@static
	*/
	loader : {

		/** initialise loader */
		init : function(){
			var img = new Image();
			img.src = '/media/boom/img/ajax_load.gif';

			this.loaders = 0;
			this.elements = {
				loader: $('#boom-loader'),
				loaderDialogOverlay: $('#boom-loader-dialog-overlay')
			};
		},

		/** show loader */
		show : function(type){

			if ( !this.elements ) {
				this.init();
			}

			type = type || '';

			this.loaders ++;

			if ( type == 'dialog' ) {

				this.elements.loaderDialogOverlay.show();
			}

			this.elements.loader.show();

			return this.loaders;
		},
		/** hide loader */
		hide : function(force){

			force = (force == undefined) ? false : true;

			if (force) this.loaders = 0;

			if (this.loaders > 0) this.loaders --;

			if (this.loaders === 0) {
				$.each(this.elements, function(){
					$(this).hide();
				});
			}
			return this;
		},
		/** FIXME: This doesn't seem to do anything. */
		hideOverlay : function(){
			//this.elements.loaderOverlay.hide();
		}
	}
});