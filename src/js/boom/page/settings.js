$.widget('boom.pageSettings', {
	bind: function() {
		var pageSettings = this;

		this.$menu.on('click', 'a', function() {
			pageSettings.show($(this).attr('data-b-page-setting'));
		});
	},

	_create: function() {
		this.page = this.options.page;
		this.$menu = this.element.find('.b-page-settings-menu');
		this.$content = this.element.find('.b-page-settings-content');

		this.bind();
	},

	getUrl: function(section) {
		switch (section) {
			case 'urls':
				return '/cms/page/urls/' + this.page.id;
			case 'tags':
				return '/cms/page/tags/list/' + this.page.id;
			case 'template':
				return '/cms/page/version/template/' + this.page.id;
			case 'drafts':
				return '/cms/page/version/status/' + this.page.id;
			default:
				return '/cms/page/settings/' + section + '/' + this.page.id;
		}
	},

	show: function(section) {
		var pageSettings = this,
			$div = $('<div class="b-page-settings-content"></div>');

		this.$menu
			.find('li')
			.removeClass('fa-caret-right')
			.end()
			.find('a[data-b-page-setting=' + section + ']')
			.parent('li')
			.addClass('fa fa-caret-right');
	
		this.$content.replaceWith($div);
		this.$content = $div;

		this.$content.load(this.getUrl(section), function() {
			var widget = 'pageSettings' + section.ucfirst();

			pageSettings.$content.ui();

			if (typeof(pageSettings.$content[widget]) === 'function') {
				pageSettings.$content[widget]({
					page: pageSettings.page,
					settings: pageSettings,
					done: function(event, data) {
						pageSettings._trigger(section + 'Save', event, data);
					}
				});
			}
		});
	}
});