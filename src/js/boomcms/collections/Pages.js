(function(Backbone, BoomCMS) {
	BoomCMS.Collections.Pages = Backbone.Collection.extend({
		model: BoomCMS.Page,
		url: BoomCMS.urlRoot + 'pages'
	});
}(Backbone, BoomCMS));
