(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Group = BoomCMS.Model.extend({
        urlRoot: BoomCMS.urlRoot + 'group',

        defaults: {
            id: null
        },

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

        getName: function() {
            return this.get('name');
        },

        getRoles: function(pageId) {
            return this.roles.fetch({data: {page_id: pageId}});
        },

        removeRole: function(roleId, pageId) {
            return $.ajax({
                type: 'delete',
                url: this.roles.url + '/' + roleId,
                data: {
                    page_id : pageId
                }
            });
        }
    });
}(Backbone, BoomCMS));
