	/**
* @class
* @name boomPage
*/
function boomPage(page_id) {
	this.id = page_id;
	this.baseUrl = '/cms/page/';

	boomPage.prototype.add = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post(this.baseUrl + 'add/' + page_id, function(response) {
			(typeof response.url !== 'undefined')? promise.resolve(response) : promise.reject(response);
		});

		return promise;
	};

	boomPage.prototype.addRelatedPage = function(page_id) {
		return $.post(this.baseUrl + 'relations/add/' + this.id, {
			related_page_id: page_id
		});
	};

	boomPage.prototype.addTag = function(group, tag) {
		return $.post(this.baseUrl + 'tags/add/' + this.id, {
			group : group,
			tag : tag
		});
	};

	boomPage.prototype.delete = function() {
		return $.post(this.baseUrl + 'settings/delete/' + this.id);
	};

	boomPage.prototype.embargo = function() {
		var page = this,
			url = this.baseUrl + 'version/embargo/' + this.id,
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

		$.post(this.baseUrl + 'version/embargo/' + this.id)
			.done(function(response) {
				promise.resolve(response);
			});

		return promise;
	};

	boomPage.prototype.requestApproval = function() {
		var url = this.baseUrl + 'version/request_approval/' + this.id;

		return $.post(url);
	};

	boomPage.prototype.removeRelatedPage = function(page_id) {
		return $.post(this.baseUrl + 'relations/remove/' + this.id, {
			related_page_id: page_id
		});
	};

	boomPage.prototype.removeTag = function(tagId) {
		return $.post(this.baseUrl + 'tags/remove/' + this.id, {
			tag : tagId
		});
	};

	boomPage.prototype.revertToPublished = function() {
		var	promise = new $.Deferred(),
			page = this;

		new boomConfirmation('Discard changes', 'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?')
			.done(function() {
				$.post(page.baseUrl + 'discard/' + page.id)
					.done(function() {
						promise.resolve();
					});
			});

		return promise;
	};

	boomPage.prototype.saveSettings = function(section, data) {
		return $.post(this.baseUrl + 'settings/' + section + '/' + this.id, data);
	};

	boomPage.prototype.setFeatureImage = function(asset) {
		return $.post(this.baseUrl + 'settings/feature/' + this.id, {
			feature_image_id : asset.getId()
		});
	};

	boomPage.prototype.setTitle = function(title) {
		return $.post(this.baseUrl + 'version/title/' + this.id, {
			title : title
		});
	};

	boomPage.prototype.setTemplate = function(templateId) {
		return $.post(this.baseUrl + 'version/template/' + this.id, {
			template_id: templateId
		});
	};
};