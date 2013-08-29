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

	/**
	Make the element editable by invokeing boom.editor.edit() on it.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Timestamp chunk slot edit');

		var data = this.getData();

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/timestamp/edit/' + $.boom.page.options.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			// cache: true,
			title: 'Edit date / time',
			onLoad : function() {
				if (data.format) {
					$('#format').val(data.format);
				}

				var time;
				if (self.options.slot.timestamp) {
					time = new Date(data.timestamp * 1000);
				} else {
					time = new Date();
				}

				$( "#timestamp" ).datepicker('setDate', time);
			},
			destroy: function(){
				self.destroy();
			},
			callback: function(){
				var format = $('#format').val();
				var stringDate = $('#timestamp').val();
				var dateyDate = new Date(stringDate);
				var timestamp = dateyDate.valueOf() / 1000;

				self
				._insert(format, timestamp)
				.done( function(){
					self.destroy();
				});
			}
		});
	},

	_insert : function(format, timestamp) {
		var self = this;

		return $.post(this.options.urlPrefix + '/timestamp/preview/' + $.boom.page.options.id, {slotname : self.options.slot.slotname, format : format, timestamp : timestamp})
			.done(function(data) {
				self._apply(data);
			});
	},

	getData: function(){
		return {
			format : this.element.attr('data-boom-format'),
			timestamp: this.element.attr('data-boom-timestamp')
		};
	}
});