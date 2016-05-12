(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager = Backbone.View.extend({
		el: $('body'),

		events: {
			'submit #b-groups-new': 'createGroup',
			'click #b-people-create': 'createPerson',
			'click #b-people-all': 'showAllPeople',
		},

		initialize: function() {
			this.$groupList = this.$('#b-groups-list');
			this.$peopleTable = this.$('#b-people-table');
			this.$content = this.$('#b-people-content');
			this.router = new BoomCMS.PeopleManager.Router();

			this.groups = new BoomCMS.Collections.Groups();
			this.people = new BoomCMS.Collections.People();

			this.listenTo(this.groups, 'edit created', this.editGroup);
			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'all sort', this.renderGroups);
			this.listenTo(this.people, 'all sort', this.renderPeople);

			this.groups.fetch();
			this.people.fetch();

			this.showAllPeople();

			Backbone.history.start();
		},

		addGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupListItem({model: group});

			this.$groupList.append(view.render().el);
		},

		addPersonToTable: function(person) {
			var view = new BoomCMS.PeopleManager.PeopleTableItem({model: person});

			this.$peopleTable.find('tbody').append(view.render().el);
		},

		createGroup: function(e) {
			e.preventDefault();

			var $el = $(e.target).find('input[type=text]'),
				group = this.groups.create({
					id: null,
					name: $el.val()
				});

			this.groups.trigger('created', group);

			$el.val('');
		},

		createPerson: function(e) {
			e.preventDefault();

			var view = new BoomCMS.PeopleManager.CreatePerson({
				groups: this.groups.models,
				people: this.people,
			});

			this.$content.html(view.render().el);
		},

		editGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupView({model: group}); 

			this.$content.html(view.render().el);
			view.$el.groupPermissionsEditor({group: group});
		},

		renderGroups: function() {
			this.$groupList.empty();
			this.groups.each(this.addGroup, this);

			return this;
		},

		renderPeople: function() {
			this.$peopleTable.find('tbody').empty();
			this.people.each(this.addPersonToTable, this);

			return this;
		},

		showAllPeople: function() {
			this.$content.html(this.$peopleTable);

			return this;
		}
	});
}(jQuery, Backbone, window.BoomCMS));