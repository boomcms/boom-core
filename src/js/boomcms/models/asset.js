(function(BoomCMS) {
    'use strict';

    BoomCMS.Asset = BoomCMS.Model.extend({
        urlRoot: BoomCMS.urlRoot + 'asset',

        getAspectRatio: function() {
            if (!this.getHeight()) {
                return 1;
            }

            return this.getWidth() / this.getHeight();
        },

        getCredits: function() {
            return this.get('credits');
        },

        getDescription: function() {
            return this.get('description');
        },

        getDownloads: function() {
            return this.get('downloads');
        },

        getEditedAt: function() {
            return this.get('edited_at');
        },

        getEmbedCode: function() {
            return $.get(this.getUrl('embed'));
        },

        getExtension: function() {
            return this.get('extension');
        },
        getFilename: function() {
            return this.get('filename');
        },

        getHeight: function() {
            return parseFloat(this.get('height'));
        },

        getMetadata: function() {
            var metadata = this.get('metadata');

            return (metadata !== undefined) ? metadata : {};
        },

        getPublishedAt: function() {
            return this.get('published_at');
        },

        getReadableFilesize: function() {
            return this.get('readable_filesize');
        },

        getThumbnailAssetId: function() {
            return this.get('thumbnail_asset_id');
        },

        getTitle: function() {
            return this.get('title');
        },

        getType: function() {
            return this.get('type');
        },

        getUploadedBy: function() {
            return new BoomCMS.Person(this.get('uploaded_by'));
        },

        getUploadedTime: function() {
            return this.get('created_at');
        },

        getUrl: function(action, width, height) {
            var url = '/asset/' + this.getId();

            if ((!action || action === 'view') && !(width || height)) {
                return url;
            }

            if (!action && (width || height)) {
                action = 'view';
            }

            url = url + '/' + action;

            if (width || height) {
                if (height === undefined) {
                    height = 0;
                }

                url = url + '/' + width + '/' + height;
            }

            return url;
        },

        getWidth: function() {
            return parseFloat(this.get('width'));
        },

        getVersions: function() {
            var versions = this.get('versions') || [],
                assetVersions = [];

            for (var i = 0; i < versions.length; i++) {
                assetVersions.push(new BoomCMS.AssetVersion(versions[i]));
            }

            return assetVersions;
        },

        hasMetadata: function() {
            return Object.keys(this.getMetadata()).length > 1;
        },

        hasPreviousVersions: function() {
            return Object.keys(this.getVersions()).length > 1;
        },

        isAudio: function() {
            return this.getType() === 'audio';
        },

        isDocument: function() {
            return this.getType() === 'doc';
        },

        isImage: function() {
            return this.getType() === 'image';
        },

        isPublic: function() {
            return this.get('public') === true;
        },

        isVideo: function() {
            return this.getType() === 'video';
        },

        replaceWith: function(blob) {
            var asset = this,
                data = new FormData();

            data.append('files[]', new File([blob], this.getFilename()));

            return $.ajax({
                data: data,
                url: this.urlRoot + '/' + this.getId() + '/replace',
                processData: false,
                contentType: false,
                type: 'post'
            }).done(function(data) {
                delete data.id;

                asset.set(data);
                asset.trigger('change:image', asset);
            });
        },

        revertToVersion: function(versionId) {
            var asset = this;

            return $.post(this.urlRoot + '/' + this.getId() + '/revert', {
                version_id: versionId
            })
            .done(function(data) {
                delete data.id;

                asset.set(data);
                asset.trigger('change:image', asset);
            });
        }
    });
}(BoomCMS));
