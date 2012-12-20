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

			$('.s-templates-delete').click(function(event){
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

			$('#s-templates-save').click(function(){

				var data = $('#s-templates form').serialize();

				$.boom.loader.show();

				$.post('/cms/templates/save', data, function(){

					$.boom.loader.hide();

					$.boom.growl.show('Templates successfully saved.')
				});
			});
		}
	}
});