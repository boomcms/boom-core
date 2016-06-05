(function(Backbone, BoomCMS) {
	BoomCMS.Collections.Pages = Backbone.Collection.extend({
		model: BoomCMS.Page,
		url: BoomCMS.urlRoot + 'search/pages',

		findBy: function(data) {
			this.fetch({
				remove: false,
				data: data
			});
		},

		findByParent: function(page) {
			var parentId = (page === null) ? null : page.getId();

			this.findBy({parent: parentId});
		},

		findByTemplate: function(template) {
			this.findBy({template: template.getId()});
		}
	});
}(Backbone, BoomCMS));
