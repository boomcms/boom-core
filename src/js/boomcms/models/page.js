(function(BoomCMS) {
    'use strict';

    BoomCMS.Page = BoomCMS.Model.extend({
        urlRoot: BoomCMS.urlRoot + 'page',

        initialize: function() {
            this.baseUrl = this.urlRoot + '/' + this.getId() + '/';
        },

        add: function() {
            var promise = new $.Deferred();

            $.post(this.baseUrl + 'add', function(response) {
                if (response.url) {
                    promise.resolve(response);
                } else {
                    promise.reject(response);
                }
            });

            return promise;
        },

        addAclGroup: function(groupId) {
            return $.post(this.baseUrl + 'acl/' + groupId);
        },

        addTag: function(group, tag) {
            return $.post(this.baseUrl + 'tags', {
                group : group,
                tag : tag
            });
        },

        addRelatedPage: function(relatedPageId) {
            return $.post(this.baseUrl + 'relations/' + relatedPageId);
        },

        delete: function(options) {
            return $.post(this.baseUrl + 'settings/delete', options);
        },

        embargo: function() {
            var url = this.baseUrl + 'version/embargo',
                promise = new $.Deferred(),
                dialog;

            dialog = new BoomCMS.Dialog({
                url: url,
                title: 'Page embargo',
                width: 440
            }).done(function() {
                $.post(url, dialog.contents.find('form').serialize())
                    .done(function(response) {
                        BoomCMS.Notification('Page embargo saved');
                        promise.resolve(response);
                    });
            });

            return promise;
        },

        hasChildren: function() {
            return this.get('has_children') === true;
        },

        getLastEdited: function() {
            return this.get('version:created_at');
        },

        getParentId: function() {
            return this.get('parent_id');
        },

        getTitle: function() {
            return this.get('title');
        },

        getUrl: function() {
            return this.get('url');
        },

        isRoot: function() {
            return this.get('parent_id') === null;
        },

        isVisible: function() {
            return this.get('visible') === 1;
        },

        publish: function() {
            var promise = new $.Deferred();

            $.post(this.baseUrl + 'version/embargo')
                .done(function(response) {
                    promise.resolve(response);
                });

            return promise;
        },

        requestApproval: function() {
            var url = this.baseUrl + 'version/request-approval';

            return $.post(url);
        },

        removeAclGroup: function(groupId) {
            return $.ajax({
                type: 'delete',
                url: this.baseUrl + 'acl/' + groupId
            });
        },

        removeRelatedPage: function(page) {
            return $.ajax({
                type: 'delete',
                url: this.baseUrl + 'relations/' + page.getId()
            });
        },

        removeTag: function(tagId) {
            return $.ajax({
                type: 'delete',
                url: this.baseUrl + 'tags/' + tagId
            });
        },

        restoreTo: function(versionId) {
            return $.post(this.baseUrl + 'version/restore', {
                versionId: versionId
            });
        },

        saveSettings: function(section, data) {
            return $.post(this.baseUrl + 'settings/' + section, data);
        },

        setEnableAcl: function(enabled) {
            return $.ajax({
                type: 'put',
                url: this.baseUrl + 'acl',
                data: {
                    enabled: enabled
                }
            });
        },

        setFeatureImage: function(asset) {
            return $.post(this.baseUrl + 'settings/feature', {
                feature_image_id: asset.getId()
            });
        },

        setTitle: function(title) {
            return $.post(this.baseUrl + 'version/title', {
                title: title
            });
        },

        setTemplate: function(templateId) {
            return $.post(this.baseUrl + 'version/template/' + templateId);
        },

        toggleVisibility: function() {
            var visible = this.isVisible() ? 0 : 1;

            this.set('visible', visible);

            return $.post(BoomCMS.urlRoot + 'page/' + this.getId() + '/settings/visibility', {
                visible: visible
            });
        }
    });
}(BoomCMS));
