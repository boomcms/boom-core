(function(BoomCMS) {
    'use strict';

    BoomCMS.Dialog = function(options) {
        var dialog = this;

        this.deferred = $.Deferred().always(function() {
            $(top.window).trigger('boom:dialog:close');
        });

        this.buttons = {
            close: 'Okay',
            cancel: 'Cancel'
        };

        this.options = $.extend({
            width: 'auto',
            cancelButton : true,
            closeButton : true,
            autoOpen: true,
            modal: true,
            resizable: false,
            draggable: false,
            closeOnEscape: false,
            buttons : [],
            dialogClass : 'b-dialog',
            boomDialog: this
        }, options);

        BoomCMS.Dialog.prototype.always = function(callback) {
            dialog.deferred.always(callback);

            return this;
        };

        BoomCMS.Dialog.prototype.cancel = function() {
            dialog.deferred.rejectWith(this.dialog);
            dialog.cleanup();
        };

        BoomCMS.Dialog.prototype.cleanup = function() {
            if (this.contents) {
                this.contents.remove();
                this.contents = null;
            }
        };

        BoomCMS.Dialog.prototype.close = function() {
            dialog.deferred.resolveWith(this.dialog);
            dialog.cleanup();
        };

        BoomCMS.Dialog.prototype.configureButtons = function(options) {
            var dialog = this;

            for (var button in dialog.buttons) {
                if (options[button + 'Button']) {
                    dialog.options.buttons.push({
                        text: dialog.buttons[button],
                        class: 'b-button-' + button,
                        click: dialog[button]
                    });
                }
            }
        };

        BoomCMS.Dialog.prototype.done = function(callback) {
            this.deferred.done(callback);

            return this;
        };

        BoomCMS.Dialog.prototype.fail = function(callback) {
            this.deferred.fail(callback);

            return this;
        };

        BoomCMS.Dialog.prototype.init = function() {
            var dialog = this;

            $(top.window).trigger('boom:dialog:open');

            this
                .contents
                .dialog(this.options)
                .ui();

            $(document).on('keydown', function(e) {
                if (e.which === $.ui.keyCode.ESCAPE) {
                    dialog.cancel();
                    e.stopPropagation();
                }
            });
        };

        BoomCMS.Dialog.prototype.open = function() {
            var self = this,
                $div = $('<div></div>');

            if (this.options.id) {
                $div.attr('id', this.options.id);
            }

            this.contents = $div.appendTo($(document).contents().find('body'));

            this.configureButtons(this.options);

            if (this.options.url && this.options.url.length) {
                if (this.contents.hasClass('ui-dialog-content')) {
                    this.contents.dialog('open');
                } else {
                    setTimeout(function() {
                        self.contents.load(self.options.url, function(response, status, xhr) {
                            if (xhr.status === 200) {
                                self.init();

                                if ($.isFunction(self.options.onLoad)) {
                                    self.options.onLoad.apply(self.dialog);
                                }
                            } else {
                                self.deferred.reject(response, xhr.status);
                            } 
                        });
                    }, 100);
                }

            } else if (this.options.msg.length) {
                setTimeout(function() {
                    self.contents.html(self.options.msg);
                    self.init();

                    if ($.isFunction(self.options.onLoad)) {
                        self.options.onLoad(self);
                    }
                }, 100);
            }
        };

        BoomCMS.Dialog.prototype.remove = function() {
            this.deferred.resolve();
            this.contents.dialog('close');
        };

        this.open();
    };
}(BoomCMS));