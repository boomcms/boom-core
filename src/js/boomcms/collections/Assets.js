(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.Collections.Assets = Backbone.Collection.extend({
		model: BoomCMS.Asset,
		url: BoomCMS.urlRoot + 'asset',
		comparator: 'name',

		addTag: function(tag) {
			$.post(this.url + 'tags/add', {
				assets : this.assets,
				tag : tag
			});
		},

		delete: function() {
			var selection = this,
				deleted = new $.Deferred(),
				confirmation,
				message = this.hasMultipleIds()? 'Are you sure you wanted to delete these assets?' : 'Are you sure you want to delete this asset?'

			confirmation = new boomConfirmation('Please confirm', message);
			confirmation.done(function() {
				$.post(selection.url + 'delete', {
					assets : selection.assets
				})
				.done(function() {
					deleted.resolve();
				});
			});

			return deleted;
		},
	
		download: function() {
			var url = this.url + 'download?',
				assets = [];

			for (var i = 0; i < this.assets.length; i++) {
				assets[i] = 'asset[]=' + this.assets[i];
			}

			url = url + assets.join('&');

			if (this.assets.length > 1) {
				var dialog = new boomDialog({
					msg: '<label><p>Enter the name of the download</p><input type="text" name="filename" value="BoomCMS Asset Download" /></label>',
					width: 400,
					id: 'b-assets-download-filename'
				})
				.done(function() {
					url = url + '&filename=' + dialog.contents.find('input[name=filename]').val();

					window.location = url;
				});
			} else {
				window.location = url;
			}
		},

		removeTag: function(tag) {
			$.post(this.url + 'tags/remove', {
				assets : this.assets,
				tag : tag
			});
		},

		revertToVersion: function(versionId) {
			return $.post(this.url + 'revert/' + this.assets[0], {
				version_id: versionId
			});
		},

		tag: function() {
			var assetSelection = this,
				url = this.url + 'tags/list/' + this.assets.join('-'),
				dialog;

			dialog = new boomDialog({
				url: url,
				title: 'Asset tags',
				width: 440,
				cancelButton : false,
				onLoad: function() {
					dialog.contents.find('#b-tags').assetTagSearch({
						addTag: function(e, tag) {
							assetSelection.addTag(tag);
						},
						removeTag: function(e, tag) {
							assetSelection.removeTag(tag);
						}
					});
				}
			});
		}
	});
}(Backbone, BoomCMS));
