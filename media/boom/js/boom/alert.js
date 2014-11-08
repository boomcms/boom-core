function boomAlert(message) {
	this.deferred = new $.Deferred();
	this.message = message;

	boomAlert.prototype.open = function() {
		var alert = this;

		new boomDialog({
			msg : alert.message,
			cancelButton : false
		});

		return this.deferred;
	};

	return this.open();
}