$.widget('boom.pageTitle', $.ui.chunk, {
    lengthCounterCreated : false,

    /* The length at which the title length inidcator turns red */
    softLimit: 70,

    /* The length at which the title won't save */
    hardLimit: 100,

    bind: function() {
        $.ui.chunk.prototype.bind.call(this);

        var self = this;

        this.createLengthCounter(self.getLength());

        this.element.textEditor({
            blur: function() {
                self.hideTitleLengthCounter();
            },
            change: function() {
                self.updateCounterPosition();
                self.updateLengthCounter(self.getLength());
            },
            focus: function() {
                if (self.isUntitled()) {
                    self.element.text('');
                }

                self.showLengthCounter();
            },
            save: function() {
                self._save();
            }
        });

        this.element
            .on('keydown change paste', function() {
                setTimeout(function() {
                    self.updateCounterPosition();
                    self.updateLengthCounter(self.getLength());
                }, 0);
            });
    },

    createLengthCounter: function() {
        this.$counter = $counter = $('<div id="b-title-length"><span></span></div>').hide();

        $(top.document)
            .find('body')
            .first()
            .append($counter);

        var title = this;

        $('<p><a href="#" id="b-title-help">What is this?</a></p>')
            .appendTo(this.$counter)
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

        if ((offset.left - 60) > this.$counter.width()) {
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

    hideTitleLengthCounter: function() {
        this.$counter.hide();
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
            title.element.focus();
        });
    },

    _save: function(title) {
        this.options.currentPage.setTitle(title)
            .done(function(data) {
                if (data.location !== top.window.location) {
                    top.history.replaceState({}, title, data.location);
                }

                BoomCMS.Notification('Page title saved');
                window.BoomCMS.page.toolbar.status.set(data.status);
            });
    },

    showLengthCounter: function() {
        this.$counter.show();
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