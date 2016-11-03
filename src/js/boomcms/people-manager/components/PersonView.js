(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager.PersonView = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-person-view-template').html()),

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
}(jQuery, Backbone, BoomCMS));