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
				break;
			case 'tags':
				return '/cms/page/tags/list/' + this.page.id;
				break;
			case 'template':
				return '/cms/page/version/template/' + this.page.id;
				break;
			default:
				return '/cms/page/settings/' + section + '/' + this.page.id;
		}
	},
	
	show: function(section) {
		this.$menu
			.find('li')
			.removeClass('fa-caret-right')
			.end()
			.find('a[data-b-page-setting=' + section + ']')
			.parent('li')
			.addClass('fa fa-caret-right');
		
		this.$content.load(this.getUrl(section));
	}
});