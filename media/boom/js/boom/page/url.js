function boomPageUrl(id) {
	this.id = id;

	boomPageUrl.prototype.add = function(page_id) {
		var url = this,
			deferred = new $.Deferred(),
			dialog;

		dialog = $.boom.dialog.open({
			url: '/cms/page/urls/add?page_id=' + page_id,
			title: 'Add URL',
			width: 500,
			callback: function() {
				var location = $(this).find('input[name=url]').val();

				url.addWithLocation(page_id, location)
					.done(function() {
						deferred.resolve();
						$.boom.dialog.destroy(dialog);
					});
			}
		});

		return deferred;
	};

	boomPageUrl.prototype.addWithLocation = function(page_id, location) {
		var deferred = new $.Deferred();

		$.boom.post('/cms/page/urls/add?page_id=' + page_id, {location : location})
			.done(function(response) {
				if (response) {
					response = $.parseJSON(response);

					if (response.existing_url_id) {
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
			deferred = new $.Deferred();

		$.boom.dialog.confirm('Please confirm', 'Are you sure you want to remove this URL? <br /><br /> This will delete the URL from the database and cannot be undone!')
			.done(function() {
				$.boom.post('/cms/page/urls/delete/' + url.id)
				.done(function() {
					deferred.resolve();
				});
			});

		return deferred;
	};

	boomPageUrl.prototype.makePrimary = function(is_primary) {
		return $.boom.post('/cms/page/urls/make_primary/' + this.id);
	};

	boomPageUrl.prototype.move = function(page_id) {
		var deferred = new $.Deferred(),
			move_dialog,
			form_url = '/cms/page/urls/move/' + this.id + '?page_id=' + page_id,
			dialog;

		dialog = $.boom.dialog.open({
			url : form_url,
			title : 'Move url',
			deferred: deferred,
			width : '500px',
			callback : function() {
				$.boom.post(form_url)
					.done(function(response) {
						deferred.resolve(response);
					});

				$.boom.dialog.destroy(dialog);
			}
		});

		return deferred;
	};
}