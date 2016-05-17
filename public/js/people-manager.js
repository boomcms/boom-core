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
			this.$content = this.$('#b-people-content');
			

			this.groups = new BoomCMS.Collections.Groups(options.groups);
			this.people = new BoomCMS.Collections.People(options.people);
			this.sites = new BoomCMS.Collections.Sites(options.sites);

			this.listenTo(this.groups, 'edit', this.editGroup);
			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'change:name', this.sortGroups);
			this.listenTo(this.groups, 'all sort', this.renderGroups);
			this.listenTo(this.groups, 'view', this.viewGroup);
			this.listenTo(this.people, 'change:name', this.sortPeople);
			this.listenTo(this.people, 'edit', this.editPerson);
			this.listenTo(this.router, 'home', this.showAllPeople);

			this.groups.sort();
			this.people.sort();

			this.$peopleTable = new BoomCMS.PeopleManager.PeopleTable({
				people: this.people
			}).render().$el;

			this.router = new BoomCMS.PeopleManager.Router({
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
			view.$el.groupPermissionsEditor({group: group});
		},

		renderGroups: function() {
			this.$groupList.empty();
			this.groups.each(this.addGroup, this);

			return this;
		},

		show: function(view) {
			this.$content.html(view.render().el);
		},

		showAllPeople: function() {
			this.people.trigger('filter', null);

			this.$content.html(this.$peopleTable);
		},

		sortGroups: function() {
			this.groups.sort();
		},

		sortPeople: function() {
			this.people.sort();
		},

		viewGroup: function(group) {
			this.people.trigger('filter', group);

			this.$content.html(this.$peopleTable);
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
		},

		render: function() {
			this.$el.html(this.template({
				groups: this.groups,
				selectedGroups: new BoomCMS.Collections.Groups
			}));

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

		deleteGroup: function(e) {
			e.preventDefault();

			var group = this.model,
				confirmation = BoomCMS.confirm('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

			confirmation.done(function() {
				group.destroy();
			});
		},

		editGroup: function(e) {
			e.preventDefault();

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
			this.listenTo(this.model.groups, 'add remove', this.render);
		},

		render: function() {
			this.$el
				.html(this.template({
					person: this.model,
					groups: this.model.getGroups().models
				}))
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
			'click #b-person-delete': 'deletePerson',
			'blur h2': 'saveName',
			'change select[name=enabled], select[name=superuser]': 'toggleAttribute'
		},

		initialize: function(options) {
			this.groups = options.groups;
			this.sites = options.sites;

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
			var person = this.model,
				groups = this.groups,
				sites = this.sites;

			this.$el.html(this.template({
				person: person,
				groups: groups,
				selectedGroups: this.model.getGroups(),
				sites: sites
			}));

			this.$name = this.$('.name').addClass(BoomCMS.editableClass);

			this.$('select[name="groups[]"]')
					.chosen()
					.change(function(event, data) {
						if (typeof(data.selected) !== 'undefined') {
							return person.addGroup(groups.get(data.selected));
						}

						return person.removeGroup(person.groups.get(data.deselected));
					});

			this.$('select[name=sites]')
					.chosen()
					.change(function(event, data) {
						if (typeof(data.selected) !== 'undefined') {
							return person.addSite(sites.get(data.selected));
						}

						return person.removeSite(person.sites.get(data.deselected));
					});

			return this;
		},

		saveName: function(e) {
			e.preventDefault();

			this.model.set('name', this.$name.text());
			this.$name.addClass(BoomCMS.editableClass);

			this.model.save();
		},

		toggleAttribute: function(e) {
			var $select = $(e.target),
				value = $select.find(':selected').val();

			this.model
				.set($select.attr('name'), value)
				.save();
		}
	});
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.Router = Backbone.Router.extend({
		routes: {
			'': 'home',
			'group/:group/edit': 'editGroup',
			'group/:group': 'viewGroup',
			'person/:person': 'editPerson'
		},

		initialize: function(options) {
			this.groups = options.groups;
			this.people = options.people;

			this.listenTo(this.groups, 'change:id', this.editGroupOnCreate);
			this.listenTo(this.people, 'destroy', this.goBackOnPersonDelete);
		},

		editGroup: function(id) {
			var group = this.groups.get(id);

			group.trigger('edit', group);
		},

		editGroupOnCreate: function(group) {
			this.navigate('group/' + group.getId() + '/edit');

			group.trigger('edit', group);
		},

		editPerson: function(id) {
			var person = this.people.get(id);

			person.trigger('edit', person);
		},

		goBackOnPersonDelete: function(person) {
			if (Backbone.history.getFragment() === 'person/' + person.getId()) {
				window.history.back();
			}
		},

		home: function() {
			this.trigger('home');
		},

		viewGroup: function(id) {
			var group = this.groups.get(id);

			group.trigger('view', group);
		}
	});
}(jQuery, Backbone, BoomCMS));
