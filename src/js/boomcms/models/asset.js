(function(BoomCMS) {
	'use strict';

	BoomCMS.Asset = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'asset',
		versions: null,

		getAspectRatio: function() {
			if (!this.getHeight()) {
				return 1;
			}

			return this.getWidth() / this.getHeight();
		},

		getCredits: function() {
			return this.get('credits');
		},

		getDescription: function() {
			return this.get('description');
		},

		getDownloads: function() {
			return this.get('downloads');
		},

		getEditedAt: function() {
			return this.get('edited_at');
		},

		getEmbedCode: function() {
			return $.get(this.getUrl('embed'));
		},

		getExtension: function() {
			return this.get('extension');
		},

		getFilename: function() {
			return this.get('filename');
		},

		getHeight: function() {
			return parseFloat(this.get('height'));
		},

		getMetadata: function() {
			var metadata = this.get('metadata');

			return (metadata !== undefined) ? metadata : {};
		},

		getReadableFilesize: function() {
			return this.get('readable_filesize');
		},

		getTags: function() {
			var asset = this;

			if (this.tags === undefined) {
				return $.get(this.url + '/tags').done(function(response) {
					asset.tags = response;
				});
			}

			return $.Deferred.resolve(this.tags);
		},

		getThumbnailAssetId: function() {
			return this.get('thumbnail_asset_id');
		},

		getTitle: function() {
			return this.get('title');
		},

		getType: function() {
			return this.get('type');
		},

		getUploadedBy: function() {
			if (this.uploadedBy === undefined) {
				this.uploadedBy = new BoomCMS.Person(this.get('uploaded_by'));
			}

			return this.uploadedBy;
		},

		getUploadedTime: function() {
			return this.get('uploaded_time');
		},

		getUrl: function(action, width, height) {
			var url = '/asset/' + this.getId();

			if ((!action || action === 'view') && !(width || height)) {
				return url;
			}

			if (!action && (width || height)) {
				action = 'view';
			}

			url = url + '/' + action;

			if (width || height) {
				url = url + '/' + width + '/' + height;
			}

			return url;
		},

		getWidth: function() {
			return parseFloat(this.get('width'));
		},

		getVersions: function() {
			if (this.versions === null) {
				var versions = this.get('versions');

				versions = (versions !== undefined) ? versions : {};

				for (var i = 0; i < versions.length; i++) {
					versions[i] = new BoomCMS.AssetVersion(versions[i]);
				}

				this.versions = versions;
			}

			return this.versions;
		},

		hasMetadata: function() {
			return Object.keys(this.getMetadata()).length > 1;
		},

		hasPreviousVersions: function() {
			return this.getVersions().length > 1;
		},

		isImage: function() {
			return this.getType() === 'image';
		},

		isVideo: function() {
			return this.getType() === 'video';
		},

		replaceWith: function(blob) {
			var asset = this,
				data = new FormData();

			data.append('files[]', blob);

			return $.ajax({
				data: data,
				url: this.urlRoot + '/' + this.getId() + '/replace',
				processData: false,
				contentType: false,
				type: 'post'
			}).done(function(data) {
				delete data.id;

				asset.versions = null;
				asset.set(data);

				asset.trigger('change:image');
			});
		},

		revertToVersion: function(versionId) {
			var asset = this;

			return $.post(this.urlRoot + '/' + this.getId() + '/revert', {
				version_id: versionId
			})
			.done(function(data) {
				delete data.id;

				asset.versions = null;
				asset.set(data);

				asset.trigger('change:image');
			});
		}
	});
}(BoomCMS));
