$.widget('boom.siteManager', {
	bind : function() {
		this.element
			.on('click', '.b-sites-delete', function(e) {
				e.preventDefault();

				var $item = $(this).closest( "tr" ),
					site = new boomSite($item.attr('data-id')),
					confirmation = new boomConfirmation("Please confirm", "Are you sure you want to delete this site?");

				confirmation
					.done(function() {
						site.delete().done(function() {
							$item.fadeOut(600, function(){
								$item.remove();
							});
						});
					});
			})
			.on('click', '.b-sites-edit', function(e) {
				e.preventDefault();

				var $item = $(this).closest( "tr" ),
					site = new boomSite($item.attr('data-id'));

				site.edit().done(function(data) {
					site.update(data);
				});
			});
	},

	_create : function() {
		this.bind();
	}
});