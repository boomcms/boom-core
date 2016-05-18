(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PeopleTable = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-people-table').html()),

		initialize: function(options) {
			this.people = options.people;

			this.listenTo(this.peeople, 'change:name', this.sortPeople);
			this.listenTo(this.people, 'all sort filter', this.render);
		},

		addPersonToTable: function(person) {
			var view = new BoomCMS.PeopleManager.PeopleTableItem({model: person}),
				$el = view.render().$el;

			this.$('tbody').append($el);
		},

		render: function(e, group) {
			var table = this;

			this.$el.html(this.template({
				group: group
			}));

			this.people.each(function(person) {
				if (!group || person.groups.get(group.getId())) {
					table.addPersonToTable(person);
				}
			});

			return this;
		},

		sortPeople: function() {
			this.people.sort();
		}
	});
}(jQuery, Backbone, BoomCMS));