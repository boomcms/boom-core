(function(BoomCMS) {
	'use strict';

	$.widget('boom.pageTree', {
		options : {
			onPageSelect: function() {}
		},

		addPageToList: function(page) {
			var parentId = page.getParentId(),
				$ul = page.isRoot() ? this.element : this.element.find('ul[parent-id=' + parentId + ']'),
				$li = $('<li></li>').data('page', page).appendTo($ul);

			$('<a></a>')
				.attr('href', page.getUrl())
				.attr('data-page-id', page.getId())
				.text(page.getTitle())
				.appendTo($li);

			if (page.hasChildren()) {
				this.makeExpandable($li);
			}

			if (typeof(this.options.active) !== 'undefined') {
				$ul
					.find('a[data-page-id=' + this.options.active + ']')
					.addClass('active');
			}
		},

		bind: function() {
			var pageTree = this;

			this.pages = new BoomCMS.Collections.Pages();
			this.pages.on('add', function(page) {
				pageTree.addPageToList(page);
			});

			this.element
				.on('click', 'a[data-page-id]', function(e) {
					e.preventDefault();

					pageTree.itemClick($(this));
				})
				.on('click', '.b-tree-toggle', function(e) {
					e.preventDefault();

					var $this = $(this);

					$this.toggleClass('expanded');

					if ($this.hasClass('expanded')) {
						pageTree.showChildren($this.closest('li'));
					} else {
						pageTree.hideChildren($this.closest('li'));
					}
				});
		},

		_create: function() {
			this.bind();
			this.getChildren(null, this.element);
		},

		itemClick: function($node) {
			this.options.onPageSelect(new boomLink($node.attr('href'), $node.attr('data-page-id'), $node.text()));
		},

		getChildren: function(page) {
			this.pages.findByParent(page);
		},

		hideChildren: function($li) {
			$li.find('> ul').hide();
		},

		makeExpandable: function($li) {
			$('<span />')
				.addClass('b-tree-toggle')
				.prependTo($li);
		},

		showChildren: function($li) {
			var $ul = $li.find('> ul');

			if ($ul.length === 0) {
				var page = $li.data('page'),
					$ul = $('<ul></ul>').attr('parent-id', page.getId());

				$li.append($ul);
				this.getChildren(page);
			}

			$ul.show();
		}
	});
}(BoomCMS));
