/**
Create a tree widget for selecting pages.
*/
$.widget('boom.pageTree', {
	options : {
		onPageSelect : function() {}
	},

	bind: function() {
		var pageTree = this;

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

	_create : function() {
		this.bind();
		this.getChildren(null, this.element);
	},

	itemClick : function($node) {
		this.options.onPageSelect(new boomLink($node.attr('href'), $node.attr('data-page-id'), $node.text()));
	},

	getChildren : function(pageId, $ul) {
		var pageTree = this;

		$.get('/cms/search/pages', {parent: pageId})
			.done(function(data) {

				$(data).each(function(i, item) {
					var $li = $('<li></li>')
						.data({
							children : parseInt(item.has_children, 10),
							'page-id' : item.id
						})
						.appendTo($ul);

					$('<a></a>')
						.attr('href', item.url)
						.attr('data-page-id', item.id)
						.text(item.title)
						.appendTo($li);

					if (item.has_children == 1) {
						pageTree.makeExpandable($li);
					}

					if (typeof(pageTree.options.active) !== 'undefined') {
						$ul
							.find('a[data-page-id=' + pageTree.options.active + ']')
							.addClass('active');
					}
				});

			pageTree._trigger('load', null, {
				elements : $ul.find('li'),
				children : data
			});
		});
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
			var $ul = $('<ul></ul>');

			$li.append($ul);
			this.getChildren($li.data('page-id'), $ul);
		} else {
			$ul.show();
		}
	}
});