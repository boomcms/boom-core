function boomImageEditor(imageUrl) {
	this.imageUrl = imageUrl;
	this.imageSelector = '#b-imageeditor-image';
	this.cropButtonSelector = '#b-imageeditor-crop';
	this.url = '/cms/ui/image-editor';
	this.deferred = new $.Deferred();
	this.isCropping = false;
	
	boomImageEditor.prototype.bind = function() {
		var imageEditor = this;
		
		this.$toolbar = this.$element.find('#b-imageeditor-toolbar');
		
		this.$element
			.on('click', '#b-imageeditor-rotate-left', function() {
				Caman(imageEditor.imageSelector, function () {
					this.rotate(-90).render();
					imageEditor.saveImageDimensions();
				});
			})
			.on('click', '#b-imageeditor-rotate-right', function() {
				Caman(imageEditor.imageSelector, function () {
					this.rotate(90).render();
					imageEditor.saveImageDimensions();
				});
			})
			.on('click', '#b-imageeditor-crop-cancel', function() {
				imageEditor.hideCropTool();
			})
			.on('click', this.cropButtonSelector, function() {
				if (imageEditor.isCropping) {
					imageEditor.hideCropTool();
				} else {
					imageEditor.showCropTool();
				}
				
				imageEditor.isCropping = !imageEditor.isCropping;
		
				$(this).blur();
			})
			.on('click', '#b-imageeditor-revert', function() {
				imageEditor.loadImage();
			})
			.on('change', '.crop-tools select', function() {
				var $this = $(this);

				imageEditor.$cropImage.Jcrop({
					aspectRatio: $this.find(':selected').val()
				});
			});
	};
	
	boomImageEditor.prototype.blobToBase64 = function(blob) {
		var deferred = new $.Deferred();

		var reader = new window.FileReader();
		reader.readAsDataURL(blob); 
		reader.onloadend = function() {
			deferred.resolve(reader.result);                
		};
		
		return deferred;
	};
	
	boomImageEditor.prototype.createCanvas = function() {
		var imageEditor = this,
			$el = this.$element;
			
		this.$cropImage = $('<img>').css('display', 'none');

		$el
			.find('#b-imageeditor')
			.append(this.$cropImage)
			.end();
	
		this.loadImage();
	};
	
	boomImageEditor.prototype.cropImage = function(x, y, width, height) {
		var imageEditor = this,
			canvas = this.$element.find('canvas').get(0),
			deferred = new $.Deferred();

		this.getImageBase64().done(function(base64) {
			var img = new Image(),
				context = canvas.getContext('2d');

			if (canvas.clientWidth > width) {
				canvas.width = width;
			}
			
			if (canvas.clientHeight > height) {
				canvas.height = height;
			}
			
			img.onload = function() {
				context.clearRect(0, 0, canvas.width, canvas.height);
				context.drawImage(img, x, y, width, height, 0, 0, canvas.width, canvas.height);
				deferred.resolve('');
			};

			img.src = base64;
			imageEditor.saveImageDimensions();
		});
		
		return deferred;
	};
	
	boomImageEditor.prototype.getImageBase64 = function() {
		var imageEditor = this,
			deferred = new $.Deferred();
		
		this.getImageBlob().done(function(blob) {
			imageEditor.blobToBase64(blob).done(function(base64) {
				deferred.resolve(base64);
			});
		});

		return deferred;
	};
	
	boomImageEditor.prototype.getImageBlob = function() {
		var deferred = new $.Deferred();

		this.$element.find(this.imageSelector).get(0).toBlob(function(blob) {
			deferred.resolve(blob);
		});

		return deferred;
	};
	
	boomImageEditor.prototype.hideCropTool = function() {
		this.$cropImage.Jcrop('destroy');

		this.$toolbar
			.children('.b-button')
			.not(this.cropButtonSelector)
			.prop('disabled', false);
	
		this.toggleCropTools();

		this.$canvas.show();
	};
	
	boomImageEditor.prototype.loadImage = function() {
		var imageEditor = this,
			$el = this.$element,
			$image = $el.find(this.imageSelector);
	
		if ($image.is('canvas')) {
			$image.replaceWith($('<img />').attr('id', this.imageSelector.replace('#', '')));
			$image = $el.find(this.imageSelector);
		}

		$image.attr('src', this.imageUrl).on('load', function() {
			imageEditor.saveImageDimensions();
			
			Caman(imageEditor.imageSelector, function () {
				imageEditor.$canvas = $el.find('canvas:first-of-type');
				imageEditor.canvas = imageEditor.$canvas[0];
				
				imageEditor.$element
					.find('.crop-tools select')
					.append(
						$('<option>')
							.val(parseFloat(imageEditor.imageWidth / imageEditor.imageHeight))
							.text('Initial')
					);
			});
		});
	};
	
	boomImageEditor.prototype.open = function() {
		var imageEditor = this;

		this.dialog = new boomDialog({
			url: this.url,
			width: document.documentElement.clientWidth < 1024? '100%' : 1024,
			height: document.documentElement.clientHeight < 768? document.documentElement.clientHeight : 768,
			title: 'Image editor',
			closeButton: false,
			saveButton: true,
			onLoad: function() {
				imageEditor.$element = imageEditor.dialog.contents;
				imageEditor.createCanvas();
				imageEditor.bind();
			}
		})
		.done(function() {
			imageEditor.getImageBlob().then(function(blob) {
				imageEditor.deferred.resolve(blob);
			});
		});
		
		return this.deferred;
	};
	
	boomImageEditor.prototype.saveImageDimensions = function() {
		this.imageWidth = this.$element.find(this.imageSelector).width();
		this.imageHeight = this.$element.find(this.imageSelector).height();
		this.aspectRatio = this.imageWidth / this.imageHeight;
	};
	
	boomImageEditor.prototype.showCropTool = function() {
		var $el = this.dialog.contents,
			imageEditor = this;
	
		this.$toolbar
			.children('.b-button')
			.not(this.cropButtonSelector)
			.prop('disabled', true);
	
		this.toggleCropTools();

		this.getImageBase64()
			.done(function(base64) {
				var crop = {};
			
				imageEditor.$cropImage
					.attr('src', base64)
					.on('load', function() {
						imageEditor.$cropImage
						.Jcrop({
							boxWidth: imageEditor.imageWidth,
							boxHeight: imageEditor.imageHeight,
							setSelect: [
								0,
								0,
								imageEditor.$cropImage[0].naturalWidth,
								imageEditor.$cropImage[0].naturalHeight
							],
							onChange: function(c) {
								crop = c;
							},
							aspectRatio: $el.find('.crop-tools select option:selected').val()
						});

						$el
							.find('.jcrop-active canvas')
							.css({
								width: imageEditor.imageWidth,
								height: imageEditor.imageHeight
							});

						imageEditor.$canvas.hide();
					});

				$el.one('click', '#b-imageeditor-crop-accept', function() {
					if (crop !== {}) {
						imageEditor
							.cropImage(crop.x, crop.y, crop.w, crop.h)
							.done(function() {
								imageEditor.hideCropTool();
							});
					}
				});
			});
	};
	
	boomImageEditor.prototype.toggleCropTools = function() {
		this.$toolbar.find('.crop-tools').slideToggle();
	};
	
	return this.open();
};