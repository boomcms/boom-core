(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.TemplateManager = Backbone.View.extend({
		el: 'body',
		
		initialize: function(options) {
			this.templates = new BoomCMS.Collections.Templates(options.templates);
			this.$container = this.$('#b-container');

			this.router = new BoomCMS.TemplateManager.Router({
				templates: this.templates
			});

			this.listenTo(this.router, 'home', this.home);
			this.listenTo(this.templates, 'view', this.viewTemplate);

			Backbone.history.start();
		},

		home: function() {
			this.$container.html(new BoomCMS.TemplateManager.TemplateTable({
				templates: this.templates
			}).render().el);
		},

		viewTemplate: function(template) {
			var view = new BoomCMS.TemplateManager.TemplateView({
				model: template
			});

			this.$container.html(view.render().el);
		}
	});
}(jQuery, Backbone, BoomCMS));
