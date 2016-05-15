(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PersonView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-person-view').html()),

		events: {
			'click .name, .name + a': 'editName',
			'click #b-person-delete': 'deletePerson',
			'blur h2': 'saveName',
			'change select[name=enabled]': 'toggleEnabled'
		},

		initialize: function(options) {
			this.groups = options.groups;

			this.listenTo(this.model, 'destroy', this.remove);
		},

		editName: function(e) {
			e.preventDefault();

			this.$name
				.removeClass(BoomCMS.editableClass)
				.focus();
		},

		deletePerson: function() {
			this.model.destroy();
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

		saveName: function(e) {
			e.preventDefault();

			this.model.set('name', this.$name.text());
			this.$name.addClass(BoomCMS.editableClass);

			this.model.save();
		},

		toggleEnabled: function(e) {
			this.model.set('enabled', this.$('select[name=enabled] :selected').val());

			this.model.save();
		}
	});
}(jQuery, Backbone, BoomCMS));