(function(BoomCMS) {
	'use strict';

	BoomCMS.Asset = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'asset',

		getAspectRatio: function() {
			if (!this.getHeight()) {
				return 1;
			}

			return this.getWidth() / this.getHeight();
		},

		getEmbedCode: function() {
			return $.get(this.getUrl('embed'));
		},

		getHeight: function() {
			return parseFloat(this.get('height'));
		},

		getTitle: function() {
			return this.get('title');
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
			}).done(function() {
				asset.trigger('replace');
			});
		},

		revertToVersion: function(versionId) {
			var asset = this;

			return $.post(this.urlRoot + '/' + this.getId() + '/revert', {
				version_id: versionId
			})
			.done(function() {
				asset.trigger('revert');
			});
		}
	});
}(BoomCMS));
