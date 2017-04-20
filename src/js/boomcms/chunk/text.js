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

        this.setOriginalContent();

        $.ui.chunk.prototype.bind.call(this);

        element.textEditor({
            edit: function() {
                if (!self.hasContent()) {
                    self.remove();
                    self.element.text('Default text.');
                } else if (self.isEdited()) {
                    self._save();
                    self.setOriginalContent();
                }

                $.ui.chunk.prototype.bind.call(self);
            }
        });
    },

    edit: function() {},

    /**
    Get the chunk HTML, escaped and cleaned.
    */
    getData: function(){
        var $content = this.element.find('.slot-content');

        this.content = ($content.length)? $content.html() : this.element.html();

        return {
            text : this.content
        };
    },

    hasContent: function() {
        return this.element.text() !== '' || this.element.find('img').length > 0;
    },

    isEdited: function() {
        return this.originalContent !== this.element.html();
    },

    linkClicked: function($a) {
        if (!$a.is(this.element)) {
            this.element.blur();

            top.location = $a.attr('href');
        }
    },

    setOriginalContent: function() {
        this.originalContent = this.element.html();
    },

    _update_html: function() {},

    _replace_html: function(html) {
        this.element.html(html);
    }
});
