$.widget( 'boom.pageManager', {
	addActionButtons : function($elements, children) {
		var pageManager = this,
			elementsById = {};

		$elements.each(function() {
			var $el = $(this);

			$el.append("<div><a href='#' class='b-pages-delete'>Delete page</a></div>");
			elementsById[$el.data('page-id')] = $el;
		});

		$(children).each(function(i, child) {
			var $visibilityButton = $("<a class='b-pages-visibility' href='#'>Visibility</a>");

			child.visible? $visibilityButton.addClass('visible') : $visibilityButton.addClass('invisible');
			elementsById[child.id].find('div').append($visibilityButton);
		});
	},

	_create : function() {
		var pageManager = this;

		this.element
			.pageTree({
				load : function(e, data) {
					pageManager.addActionButtons(data.elements, data.children);
				}
			});

		this.addActionButtons(this.element.find('li'), {});
	},

	deletePage : function($el) {
		var page = new boomPage($el.data('page-id'));

		page.remove()
			.done(function() {
				$el.remove();
			});
	},

	editVisibility : function($el) {
		console.log($el);
		console.log($el.data());
		var page = new boomPage($el.data('page-id'));

		page.visibility()
			.done(function(visible) {
				var $visibilityButton = $el.find('.b-page-visibility');

				if (visible) {
					$visibilityButton.removeClass('invisible').addClass('visible');
				} else {
					$visibilityButton.removeClass('visible').addClass('invisible');
				}
			});
	},

	_init : function() {
		var pageManager = this;

		this.element
			.on('click', '.b-pages-delete', function(e) {
				e.preventDefault();

				pageManager.deletePage($(this).closest('li'));
			})
			.on('click', '.b-pages-visibility', function(e) {
				e.preventDefault();

				pageManager.editVisibility($(this).closest('li'));
			});
	}
});