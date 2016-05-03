(function(BoomCMS, Backbone) {
	'use strict';

	BoomCMS.Group = Backbone.Model.extend({
		urlRoot: '/boomcms/group',

		addRole: function(roleId, allowed, pageId) {
			var deferred = $.Deferred(),
				group = this;

			return $.ajax({
				type: 'put',
				url: group.urlRoot + '/' + group.id + '/roles',
				data: {
					role_id : roleId,
					allowed : allowed,
					page_id: pageId
				}
			})
			.done(function(response) {
				deferred.resolve(response);
			});
		},

		getRoles: function(pageId) {
			return $.getJSON(this.urlRoot + '/' + this.id + '/roles?page_id=' + pageId);
		},

		removeRole: function(roleId, pageId) {
			return $.ajax({
				type: 'delete',
				url: this.urlRoot + '/' + this.id + '/roles',
				data: {
					role_id : roleId,
					page_id : pageId
				}
			});
		}
	});
})(window.BoomCMS, Backbone);
