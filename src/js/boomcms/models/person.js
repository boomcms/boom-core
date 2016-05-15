(function(BoomCMS, Backbone) {
	'use strict';

	BoomCMS.Person = Backbone.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'person',

		addGroup: function(groupId) {
			return this.addRelationship('group', groupId);
		},

		addRelationship: function(type, id) {
			return $.ajax({
				url: this.urlRoot + '/' + this.id + '/' + type + '/' + id,
				type: 'put'
			});
		},

		addSite: function(siteId) {
			return this.addRelationship('site', siteId);
		},

		defaults: {
			id: null,
			last_login: null
		},

		getEmail: function() {
			return this.get('email');
		},

		getName: function() {
			return this.get('name');
		},

		removeGroup: function(groupId) {
			return this.removeRelationship('group', groupId);
		},

		removeRelationship: function(type, id) {
			return $.ajax({
				type: 'delete',
				url: this.urlRoot + '/' + this.id + '/' + type + '/' + id
			});
		},

		removeSite: function(siteId) {
			return this.removeRelationship('site', siteId);
		}
	});
}(window.BoomCMS, Backbone));
