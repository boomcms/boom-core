function boomAlert(message) {
	this.message = message;

	boomAlert.prototype.open = function() {
		var alert = this;

		return new boomDialog({
			msg : alert.message,
			cancelButton : false
		});
	};

	return this.open();
}