function boomPageUrl(id) {
	this.id = id;

	boomPageUrl.prototype.add = function(page_id) {
		var url = this,
			deferred = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url : '/cms/page/urls/add?page_id=' + page_id,
			title : 'Add URL',
			closeButton: false,
			saveButton: true,
			width : 500
		}).done(function() {
			var location = dialog.contents.find('input[name=url]').val();

			url.addWithLocation(page_id, location)
				.done(function() {
					deferred.resolve();
				});
		});

		return deferred;
	};

	boomPageUrl.prototype.addWithLocation = function(page_id, location) {
		var deferred = new $.Deferred();

		$.post('/cms/page/urls/add?page_id=' + page_id, {location : location})
			.done(function(response) {
				if (response) {
					if (typeof response.existing_url_id !== 'undefined') {
						var url = new boomPageUrl(response.existing_url_id);
						url.move(page_id)
							.done(function() {
								deferred.resolve();
							});
					}
				} else {
					deferred.resolve();
				}
			});

		return deferred;
	};

	boomPageUrl.prototype.delete = function() {
		var url = this,
			deferred = new $.Deferred(),
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to remove this URL? <br /><br /> This will delete the URL from the database and cannot be undone!');

			confirmation
			.done(function() {
				$.post('/cms/page/urls/delete/' + url.id)
				.done(function() {
					deferred.resolve();
				});
			});

		return deferred;
	};

	boomPageUrl.prototype.makePrimary = function(is_primary) {
		return $.post('/cms/page/urls/make_primary/' + this.id);
	};

	boomPageUrl.prototype.move = function(page_id) {
		var deferred = new $.Deferred(),
			move_dialog,
			form_url = '/cms/page/urls/move/' + this.id + '?page_id=' + page_id,
			dialog;

		dialog = new boomDialog({
			url : form_url,
			title : 'Move url',
			deferred: deferred,
			width : '500px'
		});
		dialog.done(function() {
			$.post(form_url)
				.done(function(response) {
					deferred.resolve(response);
				});
		});

		return deferred;
	};
}