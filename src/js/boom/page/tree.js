/**
Create a tree widget for selecting pages.
*/
$.widget('boom.pageTree', {
	options : {
		onPageSelect : function() {}
	},

	_create : function() {
		var self = this;

		var treeConfig = $.extend({}, $.boom.config.tree, {
			toggleSelected: true,
			onToggle: function(page_id) {
				return self.getChildren(page_id);
			}
		});

		this.element.on('click', 'a', function(e) {
			e.preventDefault();

			self.itemClick($(this));
		});

		this.getChildren(0, this.element);

		this.element.tree('destroy').tree(treeConfig);
	},

	itemClick : function($node) {
		this.highlightItem($node);
		this.options.onPageSelect(new boomLink($node.attr('href'), $node.attr('data-page-id'), $node.text()));
	},

	getChildren : function(page_id, $ul) {
		var list_ready = $.Deferred(),
			pageTree = this;

		$.get('/page/children', {parent : page_id})
			.done(function(data) {
				var children = typeof($ul) !== 'undefined'? $ul : $('<ul></ul>');

				$( data ).each( function( i, item ){
					var li = $('<li></li>')
						.data({
							children : parseInt(item.has_children, 10),
							'page-id' : item.id
						})
						.appendTo( children );

					$('<a></a>')
						.attr('target', '_blank')
						.attr('href', item.url)
						.attr('data-page-id', item.id)
						.text(item.title)
						.appendTo(li);

					if (item.has_children == 1) {
						pageTree.element.tree('set_toggle', li);
					}
				});

			pageTree._trigger('load', null, {
				elements : children.find('li'),
				children : data
			});

			var parent_id = $('input[name=parent_id]').val();
			children.find('[data-page-id=' + page_id + ']').addClass('ui-state-active');

			list_ready.resolve( { childList: children } );
		});

		return list_ready;
	},

	highlightItem : function($item) {
		$item
			.addClass('ui-state-active')
			.parents('.boom-tree')
			.find('a.ui-state-active')
			.not($item)
			.removeClass('ui-state-active');
	}
});