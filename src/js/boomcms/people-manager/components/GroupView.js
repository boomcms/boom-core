(function($, Backbone, BoomCMS) {
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
}(jQuery, Backbone, BoomCMS));