/**
Editable text slots
@class
@name chunkText
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkText', $.ui.chunk, {
        content : '',

    _create: function() {
        var element = this.element.find('.chunk-text');
        this.element = (element.length)? $(element[0]) : this.element;

        $.ui.chunk.prototype._create.call(this);
    },

    bind: function() {
        var element = this.element,
            self = this;

        $.ui.chunk.prototype.bind.call(this);

        element.textEditor({
            save: function(e, text) {
                if (!self.hasContent()) {
                    self.remove();
                    self.element.text('Default text.');
                } else {
                    self._save({
                        text: text
                    });
                }

                $.ui.chunk.prototype.bind.call(self);
            }
        });
    },

    edit: function() {},

    getData: function() {
        return {
            text : this.element.html()
        };
    },

    hasContent: function() {
        return this.element.text() !== '' || this.element.find('img').length > 0;
    },

    linkClicked: function() {},

    _update_html: function() {},

    _replace_html: function(html) {
        this.element.html(html);
    }
});
