/**
@fileOverview Boom interface for wysihtml5.
*/
/**
* Interface for the wysihtml5 editor.
* @class
* @name window.BoomCMS.textEditor
*/
$.widget('boom.textEditor', {
    /**
    @property mode
    @type string
    */
    mode : 'block',

    /**
    * @function
    @returns {Deferred}
    */
    _create : function () {
        var self = this,
            element = this.element;

        self.mode = element.is('div') ? 'block' : 'inline';
        self.mode = (element.is(':header') || element.is('.standfirst'))? 'text' : self.mode;
        self.original_html = element.html();

        self.toolbar = $('#wysihtml5-toolbar').find('[data-buttonset=' + self.mode  + ']').first().clone(true, true).appendTo('#wysihtml5-toolbar');

        if (self.mode !== 'text') {
            self.instance = new wysihtml5.Editor(element[0], {
                toolbar: self.toolbar[0],
                parserRules:  (self.mode === 'block')? wysihtml5ParserRules : wysihtml5ParserRulesInline, // defined in parser rules set
                useLineBreaks: (self.mode === 'block'),
                contentEditableMode: true,
                autoLink: false,
                uneditableContainerClassname: 'b-asset-embed',
                handleTables: (self.mode === 'block'),
                handleTabKey: false
            });

            // Ensures that default text is wrapped in a paragraph
            if (self.mode === 'block' && element.text() === element.html()) {
                element.html($('<p></p>').text(element.text()));
            }
        } else {
            element
                .attr('contenteditable', true)
                .on('keydown', function(event) {
                    switch(event.which) {
                    case 13:
                        event.preventDefault();
                        break;
                    }
                });

            element[0].onpaste = function(e) {
                var html = e.clipboardData.getData('text/plain'),
                    text = $('<div>' + html + '</div>').text().replace(/\n|\r|\n\r/g, '');

                e.preventDefault();
                top.document.execCommand('insertHTML', false, text);
            };
        }

        element
            .on('focus', function() {
                if ( ! self.toolbar.is(':visible')) {
                    self.showToolbar();
                }
            })
            .on('keypress', function(e) {
                if (self.mode === 'inline' && element.is('p, li, dt, dd, span') && e.which === 13) {
                    e.preventDefault();

                    self.instance.composer.selection.insertHTML('<br>');
                }
            })
            .on('keyup', function(e) {
                if (e.which === wysihtml5.ESCAPE_KEY) {
                    self.cancel(self.element);
                }    
            });

        this.enableAutoSave();

        if (self.mode !== 'text') {
            $(self.instance.composer)
                .on('before:boomdialog', function() {
                    self.disableAutoSave();
                })
                .on('after:boomdialog', function() {
                    self.element.focus();
                    self.enableAutoSave();
                });

            self.instance
                .on('show:dialog', function(options) {
                    if (options.command == 'createBoomLink') {
                        if ( ! wysihtml5.commands.createBoomLink.state(self.instance.composer)) {
                            wysihtml5.commands.createBoomLink.exec(self.instance.composer);
                        }

                        self.toolbar.find('[data-wysihtml5-command=createBoomLink]').removeClass('wysihtml5-command-dialog-opened');
                    }
                })
                .on('tableselect:composer', function() {
                    window.BoomCMS.page.toolbar.element.width('160px');
                    self.toolbar.parents('#b-topbar').width('160px');
                    top.$('body').first().animate({'margin-left': '160px'}, 500);

                    $('#wysihtml5-toolbar').width('160px');
                    self.toolbar.find('[data-wysihtml5-hiddentools=table]').addClass('visible');
                })
                .on('tableunselect:composer', function() {
                    self.hideTableButtons();
                });
        }

        this.toolbar
            .on('mousedown', '.b-editor-accept', function(event) {
                event.preventDefault();

                self.disableAutoSave();
            
                if (typeof self.instance !== 'undefined') {
                    self.instance.fire('tableunselect:composer');
                }

                self.element
                    .find('.wysiwyg-tmp-selected-cell')
                    .removeClass('wysiwyg-tmp-selected-cell')
                    .end()
                    .blur();
            
                self.apply(self.element);
                self.enableAutoSave();

                return false;
            })
            .on('mousedown', '.b-editor-cancel', function(event) {
                event.preventDefault();
        
                self.cancel(self.element);
                return false;
            })
            .on('mousedown', '.b-editor-link', function() {
                wysihtml5.commands.createBoomLink.edit(self.instance.composer);
            })
            .on('mousedown', '.b-editor-table', function() {
                self.disableAutoSave();
            })
            .on('click', '.b-editor-table', function(e) {
                e.preventDefault();

                wysihtml5.commands.createTable.exec(self.instance.composer, 'createTable', {
                    rows: 2,
                    cols: 2
                });

                self.element.focus();
                self.enableAutoSave();
                self.instance.fire('tableselect:composer');
            });
    },

    /**
    * @function
    @param {Object} element The element being edited.
    */
    apply: function(element) {
        var html = this.model === 'block' ? this.instance.getValue() : element.html();

        window.BoomCMS.page.toolbar.minimise();
        this.hideToolbar();

        if (this.mode === 'text') {
            html = html.replace(/<br>|\n|\r|\n\r/g, ' ');
            element.html(html);
        }

        this._trigger('edit', html);
    },

    blur: function(element) {
        this.apply(element);
    },

    /**
    * @function
    @param {Object} element The element being edited.
    */
    cancel: function() {
        var textEditor = this;

        this.disableAutoSave();
        this.element.blur();

        if (this.hasBeenEdited()) {
            new BoomCMS.Confirmation('Cancel changes', 'Cancel all changes and exit the editor?')
                .done(function() {
                    textEditor.element.html(textEditor.original_html);
                    textEditor.hideToolbar();
                    
                    textEditor._trigger('edit', textEditor.original_html);
                })
                .fail(function() {
                    textEditor.element.focus();
                    textEditor.enableAutoSave();
                });
        } else {
            textEditor.hideToolbar();
                    
            this._trigger('edit', textEditor.original_html);
        }
    },

    disableAutoSave: function() {
        this.element.unbind('blur');
    },

    enableAutoSave: function() {
        var editor = this;

        this.element.on('blur', function() {
            if ( ! editor.toolbar.children(':focus').length) {
                editor.apply(editor.element);
            }
        });
    },

    hasBeenEdited: function() {
        return this.element.html() !== this.original_html;
    },
    
    hideTableButtons: function() {
        window.BoomCMS.page.toolbar.element.width('60px');
        this.toolbar.parents('#b-topbar').width('60px');
        top.$('body').first().animate({'margin-left': '60px'}, 500);
        $('#wysihtml5-toolbar').width('60px');
        $('#wysihtml5-toolbar [data-wysihtml5-hiddentools=table]').removeClass('visible');
    },

    hideToolbar: function() {
        $('#wysihtml5-toolbar').hide().children('[data-buttonset]').hide();
    },

    showToolbar: function() {
        this.toolbar.show();
        $('#wysihtml5-toolbar').show().children().not(this.toolbar).hide();
    }
});