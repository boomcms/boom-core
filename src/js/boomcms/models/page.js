(function(BoomCMS) {
	'use strict';

	BoomCMS.Page = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'page',

		initialize: function() {
			this.baseUrl = this.urlRoot + '/' + this.getId().toString() + '/';
		},

		add: function() {
			var promise = new $.Deferred(),
				page_id = this.getId();

			$.post(this.baseUrl + 'add', function(response) {
				if (response.url) {
					promise.resolve(response);
				} else {
					promise.reject(response);
				}
			});

			return promise;
		},

		addRelatedPage: function(relatedPageId) {
			return $.post(this.baseUrl + 'relations/' + relatedPageId);
		},

		delete: function(options) {
			return $.post(this.baseUrl + 'settings/delete', options);
		},

		embargo: function() {
			var page = this,
				url = this.baseUrl + 'version/embargo',
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

		hasChildren: function() {
			return this.get('has_children') === true;
		},

		getLastEdited: function() {
			return this.get('edited_time');
		},

		getParentId: function() {
			return this.get('parent_id');
		},

		getTitle: function() {
			return this.get('title');
		},

		getUrl: function() {
			return this.get('url');
		},

		isRoot: function() {
			return this.get('parent_id') === null;
		},

		isVisible: function() {
			return this.get('visible') === 1;
		},

		publish: function() {
			var promise = new $.Deferred();

			$.post(this.baseUrl + 'version/embargo')
				.done(function(response) {
					promise.resolve(response);
				});

			return promise;
		},

		requestApproval: function() {
			var url = this.baseUrl + 'version/request_approval';

			return $.post(url);
		},

		removeRelatedPage: function(relatedPageId) {
			return $.ajax({
				type: 'delete',
				url: this.baseUrl + 'relations/' + relatedPageId
			});
		},

		removeTag: function(tagId) {
			return $.ajax({
				type: 'delete',
				url: this.baseUrl + 'tags/' + tagId
			});
		},

		revertToPublished: function() {
			var	promise = new $.Deferred(),
				baseUrl = this.baseUrl;

			new boomConfirmation('Discard changes', 'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?')
				.done(function() {
					$.post(baseUrl + 'discard')
						.done(function() {
							promise.resolve();
						});
				});

			return promise;
		},

		saveSettings: function(section, data) {
			return $.post(this.baseUrl + 'settings/' + section, data);
		},

		setFeatureImage: function(asset) {
			return $.post(this.baseUrl + 'settings/feature', {
				feature_image_id : asset.getId()
			});
		},

		setTitle: function(title) {
			return $.post(this.baseUrl + 'version/title', {
				title : title
			});
		},

		setTemplate: function(templateId) {
			return $.post(this.baseUrl + 'version/template/' + templateId);
		},
	});
}(BoomCMS));
