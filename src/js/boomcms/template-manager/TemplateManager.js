(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.TemplateManager = Backbone.View.extend({
		el: 'body',
		
		initialize: function(options) {
			this.templates = new BoomCMS.Collections.Templates(options.templates);
			this.$container = this.$('#b-container');

			this.$container.html(new BoomCMS.TemplateManager.TemplateTable({
				templates: this.templates
			}).render().el);
		}
	});
}(jQuery, Backbone, BoomCMS));
