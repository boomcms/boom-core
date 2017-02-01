(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager = Backbone.View.extend({
        el: 'body',

        events: {
            'submit #b-groups-new': 'createGroup'
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
            this.listenTo(this.people, 'edit', this.editPerson);
            this.listenTo(this.people, 'filter', this.showPeopleTable);

            this.renderGroups();

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

            var router = this.router,
                $el = $(e.target).find('input[type=text]'),
                group;
            
            group = this.groups.create({
                name: $el.val()
            }, {
                success: function() {
                    router.navigate('group/' + group.getId() + '/edit', {trigger: true});
                }
            });

            $el.val('');
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

        show: function(view) {
            this.$content.html(view.render().el);
        },

        showPeopleTable: function(group) {
            var peopleTable = new BoomCMS.PeopleManager.PeopleTable({
                people: this.people,
                groups: this.groups,
                group: group
            });

            this.show(peopleTable);
        },

        viewGroup: function(group) {
            this.people.trigger('filter', group);

            this.$content.html(this.$peopleTable);
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager.GroupListItem = Backbone.View.extend({
        tagName: 'li',
        template: _.template($('#b-group-list-item').html()),

        events: {
            'click .delete': 'deleteGroup',
        },

        initialize: function() {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'change', this.sortGroups);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function() {
            this.$el.html(this.template(this.model.toJSON()));

            return this;
        },

        deleteGroup: function(e) {
            e.preventDefault();

            var group = this.model,
                confirmation = BoomCMS.Confirmation('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

            confirmation.done(function() {
                group.destroy();
            });
        },

        sortGroups: function() {
            this.model.collection.sort();
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager.GroupView = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-group-edit').html()),

        events: {
            'blur h2': 'save'
        },

        render: function() {
            this.$el.html(this.template(this.model.toJSON()));

            this.$name = this.$('h2').boomcmsEditableHeading();
            this.$('#b-group-roles').groupPermissionsEditor({group: this.model});

            return this;
        },

        save: function(e) {
            e.preventDefault();

            this.model
                .set('name', this.$name.text())
                .save();
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
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
;(function($, Backbone, BoomCMS) {
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

            BoomCMS.Confirmation('Please confirm', 'Are you sure you want to delete ' + model.getName() + '?')
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
                }));

            var $time = this.$('time'),
                lastLogin = $time.attr('datetime') ? moment(this.$('time').attr('datetime')).fromNow() : 'Never';

            this.$('time').text(lastLogin);

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager.PersonView = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-person-view-template').html()),

        events: {
            'click #b-person-delete': 'deletePerson',
            'blur h2': 'saveName',
            'change select[name=enabled], select[name=superuser]': 'toggleAttribute'
        },

        initialize: function(options) {
            this.groups = options.groups;
            this.sites = options.sites;

            this.listenTo(this.model, 'destroy', this.remove);
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

            this.$name = this.$('.name').boomcmsEditableHeading();

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

            this.model
                .set('name', this.$name.text())
                .save();
        },

        toggleAttribute: function(e) {
            var $select = $(e.target),
                value = $select.find(':selected').val();

            this.model
                .set($select.attr('name'), value)
                .save();
        }
    });
}(jQuery, Backbone, BoomCMS));;(function(Backbone, BoomCMS) {
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

            this.listenTo(this.people, 'destroy', this.goBackOnPersonDelete);
        },

        editGroup: function(id) {
            var group = this.groups.get(id);

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
            this.people.trigger('filter');
        },

        viewGroup: function(id) {
            var group = this.groups.get(id);

            this.people.trigger('filter', group);
        }
    });
}(Backbone, BoomCMS));
