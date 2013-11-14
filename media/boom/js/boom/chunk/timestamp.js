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

	format : '',

	timestamp : '',

	_create : function() {
		this.format = this.element.attr('data-boom-format');
		this.timestamp = this.element.attr('data-boom-timestamp');

		$.ui.chunk.prototype._create.call(this);
	},

	/**
	Make the element editable by invokeing boom.editor.edit() on it.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Timestamp chunk slot edit');

		var data = this.getData();

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/timestamp/edit/' + this.options.currentpage.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			title: 'Edit date / time',
			onLoad : function() {
				if (data.format) {
					$('#format').val(data.format);
				}

				var time;
				if (data.timestamp) {
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
					.insert(format, timestamp)
					.done( function(){
						self.destroy();
					});
			}
		});
	},

	insert : function(format, timestamp) {
		this.format = format;
		this.timestamp = timestamp;

		return this._save();
	},

	getData: function(){
		return {
			format : this.format,
			timestamp: this.timestamp
		};
	}
});