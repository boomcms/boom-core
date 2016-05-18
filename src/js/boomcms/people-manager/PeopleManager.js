(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager = Backbone.View.extend({
		el: 'body',

		events: {
			'submit #b-groups-new': 'createGroup',
			'click #b-people-create': 'createPerson'
		},

		initialize: function(options) {
			this.$groupList = this.$('#b-groups-list');
			this.$content = this.$('#b-people-content');

			this.groups = new BoomCMS.Collections.Groups(options.groups);
			this.people = new BoomCMS.Collections.People(options.people);
			this.sites = new BoomCMS.Collections.Sites(options.sites);

			this.listenTo(this.groups, 'edit', this.editGroup);
			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'sort', this.renderGroups);
			this.listenTo(this.groups, 'view', this.viewGroup);
			this.listenTo(this.people, 'edit', this.editPerson);

			this.renderGroups();
			this.renderPeople();

			new BoomCMS.PeopleManager.Router({
				groups: this.groups,
				people: this.people
			});

			Backbone.history.start();
		},

		addGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupListItem({model: group});

			this.$groupList.append(view.render().el);
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
				groups: this.groups,
				people: this.people
			});

			this.show(view);
		},

		editPerson: function(person) {
			var view = new BoomCMS.PeopleManager.PersonView({
				model: person,
				groups: this.groups,
				sites: this.sites
			});

			this.show(view);
		},

		editGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupView({model: group}); 

			this.show(view);
		},

		renderGroups: function() {
			this.$groupList.empty();
			this.groups.each(this.addGroup, this);

			return this;
		},

		renderPeople: function() {
			this.$peopleTable = new BoomCMS.PeopleManager.PeopleTable({
				people: this.people
			}).render().$el;

			this.$content.html(this.$peopleTable);
		},

		show: function(view) {
			this.$content.html(view.render().el);
		},

		viewGroup: function(group) {
			this.people.trigger('filter', group);

			this.$content.html(this.$peopleTable);
		}
	});
}(jQuery, Backbone, BoomCMS));