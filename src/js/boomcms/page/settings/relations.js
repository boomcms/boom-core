$.widget('boom.pageSettingsRelations', {
	addRelatedPage: function() {
		var page = this.page,
			$relatedPages = this.element.find('ul'),
			$el = this.element;

		new boomLinkPicker(new boomLink(), {
				external: false,
				asset: false
			})
			.done(function(link) {
				page.addRelatedPage(link.getPageId())
					.done(function() {
						var $li = $('<li></li>')
							.append('<span class="title">' + link.getTitle() + '</span>')
							.append('<span class="uri">' + link.getUrl() + '</span>')
							.append('<a href="#" class="fa fa-trash-o"><span>Remove</span></a>');

						$relatedPages.append($li);
						$el.find('.current').show();
					});
			});
	},

	bind: function() {
		var editor = this,
			page = this.page;

		this.element
			.on('click', '#b-tags-addpage', function() {
				editor.addRelatedPage();
			})
			.on('click', 'li a', function() {
				editor.removeRelatedPage($(this));
			});
	},

	_create: function() {
		this.page = this.options.page;

		this.getRelatedPages();
		this.bind();
	},

	getRelatedPages: function() {
		var $element = this.element;

		$.get('/cms/search/pages', {relatedTo: this.page.id})
			.done(function(pages) {
				var $ul = $element.find('ul');

				if (pages.length) {
					$element.find('.current').show();
				}

				for (var i = 0; i < pages.length; i++) {
					var $li = $('<li>'),
						$title = $('<span>').addClass('title').text(pages[i].title).appendTo($li),
						$uri = $('<span>').addClass('uri').text(pages[i].uri).appendTo($li),
						$delete = $('<a>')
							.attr('href', '#')
							.addClass('fa fa-trash-o')
							.attr('data-page-id', pages[i].id)
							.html('<span>Remove</span>')
							.appendTo($li);

					$ul.append($li);
				}
			});
	},

	removeRelatedPage: function($a) {
		var $el = this.element,
			$relatedPages = $el.find('ul'),
			$current = $el.find('.current');

		this.page.removeRelatedPage($a.attr('data-page-id'))
			.done(function() {
				$a.parent().remove();

				$relatedPages.find('li').length ? $current.show() : $current.hide();
			});
	}
});