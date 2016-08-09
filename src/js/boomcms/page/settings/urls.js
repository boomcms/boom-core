$.widget('boom.pageSettingsUrls', {
	baseUrl: '/boomcms/page/{page}/urls',

	add: function() {
		var url = new boomPageUrl(null, this.page.id),
			urlEditor = this;

		url.add(this.element.find('form input[type=text]').val())
			.done(function(response) {
				new boomNotification('Url added').show();

				urlEditor.element.load(urlEditor.list_url);
			});
	},

	bind: function() {
		var urlEditor = this,
			page = this.page;

		this.element
			.on('change', '.b-urls-primary', function() {
				var $url = $(this).closest('li'),
					is_primary = $url.find('.b-urls-primary').is(':checked')? 1 : 0;

				if (is_primary) {
					urlEditor.makePrimary($url);
					urlEditor._trigger('done', null, $url.find('label').text());
				}
			})
			.on('click', '.b-urls-remove', function(e) {
				e.preventDefault();

				urlEditor.delete($(e.target).closest('li'));
			})
			.on('submit', 'form', function(e) {
				e.preventDefault();

				urlEditor.add();
			});
	},

	_create: function() {
		this.page = this.options.page;
		this.list_url = this.baseUrl.replace('{page}', this.page.id);

		this.bind();
	},

	delete: function($li) {
		var id = $li.data('id'),
			url = new boomPageUrl(id, this.page.id);

		url.delete()
			.done(function() {
				$li.remove();

				new boomNotification("The specified URL has been deleted").show();
			});
	},

	makePrimary: function($url) {
		var url = new boomPageUrl($url.data('id'), this.page.id);

		url.makePrimary()
			.done(function() {
				$url
					.parent()
					.find('.b-page-urls-primary')
					.removeClass('b-page-urls-primary')
					.end()
					.find('.b-urls-primary:checked')
					.parent()
					.addClass('b-page-urls-primary');

				new boomNotification("The primary URL of the page has been updated").show();
			});
	}
});