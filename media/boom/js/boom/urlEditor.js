function boomUrlEditor(page) {
	this.page = page;
	this.list_url = '/cms/page/urls/' + this.page.id;

	boomUrlEditor.prototype.add = function() {
		var url = new boomPageUrl(),
			urlEditor = this;

		url.add(this.page.id)
			.done(function(response) {
				$.boom.growl.show('Url added.');

				urlEditor.dialog.load(urlEditor.list_url);
			});
	};

	boomUrlEditor.prototype.bind = function() {
		var urlEditor = this,
			page = this.page;

		this.dialog
			.on('change', '.b-urls-primary', function() {
				var $url = $(this).closest('li'),
					is_primary = $url.find('.b-urls-primary').is(':checked')? 1 : 0;

				if (is_primary) {
					urlEditor.makePrimary($url);
				}
			})
			.on('click', '.b-urls-remove', function(e) {
				e.preventDefault();

				urlEditor.delete($(e.target).closest('li'));
			});
	};

	boomUrlEditor.prototype.close = function() {
		$.boom.dialog.destroy(this.dialog);
	};

	boomUrlEditor.prototype.delete = function($li) {
		var id = $li.data('id'),
			url = new boomPageUrl(id);

		url.delete()
			.done(function() {
				$li.remove();

				$.boom.growl.show("The specified URL has been deleted.")
			});
	};

	boomUrlEditor.prototype.open = function() {
		var urlEditor = this,
			page = this.page;

		this.dialog = $.boom.dialog.open({
			url: this.list_url,
			title: 'URL Editor',
			width: 800,
			buttons: [
				{
					text : 'Add URL',
					title : 'Add URL',
					id : 'b-page-urls-add',
					class : 'b-button',
					icons: {primary : 'b-button-icon b-button-icon-add'},
					click: function() {
						urlEditor.add();
					}
				},
				{
					text : 'Close',
					title : 'Close',
					class : 'b-button',
					icons: {primary : 'b-button-icon b-button-icon-accept'},
					click: function() {
						urlEditor.close();
					}
				}
			],
			open: function() {
				urlEditor.bind();
			}
		});
	};

	boomUrlEditor.prototype.makePrimary = function($url) {
		var url = new boomPageUrl($url.data('id'));

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

				$.boom.growl.show("The primary URL of the page has been updated.");
			});
	};
};