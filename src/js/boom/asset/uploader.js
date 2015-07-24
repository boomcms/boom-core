$.widget('boom.assetUploader', {
	defaultOptions : {
		/**
		@type string
		@default '/cms/assets/upload'
		*/
		url: '/cms/assets/upload',

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
		formData: [],

		limitMultiFileUploads: 50
	},

	bind : function() {
		var assetUploader = this;

		this.cancelButton.on('click', function() {
			assetUploader.fileData.jqXHR && assetUploader.fileData.jqXHR.abort();

			$(this).hide();
			assetUploader.progressBar.progressbar('destroy');
			assetUploader.notify('Upload was canceled');
		});
	},

	_create : function() {
		this.cancelButton = this.element.find('#b-assets-upload-cancel');
		this.dropArea = this.element.find('#b-assets-upload-container');
		this.progressBar = this.element.find('#b-assets-upload-progress');
		this.uploadForm = this.element;

		this.options = $.extend({}, this.defaultOptions, this.options);

		this.resizeDropArea();
		this.bind();
		this.initUploader();
	},

	initUploader : function() {
		var assetUploader = this,
			uploaderOptions;

		uploaderOptions = $.extend({}, this.options, {
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

		this.uploadForm.fileupload(uploaderOptions);
	},

	notify : function(message) {
		this.dropArea.find('p.message').html(message);
	},

	/**
	 * Make the uploader replace an existing asset rather than upload a new asset.
	 */
	replacesAsset: function(asset) {
		var assetUploader = this,
			originalOptions = this.uploadForm.fileupload('option');

		this.uploadForm.fileupload('option', {
			url: '/cms/assets/replace/' + asset.id,
			singleFileUploads: true,
			done: function(e, data) {
				assetUploader.uploadForm.fileupload('option', originalOptions);
				assetUploader.uploadFinished(e, data);
			},
			fail: function(e, data) {
				assetUploader.uploadForm.fileupload('option', originalOptions);
				assetUploader.uploadFailed(e, data);
			}
		});
	},

	resizeDropArea : function() {
		this.options.dropAreaHeight && this.dropArea.height(this.options.dropAreaHeight);
	},

	updateProgressBar : function(e, percentComplete) {
		this.progressBar.progressbar('value', percentComplete);

		this._trigger('progress', e, [percentComplete]);
	},

	uploadFailed : function(e, data) {
		var message = 'Errors occurred during file upload:<br />',
			errors = $.parseJSON(data.jqXHR.responseText),
			i;

		for (i in errors) {
			message = message + errors[i] + '<br />';
		}

		this.notify(message);
		this.progressBar.progressbar('destroy');
		this.cancelButton.hide();

		this._trigger('fail', e, data);
	},

	uploadFinished : function(e, data) {
		this.notify("File upload completed");
		this.progressBar.progressbar('destroy');
		this.cancelButton.hide();

		this._trigger('done', e, data);
	},

	uploadStarted : function(e, data) {
		this.progressBar.progressbar();
		this.cancelButton.css('display', 'block');

		this.fileData = data;

		this._trigger('start', e, data);
	}
});
