/**
Common functionality for all editable slots
@class
@name $.ui.chunk
*/
$.widget('boom.boomMenu', {
	_create : function() {
		this._build_menu();
		this._bind_profile();
	},

	_bind_profile : function() {
		this.element
			.find('a[href="/cms/profile"]')
			.on('click', function(e) {
				e.preventDefault();

				var url = '/cms/profile';
				$.boom.dialog.open({
					'url': url,
					'title': 'User profile',
					width : 600,
					callback: function() {
						$.post(url, $('#b-people-profile').serialize())
							.done(function() {
								new boomNotification('Profile updated');
							});
					}
				});
			});
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
					var click_ev = document.createEvent("MouseEvent");
					click_ev.initEvent("click", true, true);
					$this[0].dispatchEvent(click_ev);
				};
				menu_items = $.extend(menu_items, item);
			});

		return menu_items;
	}
});