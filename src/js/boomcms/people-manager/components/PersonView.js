(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager.PersonView = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-person-view').html())
	});
}(jQuery, Backbone, window.BoomCMS));