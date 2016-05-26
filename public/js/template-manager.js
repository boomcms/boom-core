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
;(function($, Backbone, BoomCMS) {
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
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.TemplateManager.TemplateTableItem = Backbone.View.extend({
		tagName: 'tr',
		template: _.template($('#b-template-row').html()),

		events: {
			'click .delete': 'deleteTemplate',
			'change input': 'setAttribute',
			'keyup input': 'setAttribute',
			'paste input': 'setAttribute',
			'blur input': 'saveTemplate'
		},

		initialize: function() {
			this.listenTo(this.model, 'destroy', this.remove);
		},

		deleteTemplate: function(e) {
			e.preventDefault();

			var template = this.model;

			BoomCMS.confirm('Please confirm', "Are you sure you want to delete the template '" + template.getName() + "'?")
				.done(function() {
					template.destroy();	
				});
		},

		render: function() {
			this.$el
				.html(this.template({
					template: this.model
				}));

			return this;
		},

		saveTemplate: function() {
			if (this.model.needsSave === true) {
				this.model.save();
	
				BoomCMS.notify('Template ' + this.model.getName() + ' has been saved');
			}
		},

		setAttribute: function(e) {
			var $el = $(e.target);

			this.model.set($el.attr('name'), $el.val());
		}
	});
}(jQuery, Backbone, BoomCMS));