(function(Backbone, BoomCMS) {
	BoomCMS.Collections.Pages = Backbone.Collection.extend({
		model: BoomCMS.Page,
		url: BoomCMS.urlRoot + 'search/pages'
	});
}(Backbone, BoomCMS));
