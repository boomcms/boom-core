$.widget( 'boom.pageManager', {
	addActionButtons : function($elements, children) {
		var pageManager = this,
			elementsById = {};

		$elements.each(function() {
			var $el = $(this);

			$el.append("<div><a href='#' class='fa fa-plus b-pages-add'><span>Add page</span></a><a href='#' class='fa fa-trash-o b-pages-delete'><span>Delete page</span></a><a href='#' class='fa fa-cog b-pages-settings'><span>Settings</span></a></div>");
			elementsById[$el.find('a').attr('rel')] = $el;
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
		var page = new boomPage($el.data('page-id')),
			$settings = $('<div></div>');

		$settings
			.addClass('b-settings-container')
			.appendTo($('#b-pages'))
			.load('/cms/page/settings/index/' + page.id, function() {
				$settings
					.addClass('open')
					.pageSettings({
						page: page,
						deleteSave: function() {
							$el.remove();
							$settings.remove();
						}
					})
					.pageSettings('show', 'delete');
			});
	},

	editSettings : function($el) {
		var page = new boomPage($el.data('page-id')),
			$el = $('<div></div>');

		$el
			.addClass('b-settings-container')
			.appendTo($('#b-pages'))
			.load('/cms/page/settings/index/' + page.id, function() {
				$el
					.addClass('open')
					.pageSettings({
						page: page
					});
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
			.on('click', '.b-pages-settings', function(e) {
				e.preventDefault();

				pageManager.editSettings($(this).closest('li'));
			});
	}
});