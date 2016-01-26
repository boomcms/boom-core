/**
* @class
* @name boomPage
*/
function boomPage(page_id) {
	this.id = page_id;
	this.baseUrl = '/boomcms/page/';

	boomPage.prototype.add = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post(this.baseUrl + page_id + '/add', function(response) {
			if (response.url) {
				promise.resolve(response);
			} else {
				promise.reject(response);
			}
		});

		return promise;
	};

	boomPage.prototype.addRelatedPage = function(relatedPageId) {
		return $.post(this.baseUrl + this.id + '/relations/' + relatedPageId);
	};

	boomPage.prototype.addTag = function(group, tag) {
		return $.post(this.baseUrl + this.id + '/tags', {
			group : group,
			tag : tag
		});
	};

	boomPage.prototype.delete = function(options) {
		return $.post(this.baseUrl + this.id + '/settings/delete', options);
	};

	boomPage.prototype.embargo = function() {
		var page = this,
			url = this.baseUrl + this.id + '/version/embargo',
			promise = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url: url,
			title: 'Page embargo',
			width: 440
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

		$.post(this.baseUrl + this.id + '/version/embargo')
			.done(function(response) {
				promise.resolve(response);
			});

		return promise;
	};

	boomPage.prototype.requestApproval = function() {
		var url = this.baseUrl + this.id + '/version/request_approval';

		return $.post(url);
	};

	boomPage.prototype.removeRelatedPage = function(relatedPageId) {
		return $.ajax({
			type: 'delete',
			url: this.baseUrl + this.id + '/relations/' + relatedPageId
		});
	};

	boomPage.prototype.removeTag = function(tagId) {
		return $.ajax({
			type: 'delete',
			url: this.baseUrl + this.id + '/tags',
			data: {
				tag: tagId
			}
		});
	};

	boomPage.prototype.revertToPublished = function() {
		var	promise = new $.Deferred(),
			page = this;

		new boomConfirmation('Discard changes', 'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?')
			.done(function() {
				$.post(page.baseUrl + page.id + '/discard')
					.done(function() {
						promise.resolve();
					});
			});

		return promise;
	};

	boomPage.prototype.saveSettings = function(section, data) {
		return $.post(this.baseUrl + this.id + '/settings/' + section, data);
	};

	boomPage.prototype.setFeatureImage = function(asset) {
		return $.post(this.baseUrl + this.id + '/settings/feature', {
			feature_image_id : asset.getId()
		});
	};

	boomPage.prototype.setTitle = function(title) {
		return $.post(this.baseUrl + this.id + 'version/title', {
			title : title
		});
	};

	boomPage.prototype.setTemplate = function(templateId) {
		return $.post(this.baseUrl + this.id + '/version/template/' + templateId);
	};
};