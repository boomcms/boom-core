function boomConfirmation(title, message) {
	this.title = title;
	this.message = message;

	boomConfirmation.prototype.open = function() {
		var confirmation = this;

		return new boomDialog({
			title : confirmation.title,
			msg : '<p>' + confirmation.message + '</p>',
			width : 300
		});
	};

	return this.open();
}