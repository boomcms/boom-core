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

			this.listenTo(this.groups, 'edit created', this.editGroup);
			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'change created', this.sortGroups);
			this.listenTo(this.groups, 'all sort', this.renderGroups);
			this.listenTo(this.people, 'all sort filter', this.renderPeople);
			this.listenTo(this.people, 'edit created', this.editPerson);
			this.listenTo(this.people, 'created', this.showAllPeople);

			this.showAllPeople();
			this.people.sort();
			this.groups.sort();

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
		}
	});
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
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

			this.people.trigger('created', person);
		},

		render: function() {
			this.$el.html(this.template({groups: this.groups}));

			this.$('select').chosen();

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.GroupListItem = Backbone.View.extend({
		tagName: 'li',
		template: _.template($('#b-group-list-item').html()),

		events: {
			'click .delete': 'deleteGroup',
			'click .edit': 'editGroup'
		},

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'destroy', this.remove);
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			return this;
		},

		deleteGroup: function() {
			var group = this.model,
				confirmation = BoomCMS.confirm('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

			confirmation.done(function() {
				group.destroy();
			});
		},

		editGroup: function() {
			this.model.trigger('edit', this.model);
		}
	});
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.GroupView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-group-edit').html()),

		events: {
			'click #b-people-group-save': 'saveGroupName',
			'click h2, h2 + a': 'editName',
			'blur h2': 'save'
		},

		editName: function(e) {
			e.preventDefault();

			this.$name
				.removeClass(BoomCMS.editableClass)
				.focus();
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			this.$name = this.$('h2').addClass(BoomCMS.editableClass);

			return this;
		},

		save: function(e) {
			e.preventDefault();

			this.model.set('name', this.$name.text());
			this.$name.addClass(BoomCMS.editableClass);

			this.model.save();
		}
	});
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PeopleTableItem = Backbone.View.extend({
		tagName: 'tr',
		template: _.template($('#b-people-table-item').html()),

		events: {
			'click .delete': 'deletePerson'
		},

		deletePerson: function(e) {
			e.preventDefault();

			var model = this.model;

			BoomCMS.confirm('Please confirm', 'Are you sure you want to delete ' + model.getName() + '?')
				.done(function() {
					model.destroy();	
				});
		},

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'destroy', this.remove);
		},

		render: function() {
			this.$el
				.html(this.template(this.model.toJSON()))
				.find('time')
				.timeago();

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
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
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
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
