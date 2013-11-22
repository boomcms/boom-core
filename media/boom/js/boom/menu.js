/**
Common functionality for all editable slots
@class
@name $.ui.chunk
*/
$.widget('boom.boomMenu', {
	_create : function() {
		this._build_menu();
	},

	_build_menu : function() {
		this.element
			.find('span')
			.splitbutton({
				items: this._find_items(),
				width: 'auto',
				menuPosition: 'right',
				split: false
			});
	},

	_find_items : function() {
		var menu_items = {};

		this.element
			.find('ul')
			.children()
			.find('a')
			.each(function() {
				var $this = $(this), item = [];

				item[$this.text()] = function() {
					console.log('hello');
					console.log($this.attr('href'));
					$this[0].click();
				};
				menu_items = $.extend(menu_items, item);
			});

		return menu_items;
	}
});