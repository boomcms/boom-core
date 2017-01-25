(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager.PeopleTable = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-people-table').html()),

        events: {
            'submit #b-people-create': 'processCreatePersonForm'
        },

        initialize: function(options) {
            this.people = options.people;
            this.groups = options.groups;
            this.group = options.group;

            this.listenTo(this.people, 'change add', this.render);
            this.listenTo(this.groups, 'add', this.render);
        },

        addPersonToGroups: function(person, groupIds) {
            for (var i in groupIds) {
                person.addGroup(this.groups.get(groupIds[i]));
            }

            person.trigger('change');
        },

        createPerson: function() {
            var peopleTable = this,
                $form = this.$('form'),
                person = this.people.create({
                    name: $form.find('input[name=name]').val(),
                    email: $form.find('input[name=email]').val()
                }, {
                    success: function() {
                        peopleTable.addPersonToGroups(person, $form.find('select').val());
                    }
                });
        },

        addPersonToTable: function(person) {
            var view = new BoomCMS.PeopleManager.PeopleTableItem({model: person}),
                $el = view.render().$el;

            this.$('tbody').append($el);
        },

        processCreatePersonForm: function(e) {
            e.preventDefault();

            var $form = this.$('form'),
                email = $form.find('input[name=email]').val(),
                person = this.people.findByEmail(email);

            (person === undefined) ? this.createPerson() : this.addPersonToGroups(person, $form.find('select').val());
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
            this.$('form').find('input[name=name]').focus();

            return this;
        },

        sortPeople: function() {
            this.people.sort();
        }
    });
}(jQuery, Backbone, BoomCMS));
