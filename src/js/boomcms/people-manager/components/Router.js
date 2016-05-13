(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.Router = Backbone.Router.extend({
		routes: {
			'group/:group/edit': 'editGroup',
			'group/:group': 'viewGroup',
			'person/:person': 'editPerson'
		},

		initialize: function(options) {
			this.groups = options.groups;
			this.people = options.people;
		},

		editGroup: function(id) {
			var group = this.groups.get(id);

			group.trigger('edit', group);
		},

		viewGroup: function(id) {
			var group = this.groups.get(id);

			group.trigger('view', group);
		},

		editPerson: function(id) {
			var person = this.people.get(id);

			person.trigger('edit', person);
		}
	});
}(jQuery, Backbone, BoomCMS));
