(function(BoomCMS, Backbone) {
	'use strict';

	BoomCMS.Group = Backbone.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'group',

		initialize: function() {
			var roles = Backbone.Collection.extend({
				url: this.url() + '/roles'
			});

			this.roles = new roles();
		},

		addRole: function(roleId, allowed, pageId) {
			return this.roles.create({
				role_id : roleId,
				allowed: allowed,
				page_id: pageId
			});
		},

		getRoles: function(pageId) {
			return this.roles.fetch({data: {page_id: pageId}});
		},

		removeRole: function(roleId, pageId) {
			return $.ajax({
				type: 'delete',
				url: this.roles.url,
				data: {
					role_id : roleId,
					page_id : pageId
				}
			});
		}
	});
})(window.BoomCMS, Backbone);
