$.widget('boom.pageTitle', $.ui.chunk, {
    lengthCounterCreated : false,

    /* The length at which the title length inidcator turns red */
    softLimit: 70,

    /* The length at which the title won't save */
    hardLimit: 100,

    saveOnBlur: false,

    bind: function() {
        $.ui.chunk.prototype.bind.call(this);

        var self = this,
            oldText = this.getTitle();

        this.element.textEditor({
            edit: function() {
                var title = self.getTitle();

                if (title !== '' && title !== oldText && title.length <= self.hardLimit) {
                    self._save(title, oldText);
                }

                oldText = title;
                self.removeTitleLengthCounter();
            }
        });

        this.element
            .on('keydown change paste', function() {
                setTimeout(function() {
                    self.updateCounterPosition();
                    self.updateLengthCounter(self.getLength());
                }, 0);
            })
            .on('focus', function() {
                if (self.isUntitled()) {
                    self.element.text('');
                }

                if (!self.lengthCounterCreated) {
                    self.createLengthCounter(self.getLength());
                    self.lengthCounterCreated = true;
                }
            });
    },

    createLengthCounter: function() {
        this.$counter = $counter = $('<div id="b-title-length"><span></span></div>');

        $(top.document)
                .find('body')
                .first()
                .append($counter);

        var title = this;

        $('<p><a href="#" id="b-title-help">What is this?</a></p>')
            .appendTo(this.$counter)
            .on('mousedown', 'a', function() {
                title.element.textEditor('disableAutoSave');
            })
            .on('keydown', function(e) {
                if (e.which === 13) {
                    title.openHelp();
                }
            })
            .on('click', function(e) {
                e.preventDefault();

                title.openHelp();
            });

        this.updateCounterPosition();
        this.updateLengthCounter(this.getLength());
    },

    edit: function() {},

    getCounterPosition: function() {
        var offset = this.element.offset();
console.log(offset, this.$counter.width());
        if ((offset.left - 60) > this.$counter.width()) {
            console.log('hello');
            return {
                top : offset.top + 'px',
                left : (offset.left - 60 - this.$counter.width()) + 'px'
            };
        }

        return {
            top : (offset.top + this.element.height() + 10) + 'px',
            left : (offset.left - 60) + 'px'
        };
    },

    getCounterColorForLength: function(length) {
        if (length >= this.softLimit) {
            return 'red';
        } else if (length >= this.softLimit * 0.9) {
            return 'orange';
        } else if (length >= this.softLimit * 0.8) {
            return 'yellow';
        }

        return 'green';
    },

    getLength: function() {
        return this.getTitle().length;
    },

    getTitle: function() {
        return this.element.text().trim();
    },

    isUntitled: function() {
        return this.getTitle() === 'Untitled';
    },

    openHelp: function() {
        var title = this;

        new BoomCMS.Dialog({
            url: '/vendor/boomcms/boom-core/html/help/title_length.html',
            width: '600px',
            cancelButton: false
        }).always(function() {
            title.element.textEditor('enableAutoSave');
            title.element.focus();
        });
    },

    removeTitleLengthCounter: function() {
        this.lengthCounterCreated = false;
        $(top.document).find('#b-title-length').remove();
    },

    _save: function(title, old_title) {
        this.options.currentPage.setTitle(title)
            .done(function(data) {
                if (data.location !== top.window.location) {
                    top.history.replaceState({}, title, data.location);
                    BoomCMS.Notification('Page title saved');
                    window.BoomCMS.page.toolbar.status.set(data.status);
                } else {
                    BoomCMS.Notification('Page title saved');
                    window.BoomCMS.page.toolbar.status.set(data);
                }

                var page_title = top.$('title').text().replace(old_title, title);
                top.$('title').text(page_title);
            });
    },

    updateCounterPosition: function() {
        this.$counter.css(this.getCounterPosition());
    },

    updateLengthCounter: function(length) {
        $(top.document).find('#b-title-length')
            .find('span')
            .text(length)
            .end()
            .css('background-color', this.getCounterColorForLength(length));

        var disable_accept_button = (length >= this.hardLimit || length === 0)? true : false;
        var opacity = disable_accept_button? '.35' : 1;
        $('.b-editor-accept')
            .prop('disabled', disable_accept_button)
            .css('opacity', opacity);
    },

    unbind: function() {}
});