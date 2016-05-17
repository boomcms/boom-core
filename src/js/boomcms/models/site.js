(function(BoomCMS) {
	'use strict';

	BoomCMS.Site = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'site',

		defaults: {
			id: null
		},

		getName: function() {
			return this.get('name');
		}
	});
}(BoomCMS));
