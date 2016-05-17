(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PeopleTable = Backbone.View.extend({
		rows: {},
		tagName: 'div',
		template: _.template($('#b-people-table').html()),

		initialize: function(options) {
			this.people = options.people;

			this.listenTo(this.people, 'sort', this.showAllPeople);
			this.listenTo(this.people, 'filter', this.filterPeople);
		},

		addPersonToTable: function(person) {
			var view = new BoomCMS.PeopleManager.PeopleTableItem({model: person}),
				$el = view.render().$el;

			this.rows[person.getId()] = $el;

			this.$('tbody').append($el);
		},

		filterPeople: function(group) {
			var table = this, action;

			this.people.each(function(person) {
				action = (!group || person.groups.get(group.getId())) ? 'show' : 'hide';

				table.rows[person.getId()][action]();
			});
		},

		render: function() {
			var table = this;

			this.$el.html(this.template());

			this.people.each(function(person) {
				table.addPersonToTable(person);
			});

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));