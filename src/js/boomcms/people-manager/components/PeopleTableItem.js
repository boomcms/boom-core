(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PeopleTableItem = Backbone.View.extend({
		tagName: 'tr',
		template: _.template($('#b-people-table-item').html()),

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'destroy', this.remove);
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			return this;
		}
	});
}(jQuery, Backbone, window.BoomCMS));