(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.Router = Backbone.Router.extend({
		routes: {
			'#group/:group/edit': 'editGroup',
			'#group/:group': 'viewGroup'
		},

		editGroup: function(group) {
			console.log('edit');
			group.trigger('edit');
		},

		viewGroup: function(group) {
			group.trigger('view');
		}
	});
}(jQuery, Backbone, window.BoomCMS));
