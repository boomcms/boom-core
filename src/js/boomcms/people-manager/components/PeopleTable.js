(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PeopleTable = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-people-table').html()),

		events: {
			'submit #b-people-create': 'createPerson'
		},

		initialize: function(options) {
			this.people = options.people;
			this.groups = options.groups;
			this.group = options.group;

			this.listenTo(this.people, 'change add', this.render);
			this.listenTo(this.groups, 'add', this.render);
		},

		addPersonToTable: function(person) {
			var view = new BoomCMS.PeopleManager.PeopleTableItem({model: person}),
				$el = view.render().$el;

			this.$('tbody').append($el);
		},

		createPerson: function(e) {
			e.preventDefault();

			var $form = this.$('form'),
				groups = this.groups,
				person;

			person = this.people.create({
				name: $form.find('input[name=name]').val(),
				email: $form.find('input[name=email]').val()
			}, {
				success: function() {
					$form.find('select option[selected]').each(function() {
						var $this = $(this);

						person.addGroup(groups.get($this.val()));
						person.trigger('change');
					});
				}
			});

			$form[0].reset();
		},

		render: function() {
			var table = this,
				group = this.group;

			this.$el.html(this.template({
				group: group,
				groups: this.groups,
				selectedGroups: new Backbone.Collection([group])
			}));

			this.people.each(function(person) {
				if (!group || person.groups.get(group.getId())) {
					table.addPersonToTable(person);
				}
			});

			this.$('select').chosen();

			return this;
		},

		sortPeople: function() {
			this.people.sort();
		}
	});
}(jQuery, Backbone, BoomCMS));