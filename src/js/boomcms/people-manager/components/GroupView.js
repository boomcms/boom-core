(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.PeopleManager.GroupView = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-group-edit').html()),

        events: {
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
            this.$('#b-group-roles').groupPermissionsEditor({group: this.model});

            return this;
        },

        save: function(e) {
            e.preventDefault();

            this.model.set('name', this.$name.text());
            this.$name.addClass(BoomCMS.editableClass);

            this.model.save();
        }
    });
}(jQuery, Backbone, BoomCMS));