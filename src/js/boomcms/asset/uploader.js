(function($, BoomCMS) {
    'use strict';

    $.widget('boom.assetUploader', {
        uploading: 'uploading',

        uploaderOptions: {
            /**
            @type string
            */
            url: BoomCMS.urlRoot + 'asset',

            /**
            @type string
            @default 'json'
            */
            dataType: 'json',

            /**
            @type boolean
            @default false
            */
            singleFileUploads: false,

            /**
            @type Array
            @default []
            */
            formData: []
        },

        bind: function() {
            var assetUploader = this;

            this.cancelButton.on('click', function() {
                if (assetUploader.fileData.jqXHR !== undefined) {
                    assetUploader.fileData.jqXHR.abort();
                }

                $(this).hide();
                assetUploader.progressBar.progressbar('destroy');
                assetUploader.notify('Upload was cancelled');
            });
        },

        close: function() {
            this.element.hide();
        },

        _create: function() {
            this.cancelButton = this.element.find('.cancel').eq(0);
            this.progressBar = this.element.find('.progress').eq(0);
            this.uploadForm = this.element;
            this.bind();
        },

        _init: function() {
            if (this.options.asset !== undefined) {
                this.uploaderOptions.url = BoomCMS.urlRoot + 'asset/' + this.options.asset.getId() + '/replace',
                this.uploaderOptions.singleFileUploads = true;
            }

            this.uploaderOptions.dropZone = this.options.dropArea;
            this.initUploader();
        },

        initUploader: function() {
            var assetUploader = this;

            this.uploadForm
                .fileupload(this.uploaderOptions)
                .fileupload('option', {
                    start: function(e, data) {
                        assetUploader.uploadStarted(e, data);
                    },
                    progressall: function(e, data) {
                        var percentComplete = parseInt((data.loaded / data.total * 100), 10);

                        assetUploader.updateProgressBar(e, percentComplete);
                    },
                    done: function(e, data) {
                        assetUploader.uploadFinished(e, data);
                    },
                    fail: function(e, data) {
                        assetUploader.uploadFailed(e, data);
                    }
                });
        },

        updateProgressBar: function(e, percentComplete) {
            this.progressBar.progressbar('value', percentComplete);

            this._trigger('uploadProgress', e, [percentComplete]);
        },

        uploadFailed: function(e) {
            this.element.attr('data-status', 'failed');
            this._trigger('uploadFailed', e);
        },

        uploadFinished: function(e, data) {
            this.element.attr('data-status', 'complete');

            this._trigger('uploadFinished', e, data);
        },

        uploadStarted: function(e, data) {
            this.element.attr('data-status', 'uploading');

            this.progressBar.progressbar();
            this.fileData = data;

            this._trigger('uploadStarted', e, data);
        }
    });
}(jQuery, BoomCMS));
