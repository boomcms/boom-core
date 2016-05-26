(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.TemplateManager.TemplateTable = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-template-list').html()),

		initialize: function(options) {
			this.templates = options.templates;
		},

		addTemplate: function(template) {
			var view = new BoomCMS.TemplateManager.TemplateTableItem({model: template}),
				$el = view.render().$el;

			this.$('tbody').append($el);
		},

		render: function() {
			var table = this;

			this.$el
				.html(this.template({
					templates: this.templates
				}));

			this.templates.each(function(template) {
				table.addTemplate(template);
			});

			this.$el
				.tablesorter({
					/**
					Return the value of any form input in a table cell, or the text content of the cell.
					*/
					textExtraction: function(node) {
						var text = $(node)
							.find('input')
							.val();

						return (typeof text === 'undefined') ? $(node).text() : text;
					}
			});

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));