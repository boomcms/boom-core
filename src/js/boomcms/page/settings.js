$.widget('boom.pageSettings', {
	bind: function() {
		var pageSettings = this;

		this.$menu
			.on('click', 'a', function(e) {
				e.preventDefault();
			})
			.on('click', '.b-page-settings-close', function() {
				pageSettings.close();
			})
			.on('click', 'a[data-b-page-setting]', function() {
				pageSettings.show($(this).attr('data-b-page-setting'));
			})
			.on('click', '.toggle', function() {
				pageSettings.toggleMenu();
			});
	},

	close: function() {
		if (typeof(this.options.close) === 'function') {
			this.options.close();
		} else {
			this.element.hide();
		}
	},

	_create: function() {
		this.page = this.options.page;
		this.$menu = this.element.find('.b-page-settings-menu');
		this.$content = this.element.find('.b-page-settings-content');

		this.bind();
	},

	getUrl: function(section) {
		var prefix = '/boomcms/page/' + this.page.getId() + '/';

		switch (section) {
			case 'urls':
				return  prefix + 'urls';
			case 'relations':
				return prefix + 'relations';
			case 'tags':
				return prefix + 'tags';
			case 'template':
				return prefix + 'version/template';
			case 'drafts':
				return prefix + 'version/status';
			default:
				return prefix + 'settings/' + section;
		}
	},

	show: function(section) {
		var pageSettings = this,
			$div = $('<div class="b-page-settings-content"></div>');

		this.$menu
			.find('li')
			.removeClass('selected')
			.end()
			.find('a[data-b-page-setting=' + section + ']')
			.parent('li')
			.addClass('selected');

		this.$content.replaceWith($div);
		this.$content = $div;

		this.$content.load(this.getUrl(section), function() {
			var widget = 'pageSettings' + section.ucfirst();

			pageSettings.$content.ui();

			if (typeof(pageSettings.$content[widget]) !== 'function') {
				widget = 'pageSettingsDefault';
			}

			pageSettings.$content[widget]({
				page: pageSettings.page,
				section: section,
				settings: pageSettings,
				done: function(event, data) {
					pageSettings._trigger('save', event, data);
					pageSettings._trigger(section + 'Save', event, data);
				}
			});
		});
	},

	toggleMenu: function() {
		this.$menu.toggleClass('open');
	}
});