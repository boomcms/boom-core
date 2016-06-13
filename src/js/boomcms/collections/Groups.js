(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.Collections.Groups = Backbone.Collection.extend({
		model: BoomCMS.Group,
		url: BoomCMS.urlRoot + 'group',
		comparator: 'name'	
	});
}(Backbone, BoomCMS));
