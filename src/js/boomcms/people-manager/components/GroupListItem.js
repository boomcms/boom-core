(function($, Backbone, BoomCMS) {
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
}(jQuery, Backbone, BoomCMS));