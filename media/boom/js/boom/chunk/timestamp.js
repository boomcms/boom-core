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
			url: '/cms/chunk/timestamp/edit/' + this.options.currentPage.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			title: 'Edit date / time',
			onLoad : function() {
				data.format && $('#format').val(data.format);
				
				var time = (data.timestamp)? new Date(data.timestamp * 1000) : new Date();

				$( "#timestamp" ).datepicker('setDate', time);
			},
			destroy: function(){
				self.destroy();
			},
			callback: function(){
				var	format = $('#format').val(),
					stringDate = $('#timestamp').val(),
					dateyDate = new Date(stringDate),
					timestamp = dateyDate.valueOf() / 1000;

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