function boomPageUrl(id, pageId) {
	this.id = id;
	this.pageId = pageId;

	boomPageUrl.prototype.add = function(location) {
		var deferred = new $.Deferred(),
			pageId = this.pageId;

		$.post('/boomcms/page/' + pageId + '/urls', {location : location})
			.done(function(response) {
				if (response) {
					if (typeof response.existing_url_id !== 'undefined') {
						var url = new boomPageUrl(response.existing_url_id, pageId);
						url.move()
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
				$.ajax({
					type: 'delete',
					url: '/boomcms/page/' + url.pageId + '/urls/' + url.id
				})
				.done(function() {
					deferred.resolve();
				});
			});

		return deferred;
	};

	boomPageUrl.prototype.makePrimary = function() {
		return $.post('/boomcms/page/' + this.pageId + '/urls/' + this.id + '/make-primary');
	};

	boomPageUrl.prototype.move = function() {
		var deferred = new $.Deferred(),
			move_dialog,
			form_url = '/boomcms/page/' + this.pageId + '/urls/' + this.id + '/move',
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
