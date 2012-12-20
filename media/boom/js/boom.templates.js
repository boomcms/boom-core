$.extend($.boom, {

	/**
	* @class
	* @name $.boom.templates
	*/
	templates : {
		/** @lends $.boom.templates */

		/** @function */
		init : function(config) {

			var self = this;

			this.bind();

			$.boom.log('Template manager init');
		},

		/** @function */
		bind : function(){

			var self = this;

			$('.b-templates-delete').click(function(event){
				var item = $(this).closest( "tr" );
				$.boom.dialog.confirm(
					"Please confirm",
					"Are you sure you want to delete this template?",
					function(){
						item.fadeOut(600, function(){
							item.remove()
						});
					}
				);
			});

			$('#b-templates-save').click(function(){

				var data = $('#b-templates form').serialize();

				$.boom.loader.show();

				$.post('/cms/templates/save', data, function(){

					$.boom.loader.hide();

					$.boom.growl.show('Templates successfully saved.')
				});
			});
		}
	}
});