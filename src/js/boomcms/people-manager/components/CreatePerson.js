(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.CreatePerson = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-person-create-form').html()),

		initialize: function(options) {
			this.groups = options.groups;
			this.people = options.people;
		},

		events: {
			'click button': 'createPerson'
		},

		createPerson: function(e) {
			e.preventDefault();

			var person = this.people.create(this.$('form').serializeJSON());
		},

		render: function() {
			this.$el.html(this.template({groups: this.groups}));

			this.$('select').chosen();

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));