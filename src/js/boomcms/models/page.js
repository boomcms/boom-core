(function(BoomCMS) {
	'use strict';

	BoomCMS.Page = BoomCMS.Model.extend({
		add: function() {
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
		},

		addRelatedPage: function(relatedPageId) {
			return $.post(this.baseUrl + this.id + '/relations/' + relatedPageId);
		},

		delete: function(options) {
			return $.post(this.baseUrl + this.id + '/settings/delete', options);
		},

		embargo: function() {
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
					new boomNotification("Page embargo saved").show();
					promise.resolve(response);
				});
			});

			return promise;
		},

		getLastEdited: function() {
			return this.get('edited_time');
		},

		getTitle: function() {
			return this.get('title');
		},

		getUrl: function() {
			return this.get('url');
		},

		isVisible: function() {
			return this.get('visible') === 1;
		},

		publish: function() {
			var promise = new $.Deferred();

			$.post(this.baseUrl + this.id + '/version/embargo')
				.done(function(response) {
					promise.resolve(response);
				});

			return promise;
		},

		requestApproval: function() {
			var url = this.baseUrl + this.id + '/version/request_approval';

			return $.post(url);
		},

		removeRelatedPage: function(relatedPageId) {
			return $.ajax({
				type: 'delete',
				url: this.baseUrl + this.id + '/relations/' + relatedPageId
			});
		},

		removeTag: function(tagId) {
			return $.ajax({
				type: 'delete',
				url: this.baseUrl + this.id + '/tags/' + tagId
			});
		},

		revertToPublished: function() {
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
		},

		saveSettings: function(section, data) {
			return $.post(this.baseUrl + this.id + '/settings/' + section, data);
		},

		setFeatureImage: function(asset) {
			return $.post(this.baseUrl + this.id + '/settings/feature', {
				feature_image_id : asset.getId()
			});
		},

		setTitle: function(title) {
			return $.post(this.baseUrl + this.id + '/version/title', {
				title : title
			});
		},

		setTemplate: function(templateId) {
			return $.post(this.baseUrl + this.id + '/version/template/' + templateId);
		},
	});
}(BoomCMS));
