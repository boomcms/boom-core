(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.CreatePerson = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-person-create-form').html()),

		events: {
			'click #b-people-group-save': 'saveGroupName'
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			return this;
		},

		saveGroupName: function(e) {
			e.preventDefault();

			this.model.set('name', this.$el.find('#b-people-group-name').val());
			this.model.save();
		}
	});
}(jQuery, Backbone, window.BoomCMS));