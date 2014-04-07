function boomConfirmation(title, message) {
	this.title = title;
	this.message = message;

	boomConfirmation.prototype.open = function() {
		var confirmation = this,
			dialog;

		return new boomDialog({
			title : confirmation.title,
			msg : confirmation.message,
			width : 300,
			callback : function() {
				confirmation.deferred.resolve();
			}
		});
	};

	return this.open();
}