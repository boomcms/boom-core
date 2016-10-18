(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.Collections.Albums = Backbone.Collection.extend({
		model: BoomCMS.Album,
		url: BoomCMS.urlRoot + 'album',
		comparator: 'name'	
	});
}(Backbone, BoomCMS));
