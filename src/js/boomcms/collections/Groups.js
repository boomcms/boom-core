(function(Backbone, BoomCMS) {
	BoomCMS.Collections.Groups = Backbone.Collection.extend({
		model: BoomCMS.Group,
		url: '/boomcms/group',
		comparator: 'name'
	});
}(Backbone, window.BoomCMS));
