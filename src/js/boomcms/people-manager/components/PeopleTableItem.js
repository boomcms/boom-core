(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PeopleTableItem = Backbone.View.extend({
		tagName: 'tr',
		template: _.template($('#b-people-table-item').html()),

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'destroy', this.remove);
		}
	});
}(jQuery, Backbone, window.BoomCMS));