/**
Editable timestamps
@class
@name chunkTimestamp
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkTimestamp', $.ui.chunk,

    /**
    @lends $.ui.chunkTimestamp
    */
    {

        format: '',
        timestamp: '',

        _create: function() {
            this.format = this.getAttr('format');
            this.timestamp = this.getAttr('timestamp');
            this.formatIsEditable = (this.getAttr('formatIsEditable') === '1');

            $.ui.chunk.prototype._create.call(this);
        },

        edit: function() {
            var self = this,
                data = this.getData();

            this.dialog = new BoomCMS.Dialog({
                url: this.options.currentPage.baseUrl + 'chunk/edit?slotname=' + self.options.name + '&type=timestamp',
                width: 400,
                title: 'Edit date / time',
                onLoad: function() {
                    if (self.formatIsEditable) {
                        data.format && $('#format').val(data.format);
                    } else {
                        self.dialog.contents.find('label:first-of-type').hide();
                    }
                },
                destroy: function() {
                    self.destroy();
                }
            }).done(function() {
                var format = $('#format').val(),
                    stringDate = $('#timestamp').val(),
                    dateyDate = new Date(stringDate);

                self.insert(format, dateyDate.valueOf() / 1000);
            })
            .always(function() {
                self.bind();
            });
        },

        insert: function(format, timestamp) {
            if (this.formatIsEditable) {
                this.format = format;
            }

            this.timestamp = timestamp;

            return this._save();
        },

        getData: function() {
            return {
                format : this.format,
                timestamp: this.timestamp
            };
        }
    });