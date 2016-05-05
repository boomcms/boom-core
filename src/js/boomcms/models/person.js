(function(BoomCMS, Backbone) {
	'use strict';

	BoomCMS.Person = Backbone.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'person',

		add: function() {
			var deferred = $.Deferred(),
				person = this,
				dialog;

			dialog = new boomDialog({
				url : this.urlRoot + '/create',
				width: '600px',
				title : 'Create new person',
				closeButton: false,
				saveButton: true
			})
			.done(function() {
				var data = dialog.contents.find('form').serialize();

				person.create(data)
					.done(function(response) {
						deferred.resolve();
					})
					.fail(function() {
						deferred.reject();
					});
			});

			return deferred;
		},

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

		deleteMultiple: function(peopleIds) {
			return 	$.ajax({
				type: 'delete',
				url: this.urlRoot,
				data: {
					'people[]': peopleIds
				}
			});
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
