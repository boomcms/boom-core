(function(BoomCMS) {
	'use strict';

	BoomCMS.Asset = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'asset',

		getEmbedCode: function() {
			return $.get(this.getUrl('embed'));
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
		}
	});
}(BoomCMS));
