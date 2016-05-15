(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PersonView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-person-view').html()),

		events: {
			'click .name, .name + a': 'editName',
			'blur h2': 'saveName'
		},

		initialize: function(options) {
			this.groups = options.groups;
		},

		render: function() {
			this.$el.html(this.template({
				person: this.model,
				groups: this.groups
			}));

			this.$name = this.$('.name').addClass(BoomCMS.editableClass);
			this.$('select[multiple]').chosen();

			return this;
		},

		editName: function(e) {
			e.preventDefault();

			this.$name
				.removeClass(BoomCMS.editableClass)
				.focus();
		},

		saveName: function(e) {
			e.preventDefault();

			this.model.set('name', this.$name.text());
			this.$name.addClass(BoomCMS.editableClass);

			this.model.save();
		}
	});
}(jQuery, Backbone, BoomCMS));