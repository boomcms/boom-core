(function(Backbone, BoomCMS) {
	BoomCMS.Collections.People = Backbone.Collection.extend({
		model: BoomCMS.Person,
		url: BoomCMS.urlRoot + 'person',
		comparator: 'name'
	});
}(Backbone, window.BoomCMS));
