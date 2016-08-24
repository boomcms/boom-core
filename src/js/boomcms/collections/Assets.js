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

		destroy: function() {
			var assets = this;

			return $.ajax({
				url: this.url,
				type: 'delete',
				data: {
					'assets[]': this.getAssetIds()
				}
			})
			.done(function() {
				assets.each(function(model) {
					model.trigger('destroy');
				});
			});
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

		parse: function(data) {
			return data.assets;
		},

		getAllTags: function() {
			var assets = this;

			if (this.allTags === undefined) {
				return $.get(this.url + '/tags').done(function(response) {
					assets.allTags = response;
				});
			}

			return $.Deferred().resolve(this.allTags);
		},

		getAssetIds: function() {
			return this.pluck('id');
		},

		removeTag: function(tag) {
			$.post(this.url + 'tags/remove', {
				assets : this.assets,
				tag : tag
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
