(function($, BoomCMS) {
	'use strict';

	$.widget('boom.assetUploader', {
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
			this.cancelButton = this.element.find('.b-assets-upload-cancel').eq(0);
			this.dropArea = this.element.find('.b-assets-upload-container').eq(0);
			this.progressBar = this.element.find('.b-assets-upload-progress').eq(0);
			this.uploadForm = this.element;
			this.originalMessage = this.dropArea.find('.message').html();

			if (this.options.asset !== undefined) {
				this.uploaderOptions.url += '/' + this.options.asset.getId() + '/replace',
				this.uploaderOptions.singleFileUploads = true;
			}

			this.bind();
			this.initUploader();
		},

		initUploader: function() {
			var assetUploader = this,
				uploaderOptions;

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

		notify: function(message) {
			if ( ! message) {
				message = this.originalMessage;
			}

			this.dropArea
				.find('p.message')
				.show()
				.html(message);
		},

		reset: function() {
			this.progressBar
				.css('display', 'none')
				.progressbar('destroy');

			this.cancelButton.hide();

			// If we don't call disable first then when the uploader is reintialized
			// we end up with multiple file uploads taking place.
			this.uploadForm.fileupload('disable').fileupload('destroy');
			this.initUploader();
		},

		updateProgressBar: function(e, percentComplete) {
			this.progressBar.progressbar('value', percentComplete);

			this._trigger('uploadProgress', e, [percentComplete]);
		},

		uploadFailed: function(e, data) {
			var message = 'Errors occurred during file upload:<br />',
				errors = $.parseJSON(data.jqXHR.responseText),
				i;

			for (i = 0; i < errors.length; i++) {
				message = message + errors[i] + '<br />';
			}

			this.notify(message);
			this.reset();

			this._trigger('uploadFailed', e, data);
		},

		uploadFinished: function(e, data) {
			this._trigger('uploadFinished', e, data);
		},

		uploadStarted: function(e, data) {
			this.progressBar
				.css('display', 'block')
				.progressbar();

			this.cancelButton.css('display', 'block');

			this.fileData = data;

			this._trigger('uploadStarted', e, data);
		}
	});
}(jQuery, BoomCMS));
