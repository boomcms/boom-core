$.widget('boom.templateManager', {
	bind : function() {
		this.element
			.on('click', '.b-templates-delete', function(e) {
				e.preventDefault();

				var item = $(this).closest( "tr" ),
					confirmation = new boomConfirmation("Please confirm", "Are you sure you want to delete this template?");

				confirmation
					.done(function() {
						$.post('/cms/templates/delete/' + item.attr('data-id'))
							.done(function() {
								item.fadeOut(600, function(){
									item.remove();
								});
							});
					});
			})
			.on('click', '#b-templates-save', function() {
				var data = $('#b-templates form').serialize();

				$.post('/cms/templates/save', data, function(){
					new boomNotification('Templates successfully saved.');
				});
			})
			.on('click', '#b-template-pages-download', function() {
				window.location.href = window.location.href + '.csv';
			});
	},

	_create : function() {
		this.bind();

		this.element.find('table')
			.tablesorter({
				/**
				Return the value of any form input in a table cell, or the text content of the cell.
				*/
				textExtraction: function( node ){
					var text = $( node )
						.find( 'select, input' )
						.val();

					return (typeof text == 'undefined') ? $( node ).text() : text;
				}
			});
	}
});