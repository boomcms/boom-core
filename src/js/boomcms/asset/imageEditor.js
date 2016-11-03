(function($) {
    'use strict';

    $.widget('boom.imageEditor', {
        imageSelector: '#b-imageeditor-image',
        cropButtonSelector: '#b-imageeditor-crop',
        isCropping: false,

        bind: function() {
            var imageEditor = this;

            this.$toolbar = this.element.find('#b-imageeditor-toolbar');

            this.element
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
                })
                .on('click', '.b-imageeditor-save', function() {
                    imageEditor.getImageBlob().done(function(blob) {
                        imageEditor._trigger('save', null, blob);
                    });
                });
        },

        blobToBase64: function(blob) {
            var deferred = $.Deferred();

            var reader = new window.FileReader();
            reader.readAsDataURL(blob); 
            reader.onloadend = function() {
                deferred.resolve(reader.result);                
            };

            return deferred;
        },

        _init: function() {
            this.createCanvas();
            this.bind();
        },

        createCanvas: function() {
            this.$cropImage = $('<img>').css('display', 'none');
            this.$cropImage.appendTo(this.element.find('.image-container'));

            this.loadImage();
        },

        cropImage: function(x, y, width, height) {
            var imageEditor = this,
                canvas = this.element.find('canvas').get(0),
                deferred = $.Deferred();

            this.getImageBase64().done(function(base64) {
                var img = new Image(),
                    context = canvas.getContext('2d');

                if (canvas.width > width) {
                    canvas.width = width;
                }

                if (canvas.height > height) {
                    canvas.height = height;
                }

                img.onload = function() {
                    context.clearRect(0, 0, canvas.width, canvas.height);
                    context.drawImage(img, x, y, width, height, 0, 0, width, height);
                    deferred.resolve('');
                };

                img.src = base64;
                imageEditor.saveImageDimensions();
            });

            return deferred;
        },

        getImageBase64: function() {
            var imageEditor = this,
                deferred = $.Deferred();

            this.getImageBlob().done(function(blob) {
                imageEditor.blobToBase64(blob).done(function(base64) {
                    deferred.resolve(base64);
                });
            });

            return deferred;
        },

        getImageBlob: function() {
            var deferred = $.Deferred();

            this.element.find(this.imageSelector).get(0).toBlob(function(blob) {
                deferred.resolve(blob);
            });

            return deferred;
        },

        hideCropTool: function() {
            this.isCropping = false;

            // Using the Jcrop destroy method causing a JS error when we try to crop again.
            // So we manually remove the data and DOM element instead.
            this.$cropImage.removeData('Jcrop');
            this.element.find('.jcrop-active').remove();

            this.$toolbar
                .children('.b-button')
                .not(this.cropButtonSelector)
                .prop('disabled', false);

            this.toggleCropTools();
        },

        loadImage: function() {
            var imageEditor = this,
                $el = this.element,
                $image = $el.find(this.imageSelector);

            if ($image.is('canvas')) {
                var $new = $('#b-imageeditor-original')
                    .clone()
                    .attr('id', this.imageSelector.replace('#', ''));

                $image.replaceWith($new);
                $image = $el.find(this.imageSelector);
            }

            $image.on('load', function() {
                imageEditor.saveImageDimensions();

                Caman(imageEditor.imageSelector, function () {
                    imageEditor.$canvas = $el.find('canvas:first-of-type');
                    imageEditor.canvas = imageEditor.$canvas[0];

                    imageEditor.element
                        .find('.crop-tools select')
                        .append(
                            $('<option>')
                                .val(parseFloat(imageEditor.imageWidth / imageEditor.imageHeight))
                                .text('Initial')
                        );
                });
            });
        },

        saveImageDimensions: function() {
            this.imageWidth = this.element.find(this.imageSelector).width();
            this.imageHeight = this.element.find(this.imageSelector).height();
            this.aspectRatio = this.imageWidth / this.imageHeight;
        },

        showCropTool: function() {
            var $el = this.element,
                imageEditor = this;

            this.$toolbar
                .children('.b-button')
                .not(this.cropButtonSelector)
                .prop('disabled', true);

            this.isCropping = true;
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
        },

        toggleCropTools: function() {
            this.$toolbar.find('.crop-tools').slideToggle();
        }
    });
}(jQuery));
