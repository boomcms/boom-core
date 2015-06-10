/**
* @class
* @name boomPage
*/
function boomPage(page_id) {
	this.id = page_id;

	boomPage.prototype.add = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post('/cms/page/add/' + page_id, function(response) {
			(typeof response.url !== 'undefined')? promise.resolve(response) : promise.reject(response);
		});

		return promise;
	};

	boomPage.prototype.addTag = function(group, tag) {
		return $.post('/cms/page/tags/add/' + this.id, {
			group : group,
			tag : tag
		});
	};

	boomPage.prototype.delete = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		new boomDialog({
			width: 600,
			url: '/cms/page/delete/' + page_id,
			title: 'Please confirm',
			id: 'b-page-confirmdelete'
		}).done(function() {
			$.post('/cms/page/delete/' + page_id, {}, function(response) {
				promise.resolve(response);
			});
		});

		return promise;
	};

	boomPage.prototype.embargo = function() {
		var page = this,
			url = '/cms/page/version/embargo/' + this.id,
			promise = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url: url,
			title: 'Page embargo',
			width: 440,
			onLoad : function() {
				dialog.contents.on('click', '.visibility', function() {
					page.visibility();
					dialog.cancel();
				});
			}
		}).done(function() {
			$.post(url, dialog.contents.find('form').serialize())
			.done(function(response) {
				new boomNotification("Page embargo saved.");
				promise.resolve(response);
			});
		});

		return promise;
	},

	boomPage.prototype.publish = function() {
		var promise = new $.Deferred();

		$.post('/cms/page/version/embargo/' + this.id)
			.done(function(response) {
				promise.resolve(response);
			});

		return promise;
	};

	boomPage.prototype.requestApproval = function() {
		var url = '/cms/page/version/request_approval/' + this.id;

		return $.post(url);
	};

	boomPage.prototype.removeTag = function(tagId) {
		return $.post('/cms/page/tags/remove/' + this.id, {
			tag : tagId
		});
	};

	boomPage.prototype.revertToPublished = function() {
		var	promise = new $.Deferred(),
			page = this;

		new boomConfirmation('Discard changes', 'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?')
			.done(function() {
				$.post('/cms/page/discard/' + page.id)
					.done(function() {
						promise.resolve();
					});
			});

		return promise;
	};

	boomPage.prototype.setTitle = function(title) {
		return $.post('/cms/page/version/title/' + this.id, {
			title : title
		});
	};

	boomPage.prototype.stash = function() {
		var page_id = this.id,
			confirmation = new boomConfirmation('Edit live', 'Discard changes and edit the live page?');

		confirmation
			.done(function() {
				$.boom.log('stashing page edits');

				$.post('/cms/page/stash/' + page_id)
					.done(function(response) {
						top.window.reload();
					});
			});
	};
};