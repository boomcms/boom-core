(function(Backbone, BoomCMS) {
	BoomCMS.Collections.People = Backbone.Collection.extend({
		model: BoomCMS.Person,
		url: '/boomcms/person',
		comparator: 'name'
	});
}(Backbone, window.BoomCMS));
