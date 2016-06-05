(function(Backbone, BoomCMS) {
	BoomCMS.Collections.Pages = Backbone.Collection.extend({
		model: BoomCMS.Page,
		url: BoomCMS.urlRoot + 'search/pages',

		findByParent: function(page) {
			var parentId = (page === null) ? null : page.getId();

			this.fetch({
				remove: false,
				data: {
					parent: parentId
				}
			});
		}
	});
}(Backbone, BoomCMS));
