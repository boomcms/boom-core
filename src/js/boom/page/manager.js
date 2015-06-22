$.widget( 'boom.pageManager', {
	addActionButtons : function($elements, children) {
		var pageManager = this,
			elementsById = {};

		$elements.each(function() {
			var $el = $(this);

			$el.append("<div><a href='#' class='b-pages-add'>Add page</a><a href='#' class='b-pages-delete'>Delete page</a><a href='#' class='b-pages-urls'>URLs</a></div>");
			elementsById[$el.find('a').attr('rel')] = $el;
		});

		$(children).each(function(i, child) {
			var $visibilityButton = $("<a class='b-pages-visibility' href='#'>Visibility</a>");

			child.visible? $visibilityButton.addClass('visible') : $visibilityButton.addClass('invisible');

			if (elementsById[child.id]) {
				elementsById[child.id].find('div').append($visibilityButton);
			}
		});
	},

	addPage : function($el) {
		var page = new boomPage($el.data('page-id'));

		page.add()
			.done(function(data) {
				window.open(data.url);
			});
	},

	_create : function() {
		var pageManager = this;

		this.element
			.pageTree({
				load : function(e, data) {
					pageManager.addActionButtons(data.elements, data.children);
				},
				onPageSelect : function(link) {
					window.open(link.getUrl());
				}
			});

		this.addActionButtons(this.element.find('li'), {});
	},

	deletePage : function($el) {
		var page = new boomPage($el.data('page-id'));

		page.delete()
			.done(function() {
				$el.remove();
			});
	},

	editURLs : function($el) {
		var page = new boomPage($el.data('page-id'));

		page.urls();
	},

	editVisibility : function($el) {
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
			.on('click', '.b-pages-add', function(e) {
				e.preventDefault();

				pageManager.addPage($(this).closest('li'));
			})
			.on('click', '.b-pages-urls', function(e) {
				e.preventDefault();

				pageManager.editURLs($(this).closest('li'));
			})
			.on('click', '.b-pages-visibility', function(e) {
				e.preventDefault();

				pageManager.editVisibility($(this).closest('li'));
			});
	}
});