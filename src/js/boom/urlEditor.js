function boomUrlEditor(page) {
	this.page = page;
	this.list_url = '/cms/page/urls/' + page.id;

	boomUrlEditor.prototype.add = function() {
		var url = new boomPageUrl(),
			urlEditor = this;

		url.add(this.page.id)
			.done(function(response) {
				new boomNotification('Url added.');

				urlEditor.dialog.contents.load(urlEditor.list_url);
			});
	};

	boomUrlEditor.prototype.bind = function() {
		var urlEditor = this,
			page = this.page;

		this.dialog.contents
			.on('change', '.b-urls-primary', function() {
				var $url = $(this).closest('li'),
					is_primary = $url.find('.b-urls-primary').is(':checked')? 1 : 0;

				if (is_primary) {
					urlEditor.makePrimary($url);

					var history = new boomHistory();
					history.replaceState({},
						top.window.document.title,
						'/' + $url.find('label').text()
					);
				}
			})
			.on('click', '.b-urls-remove', function(e) {
				e.preventDefault();

				urlEditor.delete($(e.target).closest('li'));
			});
	};

	boomUrlEditor.prototype.delete = function($li) {
		var id = $li.data('id'),
			url = new boomPageUrl(id);

		url.delete()
			.done(function() {
				$li.remove();

				new boomNotification("The specified URL has been deleted.");
			});
	};

	boomUrlEditor.prototype.open = function() {
		var urlEditor = this,
			page = this.page;

		this.dialog = new boomDialog({
			url: this.list_url,
			title: 'URL Editor',
			width: 800,
			cancelButton : false,
			buttons: [
				{
					text : 'Add URL',
					title : 'Add URL',
					id : 'b-page-urls-add',
					class : 'b-button b-button-withtext',
					icons: {primary : 'b-button-icon b-button-icon-add'},
					click: function() {
						urlEditor.add();
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

				new boomNotification("The primary URL of the page has been updated.");
			});
	};
};