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

	edit : function(){

		var self = this;

		$.boom.log('Timestamp chunk slot edit');

		var data = this.getData();

		this.dialog = new boomDialog({
			url: '/cms/chunk/timestamp/edit/' + this.options.currentPage.id + '?slotname=' + self.options.name,
			width: 400,
			title: 'Edit date / time',
			closeButton: false,
			saveButton: true,
			onLoad : function() {
				data.format && $('#format').val(data.format);

				var time = (data.timestamp)? new Date(data.timestamp * 1000) : new Date();

				$( "#timestamp" ).datepicker('setDate', time);
			},
			destroy: function(){
				self.destroy();
			}
		}).done(function() {
			var	format = $('#format').val(),
				stringDate = $('#timestamp').val(),
				dateyDate = new Date(stringDate),
				timestamp = (dateyDate.valueOf() / 1000) - (new Date().getTimezoneOffset() * 60);

			self.insert(format, timestamp);
		})
		.always(function() {
			self.bind();
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