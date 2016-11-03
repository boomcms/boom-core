(function($, Backbone, BoomCMS) {
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
                }));

            var $time = this.$('time'),
                lastLogin = $time.attr('datetime') ? moment(this.$('time').attr('datetime')).fromNow() : 'Never';

            this.$('time').text(lastLogin);

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));