(function(BoomCMS, tinymce) {
    'use strict';

    $.widget('boom.textEditor', {
        skin_url: '/vendor/boomcms/boom-core/tinymce/skins/lightgray',
        image_upload_url: '/boomcms/asset/create-from-blob',
        invalid_styles: 'color font font-family font-size line-height background border margin padding',

        /**
        * @function
        @returns {Deferred}
        */
        _create : function () {
            var self = this,
                element = this.element,
                mode = element.is('div') ? 'block' : 'inline';

            mode = (element.is(':header') || element.is('.standfirst'))? 'text' : mode;

            if (mode === 'block') {
                tinymce.init({
                    target: element[0],
                    inline: true,
                    plugins: 'autolink anchor autoresize charmap hr image imagetools table link lists paste searchreplace contextmenu textpattern save media',
                    menubar: 'edit insert format image table',
                    toolbar: 'save | undo redo | styleselect bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | superscript subscript | hr image link',
                    image_caption: true,
                    contextmenu: 'link image | charmap | inserttable cell row column deletetable',
                    link_context_toolbar: true,
                    link_class_list: [
                        {
                            title: 'None',
                            value: ''
                        },
                        {
                            title: 'CTA',
                            value: 'cta'
                        }
                    ],
                    link_title: false,
                    image_context_toolbar: true,
                    image_dimensions: false,
                    file_picker_callback: this.filePicker,
                    media_url_resolver: function (data, resolve) {
                        var link = new BoomCMS.Link(data.url);

                        if (link.isAsset()) {
                            link.getAsset()
                                .getEmbedCode()
                                .done(function(html) {
                                    resolve({html: html});
                                });
                        } else {
                            resolve({html: ''});
                        }
                    },
                    convert_urls: false,
                    save_onsavecallback: function() {
                        self.save();
                    },
                    setup: function(ed) {
                        self.setup(ed);
                    },
                    skin_url: this.skin_url,
                    images_upload_handler: function(blobInfo, success, failure) {
                        self.uploadImage(blobInfo, success, failure);
                    },
                    automatic_uploads: true,
                    style_formats: [
                        {title: 'Heading', block: 'h2'},
                        {title: 'Sub-heading', block: 'h3'},
                        {title: 'Paragraph', block: 'p'},
                        {title: 'Quote', block: 'blockquote'}
                   ],
                    media_alt_source: false,
                    media_dimensions: false,
                    invalid_styles: self.invalid_styles,
                    browser_spellcheck: true
                });
            } else if (mode === 'inline') {
                tinymce.init({
                    target: self.element[0],
                    inline: true,
                    menubar: '',
                    oninit: 'setPlainText',
                    plugins: 'save paste charmap link',
                    toolbar: 'save | undo redo | bold italic | charmap | link',
                    file_picker_callback: this.filePicker,
                    file_picker_types: 'file',
                    link_context_toolbar: true,
                    selection_toolbar: '',
                    save_onsavecallback: function() {
                        self.save();
                    },
                    setup: function(ed) {
                        self.setup(ed);
                        self.removeTinymceClass();
                    },
                    skin_url: this.skin_url,
                    invalid_styles: self.invalid_styles,
                    browser_spellcheck: true
                });
            } else {
                tinymce.init({
                    target: self.element[0],
                    inline: true,
                    menubar: '',
                    oninit: 'setPlainText',
                    plugins: 'save paste charmap',
                    toolbar: 'save | undo redo | charmap',
                    save_onsavecallback: function() {
                        self.save();
                    },
                    setup: function(ed) {
                        self.setup(ed);
                        self.removeTinymceClass();
                    },
                    skin_url: this.skin_url,
                    custom_ui_selector: '#b-title-length',
                    invalid_styles: self.invalid_styles,
                    browser_spellcheck: true
                });
            }
        },

        filePicker: function(callback, value, meta) {
            var existing = new BoomCMS.Link(value);

            if (meta.filetype === 'file') {
                new BoomCMS.LinkPicker(existing)
                    .done(function(link) {
                        callback(link.getUrl(), {text: link.getTitle()});
                    });
            } else if (meta.filetype === 'media') {
                BoomCMS.AssetPicker(existing.getAsset())
                    .done(function(asset) {
                        if (asset.getId() > 0) {
                            callback(asset.getUrl(), {
                                poster: asset.getUrl('thumb'),
                                width: asset.getWidth(),
                                height: asset.getHeight()
                            });
                        }
                    });
            } else {
                BoomCMS.AssetPicker(existing.getAsset(), {
                        type: 'image'
                    })
                    .done(function(asset) {
                        if (asset.getId() > 0) {
                            callback(asset.getUrl(), {
                                alt: asset.getTitle(),
                                width: asset.getWidth(),
                                height: asset.getHeight()
                            });
                        }
                    });
            }
        },

        /**
         * Removes the mce-content-body class from the editable element.
         *
         * This is added to elements by TinyMCE and there doesn't appear to be a away of disabling it.
         *
         * This class causes uses for inline elements by messing up the line height.
         * As inline elements can't contain tables, images, etc. there's no need for this class to be added.
         *
         * @returns {undefined}
         */
        removeTinymceClass: function() {
            var $el = this.element;

            setTimeout(function() {
                $el.removeClass('mce-content-body');
            }, 0);
        },

        save: function() {
            var textEditor = this;

            this.editor.uploadImages(function() {
                textEditor._trigger('save', null, textEditor.editor.getContent());
            });
        },

        setup: function(editor) {
            var textEditor = this,
                element = this.element;

            this.editor = editor;

            editor
                .on('change undo redo', function(e) {
                    textEditor._trigger('change', editor.getContent());
                })
                .on('focus', function() {
                    textEditor._trigger('focus');

                    element.removeClass(BoomCMS.editableClass);
                })
                .on('blur', function() {
                    textEditor._trigger('blur');

                    editor.execCommand('mceSave');
                    element.blur();
                    element.addClass(BoomCMS.editableClass);
                });
        },

        uploadImage: function(blobInfo, success, failure) {
            var xhr, formData;

            xhr = new XMLHttpRequest();
            xhr.open('POST', this.image_upload_url);
            xhr.setRequestHeader('X-CSRF-TOKEN', BoomCMS.getCsrfToken());

            xhr.onload = function() {
              var json;

              if (xhr.status !== 200) {
                return failure('HTTP Error: ' + xhr.status);
              }

              json = JSON.parse(xhr.responseText);

              if (!json || typeof json.location !== 'string') {
                return failure('Invalid JSON: ' + xhr.responseText);
              }

              success(json.location);
            };

            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('url', top.location.url);

            xhr.send(formData);
        }
    });
}(BoomCMS, top.tinymce));
