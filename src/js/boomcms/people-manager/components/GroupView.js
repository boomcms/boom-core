(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.GroupView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-group-edit').html()),

		events: {
			'click .b-group-delete': 'deleteGroup'
		},

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			return this;
		},

		saveGroup: function() {
			this.model.save();
		}
	});
})(jQuery, Backbone, window.BoomCMS);