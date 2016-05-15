(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager = Backbone.View.extend({
		el: $('body'),

		events: {
			'submit #b-groups-new': 'createGroup',
			'click #b-people-create': 'createPerson',
			'click #b-people-all': 'showAllPeople'
		},

		initialize: function(options) {
			this.$groupList = this.$('#b-groups-list');
			this.$peopleTable = this.$('#b-people-table');
			this.$content = this.$('#b-people-content');

			this.groups = new BoomCMS.Collections.Groups(options.groups);
			this.people = new BoomCMS.Collections.People(options.people);
			this.router = new BoomCMS.PeopleManager.Router({
				groups: this.groups,
				people: this.people
			});

			this.listenTo(this.groups, 'edit', this.editGroup);
			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'change:name', this.sortGroups);
			this.listenTo(this.groups, 'all sort', this.renderGroups);
			this.listenTo(this.people, 'change:name', this.sortPeople);
			this.listenTo(this.people, 'all sort filter', this.renderPeople);
			this.listenTo(this.people, 'edit', this.editPerson);
			this.listenTo(this.router, 'home', this.showAllPeople);

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

			var $el = $(e.target).find('input[type=text]');
			
			this.groups.create({
				name: $el.val()
			});

			$el.val('');
		},

		createPerson: function(e) {
			e.preventDefault();

			var view = new BoomCMS.PeopleManager.CreatePerson({
				groups: this.groups.models,
				people: this.people
			});

			this.show(view);
		},

		editPerson: function(person) {
			var view = new BoomCMS.PeopleManager.PersonView({
				model: person,
				groups: this.groups.models
			});

			this.show(view);
		},

		editGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupView({model: group}); 

			this.show(view);
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

		show: function(view) {
			this.$content.html(view.render().el);
		},

		showAllPeople: function() {
			this.$content.html(this.$peopleTable);

			return this;
		},

		sortGroups: function() {
			this.groups.sort();
		},

		sortPeople: function() {
			this.people.sort();
		}
	});
}(jQuery, Backbone, BoomCMS));