$.widget('boom.textEditor', {
    skin_url: '/vendor/boomcms/boom-core/tinymce/skins/lightgray',

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
                plugins: 'autolink anchor autoresize charmap emoticons hr image table link lists paste searchreplace contextmenu textpattern save media',
                menubar: 'edit insert format image table',
                toolbar: 'save | undo redo | bold italic | bullist numlist | superscript subscript | hr blockquote | image link emoticons',
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
                skin_url: this.skin_url
            });
        } else if (mode === 'inline') {
            top.tinymce.init({
                target: self.element[0],
                inline: true,
                menubar: '',
                oninit: 'setPlainText',
                plugins: 'save paste charmap emoticons link',
                toolbar: 'save | undo redo | bold italic | charmap emoticons | link',
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
                skin_url: this.skin_url
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
        this._trigger('edit', this.element.html());
    },

    setup: function(ed) {
        var element = this.element;

        ed
        .on('focus', function() {
            element.removeClass('b-editable');
        })
        .on('blur', function() {
            ed.execCommand('mceSave');
            element.blur();
            element.addClass('b-editable');
        });
    }
});
