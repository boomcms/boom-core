$.widget('boom.textEditor', {
    skin_url: '/vendor/boomcms/boom-core/tinymce/skins/lightgray',
    image_upload_url: '/boomcms/asset/create-from-blob',

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
            top.tinymce.init({
                target: element[0],
                inline: true,
                plugins: 'autolink anchor autoresize charmap hr image imagetools table link lists paste searchreplace contextmenu textpattern save media',
                menubar: 'edit insert format image table',
                toolbar: 'save | undo redo | bold italic | bullist numlist | superscript subscript | hr blockquote | image link',
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
                automatic_uploads: true
            });
        } else if (mode === 'inline') {
            top.tinymce.init({
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
                },
                skin_url: this.skin_url
            });
        } else {
            top.tinymce.init({
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
                },
                skin_url: this.skin_url,
                custom_ui_selector: '#b-title-length'
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

    save: function() {
        this._trigger('save', this.editor.getContent());
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

                element.removeClass('b-editable');
            })
            .on('blur', function() {
                textEditor._trigger('blur');

                // Ensures that any edited images are uploaded before the content is saved.
                element.find('img').blur();

                editor.execCommand('mceSave');
                element.blur();
                element.addClass('b-editable');
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
