(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PersonView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-person-view').html()),

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			this.$('select[multiple]').chosen();

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));