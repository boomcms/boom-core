(function(Backbone, BoomCMS) {
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
