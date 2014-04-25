$.widget( 'boom.pageManager', {
	addActionButtons : function($elements) {
		$elements.each(function() {
			var $el = $(this);

			$el.append("<div><a href='#' class='b-pages-delete'>Delete page</a></div>");
		});

		$elements.sortable();
	},

	_create : function() {
		var pageManager = this;

		this.element
			.pageTree({
				load : function(e, data) {
					pageManager.addActionButtons(data.elements);
				}
			});

		this.addActionButtons(this.element.find('li'));
	},

	_init : function() {
		this.element.on('click', '.b-pages-delete', function() {
			var $this = $(this),
				$li = $this.closest('li'),
				page = new boomPage($li.data('page-id'));

			page.remove()
				.done(function() {
					$li.remove();
				});
		});
	}
});