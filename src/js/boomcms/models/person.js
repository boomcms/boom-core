(function(BoomCMS) {
    'use strict';

    BoomCMS.Person = BoomCMS.Model.extend({
        urlRoot: BoomCMS.urlRoot + 'person',

        initialize: function() {
            var person = this;

            this.groups = new BoomCMS.Collections.Groups(this.get('groups'));
            this.sites = new BoomCMS.Collections.Sites(this.get('sites'));

            this.groups
                .on('add', function(group) {
                    person.addRelationship('group', group.getId());
                })
                .on('remove', function(group) {
                    person.removeRelationship('group', group.getId());
                });

            this.sites
                .on('add', function(site) {
                    person.addRelationship('site', site.getId());
                })
                .on('remove', function(site) {
                    person.removeRelationship('site', site.getId());
                });
        },

        addGroup: function(group) {
            return this.groups.add(group);
        },

        addSite: function(site) {
            return this.sites.add(site);
        },

        getCreatedAt: function() {
            return this.get('created_at');
        },

        getCreatedBy: function() {
            return this.get('created_by');
        },

        getEmail: function() {
            return this.get('email');
        },

        getGroups: function() {
            return this.groups;
        },

        getLastLogin: function() {
            return this.get('last_login');
        },

        getName: function() {
            return this.get('name');
        },

        isEnabled: function() {
            return this.get('enabled') === true;
        },

        isSuperuser: function() {
            return this.get('superuser') === true;
        },

        removeGroup: function(group) {
            this.groups.remove(group);
        },

        removeSite: function(site) {
            return this.sites.remove(site);
        }
    });
}(BoomCMS));
