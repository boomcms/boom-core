(function(BoomCMS) {
	'use strict';

	BoomCMS.Asset = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'asset',

		getAspectRatio: function() {
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
		}
	});
}(BoomCMS));
