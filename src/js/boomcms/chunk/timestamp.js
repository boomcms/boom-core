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

	_create: function() {
		this.format = this.element.attr('data-boom-format');
		this.timestamp = this.element.attr('data-boom-timestamp');
		this.formatIsEditable = (this.element.attr('data-boom-formatIsEditable') === '1');

		$.ui.chunk.prototype._create.call(this);
	},

	edit: function() {
		var self = this,
			data = this.getData();

		this.dialog = new boomDialog({
			url: '/boomcms/page/' + this.options.currentPage.id + '/chunk/edit?slotname=' + self.options.name + '&type=timestamp',
			width: 400,
			title: 'Edit date / time',
			onLoad: function() {
				if (self.formatIsEditable) {
					data.format && $('#format').val(data.format);
				} else {
					self.dialog.contents.find('label:first-of-type').hide();
				}

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
				timestamp = (dateyDate.valueOf() / 1000) - (dateyDate.getTimezoneOffset() * 60);

			self.insert(format, timestamp);
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

	getData: function(){
		return {
			format : this.format,
			timestamp: this.timestamp
		};
	}
});