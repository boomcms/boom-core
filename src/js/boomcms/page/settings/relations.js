(function($, BoomCMS) {
	'use strict';

	$.widget('boom.pageSettingsRelations', {
		addRelatedPage: function() {
			var page = this.page,
				pages = this.pages;

			new boomLinkPicker(new boomLink(), {
					external: false,
					asset: false
				})
				.done(function(link) {
					page.addRelatedPage(link.getPageId())
						.done(function() {
							pages.add(new BoomCMS.Page({
								id: link.getPageId(),
								title: link.getTitle(),
								url: link.getUrl()
							}));
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
			var $ul = this.element.find('ul'),
				$current = this.element.find('.current');

			this.page = this.options.page;
			this.pages = new BoomCMS.Collections.Pages();

			this.pages.on('add', function(page) {
				var $li = $('<li>'),
					$title = $('<span>').addClass('title').text(page.getTitle()).appendTo($li),
					$uri = $('<span>').addClass('uri').text(page.getUrl()).appendTo($li),
					$delete = $('<a>')
						.attr('href', '#')
						.addClass('fa fa-trash-o')
						.data('page', page)
						.html('<span>Remove</span>')
						.appendTo($li);

				$ul.append($li);
				$current.show();
			});

			this.getRelatedPages();
			this.bind();
		},

		getRelatedPages: function() {
			this.pages.findByRelatedTo(this.page);
		},

		removeRelatedPage: function($a) {
			var $el = this.element,
				$relatedPages = $el.find('ul'),
				$current = $el.find('.current');

			this.page.removeRelatedPage($a.data('page'))
				.done(function() {
					$a.parent().remove();

					$relatedPages.find('li').length ? $current.show() : $current.hide();
				});
		}
	});
}(jQuery, BoomCMS));