(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.Collections.Assets = Backbone.Collection.extend({
		model: BoomCMS.Asset,
		url: BoomCMS.urlRoot + 'asset',
		comparator: 'name'	
	});
}(Backbone, BoomCMS));
